<?php

namespace Tests\Feature\Admin;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    public function setUp(): void
    {
        parent::setUp();

        Setting::factory()->adminLogo()->create();
        Setting::factory()->defaultUsersAvatar()->create();

        $this->admin = User::factory()->roleAdmin()->create();
        $this->actingAs($this->admin);
    }

    public function testShowListOfComments(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->count(20)->create([
            'post_id' => $post->id,
            'user_id' => $post->user->id,
        ]);

        $response = $this->get(route('admin.comments.index'));
        $response->assertOk();
        $response->assertViewIs('admin.comments.index');
        $response->assertViewHas('comments');
        $this->assertCount(5, $response->original->getData()['comments']);
    }

    public function testShowCommentEditPage(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $post->user->id,
        ]);

        $response = $this->get(route('admin.comments.edit', $comment->id));
        $response->assertOk();
        $response->assertViewIs('admin.comments.edit');
        $response->assertSee($comment->name);
    }

    public function testCanUpdateComment(): void
    {
        $post = Post::factory()->create();
        $data = ['post_id' => $post->id, 'user_id' => $post->user->id];
        $comment = Comment::factory()->create($data);
        $newComment = Comment::factory()->make($data);

        $response = $this->put(route('admin.comments.update', $comment->id), $newComment->toArray());
        $response->assertSessionHas('success', __('messages.comment.updated'));
        $response->assertRedirect(route('admin.comments.index'));
        $this->assertDatabaseHas('comments', [
            'content' => $newComment->content,
        ]);
    }

    public function testCanDeleteComment(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $post->user->id,
        ]);

        $response = $this->delete(route('admin.comments.destroy', $comment->id));
        $response->assertRedirect(route('admin.comments.index'));
        $response->assertSessionHas('success', __('messages.comment.deleted'));
        $this->assertDatabaseMissing('comments', [
            'content' => $comment->content
        ]);
    }
}
