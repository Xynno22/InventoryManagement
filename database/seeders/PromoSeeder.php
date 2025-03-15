<?php

namespace Database\Seeders;

use App\Models\Promo;
use App\Models\PromoType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Ambil ID dari promo types
        $percentageType = PromoType::where('name', 'Percentage')->first();
        $amountType = PromoType::where('name', 'Amount')->first();

        if (!$percentageType || !$amountType) {
            $this->command->warn("PromoType belum tersedia. Jalankan PromoTypeSeeder terlebih dahulu.");
            return;
        }

        $promos = [
            [
                'name' => 'Christmas Discount',
                'promo_type_id' => $percentageType->id,
                'amount' => 10.00,
            ],
            [
                'name' => 'Ramadhan Discount',
                'promo_type_id' => $amountType->id,
                'amount' => 50000.00,
            ],
        ];

        Promo::insert($promos);
    }
}
