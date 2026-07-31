#!/bin/bash

# ============================================
# SCRIPT COMPLETO - PORTAL TURÍSTICO
# Con todas las maquetas y vistas
# ============================================

echo "🚀 Iniciando instalación del Portal Turístico..."
echo "================================================"

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_message() { echo -e "${GREEN}✓${NC} $1"; }
print_error() { echo -e "${RED}✗${NC} $1"; }
print_step() { echo -e "\n${BLUE}▶${NC} $1"; }

# Verificar dependencias
if ! command -v composer &> /dev/null; then
    print_error "Composer no está instalado"
    exit 1
fi

if ! command -v npm &> /dev/null; then
    print_error "npm no está instalado"
    exit 1
fi

if ! command -v php &> /dev/null; then
    print_error "PHP no está instalado"
    exit 1
fi

# Crear proyecto
print_step "Creando proyecto Laravel..."
if [ -d "portal-turistico" ]; then
    print_error "El directorio portal-turistico ya existe. ¿Eliminar? (y/n)"
    read -r response
    if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
        rm -rf portal-turistico
    else
        exit 1
    fi
fi

composer create-project laravel/laravel portal-turistico
cd portal-turistico

# Instalar dependencias
print_step "Instalando dependencias..."
composer require filament/filament:"^3.0" livewire/livewire:"^3.0"
composer require laravel/socialite
composer require spatie/laravel-medialibrary
composer require spatie/laravel-sluggable

npm install -D tailwindcss postcss autoprefixer
npm install alpinejs swiper gsap aos leaflet @fortawesome/fontawesome-free
npm install -D @tailwindcss/forms @tailwindcss/typography @tailwindcss/aspect-ratio

print_message "Dependencias instaladas"

# Crear estructura
print_step "Creando estructura de directorios..."
mkdir -p app/Filament/Resources/DestinoResource/Pages
mkdir -p app/Filament/Resources/EventoResource/Pages
mkdir -p app/Filament/Resources/NoticiaResource/Pages
mkdir -p app/Http/Controllers
mkdir -p app/Models
mkdir -p resources/views/partials
mkdir -p resources/views/layouts
mkdir -p resources/views/admin
mkdir -p resources/css
mkdir -p resources/js
mkdir -p public/images
mkdir -p database/migrations

print_message "Estructura creada"

# ============================================
# 1. ARCHIVOS DE CONFIGURACIÓN
# ============================================

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

print_message "Configuración creada"

# ============================================
# 2. CSS COMPLETO
# ============================================

print_step "Creando CSS completo..."

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
    
    .btn-outline {
        @apply inline-flex items-center px-6 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-primary-600 transition-all duration-200;
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
    
    .badge {
        @apply inline-flex items-center px-3 py-1 rounded-full text-sm font-medium;
    }
}

@layer utilities {
    .text-gradient {
        @apply bg-gradient-to-r from-primary-600 to-turquoise-500 bg-clip-text text-transparent;
    }
    
    .animation-delay-100 {
        animation-delay: 100ms;
    }
    .animation-delay-200 {
        animation-delay: 200ms;
    }
    .animation-delay-300 {
        animation-delay: 300ms;
    }
    
    .bg-pattern {
        background-image: radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.1) 0%, transparent 50%);
    }
}
EOF

print_message "CSS creado"

# ============================================
# 3. JAVASCRIPT COMPLETO
# ============================================

print_step "Creando JavaScript completo..."

cat > resources/js/app.js << 'EOF'
import './bootstrap';
import Alpine from 'alpinejs';
import Swiper from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';
import AOS from 'aos';
import 'aos/dist/aos.css';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

// Alpine.js
window.Alpine = Alpine;
Alpine.start();

// AOS
AOS.init({
    duration: 1000,
    once: true,
    offset: 100,
    easing: 'ease-out-cubic',
});

// GSAP
gsap.registerPlugin(ScrollTrigger);

// Swiper - Destinos
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
            dynamicBullets: true,
        },
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        effect: 'slide',
        loop: true,
        speed: 800,
        ...options
    });
};

// Leaflet Map
window.initializeMap = (elementId, coordinates, options = {}) => {
    const map = L.map(elementId).setView(coordinates, options.zoom || 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: '<i class="fas fa-map-marker-alt" style="color: #2563eb; font-size: 40px;"></i>',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
    });
    
    L.marker(coordinates, { icon: customIcon })
        .addTo(map)
        .bindPopup(options.popup || '📍 Nuestra ubicación')
        .openPopup();
    
    return map;
};

