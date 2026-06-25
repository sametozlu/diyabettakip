<!DOCTYPE html>
<html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ __('Sağlık Raporu') }} — {{ $user->name }}</title>
<link rel="stylesheet" href="{{ asset('css/health.css') }}">
</head><body style="padding:2rem;max-width:900px;margin:0 auto;">
<div class="card"><h1 style="margin-bottom:0.5rem;">{{ __('Diyabet Takip') }} — {{ __('Paylaşılan Rapor') }}</h1>
<p class="text-muted">{{ $user->name }} · {{ now()->format('d.m.Y H:i') }} · {{ $link->views }} {{ __('görüntülenme') }}</p></div>
@if($user->healthProfile)<div class="card"><strong>{{ __('Hedef') }}:</strong> {{ $user->healthProfile->target_min }}–{{ $user->healthProfile->target_max }} mg/dL
@if($estimatedHbA1c) · <strong>{{ __('Tahmini HbA1c') }}:</strong> {{ $estimatedHbA1c }}%@endif</div>@endif
<div class="card" style="padding:0;overflow:hidden;"><table class="data-table"><thead><tr><th>{{ __('Tarih') }}</th><th>mg/dL</th><th>{{ __('Tip') }}</th></tr></thead>
<tbody>@foreach($readings as $r)<tr><td>{{ $r->measured_at->format('d.m.Y H:i') }}</td><td><strong>{{ $r->value }}</strong></td><td>{{ $r->context }}</td></tr>@endforeach</tbody></table></div>
@if($hba1c->count())<div class="card"><strong>HbA1c:</strong> @foreach($hba1c as $h){{ $h->value }}% ({{ $h->tested_at->format('d.m.Y') }}) @endforeach</div>@endif
@if($user->medications->count())<div class="card"><strong>{{ __('İlaçlar') }}:</strong><ul>@foreach($user->medications as $m)<li>{{ $m->name }} {{ $m->dosage }}</li>@endforeach</ul></div>@endif
<p class="text-muted" style="text-align:center;margin-top:2rem;font-size:0.8rem;">{{ __('Bu rapor bilgilendirme amaçlıdır. Tıbbi karar için doktorunuza başvurun.') }}</p>
</body></html>
