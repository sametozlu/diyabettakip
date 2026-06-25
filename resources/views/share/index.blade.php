@extends('layouts.health')
@section('title', __('Doktor Paylaşımı'))
@section('hero_title', __('Doktor Paylaşımı'))
@section('hero_subtitle', __('Salt okunur link ile verilerinizi paylaşın'))
@section('content')
<div class="card"><form method="POST" action="{{ route('share.store') }}">@csrf
<div class="form-group"><label>{{ __('Etiket') }}</label><input type="text" name="label" placeholder="{{ __('Doktor paylaşımı') }}"></div>
<div class="form-group"><label>{{ __('Geçerlilik') }} (gün)</label><input type="number" name="expires_days" value="30" min="1" max="90"></div>
<button class="btn"><i data-lucide="link"></i> {{ __('Link Oluştur') }}</button>
</form></div>
<div class="card" style="padding:0;overflow:hidden;">
<table class="data-table"><thead><tr><th>{{ __('Link') }}</th><th>{{ __('Görüntülenme') }}</th><th>{{ __('Bitiş') }}</th><th></th></tr></thead>
<tbody>@forelse($links as $link)<tr>
<td><a href="{{ route('share.public', $link->token) }}" target="_blank">{{ Str::limit(route('share.public', $link->token), 50) }}</a></td>
<td>{{ $link->views }}</td><td>{{ $link->expires_at?->format('d.m.Y') ?? '—' }}</td>
<td><form method="POST" action="{{ route('share.destroy', $link) }}" onsubmit="return confirm('?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i data-lucide="trash-2"></i></button></form></td>
</tr>@empty<tr><td colspan="4"><p class="text-muted">{{ __('Link yok') }}</p></td></tr>@endforelse</tbody></table></div>
@endsection
