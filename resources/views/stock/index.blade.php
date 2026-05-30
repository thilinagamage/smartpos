@extends('layouts.main')

@section('title', 'Stock Management - SmartPOS')
@section('page-title', 'Stock Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Products Stock</span>
        <div>
            <a href="{{ route('stock.low-stock') }}" class="btn btn-sm btn-danger">
                <i class="fas fa-exclamation-triangle"></i> Low Stock
            </a>
            <a href="{{ route('stock.history') }}" class="btn btn-sm btn-info">
                <i class="fas fa-history"></i> History
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                    <th>Stock</th>
                    <th>Reorder Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>{{ number_format($product->cost_price, 2) }}</td>
                    <td>{{ number_format($product->selling_price, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $product->stock_quantity <= $product->reorder_level ? 'danger' : 'success' }}">
                            {{ $product->stock_quantity }}
                        </span>
                    </td>
                    <td>{{ $product->reorder_level }}</td>
                    <td>
                        <a href="{{ route('stock.adjust', $product->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-adjust"></i> Adjust
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No products found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        {{ $products->links() }}
    </div>
</div>
@endsection
