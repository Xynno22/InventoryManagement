<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CompanySeeder::class, // Menjalankan seeder untuk akun perusahaan
            PromoTypeSeeder::class, // Menjalankan seeder untuk promo types
            PromoSeeder::class, // Menjalankan seeder untuk promo types
        ]);
    }
}

