<?php

namespace Tests\Feature\Public;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Setting::factory()->siteTitle()->create();
        Setting::factory()->siteSubtitle()->create();
        Setting::factory()->defaultUsersAvatar()->create();
    }

    public function testShowPostComments(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $post->user->id
        ]);

        $response = $this->get(route('public.posts.show', $post->slug));
        $response->assertOk();
        $response->assertViewHas('post', function ($viewPost) use ($post, $comment) {
            return $viewPost->id === $post->id &&
                $viewPost->comments->contains('content', $comment->content);
        });
    }

    public function testAuthUserCanAddCommentToPost(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $data = [
            'user_id' => $user->id,
            'post_id' => $post->id,
            'content' => $this->faker->paragraph()
        ];

        $response = $this->actingAs($user)->postJson(route('public.comments.store'), $data);
        $response->assertStatus(201);
        $response->assertJson($data);
        $this->assertDatabaseHas('comments', $data);
    }

    public function testGuestCannotAddCommentToPost(): void
    {
        $post = Post::factory()->create();
        $data = [
            'post_id' => $post->id,
            'content' => $this->faker->paragraph()
        ];

        $response = $this->postJson(route('public.comments.store'), $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('user_id');
        $response->assertJsonMissing($data);
        $this->assertDatabaseMissing('comments', $data);
    }

    public function testAuthUserCanUpdateHisComment(): void
    {
        $post = Post::factory()->create();
        $data = [
            'user_id' => $post->user->id,
            'post_id' => $post->id
        ];
        $comment = Comment::factory()->create($data);
        $newData = [
            'content' => $this->faker->paragraph(),
            'user_id' => $post->user->id,
            'post_id' => $post->id
        ];

        $response = $this->actingAs($post->user)
            ->putJson(route('public.comments.update', $comment->id), $newData);
        $response->assertStatus(201);
        $response->assertJson($newData);
        $this->assertDatabaseHas('comments', $data);
    }

    public function testGuestCannotUpdateComment(): void
    {
        $post = Post::factory()->create();
        $data = [
            'user_id' => $post->user->id,
            'post_id' => $post->id
        ];
        $comment = Comment::factory()->create($data);
        $newData = [
            'content' => $this->faker->paragraph(),
            'user_id' => 0,
            'post_id' => $post->id
        ];

        $response = $this->putJson(route('public.comments.update', $comment->id), $newData);
        $response->assertStatus(422);
        $this->assertDatabaseHas('comments', $data);
        $this->assertDatabaseMissing('comments', $newData);
    }

    public function testAuthUserCanDeleteComment(): void
    {
        $post = Post::factory()->create();
        $data = [
            'user_id' => $post->user->id,
            'post_id' => $post->id
        ];
        $comment = Comment::factory()->create($data);

        $response = $this->deleteJson(route('public.comments.destroy', $comment->id), ['id' => $comment->id]);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', $comment->toArray());
    }

    public function testUserCanReplyOnSomeoneComment(): void
    {
        $post = Post::factory()->create();
        $data = [
            'user_id' => $post->user->id,
            'post_id' => $post->id
        ];
        $comment = Comment::factory()->create($data);
        $user = User::factory()->create();
        $reply = Comment::factory()->make([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'parent_id' => $comment->id
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('public.comments.store'), $reply->toArray());
        $response->assertStatus(201);
        $response->assertJson($reply->toArray());
        $this->assertDatabaseHas('comments', $reply->toArray());
    }
}
