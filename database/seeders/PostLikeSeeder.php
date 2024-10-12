<?php

namespace Database\Seeders;

use App\Models\PostLike;
use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un nuevo like
        PostLike::create([
            'user_id' => '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3', // Blessd
            'post_id' => Post::where('user_id', '8f6eea1e-9011-40ec-8ae4-19916127672b')->first()->id,
        ]);
    }
}
