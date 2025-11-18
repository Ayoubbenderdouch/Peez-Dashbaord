<?php

namespace App\Policies;

use App\Models\Activation;
use App\Models\User;

class ActivationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated panel users can view activations
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Activation $activation): bool
    {
        // All authenticated panel users can view individual activations
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated panel users can create activations
        // (Activations are immutable logs, created through activation flow)
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Activation $activation): bool
    {
        // Activations are immutable - no one can update them
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Activation $activation): bool
    {
        // Only admin can delete activations (for data cleanup purposes)
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Activation $activation): bool
    {
        // Only admin can restore activations
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Activation $activation): bool
    {
        // Only admin can permanently delete activations
        return $user->role === 'admin';
    }
}
