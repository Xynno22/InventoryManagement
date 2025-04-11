<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusPaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert data ke tabel statuses
        DB::table('statuses')->insert([
            ['name' => 'Success'],
            ['name' => 'Pending'],
        ]);

        // Insert data ke tabel payments
        DB::table('payments')->insert([
            ['name' => 'Tunai (Cash)'],
            ['name' => 'Bank Transfer'],
            ['name' => 'E-Wallet'],
            ['name' => 'QRIS'],
            ['name' => 'Virtual Account (VA)'],
        ]);

        // Insert data ke tabel types
        DB::table('types')->insert([
            ['name' => 'Selling'],
            ['name' => 'Buying'],
        ]);
    }
}
