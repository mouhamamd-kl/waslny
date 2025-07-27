<?php

namespace App\Policies;

use App\Models\RiderCoupon;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RiderCouponPolicy
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
    public function view($user, RiderCoupon $riderCoupon): bool
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
    public function update($user, RiderCoupon $riderCoupon): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, RiderCoupon $riderCoupon): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, RiderCoupon $riderCoupon): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, RiderCoupon $riderCoupon): bool
    {
        return false;
    }
}
