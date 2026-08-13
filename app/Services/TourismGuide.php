<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TourismGuide
{
    public function answer(string $question, array $history, array $sources): ?string
    {
        $apiKey = config('services.gemini.api_key');

        if (blank($apiKey) || $sources === []) {
            return null;
        }

        $context = collect($sources)->map(function (array $source, int $index): string {
            return sprintf(
                "[%d] %s: %s\nResumen: %s\nEnlace: %s",
                $index + 1,
                $source['type'],
                $source['title'],
                $source['summary'] ?: 'Sin resumen disponible.',
                $source['url'],
            );
        })->implode("\n\n");

        $conversation = collect($history)
            ->take(-6)
            ->map(fn (array $message): string => sprintf(
                '%s: %s',
                $message['role'] === 'user' ? 'Visitante' : 'Guía',
                $message['content'],
            ))
            ->push("Visitante: {$question}")
            ->implode("\n");

        try {
            $response = Http::withHeaders(['x-goog-api-key' => $apiKey])
                ->acceptJson()
                ->timeout((int) config('services.gemini.timeout', 20))
                ->retry(2, 250, throw: false)
                ->post('https://generativelanguage.googleapis.com/v1beta/interactions', [
                    'model' => config('services.gemini.model', 'gemini-3.1-flash-lite'),
                    'system_instruction' => $this->instructions($context),
                    'input' => $conversation,
                    'store' => false,
                    'generation_config' => [
                        'max_output_tokens' => 350,
                        'thinking_level' => 'low',
                    ],
                ]);

            if ($response->failed()) {
                Log::warning('El asistente turístico no pudo obtener una respuesta de Gemini.', [
                    'status' => $response->status(),
                ]);

                return null;
            }

            return $this->outputText($response->json());
        } catch (ConnectionException $exception) {
            Log::warning('El asistente turístico no pudo conectarse con Gemini.', [
                'exception' => $exception::class,
            ]);

            return null;
        }
    }

    private function instructions(string $context): string
    {
        return <<<PROMPT
Eres Explora Tarija, guía turístico virtual del portal oficial de turismo de Tarija, Bolivia.
Responde en español claro, amable y útil, en un máximo de 120 palabras.
Usa exclusivamente la información del CONTEXTO DEL PORTAL. No inventes horarios, precios, distancias, disponibilidad ni datos de seguridad.
Cuando falte un dato, dilo con transparencia y recomienda consultar el enlace mostrado en la interfaz.
Si la consulta no es turística o no está respaldada por el contexto, explica brevemente qué temas sí puedes atender.
No escribas URLs ni referencias como [1]: las tarjetas con sus enlaces se muestran por separado.

CONTEXTO DEL PORTAL:
{$context}
PROMPT;
    }

    private function outputText(array $response): ?string
    {
        $texts = collect($response['steps'] ?? [])
            ->where('type', 'model_output')
            ->flatMap(fn (array $step): array => $step['content'] ?? [])
            ->where('type', 'text')
            ->pluck('text')
            ->filter();

        return $texts->isNotEmpty() ? trim($texts->implode("\n")) : null;
    }
}
