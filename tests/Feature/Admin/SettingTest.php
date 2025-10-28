<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->roleAdmin()->create();
        $this->actingAs($this->admin);
    }

    public function testShowListOfSettings(): void
    {
        Setting::factory()->adminLogo()->create();
        Setting::factory()->defaultUsersAvatar()->create();
        Setting::factory()->siteTitle()->create();
        Setting::factory()->siteSubtitle()->create();

        $response = $this->get(route('admin.settings.index'));
        $response->assertOk();
        $response->assertViewIs('admin.settings.index');
        $response->assertViewHas('settings');
        $this->assertCount(4, $response->original->getData()['settings']);
    }

    public function testCanEditAdminLogoSetting(): void
    {
        Setting::factory()->defaultUsersAvatar()->create();
        $setting = Setting::factory()->adminLogo()->create();

        $response = $this->get(route('admin.settings.edit', $setting->slug));
        $response->assertOk();
        $response->assertViewIs('admin.settings.edit');
        $response->assertViewHas('setting');
        $response->assertSee($setting->value);
    }

    public function testCanEditDefaultAvatarSetting(): void
    {
        Setting::factory()->adminLogo()->create();
        $setting = Setting::factory()->defaultUsersAvatar()->create();

        $response = $this->get(route('admin.settings.edit', $setting->slug));
        $response->assertOk();
        $response->assertViewIs('admin.settings.edit');
        $response->assertViewHas('setting');
        $response->assertSee($setting->value);
    }

    public function testCanEditSiteTitleSetting(): void
    {
        Setting::factory()->adminLogo()->create();
        Setting::factory()->defaultUsersAvatar()->create();
        $setting = Setting::factory()->siteTitle()->create();

        $response = $this->get(route('admin.settings.edit', $setting->slug));
        $response->assertOk();
        $response->assertViewIs('admin.settings.edit');
        $response->assertViewHas('setting');
        $response->assertSee($setting->value);
    }

    public function testCanEditSiteSubTitleSetting(): void
    {
        Setting::factory()->adminLogo()->create();
        Setting::factory()->defaultUsersAvatar()->create();
        $setting = Setting::factory()->siteSubtitle()->create();

        $response = $this->get(route('admin.settings.edit', $setting->slug));
        $response->assertOk();
        $response->assertViewIs('admin.settings.edit');
        $response->assertViewHas('setting');
        $response->assertSee($setting->value);
    }

    public function testCanUpdateSiteTitleSetting(): void
    {
        Setting::factory()->adminLogo()->create();
        Setting::factory()->defaultUsersAvatar()->create();
        $setting = Setting::factory()->siteTitle()->create();

        $response = $this->put(route('admin.settings.update', $setting->slug), [
            $setting->slug => 'New Title',
        ]);
        $response->assertSessionHas('success', __('messages.setting.updated'));
        $response->assertRedirect(route('admin.settings.index'));
        $this->assertDatabaseHas('settings', [
            'value' => 'New Title'
        ]);
    }

    public function testCanUpdateSiteSubTitleSetting(): void
    {
        Setting::factory()->adminLogo()->create();
        Setting::factory()->defaultUsersAvatar()->create();
        $setting = Setting::factory()->siteSubtitle()->create();

        $response = $this->put(route('admin.settings.update', $setting->slug), [
            $setting->slug => "New SubTitle",
        ]);
        $response->assertSessionHas('success', __('messages.setting.updated'));
        $response->assertRedirect(route('admin.settings.index'));
        $this->assertDatabaseHas('settings', [
            'value' => 'New SubTitle'
        ]);
    }
}
