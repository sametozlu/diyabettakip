@extends('layouts.health')
@section('title', __('Yemek Kaydı'))
@section('hero_title', __('Yemek Galerisi'))
@section('hero_subtitle', 'Fotoğraf + karbonhidrat tahmini · Instagram tarzı görünüm')
@section('content')
<div class="page-toolbar">
    <span class="text-muted">{{ $logs->total() }} {{ __('kayıt') }}</span>
    <a href="{{ route('food.create') }}" class="btn"><i data-lucide="plus"></i> {{ __('Yemek Ekle') }}</a>
</div>
@if($logs->isEmpty())
<div class="empty-state-card">
    <img src="{{ config('health_images.empty_meals') }}" alt="" class="empty-state-img">
    <p>{{ __('Henüz yemek kaydı yok. İlk fotoğrafınızı ekleyin!') }}</p>
    <a href="{{ route('food.create') }}" class="btn btn-sm">{{ __('Yemek Ekle') }}</a>
</div>
@else
<div class="photo-gallery photo-gallery--masonry">
@foreach($logs as $log)
<article class="gallery-item gallery-item--food">
    @if($log->photo_path)
        <img src="{{ asset('storage/'.$log->photo_path) }}" alt="{{ $log->name }}" loading="lazy">
    @else
        <div class="gallery-placeholder"><i data-lucide="utensils"></i></div>
    @endif
    <div class="gallery-overlay">
        <strong>{{ $log->name }}</strong>
        <span>{{ $mealTypes[$log->meal_type] ?? $log->meal_type }} · {{ $log->logged_at->format('d.m H:i') }}</span>
        <span class="gallery-carbs">{{ $log->carbs_grams }}g KH @if($log->estimation_source==='estimated')<em>tahmini</em>@endif</span>
        <form method="POST" action="{{ route('food.destroy', $log) }}" onsubmit="return confirm('{{ __('Silinsin mi?') }}')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm"><i data-lucide="trash-2"></i></button>
        </form>
    </div>
</article>
@endforeach
</div>
{{ $logs->links() }}
@endif
@endsection
