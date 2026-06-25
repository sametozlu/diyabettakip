<?php

namespace App\Http\Controllers;

use App\Models\ShareLink;
use App\Services\HealthAnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShareLinkController extends Controller
{
    public function __construct(private HealthAnalyticsService $analytics) {}

    public function index(Request $request): View
    {
        $links = $request->user()->shareLinks()->latest()->get();

        return view('share.index', compact('links'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:100'],
            'expires_days' => ['nullable', 'integer', 'min:1', 'max:90'],
        ]);

        $request->user()->shareLinks()->create([
            'token' => ShareLink::generateToken(),
            'label' => $validated['label'] ?? __('Doktor paylaşımı'),
            'expires_at' => isset($validated['expires_days'])
                ? now()->addDays($validated['expires_days'])
                : now()->addDays(30),
        ]);

        return redirect()->route('share.index')->with('success', __('Paylaşım linki oluşturuldu.'));
    }

    public function destroy(Request $request, ShareLink $share): RedirectResponse
    {
        abort_unless($share->user_id === $request->user()->id, 403);
        $share->delete();

        return redirect()->route('share.index')->with('success', __('Link silindi.'));
    }
}
