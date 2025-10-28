<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Composers\ResponseComposer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreRequest;
use App\Http\Requests\Admin\Category\UpdateRequest;
use App\Models\Category;
use App\Services\Admin\CategoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\Factory as ViewFactory;

final class CategoryController extends Controller
{
    protected const ACTION_INDEX = 'admin.categories.index';
    protected const VIEW_INDEX = 'admin.categories.index';
    protected const VIEW_CREATE = 'admin.categories.create';
    protected const VIEW_SHOW = 'admin.categories.show';
    protected const VIEW_EDIT = 'admin.categories.edit';
    protected const CATEGORY_ADDED_KEY = 'messages.category.added';
    protected const CATEGORY_UPDATED_KEY = 'messages.category.updated';
    protected const CATEGORY_DELETED_KEY = 'messages.category.deleted';
    protected const SUCCESS_KEY = 'success';

    public function index(ViewFactory $view, CategoryService $service): View
    {
        return $view->make(self::VIEW_INDEX, [
            'categories' => $service->latestPaginated()
        ]);
    }

    public function create(ViewFactory $view): View
    {
        return $view->make(self::VIEW_CREATE);
    }

    public function store(StoreRequest $request, CategoryService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->save($request->validated());

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::CATEGORY_ADDED_KEY));
    }

    public function show(Category $category, ViewFactory $view): View
    {
        return $view->make(self::VIEW_SHOW, [
            'category' => $category
        ]);
    }

    public function edit(Category $category, ViewFactory $view): View
    {
        return $view->make(self::VIEW_EDIT, [
            'category' => $category
        ]);
    }

    public function update(UpdateRequest $request, Category $category, CategoryService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->update($category, $request->validated());

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::CATEGORY_UPDATED_KEY));
    }

    public function destroy(Category $category, CategoryService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->delete($category);

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::CATEGORY_DELETED_KEY));
    }
}