// Animaciones al cargar
document.addEventListener('DOMContentLoaded', () => {
    // Hero animations
    gsap.from('.hero-title', {
        duration: 1.2,
        y: 60,
        opacity: 0,
        ease: 'power3.out',
        scrollTrigger: {
            trigger: '.hero-title',
            start: 'top 80%',
        }
    });
    
    gsap.from('.hero-subtitle', {
        duration: 1.2,
        y: 40,
        opacity: 0,
        delay: 0.3,
        ease: 'power3.out',
        scrollTrigger: {
            trigger: '.hero-subtitle',
            start: 'top 80%',
        }
    });
    
    gsap.from('.hero-buttons', {
        duration: 1.2,
        y: 40,
        opacity: 0,
        delay: 0.6,
        ease: 'power3.out',
        scrollTrigger: {
            trigger: '.hero-buttons',
            start: 'top 80%',
        }
    });
    
    // Cards animation
    gsap.utils.toArray('.card-animate').forEach((card, i) => {
        gsap.from(card, {
            scrollTrigger: {
                trigger: card,
                start: 'top 85%',
                toggleActions: 'play none none reverse',
            },
            duration: 0.8,
            y: 60,
            opacity: 0,
            delay: i * 0.15,
            ease: 'power3.out'
        });
    });
    
    // Stats animation
    gsap.utils.toArray('.stat-item').forEach((stat, i) => {
        gsap.from(stat, {
            scrollTrigger: {
                trigger: stat,
                start: 'top 85%',
            },
            duration: 0.8,
            scale: 0.8,
            opacity: 0,
            delay: i * 0.1,
            ease: 'back.out(1.7)'
        });
    });
});

// Alpine stores
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
    
    Alpine.store('theme', {
        dark: false,
        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark');
        }
    });
    
    Alpine.store('modal', {
        isOpen: false,
        open() { this.isOpen = true; },
        close() { this.isOpen = false; }
    });
});

// Scroll to top button
window.addEventListener('scroll', () => {
    const btn = document.getElementById('scroll-top');
    if (btn) {
        if (window.scrollY > 500) {
            btn.classList.remove('opacity-0', 'invisible');
            btn.classList.add('opacity-100', 'visible');
        } else {
            btn.classList.add('opacity-0', 'invisible');
            btn.classList.remove('opacity-100', 'visible');
        }
    }
});

// Counter animation
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
            start = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(start).toLocaleString();
    }, 16);
}
EOF

print_message "JavaScript creado"

# ============================================
# 4. LAYOUT PRINCIPAL (COMPLETO)
# ============================================

print_step "Creando Layout principal..."

cat > resources/views/layouts/app.blade.php << 'EOF'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Portal Turístico'))</title>
    <meta name="description" content="@yield('description', 'Descubre los mejores destinos turísticos')">
    <meta name="keywords" content="turismo, viajes, destinos, vacaciones, aventura">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', config('app.name', 'Portal Turístico'))">
    <meta property="og:description" content="@yield('description', 'Descubre los mejores destinos turísticos')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
</head>
<body>
    <div id="app">
        @include('partials.navbar')
        
        <main>
            @yield('content')
        </main>
        
        @include('partials.footer')
        
        <!-- Scroll to top button -->
        <button id="scroll-top" 
                onclick="window.scrollTo({top:0, behavior:'smooth'})"
                class="fixed bottom-8 right-8 bg-primary-600 text-white p-4 rounded-full shadow-lg hover:bg-primary-700 transition-all duration-300 opacity-0 invisible z-50">
            <i class="fas fa-arrow-up"></i>
        </button>
        
        @stack('scripts')
    </div>
</body>
</html>
EOF

print_message "Layout creado"

# ============================================
# 5. NAVBAR COMPLETO
# ============================================

print_step "Creando Navbar..."

