<?php


namespace App\Services;

use App\Constants\DiskNames;
use App\Constants\FileExtensions;
use Exception;
use Faker\Extension\FileExtension;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use RuntimeException;

class BaseFileService
{
    protected array $validExtensions;
    protected FileValidator $fileValidator;
    protected string $disk;
    public function __construct(
        array $validExtensions,
        ?string $disk
    ) {
        $this->fileValidator = new FileValidator();



        $this->validateAndSetDisk($disk);

        // Validate extensions
        $this->validateAndSetExtensions($validExtensions);
    }

    protected function validateAndSetDisk(
        ?string $disk
    ): void {
        if ($disk === '' || $disk === null) {
            $disk = \App\Constants\DiskNames::SUBAPASEPUBLIC->name;
        }

        DiskNames::isValidName($disk);
        $this->disk = $disk;
    }

    protected function validateAndSetExtensions(array $validExtensions): void
    {
        FileExtensions::isValidExtension($validExtensions);

        $this->validExtensions = $validExtensions;
    }

    public function uploadPublic(
        UploadedFile $file,
        string $pathPrefix,
    ):string {
        if (!$this->fileValidator->validate($this->validExtensions, $file)) {
            throw new Exception("File Type is not Supported");
        }
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk($this->disk);
        $filepath = $storage->put($pathPrefix, $file);
        $url = $storage->url($filepath);
        return $url;
    }
    public function uploadPrivate(
        UploadedFile $file,
        string $pathPrefix = '',
        string $disk = DiskNames::SUPABASEPRIVATE,
    ) {
        if (!$this->fileValidator->validate($this->validExtensions, $file)) {
            throw new Exception("File Type is not Supported");
        }
        /** @var storage $storage */
        $storage = Storage::disk($disk);
        $filepath = $storage->put($pathPrefix, $file);
        $signedUrl = $storage
            ->temporaryUrl(
                $filepath,
                now()->addMinutes(10)
            );
        return $signedUrl;
    }

    public function delete(string $filePath): bool
    {
        try {
            if (empty($filePath)) {
                Log::warning('Attempted to delete file with empty filePath');
                return false;
            }
            /** @var FilesystemAdapter $storage */
            $storage = Storage::disk($this->disk);

            if (!$storage->exists($filePath)) {
                Log::warning("File not found for deletion: {$filePath} on disk: {$this->disk}");

                return true; // Consider non-existent files as successfully deleted
            }
            return $storage->delete($filePath);
        } catch (\Exception $e) {
            Log::error("S3 Delete Error: {$e->getMessage()}", [
                'filePath' => $filePath,
                'disk' => $this->disk,
                'exception' => $e,
            ]);
            return false;
        }
    }

    public function getFilePath(string $url)
    {
        if (empty(trim($url))) {
            throw new \InvalidArgumentException('URL cannot be empty');
        }

        $projectId = config('services.supabase.project_id');
        $base = "https://" . $projectId . ".supabase.co";
        $bucket = config('services.supabase.public_bucket');
        $removed = "{$base}/storage/v1/object/public/{$bucket}/storage/v1/s3/{$bucket}/{$bucket}";
        $disk = $this->disk;
        $diskPath = DiskNames::valueFromName($disk)->value;
        if ($this->disk != DiskNames::SUBAPASEPUBLIC) {
            $removed = $removed . "/" . $diskPath;
        }
        $filePath = str_replace($removed, "", $url);
        $filePath = str_replace('\\', '/', $filePath);
        $filePath = str_replace('//', '/', $filePath);
        return $filePath;
    }

    public function getUrl(string $filePath): string
    {
        if (empty(trim($filePath))) {
            throw new \InvalidArgumentException('File path cannot be empty');
        }

        if (!array_key_exists($this->disk, config('filesystems.disks'))) {
            throw new \RuntimeException("Disk [$this->disk] not configured");
        }
        $filePath = str_replace('\\', '/', $filePath);
        $filePath = str_replace('//', '/', $filePath);
        /** @var storage $storage */

        $storage = Storage::disk($this->disk);

        try {
            // 4. Check file existence (optional - adds overhead)
            if (!$storage->exists($filePath)) {
                throw new FileNotFoundException("File [$filePath] not found in disk [$this->disk]");
            }

            // 5. Generate URL
            $url = $storage->url($filePath);

            // 6. Validate URL format
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \RuntimeException("Generated URL is invalid: $url");
            }
            $url = str_replace('\\', '/', $url);
            $url = str_replace('//', '/', $url);

            return $url;
        } catch (\Exception $e) {
            // Log detailed error for debugging
            Log::error("Failed to generate URL for [$filePath]: " . $e->getMessage());
            throw $e; // Re-throw or return default/error URL as needed
        }
    }

    public function exists(string $filePath): bool
    {
        if (empty(trim($filePath))) {
            throw new \InvalidArgumentException('File path cannot be empty');
        }
        return Storage::disk($this->disk)->exists($filePath);
    }
}
