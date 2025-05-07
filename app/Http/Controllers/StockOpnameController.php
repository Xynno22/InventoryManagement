<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockOpnameController extends Controller
{
    public function index()
    {
        // Mendapatkan ID perusahaan berdasarkan siapa yang login
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        } elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        $stockOpnames = StockOpname::with('product')->where('company_id', $companyId)->paginate(10);
        return view('stock_opname.index', compact('stockOpnames'));
    }

    public function create()
    {
        $products = Product::all();
        return view('stock_opname.create', compact('products'));
    }
    public function getSystemStock(Request $request)
    {
        // Get the product ID from the request
        $productId = $request->input('product_id');


        // Query to fetch the current stock from the stocks table
        $stock = DB::table('stocks')->where('productID', $productId)->first();

        // Return the current stock as a JSON response
        if ($stock) {
            return response()->json([
                'system_stock' => $stock->currentStock,
            ]);
        } else {
            return response()->json([
                'error' => 'Product not found',
            ], 404);
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'actual_stock' => 'required|integer',
            'note' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $productId = $request->input('product_id');


        // Query to fetch the current stock from the stocks table
        $stock = DB::table('stocks')->where('productID', $productId)->first();

        $systemStock = $stock->currentStock;
        $actualStock = $request->actual_stock;
        $difference =  $systemStock - $actualStock ;

        // Mendapatkan ID perusahaan berdasarkan siapa yang login
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        } elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }
        StockOpname::create([
            'company_id'   => $companyId,
            'product_id'   => $product->id,
            'system_stock' => $systemStock,
            'actual_stock' => $actualStock,
            'difference'   => $difference,
            'note'         => $request->note,
        ]);

        DB::table('stocks')
            ->where('productID', $productId)
            ->update(['currentStock' => $actualStock]);

        return redirect()->route('opname.index')->with('success', 'Stock opname saved and stock updated!');
    }

    public function show(StockOpname $stockOpname)
    {
        //
    }

    public function edit(StockOpname $stockOpname)
    {
        //
    }

    public function update(Request $request, StockOpname $stockOpname)
    {
        //
    }

    public function destroy($id)
    {
        StockOpname::findOrFail($id)->delete();
        return redirect()->route('opname.index')->with('success', 'Stock opname deleted successfully!');
    }
}
