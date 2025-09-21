@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height:60vh;">
    <div class="card shadow-sm" style="width:100%; max-width:420px;">
        <div class="card-body p-4">
            <h4 class="mb-3 text-center">Sign in</h4>
            <form method="POST" action="{{ route('login.store') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror" required autofocus>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label d-flex justify-content-between">
                        <span>Password</span>
                    </label>
                    <input id="password" type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" id="remember" value="1" class="form-check-input" {{
                        old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="form-check-label">Remember me</label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Sign in</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection