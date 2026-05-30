@extends('layouts.main')

@section('title', 'Warranty Report - SmartPOS')
@section('page-title', 'Warranty Report')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="expiry_date" class="form-control" value="{{ $expiryDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Warranty Items Expiring Before {{ $expiryDate->format('Y-m-d') }}</div>
    <div class="card-body">
        @if($report['count'] > 0)
        <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Sold Date</th>
                    <th>Warranty Expiry</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['items'] as $item)
                <tr>
                    <td><a href="{{ route('sales.show', $item->sale_id) }}">{{ $item->sale->invoice_no }}</a></td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->sale->customer->name ?? 'Walk-in' }}</td>
                    <td>{{ $item->sale->sale_date->format('Y-m-d') }}</td>
                    <td>{{ $item->warranty_expiry->format('Y-m-d') }}</td>
                    <td>
                        @if($item->warranty_expiry < now())
                        <span class="badge bg-danger">Expired</span>
                        @else
                        <span class="badge bg-warning">Active</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
        <div class="text-center text-muted">
            <i class="fas fa-check-circle fa-3x mb-3"></i>
            <p>No warranty items expiring in this period!</p>
        </div>
        @endif
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">Warranty Lookup</div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.warranty-lookup') }}" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="invoice_no" class="form-control" placeholder="Enter Invoice Number" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
    </div>
</div>
@endsection
