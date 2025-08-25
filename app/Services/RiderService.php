<?php

namespace App\Services;

use App\Enums\SuspensionReason;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\Rider;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class RiderService extends BaseService
{
    protected array $relations = ['defaultPaymentMethod', 'savedLocations', 'folders', 'trips', 'completedTrips', 'currentTrip', 'Ridercoupons', 'coupons', 'suspensions'];
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new Rider, $cache);
    }
    public function searchRiders(
        array $filters = [],
        int $perPage = 10
    ): LengthAwarePaginator {
        if (isset($filters['suspended']) && $filters['suspended']) {
            $filters['driver.activeSuspension'] = true;
            unset($filters['suspended']);
        }
        return $this->toggleCache(config('app.enable_caching'))
            ->paginatedList(
                filters: $filters,
                relations: $this->relations,
                perPage: $perPage,
                columns: ['*'],
                withCount: []
            );
    }

    public function deleteAssets(Rider $rider)
    {
        try {
            $assetService = FileServiceFactory::makeForRiderProfile();
            if ($rider->profile_photo && $rider->profile_photo != '') {
                $assetService->delete($rider->profile_photo);
            }
        } catch (Exception $e) {
            throw new Exception('error deleting assets for rider' . $e);
        }
    }

    public function suspendForever($riderId, $suspension_id)
    {
        try {
            /** @var Rider $rider */ // Add PHPDoc type hint
            $rider = $this->findById($riderId);
            $rider->suspendForever($suspension_id);
        } catch (Exception $e) {
            throw new Exception('error suspending for rider' . $e);
        }
    }
    public function suspendTemporarily($riderId, $suspension_id, $suspended_until)
    {
        try {
            /** @var Rider $rider */ // Add PHPDoc type hint
            $rider = $this->findById($riderId);
            $rider->suspendTemporarily($suspension_id, Carbon::parse($suspended_until));
        } catch (Exception $e) {
            throw new Exception('error suspending for rider' . $e);
        }
    }

    public function activate($riderId)
    {
        try {
            /** @var Rider $rider */ // Add PHPDoc type hint
            $rider = $this->findById($riderId);
            $rider->reinstate();
        } catch (Exception $e) {
            throw new Exception('error activating for rider' . $e);
        }
    }
}
