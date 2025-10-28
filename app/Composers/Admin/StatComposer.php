<?php declare(strict_types=1);

namespace App\Composers\Admin;

use App\Services\Admin\CommentService;
use App\Services\Admin\PostService;
use App\Services\Admin\UserService;

final class StatComposer
{
    public function __construct(
        public PostService $postService,
        public UserService $userService,
        public CommentService $commentService
    ) {}
}
