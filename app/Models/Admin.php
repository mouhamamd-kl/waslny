<?php

namespace App\Models;

use App\Notifications\AdminResetPassword;
use App\Traits\General\FilterScope;
use App\Traits\General\ResetOTP;
use App\Traits\General\TwoFactorCodeGenerator;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Model
{
    use HasFactory, TwoFactorCodeGenerator, FilterScope, HasApiTokens, ResetOTP;

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPassword($token));
    }


    public function generateTwoFactorCode(): void
    {
        $this->two_factor_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->two_factor_expires_at = now()->addMinutes(5);
        $this->save();
    }

    public function resetTwoFactorCode(): void
    {
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    // =================
    // Configuration
    // =================

    protected $table = 'riders';
    protected $primaryKey = 'id'; // Explicitly define since it's BIGINT

    protected $guarded = ['id'];
}
