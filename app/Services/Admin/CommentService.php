<?php declare(strict_types=1);

namespace App\Services\Admin;

use App\Exceptions\DbException;
use App\Models\Comment;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentService
{
    protected const PER_PAGE = 5;
    protected const DELETED_MESSAGE = 'Комментарий был удален';
    protected const COMMENT_UPDATE_ERROR_KEY = 'messages.exception.comment.update';
    protected const COMMENT_DELETE_ERROR_KEY = 'messages.exception.comment.delete';

    public function __construct(
        protected Comment $model,
        protected Connection $db,
        protected Translator $translator
    ) {}

    public function update(Comment $comment, array $data): void
    {
        try {
            $comment->update($data);
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::COMMENT_UPDATE_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function delete(Comment $comment): void
    {
        try {
            $this->db->transaction(function () use ($comment) {
                $replies = $comment->replies()->count();

                if ($replies > 0) {
                    $comment->content = self::DELETED_MESSAGE;
                    $comment->hide_edit_buttons = true;
                    $comment->save();
                } else {
                    $comment->delete();
                }
            });
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::COMMENT_DELETE_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function latestPaginated(): LengthAwarePaginator
    {
        return $this->model->orderBy('id', 'desc')->paginate(self::PER_PAGE);
    }

    public function getLastComments(int $count): Collection
    {
        return $this->model->orderBy('created_at', 'desc')->take($count)->get();
    }
}
