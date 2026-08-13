<?php

namespace Tests\Unit;

use App\Services\TourismGuide;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TourismGuideTest extends TestCase
{
    public function test_it_grounds_a_response_in_portal_sources(): void
    {
        config()->set('services.gemini.api_key', 'test-key');
        config()->set('services.gemini.model', 'test-model');
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'status' => 'completed',
                'steps' => [[
                    'type' => 'model_output',
                    'content' => [['type' => 'text', 'text' => 'Uriondo es ideal para conocer viñedos y bodegas.']],
                ]],
            ]),
        ]);

        $answer = app(TourismGuide::class)->answer('¿Qué hago en Uriondo?', [], [[
            'type' => 'Municipio',
            'title' => 'Uriondo',
            'summary' => 'Municipio vitivinícola del valle de Tarija.',
            'url' => 'https://turismo.test/municipios/uriondo',
        ]]);

        $this->assertSame('Uriondo es ideal para conocer viñedos y bodegas.', $answer);
        Http::assertSent(fn ($request) =>
            $request['model'] === 'test-model'
            && $request->hasHeader('x-goog-api-key', 'test-key')
            && str_contains($request['system_instruction'], 'Uriondo')
            && str_contains($request['input'], '¿Qué hago en Uriondo?')
            && $request['store'] === false
        );
    }

    public function test_it_uses_the_local_fallback_without_an_api_key(): void
    {
        config()->set('services.gemini.api_key', null);
        Http::fake();

        $answer = app(TourismGuide::class)->answer('¿Qué puedo visitar?', [], [[
            'type' => 'Destino', 'title' => 'Tarija', 'summary' => 'Valle', 'url' => '/tarija',
        ]]);

        $this->assertNull($answer);
        Http::assertNothingSent();
    }
}
