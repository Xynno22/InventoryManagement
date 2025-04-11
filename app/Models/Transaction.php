<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function recalculateTotalPrice()
    {
        $total = 0;

        foreach ($this->transactionDetails as $detail) {
            $unitPrice = $detail->price;
            $quantity = $detail->quantity;
            $promoStr = (string) ($detail->promo->amount ?? '0');
            $promoLength = strlen($promoStr);

            $itemTotal = $unitPrice * $quantity;
            $discount = 0;

            if ($promoLength > 3) {
                // Fixed amount (langsung dikurangin total)
                $discount = floatval($promoStr);
            } elseif ($promoLength >= 1 && $promoLength <= 2) {
                // Percentage
                $percent = floatval($promoStr);
                $discount = ($itemTotal * $percent) / 100;
            }

            $itemTotalAfterDiscount = max(0, $itemTotal - $discount);
            $total += $itemTotalAfterDiscount;
        }

        $this->total_price = $total;
        $this->save();

        return $total;
    }

    public static function generateVoucherCode()
    {
        // Generate 2 digit angka random
        $firstPart = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);

        // Generate 4 digit angka random
        $secondPart = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        // Gabungkan menjadi format INV/XX/XXXX
        $voucherCode = "INV/{$firstPart}/{$secondPart}";

        // Cek apakah kode sudah ada di database
        // Jika sudah ada, generate ulang
        if (self::where('voucher_code', $voucherCode)->exists()) {
            return self::generateVoucherCode();
        }

        return $voucherCode;
    }

}
