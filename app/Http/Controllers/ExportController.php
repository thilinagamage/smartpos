<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\ReportService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExportController extends Controller
{
    public function exportSalesExcel(Request $request)
    {
        $sales = Sale::with('customer')
            ->when($request->date_from, function ($query) use ($request) {
                $query->whereDate('sale_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($query) use ($request) {
                $query->whereDate('sale_date', '<=', $request->date_to);
            })
            ->where('status', '!=', 'refunded')
            ->get();

        return Excel::download(new SalesExport($sales), 'sales_' . date('Y_m_d') . '.xlsx');
    }

    public function exportSalesPdf(Request $request)
    {
        $sales = Sale::with('customer')
            ->when($request->date_from, function ($query) use ($request) {
                $query->whereDate('sale_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($query) use ($request) {
                $query->whereDate('sale_date', '<=', $request->date_to);
            })
            ->where('status', '!=', 'refunded')
            ->get();

        $totalRevenue = $sales->sum('total_amount');
        $totalPaid = $sales->sum('paid_amount');

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $html = view('exports.sales-pdf', compact('sales', 'totalRevenue', 'totalPaid'))->render();
        $dompdf->loadHtml($html);
        $dompdf->render();
        
        return $dompdf->stream('sales_' . date('Y_m_d') . '.pdf');
    }

    public function exportDailySalesExcel(Request $request)
    {
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        
        $reportService = new ReportService();
        $report = $reportService->getDailySalesReport($date);

        return Excel::download(new SalesExport($report['sales']), 'daily_sales_' . $date->format('Y_m_d') . '.xlsx');
    }

    public function exportDailySalesPdf(Request $request)
    {
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        
        $reportService = new ReportService();
        $report = $reportService->getDailySalesReport($date);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $html = view('exports.daily-sales-pdf', compact('report', 'date'))->render();
        $dompdf->loadHtml($html);
        $dompdf->render();
        
        return $dompdf->stream('daily_sales_' . $date->format('Y_m_d') . '.pdf');
    }

    public function exportMonthlySalesExcel(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);
        
        $reportService = new ReportService();
        $report = $reportService->getMonthlySalesReport($year, $month);

        return Excel::download(new SalesExport($report['sales']), 'monthly_sales_' . $year . '_' . $month . '.xlsx');
    }

    public function exportMonthlySalesPdf(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);
        
        $reportService = new ReportService();
        $report = $reportService->getMonthlySalesReport($year, $month);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $html = view('exports.monthly-sales-pdf', compact('report', 'year', 'month'))->render();
        $dompdf->loadHtml($html);
        $dompdf->render();
        
        return $dompdf->stream('monthly_sales_' . $year . '_' . $month . '.pdf');
    }

    public function exportProfitExcel(Request $request)
    {
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        
        $reportService = new ReportService();
        $report = $reportService->getDailySalesReport($date);

        $data = $report['sales']->map(function ($sale) {
            return [
                'Invoice' => $sale->invoice_number,
                'Date' => $sale->sale_date,
                'Revenue' => $sale->total_amount,
                'Cost' => $sale->items->sum(fn($item) => $item->quantity * $item->cost_price),
                'Profit' => $sale->total_amount - $sale->items->sum(fn($item) => $item->quantity * $item->cost_price),
            ];
        });

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;
            
            public function __construct($data) {
                $this->data = $data;
            }
            
            public function collection() {
                return $this->data;
            }
            
            public function headings(): array {
                return ['Invoice', 'Date', 'Revenue', 'Cost', 'Profit'];
            }
        }, 'profit_' . $date->format('Y_m_d') . '.xlsx');
    }

    public function exportProfitPdf(Request $request)
    {
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        
        $reportService = new ReportService();
        $report = $reportService->getDailySalesReport($date);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $html = view('exports.profit-pdf', compact('report', 'date'))->render();
        $dompdf->loadHtml($html);
        $dompdf->render();
        
        return $dompdf->stream('profit_' . $date->format('Y_m_d') . '.pdf');
    }

    public function exportInventoryExcel()
    {
        $reportService = new ReportService();
        $report = $reportService->getInventoryReport();

        $data = $report['products']->map(function ($product) {
            return [
                'SKU' => $product->sku,
                'Name' => $product->name,
                'Category' => $product->category->name ?? '-',
                'Brand' => $product->brand->name ?? '-',
                'Cost Price' => $product->cost_price,
                'Selling Price' => $product->selling_price,
                'Stock' => $product->stock_quantity,
                'Stock Value' => $product->cost_price * $product->stock_quantity,
            ];
        });

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;
            
            public function __construct($data) {
                $this->data = $data;
            }
            
            public function collection() {
                return $this->data;
            }
            
            public function headings(): array {
                return ['SKU', 'Name', 'Category', 'Brand', 'Cost Price', 'Selling Price', 'Stock', 'Stock Value'];
            }
        }, 'inventory_' . date('Y_m_d') . '.xlsx');
    }

    public function exportInventoryPdf()
    {
        $reportService = new ReportService();
        $report = $reportService->getInventoryReport();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $html = view('exports.inventory-pdf', compact('report'))->render();
        $dompdf->loadHtml($html);
        $dompdf->render();
        
        return $dompdf->stream('inventory_' . date('Y_m_d') . '.pdf');
    }

    public function exportLowStockExcel()
    {
        $reportService = new ReportService();
        $report = $reportService->getLowStockReport();

        $data = $report['products']->map(function ($product) {
            return [
                'SKU' => $product->sku,
                'Name' => $product->name,
                'Category' => $product->category->name ?? '-',
                'Brand' => $product->brand->name ?? '-',
                'Stock' => $product->stock_quantity,
                'Reorder Level' => $product->reorder_level,
            ];
        });

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;
            
            public function __construct($data) {
                $this->data = $data;
            }
            
            public function collection() {
                return $this->data;
            }
            
            public function headings(): array {
                return ['SKU', 'Name', 'Category', 'Brand', 'Stock', 'Reorder Level'];
            }
        }, 'low_stock_' . date('Y_m_d') . '.xlsx');
    }

    public function exportLowStockPdf()
    {
        $reportService = new ReportService();
        $report = $reportService->getLowStockReport();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $html = view('exports.low-stock-pdf', compact('report'))->render();
        $dompdf->loadHtml($html);
        $dompdf->render();
        
        return $dompdf->stream('low_stock_' . date('Y_m_d') . '.pdf');
    }
}
