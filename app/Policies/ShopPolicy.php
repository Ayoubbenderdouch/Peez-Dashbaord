<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ShopPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated panel users can view shops
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Shop $shop): bool
    {
        // All authenticated panel users can view individual shops
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin and manager can create shops
        return in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Shop $shop): bool
    {
        // Only admin and manager can update shops
        return in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Shop $shop): bool
    {
        // Only admin can delete shops
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Shop $shop): bool
    {
        // Only admin can restore shops
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Shop $shop): bool
    {
        // Only admin can permanently delete shops
        return $user->role === 'admin';
    }

    /**
     * Apply scoping for managers (limit to specific neighborhoods)
     * This can be used in Filament Resource queries
     */
    public static function scopeForUser(Builder $query, User $user): Builder
    {
        // Admin sees everything
        if ($user->role === 'admin') {
            return $query;
        }

        // Manager: scope to specific neighborhoods (you can add neighborhood_ids to users table)
        // For now, we'll just return the query as-is
        // TODO: Add neighborhood_ids field to users table for manager scoping
        if ($user->role === 'manager') {
            // Example: $query->whereIn('neighborhood_id', $user->managed_neighborhood_ids);
            return $query;
        }

        // Vendors see all shops (but can't edit them per create/update policies)
        return $query;
    }
}
