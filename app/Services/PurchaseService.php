<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function createPurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $purchase = Purchase::create([
                'invoice_no' => $data['invoice_no'] ?? null,
                'supplier_id' => $data['supplier_id'],
                'user_id' => $data['user_id'],
                'subtotal' => $data['subtotal'],
                'tax_amount' => $data['tax_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'total_amount' => $data['total_amount'],
                'paid_amount' => $data['paid_amount'],
                'due_amount' => $data['due_amount'] ?? 0,
                'payment_status' => $data['payment_status'] ?? 'pending',
                'status' => $data['status'] ?? 'received',
                'purchase_date' => $data['purchase_date'] ?? Carbon::today(),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                
                $previousQuantity = $product->stock_quantity;
                $newQuantity = $previousQuantity + $item['quantity'];
                
                $product->update([
                    'stock_quantity' => $newQuantity,
                    'cost_price' => $item['unit_cost'],
                    'selling_price' => $item['selling_price'] ?? $product->selling_price,
                ]);

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'selling_price' => $item['selling_price'] ?? $product->selling_price,
                    'total' => $item['total'],
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $data['user_id'],
                    'type' => 'purchase',
                    'quantity' => $item['quantity'],
                    'previous_quantity' => $previousQuantity,
                    'new_quantity' => $newQuantity,
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                ]);
            }

            return $purchase;
        });
    }
}
