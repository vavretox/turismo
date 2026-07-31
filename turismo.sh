#!/bin/bash

# ============================================
# SCRIPT DE INSTALACIÓN - PORTAL TURÍSTICO
# Laravel 12 + Tailwind + Livewire + Filament
# ============================================

echo "🚀 Iniciando instalación del Portal Turístico..."
echo "================================================"

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para imprimir mensajes
print_message() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_step() {
    echo -e "\n${BLUE}▶${NC} $1"
}

# Verificar si composer está instalado
if ! command -v composer &> /dev/null; then
    print_error "Composer no está instalado. Por favor, instálalo primero."
    exit 1
fi

# Verificar si npm está instalado
if ! command -v npm &> /dev/null; then
    print_error "npm no está instalado. Por favor, instálalo primero."
    exit 1
fi

# Verificar si PHP está instalado
if ! command -v php &> /dev/null; then
    print_error "PHP no está instalado. Por favor, instálalo primero."
    exit 1
fi

# 1. Crear el proyecto Laravel
print_step "Creando proyecto Laravel..."
if [ -d "portal-turistico" ]; then
    print_error "El directorio portal-turistico ya existe. ¿Deseas eliminarlo? (y/n)"
    read -r response
    if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
        rm -rf portal-turistico
    else
        print_error "Instalación cancelada."
        exit 1
    fi
fi

composer create-project laravel/laravel portal-turistico
cd portal-turistico

print_message "Proyecto Laravel creado exitosamente"

# 2. Instalar dependencias de Laravel
print_step "Instalando dependencias de Laravel..."
composer require filament/filament:"^3.0" livewire/livewire:"^3.0"
composer require laravel/socialite
composer require spatie/laravel-medialibrary
composer require spatie/laravel-sluggable

print_message "Dependencias instaladas"

# 3. Instalar dependencias de Node
print_step "Instalando dependencias de Node..."
npm install -D tailwindcss postcss autoprefixer
npm install alpinejs swiper gsap aos leaflet @fortawesome/fontawesome-free
npm install -D @tailwindcss/forms @tailwindcss/typography @tailwindcss/aspect-ratio

print_message "Dependencias Node instaladas"

# 4. Crear estructura de directorios
print_step "Creando estructura de directorios..."
mkdir -p app/Filament/Resources
mkdir -p app/Filament/Resources/DestinoResource
mkdir -p app/Filament/Resources/DestinoResource/Pages
mkdir -p app/Filament/Resources/EventoResource
mkdir -p app/Filament/Resources/NoticiaResource
mkdir -p app/Http/Controllers
mkdir -p app/Models
mkdir -p resources/views/partials
mkdir -p resources/views/layouts
mkdir -p resources/css
mkdir -p resources/js
mkdir -p public/images

print_message "Estructura de directorios creada"

# 5. Crear archivos de configuración
print_step "Creando archivos de configuración..."

# vite.config.js
cat > vite.config.js << 'EOF'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~': path.resolve(__dirname, 'resources'),
        },
    },
});
EOF
print_message "vite.config.js creado"

# tailwind.config.js
cat > tailwind.config.js << 'EOF'
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/Filament/**/*.php',
        './app/Http/Livewire/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                },
                turquoise: {
                    50: '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#14b8a6',
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a',
                },
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
                display: ['Montserrat', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
    ],
};
EOF
print_message "tailwind.config.js creado"

# 6. Crear archivos CSS
print_step "Creando archivos CSS..."

# resources/css/app.css
cat > resources/css/app.css << 'EOF'
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
    html {
        scroll-behavior: smooth;
    }
    
    body {
        @apply bg-gray-50 font-sans antialiased;
    }
    
    h1, h2, h3, h4, h5, h6 {
        @apply font-display;
    }
}

@layer components {
    .container-custom {
        @apply container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl;
    }
    
    .btn-primary {
        @apply inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-all duration-200 transform hover:scale-105;
    }
    
    .btn-secondary {
        @apply inline-flex items-center px-6 py-3 bg-white text-primary-600 font-semibold rounded-lg border-2 border-primary-600 hover:bg-primary-50 transition-all duration-200;
    }
    
    .section-title {
        @apply text-3xl md:text-4xl font-bold text-gray-900 mb-4;
    }
    
    .section-subtitle {
        @apply text-lg text-gray-600 max-w-2xl;
    }
    
    .card {
        @apply bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300;
    }
    
    .nav-link {
        @apply text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200;
    }
}

