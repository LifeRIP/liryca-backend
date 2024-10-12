<?php

namespace Database\Seeders;

use App\Models\Song;
use App\Models\Artist;
use App\Models\SongCollaborator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SongCollaboratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un nuevo colaborador

        $artist_id = Artist::where('user_id', '5d5e1b6c-71ec-49ea-bfb1-19100f7d5cf7')->first()->id;
        SongCollaborator::create([
            'artist_id' => $artist_id,
            'song_id' => Song::where('title', 'Ojos Azules')->first()->id,
        ]);
    }
}