cat > resources/views/partials/navbar.blade.php << 'EOF'
<nav class="bg-white/90 backdrop-blur-md shadow-lg fixed w-full z-50 transition-all duration-300" 
     x-data="{ isOpen: false, scrolled: false }"
     x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
     :class="scrolled ? 'bg-white/95 shadow-xl' : 'bg-white/90'">
    <div class="container-custom">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                <div class="relative">
                    <i class="fas fa-umbrella-beach text-3xl text-primary-600 group-hover:scale-110 transition-transform duration-300"></i>
                    <i class="fas fa-circle text-primary-400 text-xs absolute -top-1 -right-1 animate-pulse"></i>
                </div>
                <span class="text-2xl font-display font-bold text-gradient group-hover:scale-105 transition-transform duration-300">Turismo</span>
            </a>
            
            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center space-x-1">
                <a href="{{ route('home') }}" class="nav-link px-4 py-2 rounded-lg hover:bg-gray-50 {{ request()->routeIs('home') ? 'text-primary-600 bg-primary-50' : '' }}">
                    <i class="fas fa-home mr-2"></i>Inicio
                </a>
                <a href="{{ route('destinos') }}" class="nav-link px-4 py-2 rounded-lg hover:bg-gray-50 {{ request()->routeIs('destinos*') ? 'text-primary-600 bg-primary-50' : '' }}">
                    <i class="fas fa-map-pin mr-2"></i>Destinos
                </a>
                <a href="{{ route('eventos') }}" class="nav-link px-4 py-2 rounded-lg hover:bg-gray-50 {{ request()->routeIs('eventos*') ? 'text-primary-600 bg-primary-50' : '' }}">
                    <i class="fas fa-calendar-alt mr-2"></i>Eventos
                </a>
                <a href="{{ route('noticias') }}" class="nav-link px-4 py-2 rounded-lg hover:bg-gray-50 {{ request()->routeIs('noticias*') ? 'text-primary-600 bg-primary-50' : '' }}">
                    <i class="fas fa-newspaper mr-2"></i>Noticias
                </a>
                <a href="{{ route('contacto') }}" class="nav-link px-4 py-2 rounded-lg hover:bg-gray-50 {{ request()->routeIs('contacto') ? 'text-primary-600 bg-primary-50' : '' }}">
                    <i class="fas fa-envelope mr-2"></i>Contacto
                </a>
                <div class="w-px h-8 bg-gray-200 mx-2"></div>
                <a href="{{ route('filament.admin.auth.login') }}" class="btn-primary text-sm py-2 px-5">
                    <i class="fas fa-user-shield mr-2"></i> Admin
                </a>
            </div>
            
            <!-- Mobile Menu Button -->
            <button @click="isOpen = !isOpen" 
                    class="lg:hidden text-gray-600 hover:text-primary-600 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <!-- Mobile Menu -->
        <div x-show="isOpen" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 transform -translate-y-2" 
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="lg:hidden pb-4">
            <div class="flex flex-col space-y-2 bg-white rounded-xl shadow-lg p-4">
                <a href="{{ route('home') }}" class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-50 {{ request()->routeIs('home') ? 'text-primary-600 bg-primary-50' : '' }}">
                    <i class="fas fa-home mr-3 w-5 text-primary-500"></i>Inicio
                </a>
                <a href="{{ route('destinos') }}" class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-50 {{ request()->routeIs('destinos*') ? 'text-primary-600 bg-primary-50' : '' }}">
                    <i class="fas fa-map-pin mr-3 w-5 text-primary-500"></i>Destinos
                </a>
                <a href="{{ route('eventos') }}" class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-50 {{ request()->routeIs('eventos*') ? 'text-primary-600 bg-primary-50' : '' }}">
                    <i class="fas fa-calendar-alt mr-3 w-5 text-primary-500"></i>Eventos
                </a>
                <a href="{{ route('noticias') }}" class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-50 {{ request()->routeIs('noticias*') ? 'text-primary-600 bg-primary-50' : '' }}">
                    <i class="fas fa-newspaper mr-3 w-5 text-primary-500"></i>Noticias
                </a>
                <a href="{{ route('contacto') }}" class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-50 {{ request()->routeIs('contacto') ? 'text-primary-600 bg-primary-50' : '' }}">
                    <i class="fas fa-envelope mr-3 w-5 text-primary-500"></i>Contacto
                </a>
                <div class="border-t border-gray-200 my-2"></div>
                <a href="{{ route('filament.admin.auth.login') }}" class="btn-primary text-sm py-3 justify-center">
                    <i class="fas fa-user-shield mr-2"></i> Panel Admin
                </a>
            </div>
        </div>
    </div>
</nav>
EOF

print_message "Navbar creado"

# ============================================
# 6. FOOTER COMPLETO
# ============================================

print_step "Creando Footer..."

cat > resources/views/partials/footer.blade.php << 'EOF'
<footer class="bg-gray-900 text-white mt-20">
    <!-- Newsletter Bar -->
    <div class="bg-gradient-to-r from-primary-600 to-turquoise-600 py-10">
        <div class="container-custom">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-2xl font-display font-bold">¿Listo para viajar?</h3>
                    <p class="text-white/80">Suscríbete y recibe las mejores ofertas</p>
                </div>
                <form class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <input type="email" 
                           placeholder="Tu correo electrónico" 
                           class="px-5 py-3 rounded-lg bg-white/20 backdrop-blur-sm border border-white/30 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 w-full sm:w-72">
                    <button type="submit" class="bg-white text-primary-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-paper-plane mr-2"></i>Suscribirse
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="container-custom py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            <!-- About -->
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <i class="fas fa-umbrella-beach text-3xl text-primary-500"></i>
                    <span class="text-2xl font-display font-bold text-gradient">Turismo</span>
                </div>
                <p class="text-gray-400 mb-6 leading-relaxed">Descubre los mejores destinos y experiencias turísticas con nosotros. Tu próxima aventura comienza aquí.</p>
                <div class="flex space-x-4">
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition-colors duration-300">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition-colors duration-300">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition-colors duration-300">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition-colors duration-300">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition-colors duration-300">
                        <i class="fab fa-tiktok"></i>
                    </a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 class="font-display text-lg font-semibold mb-6">Enlaces Rápidos</h4>
                <ul class="space-y-3">
                    <li><a href="#" class="text-gray-400 hover:text-primary-500 transition-colors flex items-center"><i class="fas fa-chevron-right text-primary-500 mr-2 text-xs"></i>Destinos</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-primary-500 transition-colors flex items-center"><i class="fas fa-chevron-right text-primary-500 mr-2 text-xs"></i>Eventos</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-primary-500 transition-colors flex items-center"><i class="fas fa-chevron-right text-primary-500 mr-2 text-xs"></i>Noticias</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-primary-500 transition-colors flex items-center"><i class="fas fa-chevron-right text-primary-500 mr-2 text-xs"></i>Galería</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-primary-500 transition-colors flex items-center"><i class="fas fa-chevron-right text-primary-500 mr-2 text-xs"></i>Contacto</a></li>
                </ul>
            </div>
            
            <!-- Contact -->
            <div>
                <h4 class="font-display text-lg font-semibold mb-6">Información de Contacto</h4>
                <ul class="space-y-4">
                    <li class="flex items-start space-x-3">
                        <i class="fas fa-map-marker-alt text-primary-500 mt-1"></i>
                        <span class="text-gray-400">Av. Principal 123, Ciudad Turística</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <i class="fas fa-phone text-primary-500 mt-1"></i>
                        <span class="text-gray-400">+123 456 7890</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <i class="fas fa-envelope text-primary-500 mt-1"></i>
                        <span class="text-gray-400">info@turismo.com</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <i class="fas fa-clock text-primary-500 mt-1"></i>
                        <span class="text-gray-400">Lun - Vie: 9:00 - 18:00</span>
                    </li>
                </ul>
            </div>
            
            <!-- Map -->
            <div>
                <h4 class="font-display text-lg font-semibold mb-6">Ubicación</h4>
                <div class="bg-gray-800 rounded-xl overflow-hidden h-48">
                    <div id="footer-map" class="w-full h-full bg-gray-700 flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-map text-4xl text-gray-500 mb-2"></i>
                            <p class="text-gray-400 text-sm">Mapa interactivo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom -->
        <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} Turismo. Todos los derechos reservados.</p>
            <div class="flex space-x-6 text-sm">
                <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors">Política de Privacidad</a>
                <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors">Términos de Uso</a>
                <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors">Cookies</a>
            </div>
        </div>
    </div>
