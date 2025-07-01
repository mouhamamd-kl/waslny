<?php

namespace App\Services;

use App\Enums\SuspensionReason;
use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\Rider;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class DriverService extends BaseService
{
    protected DriverCarService $driverCarService;
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new Rider, $cache);
    }

    public function searchDrivers(
        array $filters = [],
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->toggleCache(config('app.enable_caching'))
            ->paginatedList(
                $filters,
                [], // relations if any
                $perPage,
                ['*'],
                [] // <-- Here is your withCount
            );
    }

    public function deleteAssets($driverId)
    {
        try {
            /** @var Driver $driver */ // Add PHPDoc type hint
            $driver = $this->findById($driverId);
            $driverProfileAssetService = FileServiceFactory::makeForDriverProfile();
            $driverLicenseAssetService = FileServiceFactory::makeForDriverLicense();
            $driverProfileAssetService->delete($driver->profile_photo);
            $driverLicenseAssetService->delete($driver->driver_license_photo);
            $this->driverCarService->deleteAssets($driver->driverCar->id);
        } catch (Exception $e) {
            throw new Exception('error deleting assets for driver' . $e);
        }
    }

    public function suspend($driverId, SuspensionReason $suspension_reason)
    {
        try {
            /** @var Driver $driver */ // Add PHPDoc type hint
            $driver = $this->findById($driverId);
            $driver->suspend($suspension_reason);
        } catch (Exception $e) {
            throw new Exception('error suspending for driver' . $e);
        }
    }

    public function activate($driverId)
    {
        try {
            /** @var Driver $driver */ // Add PHPDoc type hint
            $driver = $this->findById($driverId);
            $driver->reinstate();
        } catch (Exception $e) {
            throw new Exception('error activating for driver' . $e);
        }
    }
}
