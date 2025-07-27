<?php

namespace App\Policies;

use App\Models\RiderSavedLocation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RiderSavedLocationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, RiderSavedLocation $riderSavedLocation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, RiderSavedLocation $riderSavedLocation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, RiderSavedLocation $riderSavedLocation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, RiderSavedLocation $riderSavedLocation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, RiderSavedLocation $riderSavedLocation): bool
    {
        return false;
    }
}
