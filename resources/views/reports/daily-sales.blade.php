@extends('layouts.main')

@section('title', 'Daily Sales Report - SmartPOS')
@section('page-title', 'Daily Sales Report')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
        <div>
            <a href="{{ route('exports.daily-sales.excel', ['date' => $date->format('Y-m-d')]) }}" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <a href="{{ route('exports.daily-sales.pdf', ['date' => $date->format('Y-m-d')]) }}" class="btn btn-sm btn-danger">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <h6>Total Sales</h6>
                    <h4>{{ $report['total_sales'] }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <h6>Total Revenue</h6>
                    <h4>{{ number_format($report['total_revenue'], 2) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <h6>Total Tax</h6>
                    <h4>{{ number_format($report['total_tax'], 2) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card danger">
                    <h6>Total Profit</h6>
                    <h4>{{ number_format($report['total_profit'], 2) }}</h4>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report['sales'] as $sale)
                <tr>
                    <td><a href="{{ route('sales.show', $sale->id) }}">{{ $sale->invoice_no }}</a></td>
                    <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                    <td>{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
                    <td>{{ number_format($sale->total_amount, 2) }}</td>
                    <td>{{ ucfirst($sale->payment_method) }}</td>
                    <td>
                        @if($sale->status == 'completed')
                        <span class="badge bg-success">Completed</span>
                        @else
                        <span class="badge bg-danger">Refunded</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No sales found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
