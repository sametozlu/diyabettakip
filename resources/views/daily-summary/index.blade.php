@extends('layouts.health')
@section('title', __('Günlük Özet'))
@section('hero_title', __('Günlük Özet'))
@section('hero_subtitle', now()->locale(app()->getLocale())->isoFormat('D MMMM YYYY, dddd'))
@section('hero_stats')
<div class="kpi-grid">
<div class="kpi-card"><div class="kpi-icon teal"><i data-lucide="activity"></i></div><div class="kpi-value">{{ $summary['readings_count'] }}</div><div class="kpi-label">{{ __('Bugün ölçüm') }}</div></div>
<div class="kpi-card"><div class="kpi-icon indigo"><i data-lucide="bar-chart-3"></i></div><div class="kpi-value">{{ $summary['readings_avg'] ?? '—' }}</div><div class="kpi-label">{{ __('Ortalama glukoz') }}</div></div>
<div class="kpi-card"><div class="kpi-icon sky"><i data-lucide="droplets"></i></div><div class="kpi-value">{{ $summary['water_percent'] }}%</div><div class="kpi-label">{{ $summary['water_ml'] }}/{{ $summary['water_goal'] }} ml</div><div class="range-bar"><div class="range-bar-fill" style="width:{{ min(100,$summary['water_percent']) }}%"></div></div></div>
<div class="kpi-card"><div class="kpi-icon emerald"><i data-lucide="footprints"></i></div><div class="kpi-value">{{ $summary['exercise_steps'] ?: 0 }}</div><div class="kpi-label">{{ __('Adım') }} / {{ $summary['steps_goal'] }}</div></div>
</div>
@endsection
@section('content')
@if($summary['alerts']->isNotEmpty())
@foreach($summary['alerts'] as $alert)
<div class="alert alert-{{ $alert['severity']==='danger'?'error':($alert['severity']==='warning'?'warning':'success') }}"><i data-lucide="bell"></i> {{ $alert['message'] }}</div>
@endforeach
@endif
<div class="grid-2">
<div class="card">
<div class="card-title"><i data-lucide="utensils"></i> {{ __('Diyet') }}</div>
@if($summary['meal'])<p>{{ Str::limit($summary['meal']->menu_items, 200) }}</p>@else<p class="text-muted">{{ __('Bugün plan yok') }}</p>@endif
</div>
<div class="card">
<div class="card-title"><i data-lucide="syringe"></i> {{ __('İnsülin / Karb') }}</div>
<p>{{ $summary['insulin_logs'] }} {{ __('kayıt') }} · {{ $summary['total_carbs'] }}g KH · {{ $summary['total_insulin'] }} ünite</p>
</div>
<div class="card">
<div class="card-title"><i data-lucide="pill"></i> {{ __('Bekleyen ilaç') }}</div>
@if($summary['pending_meds']->count())@foreach($summary['pending_meds'] as $m)<p><strong>{{ $m->name }}</strong> — {{ implode(', ', $m->times ?? []) }}</p>@endforeach
@else<p class="text-muted">{{ __('Bugünkü ilaçlar tamamlandı') }}</p>@endif
</div>
<div class="card">
<div class="card-title"><i data-lucide="flask-conical"></i> {{ __('Tahmini HbA1c') }}</div>
<p style="font-size:1.75rem;font-weight:800;">{{ $summary['estimated_hba1c'] ? $summary['estimated_hba1c'].'%' : '—' }}</p>
</div>
</div>
@endsection
