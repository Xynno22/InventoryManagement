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
            'view promo',
            'view stock',
            'update stock',
            'create transaction',
            'update transaction',
            'delete transaction',
            'view transaction',
            'create operational expenses',
            'update operational expenses',
            'delete operational expenses',
            'view operational expenses',
            'create stock opname',
            'delete stock opname',
            'view stock opname',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
