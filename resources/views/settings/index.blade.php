@extends('layouts.main')

@section('title', 'Settings - SmartPOS')
@section('page-title', 'Settings')

@section('content')
<div class="card">
    <div class="card-header">Shop Settings</div>
    <div class="card-body">
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Shop Name *</label>
                        <input type="text" name="shop_name" class="form-control" value="{{ $settings['shop_name'] }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Currency *</label>
                        <select name="currency" class="form-select">
                            <option value="USD" {{ $settings['currency'] == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ $settings['currency'] == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ $settings['currency'] == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            <option value="LKR" {{ $settings['currency'] == 'LKR' ? 'selected' : '' }}>LKR - Sri Lankan Rupee</option>
                            <option value="INR" {{ $settings['currency'] == 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="shop_email" class="form-control" value="{{ $settings['shop_email'] }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="shop_phone" class="form-control" value="{{ $settings['shop_phone'] }}">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="shop_address" class="form-control" rows="2">{{ $settings['shop_address'] }}</textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Default Tax Rate (%)</label>
                        <input type="number" name="tax_percentage" class="form-control" value="{{ $settings['tax_percentage'] }}" min="0" max="100" step="0.01">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Receipt Footer Message</label>
                <textarea name="receipt_footer" class="form-control" rows="2">{{ $settings['receipt_footer'] }}</textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>
@endsection
