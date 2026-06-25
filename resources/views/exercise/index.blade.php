@extends('layouts.health')
@section('title', __('Egzersiz'))
@section('hero_title', __('Egzersiz & Adım'))
@section('hero_subtitle', __('Bugün').': '.$todaySteps.' / '.$stepsGoal.' '.__('adım'))
@section('hero_stats')
<div class="kpi-grid" style="grid-template-columns:1fr 1fr;"><div class="kpi-card"><div class="kpi-value">{{ $todaySteps }}</div><div class="kpi-label">{{ __('Bugünkü adım') }}</div><div class="range-bar"><div class="range-bar-fill" style="width:{{ min(100, round($todaySteps/max(1,$stepsGoal)*100)) }}%"></div></div></div></div>
@endsection
@section('content')
<div class="page-toolbar"><span></span><a href="{{ route('exercise.create') }}" class="btn"><i data-lucide="plus"></i> {{ __('Egzersiz Ekle') }}</a></div>
<div class="card" style="padding:0;overflow:hidden;"><table class="data-table"><thead><tr><th>{{ __('Tarih') }}</th><th>{{ __('Tür') }}</th><th>{{ __('Süre') }}</th><th>{{ __('Adım') }}</th><th></th></tr></thead>
<tbody>@forelse($logs as $log)<tr><td>{{ $log->logged_at->format('d.m.Y H:i') }}</td><td>{{ $types[$log->type] ?? $log->type }}</td><td>{{ $log->duration_minutes }} dk</td><td>{{ $log->steps ?? '—' }}</td>
<td><form method="POST" action="{{ route('exercise.destroy', $log) }}" onsubmit="return confirm('?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i data-lucide="trash-2"></i></button></form></td></tr>
@empty<tr><td colspan="5"><p class="text-muted">{{ __('Kayıt yok') }}</p></td></tr>@endforelse</tbody></table></div>{{ $logs->links() }}
@endsection
