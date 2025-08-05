<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Activatable
{
    /**
     * Activate the model.
     */
    public function activate(): void
    {
        $this->is_active = true;
        $this->save();
    }

    /**
     * Deactivate the model.
     */
    public function deActivate(): void
    {
        $this->is_active = false;
        $this->save();
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
