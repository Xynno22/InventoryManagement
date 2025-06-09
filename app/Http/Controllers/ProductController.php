<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Menampilkan semua produk
    public function index(Request $request)
    {
        // Jika yang login adalah company
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        }
        // Jika yang login adalah user, ambil company_id dari user
        elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        $query = Product::where('companyID', $companyId);

        // Filtering by search
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        if ($request->has('sort') && in_array($request->sort, ['asc', 'desc'])) {
            $query->orderBy('name', $request->sort);
        }

        $products = $query->paginate(10);

        return view('products.index', [
            'products' => $products,
        ]);
    }

    // Menampilkan form tambah produk
    public function create()
    {
        $categories = ProductCategory::where('company_id', auth('company')->id())->get();

        return view('products.create', [
            'categories' => $categories,
        ]);
    }

    // Menyimpan produk baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'categoryID' => 'required|exists:product_categories,id',
            'detail' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'unit' => 'required|string|max:50',
            'location' => 'nullable|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Jika yang login adalah company
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        }
        // Jika yang login adalah user, ambil company_id dari user
        elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        $product = Product::create([
            'name' => $request->name,
            'categoryID' => $request->categoryID,
            'detail' => $request->detail,
            'image' => $imagePath,
            'unit' => $request->unit,
            'companyID' => $companyId,
            'location' => $request->location,
            'purchase_price' => $request->purchase_price,
            'sale_price' => $request->sale_price,
        ]);

        if ($product) {
            Stock::create([
                'productID' => $product->id, // Menggunakan ID produk yang baru dibuat
                'companyID' => $companyId,
                'currentStock' => 0, // Stok awal diatur ke 0
                'minimumStock' => 0, // Bisa diubah sesuai kebutuhan
                'lastUpdated' => now(),
                'totalOrder' => 0,
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Product added successfully!');
    }

    // Menampilkan form edit produk
    public function edit($id)
    {
        $product = Product::where('id', $id)
                    ->where('companyID', auth('company')->id())
                    ->firstOrFail();

        $categories = ProductCategory::where('company_id', auth('company')->id())->get();

        return view('products.edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }
    public function show($id)
    {
        $product = Product::where('id', $id)
                    ->where('companyID', auth('company')->id())
                    ->firstOrFail();


        return view('products.show', [
            'product' => $product
        ]);
    }


    // Memperbarui produk
    public function update(Request $request, $id)
    {
        $product = Product::where('id', $id)
                    ->where('companyID', auth('company')->id())
                    ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'categoryID' => 'required|exists:product_categories,id',
            'detail' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'unit' => 'required|string|max:50',
            'location' => 'nullable|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
        ]);

        // Cek apakah ada file gambar baru yang diunggah
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // Simpan gambar baru
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        // Update data produk
        $product->update([
            'name' => $request->name,
            'categoryID' => $request->categoryID,
            'detail' => $request->detail,
            'unit' => $request->unit,
            'location' => $request->location,
            'purchase_price' => $request->purchase_price,
            'sale_price' => $request->sale_price,
            'image' => $product->image ?? $product->getOriginal('image'), // Pastikan gambar tetap ada
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }


    // Menghapus produk
    public function destroy($id)
    {
        // Jika yang login adalah company
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        }
        // Jika yang login adalah user, ambil company_id dari user
        elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }
        
        $product = Product::where('id', $id)
                    ->where('companyID', $companyId)
                    ->firstOrFail();

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }
}
