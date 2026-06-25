@extends('layouts.health')

@section('title', 'Randevular')
@section('hero_title', 'Doktor Randevuları')
@section('hero_subtitle', 'Kontrol takvimi, hatırlatmalar ve randevu geçmişi')

@php
  $upcoming = $appointments->filter(fn ($a) => $a->scheduled_at->isFuture());
  $reminderCount = $upcoming->filter(fn ($a) => $a->needsReminder())->count();
@endphp

@section('hero_stats')
<div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
  <div class="kpi-card">
    <div class="kpi-icon indigo"><i data-lucide="calendar-clock"></i></div>
    <div class="kpi-value">{{ $upcoming->count() }}</div>
    <div class="kpi-label">Yaklaşan randevu</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon amber"><i data-lucide="bell-ring"></i></div>
    <div class="kpi-value">{{ $reminderCount }}</div>
    <div class="kpi-label">24 saat içinde</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon teal"><i data-lucide="history"></i></div>
    <div class="kpi-value">{{ $appointments->count() - $upcoming->count() }}</div>
    <div class="kpi-label">Tamamlanan</div>
  </div>
</div>
@endsection

@section('content')
<div class="page-toolbar">
  <p class="text-muted" style="margin:0;">
    <i data-lucide="stethoscope" style="width:14px;height:14px;display:inline;vertical-align:-2px;"></i>
    Tüm randevularınız kronolojik sırada listelenir
  </p>
  <a href="{{ route('appointments.create') }}" class="btn"><i data-lucide="plus"></i> Yeni Randevu</a>
</div>

@if ($upcoming->isNotEmpty())
<div class="card">
  <div class="card-header">
    <div>
      <div class="card-title"><i data-lucide="calendar-days"></i> Yaklaşan Randevular</div>
      <div class="card-subtitle">Önümüzdeki kontroller</div>
    </div>
  </div>
  @foreach ($upcoming->take(3) as $appt)
  <div class="timeline-item appt-timeline">
    <img src="{{ $appt->display_image }}" alt="" class="appt-thumb">
    <div class="timeline-dot" style="{{ $appt->needsReminder() ? 'background:var(--warning);box-shadow:0 0 0 4px var(--warning-bg);' : '' }}"></div>
    <div class="timeline-content" style="flex:1;">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
        <div>
          <strong>{{ $appt->doctor_name }}</strong>
          <div class="meta">{{ $appt->specialty ?? 'Genel kontrol' }} · {{ $appt->location ?? 'Konum belirtilmedi' }}</div>
        </div>
        <div style="text-align:right;">
          <span class="badge badge-{{ $appt->needsReminder() ? 'high' : 'normal' }}">
            {{ $appt->needsReminder() ? 'Yaklaşıyor' : 'Planlandı' }}
          </span>
          <div class="meta" style="margin-top:0.35rem;font-weight:600;color:var(--text);">
            {{ $appt->scheduled_at->locale('tr')->isoFormat('D MMM YYYY, HH:mm') }}
          </div>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endif

<div class="card" style="padding:0;overflow:hidden;">
  <table class="data-table">
    <thead>
      <tr>
        <th>Doktor</th>
        <th>Branş</th>
        <th>Tarih & Saat</th>
        <th>Konum</th>
        <th>Durum</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($appointments as $appt)
      <tr>
        <td><strong>{{ $appt->doctor_name }}</strong></td>
        <td class="text-muted">{{ $appt->specialty ?? '—' }}</td>
        <td>
          <strong>{{ $appt->scheduled_at->format('d.m.Y') }}</strong>
          <span class="text-muted"> {{ $appt->scheduled_at->format('H:i') }}</span>
        </td>
        <td class="text-muted">{{ Str::limit($appt->location ?? '—', 30) }}</td>
        <td>
          @if ($appt->scheduled_at->isPast())
            <span class="badge" style="background:var(--border-light);color:var(--muted);">Geçmiş</span>
          @elseif ($appt->needsReminder())
            <span class="badge badge-high">Yaklaşıyor</span>
          @else
            <span class="badge badge-normal">Planlandı</span>
          @endif
        </td>
        <td class="actions">
          <a href="{{ route('appointments.edit', $appt) }}" class="btn btn-secondary btn-sm"><i data-lucide="pencil"></i></a>
          <form method="POST" action="{{ route('appointments.destroy', $appt) }}" style="display:inline;" onsubmit="return confirm('Bu randevuyu silmek istediğinize emin misiniz?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm"><i data-lucide="trash-2"></i></button>
          </form>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6">
          <div class="empty-state">
            <i data-lucide="calendar-x"></i>
            <p>Henüz randevu kaydı yok.</p>
            <a href="{{ route('appointments.create') }}" class="btn btn-sm" style="margin-top:1rem;">İlk randevuyu ekle</a>
          </div>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
