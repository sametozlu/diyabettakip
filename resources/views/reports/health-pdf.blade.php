<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;font-size:12px;color:#1e293b;}h1{color:#0d9488;font-size:20px;}table{width:100%;border-collapse:collapse;margin-top:12px;}th,td{border:1px solid #e2e8f0;padding:6px;text-align:left;}th{background:#f8fafc;}</style></head>
<body>
<h1>Diyabet Takip — Sağlık Raporu</h1>
<p><strong>{{ $user->name }}</strong> · {{ now()->format('d.m.Y') }}</p>
@if($profile)<p>Hedef: {{ $profile->target_min }}–{{ $profile->target_max }} mg/dL | Kilo: {{ $profile->weight }} kg | Boy: {{ $profile->height }} m@if($estimatedHbA1c) | Tahmini HbA1c: {{ $estimatedHbA1c }}%@endif</p>@endif
<h2>Son 30 Gün Glukoz</h2>
<table><thead><tr><th>Tarih</th><th>mg/dL</th><th>Tip</th><th>Not</th></tr></thead><tbody>
@foreach($readings as $r)<tr><td>{{ $r->measured_at->format('d.m.Y H:i') }}</td><td>{{ $r->value }}</td><td>{{ $r->context }}</td><td>{{ $r->notes }}</td></tr>@endforeach
</tbody></table>
@if($hba1c->count())<h2>HbA1c</h2><table><thead><tr><th>Tarih</th><th>%</th></tr></thead><tbody>@foreach($hba1c as $h)<tr><td>{{ $h->tested_at->format('d.m.Y') }}</td><td>{{ $h->value }}</td></tr>@endforeach</tbody></table>@endif
@if($medications->count())<h2>İlaçlar</h2><ul>@foreach($medications as $m)<li>{{ $m->name }} — {{ $m->dosage }} ({{ $m->frequency }})</li>@endforeach</ul>@endif
<p style="margin-top:24px;font-size:10px;color:#64748b;">Bu rapor bilgilendirme amaçlıdır. Tıbbi karar için doktorunuza danışın.</p>
</body></html>
