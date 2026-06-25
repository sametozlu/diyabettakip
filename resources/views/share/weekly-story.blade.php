<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Haftalık Özet') }} — {{ $user->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/health.css') }}">
    <style>
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #0f172a; padding: 1rem; font-family: 'Plus Jakarta Sans', sans-serif; }
        .story-card {
            width: min(400px, 100%); aspect-ratio: 9/16; border-radius: 24px; overflow: hidden;
            background: linear-gradient(160deg, #0d9488, #134e4a), url('{{ config('health_images.hero_mobile') }}') center/cover;
            color: #fff; padding: 2rem 1.5rem; display: flex; flex-direction: column; justify-content: space-between;
            box-shadow: 0 25px 50px rgba(0,0,0,.4);
        }
        .story-card h1 { font-size: 1.5rem; margin: 0; }
        .story-stat { font-size: 2.5rem; font-weight: 800; line-height: 1; }
        .story-label { opacity: 0.85; font-size: 0.85rem; margin-top: 0.25rem; }
        .story-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin: 1.5rem 0; }
        .story-box { background: rgba(255,255,255,.12); border-radius: 12px; padding: 1rem; }
        .story-footer { font-size: 0.75rem; opacity: 0.8; text-align: center; }
    </style>
</head>
<body>
<div class="story-card">
    <div>
        <p style="opacity:.8;font-size:0.8rem;margin:0;">{{ __('Haftalık Sağlık Özeti') }}</p>
        <h1>{{ $user->name }}</h1>
        <p style="opacity:.9;font-size:0.85rem;">{{ now()->subWeek()->format('d.m') }} – {{ now()->format('d.m.Y') }}</p>
    </div>
    <div>
        <div class="story-stat">{{ $report['avg_glucose'] ?? '—' }}</div>
        <div class="story-label">{{ __('Ortalama glukoz (mg/dL)') }}</div>
        <div class="story-grid">
            <div class="story-box">
                <div class="story-stat" style="font-size:1.75rem;">{{ $report['readings_count'] ?? 0 }}</div>
                <div class="story-label">{{ __('Ölçüm') }}</div>
            </div>
            <div class="story-box">
                <div class="story-stat" style="font-size:1.75rem;">{{ $report['in_range_percent'] ?? 0 }}%</div>
                <div class="story-label">{{ __('Hedefte') }}</div>
            </div>
            <div class="story-box">
                <div class="story-stat" style="font-size:1.75rem;">{{ $report['water_days'] ?? 0 }}</div>
                <div class="story-label">{{ __('Su hedefi gün') }}</div>
            </div>
            <div class="story-box">
                <div class="story-stat" style="font-size:1.75rem;">{{ $report['exercise_minutes'] ?? 0 }}</div>
                <div class="story-label">{{ __('Egzersiz dk') }}</div>
            </div>
        </div>
    </div>
    <div class="story-footer">{{ __('Diyabet Takip') }} · {{ __('Sağlıklı haftalar!') }}</div>
</div>
</body>
</html>
