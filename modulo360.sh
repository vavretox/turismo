#!/bin/bash

# ================================================
# Script: install-360-module.sh
# Descripción: Instala módulo de Tours 360 para Laravel
# Uso: ./install-360-module.sh
# ================================================

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # Sin color

# Configuración
PROJECT_NAME="Tours360"
MODULE_PATH="Modules/Tours360"

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}   MÓDULO TOURS 360 PARA LARAVEL    ${NC}"
echo -e "${BLUE}========================================${NC}"

# Función para verificar comandos
check_command() {
    if ! command -v $1 &> /dev/null; then
        echo -e "${RED}Error: $1 no está instalado.${NC}"
        exit 1
    fi
}

# Función para preguntar sí/no
ask_yes_no() {
    while true; do
        read -p "$1 (s/n): " -n 1 -r
        echo
        case $REPLY in
            [SsYy]) return 0 ;;
            [Nn]) return 1 ;;
            *) echo -e "${YELLOW}Por favor responde s o n.${NC}" ;;
        esac
    done
}

# Verificar dependencias
echo -e "\n${BLUE}[1/8] Verificando dependencias...${NC}"
check_command php
check_command composer
check_command npm

PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
echo -e "${GREEN}✓ PHP version: $PHP_VERSION${NC}"
echo -e "${GREEN}✓ Composer disponible${NC}"
echo -e "${GREEN}✓ NPM disponible${NC}"

# Crear estructura de módulos
echo -e "\n${BLUE}[2/8] Creando estructura del módulo...${NC}"

# Verificar si Laravel existe
if [ ! -f "artisan" ]; then
    echo -e "${RED}Error: No se encuentra artisan. ¿Estás en la raíz del proyecto Laravel?${NC}"
    exit 1
fi

# Crear directorios del módulo
mkdir -p $MODULE_PATH/{Controllers,Models,Migrations,Views,Resources}
echo -e "${GREEN}✓ Estructura creada en $MODULE_PATH${NC}"

# Crear archivos del módulo
echo -e "\n${BLUE}[3/8] Creando archivos del módulo...${NC}"

# Modelo Tour
cat > $MODULE_PATH/Models/Tour.php << 'EOF'
<?php

namespace Modules\Tours360\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $table = 'tours_360';

    protected $fillable = ['nombre', 'descripcion', 'imagen_portada', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function escenas()
    {
        return $this->hasMany(TourScene::class)->orderBy('orden');
    }
}
EOF

# Modelo TourScene
cat > $MODULE_PATH/Models/TourScene.php << 'EOF'
<?php

namespace Modules\Tours360\Models;

use Illuminate\Database\Eloquent\Model;

class TourScene extends Model
{
    protected $table = 'tours_360_scenes';

    protected $fillable = ['tour_id', 'nombre', 'imagen_url', 'orden', 'hotspots', 'informacion'];

    protected $casts = [
        'hotspots' => 'array',
        'informacion' => 'array',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
EOF

# Controlador Web
cat > $MODULE_PATH/Controllers/TourController.php << 'EOF'
<?php

namespace Modules\Tours360\Controllers;

use App\Http\Controllers\Controller;
use Modules\Tours360\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::where('activo', true)->get();
        return view('tours360::index', compact('tours'));
    }

    public function show($id)
    {
        $tour = Tour::with('escenas')->findOrFail($id);
        return view('tours360::show', compact('tour'));
    }

    public function adminIndex()
    {
        $tours = Tour::all();
        return view('tours360::admin.index', compact('tours'));
    }
}
EOF

echo -e "${GREEN}✓ Modelos y controladores creados${NC}"

# Crear migraciones
echo -e "\n${BLUE}[4/8] Creando migraciones...${NC}"

TIMESTAMP=$(date +%Y_%m_%d_%H%M%S)

cat > database/migrations/${TIMESTAMP}_create_tours_360_table.php << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours_360', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('imagen_portada')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('tours_360_scenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours_360')->onDelete('cascade');
            $table->string('nombre');
            $table->string('imagen_url');
            $table->integer('orden')->default(0);
            $table->json('hotspots')->nullable();
            $table->json('informacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours_360_scenes');
        Schema::dropIfExists('tours_360');
    }
};
EOF

echo -e "${GREEN}✓ Migraciones creadas${NC}"

# Crear vistas
echo -e "\n${BLUE}[5/8] Creando vistas...${NC}"

mkdir -p $MODULE_PATH/Views

# Vista Index
cat > $MODULE_PATH/Views/index.blade.php << 'EOF'
@extends('layouts.app')

