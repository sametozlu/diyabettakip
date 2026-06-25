@extends('layouts.health')

@section('title', 'HbA1c Ekle')
@section('hero_title', 'Laboratuvar Sonucu')
@section('hero_subtitle', 'HbA1c değerinizi sisteme kaydedin')

@section('content')
<div class="form-card">
  <p class="form-hint">HbA1c testi genellikle 3 ayda bir yapılır ve uzun vadeli glukoz kontrolünü gösterir.</p>
  <form method="POST" action="{{ route('hba1c.store') }}">
    @csrf
    <div class="form-group">
      <label for="value">HbA1c değeri (%)</label>
      <input type="number" step="0.1" name="value" id="value" value="{{ old('value') }}" required placeholder="örn. 6.2" style="font-size:1.25rem;font-weight:700;">
    </div>
    <div class="form-group">
      <label for="tested_at">Test tarihi</label>
      <input type="date" name="tested_at" id="tested_at" value="{{ old('tested_at', now()->format('Y-m-d')) }}" required>
    </div>
    <div class="form-group">
      <label for="notes">Laboratuvar notu (opsiyonel)</label>
      <textarea name="notes" id="notes" placeholder="Aç karnına alındı, referans aralığı...">{{ old('notes') }}</textarea>
    </div>
    <div class="actions">
      <button type="submit" class="btn"><i data-lucide="save"></i> Kaydet</button>
      <a href="{{ route('hba1c.index') }}" class="btn btn-secondary">İptal</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
