<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Diyabet Takip') }} — Profesyonel Sağlık Platformu</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/health.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body class="landing-body">
<nav class="landing-nav">
    <div class="landing-brand">
        <span class="brand-mark">❤️</span>
        <strong>{{ __('Diyabet Takip') }}</strong>
    </div>
    <div class="landing-nav-links">
        <a href="#features">{{ __('Özellikler') }}</a>
        <a href="{{ route('login') }}" class="btn btn-sm">{{ __('Giriş Yap') }}</a>
    </div>
</nav>

<header class="landing-hero" style="background-image: linear-gradient(135deg, rgba(13,148,136,.92), rgba(15,23,42,.85)), url('{{ config('health_images.hero_landing') }}')">
    <div class="landing-hero-inner">
        <span class="landing-badge">Laravel 13 · PWA · API</span>
        <h1>{{ __('Diyabet yönetiminde yeni nesil takip') }}</h1>
        <p>{{ __('Kan şekeri, diyet, ilaç ve egzersiz verilerinizi tek panelde toplayın. Akıllı uyarılar, PDF rapor ve doktor paylaşımı ile yanınızda.') }}</p>
        <div class="landing-cta">
            <a href="{{ route('login') }}" class="btn btn-lg">{{ __('Hemen Başla') }}</a>
            <a href="{{ route('mobile') }}" class="btn btn-secondary btn-lg" target="_blank">{{ __('Mobil PWA') }}</a>
        </div>
        <p class="landing-demo">Demo: <code>demo@diyabet.app</code> / <code>password</code></p>
    </div>
</header>

<section id="features" class="landing-section">
    <h2>{{ __('Tüm ihtiyaçlarınız tek platformda') }}</h2>
    <div class="landing-features">
        <article class="landing-feature-card">
            <img src="{{ config('health_images.hero_dashboard') }}" alt="">
            <h3>{{ __('Akıllı Panel') }}</h3>
            <p>{{ __('7 günlük trend, hedef aralık yüzdesi ve tahmini HbA1c ile anlık görünürlük.') }}</p>
        </article>
        <article class="landing-feature-card">
            <img src="{{ config('health_images.empty_meals') }}" alt="">
            <h3>{{ __('Diyet & Yemek') }}</h3>
            <p>{{ __('Haftalık menü planı, yemek fotoğraf galerisi ve karbonhidrat takibi.') }}</p>
        </article>
        <article class="landing-feature-card">
            <img src="{{ config('health_images.exercise') }}" alt="">
            <h3>{{ __('Yaşam Tarzı') }}</h3>
            <p>{{ __('Su, egzersiz, uyku ve stres etiketleri ile bütünsel sağlık kaydı.') }}</p>
        </article>
        <article class="landing-feature-card">
            <img src="{{ config('health_images.hospital') }}" alt="">
            <h3>{{ __('Doktor & Rapor') }}</h3>
            <p>{{ __('PDF rapor, paylaşım linki ve randevu hatırlatmaları.') }}</p>
        </article>
    </div>
</section>

<section class="landing-section landing-stats">
    <div><strong>15+</strong><span>{{ __('Modül') }}</span></div>
    <div><strong>TR/EN</strong><span>{{ __('Çoklu dil') }}</span></div>
    <div><strong>API</strong><span>Sanctum REST</span></div>
    <div><strong>PWA</strong><span>{{ __('Mobil kurulum') }}</span></div>
</section>

<footer class="landing-footer">
    <p>© {{ date('Y') }} {{ __('Diyabet Takip') }} · <a href="https://github.com/sametozlu/diyabettakip" target="_blank">GitHub</a></p>
</footer>
</body>
</html>
