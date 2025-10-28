<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    public function definition()
    {
        return [
            'slug' => $this->faker->unique()->slug,
            'name' => $this->faker->word,
            'value' => $this->faker->word,
        ];
    }

    public function adminLogo()
    {
        return $this->state([
            'slug' => 'admin-logo',
            'name' => 'Логотип админки',
            'value' => 'logo/x0uhRxiLosT9c7iNiO1w41Ix8kJHUoexqGn9PViB.png',
        ]);
    }

    public function siteTitle()
    {
        return $this->state([
            'slug' => 'site-title',
            'name' => 'Заголовок сайта',
            'value' => 'Блог на Laravel',
        ]);
    }

    public function siteSubtitle()
    {
        return $this->state([
            'slug' => 'site-subtitle',
            'name' => 'Подзаголовок сайта',
            'value' => 'Чистому коду быть!',
        ]);
    }

    public function defaultUsersAvatar()
    {
        return $this->state([
            'slug' => 'default-users-avatar',
            'name' => 'Аватар по умолчанию',
            'value' => 'avatars/SiVbnTBMneLTF7jSe7TotG3mQhyJxrYtktURyyJn.png',
        ]);
    }
}

