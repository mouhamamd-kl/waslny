<?php

namespace App\Traits;

trait BooleanCaster
{
    /**
     * Set the is_active attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setIsActiveAttribute($value)
    {
        $this->attributes['is_active'] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
