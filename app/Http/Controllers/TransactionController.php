<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\Type;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Jika yang login adalah company
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        }
        // Jika yang login adalah user, ambil company_id dari user
        elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        $query = Transaction::with('transactionDetails')->where('company_id', $companyId);

        // Filtering by search
        if ($request->has('search') && !empty($request->search)) {
            $query->where('customer_name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        if ($request->has('sort') && in_array($request->sort, ['asc', 'desc'])) {
            $query->orderBy('customer_name', $request->sort);
        }

        // Ambil transaksi dengan transaksi detail yang sudah dihitung grand_total
        $transactions = $query->paginate(10);

        // Menambahkan grand_total pada setiap transaksi
        $transactions->getCollection()->transform(function ($transaction) {
            $transaction->grand_total = $transaction->transactionDetails->sum(function ($detail) {
                return $detail->quantity * $detail->price;
            });
            return $transaction;
        });

        return view('transaction.index', ['transactions' => $transactions]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Jika yang login adalah company
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        }
        // Jika yang login adalah user, ambil company_id dari user
        elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        // Get the products based on the companyId
        $products = Product::where('companyID', $companyId)->get();
        $promos = Promo::where('company_id', $companyId)->get();

        // types, payment, statuses
        $types = Type::all();
        $payments = Payment::all();
        $statuses = Status::all();

        return view('transaction.add', compact('types', 'payments', 'statuses', 'products', 'promos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'type' => 'required|exists:types,id',
            'payment' => 'required|exists:payments,id',
            'date' => 'required|date',
            'status' => 'required|exists:statuses,id',
            'total_price' => 'required|numeric|gt:0',
            'transaction_details_json' => 'required'
        ]);

        // Ambil company_id berdasarkan siapa yang login
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        } elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            abort(403, 'Unauthorized');
        }

        DB::beginTransaction();

        try {
            // Simpan transaksi ke database
            $transaction = Transaction::create([
                'customer_name' => $request->customer_name,
                'type_id' => $request->type,
                'payment_id' => $request->payment,
                'date' => $request->date,
                'status_id' => $request->status,
                'total_price' => $request->total_price,
                'company_id' => $companyId,
                'voucher_code' => Transaction::generateVoucherCode(),
            ]);

            // Get transaction details from the request
            $transactionDetails = json_decode($request->transaction_details_json, true);

            // If there are details, call the TransactionDetailController to store them
            if (!empty($transactionDetails)) {
                app(TransactionDetailController::class)->storeDetails($transaction->id, $transactionDetails);
            }

            // Commit transaction if everything is successful
            DB::commit();

            return redirect()->route('transaction.index')->with('success', 'added');
        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Failed to create transaction: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Ambil transaksi berdasarkan ID dan pastikan milik perusahaan yang login
        $transaction = Transaction::where('id', $id)
            ->where('company_id', auth('company')->id())
            ->firstOrFail();


        // Eager load relationship transactionDetails dan product yang memeriksa company_id pada product
        $transaction->load(['transactionDetails.product' => function ($query) {
            $query->where('companyID', auth('company')->id());
        }, 'transactionDetails.promo']);

        // Hitung total_amount untuk setiap transaksi detail
        foreach ($transaction->transactionDetails as $detail) {
            // Pastikan detail produk tersedia dan memiliki nilai quantity dan price
            if ($detail->product) {
                $detail->total_amount = $detail->quantity * $detail->price;
            } else {
                $detail->total_amount = 0; // Atau logika lain jika produk tidak ditemukan
            }
        }

        // Hitung Grand Total
        $grandTotal = $transaction->transactionDetails->sum('total_amount');

        // Return ke Blade
        return view('transaction.show', compact('transaction', 'grandTotal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        // types, payment, statuses
        $types = Type::all();
        $payments = Payment::all();
        $statuses = Status::all();

        // Get promos filtered by the currently logged-in company
        if (auth()->guard('company')->check()) {
            $promos = Promo::where('company_id', auth()->guard('company')->id())->get();
        } else {
            // For admin users, you might want to show all promos or none
            $promos = Promo::all(); // or empty collection: collect()
        }

        // Pastikan user memiliki izin
        if (!auth()->user()->can('update transaction') && !auth()->guard('company')->check()) {
            abort(403, 'Unauthorized action.');
        }

        return view('transaction.edit', compact('transaction', 'types', 'payments', 'statuses', 'promos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        // Validasi input
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'type' => 'required|exists:types,id',
            'payment' => 'required|exists:payments,id',
            'date' => 'nullable|date',
            'status' => 'required|exists:statuses,id',
        ]);

        // Update transaction
        $transaction->update([
            'customer_name' => $request->customer_name,
            'total_price' => $request->total_price,
            'date' => $request->date,
            'status_id' => $request->status,
            'type_id' => $request->type,
            'payment_id' => $request->payment,
        ]);

        return redirect()->route('transaction.index')->with('success', 'updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Jika yang login adalah company
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        }
        // Jika yang login adalah user, ambil company_id dari user
        elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        try {
            $transaction = Transaction::where('id', $id)
                ->where('company_id', $companyId) // Pastikan hanya bisa menghapus transaksi milik perusahaan yang login
                ->firstOrFail();

            $transaction->delete();

            return response()->json(['success' => true, 'message' => 'Transaction deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete transaction: ' . $e->getMessage()], 500);
        }
    }
}
