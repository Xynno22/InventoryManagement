<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Models\Transaction; // Pastikan model ini ada dan sesuai
use App\Models\TransactionDetail;
use Carbon\Carbon;

class DashboardController extends Controller
{
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
        $recentOrders = Transaction::with('transactionDetails')
        ->where('company_id', $companyId)
        ->orderBy('created_at', 'desc') // urutkan dari yang terbaru
        ->limit(10)                     // ambil 10 data terakhir
        ->get();

        $today = Carbon::today();

        $stockMovements = TransactionDetail::with('product')
            ->whereHas('transaction', function ($query) use ($companyId, $today) {
                $query->where('company_id', $companyId)
                    ->whereDate('date', $today)
                    ->whereIn('type_id', [1, 2]);
            })
            ->select('product_id')
            ->selectRaw('
                SUM(CASE WHEN transactions.type_id = 1 THEN quantity ELSE 0 END) as total_in,
                SUM(CASE WHEN transactions.type_id = 2 THEN quantity ELSE 0 END) as total_out
            ')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->groupBy('product_id')
            ->get()
            ->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'total_in' => $item->total_in,
                    'total_out' => $item->total_out,
                ];
            });
        // Total transaksi hari ini
        $totalSalesToday = Transaction::where('type_id', 1)
            ->whereDate('date', $today)
            ->count();

        $totalRevenue = Transaction::where('type_id', 1)
            ->whereDate('date', $today)
            ->sum('total_price');

        $totalPending = Transaction::where('status_id', 2)->count();

        $promo = Promo::where('end_date', '>=', now())
            ->where('company_id', $companyId)
            ->orderBy('end_date', 'asc')
            ->limit(5)
            ->get();

        // === DATA CHART REAL ===

        // Weekly (7 hari terakhir) - total revenue per hari
        $weeklyLabels = [];
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyLabels[] = $date->format('D'); // Mon, Tue, dll

            $total = Transaction::where('type_id', 1)
                ->whereDate('date', $date)
                ->sum('total_price');

            $weeklyData[] = $total;
        }

        // Monthly (12 bulan terakhir) - total revenue per bulan
        $monthlyLabels = [];
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::today()->subMonths($i);
            $monthlyLabels[] = $date->format('M'); // Jan, Feb, dll

            $total = Transaction::where('type_id', 1)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('total_price');

            $monthlyData[] = $total;
        }

        // Yearly (4 tahun terakhir) - total revenue per tahun
        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 3; $i >= 0; $i--) {
            $year = Carbon::now()->year - $i;
            $yearlyLabels[] = (string) $year;

            $total = Transaction::where('type_id', 1)
                ->whereYear('date', $year)
                ->sum('total_price');

            $yearlyData[] = $total;
        }

        $revenueChartData = [
            'weekly' => [
                'labels' => $weeklyLabels,
                'data' => $weeklyData,
            ],
            'monthly' => [
                'labels' => $monthlyLabels,
                'data' => $monthlyData,
            ],
            'yearly' => [
                'labels' => $yearlyLabels,
                'data' => $yearlyData,
            ],
        ];

        return view('dashboard.dashboard', compact(
            'totalSalesToday',
            'totalPending',
            'totalRevenue',
            'revenueChartData',
            'recentOrders',
            'promo',
            'stockMovements',
        ));
    }
    public function getTopSales(Request $request)
    {
        $range = $request->input('range', '1y'); // default 1 tahun
        $startDate = match ($range) {
            '3m' => Carbon::today()->subMonths(3),
            '6m' => Carbon::today()->subMonths(6),
            default => Carbon::today()->subYear(),
        };

        $topCategories = TransactionDetail::selectRaw('
                product_categories.id,
                product_categories.name,
                SUM(transaction_details.quantity) as total_sold
            ')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('product_categories', 'products.categoryID', '=', 'product_categories.id')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.type_id', 1)
            ->where('transactions.date', '>=', $startDate)
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $topProducts = TransactionDetail::selectRaw('
                products.id,
                products.name,
                SUM(transaction_details.quantity) as total_sold
            ')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.type_id', 1)
            ->where('transactions.date', '>=', $startDate)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return response()->json([
            'topCategories' => $topCategories,
            'topProducts' => $topProducts,
        ]);
    }
    public function getLowStock()
    {
        $lowStockProducts = Stock::with('product')
        ->whereColumn('currentStock', '<=', 'minimumStock')
        ->get();

        return response()->json([
            'products' => $lowStockProducts->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Unnamed Product',
                    'stock' => $item->currentStock
                ];
            })
        ]);
    }

    public function getPaymentTypes(Request $request)
    {
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        }
        // Jika yang login adalah user, ambil company_id dari user
        elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        $range = $request->get('range', '30d');
        $startDate = match ($range) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '3m' => now()->subMonths(3),
            '1y' => now()->subYear(),
            default => now()->subDays(30)
        };

        $data = Transaction::where('company_id', $companyId)
            ->join('payments as p', 'p.id', '=', 'transactions.payment_id')
            ->where('date', '>=', $startDate)
            ->where('type_id', '=', 1)
            ->selectRaw('p.name, COUNT(*) as total')
            ->groupBy('p.name')
            ->get();

        return response()->json($data);
    }

}
