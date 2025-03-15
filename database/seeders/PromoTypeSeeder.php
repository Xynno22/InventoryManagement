<?php

namespace Database\Seeders;

use App\Models\PromoType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromoTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promoTypes = [
            ['name' => 'percentage'],
            ['name' => 'fixed amount'],
        ];

        PromoType::insert($promoTypes);
    }
}
