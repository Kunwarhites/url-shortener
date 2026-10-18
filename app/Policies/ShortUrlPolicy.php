<?php

namespace App\Policies;

use App\Models\User;

class ShortUrlPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isMember();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isMember();
    }
}