</footer>
EOF

print_message "Footer creado"

# ============================================
# 7. HOME COMPLETO CON MAQUETA VISUAL
# ============================================

print_step "Creando Home completo..."

cat > resources/views/home.blade.php << 'EOF'
@extends('layouts.app')

@section('title', 'Inicio - Portal Turístico')
@section('description', 'Descubre los mejores destinos turísticos para tus próximas vacaciones')

@section('content')
<!-- ============================================
     HERO SECTION
     ============================================ -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Background Video/Image -->
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-black/40 z-10"></div>
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" 
             alt="Paisaje turístico" 
             class="w-full h-full object-cover">
    </div>
    
    <!-- Content -->
    <div class="relative z-20 container-custom text-center text-white">
        <div class="max-w-4xl mx-auto">
            <!-- Badge -->
            <div class="inline-flex items-center bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-6" data-aos="fade-down">
                <span class="animate-pulse mr-2">🌍</span>
                <span class="text-sm font-medium">Descubre el mundo con nosotros</span>
            </div>
            
            <h1 class="hero-title text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-display font-bold leading-tight mb-6">
                Explora <span class="text-gradient">Destinos</span><br>
                <span class="text-white">Inolvidables</span>
            </h1>
            
            <p class="hero-subtitle text-lg sm:text-xl md:text-2xl text-gray-200 max-w-2xl mx-auto mb-10 leading-relaxed">
                Encuentra los mejores destinos turísticos, eventos culturales y experiencias únicas para tus próximas aventuras.
            </p>
            
            <div class="hero-buttons flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('destinos') }}" class="btn-primary text-lg px-8 py-4 group">
                    <i class="fas fa-search mr-2 group-hover:animate-pulse"></i> Explorar Destinos
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="#destacados" class="btn-outline text-lg px-8 py-4">
                    <i class="fas fa-play-circle mr-2"></i> Ver Más
                </a>
            </div>
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 animate-bounce">
        <div class="w-8 h-12 border-2 border-white/50 rounded-full flex justify-center">
            <div class="w-1.5 h-3 bg-white rounded-full mt-2 animate-pulse"></div>
        </div>
    </div>
</section>

<!-- ============================================
     DESTINOS DESTACADOS
     ============================================ -->
