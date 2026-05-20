<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseRequest;
use App\Models\Sale;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $paymentStatus = $request->query('payment_status');

        $sales = Sale::with(['customer', 'user'])
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($paymentStatus, function ($query, $paymentStatus) {
                $query->where('payment_status', $paymentStatus);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summaryQuery = Sale::query()
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($paymentStatus, function ($query, $paymentStatus) {
                $query->where('payment_status', $paymentStatus);
            });

        $totalInvoices = (clone $summaryQuery)->count();
        $totalSalesAmount = (clone $summaryQuery)->sum('total');
        $paidAmount = (clone $summaryQuery)->where('payment_status', 'paid')->sum('total');
        $unpaidAmount = (clone $summaryQuery)->where('payment_status', 'unpaid')->sum('total');

        $salesChartData = Sale::query()
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($paymentStatus, function ($query, $paymentStatus) {
                $query->where('payment_status', $paymentStatus);
            })
            ->selectRaw('DATE(created_at) as sale_date, SUM(total) as total_amount')
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get();

        $salesChartLabels = $salesChartData->pluck('sale_date');
        $salesChartValues = $salesChartData->pluck('total_amount');

        return view('reports.sales', compact(
            'sales',
            'dateFrom',
            'dateTo',
            'paymentStatus',
            'totalInvoices',
            'totalSalesAmount',
            'paidAmount',
            'unpaidAmount',
            'salesChartLabels',
            'salesChartValues'
        ));
    }

    public function inventory(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $stockStatus = $request->query('stock_status');

        $categories = Category::orderBy('name')->get();

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('sku', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($stockStatus === 'low_stock', function ($query) {
                $query->whereColumn('quantity', '<=', 'reorder_level');
            })
            ->when($stockStatus === 'available', function ($query) {
                $query->whereColumn('quantity', '>', 'reorder_level');
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $summaryQuery = Product::query()
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            });

        $totalProducts = (clone $summaryQuery)->count();
        $lowStockProducts = (clone $summaryQuery)->whereColumn('quantity', '<=', 'reorder_level')->count();
        $totalStockQuantity = (clone $summaryQuery)->sum('quantity');
        $totalStockValue = (clone $summaryQuery)
            ->selectRaw('SUM(quantity * cost_price) as value')
            ->value('value') ?? 0;

        $inventoryChartData = Product::with('category')
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderByDesc('quantity')
            ->take(10)
            ->get();

        $inventoryChartLabels = $inventoryChartData->pluck('name');
        $inventoryChartValues = $inventoryChartData->pluck('quantity');

        return view('reports.inventory', compact(
            'products',
            'categories',
            'search',
            'categoryId',
            'stockStatus',
            'totalProducts',
            'lowStockProducts',
            'totalStockQuantity',
            'totalStockValue',
            'inventoryChartLabels',
            'inventoryChartValues'
        ));
    }

    public function purchaseRequests(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $status = $request->query('status');
        $supplierId = $request->query('supplier_id');

        $suppliers = Supplier::orderBy('name')->get();

        $purchaseRequests = PurchaseRequest::with(['supplier', 'requester', 'approver'])
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($supplierId, function ($query, $supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summaryQuery = PurchaseRequest::query()
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($supplierId, function ($query, $supplierId) {
                $query->where('supplier_id', $supplierId);
            });

        $totalRequests = (clone $summaryQuery)->count();
        $pendingRequests = (clone $summaryQuery)->where('status', 'pending')->count();
        $approvedRequests = (clone $summaryQuery)->where('status', 'approved')->count();
        $estimatedTotal = (clone $summaryQuery)->sum('estimated_total');

        $purchaseStatusChartData = PurchaseRequest::query()
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($supplierId, function ($query, $supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        $purchaseChartLabels = $purchaseStatusChartData
            ->pluck('status')
            ->map(fn($status) => ucfirst($status));

        $purchaseChartValues = $purchaseStatusChartData->pluck('total');

        return view('reports.purchase-requests', compact(
            'purchaseRequests',
            'suppliers',
            'dateFrom',
            'dateTo',
            'status',
            'supplierId',
            'totalRequests',
            'pendingRequests',
            'approvedRequests',
            'estimatedTotal',
            'purchaseChartLabels',
            'purchaseChartValues'
        ));
    }

    public function salesPdf(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $paymentStatus = $request->query('payment_status');

        $sales = Sale::with(['customer', 'user'])
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($paymentStatus, function ($query, $paymentStatus) {
                $query->where('payment_status', $paymentStatus);
            })
            ->latest()
            ->get();

        $totalInvoices = $sales->count();
        $totalSalesAmount = $sales->sum('total');
        $paidAmount = $sales->where('payment_status', 'paid')->sum('total');
        $unpaidAmount = $sales->where('payment_status', 'unpaid')->sum('total');

        $pdf = Pdf::loadView('reports.pdf.sales', compact(
            'sales',
            'dateFrom',
            'dateTo',
            'paymentStatus',
            'totalInvoices',
            'totalSalesAmount',
            'paidAmount',
            'unpaidAmount'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('sales-report.pdf');
    }

    public function inventoryPdf(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $stockStatus = $request->query('stock_status');

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('sku', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($stockStatus === 'low_stock', function ($query) {
                $query->whereColumn('quantity', '<=', 'reorder_level');
            })
            ->when($stockStatus === 'available', function ($query) {
                $query->whereColumn('quantity', '>', 'reorder_level');
            })
            ->orderBy('name')
            ->get();

        $totalProducts = $products->count();
        $lowStockProducts = $products->filter(fn($product) => $product->quantity <= $product->reorder_level)->count();
        $totalStockQuantity = $products->sum('quantity');
        $totalStockValue = $products->sum(fn($product) => $product->quantity * $product->cost_price);

        $pdf = Pdf::loadView('reports.pdf.inventory', compact(
            'products',
            'search',
            'categoryId',
            'stockStatus',
            'totalProducts',
            'lowStockProducts',
            'totalStockQuantity',
            'totalStockValue'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('inventory-report.pdf');
    }

    public function purchaseRequestsPdf(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $status = $request->query('status');
        $supplierId = $request->query('supplier_id');

        $purchaseRequests = PurchaseRequest::with(['supplier', 'requester', 'approver'])
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($supplierId, function ($query, $supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->latest()
            ->get();

        $totalRequests = $purchaseRequests->count();
        $pendingRequests = $purchaseRequests->where('status', 'pending')->count();
        $approvedRequests = $purchaseRequests->where('status', 'approved')->count();
        $estimatedTotal = $purchaseRequests->sum('estimated_total');

        $pdf = Pdf::loadView('reports.pdf.purchase-requests', compact(
            'purchaseRequests',
            'dateFrom',
            'dateTo',
            'status',
            'supplierId',
            'totalRequests',
            'pendingRequests',
            'approvedRequests',
            'estimatedTotal'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('purchase-request-report.pdf');
    }
}
