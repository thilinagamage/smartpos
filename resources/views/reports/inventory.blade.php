@extends('layouts.main')

@section('title', 'Inventory Report - SmartPOS')
@section('page-title', 'Inventory Report')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Inventory Summary</span>
        <div>
            <a href="{{ route('exports.inventory.excel') }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a href="{{ route('exports.inventory.pdf') }}" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <h6>Total Products</h6>
                    <h4>{{ $report['total_products'] }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <h6>Stock Value (Cost)</h6>
                    <h4>{{ number_format($report['total_stock_value'], 2) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <h6>Retail Value</h6>
                    <h4>{{ number_format($report['total_retail_value'], 2) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card danger">
                    <h6>Low Stock Items</h6>
                    <h4>{{ $report['low_stock_count'] }}</h4>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Cost</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['products'] as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>{{ $product->brand->name ?? '-' }}</td>
                    <td>{{ number_format($product->cost_price, 2) }}</td>
                    <td>{{ number_format($product->selling_price, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $product->stock_quantity <= $product->reorder_level ? 'danger' : 'success' }}">
                            {{ $product->stock_quantity }}
                        </span>
                    </td>
                    <td>{{ number_format($product->cost_price * $product->stock_quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
