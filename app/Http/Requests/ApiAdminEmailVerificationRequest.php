<?php

namespace App\Http\Requests;

use App\Models\Admin;
use App\Models\Agent;
use Illuminate\Auth\Events\Verified;
use Illuminate\Validation\ValidationException;

class ApiAdminEmailVerificationRequest extends BaseRequest
{
    protected Admin $userInstance;

    public function authorize()
    {
        $this->userInstance = Admin::find(id: $this->route('id'));

        if (! $this->userInstance) {
            throw ValidationException::withMessages([
                'user' => 'Invalid verification link',
            ]);
        }

        return hash_equals(
            sha1($this->userInstance->getEmailForVerification()),
            (string) $this->route('hash')
        );
    }

    public function fulfill()
    {
        if (! $this->userInstance->hasVerifiedEmail()) {
            $this->userInstance->markEmailAsVerified();
            event(new Verified($this->userInstance));
        }
    }

    public function getUserInstance()
    {
        return $this->userInstance;
    }
}
