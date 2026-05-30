@extends('layouts.main')

@section('title', 'Adjust Stock - SmartPOS')

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('stock.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Product: {{ $product->name }}</h5>
                <p>Current Stock: <strong>{{ $product->stock_quantity }}</strong></p>
                <p>Reorder Level: {{ $product->reorder_level }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('stock.adjust.store', $product->id) }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Adjustment Type *</label>
                        <select name="type" class="form-select" required>
                            <option value="adjustment_in">Stock In</option>
                            <option value="adjustment_out">Stock Out</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Quantity *</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Adjust Stock</button>
        </form>
    </div>
</div>
@endsection
