<?php

namespace App\Policies;

use App\Models\ReminderRule;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReminderRulePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ReminderRule $reminderRule): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, ReminderRule $reminderRule): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, ReminderRule $reminderRule): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, ReminderRule $reminderRule): bool
    {
        return $user->isSuperAdmin();
    }

    public function forceDelete(User $user, ReminderRule $reminderRule): bool
    {
        return $user->isSuperAdmin();
    }
}
