@extends('layouts.health')

@section('title', 'Yeni Ölçüm')
@section('hero_title', 'Glukoz Ölçümü Ekle')
@section('hero_subtitle', 'Profesyonel takip için ölçüm değerinizi kaydedin')

@section('content')
<div class="form-card">
  <p class="form-hint">
    <i data-lucide="info" style="width:14px;height:14px;display:inline;vertical-align:-2px;"></i>
    Hedef aralığınız: <strong>{{ auth()->user()->healthProfile?->target_min ?? 70 }}–{{ auth()->user()->healthProfile?->target_max ?? 140 }} mg/dL</strong>
  </p>
  <form method="POST" action="{{ route('blood-sugar.store') }}">
    @csrf
    <div class="form-group">
      <label for="value">Glukoz değeri (mg/dL)</label>
      <input type="number" step="0.1" name="value" id="value" value="{{ old('value') }}" required placeholder="örn. 110" style="font-size:1.25rem;font-weight:700;">
    </div>
    <div class="form-group">
      <label for="context">Ölçüm tipi</label>
      <select name="context" id="context" required>
        @foreach ($contexts as $key => $label)
          <option value="{{ $key }}" @selected(old('context') === $key)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label for="measured_at">Tarih & saat</label>
      <input type="datetime-local" name="measured_at" id="measured_at" value="{{ old('measured_at', now()->format('Y-m-d\TH:i')) }}" required>
    </div>
    <div class="form-group">
      <label for="notes">{{ __('Klinik not') }} ({{ __('opsiyonel') }})</label>
      <textarea name="notes" id="notes" placeholder="Yemekten sonra, egzersiz sonrası...">{{ old('notes') }}</textarea>
    </div>
    <div class="grid-2" style="gap:1rem;">
      <div class="form-group">
        <label for="mood">{{ __('Ruh hali') }}</label>
        <select name="mood" id="mood">
          <option value="">—</option>
          @foreach ($moods as $key => $label)
            <option value="{{ $key }}" @selected(old('mood') === $key)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label for="sleep_hours">{{ __('Uyku') }} ({{ __('saat') }})</label>
        <input type="number" step="0.5" name="sleep_hours" id="sleep_hours" value="{{ old('sleep_hours') }}" min="0" max="24" placeholder="7.5">
      </div>
    </div>
    <div class="form-group">
      <label for="stress_level">{{ __('Stres') }} (1-5)</label>
      <input type="range" name="stress_level" id="stress_level" min="1" max="5" value="{{ old('stress_level', 3) }}" oninput="document.getElementById('stress-val').textContent=this.value">
      <span class="text-muted">{{ __('Seviye') }}: <span id="stress-val">{{ old('stress_level', 3) }}</span></span>
    </div>
    <div class="actions">
      <button type="submit" class="btn"><i data-lucide="save"></i> Kaydet</button>
      <a href="{{ route('blood-sugar.index') }}" class="btn btn-secondary">İptal</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
