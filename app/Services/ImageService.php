<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Filesystem\Filesystem as Storage;

class ImageService
{
    protected const AVATARS_FOLDER = 'avatars';
    protected const POSTS_FOLDER = 'posts';
    protected const LOGO_FOLDER = 'logo';
    protected const DISK = 'public';

    public function __construct(
        protected Storage $storage
    ) {}

    public function upload(UploadedFile $file, string $folder): string
    {
        return $this->storage->putFile($folder, $file, self::DISK);
    }

    public function uploadAvatarImage(UploadedFile $file): string
    {
        return $this->upload($file, self::AVATARS_FOLDER);
    }

    public function uploadPostImage(UploadedFile $file): string
    {
        return $this->upload($file, self::POSTS_FOLDER);
    }

    public function uploadLogoImage(UploadedFile $file): string
    {
        return $this->upload($file, self::LOGO_FOLDER);
    }

    public function delete(string $file): bool
    {
        return $this->storage->delete($file);
    }
}
