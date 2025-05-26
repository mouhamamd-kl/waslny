<?php


namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use RuntimeException;
use Illuminate\Support\Str;

class AwsFileService
{
    public function upload(
        UploadedFile $file,
        string $pathPrefix = 'AlSinwar',
        string $disk = 'supabase'
    ): string {
        // Store the file with public visibility

        $path = $file->store($pathPrefix, [
            'disk' => $disk,
            'visibility' => 'public' // Ensure file is publicly accessible
        ]);
        // Use the helper function to get public URL
        return $path;
        return supabase_public_url($path);

        // $path =  Storage::disk('s3')->put($pathPrefix, $file);
        // return supabase_public_url($path);
    }
    private function generateSafeFileName(UploadedFile $file): string
    {
        $originalName = pathinfo(
            $file->getClientOriginalName(),
            PATHINFO_FILENAME
        );

        $sanitized = preg_replace('/[^a-zA-Z0-9\-_]/', '', $originalName);
        $sanitized = $sanitized ?: 'file';

        return Str::slug($sanitized)
            . '-' . uniqid()
            . '.' . $file->extension();
    }

    private function buildFullPath(string $prefix, string $fileName): string
    {
        $prefix = trim($prefix, '/');
        return $prefix ? "$prefix/$fileName" : $fileName;
    }
    
    public function delete(string $filePath, string $disk = 's3'): bool
    {
        return Storage::disk($disk)->delete($filePath);
    }

    public function exists(string $filePath, string $disk = 's3'): bool
    {
        return Storage::disk($disk)->exists($filePath);
    }

    public function getUrl(string $filePath, string $disk = 's3'): string
    {
        $disk = Storage::disk($disk);
        $url = $disk->url($filePath);
        return $url;
    }
}
