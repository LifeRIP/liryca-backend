<?php

namespace Database\Seeders;

use App\Models\FollowAlbum;
use App\Models\Album;
use App\Models\Artist;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FollowAlbumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un nuevo follow
        FollowAlbum::create([
            'user_id' => '741944d5-4f2e-4533-af0d-1155ededce3a', // Valentina
            'album_id' => Album::where('artist_id', Artist::where('user_id', '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3')->first()->id)->first()->id, // Blessd - Álbum 1
        ]);
    }
}
