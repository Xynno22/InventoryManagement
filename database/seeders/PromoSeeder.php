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
        $fixedEndDate = Carbon::create(2030, 12, 30, 0, 0, 0);
        $promos = [
            [
                'name' => 'No Promo',
                'promo_type_id' => 2,
                'end_date' => $fixedEndDate,
                'company_id' => 2,
                'amount' => 0,
            ],
            [
                'name' => 'No Promo',
                'promo_type_id' => 2,
                'end_date' => $fixedEndDate,
                'company_id' => 1,
                'amount' => 0,
            ],
        ];

        Promo::insert($promos);
    }
}
