<?php

namespace Database\Seeders;

use App\Models\Artist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArtistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un nuevo artista
        Artist::create([
            'user_id' => '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3',
            'verified' => true,
            'about' => 'Stiven Mesa Londoño, conocido artísticamente como Blessd, es un cantante, compositor colombiano y modelo En octubre de 2021, firmó con Warner Music Latina y lanzó su primer álbum de estudio llamado "Hecho en Medellín". Una de sus canciones fue inspirada en Octavio Mesa.',
        ]);
    }
}