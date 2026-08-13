<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>DigitalsPos — Acceso</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body">
@if(session('error'))
  <div style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;background:#f8d7da;color:#721c24;padding:12px 24px;border-radius:8px;border:1px solid #f5c6cb;font-family:Nunito,sans-serif;font-weight:600">
    ❌ {{ session('error') }}
  </div>
@endif
@yield('content')
</body>
</html>
