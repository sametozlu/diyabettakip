@extends('layouts.health')
@section('title', __('Yemek Kaydı'))
@section('hero_title', __('Yemek Kaydı'))
@section('hero_subtitle', 'Fotoğraf + karbonhidrat tahmini')
@section('content')
<div class="page-toolbar"><span></span><a href="{{ route('food.create') }}" class="btn"><i data-lucide="plus"></i> Yemek Ekle</a></div>
<div class="grid-2">
@forelse($logs as $log)
<div class="med-card">
@if($log->photo_path)<img src="{{ asset('storage/'.$log->photo_path) }}" alt="" style="width:100%;height:140px;object-fit:cover;border-radius:10px;margin-bottom:0.75rem;">@endif
<strong>{{ $log->name }}</strong>
<p class="text-muted">{{ $mealTypes[$log->meal_type] ?? $log->meal_type }} · {{ $log->logged_at->format('d.m.Y H:i') }}</p>
<p><strong>{{ $log->carbs_grams }}g</strong> KH @if($log->estimation_source==='estimated')<span class="badge badge-normal">tahmini</span>@endif</p>
<form method="POST" action="{{ route('food.destroy', $log) }}" onsubmit="return confirm('?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i data-lucide="trash-2"></i></button></form>
</div>
@empty<p class="text-muted">Henüz yemek kaydı yok.</p>@endforelse
</div>
{{ $logs->links() }}
@endsection
