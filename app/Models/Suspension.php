<?php

namespace App\Models;

use App\Enums\SuspensionReasonEnum;
use App\Traits\General\ActiveScope;
use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Suspension extends Model
{
    use FilterScope, ActiveScope, HasFactory;
    // =================
    // Configuration
    // =================
    protected $table = 'suspensions';
    protected $guarded = ['id'];

    // =================
    // Relationships
    // =================
    public function accountSuspension(): HasMany
    {
        return $this->hasMany(AccountSuspension::class);
    }

    // =================
    // Accessors & Mutators
    // =================
    public function toEnum(): SuspensionReasonEnum
    {
        return SuspensionReasonEnum::from($this->reason);
    }
    public function reason(): string
    {
        return $this->reason;
    }
    public function userSuspenssionMessage(): string
    {
        return $this->user_msg;
    }
    // =================
    // Scopes
    // =================


    // =================
    // Business Logic
    // =================
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
    public function isActive(): bool
    {
        return $this->is_active;
    }
}
