<?php declare(strict_types=1);

namespace App\Services\Admin;

use App\Exceptions\DbException;
use App\Models\Role;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService
{
    protected const PER_PAGE = 5;
    protected const ROLE_USER = 'user';
    protected const ROLE_ADD_ERROR_KEY = 'messages.exception.role.add';
    protected const ROLE_UPDATE_ERROR_KEY = 'messages.exception.role.update';
    protected const ROLE_DELETE_ERROR_KEY = 'messages.exception.role.delete';

    public function __construct(
        protected Role $model,
        protected Connection $db,
        protected Translator $translator
    ) {}

    public function save(array $data): void
    {
        try {
            $this->model->create($data);
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::ROLE_ADD_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function update(Role $role, array $data): void
    {
        try {
            $role->update($data);
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::ROLE_UPDATE_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function delete(Role $role): void
    {
        try {
            $this->db->transaction(function () use ($role) {
                $role->users()->update(['role_id' => $this->getRoleUserId()]);
                $role->delete();
            });
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::ROLE_DELETE_ERROR_KEY) . $e->getMessage();
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

    public function getRoleUserId(): int
    {
        return $this->model->where('name', self::ROLE_USER)->first()->id;
    }
}
