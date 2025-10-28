<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::factory()->adminLogo()->create();
        Setting::factory()->defaultUsersAvatar()->create();
        Setting::factory()->siteTitle()->create();
        Setting::factory()->siteSubtitle()->create();
    }
}
