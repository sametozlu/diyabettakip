<!DOCTYPE html>
<html lang="tr"><body style="font-family:sans-serif;padding:24px;">
<h2 style="color:#0d9488;">{{ __('Haftalık Sağlık Raporu') }}</h2>
<p>Merhaba {{ $user->name }},</p>
<p>Dönem: <strong>{{ $report['period'] }}</strong></p>
<ul>
<li>Ölçüm sayısı: {{ $report['readings_count'] }}</li>
<li>Ortalama glukoz: {{ $report['avg_glucose'] ?? '—' }} mg/dL</li>
<li>Hedefte: %{{ $report['in_range_percent'] }}</li>
<li>Su toplamı: {{ $report['water_total'] }} ml</li>
<li>Egzersiz: {{ $report['exercise_minutes'] }} dk</li>
@if($report['estimated_hba1c'])<li>Tahmini HbA1c: {{ $report['estimated_hba1c'] }}%</li>@endif
</ul>
@if($report['meal_insights']->isNotEmpty())
<h3>Öğün Analizi</h3>
<ul>@foreach($report['meal_insights'] as $i)<li>{{ $i['label'] }}: {{ $i['insight'] }}</li>@endforeach</ul>
@endif
<p><a href="{{ config('app.url') }}/dashboard">Panele git</a></p>
<p style="color:#64748b;font-size:12px;">Diyabet Takip — bilgilendirme amaçlıdır.</p>
</body></html>
