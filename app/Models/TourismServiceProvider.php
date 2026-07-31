<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourismServiceProvider extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'has_tourism_license' => 'boolean',
            'has_guide_credential' => 'boolean',
            'declaration_accepted' => 'boolean',
            'tourism_license_issued_at' => 'date',
            'tourism_license_renewed_at' => 'date',
            'guide_credential_issued_at' => 'date',
            'guide_credential_renewed_at' => 'date',
            'application_date' => 'date',
            'lodging_services' => 'array',
            'agency_services' => 'array',
            'tourism_modalities' => 'array',
            'languages' => 'array',
            'specialties' => 'array',
            'documents' => 'array',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function mapPlace(): BelongsTo { return $this->belongsTo(AttractionPlace::class, 'attraction_place_id'); }
    public function offerings(): HasMany { return $this->hasMany(ProviderOffering::class)->orderBy('orden')->orderByDesc('id'); }

    public function contentLabels(): array
    {
        return match ($this->provider_type) {
            'hospedaje' => ['Habitaciones y promociones', 'habitación o promoción', 'Ej.: Suite matrimonial con desayuno', 'Capacidad o noches', 'Servicios incluidos'],
            'gastronomia' => ['Menús y experiencias gastronómicas', 'menú o experiencia', 'Ej.: Menú degustación chapaco', 'Horario o modalidad', 'Platos incluidos'],
            'agencia_viajes', 'operadora_turismo' => ['Paquetes turísticos', 'paquete turístico', 'Ej.: Ruta del vino — día completo', 'Duración', 'Qué incluye el paquete'],
            'guia_departamental' => ['Recorridos guiados', 'recorrido', 'Ej.: Caminata histórica por Tarija', 'Duración', 'Qué incluye el recorrido'],
            'transporte' => ['Rutas y traslados', 'ruta o traslado', 'Ej.: Aeropuerto — centro de Tarija', 'Duración del viaje', 'Qué incluye el servicio'],
            'artesania_comercio' => ['Productos destacados', 'producto', 'Ej.: Tejido artesanal chapaco', 'Tiempo de elaboración', 'Características'],
            'actividad_turistica' => ['Experiencias y actividades', 'experiencia', 'Ej.: Ciclismo entre viñedos', 'Duración', 'Qué incluye la actividad'],
            default => ['Servicios destacados', 'servicio', 'Ej.: Experiencia turística personalizada', 'Duración o modalidad', 'Qué incluye'],
        };
    }

    public function servicesProvided(): array
    {
        $labels = [
            'wifi' => 'Wi-Fi',
            'restaurante' => 'Restaurante',
            'piscina' => 'Piscina',
            'estacionamiento' => 'Estacionamiento',
            'aire_acondicionado' => 'Aire acondicionado',
            'desayuno' => 'Desayuno',
            'accesibilidad' => 'Accesibilidad',
            'boletos' => 'Venta de boletos',
            'hoteles' => 'Reservas de hoteles',
            'transporte' => 'Transporte turístico',
            'seguros' => 'Seguros de viaje',
            'cultural' => 'Turismo cultural',
            'naturaleza' => 'Naturaleza',
            'aventura' => 'Aventura',
            'comunitario' => 'Turismo comunitario',
            'rural' => 'Turismo rural',
            'gastronomico' => 'Turismo gastronómico',
            'religioso' => 'Turismo religioso',
            'arqueologia' => 'Arqueología',
            'gastronomia' => 'Gastronomía',
        ];

        [$services, $other] = match ($this->provider_type) {
            'hospedaje' => [$this->lodging_services ?? [], $this->lodging_services_other],
            'agencia_viajes' => [$this->agency_services ?? [], $this->agency_services_other],
            'operadora_turismo' => [$this->tourism_modalities ?? [], $this->package_types],
            'guia_departamental' => [$this->specialties ?? [], $this->specialty_other],
            default => [[], $this->provider_type_other],
        };

        $primaryService = match ($this->provider_type) {
            'hospedaje' => 'Alojamiento turístico',
            'agencia_viajes' => 'Agencia de viajes',
            'operadora_turismo' => 'Operación de tours',
            'guia_departamental' => 'Guianza turística',
            default => null,
        };

        return collect([$primaryService])
            ->merge($services)
            ->filter()
            ->map(fn (string $service): string => $labels[$service] ?? ucfirst(str_replace('_', ' ', $service)))
            ->when(filled($other), fn ($items) => $items->push($other))
            ->unique()
            ->values()
            ->all();
    }
}
