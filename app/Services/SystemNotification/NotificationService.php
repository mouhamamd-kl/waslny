<?php

namespace App\Services\SystemNotification;

use App\Helpers\CacheHelper;
use App\Models\Listing;
use App\Models\Notification;
use App\Models\Property;
use App\Models\User;
use App\Models\ViewedProperty;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService extends BaseService
{
    public function __construct(
        CacheHelper $cache,
    ) {
        parent::__construct(new Notification, $cache);
    }

    public function searchNotifications(
        array $filters = [],
        int $perPage = 10,
        array $relations = [],
        array $withCount = []
    ): LengthAwarePaginator {
        /** @var LengthAwarePaginator $properties */
        // $Listings = Listing::whereIn('property_id', $PropertyListingIds)->paginate($perPage, $filters);
        return $this->toggleCache(config('app.enable_caching'))
            ->paginatedList(
                $filters,
                $relations,
                $perPage,
                ['*'],
                $withCount
            );
    }
}
