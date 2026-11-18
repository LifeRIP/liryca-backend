<?php

use App\Enums\RoleEnum;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    Notification::fake();
});

it('registers users and artists with UUIDs, roles and bearer tokens', function (string $role) {
    $response = $this->postJson('/api/v1/auth/register', [
        'username' => 'listener',
        'email' => 'listener@example.com',
        'country' => 'CO',
        'birthday' => '2000-01-01',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'role' => $role,
    ])->assertCreated()->assertJsonStructure(['user' => ['id'], 'token']);

    $user = User::findOrFail($response->json('user.id'));
    expect($user->hasRole($role))->toBeTrue();
    expect($user->tokens()->count())->toBe(1);
    expect($user->artist()->exists())->toBe($role === RoleEnum::ARTIST->value);
    Notification::assertSentTo($user, Illuminate\Auth\Notifications\VerifyEmail::class);
})->with([RoleEnum::USER->value, RoleEnum::ARTIST->value]);

it('logs in, authenticates a bearer token and revokes it on logout', function () {
    $user = User::factory()->create();
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email, 'password' => 'password',
    ])->assertOk();

    $token = $response->json('token');
    // Simulate a new API request without the session guard retained by this test process.
    app('auth')->forgetGuards();
    config(['sanctum.guard' => []]);
    $this->withToken($token)->getJson('/api/v1/playlist')->assertOk();
    $this->withToken($token)->getJson('/api/v1/auth/logout')->assertOk();
    expect($user->tokens()->count())->toBe(0);
    app('auth')->forgetGuards();
    $this->withToken($token)->getJson('/api/v1/playlist')->assertUnauthorized();
});

it('rejects invalid credentials and unauthenticated requests', function () {
    $this->postJson('/api/v1/auth/login', [
        'email' => 'missing@example.com', 'password' => 'password',
    ])->assertUnauthorized();
    $this->getJson('/api/v1/playlist')->assertUnauthorized();
});

it('creates and lists playlists and searches the catalog', function () {
    Sanctum::actingAs(User::factory()->create());
    $this->postJson('/api/v1/playlist')->assertCreated();
    $this->getJson('/api/v1/playlist')->assertOk()->assertJsonCount(1, 'playlists');
    $this->getJson('/api/v1/search/no-matches')->assertOk()
        ->assertJsonStructure(['songs', 'albums', 'playlists', 'artists', 'users']);
});

it('checks database connectivity and seeds roles idempotently', function () {
    $this->seed(RoleSeeder::class);
    $this->assertDatabaseCount('roles', count(RoleEnum::cases()));
    $this->artisan('app:check-database-connection')->assertSuccessful();
});

it('loads song details, album songs and playback rankings', function () {
    $user = User::factory()->create(['country' => 'CO']);
    $artist = $user->artist()->create(['verified' => true]);
    $album = \App\Models\Album::create([
        'title' => 'Album', 'artist_id' => $artist->id, 'release_date' => '2026-01-01',
    ]);
    $song = \App\Models\Song::create([
        'title' => 'Song', 'artist_id' => $artist->id, 'album_id' => $album->id,
        'time' => '00:03:00', 'genre' => 'Pop', 'url_song' => 'https://example.com/song.mp3',
    ]);
    \App\Models\PlaybackHistory::create(['user_id' => $user->id, 'song_id' => $song->id]);
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/song/'.$song->id)->assertOk()->assertJsonPath('success', true);
    $this->getJson('/api/v1/song/by-album-id/'.$album->id)->assertOk()->assertJsonCount(1, 'data');
    $this->getJson('/api/v1/song/top-by-user/'.$user->id)->assertOk()->assertJsonCount(1, 'data');
    $this->getJson('/api/v1/song/songs/top-today')->assertOk()->assertJsonCount(1, 'data');
    $this->getJson('/api/v1/song/songs/top-today-by-my-country')->assertOk()->assertJsonCount(1, 'data');
});

it('verifies encrypted two factor secrets and issues a token', function () {
    $google2fa = app('pragmarx.google2fa');
    $secret = $google2fa->generateSecretKey();
    $user = User::factory()->create(['two_factor_secret' => $secret, 'enable_two_factor_auth' => true]);
    expect($user->getRawOriginal('two_factor_secret'))->not->toBe($secret);
    Sanctum::actingAs($user);
    $this->postJson('/api/v1/auth/verify-secret', ['token' => $google2fa->getCurrentOtp($secret)])
        ->assertOk()->assertJsonStructure(['token']);
});

