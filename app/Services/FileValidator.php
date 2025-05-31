<?php


namespace App\Services;

use App\Constants\DiskNames;
use App\Constants\FileExtensions;
use App\Services\Validation\ValidatorFactory;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use RuntimeException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use phpDocumentor\Reflection\Types\Boolean;
use PhpParser\Node\Expr\Throw_;

class FileValidator
{


    public function validate(array $allowedExtensions, UploadedFile $file): bool
    {
        if ($allowedExtensions === FileExtensions::ALL) {
            return true;
        }

        if ($allowedExtensions === FileExtensions::NONE) {
            return false;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        return in_array($extension, $allowedExtensions, true);
    }
}
