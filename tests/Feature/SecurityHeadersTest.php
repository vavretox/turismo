<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_responses_include_security_headers(): void
    {
        $response = $this->get(route('home'))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)')
            ->assertHeader('Content-Security-Policy');

        $this->assertStringNotContainsString(
            'upgrade-insecure-requests',
            $response->headers->get('Content-Security-Policy'),
        );
        $this->assertStringContainsString(
            "'unsafe-eval'",
            $response->headers->get('Content-Security-Policy'),
        );
    }

    public function test_embedded_map_can_be_framed_by_the_same_site(): void
    {
        $this->get(route('mapa.interactivo', ['embed' => 1]))
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader(
                'Content-Security-Policy',
                fn (string $policy): bool => str_contains($policy, "frame-ancestors 'self'"),
            );
    }
}
