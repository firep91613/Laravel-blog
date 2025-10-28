<?php declare(strict_types=1);

namespace App\Services\Admin;

use App\Exceptions\DbException;
use App\Models\Setting;
use App\Services\ImageService;
use Illuminate\Cache\Repository As Cache;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Collection;

class SettingService
{
    protected const ADMIN_LOGO = 'admin-logo';
    protected const DEFAULT_AVATAR = 'default-users-avatar';
    protected const SETTING_UPDATE_ERROR_KEY = 'messages.exception.setting.update';

    public function __construct(
        protected Setting $model,
        protected Connection $db,
        protected ImageService $imageService,
        protected Translator $translator,
        protected Cache $cache
    ) {}

    public function update(Setting $setting, array $data): void
    {
        $data = $this->prepareImageData($data);

        try {
            $oldValue = $this->model->where('slug', array_key_first($data))->first()->value;
            $setting->value = $data[$setting->slug];
            $setting->save();
            $this->cache->put($setting->slug, $setting->value);

            if ($this->model->isImage($oldValue)) {
                $this->imageService->delete($oldValue);
            }
        } catch (\Throwable $e) {
            $this->deleteImages($data);
            $message = $this->translator->get(self::SETTING_UPDATE_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function getAll(): Collection
    {
        return $this->model->all()->sortBy('id');
    }

    private function prepareImageData(array $data): array
    {
        if (isset($data[self::ADMIN_LOGO])) {
            $data[self::ADMIN_LOGO] = $this->imageService->uploadLogoImage($data[self::ADMIN_LOGO]);
        }

        if (isset($data[self::DEFAULT_AVATAR])) {
            $data[self::DEFAULT_AVATAR] = $this->imageService->uploadAvatarImage($data[self::DEFAULT_AVATAR]);
        }

        return $data;
    }

    private function deleteImages(array $data): void
    {
        if (isset($data[self::ADMIN_LOGO])) {
            $this->imageService->delete($data[self::ADMIN_LOGO]);
        }

        if (isset($data[self::DEFAULT_AVATAR])) {
            $this->imageService->delete($data[self::DEFAULT_AVATAR]);
        }
    }

    public function getSetting(string $slug): string
    {
        return $this->model->where('slug', $slug)->first()->value;
    }
}
