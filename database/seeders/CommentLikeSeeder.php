<?php

namespace Database\Seeders;

use App\Models\CommentLike;
use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un nuevo like
        CommentLike::create([
            'user_id' => '8f6eea1e-9011-40ec-8ae4-19916127672b', // John Doe
            'comment_id' => Comment::where('user_id', '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3')->first()->id,
        ]);
    }
}
