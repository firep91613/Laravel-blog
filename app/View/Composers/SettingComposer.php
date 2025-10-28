<?php

namespace App\View\Composers;

use App\Services\Admin\SettingService;
use Illuminate\Cache\Repository as Cache;
use Illuminate\View\View;

abstract class SettingComposer
{
    protected const CACHE_KEY = '';
    protected const VIEW_KEY = '';

    public function __construct(
        protected Cache $cache,
        protected SettingService $service
    ) {}

    public function compose(View $view): void
    {
        if (!$this->cache->has(static::CACHE_KEY)) {
            $this->cache->put(static::CACHE_KEY, $this->service->getSetting(static::CACHE_KEY));
        }

        $view->with(static::VIEW_KEY, $this->cache->get(static::CACHE_KEY));
    }
}
