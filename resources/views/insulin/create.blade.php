@extends('layouts.health')
@section('title', __('Yeni Kayıt'))
@section('hero_title', __('İnsülin / Karb Ekle'))
@section('content')
<div class="form-card"><form method="POST" action="{{ route('insulin.store') }}">@csrf
<div class="form-group"><label>KH (gram)</label><input type="number" step="0.1" name="carbs_grams"></div>
<div class="form-group"><label>{{ __('İnsülin') }} (ünite)</label><input type="number" step="0.1" name="insulin_units"></div>
<div class="form-group"><label>{{ __('Öğün') }}</label><select name="meal_type">@foreach($mealTypes as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
<div class="form-group"><label>{{ __('Tarih') }}</label><input type="datetime-local" name="logged_at" value="{{ now()->format('Y-m-d\TH:i') }}" required></div>
<div class="form-group"><label>{{ __('Not') }}</label><textarea name="notes"></textarea></div>
<button class="btn">{{ __('Kaydet') }}</button> <a href="{{ route('insulin.index') }}" class="btn btn-secondary">{{ __('İptal') }}</a>
</form></div>
@endsection
