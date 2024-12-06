<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Artist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlbumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un nuevo álbum
        Album::create([
            'title' => 'Greatest Hits',
            'artist_id' => Artist::where('user_id', '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3')->first()->id, // Blessd
            'release_date' => '2024-09-01',
            'description' => 'The best songs of all time.',
            'icon' => 'https://i.scdn.co/image/ab67616d0000b273c164b1a439733e92b5044700',
        ]);

        Album::create([
            'title' => 'Génesis',
            'artist_id' => Artist::where('user_id', '5d5e1b6c-71ec-49ea-bfb1-19100f7d5cf7')->first()->id, // Blessd
            'release_date' => '2023-06-22',
            'description' => 'Es el tercer álbum de estudio del cantante mexicano Peso Pluma',
            'icon' => 'https://i.scdn.co/image/ab67616d0000b2732fb583ed96f8f35cbf2897ba',
        ]);
    }
}
