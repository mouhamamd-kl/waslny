<?php

namespace App\Traits\General;



use App\Enums\SuspensionReason;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Model;

/**
 * Suspendable trait provides functionality for suspending and reinstating models.
 *
 * @package App\Traits
 */
trait Suspendable
{
    /**
     * Suspend the model with a given reason.
     *
     * @param SuspensionReason $suspensionReason
     * @return void
     * @throws \RuntimeException If the model doesn't use the required status functionality
     */
    public function suspend(SuspensionReason $suspensionReason): void
    {
        $this->validateSuspendable();

        $this->update([
            'suspended' => true,
            'suspension_reason_id' => $suspensionReason->id,
        ]);
    }

    /**
     * Reinstate the model after suspension.
     *
     * @return void
     * @throws \RuntimeException If the model doesn't use the required status functionality
     */
    public function reinstate(): void
    {
        $this->validateSuspendable();

        $this->update([
            'suspended' => false,
            'suspension_reason_id' => null,
        ]);
    }

    /**
     * Check if the model is currently suspended.
     *
     * @return bool
     */
    public function isSuspended(): bool
    {
        return (bool) $this->suspended;
    }

    /**
     * Get the suspension reason for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function suspensionReason()
    {
        return $this->belongsTo(SuspensionReason::class);
    }

    /**
     * Validate that the model has required properties and methods.
     *
     * @return void
     * @throws \RuntimeException
     */
    protected function validateSuspendable(): void
    {
        if (!method_exists($this, 'setStatus')) {
            throw new \RuntimeException(
                'The model must implement the setStatus method to use the Suspendable trait.'
            );
        }

        if (!property_exists($this, 'suspended') || !property_exists($this, 'suspension_reason_id')) {
            throw new \RuntimeException(
                'The model must have "suspended" and "suspension_reason_id" attributes to use the Suspendable trait.'
            );
        }
    }
}
