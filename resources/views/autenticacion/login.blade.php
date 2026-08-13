@extends('layouts.auth')
@section('content')
<div class="auth-card">
  <div class="auth-logo">🏪</div>
  <h1 class="auth-title">DigitalsPos</h1>
  <p class="auth-subtitle">Sistema de Punto de Venta</p>

  @if(session('error'))
    <div class="alert alert-danger" style="text-align:left;margin-bottom:16px;padding:10px 14px;background:#f8d7da;color:#721c24;border-radius:6px;border:1px solid #f5c6cb">
      ❌ {{ session('error') }}
    </div>
  @endif

  <form action="{{ route('login.post') }}" method="POST">
    @csrf
    <div class="form-group">
      <label>Correo electrónico</label>
      <input type="email" name="email" required autofocus
             class="form-control @error('email') is-invalid @enderror"
             value="{{ old('email') }}"
             placeholder="admin@digitalspos.com">
      @error('email')<span style="color:#e74a3b;font-size:.8rem;display:block;margin-top:4px">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
      <label>Contraseña</label>
      <input type="password" name="password" required
             class="form-control @error('password') is-invalid @enderror"
             placeholder="••••••••">
    </div>
    <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px">
      Ingresar →
    </button>
  </form>
  <p class="auth-hint">Demo: admin@digitalspos.com / admin123</p>
</div>
@endsection
