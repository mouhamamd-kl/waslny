<?php

namespace App\Services;

use Illuminate\Support\Facades\Lang;

class LanguageService
{
    public function transOrDefault(string $key, string $default): string
    {
        return Lang::has($key) ? __($key) : $default;
    }
}
