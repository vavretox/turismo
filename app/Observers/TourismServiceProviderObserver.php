<?php

namespace App\Observers;

use App\Mail\ProviderStatusMail;
use App\Models\TourismServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class TourismServiceProviderObserver
{
    public function updated(TourismServiceProvider $provider): void
    {
        if (! $provider->wasChanged(['status', 'admin_notes'])) return;

        $temporaryPassword = null;
        if ($provider->status === 'approved' && ! $provider->user_id) {
            $temporaryPassword = Str::password(14);
            $user = User::query()->where('email', $provider->email)->first();
            if (! $user) {
                $user = User::create([
                    'name' => $provider->legal_representative,
                    'email' => $provider->email,
                    'password' => $temporaryPassword,
                    'role' => 'provider',
                    'is_admin' => false,
                ]);
            }
            if ($user->role === 'provider' && ! $user->tourismServiceProvider) {
                $user->update(['password' => $temporaryPassword]);
                $provider->updateQuietly(['user_id' => $user->id]);
            } else {
                $temporaryPassword = null;
            }
        }

        if ($provider->status === 'suspended' && $provider->mapPlace?->activo) {
            $provider->mapPlace->update(['activo' => false]);
        }

        try {
            Mail::to($provider->email)->send(new ProviderStatusMail($provider->fresh(), $temporaryPassword));
        } catch (Throwable $exception) {
            Log::error('No se pudo notificar el estado del prestador.', ['provider_id' => $provider->id, 'error' => $exception->getMessage()]);
        }
    }
}
