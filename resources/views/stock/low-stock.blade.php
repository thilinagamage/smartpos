@extends('layouts.main')

@section('title', 'Low Stock - SmartPOS')
@section('page-title', 'Low Stock Products')

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('stock.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        @if($products->count() > 0)
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Reorder Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>
                        <span class="badge bg-danger">{{ $product->stock_quantity }}</span>
                    </td>
                    <td>{{ $product->reorder_level }}</td>
                    <td>
                        <a href="{{ route('stock.adjust', $product->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-plus"></i> Add Stock
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center text-muted">
            <i class="fas fa-check-circle fa-3x mb-3"></i>
            <p>No low stock products!</p>
        </div>
        @endif
    </div>
</div>
@endsection
