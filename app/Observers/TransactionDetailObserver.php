<?php

namespace App\Observers;

use App\Models\Stock;
use App\Models\TransactionDetail;

class TransactionDetailObserver
{
    /**
     * Handle the TransactionDetail "created" event.
     */
    public function created(TransactionDetail $transactionDetail): void
    {
        $transactionDetail->transaction->recalculateTotalPrice();
        $this->updateStock($transactionDetail);
    }

    /**
     * Handle the TransactionDetail "updated" event.
     */
    public function updated(TransactionDetail $transactionDetail): void
    {
        $transactionDetail->transaction->recalculateTotalPrice();

        // Jika quantity berubah
        if ($transactionDetail->isDirty('quantity')) {
            // Ambil data lama
            $oldQuantity = $transactionDetail->getOriginal('quantity');

            // Kembalikan stock lama dulu (reverse operasi)
            $this->updateStock($transactionDetail, $oldQuantity, true);

            // Update dengan quantity baru
            $this->updateStock($transactionDetail);
        }
    }

    /**
     * Handle the TransactionDetail "deleted" event.
     */
    public function deleted(TransactionDetail $transactionDetail): void
    {
        // If the transaction still exists (not deleted in cascade)
        if ($transactionDetail->transaction) {
            $transactionDetail->transaction->recalculateTotalPrice();
        }
        $this->updateStock($transactionDetail, null, true);
    }

    public function updateStock(TransactionDetail $transactionDetail, $quantity = null, $isReverse = false)
    {
        // Ambil transaction dan product
        $transaction = $transactionDetail->transaction;
        $product = $transactionDetail->product;

        if (!$transaction || !$product) {
            return;
        }

        // Jika quantity tidak diberikan, gunakan dari model
        $quantity = $quantity ?? $transactionDetail->quantity;

        // Ambil stock - gunakan productID sesuai struktur tabel stocks
        $stock = Stock::where('productID', $product->id)->first();

        if (!$stock) {
            // Buat stock baru jika belum ada
            $stock = new Stock();
            $stock->productID = $product->id; // Gunakan productID karena di tabel stocks namanya productID
            $stock->currentStock = 0;
        }

        // Update stock berdasarkan tipe transaksi
        if ($transaction->type->name == 'Buying') {
            // Jika pembelian, stock bertambah
            $stock->currentStock = $isReverse
            ? $stock->currentStock - $quantity
            : $stock->currentStock + $quantity;
        } elseif ($transaction->type->name == 'Selling') {
            // Jika penjualan, stock berkurang
            $stock->currentStock = $isReverse
            ? $stock->currentStock + $quantity
            : $stock->currentStock - $quantity;
            
            $stock->totalOrder = $stock->totalOrder + $quantity;
        }

        $stock->save();
    }
}
