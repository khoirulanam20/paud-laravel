<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('viewApiDocs', fn () => (bool) config('scramble.docs_enabled', false));

        View::composer('layouts.topbar', function ($view) {
            $user = auth()->user();
            $path = null;
            if ($user) {
                $user->loadMissing([
                    'pengajar:id,user_id,photo,name',
                    'anaks:id,user_id,photo,name',
                ]);
                if (filled(optional($user->pengajar)->photo)) {
                    $path = $user->pengajar->photo;
                } elseif ($user->hasRole('Orang Tua') && $user->anaks->isNotEmpty()) {
                    $withPhoto = $user->anaks->first(fn ($a) => filled($a->photo));
                    $path = $withPhoto?->photo;
                }
            }
            $view->with('topbarProfilePhotoPath', $path);
        });
    }
}
