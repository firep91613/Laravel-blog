<?php

namespace Tests\Feature\Public;

use App\Models\Post;
use App\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Setting::factory()->siteTitle()->create();
        Setting::factory()->siteSubtitle()->create();
        Setting::factory()->defaultUsersAvatar()->create();
    }

    public function testSearchByText(): void
    {
        $post = Post::factory()->create([
            'content' => 'This is a test post',
        ]);
        $post->searchable();

        $response = $this->get('/search?query=test');
        $response->assertOk();
        $response->assertSee($post->title);
    }

    public function testSearchReturnsNoResultsForUnknownKeyword(): void
    {
        $post = Post::factory()->create([
            'content' => 'This is a test post',
        ]);
        $post->searchable();

        $response = $this->get('/search?query=aaaaaaaaaaaaaaaa');
        $response->assertOk();
        $response->assertSee('По вашему запросу ничего не найдено');
    }

    public function testSearchByAuthor(): void
    {
        $post = Post::factory()->create();
        $post->searchable();

        $response = $this->get('/search?query=' . $post->user->name);
        $response->assertOk();
        $response->assertSee($post->title);
    }

    public function testSearchByCategory(): void
    {
        $post = Post::factory()->create();
        $post->searchable();

        $response = $this->get('/search?query=' . $post->category->name);
        $response->assertOk();
        $response->assertSee($post->title);
    }
}
