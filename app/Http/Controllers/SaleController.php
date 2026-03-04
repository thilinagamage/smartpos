<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function index(Request $request)
    {
        $sales = Sale::with(['customer', 'user'])
            ->when($request->invoice_no, function ($query, $invoiceNo) {
                $query->where('invoice_no', 'like', "%{$invoiceNo}%");
            })
            ->when($request->customer_id, function ($query, $customerId) {
                $query->where('customer_id', $customerId);
            })
            ->when($request->payment_status, function ($query, $status) {
                $query->where('payment_status', $status);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->date_from, function ($query) use ($request) {
                $query->whereDate('sale_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($query) use ($request) {
                $query->whereDate('sale_date', '<=', $request->date_to);
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('status', true)
            ->where('stock_quantity', '>', 0)
            ->with(['category', 'brand'])
            ->get();

        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $items = $request->items;
        
        if (is_string($items)) {
            $items = json_decode($items, true);
            $request->merge(['items' => $items]);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                return back()->with('error', 'Product not found.');
            }
            if ($product->stock_quantity < $item['quantity']) {
                return back()->with('error', "Insufficient stock for {$product->name}. Available: {$product->stock_quantity}");
            }
        }

        $invoiceNo = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        
        $saleData = [
            'invoice_no' => $invoiceNo,
            'user_id' => auth()->id(),
            'customer_id' => $request->customer_id,
            'items' => $items,
            'subtotal' => $request->subtotal,
            'item_discount' => $request->item_discount ?? 0,
            'order_discount' => $request->order_discount ?? 0,
            'tax_amount' => $request->tax_amount ?? 0,
            'total_amount' => $request->total_amount,
            'paid_amount' => $request->paid_amount,
            'due_amount' => $request->total_amount - $request->paid_amount,
            'payment_status' => $request->paid_amount >= $request->total_amount ? 'paid' : 'partial',
            'payment_method' => $request->payment_method ?? 'cash',
            'notes' => $request->notes,
        ];

        $sale = $this->saleService->createSale($saleData);

        return redirect()->route('sales.show', $sale->id)
            ->with('success', 'Sale completed successfully!');
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product']);
        
        return view('sales.show', compact('sale'));
    }

    public function print(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items']);
        
        return view('sales.print', compact('sale'));
    }

    public function refund(Request $request, Sale $sale)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $sale->total_amount,
            'refund_reason' => 'required|string|max:500',
        ]);

        $this->saleService->refundSale(
            $sale,
            $request->refund_amount,
            $request->refund_reason
        );

        return redirect()->route('sales.show', $sale->id)
            ->with('success', 'Sale refunded successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->get('q', '');
        
        $sales = Sale::with(['customer', 'user'])
            ->where('invoice_no', 'like', "%{$search}%")
            ->limit(20)
            ->get();

        return response()->json($sales);
    }
}
