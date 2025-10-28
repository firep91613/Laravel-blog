<?php declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Composers\ResponseComposer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\User\UpdateRequest;
use App\Models\User;
use App\Services\Public\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\View;

final class UserController extends Controller
{
    protected const ACTION_SHOW = 'public.profile.show';
    protected const VIEW_SHOW = 'public.profile.show';
    protected const VIEW_EDIT = 'public.profile.edit';
    protected const USER_UPDATED_KEY = 'messages.common.profile_updated';
    protected const SUCCESS_KEY = 'success';

    public function show(User $user, ViewFactory $view): View
    {
        return $view->make(self::VIEW_SHOW, [
            'user' => $user
        ]);
    }

    public function edit(User $user, ViewFactory $view): View
    {
        return $view->make(self::VIEW_EDIT, [
            'user' => $user
        ]);
    }

    public function update(UpdateRequest $request, User $user, UserService $service, ResponseComposer $composer): RedirectResponse
    {
        $validated = $request->validated();
        $service->update($user, $validated);

        return $composer->redirect->route(self::ACTION_SHOW, $user->id)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::USER_UPDATED_KEY));
    }
}
