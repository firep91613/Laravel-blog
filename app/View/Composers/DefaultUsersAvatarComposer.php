<?php declare(strict_types=1);

namespace App\View\Composers;

final class DefaultUsersAvatarComposer extends SettingComposer
{
    protected const CACHE_KEY = 'default-users-avatar';
    protected const VIEW_KEY = 'defaultAvatar';
}
