<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\ReportService;
use App\Services\SaleService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $saleService;
    protected $stockService;
    protected $reportService;

    public function __construct(
        SaleService $saleService,
        StockService $stockService,
        ReportService $reportService
    ) {
        $this->saleService = $saleService;
        $this->stockService = $stockService;
        $this->reportService = $reportService;
    }

    public function index()
    {
        $todaySales = $this->saleService->getDailySales();
        $todayRevenue = $this->saleService->getTodayRevenue();
        
        $monthlyRevenue = $this->saleService->getMonthlyRevenue(
            Carbon::now()->year,
            Carbon::now()->month
        );

        $totalProducts = Product::where('status', true)->count();
        $totalCustomers = Customer::where('status', true)->count();
        $lowStockProducts = $this->stockService->getLowStockProducts();

        $salesChartData = $this->reportService->getSalesChartData(30);

        return view('dashboard.index', compact(
            'todaySales',
            'todayRevenue',
            'monthlyRevenue',
            'totalProducts',
            'totalCustomers',
            'lowStockProducts',
            'salesChartData'
        ));
    }
}
