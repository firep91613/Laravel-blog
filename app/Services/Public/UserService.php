<?php declare(strict_types=1);

namespace App\Services\Public;

use App\Exceptions\DbException;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Contracts\Translation\Translator;

class UserService
{
    protected const USER_UPDATE_ERROR_KEY = 'messages.exception.user.update';

    public function __construct(
        protected User $model,
        protected ImageService $imageService,
        protected Translator $translator
    ) {}

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

    private function prepareAvatar(array $data): array
    {
        if (isset($data['avatar'])) {
            $data['avatar'] = $this->imageService->uploadAvatarImage($data['avatar']);
        }

        return $data;
    }
}
