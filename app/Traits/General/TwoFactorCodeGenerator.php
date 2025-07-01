<?php

namespace App\Traits\General;



trait TwoFactorCodeGenerator
{
    /**
     * Generates a 6-digit two-factor authentication code and sets an expiration time (5 minutes).
     */
    public function generateTwoFactorCode(): void
    {
        $this->two_factor_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->two_factor_expires_at = now()->addMinutes(5);
        $this->save();
    }
}