<!DOCTYPE html>
<html lang="tr"><body style="font-family:sans-serif;padding:24px;background:#f0fdfa;">
<h2 style="color:#0d9488;">{{ __('Randevu Hatırlatması') }}</h2>
<p>{{ __('Merhaba') }} {{ $appointment->user->name }},</p>
<p>{{ __('Yaklaşan randevunuz') }}:</p>
<ul>
<li><strong>{{ __('Doktor') }}:</strong> {{ $appointment->doctor_name }}</li>
<li><strong>{{ __('Branş') }}:</strong> {{ $appointment->specialty ?? '—' }}</li>
<li><strong>{{ __('Tarih') }}:</strong> {{ $appointment->scheduled_at->format('d.m.Y H:i') }}</li>
<li><strong>{{ __('Konum') }}:</strong> {{ $appointment->location ?? '—' }}</li>
</ul>
<p>{{ __('İyi günler dileriz.') }}<br>Diyabet Takip</p>
</body></html>
