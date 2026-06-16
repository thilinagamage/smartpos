@extends('layouts.main')

@section('title', 'Login - SmartPOS')

@section('content')
<style>
    .login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 1rem;
    }
    .login-card {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .login-logo {
        text-align: center;
        margin-bottom: 2rem;
    }
    .login-logo i {
        font-size: 3rem;
        color: #4f46e5;
    }
    @media (max-width: 575.98px) {
        .login-card {
            padding: 1.5rem;
        }
        .login-logo i {
            font-size: 2.5rem;
        }
    }
</style>

<div class="login-page">
    <div class="login-card">
        <div class="login-logo">
            <i class="fas fa-cash-register"></i>
            <h3 class="mt-3">SmartPOS</h3>
            <p class="text-muted">Point of Sale System</p>
        </div>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        
        <div class="text-center mt-3">
            <a href="{{ route('password.request') }}" class="text-muted">Forgot Password?</a>
        </div>
    </div>
</div>
@endsection
