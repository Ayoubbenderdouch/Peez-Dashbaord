<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only admin and manager can view users
        return in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Admin and manager can view all users
        // Vendors can only view themselves
        return in_array($user->role, ['admin', 'manager']) || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin can create users
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Admin can update any user
        // Manager can update vendors only
        // Vendors can update themselves only
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'manager') {
            return $model->role === 'vendor';
        }

        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Only admin can delete users
        // Cannot delete yourself
        return $user->role === 'admin' && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        // Only admin can restore users
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Only admin can permanently delete users
        return $user->role === 'admin' && $user->id !== $model->id;
    }
}
