<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function before(User $user): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function view(User $user, User $profileUser): bool
    {
        return !$user->isUser() || $this->canModifyProfile($user, $profileUser);
    }

    public function edit(User $user, User $profileUser): bool
    {
        return $this->canModifyProfile($user, $profileUser);
    }

    public function update(User $user, User $profileUser): bool
    {
        return $this->canModifyProfile($user, $profileUser);
    }

    private function canModifyProfile(User $user, User $profileUser): bool
    {
        return $user->id == $profileUser->id;
    }
}
