<?php
// app/Services/NotificationService.php
namespace App\Services\SystemNotification;

use App\Helpers\CacheHelper;
use App\Models\Notification;
use App\Services\BaseService;
use App\Services\SystemNotification\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaseNotificationService extends BaseService
{
    protected $relation;
    protected $notifiableModel;
    protected $pivotModel;
    protected NotificationService $notificationService;
    public function __construct(
        CacheHelper $cache,
        Model $notifiableModel,
        Model $pivotModel,
        string $relation,
    ) {
        $this->relation = $relation;
        $this->notifiableModel = $notifiableModel;
        $this->pivotModel = $pivotModel;
        $this->notificationService = app(NotificationService::class);
        parent::__construct($pivotModel, $cache);
    }

    public function addNotificationsToModels($data, array $modelIds): Notification
    {
        // return DB::transaction(function () use ($modelIds, $data) {
        try {
            /** @var Notification $notification */
            $notification = $this->notificationService->create($data);
            $notification->{$this->relation}()->syncWithoutDetaching($modelIds);
            return $notification;
        } catch (\Exception $e) {
            Log::error("User Notifications Creation failed: {$e->getMessage()}");
            throw $e;
        }
        // });
    }
    public function addNotificationToModel($data, $modelId): Notification
    {
        try {
            return $this->addNotificationsToModels($data, [$modelId]);
        } catch (\Exception $e) {
            Log::error("Model Notification Creation failed: {$e->getMessage()}");
            throw $e;
        }
    }
    public function clearModelNotifications($modelId)
    {
        return DB::transaction(function () use ($modelId) {
            try {
                $user = $this->notifiableModel->find($modelId);
                return $user->notifications()->detach();
            } catch (\Exception $e) {
                Log::error($this->notifiableModel::class . "  Notification Creation failed: {$e->getMessage()}");
                throw $e;
            }
        });
    }

    public function deleteModelNotification($modelId, int $modelNotificationId)
    {
        return DB::transaction(function () use ($modelId, $modelNotificationId) {
            try {
                $model = $this->notifiableModel->find($modelId);
                // return $model->notifications()->detach($modelNotificationId);
                return  $model->notifications()->detach($modelNotificationId); // Detach one listing
                // $deleted =  $this->notifiableModel::findOrFail($modelId)
                //     ->notifications()
                //     ->detach();
                // return $deleted;
            } catch (\Exception $e) {
                Log::error($this->notifiableModel::class  . " notification deletion failed: " . $e->getMessage());
                throw $e;
            }
        });
    }

    public function markAsRead($modelNotificationId)
    {
        return DB::transaction(function () use ($modelNotificationId) {
            try {
                $modelNotification = $this->findById($modelNotificationId);
                return $modelNotification->markAsRead();
            } catch (\Exception $e) {
                Log::error(
                    "Failed to mark notifications as read for [" . $this->notifiableModel::class . "]: " . $e->getMessage()
                );
                throw $e;
            }
        });
    }


    public function markAsUnRead($modelNotificationId)
    {
        return DB::transaction(function () use ($modelNotificationId) {
            try {
                $modelNotification = $this->findById($modelNotificationId);
                return  $modelNotification->markAsUnread();
            } catch (\Exception $e) {
                Log::error("{$this->notifiableModel} mark as unread failed: " . $e->getMessage());
                throw $e;
            }
        });
    }
}
