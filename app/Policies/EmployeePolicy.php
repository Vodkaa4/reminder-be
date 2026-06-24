<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isManager() || $user->isHrd();
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->isSuperAdmin() || $user->isManager() || $user->isHrd();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isHrd();
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->isSuperAdmin() || $user->isHrd();
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->isSuperAdmin() || $user->isHrd();
    }

    public function restore(User $user, Employee $employee): bool
    {
        return $user->isSuperAdmin() || $user->isHrd();
    }

    public function forceDelete(User $user, Employee $employee): bool
    {
        return $user->isSuperAdmin() || $user->isHrd();
    }
}
