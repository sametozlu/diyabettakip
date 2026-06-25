@extends('layouts.health')

@section('title', $meal->day_name)
@section('hero_title', $meal->day_name . ' Menüsü')
@section('hero_subtitle', $meal->plan_date->format('d F Y') . ($meal->week_label ? ' · ' . $meal->week_label : ''))

@section('content')
<a href="{{ route('meals.index') }}" class="btn btn-secondary btn-sm" style="margin-bottom:1.25rem;">
    <i data-lucide="arrow-left"></i> Planlara dön
</a>

<div class="meal-hero-banner">
    <img src="{{ $meal->display_image }}" alt="">
    <div class="meal-hero-overlay">
        <h3>{{ $meal->day_name }}</h3>
        <p>{{ $meal->plan_date->format('d F Y') }}</p>
    </div>
</div>

<div class="grid-2" style="margin-top:1.25rem;">
    <div class="card">
        <div class="card-title" style="margin-bottom:1rem;"><i data-lucide="book-open"></i> Günün Menüsü</div>
        <p style="font-size:0.95rem;line-height:1.75;color:var(--text-secondary);">{{ $meal->menu_items }}</p>
    </div>

    <div class="card">
        <div class="card-title" style="margin-bottom:1rem;color:var(--success);">
            <i data-lucide="check-circle"></i> Öncelikli Tüket
        </div>
        @foreach ($meal->eat_items as $item)
            <div class="reco eat"><i data-lucide="check" style="width:14px;height:14px;flex-shrink:0;margin-top:2px;"></i> {{ $item }}</div>
        @endforeach
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-title" style="margin-bottom:1rem;color:var(--warning);">
            <i data-lucide="alert-triangle"></i> Azalt
        </div>
        @forelse ($meal->reduce_items as $item)
            <div class="reco reduce"><i data-lucide="minus-circle" style="width:14px;height:14px;flex-shrink:0;margin-top:2px;"></i> {{ $item }}</div>
        @empty
            <p class="text-muted">Özel kısıtlama yok.</p>
        @endforelse
    </div>

    <div class="card">
        <div class="card-title" style="margin-bottom:1rem;color:var(--danger);">
            <i data-lucide="x-circle"></i> Kaçın
        </div>
        @forelse ($meal->skip_items as $item)
            <div class="reco skip"><i data-lucide="x" style="width:14px;height:14px;flex-shrink:0;margin-top:2px;"></i> {{ $item }}</div>
        @empty
            <p class="text-muted">Kaçınılacak öğe yok.</p>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
