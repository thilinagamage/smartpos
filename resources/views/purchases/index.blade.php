@extends('layouts.main')

@section('title', 'Purchases - SmartPOS')
@section('page-title', 'Purchases')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Purchases</span>
        <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> New Purchase
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->invoice_no }}</td>
                    <td>{{ $purchase->supplier->name }}</td>
                    <td>{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                    <td>{{ number_format($purchase->total_amount, 2) }}</td>
                    <td>{{ number_format($purchase->paid_amount, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $purchase->payment_status == 'paid' ? 'success' : ($purchase->payment_status == 'partial' ? 'warning' : 'danger') }}">
                            {{ ucfirst($purchase->payment_status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No purchases found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        {{ $purchases->links() }}
    </div>
</div>
@endsection
