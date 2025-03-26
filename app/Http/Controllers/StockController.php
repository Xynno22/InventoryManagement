<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use Carbon\Carbon;

class StockController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan ID perusahaan berdasarkan siapa yang login
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        } elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        // Ambil input search dan sort dari request
        $search = $request->input('search');
        $sort = $request->input('sort');

        // Cek apakah ada sort, jika ada maka pakai join
        if ($sort && in_array($sort, ['asc', 'desc'])) {
            $query = Stock::join('products', 'stocks.productID', '=', 'products.id')
                ->where('stocks.companyID', $companyId)
                ->select('stocks.*', 'products.name as product_name') // Pilih kolom dengan alias
                ->orderBy('product_name', $sort);
        } else {
            // Jika tidak ada sorting, cukup pakai with()
            $query = Stock::where('companyID', $companyId)->with(['product', 'company']);
        }

        // Filter pencarian berdasarkan nama produk
        if ($search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        // Ambil data stok dengan pagination
        $stocks = $query->paginate(10)->appends(request()->query());

        // Kirim data ke Blade
        return view('stock.index', ['stocks' => $stocks]);
    }



    // public function create()
    // {
    //     return view('stocks.create');
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'productID' => 'required|exists:products,id',
    //         'currentStock' => 'required|numeric|min:0',
    //         'minimumStock' => 'required|numeric|min:0',
    //         'userID' => 'required|exists:users,id',
    //     ]);

    //     Stock::create([
    //         'productID' => $request->productID,
    //         'currentStock' => $request->currentStock,
    //         'minimumStock' => $request->minimumStock,
    //         'lastUpdated' => Carbon::now(),
    //         'userID' => $request->userID,
    //         'totalOrder' => 0
    //     ]);

    //     return redirect()->route('stocks.index')->with('success', 'Stock added successfully.');
    // }

    public function edit($id)
    {
        $stock = Stock::findOrFail($id);
        return view('stock.edit', compact('stock'));
    }

    public function update(Request $request, $id)
    {
        $stock = Stock::findOrFail($id);

        $request->validate([
            'minimumStock' => 'required|numeric|min:0',
        ]);

        $stock->update([
            'minimumStock' => $request->minimumStock,
            'lastUpdated' => Carbon::now(),
        ]);

        return redirect()->route('stocks.index')->with('success', 'Stock updated successfully.');
    }

    public function destroy($id)
    {
        Stock::findOrFail($id)->delete();
        return redirect()->route('stocks.index')->with('success', 'Stock deleted successfully.');
    }
}
