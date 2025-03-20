<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRolePermissions
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika yang login adalah company, langsung lanjut
        if (auth('company')->check()) {
            return $next($request);
        }

        // Jika yang login adalah web, cek izin berdasarkan nama rute
        $routeName = $request->route()->getName();

        // Mapping rute ke izin yang dibutuhkan
        $permissions = [
            'categories.index' => 'view category',
            'categories.create' => 'create category',
            'categories.store' => 'create category',
            'categories.edit' => 'update category',
            'categories.update' => 'update category',
            'categories.destroy' => 'delete category',
            'products.index' => 'view product',
            'products.create' => 'create product',
            'products.store' => 'create product',
            'products.edit' => 'update product',
            'products.update' => 'update product',
            'products.destroy' => 'delete product',
        ];

        // Jika route membutuhkan izin dan user tidak punya izin, tolak akses
        if (isset($permissions[$routeName]) && !auth('web')->user()->can($permissions[$routeName])) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
