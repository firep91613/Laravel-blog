<?php declare(strict_types=1);

namespace App\Services\Public;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TagService
{
    protected const PER_PAGE = 5;

    public function __construct(
        protected Tag $model
    ) {}

    public function latestPaginated(): LengthAwarePaginator
    {
        return $this->model->orderBy('id', 'desc')->paginate(self::PER_PAGE);
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }
}
