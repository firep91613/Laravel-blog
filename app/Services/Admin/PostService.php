<?php declare(strict_types=1);

namespace App\Services\Admin;

use App\Exceptions\DbException;
use App\Models\Post;
use App\Services\ImageService;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Connection;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    protected const PER_PAGE = 5;
    protected const POST_ADD_ERROR_KEY = 'messages.exception.post.add';
    protected const POST_UPDATE_ERROR_KEY = 'messages.exception.post.update';
    protected const POST_DELETE_ERROR_KEY = 'messages.exception.post.delete';

    public function __construct(
        protected Post $model,
        protected Connection $db,
        protected ImageService $imageService,
        protected Translator $translator
    ) {}

    public function save(array $data): void
    {
        try {
            $this->db->transaction(function () use (&$data) {
                [$data, $tag_id] = $this->prepareData($data);
                $post = $this->model->create($data);

                if (isset($tag_id)) {
                    $post->tags()->attach($tag_id);
                }
            });
        } catch (\Throwable $e) {
            if (isset($data['image'])) {
                $this->imageService->delete($data['image']);
            }

            $message = $this->translator->get(self::POST_ADD_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function update(Post $post, array $data): void
    {
        try {
            $oldImage = $post->image;

            $this->db->transaction(function () use ($post, &$data) {
                [$data, $tag_id] = $this->prepareData($data);
                $post->update($data);

                if (isset($tag_id)) {
                    $post->tags()->sync($tag_id);
                }
            });

            if (!is_null($oldImage) && isset($data['image'])) {
                $this->imageService->delete($oldImage);
            }
        } catch (\Throwable $e) {
            if (isset($data['image'])) {
                $this->imageService->delete($data['image']);
            }

            $message = $this->translator->get(self::POST_UPDATE_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function delete(Post $post): void
    {
        try {
            $image = $post->image;

            $this->db->transaction(function () use ($post) {
                $post->tags()->detach();
                $post->category()->dissociate();
                $post->delete();
            });

            if (!is_null($image)) {
                $this->imageService->delete($image);
            }
        } catch (\Throwable $e) {
            $message = $this->translator->get(self::POST_DELETE_ERROR_KEY) . $e->getMessage();
            throw new DbException($message);
        }
    }

    public function latestPaginated(): LengthAwarePaginator
    {
        return $this->model->orderBy('id', 'desc')->paginate(self::PER_PAGE);
    }

    public function count(): int
    {
        return $this->model->count();
    }

    private function prepareData(array $data): array
    {
        $tag_id = null;

        if (isset($data['image'])) {
            $data['image'] = $this->imageService->uploadPostImage($data['image']);
        }

        if (isset($data['tag_id'])) {
            $tag_id = $data['tag_id'];
            unset($data['tag_id']);
        }

        return [$data, $tag_id];
    }
}
