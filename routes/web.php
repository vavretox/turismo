<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DestinoController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProvinciaTuristicaController;
use App\Http\Controllers\ServicioTuristicoController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\TourismChatController;
use App\Http\Controllers\InteractiveMapController;
use App\Http\Controllers\InspireController;
use App\Http\Controllers\GenerateItineraryController;
use App\Http\Controllers\WeeklyActivityController;
use App\Http\Controllers\TourismServiceProviderController;
use App\Http\Controllers\ProviderPortalController;
use App\Http\Controllers\PublicPlaceController;
use App\Http\Controllers\VirtualTourController;
use App\Http\Controllers\ProviderOfferingController;
use App\Http\Middleware\EnsureAdmin;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/registro-prestadores-turisticos', [TourismServiceProviderController::class, 'create'])->name('prestadores.create');
Route::post('/registro-prestadores-turisticos', [TourismServiceProviderController::class, 'store'])->middleware('throttle:5,1')->name('prestadores.store');
Route::get('/prestadores/ingresar', [ProviderPortalController::class, 'login'])->name('prestador.login');
Route::post('/prestadores/ingresar', [ProviderPortalController::class, 'authenticate'])->middleware('throttle:5,1')->name('prestador.authenticate');
Route::post('/prestadores/sesion-expirada', [ProviderPortalController::class, 'expire'])->name('prestador.expire');
Route::middleware('provider.session')->group(function () {
    Route::get('/prestadores/mi-pagina', [ProviderPortalController::class, 'dashboard'])->name('prestador.panel');
    Route::get('/prestadores/mi-pagina/vista-previa', [ProviderPortalController::class, 'preview'])->name('prestador.preview');
    Route::put('/prestadores/mi-pagina', [ProviderPortalController::class, 'update'])->name('prestador.update');
    Route::put('/prestadores/mi-cuenta', [ProviderPortalController::class, 'updatePassword'])->name('prestador.password');
    Route::get('/prestadores/contenidos/nuevo', [ProviderOfferingController::class, 'create'])->name('prestador.offers.create');
    Route::post('/prestadores/contenidos', [ProviderOfferingController::class, 'store'])->name('prestador.offers.store');
    Route::get('/prestadores/contenidos/{offering}/editar', [ProviderOfferingController::class, 'edit'])->name('prestador.offers.edit');
    Route::put('/prestadores/contenidos/{offering}', [ProviderOfferingController::class, 'update'])->name('prestador.offers.update');
    Route::delete('/prestadores/contenidos/{offering}', [ProviderOfferingController::class, 'destroy'])->name('prestador.offers.destroy');
    Route::post('/prestadores/salir', [ProviderPortalController::class, 'logout'])->name('prestador.logout');
    Route::post('/prestadores/actividad', fn () => response()->noContent())->name('prestador.activity');
});
Route::get('/consulta-prestadores-turisticos', [TourismServiceProviderController::class, 'index'])->middleware('throttle:30,1')->name('prestadores.index');
Route::get('/admin/prestadores/{provider}/documentos/{index}', [TourismServiceProviderController::class, 'download'])->middleware(EnsureAdmin::class.':prestadores_turisticos')->name('prestadores.documents.download');
Route::get('/admin/prestadores/{provider}/documentos/{index}/ver', [TourismServiceProviderController::class, 'preview'])->middleware(EnsureAdmin::class.':prestadores_turisticos')->name('prestadores.documents.preview');
Route::get('/mapa-interactivo', InteractiveMapController::class)->name('mapa.interactivo');
Route::get('/tours-360', [VirtualTourController::class, 'index'])->name('tours-360.index');
Route::get('/tours-360/{tour}', [VirtualTourController::class, 'show'])->name('tours-360.show');
Route::get('/lugares/{place}', [PublicPlaceController::class, 'show'])->name('lugares.show');
Route::get('/inspirame', InspireController::class)->name('inspirame');
Route::post('/inspirame/generar', GenerateItineraryController::class)->middleware('throttle:10,1')->name('inspirame.generate');
Route::get('/buscar', SearchController::class)->name('buscar');
Route::get('/idioma/{locale}', function (string $locale) {
    $locale = in_array($locale, ['es', 'en'], true) ? $locale : 'es';
    session(['locale' => $locale]);

    return back()
        ->withCookie(cookie(
            'site_locale',
            $locale,
            60 * 24 * 365,
            '/',
            null,
            request()->isSecure(),
            false,
            false,
            'Lax',
        ))
        ->withCookie(cookie(
            'googtrans',
            $locale === 'en' ? '/es/en' : '/es/es',
            60 * 24 * 365,
            '/',
            null,
            request()->isSecure(),
            false,
            true,
            'Lax',
        ));
})->name('idioma');
Route::get('/municipios', [ProvinciaTuristicaController::class, 'index'])->name('municipios.index');
Route::get('/municipios/{municipio}', [ProvinciaTuristicaController::class, 'show'])->name('municipios.show');
Route::redirect('/provincias', '/municipios');
Route::get('/servicios-turisticos', [ServicioTuristicoController::class, 'index'])->name('servicios.index');
Route::get('/servicios-turisticos/{servicio}', [ServicioTuristicoController::class, 'show'])->name('servicios.show');
Route::get('/destinos', [DestinoController::class, 'index'])->name('destinos');
Route::get('/destinos/{destino}', [DestinoController::class, 'show'])->name('destinos.show');
Route::get('/eventos', [EventoController::class, 'index'])->name('eventos');
Route::redirect(
    '/eventos/festival-de-experiencias-locales',
    '/eventos/fiesta-grande-de-san-roque',
    301,
);
Route::get('/eventos/{evento}', [EventoController::class, 'show'])->name('eventos.show');
Route::get('/actividades', [WeeklyActivityController::class, 'index'])->name('actividades.index');
Route::get('/actividades/{actividad}', [WeeklyActivityController::class, 'show'])->name('actividades.show');
Route::get('/noticias', [NoticiaController::class, 'index'])->name('noticias');
Route::get('/noticias/{noticia}', [NoticiaController::class, 'show'])->name('noticias.show');
Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto');
Route::post('/contacto', [ContactoController::class, 'store'])->middleware('throttle:5,1')->name('contacto.store');
Route::post('/asistente-turistico', TourismChatController::class)->middleware('throttle:30,1')->name('chat.ask');
Route::post('/newsletter', [NewsletterController::class, 'store'])->middleware('throttle:10,1')->name('newsletter.store');
Route::get('/newsletter/cancelar/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
