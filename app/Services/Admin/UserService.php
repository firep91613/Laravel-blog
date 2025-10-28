<?php declare(strict_types=1);

namespace App\Services\Admin;

use App\Exceptions\DbException;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    protected const PER_PAGE = 5;
    protected const USER_ADD_ERROR_KEY = 'messages.exception.user.add';
    protected const USER_UPDATE_ERROR_KEY = 'messages.exception.user.update';
    protected const USER_DELETE_ERROR_KEY = 'messages.exception.user.delete';

    public function __construct(
        protected User $model,
        protected ImageService $imageService,
        protected Translator $translator
    ) {}

    public function save(array $data): void
    {
        $data = $this->prepareAvatar($data);

        try {
            $this->model->create($data);
        } catch (\Throwable $e) {
            if (isset($data['avatar'])) {
                $this->imageService->delete($data['avatar']);
            }

            $message = $this->translator->get(self::USER_ADD_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function update(User $user, array $data): void
    {
        $data = $this->prepareAvatar($data);

        try {
            $user->update($data);
        } catch (\Throwable $e) {
            if (isset($data['avatar'])) {
                $this->imageService->delete($data['avatar']);
            }

            $message = $this->translator->get(self::USER_UPDATE_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function delete(User $user): void
    {
        try {
            $avatar = $user->avatar;
            $user->delete();

            if ($avatar) {
                $this->imageService->delete($user->avatar);
            }
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::USER_DELETE_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function getFilteredUsers(array $params): LengthAwarePaginator
    {
        $query = $this->model->orderByDesc('id');

        if (isset($params['id'])) {
            $query->where('id', $params['id']);
        }

        if (isset($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['email'])) {
            $query->where('email', 'like', '%' . $params['email'] . '%');
        }

        return $query->paginate(self::PER_PAGE);
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function getLastUsers(int $count): Collection
    {
        return $this->model->orderBy('created_at', 'desc')->take($count)->get();
    }

    public function count(): int
    {
        return $this->model->count();
    }

    private function prepareAvatar(array $data): array
    {
        if (isset($data['avatar'])) {
            $data['avatar'] = $this->imageService->uploadAvatarImage($data['avatar']);
        }

        return $data;
    }
}
