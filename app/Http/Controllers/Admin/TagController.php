<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Composers\ResponseComposer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tag\StoreRequest;
use App\Http\Requests\Admin\Tag\UpdateRequest;
use App\Models\Tag;
use App\Services\Admin\TagService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\Factory as ViewFactory;

final class TagController extends Controller
{
    protected const ACTION_INDEX = 'admin.tags.index';
    protected const VIEW_INDEX = 'admin.tags.index';
    protected const VIEW_CREATE = 'admin.tags.create';
    protected const VIEW_SHOW = 'admin.tags.show';
    protected const VIEW_EDIT = 'admin.tags.edit';
    protected const TAG_ADDED_KEY = 'messages.tag.added';
    protected const TAG_UPDATED_KEY = 'messages.tag.updated';
    protected const TAG_DELETED_KEY = 'messages.tag.deleted';
    protected const SUCCESS_KEY = 'success';

    public function index(ViewFactory $view, TagService $service): View
    {
        return $view->make(self::VIEW_INDEX, [
            'tags' => $service->latestPaginated()
        ]);
    }

    public function create(ViewFactory $view): View
    {
        return $view->make(self::VIEW_CREATE);
    }

    public function store(StoreRequest $request, TagService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->save($request->validated());

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::TAG_ADDED_KEY));
    }

    public function show(Tag $tag, ViewFactory $view): View
    {
        return $view->make(self::VIEW_SHOW, [
            'tag' => $tag
        ]);
    }

    public function edit(Tag $tag, ViewFactory $view): View
    {
        return $view->make(self::VIEW_EDIT, [
            'tag' => $tag
        ]);
    }

    public function update(UpdateRequest $request, Tag $tag, TagService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->update($tag, $request->validated());

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::TAG_UPDATED_KEY));
    }

    public function destroy(Tag $tag, TagService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->delete($tag);

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::TAG_DELETED_KEY));
    }
}
