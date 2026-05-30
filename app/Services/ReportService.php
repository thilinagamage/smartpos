<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDailySalesReport(?Carbon $date = null): array
    {
        $date = $date ?? Carbon::today();
        
        $sales = Sale::with(['customer', 'user', 'items'])
            ->whereDate('sale_date', $date)
            ->where('status', '!=', 'refunded')
            ->get();

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total_amount');
        $totalTax = $sales->sum('tax_amount');
        $totalDiscount = $sales->sum('item_discount') + $sales->sum('order_discount');
        $totalProfit = $sales->sum(function ($sale) {
            return $sale->items->sum(function ($item) {
                $product = $item->product;
                return ($item->unit_price - $product->cost_price) * $item->quantity;
            });
        });

        return [
            'date' => $date,
            'sales' => $sales,
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'total_tax' => $totalTax,
            'total_discount' => $totalDiscount,
            'total_profit' => $totalProfit,
        ];
    }

    public function getMonthlySalesReport(int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $sales = Sale::with(['customer', 'user', 'items'])
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', '!=', 'refunded')
            ->get();

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total_amount');
        $totalTax = $sales->sum('tax_amount');
        $totalDiscount = $sales->sum('item_discount') + $sales->sum('order_discount');
        $totalProfit = $sales->sum(function ($sale) {
            return $sale->items->sum(function ($item) {
                $product = $item->product;
                return ($item->unit_price - $product->cost_price) * $item->quantity;
            });
        });

        $dailySales = Sale::select(DB::raw('DATE(sale_date) as date'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', '!=', 'refunded')
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->get();

        return [
            'year' => $year,
            'month' => $month,
            'sales' => $sales,
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'total_tax' => $totalTax,
            'total_discount' => $totalDiscount,
            'total_profit' => $totalProfit,
            'daily_sales' => $dailySales,
        ];
    }

    public function getProfitReport(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::today()->startOfMonth();
        $endDate = $endDate ?? Carbon::today()->endOfMonth();

        $sales = Sale::with(['items.product'])
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', '!=', 'refunded')
            ->get();

        $totalRevenue = $sales->sum('total_amount');
        $totalCost = $sales->sum(function ($sale) {
            return $sale->items->sum(function ($item) {
                return $item->product->cost_price * $item->quantity;
            });
        });
        $totalProfit = $totalRevenue - $totalCost;

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
            'profit_margin' => $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0,
        ];
    }

    public function getInventoryReport(): array
    {
        $products = Product::with(['category', 'brand'])
            ->where('status', true)
            ->get();

        $totalProducts = $products->count();
        $totalStockValue = $products->sum(function ($product) {
            return $product->cost_price * $product->stock_quantity;
        });
        $totalRetailValue = $products->sum(function ($product) {
            return $product->selling_price * $product->stock_quantity;
        });

        $lowStockProducts = $products->filter(function ($product) {
            return $product->isLowStock();
        });

        $outOfStockProducts = $products->filter(function ($product) {
            return $product->stock_quantity <= 0;
        });

        return [
            'products' => $products,
            'total_products' => $totalProducts,
            'total_stock_value' => $totalStockValue,
            'total_retail_value' => $totalRetailValue,
            'potential_profit' => $totalRetailValue - $totalStockValue,
            'low_stock_count' => $lowStockProducts->count(),
            'out_of_stock_count' => $outOfStockProducts->count(),
        ];
    }

    public function getLowStockReport(): array
    {
        $products = Product::with(['category', 'brand'])
            ->whereRaw('stock_quantity <= reorder_level')
            ->where('status', true)
            ->orderBy('stock_quantity', 'asc')
            ->get();

        return [
            'products' => $products,
            'count' => $products->count(),
        ];
    }

    public function getWarrantyReport(?Carbon $expiryDate = null): array
    {
        $expiryDate = $expiryDate ?? Carbon::today()->addMonths(3);

        $warrantyItems = SaleItem::with(['sale.customer', 'sale.user', 'product'])
            ->whereNotNull('warranty_expiry')
            ->where('warranty_expiry', '<=', $expiryDate)
            ->whereHas('sale', function ($query) {
                $query->where('status', 'completed');
            })
            ->orderBy('warranty_expiry', 'asc')
            ->get();

        return [
            'items' => $warrantyItems,
            'count' => $warrantyItems->count(),
            'expiry_date' => $expiryDate,
        ];
    }

    public function searchWarrantyByInvoice(string $invoiceNo): ?Sale
    {
        return Sale::with(['items.product', 'customer', 'user'])
            ->where('invoice_no', 'like', "%{$invoiceNo}%")
            ->where('status', 'completed')
            ->first();
    }

    public function getSalesChartData(int $days = 30): array
    {
        $startDate = Carbon::today()->subDays($days);
        
        $salesData = Sale::select(
            DB::raw('DATE(sale_date) as date'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('sale_date', '>=', $startDate)
            ->where('status', '!=', 'refunded')
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy('date', 'asc')
            ->get();

        return $salesData->pluck('total', 'date')->toArray();
    }
}
