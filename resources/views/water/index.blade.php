@extends('layouts.health')
@section('title', __('Su Takibi'))
@section('hero_title', __('Su Takibi'))
@section('hero_subtitle', __('Bugün').': '.$todayTotal.' / '.$goal.' ml')
@section('hero_stats')
<div class="kpi-grid" style="grid-template-columns:1fr;"><div class="kpi-card"><div class="kpi-value">{{ round($todayTotal/max(1,$goal)*100) }}%</div><div class="kpi-label">{{ __('Günlük hedef') }}</div><div class="range-bar"><div class="range-bar-fill" style="width:{{ min(100,round($todayTotal/max(1,$goal)*100)) }}%"></div></div></div></div>
@endsection
@section('content')
<div class="card"><form method="POST" action="{{ route('water.store') }}">@csrf
<p class="text-muted" style="margin-bottom:1rem;">{{ __('Hızlı ekle') }}</p>
<div class="quick-add-grid">
@foreach([200,250,330,500] as $ml)<button type="submit" name="amount_ml" value="{{ $ml }}" class="quick-add-btn">{{ $ml }} ml</button>@endforeach
</div>
<div class="form-group"><label>{{ __('Özel miktar') }} (ml)</label><input type="number" name="amount_ml" min="50" max="2000" placeholder="250"></div>
<button class="btn"><i data-lucide="plus"></i> {{ __('Ekle') }}</button>
</form></div>
<div class="card" style="padding:0;overflow:hidden;margin-top:1rem;"><table class="data-table"><thead><tr><th>{{ __('Tarih') }}</th><th>{{ __('Miktar') }}</th><th></th></tr></thead>
<tbody>@forelse($logs as $log)<tr><td>{{ $log->logged_at->format('d.m.Y H:i') }}</td><td><strong>{{ $log->amount_ml }} ml</strong></td>
<td><form method="POST" action="{{ route('water.destroy', $log) }}" onsubmit="return confirm('?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i data-lucide="trash-2"></i></button></form></td></tr>
@empty<tr><td colspan="3"><p class="text-muted">{{ __('Kayıt yok') }}</p></td></tr>@endforelse</tbody></table></div>{{ $logs->links() }}
@endsection