@layer utilities {
    .text-gradient {
        @apply bg-gradient-to-r from-primary-600 to-turquoise-500 bg-clip-text text-transparent;
    }
}
EOF
print_message "app.css creado"

# 7. Crear archivos JavaScript
print_step "Creando archivos JavaScript..."

# resources/js/app.js
cat > resources/js/app.js << 'EOF'
import './bootstrap';
import Alpine from 'alpinejs';
import Swiper from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';
import AOS from 'aos';
import 'aos/dist/aos.css';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

window.Alpine = Alpine;
Alpine.start();

AOS.init({
    duration: 1000,
    once: true,
    offset: 100,
});

gsap.registerPlugin(ScrollTrigger);

window.initializeSwiper = (selector, options = {}) => {
    return new Swiper(selector, {
        modules: [Navigation, Pagination, Autoplay, EffectFade],
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        effect: 'fade',
        loop: true,
        ...options
    });
};

window.initializeMap = (elementId, coordinates, options = {}) => {
    const map = L.map(elementId).setView(coordinates, options.zoom || 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    L.marker(coordinates).addTo(map)
        .bindPopup(options.popup || 'Ubicación')
        .openPopup();
    
    return map;
};

document.addEventListener('DOMContentLoaded', () => {
    gsap.from('.hero-title', {
        duration: 1,
        y: 50,
        opacity: 0,
        ease: 'power3.out',
        scrollTrigger: {
            trigger: '.hero-title',
            start: 'top 80%',
        }
    });
    
    gsap.from('.hero-subtitle', {
        duration: 1,
        y: 30,
        opacity: 0,
        delay: 0.3,
        ease: 'power3.out',
        scrollTrigger: {
            trigger: '.hero-subtitle',
            start: 'top 80%',
        }
    });
    
    gsap.from('.hero-button', {
        duration: 1,
        y: 30,
        opacity: 0,
        delay: 0.6,
        ease: 'power3.out',
        scrollTrigger: {
            trigger: '.hero-button',
            start: 'top 80%',
        }
    });
});

document.addEventListener('alpine:init', () => {
    Alpine.store('menu', {
        isOpen: false,
        toggle() {
            this.isOpen = !this.isOpen;
        },
        close() {
            this.isOpen = false;
        }
    });
});
EOF
print_message "app.js creado"

# 8. Crear vistas Blade
print_step "Creando vistas Blade..."

# Layout principal
cat > resources/views/layouts/app.blade.php << 'EOF'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Portal Turístico'))</title>
    <meta name="description" content="@yield('description', 'Descubre los mejores destinos turísticos')">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body>
    <div id="app" x-data>
        @include('partials.navbar')
        
        <main>
            @yield('content')
        </main>
        
        @include('partials.footer')
        
        @stack('scripts')
    </div>
</body>
</html>
EOF

# Navbar
cat > resources/views/partials/navbar.blade.php << 'EOF'
<nav class="bg-white shadow-lg fixed w-full z-50" x-data="{ isOpen: false }">
    <div class="container-custom">
        <div class="flex justify-between items-center h-20">
            <a href="/" class="flex items-center space-x-2">
                <i class="fas fa-umbrella-beach text-3xl text-primary-600"></i>
                <span class="text-2xl font-display font-bold text-gradient">Turismo</span>
            </a>
            
            <div class="hidden lg:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="nav-link">Inicio</a>
                <a href="{{ route('destinos') }}" class="nav-link">Destinos</a>
                <a href="{{ route('eventos') }}" class="nav-link">Eventos</a>
                <a href="{{ route('noticias') }}" class="nav-link">Noticias</a>
                <a href="{{ route('contacto') }}" class="nav-link">Contacto</a>
                <a href="{{ route('filament.admin.auth.login') }}" class="btn-primary text-sm py-2 px-4">
                    <i class="fas fa-user mr-2"></i> Admin
                </a>
            </div>
            
            <button @click="isOpen = !isOpen" class="lg:hidden text-gray-600 hover:text-primary-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <div x-show="isOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="lg:hidden pb-4">
            <div class="flex flex-col space-y-3">
                <a href="{{ route('home') }}" class="nav-link block px-3 py-2 rounded-lg hover:bg-gray-50">Inicio</a>
                <a href="{{ route('destinos') }}" class="nav-link block px-3 py-2 rounded-lg hover:bg-gray-50">Destinos</a>
                <a href="{{ route('eventos') }}" class="nav-link block px-3 py-2 rounded-lg hover:bg-gray-50">Eventos</a>
                <a href="{{ route('noticias') }}" class="nav-link block px-3 py-2 rounded-lg hover:bg-gray-50">Noticias</a>
                <a href="{{ route('contacto') }}" class="nav-link block px-3 py-2 rounded-lg hover:bg-gray-50">Contacto</a>
            </div>
        </div>
    </div>
</nav>
EOF

# Footer
cat > resources/views/partials/footer.blade.php << 'EOF'
<footer class="bg-gray-900 text-white mt-20">
    <div class="container-custom py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-2xl font-display font-bold text-gradient mb-4">Turismo</h3>
                <p class="text-gray-400 mb-4">Descubre los mejores destinos y experiencias turísticas con nosotros.</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors"><i class="fab fa-facebook text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors"><i class="fab fa-instagram text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors"><i class="fab fa-twitter text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors"><i class="fab fa-youtube text-xl"></i></a>
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold text-lg mb-4">Enlaces Rápidos</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-primary-500 transition-colors">Destinos</a></li>
                    <li><a href="#" class="hover:text-primary-500 transition-colors">Eventos</a></li>
                    <li><a href="#" class="hover:text-primary-500 transition-colors">Noticias</a></li>
                    <li><a href="#" class="hover:text-primary-500 transition-colors">Galería</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-semibold text-lg mb-4">Contacto</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><i class="fas fa-map-marker-alt mr-2 text-primary-500"></i> Ciudad, País</li>
                    <li><i class="fas fa-phone mr-2 text-primary-500"></i> +123 456 7890</li>
                    <li><i class="fas fa-envelope mr-2 text-primary-500"></i> info@turismo.com</li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-semibold text-lg mb-4">Newsletter</h4>
                <p class="text-gray-400 mb-4">Suscríbete para recibir noticias y ofertas.</p>
                <form class="flex flex-col space-y-2">
                    <input type="email" placeholder="Tu email" class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <button type="submit" class="btn-primary text-sm py-2">Suscribirse</button>
                </form>
            </div>
        </div>
        
        <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} Turismo. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>
