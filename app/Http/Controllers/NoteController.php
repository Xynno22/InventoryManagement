<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\NoteDetail;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{


    /**
     * Check who is logged in and get company ID
     */
    private function getCompanyId()
    {
        if (auth('company')->check()) {
            return auth('company')->id();
        } elseif (auth('web')->check()) {
            return auth('web')->user()->company_id;
        } else {
            abort(403, 'Unauthorized');
        }
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Cek siapa yang login
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        } elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        // Query dasar
        $query = Note::where('company_id', $companyId);

        // Filtering berdasarkan search (contohnya: title atau content)
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%');
            });
        }

        // Ambil catatan yang sudah difilter dan dipaginasi
        $notes = $query->paginate(10);

        return view('note.index', ['notes' => $notes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companyId = $this->getCompanyId();

        $products = Product::where('companyID', $companyId)->get();
        $transactions = Transaction::select('voucher_code', 'customer_name')
            ->where('company_id', $companyId)
            ->get();

        return view('note.add', compact('products', 'transactions'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'description' => 'required|string',
            // 'voucher_code' => 'required|string|exists:transactions,voucher_code',
            'total_price' => 'required|numeric|min:0',
            'note_details_json' => 'required|string',
        ]);

        // Validate voucher_code belongs to the company
        // $transaction = Transaction::where('voucher_code', $request->voucher_code)
        //     ->where('company_id', $companyId)
        //     ->first();
        //
        // if (!$transaction) {
        //     return back()->with('error', 'Invalid voucher code for your company.');
        // }

        // Parse note details
        $noteDetails = json_decode($request->note_details_json, true);

        if (empty($noteDetails)) {
            return back()->with('error', 'Please add at least one note detail.');
        }

        try {
            DB::beginTransaction();

            // Create note
            $note = Note::create([
                'customer_name' => $request->customer_name,
                'description' => $request->description,
                'voucher_code' => $request->voucher_code,
                'total_price' => $request->total_price,
                'company_id' => $companyId,
            ]);

            // Create note details
            foreach ($noteDetails as $detail) {
                // Validate product belongs to company
                $product = Product::where('id', $detail['productId'])
                    ->where('companyID', $companyId)
                    ->first();

                if (!$product) {
                    throw new \Exception('Invalid product selected.');
                }

                NoteDetail::create([
                    'note_id' => $note->id,
                    'product_id' => $detail['productId'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                ]);
            }

            DB::commit();

            return redirect()->route('note.index')
                ->with('success', 'create');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create note: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        $companyId = $this->getCompanyId();

        // Check if note belongs to the company
        if ($note->company_id !== $companyId) {
            abort(403, 'Unauthorized access to this note.');
        }

        $note->load(['noteDetails.product']);

        return view('note.show', compact('note'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        $companyId = $this->getCompanyId();

        // Check if note belongs to the company
        if ($note->company_id !== $companyId) {
            abort(403, 'Unauthorized access to this note.');
        }

        // Load note details with product information
        $note->load(['noteDetails.product']);

        $products = Product::where('companyID', $companyId)->get();
        $transactions = Transaction::select('voucher_code', 'customer_name')
            ->where('company_id', $companyId)
            ->get();

        return view('note.edit', compact('note', 'products', 'transactions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        $companyId = $this->getCompanyId();

        // Check if note belongs to the company
        if ($note->company_id !== $companyId) {
            abort(403, 'Unauthorized access to this note.');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'description' => 'required|string',
            // 'voucher_code' => 'required|string|exists:transactions,voucher_code',
            'total_price' => 'required|numeric|min:0',
            'note_details_json' => 'required|string',
        ]);

        // Validate voucher_code belongs to the company
        // $transaction = Transaction::where('voucher_code', $request->voucher_code)
        //     ->where('company_id', $companyId)
        //     ->first();
        //
        // if (!$transaction) {
        //     return back()->with('error', 'Invalid voucher code for your company.');
        // }

        // Parse note details
        $noteDetails = json_decode($request->note_details_json, true);

        if (empty($noteDetails)) {
            return back()->with('error', 'Please add at least one note detail.');
        }

        try {
            DB::beginTransaction();

            // Update note
            $note->update([
                'customer_name' => $request->customer_name,
                'description' => $request->description,
                // 'voucher_code' => $request->voucher_code,
                'total_price' => $request->total_price,
            ]);

            // Delete existing note details
            NoteDetail::where('note_id', $note->id)->delete();

            // Create new note details
            foreach ($noteDetails as $detail) {
                // Validate product belongs to company
                $product = Product::where('id', $detail['productId'])
                    ->where('companyID', $companyId)
                    ->first();

                if (!$product) {
                    throw new \Exception('Invalid product selected.');
                }

                NoteDetail::create([
                    'note_id' => $note->id,
                    'product_id' => $detail['productId'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                ]);
            }

            DB::commit();

            return redirect()->route('note.index')
                ->with('success', 'updated');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update note: ' . $e->getMessage());
        }
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
            $note = Note::where('id', $id)
                ->where('company_id', $companyId) // Pastikan hanya bisa menghapus note milik perusahaan yang login
                ->firstOrFail();

            $note->delete();

            return response()->json(['success' => true, 'message' => 'Note deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete note: ' . $e->getMessage()], 500);
        }
    }

    // note compare function
    public function compare(Note $note, Request $request)
    {
        // Ambil semua transaction untuk dropdown
        $transactions = Transaction::with('transactionDetails.product')
            ->orderBy('date', 'desc')
            ->get();

        // Ambil transaction yang dipilih jika ada
        $selectedTransaction = null;
        if ($request->has('transaction_id') && $request->transaction_id) {
            $selectedTransaction = Transaction::with('transactionDetails.product')
                ->find($request->transaction_id);
        }

        // Load note dengan relasi
        $note->load('noteDetails.product');

        return view('note.compare', compact('note', 'transactions', 'selectedTransaction'));
    }

    public function getTransaction(Request $request)
    {
        $voucherCode = $request->voucher_code;

        $transaction = Transaction::where('voucher_code', $voucherCode)->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ]);
    }

    // compare function
    /**
 * Calculate differences between transaction and note
 */
    private function calculateDifferences($data)
    {
        $differences = [
            'total_price_diff' => 0,
            'customer_name_match' => true,
            'missing_in_note' => collect(),
            'missing_in_transaction' => collect(),
            'quantity_differences' => collect(),
            'price_differences' => collect()
        ];

        if (!$data['note']['exists']) {
            $differences['note_missing'] = true;
            return $differences;
        }

        // Compare total prices
        $differences['total_price_diff'] = $data['transaction']['total_price'] - $data['note']['total_price'];

        // Compare customer names
        $differences['customer_name_match'] = $data['transaction']['customer_name'] === $data['note']['customer_name'];

        // Get product collections
        $transactionProducts = $data['transaction']['details'];
        $noteProducts = $data['note']['details'];

        // Find missing products in note
        foreach ($transactionProducts as $transProduct) {
            $foundInNote = $noteProducts->firstWhere('product_id', $transProduct['product_id']);
            if (!$foundInNote) {
                $differences['missing_in_note']->push($transProduct);
            } else {
                // Check quantity differences
                if ($transProduct['quantity'] != $foundInNote['quantity']) {
                    $differences['quantity_differences']->push([
                        'product_name' => $transProduct['product_name'],
                        'transaction_qty' => $transProduct['quantity'],
                        'note_qty' => $foundInNote['quantity'],
                        'difference' => $transProduct['quantity'] - $foundInNote['quantity']
                    ]);
                }

                // Check price differences
                if ($transProduct['price'] != $foundInNote['price']) {
                    $differences['price_differences']->push([
                        'product_name' => $transProduct['product_name'],
                        'transaction_price' => $transProduct['price'],
                        'note_price' => $foundInNote['price'],
                        'difference' => $transProduct['price'] - $foundInNote['price']
                    ]);
                }
            }
        }

        // Find missing products in transaction
        foreach ($noteProducts as $noteProduct) {
            $foundInTransaction = $transactionProducts->firstWhere('product_id', $noteProduct['product_id']);
            if (!$foundInTransaction) {
                $differences['missing_in_transaction']->push($noteProduct);
            }
        }

        return $differences;
    }

    /**
     * Get comparison summary for dashboard or reports
     */
    public function getComparisonSummary()
    {
        $companyId = $this->getCompanyId();

        $summary = [
            'total_transactions' => Transaction::where('company_id', $companyId)->count(),
            'total_notes' => Note::where('company_id', $companyId)->count(),
            'matched_vouchers' => 0,
            'unmatched_transactions' => 0,
            'unmatched_notes' => 0,
            'total_differences' => 0
        ];

        // Get all voucher codes from transactions
        $transactionVouchers = Transaction::where('company_id', $companyId)
            ->pluck('voucher_code')
            ->unique();

        // Get all voucher codes from notes
        $noteVouchers = Note::where('company_id', $companyId)
            ->pluck('voucher_code')
            ->unique();

        // Calculate matched vouchers
        $summary['matched_vouchers'] = $transactionVouchers->intersect($noteVouchers)->count();

        // Calculate unmatched
        $summary['unmatched_transactions'] = $transactionVouchers->diff($noteVouchers)->count();
        $summary['unmatched_notes'] = $noteVouchers->diff($transactionVouchers)->count();

        // Calculate total price differences for matched vouchers
        $matchedVouchers = $transactionVouchers->intersect($noteVouchers);
        foreach ($matchedVouchers as $voucher) {
            $transaction = Transaction::where('voucher_code', $voucher)
                ->where('company_id', $companyId)
                ->first();
            $note = Note::where('voucher_code', $voucher)
                ->where('company_id', $companyId)
                ->first();

            if ($transaction && $note) {
                $summary['total_differences'] += abs($transaction->total_price - $note->total_price);
            }
        }

        return $summary;
    }

    /**
     * Download note as PDF
     */
    public function downloadPDF($id)
    {
        try {
            $note = Note::with(['noteDetails.product'])->findOrFail($id);

            // Generate PDF
            $pdf = PDF::loadView('note.note-pdf', compact('note'));

            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');

            // Generate filename
            $filename = 'note-' . $note->id . '-' . date('Y-m-d') . '.pdf';

            // Download PDF
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}
