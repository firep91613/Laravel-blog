<?php

namespace Tests\Feature\Public;

use App\Models\Setting;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Setting::factory()->siteTitle()->create();
        Setting::factory()->siteSubtitle()->create();
        Setting::factory()->defaultUsersAvatar()->create();
    }

    public function testUsersCanShowProfiles(): void
    {
        $user = User::factory()->create();
        $userName = $user->name;

        $response = $this->actingAs($user)->get(route('public.profile.show', $user->id));
        $response->assertOk();
        $response->assertViewHas('user', function (User $user) use ($userName) {
            return $user->name == $userName;
        });
    }

    public function testEditorsCanShowSomeoneProfile(): void
    {
        $user = User::factory()->create();
        $editor = User::factory()->roleEditor()->create();
        $userName = $user->name;

        $response = $this->actingAs($editor)->get(route('public.profile.show', $user->id));
        $response->assertOk();
        $response->assertViewHas('user', function (User $user) use ($userName) {
            return $user->name == $userName;
        });
    }

    public function testAdminsCanShowSomeoneProfile(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->roleAdmin()->create();
        $userName = $user->name;

        $response = $this->actingAs($admin)->get(route('public.profile.show', $user->id));
        $response->assertOk();
        $response->assertViewHas('user', function (User $user) use ($userName) {
            return $user->name == $userName;
        });
    }

    public function testUsersCannotShowSomeoneProfile(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->roleUser()->create();

        $response = $this->actingAs($anotherUser)->get(route('public.profile.show', $user->id));
        $response->assertForbidden();
    }

    public function testUsersCanShowProfileEditPage(): void
    {
        $user = User::factory()->create();
        $userName = $user->name;

        $response = $this->actingAs($user)->get(route('public.profile.edit', $user->id));
        $response->assertOk();
        $response->assertViewHas('user', function (User $user) use ($userName) {
            return $user->name == $userName;
        });
    }

    public function testAdminsCanShowSomeoneProfileEditPage(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->roleAdmin()->create();
        $userName = $user->name;

        $response = $this->actingAs($admin)->get(route('public.profile.edit', $user->id));
        $response->assertOk();
        $response->assertViewHas('user', function (User $user) use ($userName) {
            return $user->name == $userName;
        });
    }

    public function testUsersCanUpdateProfile(): void
    {
        $user = User::factory()->create();
        $newUser = User::factory()->make();
        $password = Hash::make('password');
        $passwordData = ['password' => $password, 'password_confirmation' => $password];

        $response = $this->actingAs($user)
            ->put(route('public.profile.update', $user->id), array_merge($newUser->toArray(), $passwordData));
        $this->assertDatabaseHas('users', ['email' => $newUser->email]);
        $response->assertRedirect(route('public.profile.show', $user->id));
        $response->assertSessionHas('success', __('messages.common.profile_updated'));
    }

    public function testAdminsCanUpdateSomeoneProfile(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->roleAdmin()->create();
        $password = Hash::make('123456789');
        $newName = 'Hello world';

        $response = $this->actingAs($admin)
            ->put(route('public.profile.update', $user->id), [
                'name' => $newName,
                'email' => $user->email,
                'password' => $password,
                'password_confirmation' => $password
            ]);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $newName]);
        $response->assertRedirect(route('public.profile.show', $user->id));
        $response->assertSessionHas('success', __('messages.common.profile_updated'));
    }

    public function testEditorsCannotUpdateSomeoneProfile(): void
    {
        $user = User::factory()->create();
        $editor = User::factory()->roleEditor()->create();
        $password = Hash::make('123456789');
        $newName = 'Hello world';

        $this->actingAs($editor)
            ->put(route('public.profile.update', $user->id), [
                'name' => $newName,
                'email' => $user->email,
                'password' => $password,
                'password_confirmation' => $password
            ]);

        $this->assertDatabaseMissing('users', ['id' => $user->id, 'name' => $newName]);
    }

    public function testUsersCannotUpdateSomeoneProfile(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->roleUser()->create();
        $password = Hash::make('123456789');
        $newName = 'Hello world';

        $this->actingAs($anotherUser)->put(route('public.profile.update', $user->id), [
                'name' => $newName,
                'email' => $user->email,
                'password' => $password,
                'password_confirmation' => $password
            ]);

        $this->assertDatabaseMissing('users', ['id' => $user->id, 'name' => $newName]);
    }

    public function testUserCanUpdateAvatar(): void
    {
        Storage::fake('public');
        $this->app->bind(ImageService::class, function () {
            return new ImageService(Storage::disk('public'));
        });

        $file = UploadedFile::fake()->image('test-avatar.jpg');
        $user = User::factory()->create();
        $password = Hash::make('123456789');

        $response = $this->actingAs($user)->put(route('public.profile.update', $user->id), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $password,
            'avatar' => $file
        ]);
        $response->assertRedirect(route('public.profile.show', $user->id));
        $response->assertSessionHas('success', __('messages.common.profile_updated'));
        $this->assertDatabaseHas('users', [
            'avatar' => 'avatars/' . $file->hashName()
        ]);
        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }
}
