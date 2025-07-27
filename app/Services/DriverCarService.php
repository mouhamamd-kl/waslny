<?php

namespace App\Services;

use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use App\Models\CarPhotoType;
use App\Models\DriverCar;
use App\Models\Rider;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class DriverCarService extends BaseService
{
    public function __construct(CacheHelper $cache)
    {
        parent::__construct(new DriverCar, $cache);
    }
    public function delete(int $driverCarId): void
    {
        $this->deleteAssets($driverCarId);
        parent::delete($driverCarId);
    }

    public function deleteAssets($driverCarId)
    {
        try {
            /** @var DriverCar $driverCar */ // Add PHPDoc type hint
            $driverCar = $this->findById($driverCarId);
            $driverCar->deletePhotos();
        } catch (Exception $e) {
            throw new Exception('error deleting assets for rider' . $e);
        }
    }
}