<section id="destacados" class="py-20 bg-gray-50">
    <div class="container-custom">
        <!-- Header -->
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block text-primary-600 font-semibold text-sm uppercase tracking-wider mb-2">Explora</span>
            <h2 class="section-title">Destinos <span class="text-gradient">Destacados</span></h2>
            <p class="section-subtitle mx-auto">Los lugares más populares para tus próximas vacaciones</p>
        </div>
        
        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @php
                $destinos = [
                    [
                        'nombre' => 'Playa del Carmen',
                        'descripcion' => 'Hermosa playa con aguas cristalinas y arena blanca perfecta para relajarse.',
                        'precio' => 299,
                        'rating' => 4.8,
                        'img' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&h=400&fit=crop',
                        'categoria' => 'Playas'
                    ],
                    [
                        'nombre' => 'Montañas de los Andes',
                        'descripcion' => 'Impresionantes paisajes montañosos ideales para aventureros y amantes de la naturaleza.',
                        'precio' => 499,
                        'rating' => 4.9,
                        'img' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=600&h=400&fit=crop',
                        'categoria' => 'Montaña'
                    ],
                    [
                        'nombre' => 'Bosque de Niebla',
                        'descripcion' => 'Un lugar mágico lleno de biodiversidad y paisajes de ensueño.',
                        'precio' => 349,
                        'rating' => 4.7,
                        'img' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=600&h=400&fit=crop',
                        'categoria' => 'Naturaleza'
                    ],
                ];
            @endphp
            
            @foreach($destinos as $destino)
            <div class="card-animate group" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                <div class="card">
                    <div class="relative overflow-hidden">
                        <img src="{{ $destino['img'] }}" 
                             alt="{{ $destino['nombre'] }}" 
                             class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700 ease-out">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        
                        <!-- Badges -->
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            <span class="badge bg-primary-600 text-white">
                                <i class="fas fa-tag mr-1"></i> {{ $destino['categoria'] }}
                            </span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="badge bg-white/90 backdrop-blur-sm text-gray-800">
                                <i class="fas fa-star text-yellow-400 mr-1"></i> {{ $destino['rating'] }}
                            </span>
                        </div>
                        
                        <!-- Price Tag -->
                        <div class="absolute bottom-4 left-4">
                            <div class="bg-white/90 backdrop-blur-sm px-4 py-2 rounded-lg">
                                <span class="text-sm text-gray-500">Desde</span>
                                <span class="text-xl font-bold text-primary-600">${{ $destino['precio'] }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="font-display text-xl font-bold mb-2 group-hover:text-primary-600 transition-colors">
                            {{ $destino['nombre'] }}
                        </h3>
                        <p class="text-gray-600 mb-4 text-sm leading-relaxed">{{ $destino['descripcion'] }}</p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt text-primary-500 mr-1"></i>
                                <span>México</span>
                            </div>
                            <a href="#" class="text-primary-600 hover:text-primary-700 font-medium group-hover:translate-x-1 transition-transform inline-flex items-center">
                                Ver más <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Ver Todos -->
        <div class="text-center mt-12" data-aos="fade-up">
            <a href="{{ route('destinos') }}" class="btn-primary">
                Ver Todos los Destinos
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- ============================================
     ESTADÍSTICAS
     ============================================ -->
<section class="py-16 bg-gradient-to-r from-primary-600 to-turquoise-600 text-white">
    <div class="container-custom">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="stat-item text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="text-4xl font-display font-bold mb-2" x-data="{ count: 0 }" x-init="animateCounter($el, 1500, 2000)">0</div>
                <p class="text-white/80">Destinos Disponibles</p>
            </div>
            <div class="stat-item text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="text-4xl font-display font-bold mb-2" x-data="{ count: 0 }" x-init="animateCounter($el, 500, 2000)">0</div>
                <p class="text-white/80">Eventos Realizados</p>
            </div>
            <div class="stat-item text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="text-4xl font-display font-bold mb-2" x-data="{ count: 0 }" x-init="animateCounter($el, 25000, 2000)">0</div>
                <p class="text-white/80">Viajeros Felices</p>
            </div>
            <div class="stat-item text-center" data-aos="fade-up" data-aos-delay="400">
                <div class="text-4xl font-display font-bold mb-2" x-data="{ count: 0 }" x-init="animateCounter($el, 98, 2000)">0</div>
                <p class="text-white/80">% Satisfacción</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     CARACTERÍSTICAS
     ============================================ -->
<section class="py-20 bg-white">
    <div class="container-custom">
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block text-primary-600 font-semibold text-sm uppercase tracking-wider mb-2">Ventajas</span>
            <h2 class="section-title">¿Por qué <span class="text-gradient">viajar con nosotros</span>?</h2>
            <p class="section-subtitle mx-auto">Ofrecemos experiencias únicas y servicios de calidad para tus viajes</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-8 rounded-2xl hover:shadow-xl transition-shadow duration-300" data-aos="fade-up" data-aos-delay="100">
                <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-map-marked-alt text-3xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Destinos Únicos</h3>
                <p class="text-gray-600 leading-relaxed">Explora los lugares más hermosos y auténticos del mundo con nuestras guías expertas.</p>
            </div>
            
            <div class="text-center p-8 rounded-2xl hover:shadow-xl transition-shadow duration-300" data-aos="fade-up" data-aos-delay="200">
                <div class="w-20 h-20 bg-turquoise-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-calendar-check text-3xl text-turquoise-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Eventos Exclusivos</h3>
                <p class="text-gray-600 leading-relaxed">Participa en eventos culturales únicos y experiencias inolvidables.</p>
            </div>
            
            <div class="text-center p-8 rounded-2xl hover:shadow-xl transition-shadow duration-300" data-aos="fade-up" data-aos-delay="300">
                <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-headset text-3xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Asistencia 24/7</h3>
                <p class="text-gray-600 leading-relaxed">Nuestro equipo está disponible para ayudarte en todo momento, donde quiera que estés.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     MAPA INTERACTIVO
     ============================================ -->
<section class="py-20 bg-gray-50">
    <div class="container-custom">
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block text-primary-600 font-semibold text-sm uppercase tracking-wider mb-2">Ubicación</span>
            <h2 class="section-title">Encuentra tu <span class="text-gradient">Destino</span></h2>
            <p class="section-subtitle mx-auto">Explora nuestra ubicación y descubre los mejores lugares</p>
        </div>
        
        <div class="relative" data-aos="fade-up" data-aos-delay="100">
            <div id="map" class="w-full h-[500px] rounded-2xl shadow-xl overflow-hidden bg-gray-200">
                <div class="w-full h-full flex items-center justify-center flex-col bg-gradient-to-br from-gray-100 to-gray-200">
                    <i class="fas fa-map text-6xl text-primary-400 mb-4"></i>
                    <p class="text-gray-600 font-semibold text-lg">Mapa interactivo con Leaflet</p>
                    <p class="text-gray-500 text-sm">Ubicación: -33.4489, -70.6693 (Santiago, Chile)</p>
                    <div class="flex gap-4 mt-4">
                        <span class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm"><i class="fas fa-plus mr-1"></i>Zoom +</span>
                        <span class="bg-gray-600 text-white px-4 py-2 rounded-lg text-sm"><i class="fas fa-minus mr-1"></i>Zoom -</span>
                    </div>
                </div>
            </div>
            
            <!-- Info Card -->
            <div class="absolute bottom-4 left-4 bg-white rounded-xl shadow-lg p-4 max-w-xs">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-location-dot text-primary-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-sm">Nuestra Oficina</p>
                        <p class="text-xs text-gray-500">Av. Principal 123, Ciudad</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     EVENTOS PRÓXIMOS
     ============================================ -->
<section class="py-20 bg-white">
    <div class="container-custom">
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block text-primary-600 font-semibold text-sm uppercase tracking-wider mb-2">Agenda</span>
            <h2 class="section-title">Eventos <span class="text-gradient">Próximos</span></h2>
            <p class="section-subtitle mx-auto">No te pierdas nuestros eventos culturales y experiencias únicas</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-start space-x-4 p-4 rounded-xl hover:bg-gray-50 transition-colors" data-aos="fade-up" data-aos-delay="100">
                <div class="flex-shrink-0 w-16 h-16 bg-primary-100 rounded-xl flex flex-col items-center justify-center">
                    <span class="text-xl font-bold text-primary-600">25</span>
                    <span class="text-xs text-primary-500">DIC</span>
                </div>
                <div>
                    <h4 class="font-semibold">Festival de Verano</h4>
                    <p class="text-sm text-gray-500">Música y arte en la playa</p>
                    <span class="text-xs text-primary-600">📍 Playa del Carmen</span>
                </div>
            </div>
            
            <div class="flex items-start space-x-4 p-4 rounded-xl hover:bg-gray-50 transition-colors" data-aos="fade-up" data-aos-delay="200">
                <div class="flex-shrink-0 w-16 h-16 bg-turquoise-100 rounded-xl flex flex-col items-center justify-center">
                    <span class="text-xl font-bold text-turquoise-600">15</span>
                    <span class="text-xs text-turquoise-500">ENE</span>
                </div>
                <div>
                    <h4 class="font-semibold">Exposición de Arte</h4>
                    <p class="text-sm text-gray-500">Arte contemporáneo local</p>
                    <span class="text-xs text-turquoise-600">📍 Galería Central</span>
                </div>
            </div>
            
            <div class="flex items-start space-x-4 p-4 rounded-xl hover:bg-gray-50 transition-colors" data-aos="fade-up" data-aos-delay="300">
                <div class="flex-shrink-0 w-16 h-16 bg-primary-100 rounded-xl flex flex-col items-center justify-center">
                    <span class="text-xl font-bold text-primary-600">5</span>
                    <span class="text-xs text-primary-500">FEB</span>
                </div>
                <div>
                    <h4 class="font-semibold">Carrera de Montaña</h4>
                    <p class="text-sm text-gray-500">Desafío deportivo en la montaña</p>
                    <span class="text-xs text-primary-600">📍 Parque Nacional</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     CALL TO ACTION
     ============================================ -->
<section class="py-20 bg-gradient-to-r from-primary-600 to-turquoise-600 text-white relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
    </div>
    
    <div class="container-custom text-center relative z-10">
        <h2 class="text-3xl md:text-5xl font-display font-bold mb-6" data-aos="fade-up">
            ¿Listo para tu <span class="text-white/90">próxima aventura</span>?
        </h2>
        <p class="text-xl text-white/90 mb-10 max-w-2xl mx-auto leading-relaxed" data-aos="fade-up" data-aos-delay="100">
            Únete a miles de viajeros que ya descubrieron nuevos horizontes con nosotros. 
            Tu próximo destino te espera.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4" data-aos="fade-up" data-aos-delay="200">
            <a href="{{ route('contacto') }}" class="inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-200 transform hover:scale-105 shadow-lg">
                <i class="fas fa-paper-plane mr-3"></i> Comienza Ahora
            </a>
            <a href="#" class="inline-flex items-center px-8 py-4 bg-white/20 backdrop-blur-sm text-white font-semibold rounded-lg hover:bg-white/30 transition-all duration-200 border border-white/30">
                <i class="fas fa-play mr-3"></i> Ver Video
            </a>
        </div>
    </div>
</section>

<!-- ============================================
     BLOG / NOTICIAS
     ============================================ -->
<section class="py-20 bg-gray-50">
    <div class="container-custom">
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-block text-primary-600 font-semibold text-sm uppercase tracking-wider mb-2">Blog</span>
            <h2 class="section-title">Últimas <span class="text-gradient">Noticias</span></h2>
            <p class="section-subtitle mx-auto">Mantente informado sobre las últimas novedades y consejos para viajar</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php
                $noticias = [
                    [
                        'titulo' => 'Nuevos destinos turísticos para 2027',
                        'descripcion' => 'Descubre los lugares más prometedores para viajar el próximo año.',
                        'fecha' => '25 Dic 2026',
                        'img' => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=600&h=300&fit=crop'
                    ],
                    [
                        'titulo' => 'Eventos culturales imperdibles',
                        'descripcion' => 'Los mejores festivales y eventos culturales del año.',
                        'fecha' => '20 Dic 2026',
                        'img' => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=600&h=300&fit=crop'
                    ],
                    [
                        'titulo' => 'Consejos para viajar seguro',
                        'descripcion' => 'Guía completa para disfrutar de tus viajes con total seguridad.',
                        'fecha' => '15 Dic 2026',
                        'img' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=600&h=300&fit=crop'
                    ],
                ];
            @endphp
            
            @foreach($noticias as $noticia)
            <div class="card-animate bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                <div class="relative overflow-hidden">
                    <img src="{{ $noticia['img'] }}" alt="{{ $noticia['titulo'] }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-3 left-3">
                        <span class="badge bg-primary-600 text-white">
                            <i class="fas fa-newspaper mr-1"></i> Novedad
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <i class="fas fa-calendar-alt text-primary-500 mr-2"></i>
                        <span>{{ $noticia['fecha'] }}</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2 hover:text-primary-600 transition-colors">
                        <a href="#">{{ $noticia['titulo'] }}</a>
                    </h3>
                    <p class="text-gray-600 text-sm mb-4">{{ $noticia['descripcion'] }}</p>
                    <a href="#" class="text-primary-600 font-medium hover:text-primary-700 inline-flex items-center group">
                        Leer más 
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar mapa si existe
    const mapElement = document.getElementById('map');
    if (mapElement && typeof window.initializeMap === 'function') {
        try {
            window.initializeMap('map', [-33.4489, -70.6693], {
                zoom: 13,
                popup: '📍 Nuestra Oficina Principal'
            });
        } catch(e) {
            console.log('Mapa no inicializado:', e);
        }
    }
    
    // Inicializar footer map
    const footerMap = document.getElementById('footer-map');
    if (footerMap && typeof window.initializeMap === 'function') {
        try {
            window.initializeMap('footer-map', [-33.4489, -70.6693], {
                zoom: 13,
                popup: '📍 Nuestra Oficina'
            });
        } catch(e) {
            console.log('Footer map no inicializado:', e);
        }
    }
});
</script>
@endpush
EOF

