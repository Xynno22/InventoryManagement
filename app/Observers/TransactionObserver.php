<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        // Jika tipe transaksi berubah
        if ($transaction->isDirty('type_id')) {
            // Ambil semua detail transaksi
            $details = $transaction->details;

            // Update semua stock terkait
            foreach ($details as $detail) {
                // Kembalikan perubahan stock lama
                $this->reverseStockChange($detail, $transaction->getOriginal('type_id'));

                // Update dengan tipe baru
                $this->updateStock($detail);
            }
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleting(Transaction $transaction): void
    {
        // Pastikan tipe transaksi ada dan valid
        if (!$transaction->type) {
            return;
        }

        // Ambil semua detail transaksi
        $details = $transaction->details;

        // Kembalikan semua perubahan stock
        foreach ($details as $detail) {
            // Dapatkan produk dan stok terkait
            $product = $detail->product;
            if (!$product) {
                continue;
            }

            $stock = \App\Models\Stock::where('productID', $product->id)->first();
            if (!$stock) {
                continue;
            }

            // Reverse operasi berdasarkan tipe transaksi
            if ($transaction->type->name == 'Buying') {
                // Jika transaksi adalah pembelian, kurangi stok
                $stock->currentStock -= $detail->quantity;
            } elseif ($transaction->type->name == 'Selling') {
                // Jika transaksi adalah penjualan, tambah stok
                $stock->currentStock += $detail->quantity;
            }

            $stock->save();
        }
    }

    /**
     * Handle the reverseStockChange event.
     */
    protected function reverseStockChange($detail, $typeId)
    {
        // Ambil tipe transaksi
        $type = \App\Models\Type::find($typeId);

        if (!$type) {
            return;
        }

        // Ambil stock
        $stock = \App\Models\Stock::where('productID', $detail->product_id)->first();

        if (!$stock) {
            return;
        }

        // Reverse operasi berdasarkan tipe
        if ($type->name == 'Buying') {
            // Jika sebelumnya buying, kurangi stock
            $stock->currentStock -= $detail->quantity;
        } elseif ($type->name == 'Selling') {
            // Jika sebelumnya selling, tambah stock
            $stock->currentStock += $detail->quantity;
        }

        $stock->save();
    }

    protected function updateStock($detail)
    {
        // Reuse fungsi dari TransactionDetailObserver
        app(TransactionDetailObserver::class)->updateStock($detail);
    }

}
