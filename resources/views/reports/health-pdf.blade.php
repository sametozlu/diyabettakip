<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>
body{font-family:DejaVu Sans,sans-serif;font-size:12px;color:#1e293b;margin:0;}
.pdf-header{background:#0d9488;color:#fff;padding:20px 24px;}
.pdf-header h1{margin:0;font-size:22px;}
.pdf-header p{margin:4px 0 0;opacity:.9;font-size:11px;}
.pdf-logo{display:inline-block;width:40px;height:40px;background:#fff;border-radius:10px;text-align:center;line-height:40px;font-size:20px;margin-right:12px;vertical-align:middle;}
.pdf-body{padding:24px;}
h2{color:#0d9488;font-size:14px;margin-top:20px;border-bottom:2px solid #e2e8f0;padding-bottom:4px;}
table{width:100%;border-collapse:collapse;margin-top:8px;}th,td{border:1px solid #e2e8f0;padding:6px;text-align:left;}th{background:#f8fafc;}
.footer{margin-top:24px;font-size:10px;color:#64748b;border-top:1px solid #e2e8f0;padding-top:12px;}
</style></head>
<body>
<div class="pdf-header">
    <span class="pdf-logo">❤</span>
    <span style="vertical-align:middle;">
        <h1>Diyabet Takip — Sağlık Raporu</h1>
        <p>{{ $user->name }} · {{ now()->format('d.m.Y H:i') }}</p>
    </span>
</div>
<div class="pdf-body">
@if($profile)<p><strong>Hedef glukoz:</strong> {{ $profile->target_min }}–{{ $profile->target_max }} mg/dL
@if($profile->weight) | <strong>Kilo:</strong> {{ $profile->weight }} kg@endif
@if($profile->height) | <strong>Boy:</strong> {{ $profile->height }} m@endif
@if($estimatedHbA1c) | <strong>Tahmini HbA1c:</strong> {{ $estimatedHbA1c }}%@endif</p>@endif
<h2>Son 30 Gün Glukoz Ölçümleri</h2>
<table><thead><tr><th>Tarih</th><th>mg/dL</th><th>Tip</th><th>Not</th></tr></thead><tbody>
@foreach($readings as $r)<tr><td>{{ $r->measured_at->format('d.m.Y H:i') }}</td><td><strong>{{ $r->value }}</strong></td><td>{{ $r->context }}</td><td>{{ $r->notes }}</td></tr>@endforeach
</tbody></table>
@if($hba1c->count())<h2>HbA1c Geçmişi</h2><table><thead><tr><th>Tarih</th><th>%</th></tr></thead><tbody>@foreach($hba1c as $h)<tr><td>{{ $h->tested_at->format('d.m.Y') }}</td><td>{{ $h->value }}</td></tr>@endforeach</tbody></table>@endif
@if($medications->count())<h2>Aktif İlaçlar</h2><ul>@foreach($medications as $m)<li>{{ $m->name }} — {{ $m->dosage }} ({{ $m->frequencyLabel() }})</li>@endforeach</ul>@endif
<div class="footer">Bu rapor bilgilendirme amaçlıdır. Tıbbi karar için doktorunuza danışın. — Diyabet Takip</div>
</div>
</body></html>
