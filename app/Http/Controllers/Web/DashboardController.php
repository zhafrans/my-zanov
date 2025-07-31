<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get date ranges
        $today = Carbon::today();
        $lastMonth = Carbon::today()->subMonth();
        $startOfMonth = Carbon::today()->startOfMonth();
        $startOfLastMonth = Carbon::today()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::today()->subMonth()->endOfMonth();
        
        // 1. Total Sales Stats
        $totalSales = Transaction::where('status', 'paid')->sum('deal_price');
        $lastMonthSales = Transaction::where('status', 'paid')
            ->whereBetween('transaction_date', [$startOfLastMonth, $endOfLastMonth])
            ->sum('deal_price');
        $salesChange = $lastMonthSales > 0 ? 
            round(($totalSales - $lastMonthSales) / $lastMonthSales * 100, 2) : 100;
        
        // 2. Products Sold Stats
        $productsSold = TransactionItem::count();
        $lastMonthProducts = TransactionItem::whereHas('transaction', function($query) use ($startOfLastMonth, $endOfLastMonth) {
            $query->whereBetween('transaction_date', [$startOfLastMonth, $endOfLastMonth]);
        })->count();
        $productsChange = $lastMonthProducts > 0 ? 
            round(($productsSold - $lastMonthProducts) / $lastMonthProducts * 100, 2) : 100;
        
        // 3. New Customers Stats
        $newCustomers = Customer::whereDate('created_at', '>=', $startOfMonth)->count();
        $lastMonthCustomers = Customer::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $customersChange = $lastMonthCustomers > 0 ? 
            round(($newCustomers - $lastMonthCustomers) / $lastMonthCustomers * 100, 2) : 100;
        
        // 4. Outstanding Payments
        $outstandingQuery = DB::table('transaction_outstandings')
            ->select(DB::raw('SUM(outstanding_amount) as total_outstanding'));
        $outstandingResult = $outstandingQuery->first();
        $totalOutstanding = $outstandingResult->total_outstanding ?? 0;
        
        $totalPotential = Transaction::sum('deal_price');
        $productionRate = $totalPotential > 0 ? 
            round(($totalPotential - $totalOutstanding) / $totalPotential * 100, 2) : 100;
        $lastMonthRate = 75; // This would need historical data to calculate properly
        
        // 5. Sales Chart Data (last 90 days)
        $salesData = Transaction::select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(deal_price) as total')
            )
            ->where('transaction_date', '>=', Carbon::today()->subDays(90))
            ->where('status', 'paid')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // 6. Top Selling Products
        $topProducts = ProductVariant::select('product_variants.other_code', 
                DB::raw('COUNT(transaction_items.id) as sales_count'),
                DB::raw('SUM(transaction_items.snapshot_price) as total_sales')
            )
            ->join('transaction_items', 'transaction_items.product_variant_id', '=', 'product_variants.id')
            ->groupBy('product_variants.other_code')
            ->orderByDesc('sales_count')
            ->limit(4)
            ->get();
        
        // 7. Recent Transactions
        $recentTransactions = Transaction::with('customer')
            ->orderByDesc('transaction_date')
            ->limit(5)
            ->get();
        
        return view('dashboard.index', compact(
            'user',
            'totalSales',
            'salesChange',
            'productsSold',
            'productsChange',
            'newCustomers',
            'customersChange',
            'productionRate',
            'lastMonthRate',
            'salesData',
            'topProducts',
            'recentTransactions',
            'totalOutstanding',
            'totalPotential'
        ));
    }
}
