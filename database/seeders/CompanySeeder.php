<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::insert([
            [
                'name' => 'TechGear Solutions',
                'email' => 'techgear@gmail.com',
                'password' => Hash::make('Admin123!'),
                'address' => 'Jl. Teknologi No. 10, Jakarta',
                'phone' => '0812-3456-7890',
                'email_verified_at' => Carbon::now(),
            ],
            [
                'name' => 'Inovasi Digital Nusantara',
                'email' => 'inovasi.digital@gmail.com',
                'password' => Hash::make('SecurePass456!'),
                'address' => 'Jl. Inovasi No. 25, Surabaya',
                'phone' => '0813-9876-5432',
                'email_verified_at' => Carbon::now(),
            ],
        ]);
        
    }
}
