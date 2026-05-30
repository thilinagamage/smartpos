@extends('layouts.main')

@section('title', 'Product Details - SmartPOS')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Product: {{ $product->name }}</span>
        <div>
            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Product Information</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>SKU:</strong></td>
                        <td>{{ $product->sku }}</td>
                    </tr>
                    <tr>
                        <td><strong>Barcode:</strong></td>
                        <td>{{ $product->barcode }}</td>
                    </tr>
                    <tr>
                        <td><strong>Category:</strong></td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Brand:</strong></td>
                        <td>{{ $product->brand->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Description:</strong></td>
                        <td>{{ $product->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Warranty:</strong></td>
                        <td>{{ $product->warranty_days > 0 ? $product->warranty_days . ' days' : 'No warranty' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            @if($product->status)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Pricing & Stock</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Cost Price:</strong></td>
                        <td>{{ number_format($product->cost_price, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Selling Price:</strong></td>
                        <td>{{ number_format($product->selling_price, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Stock Quantity:</strong></td>
                        <td>
                            <span class="badge bg-{{ $product->stock_quantity <= $product->reorder_level ? 'danger' : 'success' }}">
                                {{ $product->stock_quantity }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Reorder Level:</strong></td>
                        <td>{{ $product->reorder_level }}</td>
                    </tr>
                    <tr>
                        <td><strong>Profit Margin:</strong></td>
                        <td>{{ number_format(($product->selling_price - $product->cost_price) / $product->cost_price * 100, 2) }}%</td>
                    </tr>
                    <tr>
                        <td><strong>Stock Value:</strong></td>
                        <td>{{ number_format($product->cost_price * $product->stock_quantity, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
