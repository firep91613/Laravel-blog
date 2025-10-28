<?php

namespace App\Composers\Admin;

use App\Services\Admin\CategoryService;
use App\Services\Admin\TagService;

class PostMetaComposer
{
    public function __construct(
        public CategoryService $categoryService,
        public TagService $tagService
    ) {}
}
