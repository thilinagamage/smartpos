@extends('layouts.main')

@section('title', 'Edit Brand - SmartPOS')

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('brands.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('brands.update', $brand->id) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Brand Name *</label>
                <input type="text" name="name" class="form-control" value="{{ $brand->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="1" {{ $brand->status ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$brand->status ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Brand</button>
        </form>
    </div>
</div>
@endsection
