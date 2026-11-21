<?php

namespace App\Providers;

use App\Enums\RoleEnum;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        foreach (array_keys(config('security.limits')) as $name) {
            RateLimiter::for($name, function (Request $request) use ($name) {
                // The global IP limit must never resolve an authenticated user / query SQL.
                $perUser = in_array($name, ['api', 'search', 'two-factor', 'verification-email'], true);
                $identity = $perUser && $request->user()
                    ? 'user:'.$request->user()->getAuthIdentifier()
                    : 'ip:'.$request->ip();

                return Limit::perMinute(max(1, config('security.limits.'.$name)))->by($identity)
                    ->response(fn (Request $request, array $headers) => response()->json([
                        'message' => 'Demasiadas solicitudes. Intenta de nuevo en unos segundos.',
                    ], 429, $headers));
            });
        }

        // Force HTTPS in production
        if ($this->app->environment('production')) {
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
