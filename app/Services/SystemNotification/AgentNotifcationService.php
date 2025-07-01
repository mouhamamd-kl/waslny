<?php

namespace App\Services\SystemNotification;

use App\Services\SystemNotification\BaseNotificationService;

class AgentNotifcationService extends BaseNotificationService
{
    // public function __construct(
    //     CacheHelper $cache,
    //     protected NotificationService $notificationService, // Add this
    //     protected AgentService $agentService, // Add this
    // ) {
    //     parent::__construct(cache: $cache, pivotModel: new AgentNotification, notifiableModel: new Agent, relation: 'agents');
    // }
    // public function searchAgentNotifications(
    //     array $filters = [],
    //     int $perPage = 10,
    //     array $relations = ['notification'],
    //     array $withCount = []
    // ): LengthAwarePaginator {
    //     $agentId = null;
    //     if ($filters['agent_id'] != null) {
    //         $agentId = $filters['agent_id'];
    //     }
    //     /** @var LengthAwarePaginator $properties */
    //     // $Listings = Listing::whereIn('property_id', $PropertyListingIds)->paginate($perPage, $filters);
    //     $notifications = $this->notificationService->collection(
    //         filters: $filters,
    //         relations: [],
    //     );
    //     $notificationsIds = $notifications->pluck('id'); // Extract IDs

    //     $filterso = ['notification_id' => $notificationsIds];
    //     if ($agentId != null) {
    //         $filterso = ['agent_id' => $agentId];
    //     }
    //     // $Listings = Listing::whereIn('property_id', $PropertyListingIds)->paginate($perPage, $filters);
    //     return $this->toggleCache(config('app.enable_caching'))
    //         ->paginatedList(
    //             $filterso,
    //             $relations,
    //             $perPage,
    //             ['*'],
    //             $withCount
    //         );
    // }

    // public function addNotificationsToAgents($data, array $agentIds): Notification
    // {
    //     return $this->addNotificationsToModels(data: $data, modelIds: $agentIds);
    // }
    // public function addNotificationToAgent($data, $agentId): Notification
    // {
    //     return $this->addNotificationToModel(data: $data, modelId: $agentId);
    // }
    // public function clearAgentNotifications($agentId)
    // {
    //     return $this->clearModelNotifications($agentId);
    // }

    // public function deleteAgentNotification($agentId, $agentNotificationId)
    // {
    //     return $this->deleteModelNotification($agentId, $agentNotificationId);
    // }

    // public function markAgentNotificationAsRead($agentNotificationId)
    // {
    //     return $this->markAsRead($agentNotificationId);
    // }
    // public function markAgentNotificationAsUnRead($agentNotificationId)
    // {
    //     return $this->markAsUnRead($agentNotificationId);
    // }
    
}
