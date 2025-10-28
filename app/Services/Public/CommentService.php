<?php declare(strict_types=1);

namespace App\Services\Public;

use App\Exceptions\DbException;
use App\Models\Comment;
use App\Http\Resources\CommentResource;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentService
{
    const DELETED_MESSAGE = 'Комментарий был удален';
    protected const COMMENT_ADD_ERROR_KEY = 'messages.exception.comment.add';
    protected const COMMENT_UPDATE_ERROR_KEY = 'messages.exception.comment.update';

    public function __construct(
        protected Comment $model,
        protected Translator $translator
    ) {}

    public function getById(int $id): Comment
    {
        return $this->model->findOrFail($id);
    }

    public function save(array $data): JsonResource
    {
        try {
            $comment =  $this->model->create($data);

            return new CommentResource($comment);
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::COMMENT_ADD_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function update(array $data, int $id): JsonResource
    {
        $comment = $this->getById($id);

        try {
            $comment->update($data);
            $comment->refresh();

            return new CommentResource($comment);
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::COMMENT_UPDATE_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function delete(int $id, bool $canDelete): array
    {
        $comment = $this->getById($id);
        $result = [];

        if ($canDelete) {
            $comment->delete();
            $result['deleted'] = true;
        } else {
            $message = self::DELETED_MESSAGE;
            $comment->content = $message;
            $comment->hide_edit_buttons = true;
            $comment->save();
            $result['deleted'] = false;
            $result['message'] = $message;
        }

        return $result;
    }
}

