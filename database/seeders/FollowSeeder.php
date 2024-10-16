<?php

namespace Database\Seeders;

use App\Models\Follow;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un nuevo follow
        Follow::create([
            'follower_id' => '8f6eea1e-9011-40ec-8ae4-19916127672b', // John Doe
            'followed_id' => '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3', // Blessd
        ]);

        // Crear un nuevo follow
        Follow::create([
            'follower_id' => '741944d5-4f2e-4533-af0d-1155ededce3a', // Blessd
            'followed_id' => '8f6eea1e-9011-40ec-8ae4-19916127672b', // John Doe
        ]);

        // Crear un nuevo follow
        Follow::create([
            'follower_id' => '8f6eea1e-9011-40ec-8ae4-19916127672b', // John Doe
            'followed_id' => '741944d5-4f2e-4533-af0d-1155ededce3a', // Valentina
        ]);

        // Crear un nuevo follow
        Follow::create([
            'follower_id' => '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3', // Blessd
            'followed_id' => '741944d5-4f2e-4533-af0d-1155ededce3a', // Valentina
        ]);
    }
}
