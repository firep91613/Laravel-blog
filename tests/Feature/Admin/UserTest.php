<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserTest extends TestCase
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

    public function testShowListOfUsers(): void
    {
        User::factory()->count(20)->create();

        $response = $this->get(route('admin.users.index'));
        $response->assertOk();
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('users');
        $this->assertCount(5, $response->original->getData()['users']);
    }

    public function testShowUserInfo(): void
    {
        $user = User::factory()->create();

        $response = $this->get(route('admin.users.show', $user->id));
        $response->assertOk();
        $response->assertViewIs('admin.users.show');
        $response->assertViewHas('user', function($viewUser) use ($user) {
            return $viewUser->name == $user->name;
        });
    }

    public function testShowUserEditPage(): void
    {
        $user = User::factory()->create();

        $response = $this->get(route('admin.users.edit', $user->id));
        $response->assertOk();
        $response->assertViewIs('admin.users.edit');
        $response->assertSee($user->name);
    }

    public function testCanUpdateUser(): void
    {
        $user = User::factory()->create();
        $newUser = User::factory()->make();
        $newData = [
            'role_id' => $user->role_id,
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];

        $response = $this->put(route('admin.users.update', $user->id), array_merge($newData, $newUser->toArray()));
        $response->assertSessionHas('success', __('messages.user.updated'));
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => $newUser->name,
            'email' => $newUser->email
        ]);
    }

    public function testCanDeleteUser(): void
    {
        $user = User::factory()->create();

        $response = $this->delete(route('admin.users.destroy', $user->id));
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', __('messages.user.deleted'));
        $this->assertDatabaseMissing('users', [
            'email' => $user->email
        ]);
    }

    public function testCanAddUser(): void
    {
        $user = User::factory()->make();

        $response = $this->post(route('admin.users.store'), [
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ]);
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', __('messages.user.added'));
        $this->assertDatabaseHas('users', [
            'email' => $user->email
        ]);
    }

    public function testCanUpdateAvatar(): void
    {
        Storage::fake('public');
        $this->app->bind(ImageService::class, function () {
            return new ImageService(Storage::disk('public'));
        });

        $file = UploadedFile::fake()->image('test-avatar.jpg');
        $user = User::factory()->create();
        $password = Hash::make('123456789');

        $response = $this->put(route('admin.users.update', $user->id), [
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'password' => $password,
            'password_confirmation' => $password,
            'avatar' => $file
        ]);
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', __('messages.user.updated'));
        $this->assertDatabaseHas('users', [
            'avatar' => 'avatars/' . $file->hashName()
        ]);
        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }

    public function testCanStoreAvatar(): void
    {
        Storage::fake('public');
        $this->app->bind(ImageService::class, function () {
            return new ImageService(Storage::disk('public'));
        });

        $file = UploadedFile::fake()->image('test-avatar.jpg');
        $password = Hash::make('123456789');

        $response = $this->post(route('admin.users.store'), [
            'name' => 'test user',
            'email' => 'test@example.com',
            'role_id' => Role::factory()->author()->create()->id,
            'password' => $password,
            'password_confirmation' => $password,
            'avatar' => $file
        ]);
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', __('messages.user.added'));
        $this->assertDatabaseHas('users', [
            'avatar' => 'avatars/' . $file->hashName()
        ]);
        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }
}
