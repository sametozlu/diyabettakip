@extends('layouts.health')

@section('title', 'HbA1c')
@section('hero_title', 'HbA1c Analizi')
@section('hero_subtitle', '3 aylık ortalama glukoz kontrolü ve trend takibi')

@php
  $latest = $stats['latest'] ?? null;
@endphp

@section('hero_stats')
<div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
  <div class="kpi-card">
    <div class="kpi-icon amber"><i data-lucide="flask-conical"></i></div>
    <div class="kpi-value">{{ $latest ? $latest->value.'%' : '—' }}</div>
    <div class="kpi-label">Son HbA1c @if($latest) · {{ $latest->tested_at->format('d.m.Y') }}@endif</div>
    @if($latest)
      <span class="badge badge-{{ $latest->status() === 'normal' ? 'normal' : ($latest->status() === 'high' ? 'high' : 'low') }}" style="margin-top:0.5rem;">
        {{ $latest->statusLabel() }}
      </span>
    @endif
  </div>
  <div class="kpi-card">
    <div class="kpi-icon indigo"><i data-lucide="bar-chart-2"></i></div>
    <div class="kpi-value">{{ $stats['average'] ? number_format($stats['average'], 1).'%' : '—' }}</div>
    <div class="kpi-label">Kayıtlı ortalama</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon teal"><i data-lucide="clipboard-list"></i></div>
    <div class="kpi-value">{{ $stats['total'] }}</div>
    <div class="kpi-label">Toplam laboratuvar kaydı</div>
  </div>
</div>
@endsection

@section('content')
<div class="page-toolbar">
  <p class="text-muted" style="margin:0;">HbA1c, son 2–3 aydaki ortalama kan şekerini yansıtır</p>
  <a href="{{ route('hba1c.create') }}" class="btn"><i data-lucide="plus"></i> Yeni Sonuç</a>
</div>

@if ($allReadings->count() > 1)
<div class="card">
  <div class="card-header">
    <div class="card-title"><i data-lucide="trending-up"></i> HbA1c Trendi</div>
  </div>
  <div class="chart-wrap"><canvas id="hba1cChart"></canvas></div>
</div>
@endif

<div class="card" style="padding:0;overflow:hidden;">
  <table class="data-table">
    <thead>
      <tr>
        <th>Test Tarihi</th>
        <th>Değer</th>
        <th>Klinik Durum</th>
        <th>Not</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($readings as $reading)
      <tr>
        <td><strong>{{ $reading->tested_at->format('d.m.Y') }}</strong></td>
        <td><strong style="font-size:1.05rem;">{{ $reading->value }}%</strong></td>
        <td>
          <span class="badge badge-{{ $reading->status() === 'normal' ? 'normal' : ($reading->status() === 'high' ? 'high' : 'low') }}">
            {{ $reading->statusLabel() }}
          </span>
        </td>
        <td class="text-muted">{{ Str::limit($reading->notes ?? '—', 60) }}</td>
        <td>
          <form method="POST" action="{{ route('hba1c.destroy', $reading) }}" onsubmit="return confirm('Bu kaydı silmek istediğinize emin misiniz?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm"><i data-lucide="trash-2"></i></button>
          </form>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="5">
          <div class="empty-state">
            <i data-lucide="flask-conical"></i>
            <p>Henüz HbA1c kaydı yok.</p>
            <a href="{{ route('hba1c.create') }}" class="btn btn-sm" style="margin-top:1rem;">İlk sonucu ekle</a>
          </div>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
  @if($readings->hasPages())
  <div style="padding:1rem;">{{ $readings->links() }}</div>
  @endif
</div>

<div class="ref-card">
  <h3><i data-lucide="book-open" style="width:16px;height:16px;"></i> Klinik Referans Aralıkları</h3>
  <p class="text-muted" style="font-size:0.85rem;margin:0;">Genel referans değerleridir. Tedavi hedefleriniz için doktorunuza danışın.</p>
  <div class="ref-scale">
    <div class="ref-item" style="border-left:3px solid var(--success);">&lt; 5.7% — Normal</div>
    <div class="ref-item" style="border-left:3px solid var(--warning);">5.7–6.4% — Prediyabet</div>
    <div class="ref-item" style="border-left:3px solid var(--danger);">≥ 6.5% — Diyabet</div>
  </div>
</div>
@endsection

@push('scripts')
<script>
lucide.createIcons();
@if($allReadings->count() > 1)
const ctx = document.getElementById('hba1cChart');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: {!! json_encode($allReadings->map(fn($r) => $r->tested_at->format('d.m.Y'))->values()) !!},
    datasets: [{
      data: {!! json_encode($allReadings->pluck('value')->values()) !!},
      borderColor: '#d97706',
      backgroundColor: 'rgba(217,119,6,0.1)',
      fill: true,
      tension: 0.35,
      borderWidth: 2.5,
      pointRadius: 5,
      pointBackgroundColor: '#d97706',
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      y: { suggestedMin: 4, suggestedMax: 10, grid: { color: '#f1f5f9' } },
      x: { grid: { display: false } }
    }
  }
});
@endif
</script>
@endpush
