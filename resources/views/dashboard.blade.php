@extends('layouts.health')

@section('title', 'Panel')
@section('hero_title', 'Sağlık Paneli')
@section('hero_subtitle', 'Kan şekeri, diyet ve tedavi takibiniz tek ekranda')

@section('hero_stats')
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon teal"><i data-lucide="droplets"></i></div>
        <div class="kpi-value">{{ $stats['last'] ?? '—' }}<span style="font-size:0.9rem;font-weight:600;color:var(--muted)"> mg/dL</span></div>
        <div class="kpi-label">Son glukoz ölçümü</div>
        @if(isset($stats['last_status']))
            <span class="badge badge-{{ $stats['last_status'] }}" style="margin-top:0.5rem;">
                {{ $stats['last_status'] === 'normal' ? 'Hedef aralıkta' : ($stats['last_status'] === 'high' ? 'Yüksek' : 'Düşük') }}
            </span>
        @endif
    </div>

    <div class="kpi-card">
        <div class="kpi-icon indigo"><i data-lucide="bar-chart-3"></i></div>
        <div class="kpi-value">{{ $stats['avg_7d'] ?: '—' }}</div>
        <div class="kpi-label">7 günlük ortalama (mg/dL)</div>
        @if($stats['trend'] !== null)
            <span class="kpi-trend {{ $stats['trend'] > 0 ? 'up' : ($stats['trend'] < 0 ? 'down' : 'neutral') }}">
                <i data-lucide="{{ $stats['trend'] > 0 ? 'trending-up' : ($stats['trend'] < 0 ? 'trending-down' : 'minus') }}" style="width:12px;height:12px;"></i>
                {{ $stats['trend'] > 0 ? '+' : '' }}{{ $stats['trend'] }} önceki haftaya göre
            </span>
        @endif
    </div>

    <div class="kpi-card">
        <div class="kpi-icon emerald"><i data-lucide="target"></i></div>
        <div class="kpi-value">{{ $stats['in_range_percent'] }}%</div>
        <div class="kpi-label">Hedef aralıkta ({{ $targetMin }}–{{ $targetMax }} mg/dL)</div>
        <div class="range-bar"><div class="range-bar-fill" style="width:{{ $stats['in_range_percent'] }}%"></div></div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon amber"><i data-lucide="flask-conical"></i></div>
        <div class="kpi-value">{{ $stats['last_hba1c'] ?? '—' }}{{ $stats['last_hba1c'] ? '%' : '' }}</div>
        <div class="kpi-label">Son HbA1c @if($stats['hba1c_status']) · {{ $stats['hba1c_status'] }}@endif</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon sky"><i data-lucide="clipboard-list"></i></div>
        <div class="kpi-value">{{ $stats['readings_7d'] }}</div>
        <div class="kpi-label">Bu hafta ölçüm · {{ $stats['active_meds'] }} aktif ilaç</div>
    </div>
</div>
@endsection

@section('content')
@if ($reminders->isNotEmpty())
<div class="alert alert-warning">
    <i data-lucide="bell-ring"></i>
    <div>
        <strong>Randevu hatırlatması</strong> — 24 saat içinde:
        @foreach ($reminders as $r)
            {{ $r->doctor_name }} ({{ $r->scheduled_at->format('d.m H:i') }})@if(!$loop->last), @endif
        @endforeach
    </div>
</div>
@endif

@if(isset($alerts) && $alerts->isNotEmpty())
@foreach($alerts as $alert)
<div class="alert alert-{{ $alert['severity']==='danger'?'error':($alert['severity']==='warning'?'warning':'success') }}">
    <i data-lucide="alert-triangle"></i> {{ $alert['message'] }}
</div>
@endforeach
@endif

@if(isset($estimatedHbA1c) && $estimatedHbA1c)
<div class="alert alert-success" style="margin-bottom:1.25rem;">
    <i data-lucide="flask-conical"></i>
    <span><strong>{{ __('Tahmini HbA1c') }}:</strong> {{ $estimatedHbA1c }}% ({{ __('son 90 gün ortalamasından') }})</span>
</div>
@endif

