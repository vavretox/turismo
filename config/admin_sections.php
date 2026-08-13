<?php

use App\Filament\Resources\AttractionPlaceResource;
use App\Filament\Resources\AttractionTypeResource;
use App\Filament\Resources\CategoriaResource;
use App\Filament\Resources\DestinoResource;
use App\Filament\Resources\EventoResource;
use App\Filament\Resources\NewsletterSubscriberResource;
use App\Filament\Resources\NoticiaResource;
use App\Filament\Resources\PortalImageResource;
use App\Filament\Resources\ProvinciaTuristicaResource;
use App\Filament\Resources\SiteIdentityResource;
use App\Filament\Resources\TourismServiceProviderResource;
use App\Filament\Resources\WeeklyActivityResource;
use App\Filament\Resources\VirtualTourResource;

return [
    'destinos' => ['label' => 'Destinos', 'resource' => DestinoResource::class],
    'categorias' => ['label' => 'Categorías', 'resource' => CategoriaResource::class],
    'eventos' => ['label' => 'Eventos', 'resource' => EventoResource::class],
    'noticias' => ['label' => 'Noticias', 'resource' => NoticiaResource::class],
    'municipios' => ['label' => 'Municipios turísticos', 'resource' => ProvinciaTuristicaResource::class],
    'actividad_semanal' => ['label' => 'Actividad de la semana', 'resource' => WeeklyActivityResource::class],
    'lugares_mapa' => ['label' => 'Lugares del mapa', 'resource' => AttractionPlaceResource::class],
    'tours_360' => ['label' => 'Fotografías 360°', 'resource' => VirtualTourResource::class],
    'tipos_atractivo' => ['label' => 'Tipos de atractivo', 'resource' => AttractionTypeResource::class],
    'imagenes_portal' => ['label' => 'Imágenes del portal', 'resource' => PortalImageResource::class],
    'identidad_sitio' => ['label' => 'Identidad y tipografía', 'resource' => SiteIdentityResource::class],
    'prestadores_turisticos' => ['label' => 'Prestadores turísticos', 'resource' => TourismServiceProviderResource::class],
    'suscriptores' => ['label' => 'Suscriptores del newsletter', 'resource' => NewsletterSubscriberResource::class],
];