@section('title', 'Tours 360°')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8 text-center">🌍 Tours Virtuales 360°</h1>

    <div class="grid md:grid-cols-3 gap-6">
        @foreach($tours as $tour)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
            @if($tour->imagen_portada)
                <img src="{{ asset('storage/' . $tour->imagen_portada) }}" alt="{{ $tour->nombre }}" class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                    <i class="fas fa-vr-cardboard text-4xl text-gray-500"></i>
                </div>
            @endif
            <div class="p-4">
                <h3 class="text-xl font-bold mb-2">{{ $tour->nombre }}</h3>
                <p class="text-gray-600 mb-4">{{ Str::limit($tour->descripcion, 100) }}</p>
                <a href="{{ route('tour.show', $tour->id) }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    <i class="fas fa-eye"></i> Recorrer
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
EOF

# Vista Show (Visor 360)
cat > $MODULE_PATH/Views/show.blade.php << 'EOF'
@extends('layouts.app')

@section('title', $tour->nombre)

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4/dist/photo-sphere-viewer.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4/dist/plugins/markers.min.css"/>
<style>
    #viewer {
        width: 100vw;
        height: calc(100vh - 64px);
        margin-top: -64px;
    }
    .scene-selector {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 100;
        display: flex;
        gap: 10px;
        background: rgba(0,0,0,0.7);
        padding: 12px 20px;
        border-radius: 30px;
        backdrop-filter: blur(10px);
        flex-wrap: wrap;
        justify-content: center;
    }
    .scene-btn {
        padding: 8px 16px;
        border-radius: 20px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
    }
    .scene-btn.active {
        background: #3b82f6;
        color: white;
    }
    .scene-btn.inactive {
        background: #4b5563;
        color: #d1d5db;
    }
    .scene-btn.inactive:hover {
        background: #6b7280;
    }
    .back-btn {
        position: fixed;
        top: 80px;
        left: 20px;
        z-index: 100;
        background: rgba(0,0,0,0.5);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        backdrop-filter: blur(10px);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .back-btn:hover {
        background: rgba(0,0,0,0.7);
        color: white;
    }
</style>
@endpush

@section('content')
<div id="viewer"></div>

<a href="{{ route('tours.index') }}" class="back-btn">
    <i class="fas fa-arrow-left"></i> Volver
</a>

<div class="scene-selector">
    @foreach($tour->escenas as $index => $escena)
    <button class="scene-btn {{ $index == 0 ? 'active' : 'inactive' }}"
            data-index="{{ $index }}"
            data-url="{{ asset('storage/' . $escena->imagen_url) }}">
        {{ $escena->nombre }}
    </button>
    @endforeach
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/uevent@2/browser.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4/dist/photo-sphere-viewer.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4/dist/plugins/markers.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const escenas = @json($tour->escenas);
    let currentSceneIndex = 0;

    // Inicializar visor
    const viewer = new PhotoSphereViewer.Viewer({
        container: document.querySelector('#viewer'),
        panorama: '/storage/{{ $tour->escenas[0]->imagen_url }}',
        defaultZoomLvl: 0,
        navbar: ['autorotate', 'zoom', 'fullscreen'],
        plugins: [
            [PhotoSphereViewer.MarkersPlugin, {
                markers: @json($tour->escenas[0]->hotspots ?? [])
            }]
        ]
    });

    const markersPlugin = viewer.getPlugin(PhotoSphereViewer.MarkersPlugin);

    function changeScene(index) {
        if (index === currentSceneIndex) return;

        const escena = escenas[index];
        viewer.setPanorama('/storage/' + escena.imagen_url, {
            zoom: 0,
            longitude: 0,
            latitude: 0
        });

        // Actualizar marcadores
        markersPlugin.clearMarkers();
        if (escena.hotspots) {
            escena.hotspots.forEach(hotspot => {
                markersPlugin.addMarker({
                    id: hotspot.id || 'marker-' + Date.now(),
                    longitude: hotspot.longitude,
                    latitude: hotspot.latitude,
                    html: hotspot.html || '<div style="background: #3b82f6; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; cursor: pointer; border: 2px solid white; box-shadow: 0 0 20px rgba(59,130,246,0.5);">➡️</div>',
                    width: 40,
                    height: 40,
                    anchor: 'center'
                });
            });
        }

        // Actualizar botones
        document.querySelectorAll('.scene-btn').forEach((btn, i) => {
            btn.className = `scene-btn ${i === index ? 'active' : 'inactive'}`;
        });

        currentSceneIndex = index;
    }

    document.querySelectorAll('.scene-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            changeScene(parseInt(this.dataset.index));
        });
    });

    // Manejar clic en marcadores
    markersPlugin.on('select-marker', function(e) {
        const markerId = e.marker.id;
        const escenaActual = escenas[currentSceneIndex];
        if (escenaActual.hotspots) {
            const hotspot = escenaActual.hotspots.find(h => h.id === markerId);
            if (hotspot && hotspot.targetSceneId) {
                const targetIndex = escenas.findIndex(e => e.id === hotspot.targetSceneId);
                if (targetIndex !== -1) {
                    changeScene(targetIndex);
                }
            }
        }
    });
});
</script>
@endpush
@endsection
EOF

