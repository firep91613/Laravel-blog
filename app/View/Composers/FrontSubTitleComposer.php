<?php declare(strict_types=1);

namespace App\View\Composers;

final class FrontSubTitleComposer extends SettingComposer
{
    protected const CACHE_KEY = 'site-subtitle';
    protected const VIEW_KEY = 'siteSubTitle';
}
