<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        Comment::truncate();

        $post = Post::find(29);
        $user = User::find(7);

        $comment1 = Comment::create([
            'content' => 'Это основной комментарий',
            'post_id' => $post->id,
            'user_id' => $user->id,
            'parent_id' => null,
        ]);

        $sub_comment1 = Comment::create([
            'content' => 'Это подкомментарий к основному комментарию',
            'post_id' => $post->id,
            'user_id' => $user->id,
            'parent_id' => $comment1->id,
        ]);

        Comment::create([
            'content' => 'Это вторичный подкомментарий к подкомментарию 1',
            'post_id' => $post->id,
            'user_id' => $user->id,
            'parent_id' => $sub_comment1->id,
        ]);

        Comment::create([
            'content' => 'Это второй вторичный подкомментарий к подкомментарию 1',
            'post_id' => $post->id,
            'user_id' => $user->id,
            'parent_id' => $sub_comment1->id,
        ]);
    }
}
