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

        // Crear un nuevo artista
        Artist::create([
            'user_id' => '5d5e1b6c-71ec-49ea-bfb1-19100f7d5cf7', // ID del usuario asociado (Peso Pluma)
            'verified' => true, // Artista verificado
            'about' => 'Hassan Emilio Kabande Laija, conocido como Peso Pluma, es un cantante y compositor mexicano de corridos tumbados. Se ha convertido en un referente de la música regional mexicana, con éxitos como "Ella Baila Sola" y "PRC".',
        ]);
    }
}
