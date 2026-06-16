<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    protected $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function index(Request $request)
    {
        $purchases = Purchase::with(['supplier', 'user'])
            ->when($request->invoice_no, function ($query, $invoiceNo) {
                $query->where('invoice_no', 'like', "%{$invoiceNo}%");
            })
            ->when($request->supplier_id, function ($query, $supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->payment_status, function ($query, $status) {
                $query->where('payment_status', $status);
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', true)->get();
        $products = Product::where('status', true)->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $invoiceNo = 'PUR-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        
        $purchaseData = [
            'invoice_no' => $invoiceNo,
            'supplier_id' => $request->supplier_id,
            'user_id' => auth()->id(),
            'items' => $request->items,
            'subtotal' => $request->subtotal,
            'tax_amount' => $request->tax_amount ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'total_amount' => $request->total_amount,
            'paid_amount' => $request->paid_amount,
            'due_amount' => $request->total_amount - $request->paid_amount,
            'payment_status' => $request->paid_amount >= $request->total_amount ? 'paid' : 'partial',
            'status' => $request->status ?? 'received',
            'purchase_date' => $request->purchase_date ?? now(),
            'notes' => $request->notes,
        ];

        $purchase = $this->purchaseService->createPurchase($purchaseData);

        return redirect()->route('purchases.show', $purchase->id)
            ->with('success', 'Purchase created successfully!');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product']);
        
        return view('purchases.show', compact('purchase'));
    }
}
