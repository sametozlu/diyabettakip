<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Hoş Geldin') }} — {{ __('Diyabet Takip') }}</title>
    <link rel="stylesheet" href="{{ asset('css/health.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body class="onboarding-body">
<div class="onboarding-wrap">
    <div class="onboarding-slides" id="slides">
        <div class="onboarding-slide active" data-slide="0">
            <img src="{{ config('health_images.hero_dashboard') }}" alt="">
            <h2>{{ __('Sağlık paneliniz hazır') }}</h2>
            <p>{{ __('Kan şekeri trendleri, hedef aralık ve akıllı uyarılarla günlük takibinizi kolaylaştırın.') }}</p>
        </div>
        <div class="onboarding-slide" data-slide="1">
            <img src="{{ config('health_images.empty_meals') }}" alt="">
            <h2>{{ __('Beslenme & diyet planı') }}</h2>
            <p>{{ __('Haftalık menü önerileri, yemek fotoğrafları ve karbonhidrat kaydı ile öğünlerinizi yönetin.') }}</p>
        </div>
        <div class="onboarding-slide" data-slide="2">
            <img src="{{ config('health_images.exercise') }}" alt="">
            <h2>{{ __('Yaşam tarzı takibi') }}</h2>
            <p>{{ __('Su, egzersiz, ilaç hatırlatmaları ve PDF rapor — hepsi tek uygulamada.') }}</p>
        </div>
    </div>
    <div class="onboarding-dots" id="dots">
        <span class="active"></span><span></span><span></span>
    </div>
    <div class="onboarding-actions">
        <button type="button" class="btn btn-secondary" id="btn-prev" disabled>{{ __('Geri') }}</button>
        <button type="button" class="btn" id="btn-next">{{ __('İleri') }}</button>
        <form method="POST" action="{{ route('onboarding.complete') }}" id="form-done" class="hidden">
            @csrf
            <button type="submit" class="btn">{{ __('Başlayalım') }}</button>
        </form>
    </div>
</div>
<script>
let cur = 0;
const slides = document.querySelectorAll('.onboarding-slide');
const dots = document.querySelectorAll('.onboarding-dots span');
const prev = document.getElementById('btn-prev');
const next = document.getElementById('btn-next');
const form = document.getElementById('form-done');
function show(i) {
    slides.forEach((s, j) => s.classList.toggle('active', j === i));
    dots.forEach((d, j) => d.classList.toggle('active', j === i));
    prev.disabled = i === 0;
    if (i === slides.length - 1) { next.classList.add('hidden'); form.classList.remove('hidden'); }
    else { next.classList.remove('hidden'); form.classList.add('hidden'); }
    cur = i;
}
prev.onclick = () => show(cur - 1);
next.onclick = () => show(cur + 1);
</script>
</body>
</html>
