<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;

class SubscriptionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated panel users can view subscriptions
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Subscription $subscription): bool
    {
        // All authenticated panel users can view individual subscriptions
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin and manager can create subscriptions directly
        // Vendors use the activation flow instead
        return in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Subscription $subscription): bool
    {
        // Only admin and manager can update subscriptions
        return in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Subscription $subscription): bool
    {
        // Only admin can delete subscriptions
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Subscription $subscription): bool
    {
        // Only admin can restore subscriptions
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Subscription $subscription): bool
    {
        // Only admin can permanently delete subscriptions
        return $user->role === 'admin';
    }
}
