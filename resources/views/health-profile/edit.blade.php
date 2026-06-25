@extends('layouts.health')
@section('title', __('Sağlık Profili'))
@section('hero_title', __('Sağlık Profili'))
@section('hero_subtitle', 'Hedefler, BMI ve kişisel sağlık bilgileri')
@section('content')
<div class="grid-2">
<div class="form-card">
<form method="POST" action="{{ route('health-profile.update') }}" enctype="multipart/form-data">@csrf @method('PUT')
@if($profile->cover_photo)
<p><img src="{{ asset('storage/'.$profile->cover_photo) }}" alt="" style="width:100%;height:120px;object-fit:cover;border-radius:12px;margin-bottom:1rem;"></p>
@endif
<div class="grid-2" style="gap:1rem;margin-bottom:1rem;">
<div class="form-group"><label>{{ __('Kapak fotoğrafı') }}</label><input type="file" name="cover_photo" accept="image/*"></div>
<div class="form-group"><label>{{ __('Profil fotoğrafı') }}</label><input type="file" name="avatar_photo" accept="image/*">
@if($profile->avatar_photo)<img src="{{ asset('storage/'.$profile->avatar_photo) }}" alt="" style="width:48px;height:48px;border-radius:50%;margin-top:0.5rem;object-fit:cover;">@endif
</div>
</div>
<div class="grid-2" style="gap:1rem;">
<div class="form-group"><label>Hedef min (mg/dL)</label><input type="number" name="target_min" value="{{ old('target_min', $profile->target_min) }}" required></div>
<div class="form-group"><label>Hedef max (mg/dL)</label><input type="number" name="target_max" value="{{ old('target_max', $profile->target_max) }}" required></div>
</div>
<div class="grid-2" style="gap:1rem;">
<div class="form-group"><label>{{ __('Kilo') }} (kg)</label><input type="number" step="0.1" name="weight" value="{{ old('weight', $profile->weight) }}"></div>
<div class="form-group"><label>{{ __('Boy') }} (m)</label><input type="number" step="0.01" name="height" value="{{ old('height', $profile->height) }}"></div>
</div>
<div class="form-group"><label>{{ __('Diyabet tipi') }}</label>
<select name="diabetes_type"><option value="">—</option>
@foreach($diabetesTypes as $k=>$v)<option value="{{ $k }}" @selected(old('diabetes_type', $profile->diabetes_type)===$k)>{{ $v }}</option>@endforeach
</select></div>
<div class="form-group"><label>{{ __('Doktor') }}</label><input type="text" name="doctor_name" value="{{ old('doctor_name', $profile->doctor_name) }}"></div>
<div class="grid-2" style="gap:1rem;">
<div class="form-group"><label>{{ __('Su hedefi') }} (ml)</label><input type="number" name="water_goal_ml" value="{{ old('water_goal_ml', $profile->water_goal_ml ?? 2000) }}" required></div>
<div class="form-group"><label>{{ __('Adım hedefi') }}</label><input type="number" name="daily_steps_goal" value="{{ old('daily_steps_goal', $profile->daily_steps_goal ?? 8000) }}" required></div>
</div>
<button type="submit" class="btn"><i data-lucide="save"></i> {{ __('Kaydet') }}</button>
</form>
</div>
<div class="card">
<div class="card-title"><i data-lucide="calculator"></i> BMI</div>
@if($profile->bmi())
<p style="font-size:2.5rem;font-weight:800;margin:1rem 0;">{{ $profile->bmi() }}</p>
<p class="text-muted">{{ $profile->bmiCategory() }}</p>
@else<p class="text-muted">{{ __('Kilo ve boy girerek BMI hesaplayın.') }}</p>@endif
</div>
</div>
@endsection
