@extends('layouts.main')

@section('title', 'Stock History - SmartPOS')
@section('page-title', 'Stock History')

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('stock.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        @if($product)
        <div class="mb-3">
            <strong>Product:</strong> {{ $product->name }} | 
            <strong>Current Stock:</strong> {{ $product->stock_quantity }}
        </div>
        @endif
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Previous</th>
                    <th>New</th>
                    <th>User</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $movement->product->name }}</td>
                    <td>
                        @switch($movement->type)
                            @case('purchase')
                            <span class="badge bg-success">Purchase</span>
                            @case('sale')
                            <span class="badge bg-danger">Sale</span>
                            @case('adjustment_in')
                            <span class="badge bg-info">Stock In</span>
                            @case('adjustment_out')
                            <span class="badge bg-warning">Stock Out</span>
                            @case('return')
                            <span class="badge bg-primary">Return</span>
                            @default
                            <span class="badge bg-secondary">{{ $movement->type }}</span>
                        @endswitch
                    </td>
                    <td>{{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}</td>
                    <td>{{ $movement->previous_quantity }}</td>
                    <td>{{ $movement->new_quantity }}</td>
                    <td>{{ $movement->user->name }}</td>
                    <td>{{ $movement->notes }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No stock movements found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
