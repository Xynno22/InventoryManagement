<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    // Menampilkan semua kategori
    public function index()
    {
        $categories = ProductCategory::where('company_id', auth('company')->id())->paginate(10);
        return view('product_category.index', compact('categories'));
    }

    // Menampilkan form tambah kategori
    // public function view()
    // {
    //     $categories = ProductCategory::all();
    //     return view('categories.index', ['categories' => $categories]);
    // }

    // Menyimpan kategori baru
    public function create()
    {
        return view('product_category.add');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:product_categories|max:255',
        ]);

        ProductCategory::create(['name' => $request->name, 'company_id' => auth('company')->id()]);

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
        $category = ProductCategory::where('id', $id)
                ->where('company_id', auth('company')->id())
                // Ensures the category belongs to the logged-in company
                ->firstOrFail();
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }
}
