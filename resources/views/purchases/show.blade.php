@extends('layouts.main')

@section('title', 'Purchase Details - SmartPOS')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Purchase: {{ $purchase->invoice_no }}</span>
        <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Supplier Information</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $purchase->supplier->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td>{{ $purchase->supplier->phone }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td>{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Payment Information</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td>{{ number_format($purchase->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Paid:</strong></td>
                        <td>{{ number_format($purchase->paid_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Due:</strong></td>
                        <td>{{ number_format($purchase->due_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge bg-{{ $purchase->payment_status == 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($purchase->payment_status) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <h5>Items</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_cost, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
