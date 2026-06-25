@extends('layouts.health')

@section('title', 'Kan Şekeri')
@section('hero_title', 'Glukoz Takibi')
@section('hero_subtitle', 'Tüm kan şekeri ölçümleriniz ve analiz geçmişi')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:0.75rem;">
    <div class="text-muted" style="font-size:0.88rem;">
        <i data-lucide="info" style="width:14px;height:14px;display:inline;vertical-align:-2px;"></i>
        Hedef aralık: <strong style="color:var(--text);">{{ auth()->user()->healthProfile?->target_min ?? 70 }}–{{ auth()->user()->healthProfile?->target_max ?? 140 }} mg/dL</strong>
    </div>
    <a href="{{ route('blood-sugar.create') }}" class="btn"><i data-lucide="plus"></i> Yeni Ölçüm</a>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Ölçüm</th>
                <th>Değer</th>
                <th>Durum</th>
                <th>Tip</th>
                <th>Tarih & Saat</th>
                <th>Not</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($readings as $reading)
            <tr>
                <td>
                    <div class="glucose-meter {{ $reading->status() }}" style="width:40px;height:40px;font-size:0.8rem;border-radius:10px;">
                        {{ round($reading->value) }}
                    </div>
                </td>
                <td><strong style="font-size:1rem;">{{ $reading->value }}</strong> <span class="text-muted">mg/dL</span></td>
                <td>
                    <span class="badge badge-{{ $reading->status() }}">
                        {{ $reading->status() === 'normal' ? 'Normal' : ($reading->status() === 'high' ? 'Yüksek' : 'Düşük') }}
                    </span>
                </td>
                <td>{{ $contexts[$reading->context] ?? $reading->context }}</td>
                <td>
                    <strong>{{ $reading->measured_at->format('d.m.Y') }}</strong>
                    <span class="text-muted"> {{ $reading->measured_at->format('H:i') }}</span>
                </td>
                <td class="text-muted" style="max-width:180px;">{{ Str::limit($reading->notes ?? '—', 50) }}</td>
                <td>
                    <form method="POST" action="{{ route('blood-sugar.destroy', $reading) }}" onsubmit="return confirm('Bu kaydı silmek istediğinize emin misiniz?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i data-lucide="trash-2"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i data-lucide="activity"></i>
                        <p>Henüz glukoz ölçümü kaydedilmedi.</p>
                        <a href="{{ route('blood-sugar.create') }}" class="btn btn-sm" style="margin-top:1rem;">İlk ölçümü ekle</a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:1rem;">{{ $readings->links() }}</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
