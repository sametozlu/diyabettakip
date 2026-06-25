<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Giriş') }} — {{ __('Diyabet Takip') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/health.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body class="login-split-body">
<div class="login-split">
    <div class="login-split-visual" style="background-image: linear-gradient(160deg, rgba(13,148,136,.88), rgba(15,23,42,.75)), url('{{ config('health_images.hero_login') }}')">
        <div class="login-split-copy">
            <h1>{{ __('Diyabet Takip') }}</h1>
            <p>{{ __('Sağlık verilerinizi güvenle yönetin. Kan şekeri, diyet ve tedavi tek yerde.') }}</p>
            <a href="{{ route('landing') }}" class="login-back">← {{ __('Ana sayfa') }}</a>
        </div>
    </div>
    <div class="login-split-form">
        <div class="form-card" style="max-width:400px;width:100%;">
            <h2 style="margin:0 0 0.25rem;font-size:1.5rem;">{{ __('Hoş geldiniz') }}</h2>
            <p class="text-muted" style="margin:0 0 1.5rem;font-size:0.9rem;">{{ __('Hesabınıza giriş yapın') }}</p>

            @if (session('status'))
                <div class="alert alert-success" style="margin-bottom:1rem;"><span>{{ session('status') }}</span></div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">{{ __('E-posta') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'demo@diyabet.app') }}" required autofocus>
                    @error('email')<span class="text-danger" style="font-size:0.8rem;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="password">{{ __('Şifre') }}</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                    @error('password')<span class="text-danger" style="font-size:0.8rem;">{{ $message }}</span>@enderror
                </div>
                <label style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.25rem;font-size:0.88rem;cursor:pointer;">
                    <input type="checkbox" name="remember" style="width:auto;"> {{ __('Beni hatırla') }}
                </label>
                <button type="submit" class="btn btn-block">{{ __('Giriş Yap') }}</button>
                @if (Route::has('password.request'))
                    <p style="text-align:center;margin-top:1rem;font-size:0.85rem;">
                        <a href="{{ route('password.request') }}" style="color:var(--primary);">{{ __('Şifremi unuttum') }}</a>
                    </p>
                @endif
            </form>
        </div>
    </div>
</div>
</body>
</html>
