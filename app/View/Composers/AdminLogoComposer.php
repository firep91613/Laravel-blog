<?php declare(strict_types=1);

namespace App\View\Composers;

final class AdminLogoComposer extends SettingComposer
{
    protected const CACHE_KEY = 'admin-logo';
    protected const VIEW_KEY = 'adminLogo';
}
