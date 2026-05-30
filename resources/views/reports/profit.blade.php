@extends('layouts.main')

@section('title', 'Profit Report - SmartPOS')
@section('page-title', 'Profit Report')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="date" name="date_from" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_to" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
        <div>
            <a href="{{ route('exports.profit.excel', ['date' => $startDate->format('Y-m-d')]) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a href="{{ route('exports.profit.pdf', ['date' => $startDate->format('Y-m-d')]) }}" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card primary">
                    <h6>Total Revenue</h6>
                    <h4>{{ number_format($report['total_revenue'], 2) }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card warning">
                    <h6>Total Cost</h6>
                    <h4>{{ number_format($report['total_cost'], 2) }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card success">
                    <h6>Total Profit</h6>
                    <h4>{{ number_format($report['total_profit'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="alert alert-info">
            Profit Margin: {{ number_format($report['profit_margin'], 2) }}%
        </div>
    </div>
</div>
@endsection
