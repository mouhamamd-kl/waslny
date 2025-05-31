<?php


namespace App\Services;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use RuntimeException;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Boolean;
use PhpParser\Node\Expr\Throw_;

class AwsFileService
{
    public function upload(
        UploadedFile $file,
        string $pathPrefix = '',
        Bool $public = true,
    ): string {
        if ($public) {
            /** @var storage $storage */
            $storage = Storage::disk('supabase');

            $filepath = Storage::disk('supabase')->put('', $file);
            $url = $storage->url($filepath);
            return $url;
        } else {
            /** @var storage $storage */
            $storage = Storage::disk('supabase_private');
            $filepath = $storage->put('', $file);
            $signedUrl = $storage
                ->temporaryUrl(
                    $filepath,
                    now()->addMinutes(10)
                );
            return $signedUrl;
        }
    }



    public function delete(string $filePath, string $disk = 'supabase'): bool
    {
        try {
            if (empty($filePath)) {
                Log::warning('Attempted to delete file with empty filePath');

                return false;
            }

            /** @var FilesystemAdapter $storage */
            $storage = Storage::disk($disk);

            if (! $storage->exists($filePath)) {
                Log::warning("File not found for deletion: {$filePath} on disk: {$disk}");

                return true; // Consider non-existent files as successfully deleted
            }

            return $storage->delete($filePath);
        } catch (\Exception $e) {
            Log::error("S3 Delete Error: {$e->getMessage()}", [
                'filePath' => $filePath,
                'disk' => $disk,
                'exception' => $e,
            ]);

            return false;
        }
    }

    public function exists(string $filePath, string $disk = 'supabase'): bool
    {
        return Storage::disk($disk)->exists($filePath);
    }

    public function getUrl(string $filePath, string $disk = 'supabase'): string
    {
        if (empty(trim($filePath))) {
            throw new \InvalidArgumentException('File path cannot be empty');
        }

        if (!array_key_exists($disk, config('filesystems.disks'))) {
            throw new \RuntimeException("Disk [$disk] not configured");
        }
        /** @var storage $storage */

        $storage = Storage::disk($disk);

        try {
            // 4. Check file existence (optional - adds overhead)
            if (!$storage->exists($filePath)) {
                throw new FileNotFoundException("File [$filePath] not found in disk [$disk]");
            }

            // 5. Generate URL
            $url = $storage->url($filePath);

            // 6. Validate URL format
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \RuntimeException("Generated URL is invalid: $url");
            }

            return $url;
        } catch (\Exception $e) {
            // Log detailed error for debugging
            Log::error("Failed to generate URL for [$filePath]: " . $e->getMessage());
            throw $e; // Re-throw or return default/error URL as needed
        }


        // $url = $disk->url($filePath);
        // return $url;
    }
}
