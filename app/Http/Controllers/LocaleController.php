<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, ['tr', 'en'], true), 404);

        if ($request->user()) {
            $request->user()->update(['locale' => $locale]);
        }

        $request->session()->put('locale', $locale);

        return back()->withCookie(cookie('locale', $locale, 60 * 24 * 365));
    }
}
