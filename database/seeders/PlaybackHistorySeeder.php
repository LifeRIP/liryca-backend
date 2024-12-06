<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\PlaybackHistory;
use App\Models\Song;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaybackHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un nuevo historial de reproducción
        $artist_id = Artist::where('user_id', '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3')->first()->id; // Blessd
        PlaybackHistory::create([
            'user_id' => '8f6eea1e-9011-40ec-8ae4-19916127672b', // John Doe
            'song_id' => Song::where('artist_id', $artist_id)->first()->id, // Mírame
        ]);

        // Crear un nuevo historial de reproducción
        $artist_id = Artist::where('user_id', '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3')->first()->id; // Blessd
        PlaybackHistory::create([
            'user_id' => '8f6eea1e-9011-40ec-8ae4-19916127672b', // John Doe
            'song_id' => Song::where('artist_id', $artist_id)->first()->id, // Mírame
        ]);

        // Crear un nuevo historial de reproducción
        $artist_id = Artist::where('user_id', '5d5e1b6c-71ec-49ea-bfb1-19100f7d5cf7')->first()->id; // Peso Pluma
        PlaybackHistory::create([
            'user_id' => '8f6eea1e-9011-40ec-8ae4-19916127672b', // John Doe
            'song_id' => Song::where('artist_id', $artist_id)->first()->id, // Bye
        ]);
    }
}
