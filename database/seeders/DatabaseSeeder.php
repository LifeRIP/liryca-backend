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
            RoleSeeder::class,
            UserSeeder::class,
            ArtistSeeder::class,
            AlbumSeeder::class,
            SongSeeder::class,
            SongCollaboratorSeeder::class,
            PlaylistSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
            PostLikeSeeder::class,
            CommentLikeSeeder::class,
            FollowSeeder::class,
            PlaylistSongSeeder::class,
            SharedPlaylistSeeder::class,
            PlaybackHistorySeeder::class,
            FollowedPlaylistSeeder::class,
            FollowAlbumSeeder::class,
        ]);
    }
}
