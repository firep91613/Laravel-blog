<?php declare(strict_types=1);

namespace App\Services\Public;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        protected Category $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->all();
    }
}
