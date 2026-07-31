<?php

namespace App\Http\Controllers;

use App\Models\ProviderOffering;
use App\Models\ProvinciaTuristica;
use App\Models\TourismServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProviderOfferingController extends Controller
{
    public function create(Request $request): View
    {
        return view('provider-portal.offering-form', [
            'provider' => $this->provider($request),
            'offering' => null,
            'destinations' => $this->destinations(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $provider = $this->provider($request);
        $data = $this->validated($request);
        $data['imagen'] = $request->file('imagen')->store('prestadores/ofertas', 'public');
        $data['galeria'] = collect($request->file('galeria', []))->map(fn ($image) => $image->store('prestadores/ofertas/galeria', 'public'))->all();
        $data['tourism_service_provider_id'] = $provider->id;
        ProviderOffering::create($data);
        return redirect()->route('prestador.panel')->with('success', 'Contenido publicado correctamente en tu portal web.');
    }

    public function edit(Request $request, ProviderOffering $offering): View
    {
        $provider = $this->provider($request);
        $this->authorizeOwner($provider, $offering);
        return view('provider-portal.offering-form', [
            'provider' => $provider,
            'offering' => $offering,
            'destinations' => $this->destinations(),
        ]);
    }

    public function update(Request $request, ProviderOffering $offering): RedirectResponse
    {
        $provider = $this->provider($request);
        $this->authorizeOwner($provider, $offering);
        $data = $this->validated($request, $offering);
        if ($request->hasFile('imagen')) {
            Storage::disk('public')->delete($offering->imagen);
            $data['imagen'] = $request->file('imagen')->store('prestadores/ofertas', 'public');
        }
        if ($request->hasFile('galeria')) {
            collect($offering->galeria ?? [])->each(fn ($image) => Storage::disk('public')->delete($image));
            $data['galeria'] = collect($request->file('galeria'))->map(fn ($image) => $image->store('prestadores/ofertas/galeria', 'public'))->all();
        }
        $offering->update($data);
        return redirect()->route('prestador.panel')->with('success', 'Contenido actualizado correctamente.');
    }

    public function destroy(Request $request, ProviderOffering $offering): RedirectResponse
    {
        $provider = $this->provider($request);
        $this->authorizeOwner($provider, $offering);
        Storage::disk('public')->delete(array_filter([$offering->imagen, ...($offering->galeria ?? [])]));
        $offering->delete();
        return back()->with('success', 'Contenido eliminado de tu portal.');
    }

    private function validated(Request $request, ?ProviderOffering $offering = null): array
    {
        return $request->validate([
            'titulo' => ['required', 'string', 'max:120'],
            'resumen' => ['required', 'string', 'max:350'],
            'descripcion' => ['nullable', 'string', 'max:3000'],
            'imagen' => [$offering?->imagen ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'galeria' => ['nullable', 'array', 'max:4'],
            'galeria.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'duracion' => ['nullable', 'string', 'max:100'],
            'precio' => ['nullable', 'string', 'max:100'],
            'incluye' => ['nullable', 'string', 'max:1000'],
            'destination_ids' => [
                in_array($request->user()->tourismServiceProvider?->provider_type, ['agencia_viajes', 'operadora_turismo'], true) ? 'required' : 'nullable',
                'array',
                'min:1',
            ],
            'destination_ids.*' => ['integer', 'distinct', 'exists:provincias_turisticas,id'],
            'activo' => ['nullable', 'boolean'],
            'orden' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]) + ['activo' => $request->boolean('activo')];
    }

    private function provider(Request $request): TourismServiceProvider
    {
        $provider = $request->user()->tourismServiceProvider;
        abort_unless($provider?->status === 'approved', 403);
        return $provider;
    }

    private function authorizeOwner(TourismServiceProvider $provider, ProviderOffering $offering): void
    {
        abort_unless($offering->tourism_service_provider_id === $provider->id, 403);
    }

    private function destinations()
    {
        return ProvinciaTuristica::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->get(['id', 'nombre', 'provincia']);
    }
}
