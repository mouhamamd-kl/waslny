<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

if (!function_exists('supabase_url')) {
    /**
     * Generate public URL for Supabase Storage file
     *
     * @param string $filePath
     * @return string
     */

    function supabase_url(string $filePath, bool $public): string
    {
        return $public ? supabase_url_public($filePath) : supabase_url_private($filePath);
    }
    // function supabase_url_private(string $filePath): string
    // {
    //     $base = "https://" . config('services.supabase.project_id') . ".supabase.co";
    //     $bucket =  config('services.supabase.private_bucket');
    //     return "{$base}/storage/v1/object/sign/{$bucket}/storage/v1/s3/{$bucket}/{$bucket}/{$filePath}?token=";
    // }
    // function supabase_url_public(string $filePath): string
    // {
    //     $base = "https://" . config('services.supabase.project_id') . ".supabase.co";
    //     $bucket = config('services.supabase.public_bucket');
    //     return "{$base}/storage/v1/object/public/{$bucket}/storage/v1/s3/{$bucket}/{$bucket}/{$filePath}";
    // }
}
if (!function_exists(('supabase_url_private'))) {
    /**
     * Get Supabase Storage file Path
     *
     * @param string $filePath
     * @return string
     */

    function supabase_url_private(string $filePath): string
    {
        $serviceRoleKey = config('services.supabase.service_role_key');
        $base = "https://" . config('services.supabase.project_id') . ".supabase.co";
        $bucket =  config('services.supabase.private_bucket');
        // Request a signed URL from Supabase
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$serviceRoleKey}",
        ])->post("{$base}/storage/v1/object/sign/{$bucket}/storage/v1/s3/{$bucket}/{$bucket}/{$filePath}", [
            'expiresIn' => 60 // Expires in 60 seconds
        ]);

        if ($response->failed()) {
            abort(404, 'File not found');
        }

        // Construct the publicly accessible URL with the token
        $token = $response->json('signedURL');

        // $publicUrl = "{$base}/storage/v1/object/sign/{$bucket}/storage/v1/s3/{$bucket}/{$bucket}/{$filePath}?token={$token}";
        $publicUrl = "{$base}/storage/v1/$token}";

        return $publicUrl;
    }
}
if (!function_exists(('supabase_url_public'))) {
    /**
     * Get Supabase Storage file Path
     *
     * @param string $filePath
     * @return string
     */

    function supabase_url_public(string $filePath): string
    {
        $base = "https://" . config('services.supabase.project_id') . ".supabase.co";
        $bucket = config('services.supabase.public_bucket');
        return "{$base}/storage/v1/object/public/{$bucket}/storage/v1/s3/{$bucket}/{$bucket}/{$filePath}";
    }
}
if (!function_exists(('get_filepath_from_url'))) {
    /**
     * Get Supabase Storage file Path
     *
     * @param string $url
     * @return string
     */

    function get_filepath_from_url(string $url): string
    {
        $projectId = config('services.supabase.project_id');
        $base = "https://" . $projectId . ".supabase.co";
        $bucket = config('services.supabase.public_bucket');
        $removed="{$base}/storage/v1/object/public/{$bucket}/{$bucket}";
        $filePath = str_replace($removed, "", $url);
        return $filePath;
    }
}
