<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Composers\ResponseComposer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreRequest;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Models\User;
use App\Services\Admin\RoleService;
use App\Services\Admin\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\Factory as ViewFactory;

final class UserController extends Controller
{
    protected const ACTION_INDEX = 'admin.users.index';
    protected const VIEW_INDEX = 'admin.users.index';
    protected const VIEW_CREATE = 'admin.users.create';
    protected const VIEW_SHOW = 'admin.users.show';
    protected const VIEW_EDIT = 'admin.users.edit';
    protected const USER_ADDED_KEY = 'messages.user.added';
    protected const USER_UPDATED_KEY = 'messages.user.updated';
    protected const USER_DELETED_KEY = 'messages.user.deleted';
    protected const SUCCESS_KEY = 'success';

    public function index(Request $request, UserService $service, ViewFactory $view): View
    {
        return $view->make(self::VIEW_INDEX, [
            'users' => $service->getFilteredUsers($request->all()),
        ]);
    }

    public function create(RoleService $service, ViewFactory $view): View
    {
        return $view->make(self::VIEW_CREATE, [
            'roles' => $service->getAll()
        ]);
    }

    public function store(StoreRequest $request, UserService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->save($request->validated());

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::USER_ADDED_KEY));
    }

    public function show(User $user, ViewFactory $view): View
    {
        return $view->make(self::VIEW_SHOW, [
            'user' => $user
        ]);
    }

    public function edit(User $user, RoleService $service, ViewFactory $view): View
    {
        return $view->make(self::VIEW_EDIT, [
            'user' => $user,
            'roles' => $service->getAll()
        ]);
    }

    public function update(UpdateRequest $request, User $user, UserService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->update($user, $request->validated());

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::USER_UPDATED_KEY));
    }

    public function destroy(User $user, UserService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->delete($user);

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::USER_DELETED_KEY));
    }
}
