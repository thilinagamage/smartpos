@extends('layouts.main')

@section('title', 'Edit Customer - SmartPOS')

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('customers.update', $customer->id) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Name *</label>
                <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $customer->email }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Phone *</label>
                <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ $customer->address }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Credit Limit</label>
                <input type="number" name="credit_limit" class="form-control" value="{{ $customer->credit_limit }}" step="0.01" min="0">
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="1" {{ $customer->status ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$customer->status ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Customer</button>
        </form>
    </div>
</div>
@endsection
