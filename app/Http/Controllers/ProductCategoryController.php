<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    // Menampilkan semua kategori
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

        // Query kategori berdasarkan company_id
        $query = ProductCategory::where('company_id', $companyId);

        // Filtering by search
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        if ($request->has('sort') && in_array($request->sort, ['asc', 'desc'])) {
            $query->orderBy('name', $request->sort);
        }

        $categories = $query->paginate(10);

        return view('product_category.index', compact('categories'));
    }

  
    public function create()
    {
        return view('product_category.add');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:product_categories|max:255',
        ]);

        // Cek siapa yang login
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        } elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        // Simpan kategori dengan company_id yang sesuai
        ProductCategory::create([
            'name' => $request->name,
            'company_id' => $companyId,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category added successfully!');
    }


    // Menampilkan form edit kategori
    public function edit($id)
    {
        $category = ProductCategory::findOrFail($id);
        return view('product_category.edit', compact('category'));
    }

    // Memperbarui kategori
    public function update(Request $request, $id)
    {
        $category = ProductCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:product_categories,name,' . $id . '|max:255',
        ]);

        $category->update(['name' => $request->name]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
    }

    // Menghapus kategori
    public function destroy($id)
    {
        // Cek siapa yang login
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        } elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        // Hanya bisa menghapus kategori yang sesuai dengan company_id
        $category = ProductCategory::where('id', $id)
                    ->where('company_id', $companyId)
                    ->firstOrFail();

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }
}
