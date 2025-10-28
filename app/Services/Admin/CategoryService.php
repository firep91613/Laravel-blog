<?php declare(strict_types=1);

namespace App\Services\Admin;

use App\Exceptions\DbException;
use App\Models\Category;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService
{
    protected const PER_PAGE = 5;
    protected const CATEGORY_ADD_ERROR_KEY = 'messages.exception.category.add';
    protected const CATEGORY_UPDATE_ERROR_KEY = 'messages.exception.category.update';
    protected const CATEGORY_DELETE_ERROR_KEY = 'messages.exception.category.delete';

    public function __construct(
        protected Category $model,
        protected Connection $db,
        protected Translator $translator
    ) {}

    public function save(array $data): void
    {
        try {
            $this->model->create($data);
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::CATEGORY_ADD_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function update(Category $category, array $data): void
    {
        try {
            $category->update($data);
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::CATEGORY_UPDATE_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function delete(Category $category): void
    {
        try {
            $category->delete();
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::CATEGORY_DELETE_ERROR_KEY) . $e->getMessage();
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
