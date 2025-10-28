<?php declare(strict_types=1);

namespace App\View\Composers;

final class FrontTitleComposer extends SettingComposer
{
    protected const CACHE_KEY = 'site-title';
    protected const VIEW_KEY = 'siteTitle';
}
