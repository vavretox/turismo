<?php

namespace App\Providers;

use App\Models\SiteIdentity;
use App\Models\WeeklyActivity;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rules\Password;
use App\Models\TourismServiceProvider;
use App\Observers\TourismServiceProviderObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        config([
            'livewire.temporary_file_upload.rules' => ['required', 'file'],
            'livewire.temporary_file_upload.max_upload_time' => 60,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TourismServiceProvider::observe(TourismServiceProviderObserver::class);
        Password::defaults(fn (): Password => Password::min(12)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->uncompromised());

        View::composer(['layouts.app', 'partials.navbar', 'partials.footer'], function ($view): void {
            $view->with('siteIdentity', SiteIdentity::query()->where('clave', 'main')->first());
        });

        View::composer('partials.weekly-activity-popup', function ($view): void {
            $view->with('weeklyActivities', WeeklyActivity::query()->visible()->orderBy('orden')->orderBy('fecha_actividad')->get());
        });
    }
}
