<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'create category',
            'update category',
            'delete category',
            'view category',
            'create product',
            'update product',
            'delete product',
            'view product',
            'create promo',
            'update promo',
            'delete promo',
            'view promo'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
