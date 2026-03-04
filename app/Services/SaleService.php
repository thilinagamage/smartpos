<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $sale = Sale::create([
                'invoice_no' => $data['invoice_no'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'user_id' => $data['user_id'],
                'subtotal' => $data['subtotal'],
                'item_discount' => $data['item_discount'] ?? 0,
                'order_discount' => $data['order_discount'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'total_amount' => $data['total_amount'],
                'paid_amount' => $data['paid_amount'],
                'due_amount' => $data['due_amount'] ?? 0,
                'payment_status' => $data['payment_status'] ?? 'paid',
                'payment_method' => $data['payment_method'] ?? 'cash',
                'status' => 'completed',
                'sale_date' => isset($data['sale_date']) ? ($data['sale_date'] instanceof \Carbon\Carbon ? $data['sale_date'] : \Carbon\Carbon::parse($data['sale_date'])) : now(),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                
                $warrantyExpiry = null;
                if ($product->warranty_days > 0) {
                    $warrantyExpiry = Carbon::now()->addDays($product->warranty_days);
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'unit_discount' => $item['unit_discount'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'total' => $item['total'],
                    'warranty_expiry' => $warrantyExpiry,
                ]);

                $previousQuantity = $product->stock_quantity;
                $newQuantity = $previousQuantity - $item['quantity'];
                
                $product->update(['stock_quantity' => $newQuantity]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $data['user_id'],
                    'type' => 'sale',
                    'quantity' => -$item['quantity'],
                    'previous_quantity' => $previousQuantity,
                    'new_quantity' => $newQuantity,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                ]);
            }

            return $sale;
        });
    }

    public function refundSale(Sale $sale, float $amount, string $reason): Sale
    {
        return DB::transaction(function () use ($sale, $amount, $reason) {
            foreach ($sale->items as $item) {
                $product = $item->product;
                $previousQuantity = $product->stock_quantity;
                $refundQuantity = ($item->quantity / $sale->total_amount) * $amount;
                $newQuantity = $previousQuantity + $refundQuantity;
                
                $product->update(['stock_quantity' => $newQuantity]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'return',
                    'quantity' => $refundQuantity,
                    'previous_quantity' => $previousQuantity,
                    'new_quantity' => $newQuantity,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'notes' => 'Refund for sale: ' . $sale->invoice_no,
                ]);
            }

            $sale->update([
                'status' => 'refunded',
                'refund_amount' => $amount,
                'refund_reason' => $reason,
            ]);

            return $sale;
        });
    }

    public function getDailySales(?Carbon $date = null): \Illuminate\Database\Eloquent\Collection
    {
        $date = $date ?? Carbon::today();
        
        return Sale::with(['customer', 'user'])
            ->whereDate('sale_date', $date)
            ->where('status', '!=', 'refunded')
            ->orderBy('sale_date', 'desc')
            ->get();
    }

    public function getMonthlySales(int $year, int $month): \Illuminate\Database\Eloquent\Collection
    {
        return Sale::with(['customer', 'user'])
            ->whereYear('sale_date', $year)
            ->whereMonth('sale_date', $month)
            ->where('status', '!=', 'refunded')
            ->orderBy('sale_date', 'desc')
            ->get();
    }

    public function getTodayRevenue(): float
    {
        return Sale::whereDate('sale_date', Carbon::today())
            ->where('status', '!=', 'refunded')
            ->sum('total_amount');
    }

    public function getMonthlyRevenue(int $year, int $month): float
    {
        return Sale::whereYear('sale_date', $year)
            ->whereMonth('sale_date', $month)
            ->where('status', '!=', 'refunded')
            ->sum('total_amount');
    }
}
