<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Composers\Admin\PostMetaComposer;
use App\Composers\ResponseComposer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Post\StoreRequest;
use App\Http\Requests\Admin\Post\UpdateRequest;
use App\Models\Post;
use App\Services\Admin\PostService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\Factory as ViewFactory;

final class PostController extends Controller
{
    protected const ACTION_INDEX = 'admin.posts.index';
    protected const VIEW_INDEX = 'admin.posts.index';
    protected const VIEW_CREATE = 'admin.posts.create';
    protected const VIEW_SHOW = 'admin.posts.show';
    protected const VIEW_EDIT = 'admin.posts.edit';
    protected const POST_ADDED_KEY = 'messages.post.added';
    protected const POST_UPDATED_KEY = 'messages.post.updated';
    protected const POST_DELETED_KEY = 'messages.post.deleted';
    protected const SUCCESS_KEY = 'success';

    public function index(ViewFactory $view, PostService $service): View
    {
        return $view->make(self::VIEW_INDEX, [
            'posts' => $service->latestPaginated()
        ]);
    }

    public function create(PostMetaComposer $composer, ViewFactory $view): View
    {
        return $view->make(self::VIEW_CREATE, [
            'categories' => $composer->categoryService->getAll(),
            'tags' => $composer->tagService->getAll()
        ]);
    }

    public function store(StoreRequest $request, PostService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->save($request->validated());

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::POST_ADDED_KEY));
    }

    public function show(Post $post, ViewFactory $view): View
    {
        return $view->make(self::VIEW_SHOW, [
            'post' => $post
        ]);
    }

    public function edit(Post $post, PostMetaComposer $composer, ViewFactory $view): View
    {
        return $view->make(self::VIEW_EDIT, [
            'post' => $post,
            'categories' => $composer->categoryService->getAll(),
            'tags' => $composer->tagService->getAll()
        ]);
    }

    public function update(UpdateRequest $request, Post $post, PostService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->update($post, $request->validated());

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::POST_UPDATED_KEY));
    }

    public function destroy(Post $post, PostService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->delete($post);

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::POST_DELETED_KEY));
    }
}
