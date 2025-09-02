<?php

namespace App\Http\Controllers;

use App\Models\Paste;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Requests\PasteRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class PastesController extends Controller
{
    public function post(PasteRequest $request): RedirectResponse
    {
        $paste = Paste::fromRequest($request);

        return redirect()->route('show', $paste->hash);
    }

    public function show(Paste $paste, Request $request): View
    {
        if ($paste->expires_at && now()->greaterThan($paste->expires_at)) {
            abort(404, 'This paste has expired.');
        }

        if ($paste->password) {
            if (!$request->session()->get('paste_access_' . $paste->id)) {
                return view('lock', compact('paste'));
            }
        }

        return view('show', compact('paste'));
    }

    public function raw(Paste $paste, Request $request): View
    {
        if ($paste->expires_at && now()->greaterThan($paste->expires_at)) {
            abort(404, 'This paste has expired.');
        }

        if ($paste->password) {
            if (!$request->session()->get('paste_access_' . $paste->id)) {
                return view('lock', compact('paste'));
            }
        }

        return view('raw', compact('paste'));
    }

    public function edit(Paste $paste, Request $request): View
    {
        if ($paste->expires_at && now()->greaterThan($paste->expires_at)) {
            abort(404, 'This paste has expired.');
        }

        if ($paste->password) {
            if (!$request->session()->get('paste_access_' . $paste->id)) {
                return view('lock', compact('paste'));
            }
        }

        return view('edit', compact('paste'));
    }

    public function fork(PasteRequest $request, Paste $paste): RedirectResponse
    {
        $paste = Paste::fromFork($paste, $request);

        return redirect()->route('show', $paste->hash);
    }

    public function unlock(Request $request, Paste $paste)
    {
        $request->validate(['password' => 'required|string']);

        if (Hash::check($request->password, $paste->password)) {
            $request->session()->put('paste_access_' . $paste->id, true);
            return redirect()->route('show', $paste);
        }

        return back()->withErrors(['password' => 'Incorrect password.']);
    }
}
