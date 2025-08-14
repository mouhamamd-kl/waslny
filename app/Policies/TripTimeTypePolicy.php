<?php

namespace App\Policies;

use App\Models\TripTimeType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TripTimeTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, TripTimeType $tripTimeType): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, TripTimeType $tripTimeType): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, TripTimeType $tripTimeType): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, TripTimeType $tripTimeType): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, TripTimeType $tripTimeType): bool
    {
        return true;
    }
}
