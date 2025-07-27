<?php

namespace App\Policies;

use App\Models\CarServiceLevel;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CarServiceLevelPolicy
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
    public function view($user, CarServiceLevel $carServiceLevel): bool
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
    public function update($user, CarServiceLevel $carServiceLevel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, CarServiceLevel $carServiceLevel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, CarServiceLevel $carServiceLevel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, CarServiceLevel $carServiceLevel): bool
    {
        return false;
    }
}
