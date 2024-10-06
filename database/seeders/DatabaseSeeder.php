<?php

namespace Database\Seeders;

use App\Models\Follow;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamar a los seeders
        $this->call([
            UserSeeder::class,
            ArtistSeeder::class,
            AlbumSeeder::class,
            SongSeeder::class,
            PostSeeder::class,
            LikeSeeder::class,
            FollowSeeder::class,
            PlaylistSeeder::class,
            SharedPlaylistSeeder::class,
        ]);
    }
}
