<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class ContactoController extends Controller
{
    public function index(): View
    {
        return view('contacto.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $contact = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'telefono' => ['nullable', 'string', 'max:40'],
            'motivo' => ['required', 'in:consulta,experiencia,sugerencia,queja,servicio'],
            'mensaje' => ['required', 'string', 'max:3000'],
        ]);

        try {
            Mail::to(config('contact.recipient'))->send(new ContactMessage($contact));
        } catch (Throwable $exception) {
            Log::error('No se pudo enviar el formulario de contacto.', [
                'exception' => $exception->getMessage(),
                'sender' => $contact['email'],
            ]);

            return back()->withInput()->with('error', 'No pudimos enviar tu mensaje en este momento. Intenta nuevamente más tarde.');
        }

        return back()->with('success', 'Tu mensaje fue enviado correctamente. Te contactaremos pronto.');
    }
}