print_message "Home completo creado"

# ============================================
# 8. MODELOS
# ============================================

print_step "Creando modelos..."

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

cat > app/Models/Categoria.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'icono',
    ];
    
    public function destinos()
    {
        return $this->hasMany(Destino::class);
    }
}
EOF

cat > app/Models/Evento.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Evento extends Model implements HasMedia
{
    use InteractsWithMedia;
    
    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'ubicacion',
        'destino_id',
        'activo',
    ];
    
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'activo' => 'boolean',
    ];
    
    public function destino()
    {
        return $this->belongsTo(Destino::class);
    }
}
EOF

cat > app/Models/Noticia.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Noticia extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia;
    
    protected $fillable = [
        'titulo',
        'slug',
        'contenido',
        'resumen',
        'fecha_publicacion',
        'destino_id',
        'activo',
    ];
    
    protected $casts = [
        'fecha_publicacion' => 'datetime',
        'activo' => 'boolean',
    ];
    
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('titulo')
            ->saveSlugsTo('slug');
    }
    
    public function destino()
    {
        return $this->belongsTo(Destino::class);
    }
}
EOF

print_message "Modelos creados"

# ============================================
# 9. CONTROLADORES
# ============================================

print_step "Creando controladores..."

cat > app/Http/Controllers/HomeController.php << 'EOF'
<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use App\Models\Evento;
use App\Models\Noticia;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $destinos = Destino::where('activo', true)
            ->where('destacado', true)
            ->limit(6)
            ->get();
            
        $eventos = Evento::where('activo', true)
            ->where('fecha_inicio', '>=', now())
            ->limit(4)
            ->get();
            
        $noticias = Noticia::where('activo', true)
            ->limit(3)
            ->get();
            
        return view('home', compact('destinos', 'eventos', 'noticias'));
    }
}
EOF

