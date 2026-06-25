@extends('layouts.health')
@section('title', __('İlerleme'))
@section('hero_title', __('İlerleme Takibi'))
@section('hero_subtitle', 'Kilo, HbA1c ve bel ölçümlerinizi fotoğraflarla kaydedin')
@section('content')
<div class="grid-2" style="margin-bottom:1.5rem;">
    <div class="form-card">
        <div class="card-title" style="margin-bottom:1rem;"><i data-lucide="plus-circle"></i> {{ __('Yeni kayıt') }}</div>
        <form method="POST" action="{{ route('progress.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid-2" style="gap:1rem;">
                <div class="form-group">
                    <label>{{ __('Tür') }}</label>
                    <select name="type" required>
                        @foreach($types as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __('Değer') }}</label>
                    <input type="number" step="0.01" name="value" required>
                </div>
            </div>
            <div class="form-group">
                <label>{{ __('Tarih') }}</label>
                <input type="date" name="recorded_at" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="form-group">
                <label>{{ __('Fotoğraf') }} ({{ __('opsiyonel') }})</label>
                <input type="file" name="photo" accept="image/*">
            </div>
            <div class="form-group">
                <label>{{ __('Not') }}</label>
                <textarea name="notes" rows="2"></textarea>
            </div>
            <button type="submit" class="btn"><i data-lucide="save"></i> {{ __('Kaydet') }}</button>
        </form>
    </div>
    <div class="card">
        <div class="card-title"><i data-lucide="bar-chart-2"></i> {{ __('Özet') }}</div>
        @if($weightHistory->isNotEmpty())
            <p><strong>{{ __('Son kilo') }}:</strong> {{ $weightHistory->first()->value }} kg ({{ $weightHistory->first()->recorded_at->format('d.m.Y') }})</p>
        @endif
        @if($hba1cHistory->isNotEmpty())
            <p><strong>{{ __('Son HbA1c') }}:</strong> {{ $hba1cHistory->first()->value }}% ({{ $hba1cHistory->first()->recorded_at->format('d.m.Y') }})</p>
        @endif
        @if($snapshots->isEmpty())
            <p class="text-muted">{{ __('Henüz ilerleme kaydı yok.') }}</p>
        @endif
    </div>
</div>

@if($snapshots->isNotEmpty())
<div class="photo-gallery">
    @foreach($snapshots as $snap)
    <div class="gallery-item">
        @if($snap->photo_path)
            <img src="{{ asset('storage/'.$snap->photo_path) }}" alt="">
        @else
            <div class="gallery-placeholder"><i data-lucide="trending-up"></i></div>
        @endif
        <div class="gallery-caption">
            <strong>{{ $types[$snap->type] ?? $snap->type }}: {{ $snap->value }}</strong>
            <span>{{ $snap->recorded_at->format('d.m.Y') }}</span>
            @if($snap->notes)<p class="text-muted">{{ $snap->notes }}</p>@endif
            <form method="POST" action="{{ route('progress.destroy', $snap) }}" onsubmit="return confirm('?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm"><i data-lucide="trash-2"></i></button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
