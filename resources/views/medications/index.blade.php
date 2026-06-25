@extends('layouts.health')

@section('title', 'İlaçlar')
@section('hero_title', 'İlaç Yönetimi')
@section('hero_subtitle', 'Tedavi programınız ve günlük doz takibi')

@php
  $active = $medications->where('is_active', true);
  $inactive = $medications->where('is_active', false);
@endphp

@section('hero_stats')
<div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
  <div class="kpi-card">
    <div class="kpi-icon teal"><i data-lucide="pill"></i></div>
    <div class="kpi-value">{{ $active->count() }}</div>
    <div class="kpi-label">Aktif ilaç</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon indigo"><i data-lucide="clock"></i></div>
    <div class="kpi-value">{{ $active->sum(fn($m) => count($m->times ?? [])) }}</div>
    <div class="kpi-label">Günlük doz zamanı</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon sky"><i data-lucide="archive"></i></div>
    <div class="kpi-value">{{ $inactive->count() }}</div>
    <div class="kpi-label">Pasif / arşiv</div>
  </div>
</div>
@endsection

@section('content')
<div class="page-toolbar">
  <p class="text-muted" style="margin:0;">İlaç saatlerinizi düzenli takip edin</p>
  <a href="{{ route('medications.create') }}" class="btn"><i data-lucide="plus"></i> İlaç Ekle</a>
</div>

<div class="grid-2">
  @forelse ($medications as $med)
  <div class="med-card {{ !$med->is_active ? 'inactive' : '' }}">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.75rem;">
      <div style="display:flex;gap:0.85rem;align-items:flex-start;">
        <div class="kpi-icon teal" style="width:42px;height:42px;border-radius:11px;flex-shrink:0;">
          <i data-lucide="pill" style="width:18px;height:18px;"></i>
        </div>
        <div>
          <h3 style="margin:0;font-size:1rem;font-weight:800;">{{ $med->name }}</h3>
          @if ($med->dosage)<p class="text-muted" style="margin:0.15rem 0 0;">{{ $med->dosage }}</p>@endif
        </div>
      </div>
      <span class="badge {{ $med->is_active ? 'badge-normal' : '' }}" style="{{ !$med->is_active ? 'background:var(--border-light);color:var(--muted);' : '' }}">
        {{ $med->is_active ? 'Aktif' : 'Pasif' }}
      </span>
    </div>

    <p style="margin:0.85rem 0 0.35rem;font-size:0.88rem;font-weight:600;color:var(--text-secondary);">
      <i data-lucide="repeat" style="width:14px;height:14px;display:inline;vertical-align:-2px;"></i>
      {{ $med->frequencyLabel() }}
    </p>

    @if ($med->times)
    <div style="margin-top:0.35rem;">
      @foreach ($med->times as $time)
        <span class="med-time"><i data-lucide="alarm-clock" style="width:12px;height:12px;"></i> {{ $time }}</span>
      @endforeach
    </div>
    @endif

    @if ($med->notes)
      <p class="text-muted" style="font-size:0.82rem;margin-top:0.65rem;line-height:1.5;">{{ $med->notes }}</p>
    @endif

    <div class="actions" style="margin-top:1rem;padding-top:0.85rem;border-top:1px solid var(--border-light);">
      <a href="{{ route('medications.edit', $med) }}" class="btn btn-secondary btn-sm"><i data-lucide="pencil"></i> Düzenle</a>
      <form method="POST" action="{{ route('medications.destroy', $med) }}" onsubmit="return confirm('Bu ilacı silmek istediğinize emin misiniz?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm"><i data-lucide="trash-2"></i></button>
      </form>
    </div>
  </div>
  @empty
  <div class="card">
    <div class="empty-state">
      <i data-lucide="pill-bottle"></i>
      <p>Henüz ilaç kaydı yok.</p>
      <a href="{{ route('medications.create') }}" class="btn btn-sm" style="margin-top:1rem;">İlk ilacı ekle</a>
    </div>
  </div>
  @endforelse
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
