@extends('layouts.health')

@section('title', 'İlaç Ekle')
@section('hero_title', 'Yeni İlaç Kaydı')
@section('hero_subtitle', 'Tedavi programınıza ilaç ekleyin')

@section('content')
<div class="form-card">
  <form method="POST" action="{{ route('medications.store') }}">
    @csrf
    <div class="form-group">
      <label for="name">İlaç adı</label>
      <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Metformin, İnsülin, Vitamin D...">
    </div>
    <div class="form-group">
      <label for="dosage">Dozaj</label>
      <input type="text" name="dosage" id="dosage" value="{{ old('dosage') }}" placeholder="500 mg, 10 ünite, 1 tablet...">
    </div>
    <div class="form-group">
      <label for="frequency">Kullanım sıklığı</label>
      <select name="frequency" id="frequency" required>
        @foreach ($frequencies as $key => $label)
          <option value="{{ $key }}" @selected(old('frequency') === $key)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Günlük saatler (opsiyonel)</label>
      <input type="time" name="times[]" value="{{ old('times.0', '08:00') }}" style="margin-bottom:0.5rem;">
      <input type="time" name="times[]" value="{{ old('times.1', '20:00') }}">
    </div>
    <div class="form-group">
      <label for="notes">Kullanım notu</label>
      <textarea name="notes" id="notes" placeholder="Yemekten sonra alınacak...">{{ old('notes') }}</textarea>
    </div>
    <div class="form-group">
      <label style="display:flex;align-items:center;gap:0.5rem;font-weight:600;cursor:pointer;">
        <input type="checkbox" name="is_active" value="1" checked style="width:auto;"> Aktif tedavi programında göster
      </label>
    </div>
    <div class="actions">
      <button type="submit" class="btn"><i data-lucide="save"></i> Kaydet</button>
      <a href="{{ route('medications.index') }}" class="btn btn-secondary">İptal</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
