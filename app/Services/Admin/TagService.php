<?php declare(strict_types=1);

namespace App\Services\Admin;

use App\Exceptions\DbException;
use App\Models\Tag;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TagService
{
    protected const PER_PAGE = 5;
    protected const TAG_ADD_ERROR_KEY = 'messages.exception.tag.add';
    protected const TAG_UPDATE_ERROR_KEY = 'messages.exception.tag.update';
    protected const TAG_DELETE_ERROR_KEY = 'messages.exception.tag.delete';

    public function __construct(
        protected Tag $model,
        protected Connection $db,
        protected Translator $translator
    ) {}

    public function save(array $data): void
    {
        try {
            $this->model->create($data);
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::TAG_ADD_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function update(Tag $tag, array $data): void
    {
        try {
            $tag->update($data);
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::TAG_UPDATE_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function delete(Tag $tag): void
    {
        try {
            $this->db->transaction(function () use ($tag) {
                $tag->posts()->detach();
                $tag->delete();
            });
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::TAG_DELETE_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function latestPaginated(): LengthAwarePaginator
    {
        return $this->model->orderBy('id')->paginate(self::PER_PAGE);
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }
}
