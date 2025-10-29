<?php

namespace App\Composers\Admin;

use App\Services\Admin\CategoryService;
use App\Services\Admin\TagService;

final class PostMetaComposer
{
    public function __construct(
        public CategoryService $categoryService,
        public TagService $tagService
    ) {}
}