# Vista Admin (para panel de control)
cat > $MODULE_PATH/Views/admin/index.blade.php << 'EOF'
@extends('layouts.app')

@section('title', 'Administrar Tours 360')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">📁 Administrar Tours 360</h1>
        <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="fas fa-plus"></i> Nuevo Tour
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Escenas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($tours as $tour)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $tour->id }}</td>
                    <td class="px-6 py-4">{{ $tour->nombre }}</td>
                    <td class="px-6 py-4">{{ $tour->escenas->count() }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs {{ $tour->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $tour->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('tour.show', $tour->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="#" class="text-yellow-600 hover:text-yellow-900 mr-2">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
EOF

echo -e "${GREEN}✓ Vistas creadas${NC}"

# Registrar rutas
echo -e "\n${BLUE}[6/8] Registrando rutas...${NC}"

# Verificar si las rutas ya existen en web.php
if ! grep -q "Tours360" routes/web.php; then
    cat >> routes/web.php << 'EOF'

// ========== TOURS 360 MODULE ==========
Route::prefix('tours360')->group(function () {
    Route::get('/', [Modules\Tours360\Controllers\TourController::class, 'index'])->name('tours.index');
    Route::get('/tour/{id}', [Modules\Tours360\Controllers\TourController::class, 'show'])->name('tour.show');
    Route::get('/admin', [Modules\Tours360\Controllers\TourController::class, 'adminIndex'])->name('tours.admin');
});
EOF
    echo -e "${GREEN}✓ Rutas añadidas a routes/web.php${NC}"
else
    echo -e "${YELLOW}⚠ Las rutas ya existen en web.php${NC}"
fi

# Instalar dependencias NPM
echo -e "\n${BLUE}[7/8] Instalando dependencias frontend...${NC}"

if ask_yes_no "¿Deseas instalar photo-sphere-viewer vía npm?"; then
    npm install photo-sphere-viewer@^4.10 --save
    echo -e "${GREEN}✓ photo-sphere-viewer instalado${NC}"
else
    echo -e "${YELLOW}⚠ Se usarán CDN para las librerías${NC}"
fi

# Ejecutar migraciones
echo -e "\n${BLUE}[8/8] Ejecutando migraciones...${NC}"

if ask_yes_no "¿Deseas ejecutar las migraciones ahora?"; then
    php artisan migrate
    echo -e "${GREEN}✓ Migraciones ejecutadas${NC}"
else
    echo -e "${YELLOW}⚠ Recuerda ejecutar: php artisan migrate${NC}"
fi

# Crear enlace storage
php artisan storage:link 2>/dev/null || echo -e "${YELLOW}⚠ storage:link ya existe o no se pudo crear${NC}"

# Finalizar
echo -e "\n${GREEN}========================================${NC}"
echo -e "${GREEN}   ¡MÓDULO INSTALADO CON ÉXITO!       ${NC}"
echo -e "${GREEN}========================================${NC}"
echo -e "\n${BLUE}📌 INSTRUCCIONES:${NC}"
echo -e "1. Visita: ${GREEN}/tours360${NC} para ver los tours"
echo -e "2. Visita: ${GREEN}/tours360/admin${NC} para administrar"
echo -e "3. Crea un tour con: ${GREEN}php artisan tinker${NC}"
echo -e "\n${BLUE}📝 EJEMPLO DE DATOS:${NC}"
echo -e "\$tour = new Modules\\Tours360\\Models\\Tour();"
echo -e "\$tour->nombre = 'Mi Tour 360';"
echo -e "\$tour->descripcion = 'Descripción del tour';"
echo -e "\$tour->save();"
echo -e "\n${YELLOW}⚠ No olvides configurar los permisos de storage:${NC}"
echo -e "   chmod -R 775 storage/app/public"
echo -e "\n${GREEN}¡Disfruta tu módulo de Tours 360! 🎉${NC}"