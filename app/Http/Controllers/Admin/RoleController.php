<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Composers\ResponseComposer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\StoreRequest;
use App\Http\Requests\Admin\Role\UpdateRequest;
use App\Models\Role;
use App\Services\Admin\RoleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\Factory as ViewFactory;

final class RoleController extends Controller
{
    protected const ACTION_INDEX = 'admin.roles.index';
    protected const VIEW_INDEX = 'admin.roles.index';
    protected const VIEW_CREATE = 'admin.roles.create';
    protected const VIEW_SHOW = 'admin.roles.show';
    protected const VIEW_EDIT = 'admin.roles.edit';
    protected const ROLE_ADDED_KEY = 'messages.role.added';
    protected const ROLE_UPDATED_KEY = 'messages.role.updated';
    protected const ROLE_DELETED_KEY = 'messages.role.deleted';
    protected const SUCCESS_KEY = 'success';

    public function index(ViewFactory $view, RoleService $service): View
    {
        return $view->make(self::VIEW_INDEX, [
            'roles' => $service->latestPaginated()
        ]);
    }

    public function create(ViewFactory $view): View
    {
        return $view->make(self::VIEW_CREATE);
    }

    public function store(StoreRequest $request, RoleService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->save($request->validated());

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::ROLE_ADDED_KEY));
    }

    public function show(Role $role, ViewFactory $view): View
    {
        return $view->make(self::VIEW_SHOW, [
            'role' => $role
        ]);
    }

    public function edit(Role $role, ViewFactory $view): View
    {
        return $view->make(self::VIEW_EDIT, [
            'role' => $role
        ]);
    }

    public function update(UpdateRequest $request, Role $role, RoleService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->update($role, $request->validated());

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::ROLE_UPDATED_KEY));
    }

    public function destroy(Role $role, RoleService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->delete($role);

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::ROLE_DELETED_KEY));
    }
}
