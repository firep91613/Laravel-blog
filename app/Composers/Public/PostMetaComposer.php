<?php

namespace App\Composers\Public;

use App\Services\Public\CategoryService;
use App\Services\Public\TagService;

class PostMetaComposer
{
    public function __construct(
        public CategoryService $categoryService,
        public TagService $tagService
    ) {}
}
