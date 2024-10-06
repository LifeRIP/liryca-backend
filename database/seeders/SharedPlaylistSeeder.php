<?php

namespace Database\Seeders;

use App\Models\Playlist;
use App\Models\SharedPlaylist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SharedPlaylistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear una playlist compartida
        SharedPlaylist::create([
            'playlist_id' => Playlist::where('user_id', '8f6eea1e-9011-40ec-8ae4-19916127672b')->first()->id, // Awesome Mix Vol. 1
            'user_id' => '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3', // Blessd
        ]);
    }
}
