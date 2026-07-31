<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
            'nombre' => ['nullable', 'string', 'max:100'],
            'consentimiento' => ['accepted'],
        ]);

        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => mb_strtolower($data['email'])],
            [
                'nombre' => $data['nombre'] ?? null,
                'activo' => true,
                'suscrito_en' => now(),
                'cancelado_en' => null,
            ],
        );

        return response()->json([
            'message' => $subscriber->wasRecentlyCreated
                ? '¡Bienvenido! Ya formas parte de nuestra comunidad viajera.'
                : 'Tu suscripción fue actualizada correctamente.',
        ]);
    }

    public function unsubscribe(string $token): RedirectResponse
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();
        $subscriber->update(['activo' => false, 'cancelado_en' => now()]);

        return redirect()->route('home')->with('newsletter_status', 'Tu suscripción fue cancelada correctamente.');
    }
}
