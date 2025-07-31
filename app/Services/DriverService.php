<?php

namespace App\Services;

use App\Enums\SuspensionReason;
use App\Enums\SuspensionReasonEnum;
use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\Driver;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class DriverService extends BaseService
{
    protected array $relations = ['status', 'driverCar', 'trips', 'tripNotifications', 'notifiedTrips', 'completedTrips', 'currentTrip', 'suspensions'];
    protected DriverCarService $driverCarService;
    protected SuspenssionService $suspenssion_service;
    public function __construct(CacheHelper $cache, SuspenssionService $suspenssionService, DriverCarService $driverCarService)
    {
        parent::__construct(new Driver, $cache);
        $this->suspenssion_service = $suspenssionService;
        $this->driverCarService = $driverCarService;
    }

    public function searchDrivers(
        array $filters = [],
        int $perPage = 10
    ): LengthAwarePaginator {
        return $this->toggleCache(config('app.enable_caching'))
            ->paginatedList(
                filters: $filters,
                relations: $this->relations,
                perPage: $perPage,
                columns: ['*'],
                withCount: []
            );
    }

    public function deleteAssets($driverId)
    {
        try {
            /** @var Driver $driver */ // Add PHPDoc type hint
            $driver = $this->findById($driverId);
            $driver->deletePhotos();
            $this->driverCarService->deleteAssets($driver->driverCar->id);
        } catch (Exception $e) {
            throw new Exception('error deleting assets for driver' . $e);
        }
    }

    public function suspendForever($driverId, $suspension_id)
    {
        try {
            /** @var Driver $driver */ // Add PHPDoc type hint
            $driver = $this->findById($driverId);
            $driver->suspendForever($suspension_id);
        } catch (Exception $e) {
            throw new Exception('error suspending for driver' . $e);
        }
    }
    public function suspendNeedConfirmation($driverId)
    {
        try {
            /** @var Driver $driver */ // Add PHPDoc type hint
            $driver = $this->findById($driverId);
            $suspention = $this->suspenssion_service->searchByReason(SuspensionReasonEnum::NEED_REVIEW->value);
            $driver->suspendForever($suspention->id);
        } catch (Exception $e) {
            throw new Exception('error suspending for driver' . $e);
        }
    }
    public function suspendTemporarily($driverId, $suspension_id, $suspended_until)
    {
        try {
            /** @var Driver $driver */ // Add PHPDoc type hint
            $driver = $this->findById($driverId);
            $driver->suspendTemporarily($suspension_id, $suspended_until);
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