EOF

# Home
cat > resources/views/home.blade.php << 'EOF'
@extends('layouts.app')

@section('title', 'Inicio - Portal Turístico')
@section('description', 'Descubre los mejores destinos turísticos para tus próximas vacaciones')

@section('content')
    <section class="relative h-screen flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" 
                 alt="Paisaje turístico" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        </div>
        
        <div class="relative container-custom text-center text-white">
            <h1 class="hero-title text-4xl md:text-6xl lg:text-7xl font-display font-bold leading-tight mb-6">
                Descubre el <span class="text-gradient">Mundo</span><br>
                con Nosotros
            </h1>
            <p class="hero-subtitle text-xl md:text-2xl text-gray-200 max-w-3xl mx-auto mb-8">
                Encuentra los mejores destinos turísticos, eventos y experiencias para tus próximas aventuras.
            </p>
            <div class="hero-button flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('destinos') }}" class="btn-primary text-lg px-8 py-4">
                    <i class="fas fa-search mr-2"></i> Explorar Destinos
                </a>
                <a href="#destacados" class="btn-secondary text-lg px-8 py-4">
                    <i class="fas fa-play mr-2"></i> Ver Más
                </a>
            </div>
        </div>
        
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <i class="fas fa-chevron-down text-2xl text-white"></i>
        </div>
    </section>
    
    <section id="destacados" class="py-16 bg-gray-50">
        <div class="container-custom">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="section-title">Destinos <span class="text-gradient">Destacados</span></h2>
                <p class="section-subtitle mx-auto">Los lugares más populares para tus próximas vacaciones</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @php
                    $destinos = [
                        ['nombre' => 'Playa del Carmen', 'descripcion' => 'Hermosa playa con aguas cristalinas', 'precio' => 299],
                        ['nombre' => 'Montañas de los Andes', 'descripcion' => 'Impresionantes paisajes montañosos', 'precio' => 499],
                        ['nombre' => 'Bosque de Niebla', 'descripcion' => 'Un lugar mágico lleno de biodiversidad', 'precio' => 349],
                    ];
                @endphp
                
                @foreach($destinos as $destino)
                <div class="card group" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&h=400&fit=crop" 
                             alt="{{ $destino['nombre'] }}" 
                             class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 right-4 bg-primary-600 text-white px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-star mr-1"></i> 4.8
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-display text-xl font-bold mb-2">{{ $destino['nombre'] }}</h3>
                        <p class="text-gray-600 mb-4">{{ $destino['descripcion'] }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-primary-600 font-semibold">Desde ${{ $destino['precio'] }}</span>
                            <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">
                                Ver más <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    
    <section class="py-16 bg-white">
        <div class="container-custom">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marked-alt text-3xl text-primary-600"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Destinos Únicos</h3>
                    <p class="text-gray-600">Explora los lugares más hermosos y auténticos del mundo.</p>
                </div>
                
                <div class="text-center p-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-20 h-20 bg-turquoise-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-check text-3xl text-turquoise-600"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Eventos Exclusivos</h3>
                    <p class="text-gray-600">Participa en eventos culturales y experiencias inolvidables.</p>
                </div>
                
                <div class="text-center p-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-3xl text-primary-600"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Asistencia 24/7</h3>
                    <p class="text-gray-600">Nuestro equipo está disponible para ayudarte en todo momento.</p>
                </div>
            </div>
        </div>
    </section>
    
    <section class="py-16 bg-gray-50">
        <div class="container-custom">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="section-title">Encuentra tu <span class="text-gradient">Destino</span></h2>
                <p class="section-subtitle mx-auto">Explora nuestra ubicación en el mapa</p>
            </div>
            
            <div class="relative" data-aos="fade-up" data-aos-delay="100">
                <div id="map" class="w-full h-96 rounded-2xl shadow-lg bg-gray-200 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-map text-6xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">Mapa interactivo con Leaflet</p>
                        <p class="text-gray-500 text-sm">Ubicación: -33.4489, -70.6693</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="py-16 bg-gradient-to-r from-primary-600 to-turquoise-600 text-white">
        <div class="container-custom text-center">
            <h2 class="text-3xl md:text-4xl font-display font-bold mb-4" data-aos="fade-up">
                ¿Listo para tu próxima aventura?
            </h2>
            <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                Únete a miles de viajeros que ya descubrieron nuevos horizontes con nosotros.
            </p>
            <a href="{{ route('contacto') }}" class="inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-200 transform hover:scale-105" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-paper-plane mr-2"></i> Comienza Ahora
            </a>
        </div>
    </section>
@endsection
EOF

print_message "Vistas Blade creadas"

# 9. Crear modelos
print_step "Creando modelos..."

# Modelo Destino
cat > app/Models/Destino.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Destino extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia;
    
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'precio',
        'categoria_id',
        'destacado',
        'activo',
        'orden',
    ];
    
    protected $casts = [
        'destacado' => 'boolean',
        'activo' => 'boolean',
        'precio' => 'decimal:2',
    ];
    
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('nombre')
            ->saveSlugsTo('slug');
    }
    
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
    
    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }
    
    public function noticias()
    {
        return $this->hasMany(Noticia::class);
    }
}
EOF

