<?php declare(strict_types=1);

namespace App\Services\Public;

use App\Exceptions\DbException;
use App\Models\User;
use App\Services\Admin\RoleService;
use App\Services\ImageService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Translation\Translator;

class RegisterService
{
    protected const REGISTER_ADD_ERROR_KEY = 'messages.exception.register.add';

    public function __construct(
        protected User $model,
        protected Dispatcher $dispatcher,
        protected ImageService $imageService,
        protected Translator $translator,
        protected RoleService $roleService
    ) {}

    public function create(array $data): User
    {
        $data = $this->prepareData($data);

        try {
            $user = $this->model->create($data);
            $this->dispatcher->dispatch(new Registered($user));
            return $user;
        } catch (\Throwable $e) {
            if (isset($data['avatar'])) {
                $this->imageService->delete($data['avatar']);
            }

            $message = $this->translator->get(self::REGISTER_ADD_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    private function prepareData(array $data): array
    {
        if (isset($data['avatar'])) {
            $data['avatar'] = $this->imageService->uploadAvatarImage($data['avatar']);
        }

        $data['role_id'] = $this->roleService->getRoleUserId();

        return $data;
    }
}
