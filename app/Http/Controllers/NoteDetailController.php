<?php

namespace App\Http\Controllers;

use App\Models\NoteDetail;
use App\Models\Product;
use Illuminate\Http\Request;

class NoteDetailController extends Controller
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
            // $promoId = null;
            // if (!empty($detail['promo'])) {
            //     $promo = Promo::where('amount', $detail['promo'])->first();
            //     if (!$promo) {
            //         throw new \Exception("Promo not found: {$detail['promo']}");
            //     }
            //     $promoId = $promo->id;
            // }

            // Create the transaction detail
            NoteDetail::create([
                'transaction_id' => $transactionId,
                'product_id' => $product->id,
                'quantity' => $detail['quantity'],
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(NoteDetail $noteDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NoteDetail $noteDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NoteDetail $noteDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NoteDetail $noteDetail)
    {
        //
    }
}
