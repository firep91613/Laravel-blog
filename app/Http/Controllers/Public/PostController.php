<?php declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Composers\Public\PostMetaComposer;
use App\Composers\ResponseComposer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Post\StoreRequest;
use App\Http\Requests\Public\Post\UpdateRequest;
use App\Models\Post;
use App\Services\Public\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\View;

final class PostController extends Controller
{
    protected const ACTION_INDEX = 'public.posts.index';
    protected const VIEW_INDEX = 'public.posts.index';
    protected const VIEW_CREATE = 'public.posts.create';
    protected const VIEW_SHOW = 'public.posts.show';
    protected const VIEW_EDIT = 'public.posts.edit';
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

    public function show(Post $post, PostService $service, ViewFactory $view): View
    {
        return $view->make(self::VIEW_SHOW, [
            'post' => $post,
            'comments' => $service->getComments($post)
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