it('generates a two factor QR code and stores the secret encrypted', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $response = $this->getJson('/api/v1/auth/generate-2fa-secret')->assertOk();
    $secret = $response->json('1.secret');
    expect($secret)->toBeString()->not->toBeEmpty();
    expect($response->json('1.google2fa_url'))->toBeString()->not->toBeEmpty();
    expect($user->fresh()->two_factor_secret)->toBe($secret);
    expect($user->fresh()->getRawOriginal('two_factor_secret'))->not->toBe($secret);
});

it('keeps liked playlists private to the authenticated user', function () {
    $owner = User::factory()->create();
    $viewer = User::factory()->create();
    $artist = $owner->artist()->create(['verified' => true]);
    $album = \App\Models\Album::create([
        'title' => 'Favorites', 'artist_id' => $artist->id, 'release_date' => '2026-01-01',
    ]);
    $song = \App\Models\Song::create([
        'title' => 'Favorite song', 'artist_id' => $artist->id, 'album_id' => $album->id,
        'time' => '00:03:00', 'genre' => 'Pop', 'url_song' => 'https://example.com/song.mp3',
    ]);
    Sanctum::actingAs($owner);
    $this->postJson('/api/v1/playlistsong/likeSong/'.$song->id)->assertOk();

    Sanctum::actingAs($viewer);
    $this->getJson('/api/v1/playlistsong/playlist/like')->assertOk()->assertJsonCount(0, 'data');
    $this->assertDatabaseHas('playlists', ['user_id' => $viewer->id, 'name' => 'LikedSongs', 'privacy' => 'private']);
    $this->getJson('/api/v1/playlistsong/playlist/like')->assertOk()->assertJsonCount(0, 'data');
    expect($viewer->playlists()->where('name', 'LikedSongs')->count())->toBe(1);

    Sanctum::actingAs($owner);
    $this->getJson('/api/v1/playlistsong/playlist/like')->assertOk()
        ->assertJsonCount(1, 'data')->assertJsonPath('data.0.song.title', $song->title)
        ->assertJsonPath('data.0.song.is_liked', true)
        ->assertJsonPath('data.0.artist.username', $owner->username);
});

it('lists only the requested users posts with viewer like status', function (string $type) {
    $owner = User::factory()->create();
    $viewer = User::factory()->create();
    $artist = $owner->artist()->create(['verified' => true]);
    $album = \App\Models\Album::create([
        'title' => 'Shared album', 'artist_id' => $artist->id, 'release_date' => '2026-01-01',
    ]);
    $song = \App\Models\Song::create([
        'title' => 'Shared song', 'artist_id' => $artist->id, 'album_id' => $album->id,
        'time' => '00:03:00', 'genre' => 'Pop', 'url_song' => 'https://example.com/song.mp3',
    ]);
    $playlist = \App\Models\Playlist::create(['user_id' => $owner->id, 'name' => 'Shared playlist']);
    $association = match ($type) {
        'song' => ['song_id' => $song->id],
        'album' => ['album_id' => $album->id],
        'artist' => ['artist_id' => $artist->id],
        'playlist' => ['playlist_id' => $playlist->id],
        default => [],
    };
    $post = \App\Models\Post::create(['user_id' => $owner->id, 'content' => 'Owner post', ...$association]);
    \App\Models\Post::create(['user_id' => $viewer->id, 'content' => 'Other post']);
    \App\Models\PostLike::create(['user_id' => $viewer->id, 'post_id' => $post->id]);
    Sanctum::actingAs($viewer);

    $this->getJson('/api/v1/post/'.$owner->id)->assertOk()
        ->assertJsonPath('success', true)->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $post->id)->assertJsonPath('data.0.type', $type)
        ->assertJsonPath('data.0.is_liked', true)->assertJsonPath('data.0.like_count', 1);
    $this->getJson('/api/v1/post/'.User::factory()->create()->id)->assertOk()->assertJsonCount(0, 'data');
})->with(['text', 'song', 'album', 'artist', 'playlist']);
