<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('supabase_public_url')) {
    /**
     * Generate public URL for Supabase Storage file
     *
     * @param string $filePath
     * @return string
     */
    function supabase_public_url(string $filePath): string
    {
        $base = "https://" . config('services.supabase.project_id') . ".supabase.co";
        $bucket = config('services.supabase.bucket');

        // Remove any accidental duplicate paths
        // $filePath = str_replace("storage/v1/s3/{$bucket}/", '', $filePath);

        return "{$base}/storage/v1/object/public/{$bucket}/storage/v1/s3/{$bucket}/{$bucket}/{$filePath}";
    }
}
