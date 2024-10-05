<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Post;
use App\Models\Song;
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
            'user_id' => '8f6eea1e-9011-40ec-8ae4-19916127672b',
            'content' => 'This is my favorite song',
            'image' => 'https://i.ytimg.com/vi/zcw8NlHljF4/maxresdefault.jpg',
            'song_id' => Song::where('artist_id', $artist_id)->first()->id, // Mírame
        ]);
    }
}
