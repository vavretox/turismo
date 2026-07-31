<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(isSimple: false)
            ->brandName('Portal Turistico')
            ->brandLogo(asset('storage/identidad/01KX4DBRY2PYS7W7SJHBV2D02H.png'))
            ->brandLogoHeight('6rem')
            ->colors(['primary' => [
                50 => '#fbf7f5', 100 => '#f2e8e4', 200 => '#e4d2cc', 300 => '#cfb2aa',
                400 => '#b47f7d', 500 => '#96545d', 600 => '#7e3444', 700 => '#6f1d2c',
                800 => '#581722', 900 => '#42121b', 950 => '#2d0b12',
            ]])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString($this->adminStyles()),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([AccountWidget::class])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                \App\Http\Middleware\RedirectProviderFromAdmin::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class]);
    }

    private function adminStyles(): string
    {
        return <<<'HTML'
<style>
    :root {
        --tourism-ink: #2d0b12;
        --tourism-teal: #6f1d2c;
        --tourism-mint: #96545d;
        --tourism-sun: #e1d3c2;
        --tourism-sky: #fbf7f5;
    }

    .fi-body {
        background: #faf7f2;
    }

    .fi-simple-layout {
        min-height: 100vh;
        background:
            radial-gradient(circle at 18% 22%, rgba(229, 216, 200, .34), transparent 28rem),
            radial-gradient(circle at 88% 12%, rgba(180, 127, 125, .28), transparent 26rem),
            linear-gradient(135deg, rgba(45, 11, 18, .96), rgba(111, 29, 44, .90)),
            repeating-linear-gradient(45deg, rgba(255, 255, 255, .06) 0 1px, transparent 1px 24px);
        display: grid;
        place-items: center;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .fi-simple-main-ctn {
        width: min(100%, 28rem);
        margin: 0 auto;
        z-index: 1;
    }

    .fi-simple-main {
        width: 100%;
        border-radius: 1.25rem;
        background: rgba(255, 255, 255, .96);
        box-shadow: 0 30px 80px rgba(69, 10, 10, .34);
        border: 1px solid rgba(255, 255, 255, .62);
        padding: clamp(1.5rem, 5vw, 2.5rem);
        backdrop-filter: blur(16px);
    }

    .fi-logo {
        color: var(--tourism-ink);
        font-weight: 800;
        letter-spacing: 0;
    }

    .fi-simple-header img.fi-logo {
        width: auto;
        max-width: 7rem;
        height: 6rem;
        object-fit: contain;
        margin-inline: auto;
    }

    .fi-simple-header-heading {
        color: var(--tourism-ink);
        font-size: 1.45rem;
        font-weight: 800;
        letter-spacing: 0;
    }

    .fi-simple-header-subheading {
        color: #64748b;
    }

    .fi-input-wrp {
        border-radius: .9rem;
        background: #f8fafc;
        border: 1px solid #e4d2cc;
        box-shadow: none;
    }

    .fi-input-wrp:focus-within {
        border-color: var(--tourism-mint);
        box-shadow: 0 0 0 4px rgba(220, 38, 38, .16);
    }

    .fi-input {
        min-height: 2.85rem;
    }

    .fi-fo-field-label-content {
        color: var(--tourism-ink);
        font-weight: 700;
    }

    .fi-btn.fi-color-primary {
        min-height: 2.9rem;
        border-radius: .9rem;
        background: linear-gradient(135deg, var(--tourism-teal), #0d9488);
        box-shadow: 0 16px 30px rgba(153, 27, 27, .24);
        font-weight: 800;
    }

    .fi-btn.fi-color-primary:hover {
        background: linear-gradient(135deg, #0d9488, var(--tourism-teal));
        transform: translateY(-1px);
    }

    .fi-loading-indicator {
        width: 1.25rem !important;
        height: 1.25rem !important;
    }

    @media (max-width: 900px) {
        .fi-simple-layout {
            place-items: center;
            padding: 1rem;
        }

        .fi-simple-main-ctn {
            margin: 0;
        }
    }
</style>
HTML;
    }
}
