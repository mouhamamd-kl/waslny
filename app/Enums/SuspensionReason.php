<?php

namespace App\Enums;

enum SuspensionReason: string
{
    case REVIEW_FAILED = 'review_failed';
    case NEED_REVIEW = 'need_review';
    case COMPLAINT = 'complaint';
    case PAYMENT_ISSUE = 'payment_issue';
    case SAFETY_VIOLATION = 'safety_violation';
    case OTHER = 'other';

    public function message(): string
    {
        return match ($this) {
            self::REVIEW_FAILED => 'Your account could not be verified. Please contact support.',
            self::NEED_REVIEW => 'Your account is under verification. Please wait for admin approval.',
            self::COMPLAINT => 'Account suspended due to customer complaints. Contact support for resolution.',
            self::PAYMENT_ISSUE => 'Account suspended due to payment issues. Please settle your balance.',
            self::SAFETY_VIOLATION => 'Account suspended for safety violations. Contact support for appeal.',
            self::OTHER => 'Account suspended. Please contact support for details.',
        };
    }

    public function adminMessage(): string
    {
        return match ($this) {
            self::REVIEW_FAILED => 'Driver verification failed - documents rejected',
            self::NEED_REVIEW => 'New driver awaiting review',
            self::COMPLAINT => 'Driver suspended due to complaints',
            self::PAYMENT_ISSUE => 'Driver suspended for payment issues',
            self::SAFETY_VIOLATION => 'Driver suspended for safety violations',
            self::OTHER => 'Driver suspended - see notes for details',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    // In your SuspensionReason enum
    public static function rule(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