cat > app/Http/Controllers/DestinoController.php << 'EOF'
<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use Illuminate\Http\Request;

class DestinoController extends Controller
{
    public function index()
    {
        $destinos = Destino::where('activo', true)
            ->orderBy('orden')
            ->paginate(12);
            
        return view('destinos.index', compact('destinos'));
    }
    
    public function show(Destino $destino)
    {
        return view('destinos.show', compact('destino'));
    }
}
EOF

cat > app/Http/Controllers/EventoController.php << 'EOF'
<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::where('activo', true)
            ->where('fecha_inicio', '>=', now())
            ->orderBy('fecha_inicio')
            ->paginate(9);
            
        return view('eventos.index', compact('eventos'));
    }
    
    public function show(Evento $evento)
    {
        return view('eventos.show', compact('evento'));
    }
}
EOF

cat > app/Http/Controllers/NoticiaController.php << 'EOF'
<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Http\Request;

class NoticiaController extends Controller
{
    public function index()
    {
        $noticias = Noticia::where('activo', true)
            ->orderBy('fecha_publicacion', 'desc')
            ->paginate(9);
            
        return view('noticias.index', compact('noticias'));
    }
    
    public function show(Noticia $noticia)
    {
        return view('noticias.show', compact('noticia'));
    }
}
EOF

