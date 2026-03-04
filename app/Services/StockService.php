<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function adjustStock(int $productId, int $quantity, string $type, string $notes = null, int $userId = null): Product
    {
        return DB::transaction(function () use ($productId, $quantity, $type, $notes, $userId) {
            $product = Product::findOrFail($productId);
            $previousQuantity = $product->stock_quantity;
            
            switch ($type) {
                case 'adjustment_in':
                    $newQuantity = $previousQuantity + $quantity;
                    $movementType = 'adjustment_in';
                    break;
                case 'adjustment_out':
                    $newQuantity = $previousQuantity - $quantity;
                    $movementType = 'adjustment_out';
                    break;
                default:
                    throw new \InvalidArgumentException("Invalid stock adjustment type: {$type}");
            }

            $product->update(['stock_quantity' => $newQuantity]);

            StockMovement::create([
                'product_id' => $productId,
                'user_id' => $userId ?? auth()->id(),
                'type' => $movementType,
                'quantity' => $type === 'adjustment_in' ? $quantity : -$quantity,
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $newQuantity,
                'notes' => $notes,
            ]);

            return $product;
        });
    }

    public function getStockHistory(int $productId): \Illuminate\Database\Eloquent\Collection
    {
        return StockMovement::with('user')
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getLowStockProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::whereRaw('stock_quantity <= reorder_level')
            ->where('status', true)
            ->with(['category', 'brand'])
            ->get();
    }

    public function getStockMovements(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = StockMovement::with(['product', 'user']);

        if (isset($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
