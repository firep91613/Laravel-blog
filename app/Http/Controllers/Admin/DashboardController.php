<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Composers\Admin\StatComposer;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\View\Factory as ViewFactory;

final class DashboardController extends Controller
{
    protected const ACTION_INDEX = 'admin.dashboard';
    protected const LAST_COMMENTS_COUNT = 5;
    protected const LAST_USERS_COUNT = 5;

    public function __invoke(StatComposer $composer, ViewFactory $view): View
    {
        return $view->make(self::ACTION_INDEX, [
            'usersCount' => $composer->userService->count(),
            'postsCount' => $composer->postService->count(),
            'latestUsers' => $composer->userService->getLastUsers(self::LAST_USERS_COUNT),
            'latestComments' => $composer->commentService->getLastComments(self::LAST_COMMENTS_COUNT)
        ]);
    }
}
