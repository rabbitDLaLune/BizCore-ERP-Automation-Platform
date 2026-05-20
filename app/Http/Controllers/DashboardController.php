<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseRequest;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalCategories = Category::count();
        $totalProducts = Product::count();
        $lowStockProducts = Product::whereColumn('quantity', '<=', 'reorder_level')->count();

        $totalCustomers = Customer::count();
        $totalSuppliers = Supplier::count();

        $totalSales = Sale::count();
        $totalSalesAmount = Sale::sum('total');
        $todaySalesAmount = Sale::whereDate('created_at', today())->sum('total');

        $pendingPurchaseRequests = PurchaseRequest::where('status', 'pending')->count();
        $approvedPurchaseRequests = PurchaseRequest::where('status', 'approved')->count();

        $recentSales = Sale::with(['customer', 'user'])
            ->latest()
            ->take(5)
            ->get();

        $recentStockMovements = StockMovement::with(['product', 'user'])
            ->latest()
            ->take(5)
            ->get();

        $recentPurchaseRequests = PurchaseRequest::with(['supplier', 'requester'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalUsers',
            'totalCategories',
            'totalProducts',
            'lowStockProducts',
            'totalCustomers',
            'totalSuppliers',
            'totalSales',
            'totalSalesAmount',
            'todaySalesAmount',
            'pendingPurchaseRequests',
            'approvedPurchaseRequests',
            'recentSales',
            'recentStockMovements',
            'recentPurchaseRequests'
        ));
    }
}
