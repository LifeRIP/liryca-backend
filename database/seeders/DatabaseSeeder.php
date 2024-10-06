<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Follow;
use App\Models\PlaylistSong;
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
            CommentSeeder::class,
            LikeSeeder::class,
            FollowSeeder::class,
            PlaylistSeeder::class,
            PlaylistSongSeeder::class,
            SharedPlaylistSeeder::class,
            PlaybackHistorySeeder::class,
        ]);
    }
}
