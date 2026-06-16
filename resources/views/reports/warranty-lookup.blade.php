@extends('layouts.main')

@section('title', 'Warranty Lookup - SmartPOS')

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('reports.warranty') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <h5>Invoice: {{ $sale->invoice_no }}</h5>
        <table class="table table-sm mb-4">
            <tr>
                <td><strong>Customer:</strong></td>
                <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
            </tr>
            <tr>
                <td><strong>Date:</strong></td>
                <td>{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td>{{ number_format($sale->total_amount, 2) }}</td>
            </tr>
        </table>
        
        <h6>Warranty Information</h6>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Warranty Period</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->product->warranty_days ?? 0 }} days</td>
                    <td>{{ $item->warranty_expiry?->format('Y-m-d') ?? 'N/A' }}</td>
                    <td>
                        @if($item->warranty_expiry)
                            @if($item->warranty_expiry >= now())
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Expired</span>
                            @endif
                        @else
                        <span class="badge bg-secondary">No Warranty</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
