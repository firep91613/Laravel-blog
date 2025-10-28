<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Composers\ResponseComposer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Comment\UpdateRequest;
use App\Models\Comment;
use App\Services\Admin\CommentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\Factory as ViewFactory;

final class CommentController extends Controller
{
    protected const ACTION_INDEX = 'admin.comments.index';
    protected const VIEW_INDEX = 'admin.comments.index';
    protected const VIEW_EDIT = 'admin.comments.edit';
    protected const VIEW_SHOW = 'admin.comments.show';
    protected const COMMENT_UPDATED_KEY = 'messages.comment.updated';
    protected const COMMENT_DELETED_KEY = 'messages.comment.deleted';
    protected const SUCCESS_KEY = 'success';

    public function index(ViewFactory $view, CommentService $service): View
    {
        return $view->make(self::VIEW_INDEX, [
            'comments' => $service->latestPaginated()
        ]);
    }

    public function show(Comment $comment, ViewFactory $view): View
    {
        return $view->make(self::VIEW_SHOW, [
            'comment' => $comment
        ]);
    }

    public function edit(Comment $comment, ViewFactory $view): View
    {
        return $view->make(self::VIEW_EDIT, [
            'comment' => $comment
        ]);
    }

    public function update(UpdateRequest $request, Comment $comment, CommentService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->update($comment, $request->validated());

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::COMMENT_UPDATED_KEY));
    }

    public function destroy(Comment $comment, CommentService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->delete($comment);

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::COMMENT_DELETED_KEY));
    }
}
