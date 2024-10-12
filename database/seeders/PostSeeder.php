<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Post;
use App\Models\Song;
use App\Models\Playlist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un nuevo post
        $artist_id = Artist::where('user_id', '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3')->first()->id; // Blessd
        Post::create([
            'user_id' => '8f6eea1e-9011-40ec-8ae4-19916127672b', // John Doe
            'content' => 'This is my favorite song',
            'song_id' => Song::where('artist_id', $artist_id)->first()->id, // Mírame
        ]);

        // Crear un nuevo post
        $album_id = Album::Where('title', 'Greatest Hits')->first()->id; // Greatest Hits
        Post::create([
            'user_id' => '741944d5-4f2e-4533-af0d-1155ededce3a', // Valentina
            'content' => 'This is my favorite album',
            'album_id' => $album_id, // Greatest Hits
        ]);

        // Crear un nuevo post
        $artist_id = Artist::where('user_id', '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3')->first()->id; // Blessd
        Post::create([
            'user_id' => '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3', // Blessd
            'content' => 'This is my favorite artist',
            'artist_id' => $artist_id, // Blessd
        ]);

        // Crear un nuevo post
        $playlist_id = Playlist::where('name', 'La mejor musica')->first()->id; // La mejor musica
        Post::create([
            'user_id' => '741944d5-4f2e-4533-af0d-1155ededce3a', // Valentina
            'content' => 'This is my favorite playlist',
            'playlist_id' => $playlist_id, // La mejor musica
        ]);
    }
}
