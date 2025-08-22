<?php

namespace App\Traits\General;



use App\Enums\SuspensionReason;
use App\Enums\UserStatus;
use App\Models\AccountSuspension;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
    // In Rider/Driver models
    public function suspendForever($suspendId): AccountSuspension
    {
        if ($existingSuspension = $this->activeSuspension()) {
            return $existingSuspension;
        }
        return $this->suspensions()->create([
            'suspension_id' => $suspendId,
            'is_permanent' => true,
        ]);
    }

    public function suspendTemporarily($suspendId, Carbon $suspended_until): AccountSuspension
    {
        if ($existingSuspension = $this->activeSuspension()) {
            return $existingSuspension;
        }
        return $this->suspensions()->create([
            'suspended_until' => $suspended_until,
            'suspension_id' => $suspendId,
        ]);
    }

    /**
     * Reinstate the model after suspension.
     *
     * @return void
     * @throws \RuntimeException If the model doesn't use the required status functionality
     */
    public function reinstate(): bool
    {
        /** @var AccountSuspension $accountSuspension */ // Add PHPDoc type hint
        if ($accountSuspension = $this->activeSuspension()) {
            return $accountSuspension->lift();
        }
        return false;
    }

    public function activeSuspension(): ?AccountSuspension
    {
        return $this->suspensions()
            ->with('suspension')
            ->where(function ($query) {
                $query->where('is_permanent', true)
                    ->orWhere('suspended_until', '>', now());
            })
            ->whereNull('lifted_at')
            ->first();
    }
    /**
     * Check if the model is currently suspended.
     *
     * @return bool
     */
    public function isSuspended(): bool
    {
        return $this->suspensions()->active()->exists();
    }

    /**
     * Get the suspension reason for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function suspensionReason()
    {
        return $this->activeSuspension()->suspension()->reason();
    }

    public function userSuspensionMessage()
    {
        return $this->activeSuspension()->suspension()->userSuspenssionMessage();
    }

    public function suspensionStatus(): ?string
    {
        if (!$suspension = $this->activeSuspension()) {
            return null;
        }

        return $suspension->is_permanent
            ? 'Permanently suspended'
            : 'Suspended until ' . $suspension->suspended_until->format('M d, Y');
    }

    public function getAccountStatusAttribute(): string
    {
        return $this->isSuspended()
            ? ($this->activeSuspension->is_permanent ? 'banned' : 'suspended')
            : 'active';
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
