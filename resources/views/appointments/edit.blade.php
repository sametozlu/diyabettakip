@extends('layouts.health')

@section('title', 'Randevu Düzenle')
@section('hero_title', 'Randevu Düzenle')
@section('hero_subtitle', $appointment->doctor_name)

@section('content')
<div class="form-card">
  <form method="POST" action="{{ route('appointments.update', $appointment) }}">
    @csrf @method('PUT')
    <div class="form-group">
      <label for="doctor_name">Doktor adı</label>
      <input type="text" name="doctor_name" id="doctor_name" value="{{ old('doctor_name', $appointment->doctor_name) }}" required>
    </div>
    <div class="form-group">
      <label for="specialty">Branş / Uzmanlık</label>
      <input type="text" name="specialty" id="specialty" value="{{ old('specialty', $appointment->specialty) }}">
    </div>
    <div class="form-group">
      <label for="scheduled_at">Tarih & saat</label>
      <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at', $appointment->scheduled_at->format('Y-m-d\TH:i')) }}" required>
    </div>
    <div class="form-group">
      <label for="location">Hastane / Klinik</label>
      <input type="text" name="location" id="location" value="{{ old('location', $appointment->location) }}">
    </div>
    <div class="form-group">
      <label for="notes">Notlar</label>
      <textarea name="notes" id="notes">{{ old('notes', $appointment->notes) }}</textarea>
    </div>
    <div class="actions">
      <button type="submit" class="btn"><i data-lucide="save"></i> Güncelle</button>
      <a href="{{ route('appointments.index') }}" class="btn btn-secondary">İptal</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
