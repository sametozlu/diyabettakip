@extends('layouts.health')
@section('title', __('Egzersiz Ekle'))
@section('hero_title', __('Egzersiz Kaydı'))
@section('content')
<div class="form-card"><form method="POST" action="{{ route('exercise.store') }}">@csrf
<div class="form-group"><label>{{ __('Tür') }}</label><select name="type">@foreach($types as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
<div class="form-group"><label>{{ __('Süre') }} (dk)</label><input type="number" name="duration_minutes" required min="1"></div>
<div class="form-group"><label>{{ __('Adım') }}</label><input type="number" name="steps" min="0"></div>
<div class="form-group"><label>{{ __('Tarih') }}</label><input type="datetime-local" name="logged_at" value="{{ now()->format('Y-m-d\TH:i') }}" required></div>
<div class="form-group"><label>{{ __('Not') }}</label><textarea name="notes"></textarea></div>
<button class="btn">{{ __('Kaydet') }}</button> <a href="{{ route('exercise.index') }}" class="btn btn-secondary">{{ __('İptal') }}</a>
</form></div>
@endsection
