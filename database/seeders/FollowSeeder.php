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
            'following_id' => '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3', // Blessd
        ]);

        // Crear un nuevo follow
        Follow::create([
            'follower_id' => '8f6eea1e-9011-40ec-8ae4-19916127672b', // John Doe
            'following_id' => '5d5e1b6c-71ec-49ea-bfb1-19100f7d5cf7', // Peso Pluma
        ]);

        // Crear un nuevo follow
        Follow::create([
            'follower_id' => '741944d5-4f2e-4533-af0d-1155ededce3a', // Blessd
            'following_id' => '8f6eea1e-9011-40ec-8ae4-19916127672b', // John Doe
        ]);

        // Crear un nuevo follow
        Follow::create([
            'follower_id' => '8f6eea1e-9011-40ec-8ae4-19916127672b', // John Doe
            'following_id' => '741944d5-4f2e-4533-af0d-1155ededce3a', // Valentina
        ]);

        // Crear un nuevo follow
        Follow::create([
            'follower_id' => '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3', // Blessd
            'following_id' => '741944d5-4f2e-4533-af0d-1155ededce3a', // Valentina
        ]);
    }
}
