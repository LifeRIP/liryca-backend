<?php

namespace App\Providers;

use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Discord\DiscordExtendSocialite;

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
        // Force HTTPS in production
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        // Only works if role_or_permission middleware is used, this doesn't work with role middleware
        Gate::before(function ($user, $ability) {
            return $user->hasRole(RoleEnum::ADMIN->value) ? true : null;
        });

        // Registrar el controlador de Discord
        $this->app['events']->listen(
            SocialiteWasCalled::class,
            [DiscordExtendSocialite::class, 'handle']
        );
    }
}
