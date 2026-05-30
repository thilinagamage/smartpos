@extends('layouts.main')

@section('title', 'Forgot Password - SmartPOS')

@section('content')
<style>
    .login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    }
    .login-card {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="login-page">
    <div class="login-card">
        <h4 class="text-center mb-3">Forgot Password</h4>
        <p class="text-muted text-center mb-3">Enter your email to reset password</p>
        
        @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
        </form>
        
        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-muted">Back to Login</a>
        </div>
    </div>
</div>
@endsection
