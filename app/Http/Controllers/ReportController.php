<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function dailySales(Request $request)
    {
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        
        $report = $this->reportService->getDailySalesReport($date);

        return view('reports.daily-sales', compact('report', 'date'));
    }

    public function monthlySales(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);
        
        $report = $this->reportService->getMonthlySalesReport($year, $month);

        return view('reports.monthly-sales', compact('report', 'year', 'month'));
    }

    public function profit(Request $request)
    {
        $startDate = $request->get('date_from') ? Carbon::parse($request->get('date_from')) : Carbon::today()->startOfMonth();
        $endDate = $request->get('date_to') ? Carbon::parse($request->get('date_to')) : Carbon::today()->endOfMonth();
        
        $report = $this->reportService->getProfitReport($startDate, $endDate);

        return view('reports.profit', compact('report', 'startDate', 'endDate'));
    }

    public function inventory()
    {
        $report = $this->reportService->getInventoryReport();

        return view('reports.inventory', compact('report'));
    }

    public function lowStock()
    {
        $report = $this->reportService->getLowStockReport();

        return view('reports.low-stock', compact('report'));
    }

    public function warranty(Request $request)
    {
        $expiryDate = $request->get('expiry_date') 
            ? Carbon::parse($request->get('expiry_date')) 
            : Carbon::today()->addMonths(3);
        
        $report = $this->reportService->getWarrantyReport($expiryDate);

        return view('reports.warranty', compact('report', 'expiryDate'));
    }

    public function warrantyLookup(Request $request)
    {
        $request->validate([
            'invoice_no' => 'required|string',
        ]);

        $sale = $this->reportService->searchWarrantyByInvoice($request->invoice_no);

        if (!$sale) {
            return back()->with('error', 'No sale found with this invoice number.');
        }

        return view('reports.warranty-lookup', compact('sale'));
    }
}
