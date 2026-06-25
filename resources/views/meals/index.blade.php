@extends('layouts.health')

@section('title', 'Diyet Planı')
@section('hero_title', 'Beslenme Programı')
@section('hero_subtitle', 'Günlük menü önerileri ve porsiyon rehberi')

@section('content')
@forelse ($meals as $week => $days)
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title"><i data-lucide="calendar-range"></i> {{ $week ?: 'Genel Plan' }}</div>
            <div class="card-subtitle">{{ $days->count() }} günlük plan</div>
        </div>
    </div>

    <div class="grid-3">
        @foreach ($days as $meal)
        <a href="{{ route('meals.show', $meal) }}" style="text-decoration:none;color:inherit;">
            <div class="med-card meal-card-visual" style="height:100%;padding:0;overflow:hidden;{{ $meal->plan_date->isToday() ? 'border-color:var(--primary);box-shadow:0 0 0 2px var(--primary-soft);' : '' }}">
                <img src="{{ $meal->display_image }}" alt="" class="meal-card-img" loading="lazy">
                <div style="padding:0.85rem;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.5rem;">
                    <div>
                        <strong style="font-size:0.95rem;">{{ $meal->day_name }}</strong>
                        <div class="text-muted" style="font-size:0.78rem;margin-top:0.15rem;">{{ $meal->plan_date->format('d.m.Y') }}</div>
                    </div>
                    @if ($meal->plan_date->isToday())
                        <span class="badge badge-normal">BUGÜN</span>
                    @endif
                </div>
                <p class="text-muted" style="font-size:0.82rem;line-height:1.5;margin-bottom:0.75rem;">{{ Str::limit($meal->menu_items, 90) }}</p>
                <div class="tag-row" style="margin-top:0;">
                    <span class="tag tag-eat">{{ count($meal->eat_items) }} öneri</span>
                    @if(count($meal->skip_items))
                        <span class="tag tag-skip">{{ count($meal->skip_items) }} kaçın</span>
                    @endif
                </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@empty
<div class="card">
    <div class="empty-state">
        <i data-lucide="utensils-crossed"></i>
        <p>Henüz diyet planı tanımlanmamış.</p>
    </div>
</div>
@endforelse
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
