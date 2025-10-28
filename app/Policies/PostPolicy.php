<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Post;

class PostPolicy
{
    public function before(User $user): bool|null
    {
        if ($user->isAdmin() || $user->isEditor()) {
            return true;
        }

        return null;
    }

    public function create(User $user): bool
    {
        return $user->isAuthor();
    }

    public function store(User $user): bool
    {
        return $user->isAuthor();
    }

    public function edit(User $user, Post $post): bool
    {
        return $this->canModifyPost($user, $post);
    }

    public function update(User $user, Post $post): bool
    {
        return $this->canModifyPost($user, $post);
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->canModifyPost($user, $post);
    }

    private function canModifyPost(User $user, Post $post): bool
    {
        return $user->isAuthor() && $user->id == $post->user_id;
    }
}
