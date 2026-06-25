@extends('layouts.health')
@section('title', 'Yemek Ekle')
@section('hero_title', 'Yemek & KH Kaydı')
@section('content')
<div class="form-card"><form method="POST" action="{{ route('food.store') }}" enctype="multipart/form-data">@csrf
<div class="form-group"><label>Yemek adı</label><input type="text" name="name" placeholder="ör. mercimek çorba + pilav"></div>
<div class="form-group"><label>Açıklama (KH tahmini için)</label><textarea name="description" placeholder="2 dilim ekmek, az pilav, çorba..."></textarea></div>
<div class="form-group"><label>KH (gram) — boş bırakırsan tahmin edilir</label><input type="number" step="0.1" name="carbs_grams"></div>
<div class="form-group"><label>Öğün</label><select name="meal_type">@foreach($mealTypes as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
<div class="form-group"><label>Fotoğraf</label><input type="file" name="photo" accept="image/*" capture="environment"></div>
<div class="form-group"><label>Tarih</label><input type="datetime-local" name="logged_at" value="{{ now()->format('Y-m-d\TH:i') }}" required></div>
<button class="btn">Kaydet</button> <a href="{{ route('food.index') }}" class="btn btn-secondary">İptal</a>
</form></div>
@endsection
