<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SearchController;
use App\Models\User;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

it('limits unknown URLs across paths and methods before any SQL or session access', function () {
    config(['security.limits.traffic' => 2, 'cache.default' => 'database', 'session.driver' => 'database']);
    $queries = [];
    DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });

    $this->get('/scanner-one')->assertNotFound();
    $this->post('/scanner-two')->assertNotFound();
    $this->get('/scanner-three')->assertStatus(429)->assertHeader('Retry-After')
        ->assertJsonStructure(['message']);
    $this->get('/up')->assertOk();
    expect($queries)->toBeEmpty();

    $this->travel(61)->seconds();
    $this->get('/scanner-three')->assertNotFound();
});

it('serves the welcome page without opening a database session', function () {
    config(['cache.default' => 'database', 'session.driver' => 'database']);
    $queries = [];
    DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });
    $this->get('/')->assertOk()->assertCookieMissing(config('session.cookie'));
    expect($queries)->toBeEmpty();
});

it('ignores forged forwarding headers from an untrusted peer', function () {
    config(['security.limits.traffic' => 1, 'trustedproxy.proxies' => []]);
    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
        ->withHeader('X-Forwarded-For', '198.51.100.1')->get('/one')->assertNotFound();
    $this->withHeader('X-Forwarded-For', '198.51.100.2')->get('/two')->assertStatus(429);
});

it('separates real clients behind the trusted Traefik proxy', function () {
    config(['security.limits.traffic' => 1, 'trustedproxy.proxies' => ['192.0.2.10']]);
    $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])
        ->withHeader('X-Forwarded-For', '198.51.100.1')->get('/one')->assertNotFound();
    $this->withHeader('X-Forwarded-For', '198.51.100.2')->get('/two')->assertNotFound();
    $this->withHeader('X-Forwarded-For', '198.51.100.1')->get('/three')->assertStatus(429);
});

it('throttles sensitive public actions before executing their controllers', function ($path, $method, $limit) {
    $controller = Mockery::mock(AuthController::class)->makePartial();
    if ($method === 'validateToken') {
        Sanctum::actingAs(User::factory()->make(['id' => (string) Str::uuid()]));
    }
    $controller->shouldReceive($method)->times($limit)
        ->andReturn($method === 'validateToken' ? true : response()->json(['ok' => true]));
    $this->app->instance(AuthController::class, $controller);

    for ($i = 0; $i < $limit; $i++) {
        $this->postJson('/api/v1/auth/'.$path)->assertOk();
    }
    $this->postJson('/api/v1/auth/'.$path)->assertStatus(429)->assertHeader('Retry-After');
})->with([
    ['login', 'login', 5],
    ['register', 'register', 3],
    ['sendEmailRecovery', 'sendEmailRecovery', 3],
    ['reset-password', 'savePassword', 3],
    ['validate-token', 'validateToken', 5],
]);

it('applies search and two factor limits to authenticated users', function ($path, $controllerClass, $method, $limiter) {
    config(['security.limits.'.$limiter => 2]);
    Sanctum::actingAs(User::factory()->make(['id' => (string) Str::uuid()]));
    $controller = Mockery::mock($controllerClass)->makePartial();
    $controller->shouldReceive($method)->twice()->andReturn(response()->json(['ok' => true]));
    $this->app->instance($controllerClass, $controller);

    for ($i = 0; $i < 3; $i++) {
        $response = str_contains($path, '/search/') ? $this->getJson($path) : $this->postJson($path);
        $response->assertStatus($i < 2 ? 200 : 429);
    }
})->with([
    ['/api/v1/search/song', SearchController::class, 'search_bar', 'search'],
    ['/api/v1/auth/verify-secret', AuthController::class, 'verify', 'two-factor'],
    ['/api/v1/auth/email/verification-notification', AuthController::class, 'sendEmail', 'verification-email'],
]);

it('gives different authenticated users their own API allowance on a shared IP', function () {
    config(['security.limits.api' => 1]);
    Route::middleware(['api', 'auth:sanctum'])->get('/test-api-limit', fn () => response()->json(['ok' => true]));
    Sanctum::actingAs(User::factory()->make(['id' => (string) Str::uuid()]));
    $this->getJson('/test-api-limit')->assertOk();
    $this->getJson('/test-api-limit')->assertStatus(429);
    Sanctum::actingAs(User::factory()->make(['id' => (string) Str::uuid()]));
    $this->getJson('/test-api-limit')->assertOk();
});

it('requires authentication to validate a two factor token', function () {
    $this->postJson('/api/v1/auth/validate-token', ['token' => '123456'])->assertUnauthorized();
});

it('rejects unknown OAuth providers before starting a database session', function () {
    config(['session.driver' => 'database']);
    $this->getJson('/api/v1/auth/wordpress')->assertNotFound();
    $this->getJson('/api/v1/auth/wordpress/callback')->assertNotFound();
});

it('shares local file counters between independent rate limiter instances', function () {
    $key = 'test:'.Str::uuid();
    $first = new RateLimiter(Cache::store('rate_limits'));
    $second = new RateLimiter(Cache::store('rate_limits'));
    try {
        $first->hit($key, 60);
        expect($second->tooManyAttempts($key, 1))->toBeTrue();
    } finally {
        $first->clear($key);
    }
});