@if(isset($mealInsights) && $mealInsights->isNotEmpty())
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-header">
        <div class="card-title"><i data-lucide="utensils-crossed"></i> {{ __('Öğün Analizi') }}</div>
        <div class="card-subtitle">Son 30 gün · öğün bazlı glukoz</div>
    </div>
    @foreach($mealInsights as $insight)
    <div class="timeline-item">
        <div class="timeline-dot"></div>
        <div class="timeline-content">
            <strong>{{ $insight['label'] }}</strong> — {{ $insight['avg'] }} mg/dL ort. ({{ $insight['count'] }} ölçüm)
            <div class="meta">{{ $insight['insight'] }}</div>
        </div>
    </div>
    @endforeach
</div>
@endif

@if(isset($achievements))
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-header"><div class="card-title"><i data-lucide="award"></i> {{ __('Rozetler') }}</div></div>
    <div class="grid-3">
        @foreach($achievements as $badge)
        <div class="badge-card {{ $badge['earned'] ? 'earned' : 'locked' }}">
            <div class="badge-icon">{{ $badge['earned'] ? \App\Helpers\HealthImages::badge($badge['id']) : '🔒' }}</div>
            <strong style="font-size:0.88rem;">{{ $badge['title'] }}</strong>
            <p class="text-muted" style="font-size:0.75rem;margin-top:0.25rem;">{{ $badge['description'] }}</p>
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="grid-2" style="margin-bottom:1.25rem;">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title"><i data-lucide="line-chart"></i> Glukoz Trend Analizi</div>
                <div class="card-subtitle">Son 7 gün · hedef bant {{ $targetMin }}–{{ $targetMax }} mg/dL</div>
            </div>
            <a href="{{ route('blood-sugar.index') }}" class="btn btn-secondary btn-sm">Tüm veriler</a>
        </div>
        <div class="chart-wrap"><canvas id="weeklyChart"></canvas></div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title"><i data-lucide="utensils"></i> Bugünün Beslenme Planı</div>
                @if($todayMeal)
                    <div class="card-subtitle">{{ $todayMeal->day_name }} · {{ $todayMeal->plan_date->format('d.m.Y') }}</div>
                @endif
            </div>
            @if($todayMeal)
                <a href="{{ route('meals.show', $todayMeal) }}" class="btn btn-sm">Detay</a>
            @endif
        </div>
        @if ($todayMeal)
            <p style="font-size:0.9rem;line-height:1.65;color:var(--text-secondary);margin-bottom:1rem;">{{ $todayMeal->menu_items }}</p>
            <div class="tag-row">
                @foreach(array_slice($todayMeal->eat_items, 0, 3) as $item)
                    <span class="tag tag-eat"><i data-lucide="check" style="width:12px;height:12px;"></i> {{ Str::limit($item, 40) }}</span>
                @endforeach
            </div>
            @if(count($todayMeal->skip_items))
                <div class="tag-row">
                    @foreach($todayMeal->skip_items as $item)
                        <span class="tag tag-skip"><i data-lucide="x" style="width:12px;height:12px;"></i> {{ $item }}</span>
                    @endforeach
                </div>
            @endif
        @else
            <div class="empty-state">
                <i data-lucide="utensils-crossed"></i>
                <p>Bugün için diyet planı tanımlı değil.</p>
                <a href="{{ route('meals.index') }}" class="btn btn-secondary btn-sm" style="margin-top:1rem;">Planları görüntüle</a>
            </div>
        @endif
    </div>
</div>

