<?php

namespace App\Composers\Public;

use App\Services\Public\CategoryService;
use App\Services\Public\TagService;

final class PostMetaComposer
{
    public function __construct(
        public CategoryService $categoryService,
        public TagService $tagService
    ) {}
}
