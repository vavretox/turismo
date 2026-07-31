<?php

namespace App\Http\Controllers;

use App\Models\AttractionPlace;
use App\Models\AttractionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Http\Middleware\EnsureActiveProviderSession;
use Illuminate\View\View;

class ProviderPortalController extends Controller
{
    public function login(): View { return view('provider-portal.login'); }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))->withErrors(['email' => 'Las credenciales no son correctas.']);
        }
        $request->session()->regenerate();
        if (! $request->user()->tourismServiceProvider || $request->user()->tourismServiceProvider->status !== 'approved') {
            Auth::logout();
            return back()->withErrors(['email' => 'Esta cuenta no está habilitada como prestador turístico.']);
        }
        $request->session()->put(EnsureActiveProviderSession::SESSION_KEY, now()->timestamp);
        return redirect()->intended(route('prestador.panel'));
    }

    public function dashboard(Request $request): View
    {
        $provider = $this->provider($request);
        $types = AttractionType::query()->where('activo', true)->orderBy('nombre')->get();
        return view('provider-portal.dashboard', compact('provider', 'types'));
    }

    public function update(Request $request): RedirectResponse
    {
        $provider = $this->provider($request);
        $place = $provider->mapPlace;
        $data = $request->validate([
            'attraction_type_id' => ['required', Rule::exists('attraction_types', 'id')->where('activo', true)],
            'resumen' => ['required', 'string', 'max:500'],
            'descripcion' => ['required', 'string', 'max:5000'],
            'imagen' => [$place?->imagen ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'galeria' => ['nullable', 'array', 'max:5'],
            'galeria.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'latitud' => ['required', 'numeric', 'between:-22.5,-20.5'],
            'longitud' => ['required', 'numeric', 'between:-65.8,-63.5'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:100'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'sitio_web' => ['nullable', 'url', 'max:255'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'tiktok' => ['nullable', 'url', 'max:255'],
            'x_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'horario' => ['nullable', 'string', 'max:255'],
            'precio' => ['nullable', 'string', 'max:100'],
            'room_options' => ['nullable', 'array', 'max:8'],
            'room_options.*' => ['string', 'max:80'],
            'detail_1' => ['nullable', 'string', 'max:200'],
            'detail_2' => ['nullable', 'string', 'max:500'],
            'detail_3' => ['nullable', 'string', 'max:500'],
            'action' => ['required', Rule::in(['preview', 'publish'])],
        ]);
        if ($request->hasFile('imagen')) {
            if ($place?->imagen) Storage::disk('public')->delete($place->imagen);
            $data['imagen'] = $request->file('imagen')->store('prestadores/mapa', 'public');
        }
        if ($request->hasFile('galeria')) {
            collect($place?->galeria ?? [])->each(fn ($image) => Storage::disk('public')->delete($image));
            $data['galeria'] = collect($request->file('galeria'))->map(fn ($image) => $image->store('prestadores/mapa/galeria', 'public'))->all();
        }
        $data['service_details'] = array_filter([
            'detail_1' => $data['detail_1'] ?? null,
            'detail_2' => $data['detail_2'] ?? null,
            'detail_3' => $data['detail_3'] ?? null,
        ], fn ($value) => filled($value));
        unset($data['detail_1'], $data['detail_2'], $data['detail_3'], $data['action']);
        $publish = $request->input('action') === 'publish';
        $data += ['tourism_service_provider_id' => $provider->id, 'titulo' => $provider->commercial_name, 'destacado' => false];
        $data['activo'] = $publish;
        $place = $place ? tap($place)->update($data) : AttractionPlace::create($data);
        $provider->update(['attraction_place_id' => $place->id]);
        if (! $publish) return redirect()->route('prestador.preview')->with('success', 'Borrador guardado. Revisa cómo se verá antes de publicarlo.');
        return back()->with('success', 'Cambios publicados correctamente en el portal turístico.');
    }

    public function preview(Request $request): View
    {
        $provider = $this->provider($request);
        abort_unless($provider->mapPlace, 404);
        return view('provider-portal.preview', ['provider' => $provider, 'place' => $provider->mapPlace]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('prestador.login');
    }

    public function expire(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('prestador.login')
            ->with('session_expired', 'Tu sesión se cerró por permanecer 20 minutos sin actividad.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $this->provider($request);
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        $request->user()->update(['password' => $data['password']]);
        return back()->with('account_success', 'Contraseña actualizada correctamente.');
    }

    private function provider(Request $request): mixed
    {
        $provider = $request->user()->tourismServiceProvider()->with('mapPlace.type')->firstOrFail();
        abort_unless($provider->status === 'approved', 403, 'Su acceso como prestador no está activo.');
        return $provider;
    }
}
