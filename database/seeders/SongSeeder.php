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

        // Crear una nueva canción
        $artist_id = Artist::where('user_id', '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3')->first()->id; // Blessd
        Song::create([
            'title' => 'Ojos Azules',
            'artist_id' => $artist_id,
            'album_id' => Album::where('artist_id', $artist_id)->first()->id, // ID del álbum (suponiendo que el álbum ya existe)
            'time' => '00:03:45',
            'genre' => 'Reggaeton',
            'url_song' => 'https://i.scdn.co/image/ab67616d0000b2730bcfe30a8ac5fee4463fc6f1',
        ]);

        // Crear una nueva canción
        $artist_id = Artist::where('user_id', '5d5e1b6c-71ec-49ea-bfb1-19100f7d5cf7')->first()->id; // Peso Pluma
        Song::create([
            'title' => 'Bye',
            'artist_id' => $artist_id,
            'album_id' => Album::where('artist_id', $artist_id)->first()->id, // ID del álbum (suponiendo que el álbum ya existe)
            'time' => '00:03:33',
            'genre' => 'Reggaeton',
            'url_song' => 'https://i1.sndcdn.com/artworks-0DsBdbOLjg8T-0-t500x500.jpg',
        ]);
    }
}
