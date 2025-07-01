<?php

if (! function_exists('trans_fallback')) {
    function trans_fallback($key, $default)
    {
        return app(\App\Services\LanguageService::class)->transOrDefault($key, $default);
    }
}
