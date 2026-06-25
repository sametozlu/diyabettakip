@extends('layouts.health')

@section('title', 'İlaç Düzenle')
@section('hero_title', 'İlaç Düzenle')
@section('hero_subtitle', $medication->name)

@section('content')
<div class="form-card">
  <form method="POST" action="{{ route('medications.update', $medication) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="form-group">
      <label for="name">İlaç adı</label>
      <input type="text" name="name" id="name" value="{{ old('name', $medication->name) }}" required>
    </div>
    <div class="form-group">
      <label for="dosage">Dozaj</label>
      <input type="text" name="dosage" id="dosage" value="{{ old('dosage', $medication->dosage) }}">
    </div>
    <div class="form-group">
      <label for="frequency">Kullanım sıklığı</label>
      <select name="frequency" id="frequency" required>
        @foreach ($frequencies as $key => $label)
          <option value="{{ $key }}" @selected(old('frequency', $medication->frequency) === $key)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Günlük saatler</label>
      <input type="time" name="times[]" value="{{ old('times.0', $medication->times[0] ?? '08:00') }}" style="margin-bottom:0.5rem;">
      <input type="time" name="times[]" value="{{ old('times.1', $medication->times[1] ?? '20:00') }}">
    </div>
    <div class="form-group">
      <label for="notes">Kullanım notu</label>
      <textarea name="notes" id="notes">{{ old('notes', $medication->notes) }}</textarea>
    </div>
    <div class="form-group">
      <label for="photo">{{ __('İlaç fotoğrafı') }}</label>
      @if($medication->photo_path)<img src="{{ asset('storage/'.$medication->photo_path) }}" alt="" style="height:80px;border-radius:8px;margin-bottom:0.5rem;display:block;">@endif
      <input type="file" name="photo" id="photo" accept="image/*">
    </div>
    <div class="form-group">
      <label style="display:flex;align-items:center;gap:0.5rem;font-weight:600;cursor:pointer;">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $medication->is_active)) style="width:auto;"> Aktif tedavi programında göster
      </label>
    </div>
    <div class="actions">
      <button type="submit" class="btn"><i data-lucide="save"></i> Güncelle</button>
      <a href="{{ route('medications.index') }}" class="btn btn-secondary">İptal</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
