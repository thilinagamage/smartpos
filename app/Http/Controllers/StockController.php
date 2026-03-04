<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $products = Product::with(['category', 'brand'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->when($request->low_stock === '1', function ($query) {
                $query->whereRaw('stock_quantity <= reorder_level');
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('stock.index', compact('products'));
    }

    public function history(Request $request)
    {
        $productId = $request->get('product_id');
        
        if ($productId) {
            $movements = $this->stockService->getStockHistory($productId);
            $product = Product::find($productId);
        } else {
            $movements = $this->stockService->getStockMovements($request->all());
            $product = null;
        }

        return view('stock.history', compact('movements', 'product'));
    }

    public function adjustForm(Product $product)
    {
        return view('stock.adjust', compact('product'));
    }

    public function adjust(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:adjustment_in,adjustment_out',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($request->type === 'adjustment_out' && $product->stock_quantity < $request->quantity) {
            return back()->with('error', 'Insufficient stock for adjustment.');
        }

        $this->stockService->adjustStock(
            $product->id,
            $request->quantity,
            $request->type,
            $request->notes
        );

        return redirect()->route('stock.index')
            ->with('success', 'Stock adjusted successfully.');
    }

    public function lowStock()
    {
        $products = $this->stockService->getLowStockProducts();
        
        return view('stock.low-stock', compact('products'));
    }
}
