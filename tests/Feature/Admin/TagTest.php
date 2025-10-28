<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TagTest extends TestCase
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

    public function testShowListOfTags(): void
    {
        Tag::factory()->count(20)->create();

        $response = $this->get(route('admin.tags.index'));
        $response->assertOk();
        $response->assertViewIs('admin.tags.index');
        $response->assertViewHas('tags');
        $this->assertCount(5, $response->original->getData()['tags']);
    }

    public function testShowTagInfo(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->get(route('admin.tags.show', $tag->id));
        $response->assertOk();
        $response->assertViewIs('admin.tags.show');
        $response->assertViewHas('tag', function($viewTag) use ($tag) {
            return $viewTag->name == $tag->name;
        });
    }

    public function testShowTagEditPage(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->get(route('admin.tags.edit', $tag->id));
        $response->assertOk();
        $response->assertViewIs('admin.tags.edit');
        $response->assertSee($tag->name);
    }

    public function testCanUpdateTag(): void
    {
        $tag = Tag::factory()->create();
        $newTag = Tag::factory()->make();

        $response = $this->put(route('admin.tags.update', $tag->id), $newTag->toArray());
        $response->assertSessionHas('success', __('messages.tag.updated'));
        $response->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseHas('tags', [
            'name' => $newTag->name,
            'slug' => $newTag->slug
        ]);
    }

    public function testCanDeleteTag(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->delete(route('admin.tags.destroy', $tag->id));
        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('success', __('messages.tag.deleted'));
        $this->assertDatabaseMissing('tags', [
            'name' => $tag->name,
            'slug' => $tag->slug
        ]);
    }

    public function testCanAddTag(): void
    {
        $tag = Tag::factory()->make();

        $response = $this->post(route('admin.tags.store'), [
            'name' => $tag->name,
            'slug' => $tag->slug
        ]);
        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('success', __('messages.tag.added'));
        $this->assertDatabaseHas('tags', [
            'name' => $tag->name,
            'slug' => $tag->slug
        ]);
    }
}