print_message "Modelos creados"

# 10. Crear controladores
print_step "Creando controladores..."

# HomeController
cat > app/Http/Controllers/HomeController.php << 'EOF'
<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }
}
EOF

print_message "Controladores creados"

# 11. Crear recurso de Filament
print_step "Creando recursos de Filament..."

# DestinoResource
mkdir -p app/Filament/Resources/DestinoResource/Pages
cat > app/Filament/Resources/DestinoResource.php << 'EOF'
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DestinoResource\Pages;
use App\Models\Destino;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class DestinoResource extends Resource
{
    protected static ?string $model = Destino::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Contenido';
    protected static ?int $navigationSort = 1;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Destino')
                    ->schema([
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nombre del destino'),
                        
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('url-amigable'),
                        
                        RichEditor::make('descripcion')
                            ->required()
                            ->placeholder('Descripción detallada del destino'),
                        
                        TextInput::make('precio')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('Precio desde'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Multimedia')
                    ->schema([
                        FileUpload::make('imagen_principal')
                            ->image()
                            ->directory('destinos')
                            ->visibility('public')
                            ->maxSize(5120),
                    ]),
                
                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Toggle::make('destacado')
                            ->label('Destacado en Home')
                            ->default(false),
                        
                        Toggle::make('activo')
                            ->label('Publicar')
                            ->default(true),
                        
                        TextInput::make('orden')
                            ->numeric()
                            ->default(0)
                            ->placeholder('Orden de visualización'),
                    ])->columns(3),
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen_principal')
                    ->square()
                    ->size(60)
                    ->label('Imagen'),
                
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('precio')
                    ->money('USD')
                    ->sortable(),
                
                ToggleColumn::make('destacado')
                    ->label('Destacado'),
                
                ToggleColumn::make('activo')
                    ->label('Activo'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('destacado')
                    ->label('Destacado'),
                
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDestinos::route('/'),
            'create' => Pages\CreateDestino::route('/create'),
            'edit' => Pages\EditDestino::route('/{record}/edit'),
        ];
    }
}
EOF