<div class="grid-3">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i data-lucide="activity"></i> Son Ölçümler</div>
            <a href="{{ route('blood-sugar.create') }}" class="btn btn-sm"><i data-lucide="plus"></i> Ekle</a>
        </div>
        @forelse ($readings as $reading)
            <div class="glucose-row">
                <div class="glucose-meter {{ $reading->status() }}">{{ round($reading->value) }}</div>
                <div class="glucose-info">
                    <div class="value-line">{{ $reading->value }} mg/dL</div>
                    <div class="meta-line">
                        {{ \App\Models\BloodSugarReading::CONTEXTS[$reading->context] ?? $reading->context }}
                        · {{ $reading->measured_at->format('d.m.Y H:i') }}
                    </div>
                </div>
                <span class="badge badge-{{ $reading->status() }}">
                    {{ $reading->status() === 'normal' ? 'Normal' : ($reading->status() === 'high' ? 'Yüksek' : 'Düşük') }}
                </span>
            </div>
        @empty
            <div class="empty-state"><i data-lucide="inbox"></i><p>Henüz ölçüm kaydı yok.</p></div>
        @endforelse
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i data-lucide="calendar-clock"></i> Randevular</div>
            <a href="{{ route('appointments.create') }}" class="btn btn-sm"><i data-lucide="plus"></i> Ekle</a>
        </div>
        @forelse ($upcomingAppointments as $appt)
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <strong>{{ $appt->doctor_name }}</strong>
                    @if($appt->specialty)<span class="text-muted"> — {{ $appt->specialty }}</span>@endif
                    <div class="meta">
                        <i data-lucide="clock" style="width:12px;height:12px;display:inline;vertical-align:-2px;"></i>
                        {{ $appt->scheduled_at->format('d.m.Y · H:i') }}
                        @if($appt->location) · {{ $appt->location }}@endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state"><i data-lucide="calendar-x"></i><p>Yaklaşan randevu bulunmuyor.</p></div>
        @endforelse
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i data-lucide="pill"></i> İlaç Programı</div>
            <a href="{{ route('medications.index') }}" class="btn btn-secondary btn-sm">Yönet</a>
        </div>
        @forelse ($activeMedications as $med)
            <div class="glucose-row">
                <div class="glucose-meter normal" style="font-size:1rem;">💊</div>
                <div class="glucose-info">
                    <div class="value-line">{{ $med->name }}</div>
                    <div class="meta-line">{{ $med->dosage }} · {{ $med->frequencyLabel() }}</div>
                    @if($med->times)
                        <div style="margin-top:0.35rem;">
                            @foreach($med->times as $t)
                                <span class="med-time"><i data-lucide="clock" style="width:10px;height:10px;"></i> {{ $t }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state"><i data-lucide="pill-bottle"></i><p>Aktif ilaç kaydı yok.</p></div>
        @endforelse
    </div>
</div>

{{-- Günlük özet tablosu --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title"><i data-lucide="table-2"></i> Haftalık Özet Tablosu</div>
            <div class="card-subtitle">Günlük ortalama, min-max ve hedef uyumu</div>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Gün</th>
                <th>Tarih</th>
                <th>Ortalama</th>
                <th>Min – Max</th>
                <th>Ölçüm</th>
                <th>Hedef uyumu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($weeklyDetail as $day)
            <tr>
                <td><strong>{{ $day['label'] }}</strong></td>
                <td class="text-muted">{{ $day['date'] }}</td>
                <td>
                    @if($day['avg'])
                        <strong>{{ $day['avg'] }}</strong> <span class="text-muted">mg/dL</span>
                    @else — @endif
                </td>
                <td class="text-muted">
                    @if($day['min']){{ $day['min'] }} – {{ $day['max'] }}@else — @endif
                </td>
                <td>{{ $day['count'] ?: '—' }}</td>
                <td>
                    @if($day['in_range'] !== null)
                        <span class="badge {{ $day['in_range'] >= 70 ? 'badge-normal' : ($day['in_range'] >= 50 ? 'badge-low' : 'badge-high') }}">
                            {{ $day['in_range'] }}%
                        </span>
                    @else — @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
const labels = {!! json_encode($weeklyDetail->pluck('label')) !!};
const values = {!! json_encode($weeklyDetail->pluck('avg')) !!};
const targetMin = {{ $targetMin }};
const targetMax = {{ $targetMax }};

const ctx = document.getElementById('weeklyChart');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Ortalama mg/dL',
                    data: values,
                    borderColor: '#0d9488',
                    backgroundColor: 'rgba(13,148,136,0.08)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointRadius: 5,
                    pointBackgroundColor: '#0d9488',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    spanGaps: true,
                },
                {
                    label: 'Üst hedef',
                    data: Array(labels.length).fill(targetMax),
                    borderColor: 'rgba(220,38,38,0.35)',
                    borderDash: [6, 4],
                    borderWidth: 1.5,
                    pointRadius: 0,
                    fill: false,
                },
                {
                    label: 'Alt hedef',
                    data: Array(labels.length).fill(targetMin),
                    borderColor: 'rgba(217,119,6,0.35)',
                    borderDash: [6, 4],
                    borderWidth: 1.5,
                    pointRadius: 0,
                    fill: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 16, font: { family: 'Plus Jakarta Sans', size: 11 } }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 12,
                    cornerRadius: 10,
                    titleFont: { family: 'Plus Jakarta Sans', weight: '700' },
                    bodyFont: { family: 'Plus Jakarta Sans' },
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }, color: '#94a3b8' } },
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#94a3b8' },
                    suggestedMin: 50,
                    suggestedMax: 200,
                }
            }
        }
    });
}
lucide.createIcons();
</script>
@endpush
