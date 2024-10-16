<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear una nueva canción
        $artist_id = Artist::where('user_id', '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3')->first()->id; // Blessd
        Song::create([
            'title' => 'Mírame',
            'artist_id' => $artist_id,
            'album_id' => Album::where('artist_id', $artist_id)->first()->id, // Greatest Hits
            'time' => '00:01:38', // hh:mm:ss
            'genre' => 'Reggaeton',
            'url_song' => 'https://i.scdn.co/image/ab67616d0000b273b62a2ec2d61d48f34a368144',
        ]);

        $artist_id = Artist::where('user_id', '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3')->first()->id; // Blessd
        Song::create([
            'title' => 'Ojos Azules', // Título de la canción
            'artist_id' => $artist_id, // ID del artista
            'album_id' => Album::where('artist_id', $artist_id)->first()->id, // ID del álbum (suponiendo que el álbum ya existe)
            'time' => '00:03:45', // Duración de la canción en formato hh:mm:ss
            'genre' => 'Reggaeton', // Género musical
            'url_song' => 'https://i.scdn.co/image/ab67616d0000b273d6451db776f3c3eac2c8f7b3', // URL de la canción (puedes cambiarlo si tienes la URL correcta)
        ]);
    }
}
