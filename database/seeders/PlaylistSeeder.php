<?php

namespace Database\Seeders;

use App\Models\Playlist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaylistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear una nueva playlist
        Playlist::create([
            'name' => 'Awesome Mix Vol. 1',
            'user_id' => '8f6eea1e-9011-40ec-8ae4-19916127672b', // John Doe
            'description' => 'A collection of my favorite songs.',
            'privacy' => 'public',
            'image' => 'https://i.ibb.co/zbCwymf/291216a.jpg',
        ]);

        // Crear una nueva playlist
        Playlist::create([
            'name' => 'La mejor musica',
            'user_id' => '741944d5-4f2e-4533-af0d-1155ededce3a', // Valentina
            'description' => 'lo que mas me gusta.',
            'privacy' => 'private',
            'image' => 'https://static.nationalgeographicla.com/files/styles/image_3200/public/nationalgeographic_1468962.jpg?w=1900&h=1400',
        ]);
    }
}
