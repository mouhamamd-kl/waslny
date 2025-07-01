<?php

namespace App\Traits\General;

use Illuminate\Database\Eloquent\Builder;

trait ActiveScope
{
    public function activeScope(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
