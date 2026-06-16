@extends('layouts.main')

@section('title', 'Low Stock Report - SmartPOS')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Low Stock Products ({{ $report['count'] }})</span>
        <div>
            <a href="{{ route('exports.low-stock.excel') }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a href="{{ route('exports.low-stock.pdf') }}" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($report['count'] > 0)
        <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Stock</th>
                    <th>Reorder Level</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['products'] as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>{{ $product->brand->name ?? '-' }}</td>
                    <td><span class="badge bg-danger">{{ $product->stock_quantity }}</span></td>
                    <td>{{ $product->reorder_level }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
        <div class="text-center text-muted">
            <i class="fas fa-check-circle fa-3x mb-3"></i>
            <p>No low stock products!</p>
        </div>
        @endif
    </div>
</div>
@endsection
