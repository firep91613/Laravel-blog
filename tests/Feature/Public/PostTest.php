<?php

namespace Tests\Feature\Public;

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
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class PostTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Setting::factory()->siteTitle()->create();
        Setting::factory()->siteSubtitle()->create();
        Setting::factory()->defaultUsersAvatar()->create();
    }

    public function testHomePage(): void
    {
        Post::factory()->count(10)->create();

        $response = $this->get(route('public.posts.index'));
        $response->assertOk();
        $response->assertViewIs('public.posts.index');
        $response->assertViewHas('posts');
        $this->assertCount(5, $response->original->getData()['posts']);
    }

    public function testShowPostPage(): void
    {
        $title = 'Hello, world';
        $post = Post::factory()->create(['title' => $title]);

        $response = $this->get(route('public.posts.show', $post->slug));
        $response->assertOk();
        $response->assertViewIs('public.posts.show');
        $response->assertViewHas('post', function ($post) use ($title) {
            return $post->title == $title;
        });
    }

    public function testShowPage404(): void
    {
        $response = $this->get(route('public.posts.show', $this->faker->slug));
        $response->assertNotFound();
    }

    public function testAuthorCanDeleteHisPost(): void
    {
        $post = Post::factory()->create();

        $response = $this->actingAs($post->user)
            ->delete(route('public.posts.destroy', $post->id));
        $this->assertDatabaseMissing('posts', ['slug' => $post->slug]);
        $response->assertRedirect(route('public.posts.index'));
        $response->assertSessionHas('success', __('messages.post.deleted'));
    }

    public function testEditorCanDeleteHisPost(): void
    {
        $post = Post::factory()->withUserRoleEditor()->create();

        $response = $this->actingAs($post->user)->delete(route('public.posts.destroy', $post->id));
        $this->assertDatabaseMissing('posts', ['slug' => $post->slug]);
        $response->assertRedirect(route('public.posts.index'));
        $response->assertSessionHas('success', __('messages.post.deleted'));
    }

    public function testAdminCanDeleteHisPost(): void
    {
        $post = Post::factory()->withUserRoleAdmin()->create();

        $response = $this->actingAs($post->user)->delete(route('public.posts.destroy', $post->id));
        $this->assertDatabaseMissing('posts', ['slug' => $post->slug]);
        $response->assertRedirect(route('public.posts.index'));
        $response->assertSessionHas('success', __('messages.post.deleted'));
    }

    public function testUserCannotDeletePost(): void
    {
        $post = Post::factory()->withUserRoleUser()->create();

        $response = $this->delete(route('public.posts.destroy', $post->id));
        $response->assertForbidden();
        $this->assertDatabaseHas('posts', ['slug' => $post->slug]);
    }

    public function testGuestCannotAccessToCreatePostPage(): void
    {
        $response = $this->get(route('public.posts.create'));
        $response->assertForbidden();
    }

    public function testAuthorCanAccessToCreatePostPage(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('public.posts.create'));
        $response->assertOk();
        $response->assertViewIs('public.posts.create');
    }

    public function testEditorCanAccessToCreatePostPage(): void
    {
        $user = User::factory()->roleEditor()->create();

        $response = $this->actingAs($user)->get(route('public.posts.create'));
        $response->assertOk();
        $response->assertViewIs('public.posts.create');
    }

    public function testAdminCanAccessToCreatePostPage(): void
    {
        $user = User::factory()->roleAdmin()->create();

        $response = $this->actingAs($user)->get(route('public.posts.create'));
        $response->assertOk();
        $response->assertViewIs('public.posts.create');
    }

    public function testAuthorCanEditHisPost(): void
    {
        $post = Post::factory()->create();

        $response = $this->actingAs($post->user)->get(route('public.posts.edit', $post->slug));
        $response->assertOk();
        $response->assertViewHas('post');
    }

    public function testEditorCanEditSomeonePost(): void
    {
        $post = Post::factory()->create();
        $editor = User::factory()->roleEditor()->create();

        $response = $this->actingAs($editor)->get(route('public.posts.edit', $post->slug));
        $response->assertOk();
        $response->assertViewHas('post');
    }

    public function testAdminCanEditSomeonePost(): void
    {
        $post = Post::factory()->create();
        $admin = User::factory()->roleAdmin()->create();

        $response = $this->actingAs($admin)->get(route('public.posts.edit', $post->slug));
        $response->assertOk();
        $response->assertViewHas('post');
    }

    public function testAuthorCanStoreHisPost(): void
    {
        $author = User::factory()->create();
        $post = Post::factory()->make(['user_id' => $author->id]);

        $response = $this->actingAs($author)->post(route('public.posts.store'), $post->toArray());
        $this->assertDatabaseHas('posts', ['slug' => $post->slug]);
        $response->assertRedirect(route('public.posts.index'));
        $response->assertSessionHas('success', __('messages.post.added'));
    }

    public function testEditorCanStoreHisPost(): void
    {
        $editor = User::factory()->roleEditor()->create();
        $post = Post::factory()->make(['user_id' => $editor->id]);

        $response = $this->actingAs($editor)->post(route('public.posts.store'), $post->toArray());
        $this->assertDatabaseHas('posts', ['slug' => $post->slug]);
        $response->assertRedirect(route('public.posts.index'));
        $response->assertSessionHas('success', __('messages.post.added'));
    }

    public function testAdminCanStoreHisPost(): void
    {
        $admin = User::factory()->roleAdmin()->create();
        $post = Post::factory()->make(['user_id' => $admin->id]);

        $response = $this->actingAs($admin)->post(route('public.posts.store'), $post->toArray());
        $this->assertDatabaseHas('posts', ['slug' => $post->slug]);
        $response->assertRedirect(route('public.posts.index'));
        $response->assertSessionHas('success', __('messages.post.added'));
    }

    public function testAuthorCanUpdateHisPost(): void
    {
        $post = Post::factory()->create();
        $newPost = Post::factory()->make([
            'user_id' => $post->user->id,
            'category_id' => $post->category->id
        ]);

        $response = $this->actingAs($post->user)
            ->put(route('public.posts.update', $post->id), $newPost->toArray());
        $this->assertDatabaseHas('posts', ['slug' => $newPost->slug]);
        $response->assertRedirect(route('public.posts.index'));
        $response->assertSessionHas('success', __('messages.post.updated'));
    }

    public function testEditorCanUpdateHisPost(): void
    {
        $post = Post::factory()->withUserRoleEditor()->create();
        $newPost = Post::factory()->make();

        $response = $this->actingAs($post->user)
            ->put(route('public.posts.update', $post->id), $newPost->toArray());
        $this->assertDatabaseHas('posts', ['slug' => $newPost->slug]);
        $response->assertRedirect(route('public.posts.index'));
        $response->assertSessionHas('success', __('messages.post.updated'));
    }

    public function testAdminCanUpdateHisPost(): void
    {
        $post = Post::factory()->withUserRoleAdmin()->create();
        $newPost = Post::factory()->make();

        $response = $this->actingAs($post->user)
            ->put(route('public.posts.update', $post->id), $newPost->toArray());
        $this->assertDatabaseHas('posts', ['slug' => $newPost->slug]);
        $response->assertRedirect(route('public.posts.index'));
        $response->assertSessionHas('success', __('messages.post.updated'));
    }

    public function testEditorCanUpdateSomeonePost(): void
    {
        $post = Post::factory()->create();
        $editor = User::factory()->roleEditor()->create();
        $newData = [
            'title' => 'Hello World',
            'slug' => 'hello-world',
            'excerpt' => 'Hello World',
            'content' => 'Hello World',
            'category_id' => $post->category->id,
            'user_id' => $post->user->id,
        ];

        $response = $this->actingAs($editor)->put(route('public.posts.update', $post->id), $newData);
        $this->assertDatabaseHas('posts', $newData);
        $response->assertRedirect(route('public.posts.index'));
        $response->assertSessionHas('success', __('messages.post.updated'));
    }

    public function testAdminCanUpdateSomeonePost(): void
    {
        $post = Post::factory()->create();
        $admin = User::factory()->roleAdmin()->create();
        $newData = [
            'title' => 'Hello World',
            'slug' => 'hello-world',
            'excerpt' => 'Hello World',
            'content' => 'Hello World',
            'category_id' => $post->category->id,
            'user_id' => $post->user->id,
        ];

        $response = $this->actingAs($admin)->put(route('public.posts.update', $post->id), $newData);
        $this->assertDatabaseHas('posts', $newData);
        $response->assertRedirect(route('public.posts.index'));
        $response->assertSessionHas('success', __('messages.post.updated'));
    }

    public function testTagsAndCategoriesIncludedInPost(): void
    {
        $tags = Tag::factory()->count(3)->create();
        $post = Post::factory()->create();
        $post->tags()->attach($tags);

        $response = $this->get(route('public.posts.show', $post->slug));
        $response->assertOk();
        $response->assertViewIs('public.posts.show');
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

        $author = User::factory()->create();
        $file = UploadedFile::fake()->image('test-image.jpg');
        $post = Post::factory()->make([
            'image' => $file,
            'user_id' => $author->id
        ]);

        $response = $this->actingAs($author)->post(route('public.posts.store'), $post->toArray());
        $response->assertRedirect(route('public.posts.index'));
        $response->assertSessionHas('success', __('messages.post.added'));
        $this->assertDatabaseHas('posts', [
            'image' => 'posts/' . $file->hashName()
        ]);
        Storage::disk('public')->assertExists('posts/' . $file->hashName());
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
