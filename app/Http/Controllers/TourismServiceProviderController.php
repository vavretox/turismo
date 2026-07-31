<?php

namespace App\Http\Controllers;

use App\Models\TourismServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TourismServiceProviderController extends Controller
{
    public function create(): View
    {
        return view('service-providers.create');
    }

    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q'));
        $providers = collect();

        if (mb_strlen($query) >= 2) {
            $safeQuery = addcslashes($query, '%_\\');
            $providers = TourismServiceProvider::query()
                ->where('status', 'approved')
                ->where('commercial_name', 'like', "%{$safeQuery}%")
                ->orderBy('commercial_name')
                ->limit(20)
                ->get();
        }

        return view('service-providers.index', compact('query', 'providers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $uppercaseFields = [
            'provider_type_other', 'commercial_name', 'business_name', 'nit',
            'legal_representative', 'identity_document', 'department', 'municipality',
            'address', 'lodging_type_other', 'lodging_services_other',
            'agency_services_other', 'package_types', 'main_destinations',
            'language_other', 'specialty_other', 'applicant_name', 'application_place',
        ];
        $request->merge(collect($uppercaseFields)
            ->mapWithKeys(fn (string $field): array => [
                $field => filled($request->input($field))
                    ? mb_strtoupper(trim((string) $request->input($field)), 'UTF-8')
                    : $request->input($field),
            ])
            ->all());

        $types = ['hospedaje', 'gastronomia', 'agencia_viajes', 'operadora_turismo', 'guia_departamental', 'transporte', 'artesania_comercio', 'actividad_turistica', 'otro'];
        $data = $request->validate([
            'provider_type' => ['required', Rule::in($types)],
            'provider_type_other' => ['nullable', 'required_if:provider_type,otro', 'string', 'max:120'],
            'commercial_name' => ['required', 'string', 'max:180', Rule::unique('tourism_service_providers', 'commercial_name')],
            'business_name' => ['nullable', 'string', 'max:180'],
            'nit' => ['nullable', 'string', 'max:40'],
            'has_tourism_license' => ['nullable', 'boolean'],
            'tourism_license_issued_at' => ['nullable', 'date'],
            'tourism_license_renewed_at' => ['nullable', 'date', 'after_or_equal:tourism_license_issued_at'],
            'legal_representative' => ['required', 'string', 'max:180'],
            'identity_document' => ['required', 'string', 'max:50'],
            'landline' => ['nullable', 'string', 'max:40'],
            'whatsapp' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:180'],
            'website' => ['nullable', 'url', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'x_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'other_social_network' => ['nullable', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:100'],
            'municipality' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:255'],
            'maps_location' => ['nullable', 'string', 'max:1000'],
            'lodging_type' => ['nullable', 'required_if:provider_type,hospedaje', 'string', 'max:80'],
            'lodging_type_other' => ['nullable', 'string', 'max:120'],
            'room_count' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'guest_capacity' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'lodging_services' => ['nullable', 'array'],
            'lodging_services.*' => ['string', 'max:80'],
            'lodging_services_other' => ['nullable', 'string', 'max:180'],
            'agency_type' => ['nullable', 'required_if:provider_type,agencia_viajes', 'string', 'max:80'],
            'agency_services' => ['nullable', 'array'],
            'agency_services.*' => ['string', 'max:80'],
            'agency_services_other' => ['nullable', 'string', 'max:180'],
            'tourism_modalities' => ['nullable', 'array'],
            'tourism_modalities.*' => ['string', 'max:80'],
            'package_types' => ['nullable', 'string', 'max:255'],
            'main_destinations' => ['nullable', 'string', 'max:2000'],
            'has_guide_credential' => ['nullable', 'boolean'],
            'guide_credential_issued_at' => ['nullable', 'date'],
            'guide_credential_renewed_at' => ['nullable', 'date', 'after_or_equal:guide_credential_issued_at'],
            'languages' => ['nullable', 'array'],
            'languages.*' => ['string', 'max:80'],
            'language_other' => ['nullable', 'string', 'max:120'],
            'specialties' => ['nullable', 'array'],
            'specialties.*' => ['string', 'max:80'],
            'specialty_other' => ['nullable', 'string', 'max:120'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:100'],
            'documents' => ['nullable', 'array', 'max:7'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'declaration_accepted' => ['accepted'],
            'applicant_name' => ['required', 'string', 'max:180'],
            'application_place' => ['required', 'string', 'max:120'],
            'application_date' => ['required', 'date', 'before_or_equal:today'],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'required_if' => 'Este campo es obligatorio para el tipo de prestador seleccionado.',
            'declaration_accepted.accepted' => 'Debe aceptar la declaración para enviar el registro.',
            'documents.*.max' => 'Cada documento debe pesar como máximo 10 MB.',
            'documents.*.mimes' => 'Cada documento debe ser PDF, JPG, PNG o WEBP.',
            'commercial_name.unique' => 'Ya existe un prestador registrado con este nombre comercial.',
        ], [
            'provider_type' => 'tipo de prestador',
            'commercial_name' => 'nombre comercial',
            'legal_representative' => 'representante legal o propietario',
            'identity_document' => 'documento de identidad',
            'whatsapp' => 'celular / WhatsApp',
            'email' => 'correo electrónico',
            'department' => 'departamento',
            'municipality' => 'municipio',
            'address' => 'dirección',
            'applicant_name' => 'nombre del solicitante',
            'application_place' => 'lugar de solicitud',
            'application_date' => 'fecha de solicitud',
        ]);

        $data['has_tourism_license'] = $request->boolean('has_tourism_license');
        $data['has_guide_credential'] = $request->boolean('has_guide_credential');
        $data['declaration_accepted'] = true;
        $data['documents'] = collect($request->file('documents', []))
            ->map(fn ($file, $key) => $file ? ['type' => $key, 'path' => $file->store('prestadores/documentos'), 'name' => $file->getClientOriginalName()] : null)
            ->filter()->values()->all();

        TourismServiceProvider::create($data);

        return redirect()->route('prestadores.create')->with('success', 'Registro enviado correctamente. Recibirás por correo el resultado de la revisión, las observaciones y, si eres dado de alta, tus credenciales de acceso.');
    }

    public function download(TourismServiceProvider $provider, int $index): StreamedResponse
    {
        $document = $this->findDocument($provider, $index);

        return Storage::disk('local')->download(
            $document['path'],
            $document['name'] ?? basename($document['path']),
        );
    }

    public function preview(TourismServiceProvider $provider, int $index): StreamedResponse
    {
        $document = $this->findDocument($provider, $index);

        return Storage::disk('local')->response(
            $document['path'],
            $document['name'] ?? basename($document['path']),
            [],
            'inline',
        );
    }

    private function findDocument(TourismServiceProvider $provider, int $index): array
    {
        $document = $provider->documents[$index] ?? null;
        abort_unless(
            $document
            && isset($document['path'])
            && Storage::disk('local')->exists($document['path']),
            404,
        );

        return $document;
    }
}
