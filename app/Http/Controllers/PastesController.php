<?php

namespace App\Http\Controllers;

use App\DTOs\CreatePasteDataDTO;
use App\Enums\ColorScheme;
use App\Enums\ExpiryOption;
use App\Models\Paste;
use GuzzleHttp\Promise\Create;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Requests\PasteRequest;
use App\Services\PasteService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class PastesController extends Controller
{
    public function __construct(private PasteService $pasteService) {}

    public function post(PasteRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $data = new CreatePasteDataDTO(
            code: $validated['code'],
            colorScheme: ColorScheme::fromInput($validated['color_scheme']),
            expiryOption: ExpiryOption::fromInput($validated['expiry']),
            customExpiry: $validated['custom_expiry'] ?
                \Carbon\CarbonImmutable::parse($validated['custom_expiry']) : null,
            passwordPlain: $validated['password'],
        );

        $paste = $this->pasteService->create($data);

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
