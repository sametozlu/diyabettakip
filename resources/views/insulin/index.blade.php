@extends('layouts.health')
@section('title', __('İnsülin / Karb'))
@section('hero_title', __('İnsülin & Karbonhidrat'))
@section('hero_subtitle', __('Öğün bazlı insülin ve karb kayıtları'))
@section('content')
<div class="page-toolbar"><span></span><a href="{{ route('insulin.create') }}" class="btn"><i data-lucide="plus"></i> {{ __('Yeni Kayıt') }}</a></div>
<div class="card" style="padding:0;overflow:hidden;">
<table class="data-table"><thead><tr><th>{{ __('Tarih') }}</th><th>{{ __('Öğün') }}</th><th>KH (g)</th><th>{{ __('İnsülin') }}</th><th></th></tr></thead>
<tbody>@forelse($logs as $log)<tr>
<td>{{ $log->logged_at->format('d.m.Y H:i') }}</td><td>{{ $mealTypes[$log->meal_type] ?? $log->meal_type }}</td>
<td>{{ $log->carbs_grams ?? '—' }}</td><td>{{ $log->insulin_units ?? '—' }} ünite</td>
<td><form method="POST" action="{{ route('insulin.destroy', $log) }}" onsubmit="return confirm('?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i data-lucide="trash-2"></i></button></form></td>
</tr>@empty<tr><td colspan="5" class="empty-state"><p>{{ __('Kayıt yok') }}</p></td></tr>@endforelse</tbody></table></div>
{{ $logs->links() }}
@endsection
