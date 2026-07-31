<?php

namespace Tests\Feature;

use App\Models\TourismServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TourismServiceProviderRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_form_is_public(): void
    {
        $this->get(route('prestadores.create'))->assertOk()->assertSee('Registro de prestadores');
    }

    public function test_provider_can_submit_registration(): void
    {
        Storage::fake('local');

        $response = $this->post(route('prestadores.store'), [
            'provider_type' => 'hospedaje', 'commercial_name' => 'Hotel Tarija',
            'legal_representative' => 'Ana Pérez', 'identity_document' => '1234567',
            'whatsapp' => '70000000', 'email' => 'hotel@example.com',
            'department' => 'Tarija', 'municipality' => 'Tarija', 'address' => 'Centro',
            'lodging_type' => 'hotel', 'declaration_accepted' => '1',
            'applicant_name' => 'Ana Pérez', 'application_place' => 'Tarija',
            'application_date' => now()->toDateString(),
            'documents' => ['nit' => UploadedFile::fake()->image('nit.jpg')],
        ]);

        $response->assertRedirect(route('prestadores.create'));
        $this->assertDatabaseHas('tourism_service_providers', ['commercial_name' => 'Hotel Tarija', 'status' => 'pending']);
        $path = TourismServiceProvider::firstOrFail()->documents[0]['path'];
        Storage::disk('local')->assertExists($path);
    }

    public function test_duplicate_commercial_name_is_rejected(): void
    {
        TourismServiceProvider::create([
            'provider_type' => 'hospedaje', 'commercial_name' => 'Hotel Tarija',
            'legal_representative' => 'Ana Pérez', 'identity_document' => '1234567',
            'whatsapp' => '70000000', 'email' => 'hotel@example.com', 'department' => 'Tarija',
            'municipality' => 'Tarija', 'address' => 'Centro', 'declaration_accepted' => true,
            'applicant_name' => 'Ana Pérez', 'application_place' => 'Tarija',
            'application_date' => now()->toDateString(),
        ]);

        $this->from(route('prestadores.create'))->post(route('prestadores.store'), [
            'provider_type' => 'hospedaje', 'commercial_name' => 'Hotel Tarija',
            'legal_representative' => 'Otra persona', 'identity_document' => '7654321',
            'whatsapp' => '71111111', 'email' => 'otro@example.com', 'department' => 'Tarija',
            'municipality' => 'Tarija', 'address' => 'Otra dirección', 'lodging_type' => 'hotel',
            'declaration_accepted' => '1', 'applicant_name' => 'Otra persona',
            'application_place' => 'Tarija', 'application_date' => now()->toDateString(),
        ])->assertRedirect(route('prestadores.create'))->assertSessionHasErrors('commercial_name');

        $this->assertDatabaseCount('tourism_service_providers', 1);
    }

    public function test_public_can_check_an_approved_provider_license(): void
    {
        TourismServiceProvider::create([
            'provider_type' => 'hospedaje', 'commercial_name' => 'Hotel Público',
            'legal_representative' => 'Dato Privado', 'identity_document' => 'PRIVADO-123',
            'whatsapp' => '70000000', 'email' => 'privado@example.com', 'department' => 'Tarija',
            'municipality' => 'Tarija', 'address' => 'Dirección privada', 'declaration_accepted' => true,
            'applicant_name' => 'Dato Privado', 'application_place' => 'Tarija',
            'application_date' => now()->toDateString(), 'status' => 'approved',
            'has_tourism_license' => true, 'tourism_license_renewed_at' => now()->addYear()->toDateString(),
        ]);

        $this->get(route('prestadores.index', ['q' => 'Hotel Público']))
            ->assertOk()
            ->assertSee('Hotel Público')
            ->assertSee('Licencia vigente')
            ->assertDontSee('PRIVADO-123')
            ->assertDontSee('privado@example.com');
    }

    public function test_public_cannot_see_unapproved_provider_workflows(): void
    {
        TourismServiceProvider::create([
            'provider_type' => 'hospedaje', 'commercial_name' => 'Expediente Reservado',
            'legal_representative' => 'Dato Privado', 'identity_document' => 'PRIVADO-456',
            'whatsapp' => '70000000', 'email' => 'privado@example.com', 'department' => 'Tarija',
            'municipality' => 'Tarija', 'address' => 'Dirección privada', 'declaration_accepted' => true,
            'applicant_name' => 'Dato Privado', 'application_place' => 'Tarija',
            'application_date' => now()->toDateString(), 'status' => 'reviewing',
        ]);

        $this->get(route('prestadores.index', ['q' => 'Expediente Reservado']))
            ->assertOk()
            ->assertSee('0 encontrado(s)')
            ->assertDontSee('En revisión');
    }

    public function test_document_download_requires_an_administrator(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('prestadores/documentos/test.pdf', 'private');
        $provider = TourismServiceProvider::create([
            'provider_type' => 'hospedaje', 'commercial_name' => 'Documentos Privados',
            'legal_representative' => 'Persona', 'identity_document' => '123',
            'whatsapp' => '70000000', 'email' => 'provider@example.com', 'department' => 'Tarija',
            'municipality' => 'Tarija', 'address' => 'Centro', 'declaration_accepted' => true,
            'applicant_name' => 'Persona', 'application_place' => 'Tarija',
            'application_date' => now()->toDateString(),
            'documents' => [['path' => 'prestadores/documentos/test.pdf', 'name' => 'test.pdf']],
        ]);
        $url = route('prestadores.documents.download', ['provider' => $provider, 'index' => 0]);

        $this->get($url)->assertRedirect(route('filament.admin.auth.login'));

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get($url)->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true, 'role' => 'admin']);
        $this->actingAs($admin)->get($url)->assertDownload('test.pdf');
    }
}
