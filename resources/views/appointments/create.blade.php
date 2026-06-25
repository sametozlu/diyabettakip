@extends('layouts.health')

@section('title', 'Yeni Randevu')
@section('hero_title', 'Randevu Planla')
@section('hero_subtitle', 'Doktor kontrolünüzü sisteme kaydedin')

@section('content')
<div class="form-card">
  <p class="form-hint">Randevu tarihinden 24 saat önce otomatik hatırlatma alırsınız.</p>
  <form method="POST" action="{{ route('appointments.store') }}">
    @csrf
    <div class="form-group">
      <label for="doctor_name">Doktor adı</label>
      <input type="text" name="doctor_name" id="doctor_name" value="{{ old('doctor_name') }}" required placeholder="Dr. Ayşe Yılmaz">
    </div>
    <div class="form-group">
      <label for="specialty">Branş / Uzmanlık</label>
      <input type="text" name="specialty" id="specialty" value="{{ old('specialty') }}" placeholder="Endokrinoloji, Dahiliye...">
    </div>
    <div class="form-group">
      <label for="scheduled_at">Tarih & saat</label>
      <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at') }}" required>
    </div>
    <div class="form-group">
      <label for="location">Hastane / Klinik</label>
      <input type="text" name="location" id="location" value="{{ old('location') }}" placeholder="Şehir Hastanesi, Poliklinik 3">
    </div>
    <div class="form-group">
      <label for="notes">Notlar</label>
      <textarea name="notes" id="notes" placeholder="Aç karnına gel, kan tahlili sonuçlarını getir...">{{ old('notes') }}</textarea>
    </div>
    <div class="actions">
      <button type="submit" class="btn"><i data-lucide="calendar-plus"></i> Kaydet</button>
      <a href="{{ route('appointments.index') }}" class="btn btn-secondary">İptal</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