cat > app/Http/Controllers/ContactoController.php << 'EOF'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactoController extends Controller
{
    public function index()
    {
        return view('contacto.index');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'mensaje' => 'required|string',
        ]);
        
        // Aquí se enviaría el email o se guardaría en la base de datos
        
        return redirect()->route('contacto')
            ->with('success', '¡Mensaje enviado con éxito! Te contactaremos pronto.');
    }
}
EOF

print_message "Controladores creados"

# ============================================
# 10. RECURSOS DE FILAMENT
# ============================================

print_step "Creando recursos de Filament..."

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
                        
                        Select::make('categoria_id')
                            ->relationship('categoria', 'nombre')
                            ->placeholder('Selecciona una categoría'),
                        
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
                            ->maxSize(5120)
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9'),
                        
                        FileUpload::make('galeria')
                            ->image()
                            ->multiple()
                            ->directory('destinos/galeria')
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
                
                TextColumn::make('categoria.nombre')
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('precio')
                    ->money('USD')
                    ->sortable(),
                
                ToggleColumn::make('destacado')
                    ->label('Destacado'),
                
                ToggleColumn::make('activo')
                    ->label('Activo'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->relationship('categoria', 'nombre'),
                
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

# Páginas del recurso
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

# ============================================
# 11. CONFIGURACIÓN DE FILAMENT
# ============================================

print_step "Configurando Filament..."

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
            ->brandLogo(asset('images/logo-white.svg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('images/favicon.ico'))
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Zinc,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Contenido')
                    ->icon('heroicon-o-document-text'),
                NavigationGroup::make()
                    ->label('Usuarios')
                    ->icon('heroicon-o-users'),
                NavigationGroup::make()
                    ->label('Sistema')
                    ->icon('heroicon-o-cog-6-tooth'),
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

# ============================================
# 12. RUTAS
# ============================================

print_step "Configurando rutas..."

cat > routes/web.php << 'EOF'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DestinoController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\ContactoController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/destinos', [DestinoController::class, 'index'])->name('destinos');
Route::get('/destinos/{destino}', [DestinoController::class, 'show'])->name('destinos.show');
Route::get('/eventos', [EventoController::class, 'index'])->name('eventos');
Route::get('/eventos/{evento}', [EventoController::class, 'show'])->name('eventos.show');
Route::get('/noticias', [NoticiaController::class, 'index'])->name('noticias');
Route::get('/noticias/{noticia}', [NoticiaController::class, 'show'])->name('noticias.show');
Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');
EOF

print_message "Rutas configuradas"

# ============================================
# 13. MIGRACIONES
# ============================================

print_step "Creando migraciones..."

cat > database/migrations/2024_01_01_000001_create_categorias_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->string('icono')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
EOF

cat > database/migrations/2024_01_01_000002_create_destinos_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion');
            $table->decimal('precio', 10, 2)->nullable();
            $table->foreignId('categoria_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('destacado')->default(false);
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinos');
    }
};
EOF

cat > database/migrations/2024_01_01_000003_create_eventos_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('descripcion');
            $table->string('ubicacion');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->foreignId('destino_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
EOF

cat > database/migrations/2024_01_01_000004_create_noticias_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('contenido');
            $table->text('resumen')->nullable();
            $table->dateTime('fecha_publicacion');
            $table->foreignId('destino_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticias');
    }
};
EOF

print_message "Migraciones creadas"

# ============================================
# 14. CONFIGURACIÓN .ENV
# ============================================

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

touch database/database.sqlite

print_message ".env configurado"

# ============================================
# 15. INSTALACIÓN FINAL
# ============================================

print_step "Ejecutando instalación final..."

composer install
npm install
php artisan key:generate
php artisan storage:link
php artisan migrate

print_message "¡Instalación completada!"

# ============================================
# 16. MENSAJE FINAL
# ============================================

echo -e "\n${GREEN}================================================${NC}"
echo -e "${GREEN}✅ ¡PORTAL TURÍSTICO INSTALADO CON ÉXITO!${NC}"
echo -e "${GREEN}================================================${NC}\n"

echo -e "${YELLOW}📌 Para iniciar el servidor:${NC}"
echo -e "  cd portal-turistico"
echo -e "  php artisan serve"
echo -e "\n${YELLOW}📌 Para compilar assets (desarrollo):${NC}"
echo -e "  npm run dev"
echo -e "\n${YELLOW}📌 Para compilar assets (producción):${NC}"
echo -e "  npm run build"
echo -e "\n${YELLOW}📌 Para crear un usuario administrador:${NC}"
echo -e "  php artisan make:filament-user"
echo -e "\n${YELLOW}📌 Acceso al panel admin:${NC}"
echo -e "  http://localhost:8000/admin"
echo -e "\n${YELLOW}📌 Estructura del proyecto:${NC}"
echo -e "  - Home con Hero Section animado"
echo -e "  - Destinos destacados con Grid"
echo -e "  - Sección de características"
echo -e "  - Mapa interactivo (Leaflet)"
echo -e "  - Eventos próximos"
echo -e "  - Blog de noticias"
echo -e "  - Call to Action"
echo -e "  - Footer completo con newsletter"
echo -e "  - Panel administrativo (Filament)"
echo -e "\n${GREEN}¡Disfruta tu nuevo Portal Turístico! 🚀${NC}\n"
EOF