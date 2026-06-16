@extends('layouts.main')

@section('title', 'Print Receipt - SmartPOS')

@section('content')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $sale->invoice_no }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; }
        .receipt { max-width: 80mm; margin: 0 auto; padding: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        .border-bottom { border-bottom: 1px dashed #000; }
        .border-top { border-top: 1px dashed #000; }
        .fw-bold { font-weight: bold; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="text-center border-bottom pb-3">
            <h4>{{ \App\Models\Setting::get('shop_name', 'SmartPOS') }}</h4>
            <p>{{ \App\Models\Setting::get('shop_address', '') }}<br>
            {{ \App\Models\Setting::get('shop_phone', '') }}</p>
        </div>
        
        <div class="border-bottom py-2">
            <p class="mb-0">Invoice: {{ $sale->invoice_no }}</p>
            <p class="mb-0">Date: {{ $sale->sale_date->format('Y-m-d H:i') }}</p>
            <p class="mb-0">Customer: {{ $sale->customer->name ?? 'Walk-in' }}</p>
            <p class="mb-0">Cashier: {{ $sale->user->name }}</p>
        </div>
        
        <table class="border-bottom py-2">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="border-bottom py-2">
            <div class="d-flex justify-content-between">
                <span>Subtotal:</span>
                <span>{{ number_format($sale->subtotal, 2) }}</span>
            </div>
            @if($sale->item_discount > 0)
            <div class="d-flex justify-content-between">
                <span>Discount:</span>
                <span>-{{ number_format($sale->item_discount + $sale->order_discount, 2) }}</span>
            </div>
            @endif
            @if($sale->tax_amount > 0)
            <div class="d-flex justify-content-between">
                <span>Tax:</span>
                <span>{{ number_format($sale->tax_amount, 2) }}</span>
            </div>
            @endif
            <div class="d-flex justify-content-between fw-bold">
                <span>TOTAL:</span>
                <span>{{ number_format($sale->total_amount, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Paid:</span>
                <span>{{ number_format($sale->paid_amount, 2) }}</span>
            </div>
            @if($sale->due_amount > 0)
            <div class="d-flex justify-content-between">
                <span>Due:</span>
                <span>{{ number_format($sale->due_amount, 2) }}</span>
            </div>
            @endif
        </div>
        
        <div class="text-center border-top pt-3">
            <p>{{ \App\Models\Setting::get('receipt_footer', 'Thank you for your purchase!') }}</p>
        </div>
        
        <div class="text-center no-print mt-3">
            <button onclick="window.print()" class="btn btn-primary">Print</button>
            <button onclick="window.close()" class="btn btn-secondary">Close</button>
        </div>
    </div>
</body>
</html>
@endsection
