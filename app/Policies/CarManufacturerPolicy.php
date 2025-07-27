<?php

namespace App\Policies;

use App\Models\CarManufacturer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CarManufacturerPolicy
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
    public function view($user, CarManufacturer $carManufacturer): bool
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
    public function update($user, CarManufacturer $carManufacturer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, CarManufacturer $carManufacturer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, CarManufacturer $carManufacturer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, CarManufacturer $carManufacturer): bool
    {
        return false;
    }
}