# Crear páginas del recurso
cat > app/Filament/Resources/DestinoResource/Pages/ListDestinos.php << 'EOF'
<?php

namespace App\Filament\Resources\DestinoResource\Pages;

use App\Filament\Resources\DestinoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDestinos extends ListRecords
{
    protected static string $resource = DestinoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
EOF

cat > app/Filament/Resources/DestinoResource/Pages/CreateDestino.php << 'EOF'
<?php

namespace App\Filament\Resources\DestinoResource\Pages;

use App\Filament\Resources\DestinoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDestino extends CreateRecord
{
    protected static string $resource = DestinoResource::class;
}
EOF

cat > app/Filament/Resources/DestinoResource/Pages/EditDestino.php << 'EOF'
<?php

namespace App\Filament\Resources\DestinoResource\Pages;

use App\Filament\Resources\DestinoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDestino extends EditRecord
{
    protected static string $resource = DestinoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
EOF

print_message "Recursos de Filament creados"

# 12. Configurar Filament
print_step "Configurando Filament..."

# AdminPanelProvider
mkdir -p app/Providers/Filament
cat > app/Providers/Filament/AdminPanelProvider.php << 'EOF'
<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->brandName('Portal Turístico')
            ->brandLogoHeight('2rem')
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Zinc,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Contenido')
                    ->icon('heroicon-o-document-text'),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Ver Sitio')
                    ->icon('heroicon-o-globe-alt')
                    ->url('/', shouldOpenInNewTab: true),
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->darkMode(false);
    }
}
EOF

print_message "Filament configurado"

# 13. Crear rutas
print_step "Configurando rutas..."

cat > routes/web.php << 'EOF'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/destinos', function () {
    return view('home');
})->name('destinos');
Route::get('/eventos', function () {
    return view('home');
})->name('eventos');
Route::get('/noticias', function () {
    return view('home');
})->name('noticias');
Route::get('/contacto', function () {
    return view('home');
})->name('contacto');
EOF

print_message "Rutas configuradas"

# 14. Crear migraciones básicas
print_step "Creando migraciones..."

php artisan make:migration create_categorias_table
php artisan make:migration create_destinos_table
php artisan make:migration create_eventos_table
php artisan make:migration create_noticias_table

print_message "Migraciones creadas"

# 15. Configurar .env
print_step "Configurando .env..."

cat > .env << 'EOF'
APP_NAME="Portal Turístico"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

VITE_APP_NAME="${APP_NAME}"
EOF

# Crear archivo de base de datos SQLite
touch database/database.sqlite

print_message ".env configurado"

# 16. Ejecutar instalación final
print_step "Ejecutando instalación final..."

# Instalar dependencias
composer install
npm install

# Generar key
php artisan key:generate

# Crear enlace de almacenamiento
php artisan storage:link

# Ejecutar migraciones
php artisan migrate

print_message "¡Instalación completada!"

# 17. Mensaje final
echo -e "\n${GREEN}================================================${NC}"
echo -e "${GREEN}✅ ¡PORTAL TURÍSTICO INSTALADO CON ÉXITO!${NC}"
echo -e "${GREEN}================================================${NC}\n"

echo -e "${YELLOW}📌 Para iniciar el servidor:${NC}"
echo -e "  cd portal-turistico"
echo -e "  php artisan serve"
echo -e "\n${YELLOW}📌 Para compilar assets:${NC}"
echo -e "  npm run dev"
echo -e "\n${YELLOW}📌 Para crear un usuario administrador:${NC}"
echo -e "  php artisan make:filament-user"
echo -e "\n${YELLOW}📌 Acceso al panel admin:${NC}"
echo -e "  http://localhost:8000/admin"
echo -e "\n${YELLOW}📌 Credenciales por defecto:${NC}"
echo -e "  Usuario: admin@example.com"
echo -e "  Contraseña: password"
echo -e "\n${GREEN}¡Disfruta tu nuevo Portal Turístico! 🚀${NC}\n"