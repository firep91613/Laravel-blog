<?php

namespace Tests\Feature\Admin;

use App\Exceptions\DbException;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use App\Services\ImageService;
use App\Services\Public\PostService;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Connection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class PostTest extends TestCase
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

    public function testShowListOfPosts(): void
    {
        Post::factory()->count(20)->create();

        $response = $this->get(route('admin.posts.index'));
        $response->assertOk();
        $response->assertViewIs('admin.posts.index');
        $response->assertViewHas('posts');
        $this->assertCount(5, $response->original->getData()['posts']);
    }

    public function testShowPost(): void
    {
        $post = Post::factory()->create();

        $response = $this->get(route('admin.posts.show', $post->id));
        $response->assertOk();
        $response->assertViewIs('admin.posts.show');
        $response->assertViewHas('post', function($viewPost) use ($post) {
            return $viewPost->content == $post->content;
        });
    }

    public function testTagsAndCategoriesIncludedInPost(): void
    {
        $tags = Tag::factory()->count(3)->create();
        $post = Post::factory()->create();
        $post->tags()->attach($tags);

        $response = $this->get(route('admin.posts.show', $post->id));
        $response->assertOk();
        $response->assertViewIs('admin.posts.show');
        $response->assertViewHas('post', function ($viewPost) use ($tags, $post) {
            $tagsResult = $viewPost->tags->every(function ($tag) use ($tags) {
                return $tags->contains('id', $tag->id);
            });
            $categoryResult = $viewPost->category->name == $post->category->name;

            return $tagsResult && $categoryResult;
        });
    }

    public function testImageIncludedInPost(): void
    {
        Storage::fake('public');
        $this->app->bind(ImageService::class, function () {
            return new ImageService(Storage::disk('public'));
        });

        $file = UploadedFile::fake()->image('test-image.jpg', 700, 400);
        $post = Post::factory()->make([
            'image' => $file,
            'user_id' => $this->admin->id
        ]);

        $response = $this->post(route('admin.posts.store'), $post->toArray());
        $response->assertRedirect(route('admin.posts.index'));
        $response->assertSessionHas('success', __('messages.post.added'));
        $post->refresh();
        $this->assertNotNull($post->image);
        $this->assertDatabaseHas('posts', [
            'image' => 'posts/' . $file->hashName()
        ]);
        Storage::disk('public')->assertExists('posts/' . $file->hashName());
    }

    public function testShowPostEditPage(): void
    {
        $post = Post::factory()->create();

        $response = $this->get(route('admin.posts.edit', $post->id));
        $response->assertOk();
        $response->assertViewIs('admin.posts.edit');
        $response->assertSee($post->content);
    }

    public function testCanUpdatePost(): void
    {
        $post = Post::factory()->create();
        $newPost = Post::factory()->make();

        $response = $this->put(route('admin.posts.update', $post->id), $newPost->toArray());
        $response->assertSessionHas('success', __('messages.post.updated'));
        $response->assertRedirect(route('admin.posts.index'));
        $this->assertDatabaseHas('posts', $newPost->toArray());
    }

    public function testCanDeletePost(): void
    {
        $post = Post::factory()->create();

        $response = $this->delete(route('admin.posts.destroy', $post->id));
        $response->assertRedirect(route('admin.posts.index'));
        $response->assertSessionHas('success', __('messages.post.deleted'));
        $this->assertDatabaseMissing('posts', $post->toArray());
    }

    public function testCanAddPost(): void
    {
        $post = Post::factory()->make();

        $response = $this->post(route('admin.posts.store'), $post->toArray());
        $response->assertRedirect(route('admin.posts.index'));
        $response->assertSessionHas('success', __('messages.post.added'));
        $this->assertDatabaseHas('posts', $post->toArray());
    }

    public function testExceptionOnSavingPost(): void
    {
        $data = Post::factory()->make([
            'image' => 'posts/test-image.jpg',
            'user_id' => User::factory()->roleEditor()->create()
        ]);
        $dbMock = Mockery::mock(Connection::class);
        $modelMock = Mockery::mock(Post::class);
        $imageServiceMock = Mockery::mock(ImageService::class);
        $translatorMock = Mockery::mock(Translator::class);
        $translatorMock->shouldReceive('get')
            ->with('messages.exception.post.add')
            ->andReturn('Ошибка добавления поста: ');
        $dbMock->shouldReceive('transaction')
            ->once()
            ->andThrow(new \Exception('Saving error'));
        $imageServiceMock->shouldReceive('delete')
            ->once()
            ->with('posts/test-image.jpg');
        $service = new PostService(
            $modelMock,
            $dbMock,
            $imageServiceMock,
            $translatorMock
        );

        $this->expectException(DbException::class);
        $this->expectExceptionMessage('Хьюстон, у нас проблемы с базой!');

        $service->save($data->toArray());
    }

    public function testExceptionOnUpdatingPost(): void
    {
        $post = Post::factory()->create([
            'image' => 'posts/test-image.jpg'
        ]);
        $newData = Post::factory()->make([
            'image' => 'posts/test-image.png',
            'user_id' => $post->user->id
        ]);
        $dbMock = Mockery::mock(Connection::class);
        $modelMock = Mockery::mock(Post::class);
        $imageServiceMock = Mockery::mock(ImageService::class);
        $translatorMock = Mockery::mock(Translator::class);
        $translatorMock->shouldReceive('get')
            ->with('messages.exception.post.update')
            ->andReturn('Ошибка обновления поста: ');
        $dbMock->shouldReceive('transaction')
            ->once()
            ->andThrow(new \Exception('Saving error'));
        $imageServiceMock->shouldReceive('delete')
            ->once()
            ->with('posts/test-image.png');
        $service = new PostService(
            $modelMock,
            $dbMock,
            $imageServiceMock,
            $translatorMock
        );

        $this->expectException(DbException::class);
        $this->expectExceptionMessage('Хьюстон, у нас проблемы с базой!');

        $service->update($post, $newData->toArray());
    }

    public function testExceptionOnDeletingPost(): void
    {
        $post = Post::factory()->create();
        $dbMock = Mockery::mock(Connection::class);
        $modelMock = Mockery::mock(Post::class);
        $imageServiceMock = Mockery::mock(ImageService::class);
        $translatorMock = Mockery::mock(Translator::class);
        $translatorMock->shouldReceive('get')
            ->with('messages.exception.post.delete')
            ->andReturn('Ошибка удаления поста: ');
        $dbMock->shouldReceive('transaction')
            ->once()
            ->andThrow(new \Exception('Saving error'));
        $service = new PostService(
            $modelMock,
            $dbMock,
            $imageServiceMock,
            $translatorMock
        );

        $this->expectException(DbException::class);
        $this->expectExceptionMessage('Хьюстон, у нас проблемы с базой!');

        $service->delete($post);
    }
}
