<?php

namespace Database\Seeders;

use App\Models\FollowedPlaylist;
use App\Models\Playlist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FollowedPlaylistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear una nueva playlist seguida
        FollowedPlaylist::create([
            'user_id' => '741944d5-4f2e-4533-af0d-1155ededce3a', // Valentina
            'playlist_id' => Playlist::where('user_id', '8f6eea1e-9011-40ec-8ae4-19916127672b')->first()->id, // Awesome Mix Vol. 1
        ]);
    }
}
