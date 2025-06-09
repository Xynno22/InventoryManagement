<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Promo;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionDetailController extends Controller
{
    /**
     * Store multiple transaction details for a transaction
     *
     * @param int $transactionId
     * @param array $details
     * @return void
     */

    public function storeDetails($transactionId, array $details)
    {
        foreach ($details as $detail) {
            // Get the product ID from the product name
            $product = Product::where('name', $detail['product'])->first();
            if (!$product) {
                throw new \Exception("Product not found: {$detail['product']}");
            }

            // Get the promo ID if promo is provided
            $promoId = null;
            if (!empty($detail['promo'])) {
                $promo = Promo::where('amount', $detail['promo'])->first();
                if (!$promo) {
                    throw new \Exception("Promo not found: {$detail['promo']}");
                }
                $promoId = $promo->id;
            }

            // Create the transaction detail
            TransactionDetail::create([
                'transaction_id' => $transactionId,
                'product_id' => $product->id,
                'quantity' => $detail['quantity'],
                'promo_id' => $promoId,
                'price' => $detail['price'],
            ]);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'promo_id' => 'required|exists:promos,id',
            'price' => 'required|numeric|min:0'
        ]);

        // Add company_id if needed
        if (auth('company')->check()) {
            $validated['company_id'] = auth('company')->id();
        } elseif (auth('web')->check()) {
            $validated['company_id'] = auth('web')->user()->company_id;
        }

        TransactionDetail::create($validated);

        return back()->with('success', 'Transaction detail added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionDetail $transactionDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionDetail $transactionDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Cek data request dulu kalau perlu debugging
        $detail = TransactionDetail::findOrFail($id);

        // Validasi input
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
            'promo_id' => 'nullable|exists:promos,id',
        ]);

        // Simpan perubahan ke database
        $detail->update([
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
            'promo_id' => $validated['promo_id'] ?? null,
        ]);

        // Recalculate total price dari parent transaction
        $transaction = $detail->transaction;
        $totalPrice = $transaction->recalculateTotalPrice();

        return response()->json([
            'message' => 'Transaction detail updated successfully',
            'total_price' => $totalPrice,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionDetail $transactionDetail)
    {
        // Jika yang login adalah company
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        }
        // Jika yang login adalah user, ambil company_id dari user
        elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            Log::warning('Unauthorized access attempt to delete transaction detail: no auth guard matched.');
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: user not authenticated.'
            ], 403);
        }

        try {
            // Store transaction reference before deletion
            $transaction = $transactionDetail->transaction;

            // Pastikan detail transaksi yang akan dihapus milik perusahaan yang sedang login
            if (!$transaction || $transaction->company_id !== $companyId) {
                Log::warning("Unauthorized delete attempt. Detail ID: {$transactionDetail->id}, Auth Company ID: {$companyId}, Actual Company ID: " . ($transaction->company_id ?? 'null'));
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this transaction detail.'
                ], 403);
            }

            // Check if this is the only detail record for this transaction
            $detailCount = $transaction->details()->count();
            if ($detailCount <= 1) {
                Log::info("Delete prevented: Transaction ID {$transaction->id} has only one detail record.");
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the only transaction detail. A transaction must have at least one item.'
                ], 422);
            }

            // Delete the transaction detail
            $transactionDetail->delete();

            // Recalculate total price
            $newTotalPrice = $transaction->recalculateTotalPrice();

            return response()->json([
                'success' => true,
                'message' => 'Transaction detail deleted successfully!',
                'total_price' => $newTotalPrice
            ], 200);
        } catch (\Exception $e) {
            Log::error("Failed to delete transaction detail ID {$transactionDetail->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transaction detail: ' . $e->getMessage()
            ], 500);
        }
    }
}
