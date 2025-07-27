<?php

namespace App\Policies;

use App\Models\RiderFolder;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RiderFolderPolicy
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
    public function view($user, RiderFolder $riderFolder): bool
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
    public function update($user, RiderFolder $riderFolder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, RiderFolder $riderFolder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, RiderFolder $riderFolder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, RiderFolder $riderFolder): bool
    {
        return false;
    }
}
