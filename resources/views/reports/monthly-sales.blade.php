@extends('layouts.main')

@section('title', 'Monthly Sales Report - SmartPOS')
@section('page-title', 'Monthly Sales Report')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="year" class="form-select">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <select name="month" class="form-select">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ Carbon\Carbon::createFromDate($year, $m)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
        <div>
            <a href="{{ route('exports.monthly-sales.excel', ['year' => $year, 'month' => $month]) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a href="{{ route('exports.monthly-sales.pdf', ['year' => $year, 'month' => $month]) }}" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> PDF
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
    </div>
</div>
@endsection
