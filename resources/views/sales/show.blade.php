@extends('layouts.main')

@section('title', 'Sale Details - SmartPOS')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Invoice: {{ $sale->invoice_no }}</span>
        <div>
            <a href="{{ route('sales.print', $sale->id) }}" class="btn btn-sm btn-primary" target="_blank">
                <i class="fas fa-print"></i> Print
            </a>
            <a href="{{ route('sales.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Sale Information</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Invoice #:</strong></td>
                        <td>{{ $sale->invoice_no }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td>{{ $sale->sale_date->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Customer:</strong></td>
                        <td>{{ $sale->customer->name ?? 'Walk-in Customer' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Cashier:</strong></td>
                        <td>{{ $sale->user->name }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Payment Information</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Payment Method:</strong></td>
                        <td>{{ ucfirst($sale->payment_method) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Payment Status:</strong></td>
                        <td>
                            <span class="badge bg-{{ $sale->payment_status == 'paid' ? 'success' : ($sale->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                {{ ucfirst($sale->payment_status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            @if($sale->status == 'completed')
                            <span class="badge bg-success">Completed</span>
                            @elseif($sale->status == 'refunded')
                            <span class="badge bg-danger">Refunded</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <h5>Sale Items</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Discount</th>
                    <th>Tax</th>
                    <th>Total</th>
                    @if($sale->items->first()?->warranty_expiry)
                    <th>Warranty</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->unit_discount, 2) }}</td>
                    <td>{{ number_format($item->tax_amount, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                    @if($item->warranty_expiry)
                    <td>{{ $item->warranty_expiry->format('Y-m-d') }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">Subtotal</th>
                    <th>{{ number_format($sale->subtotal, 2) }}</th>
                </tr>
                @if($sale->item_discount > 0)
                <tr>
                    <th colspan="5" class="text-end">Item Discount</th>
                    <th>{{ number_format($sale->item_discount, 2) }}</th>
                </tr>
                @endif
                @if($sale->order_discount > 0)
                <tr>
                    <th colspan="5" class="text-end">Order Discount</th>
                    <th>{{ number_format($sale->order_discount, 2) }}</th>
                </tr>
                @endif
                @if($sale->tax_amount > 0)
                <tr>
                    <th colspan="5" class="text-end">Tax</th>
                    <th>{{ number_format($sale->tax_amount, 2) }}</th>
                </tr>
                @endif
                <tr>
                    <th colspan="5" class="text-end">Total</th>
                    <th>{{ number_format($sale->total_amount, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="5" class="text-end">Paid</th>
                    <th>{{ number_format($sale->paid_amount, 2) }}</th>
                </tr>
                @if($sale->due_amount > 0)
                <tr>
                    <th colspan="5" class="text-end">Due</th>
                    <th>{{ number_format($sale->due_amount, 2) }}</th>
                </tr>
                @endif
            </tfoot>
        </table>
        
        @if($sale->status == 'completed')
        <div class="mt-3">
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#refundModal">
                <i class="fas fa-undo"></i> Refund
            </button>
        </div>
        
        <div class="modal fade" id="refundModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('sales.refund', $sale->id) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Refund Sale</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Refund Amount</label>
                                <input type="number" name="refund_amount" class="form-control" value="{{ $sale->total_amount }}" max="{{ $sale->total_amount }}" min="0.01" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Reason</label>
                                <textarea name="refund_reason" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Process Refund</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
