<?php

namespace App\Policies;

use App\Models\Permit;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PermitPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isManager() || $user->isLegal();
    }

    public function view(User $user, Permit $permit): bool
    {
        return $user->isSuperAdmin() || $user->isManager() || $user->isLegal();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isLegal();
    }

    public function update(User $user, Permit $permit): bool
    {
        return $user->isSuperAdmin() || $user->isLegal();
    }

    public function delete(User $user, Permit $permit): bool
    {
        return $user->isSuperAdmin() || $user->isLegal();
    }

    public function restore(User $user, Permit $permit): bool
    {
        return $user->isSuperAdmin() || $user->isLegal();
    }

    public function forceDelete(User $user, Permit $permit): bool
    {
        return $user->isSuperAdmin() || $user->isLegal();
    }
}
