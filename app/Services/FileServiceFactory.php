<?php

namespace App\Services;

use App\Constants\DiskNames;
use App\Constants\FileExtensions;
use App\Services\BaseFileService;

class FileServiceFactory
{
    // Rider-related services
    public static function makeForRiderProfile(): BaseFileService
    {
        return new BaseFileService(
            FileExtensions::PROFILE_PHOTOS,
            DiskNames::RIDERS->name
        );
    }

    // Driver-related services
    public static function makeForDriverProfile(): BaseFileService
    {
        return new BaseFileService(
            FileExtensions::PROFILE_PHOTOS,
            DiskNames::DRIVERS_PROFILE->name
        );
    }

    public static function makeForDriverLicense(): BaseFileService
    {
        return new BaseFileService(
            FileExtensions::LICENSE_FILES,
            DiskNames::DRIVERS_LICENSE->name
        );
    }

    public static function makeForDriverCarPhotos(): BaseFileService
    {
        return new BaseFileService(
            FileExtensions::CAR_PHOTOS,
            DiskNames::DRIVERS_CAR_PHOTOS->name
        );
    }

    // System services
    public static function makeForSystemFiles(): BaseFileService
    {
        return new BaseFileService(
            FileExtensions::SYSTEM_FILES,
            DiskNames::SYSTEM->name
        );
    }

    // Public/private storage
    public static function makeForPublicStorage(): BaseFileService
    {
        return new BaseFileService(
            FileExtensions::ALL,  // Most public files are images
            DiskNames::SUBAPASEPUBLIC->name
        );
    }

    public static function makeForPrivateStorage(): BaseFileService
    {
        return new BaseFileService(
            FileExtensions::ALL,  // Allow any file type in private storage
            DiskNames::SUPABASEPRIVATE->name
        );
    }

    // Generic services
    public static function makeForImages(): BaseFileService
    {
        return new BaseFileService(
            FileExtensions::IMAGES,
            DiskNames::SUBAPASEPUBLIC->name  // Default to public storage
        );
    }

    public static function makeForDocuments(): BaseFileService
    {
        return new BaseFileService(
            FileExtensions::DOCUMENTS,
            DiskNames::SUPABASEPRIVATE->name  // Sensitive documents in private storage
        );
    }

    // Special cases
    public static function makeForUnrestrictedUploads(): BaseFileService
    {
        return new BaseFileService(
            FileExtensions::ALL,
            DiskNames::SUPABASEPRIVATE->name
        );
    }

    public static function makeForNoUploads(): BaseFileService
    {
        return new BaseFileService(
            FileExtensions::NONE,
            DiskNames::SUPABASEPRIVATE->name
        );
    }
}
