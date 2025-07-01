<?php

namespace App\Services\Notification;

use App\Models\Listing;
use App\Models\Property;
use App\Notifications\User\PropertyUpdated;
use Exception;
use Illuminate\Support\Facades\Notification;

class PropertyUpdateNotifier extends NotificationService
{
    public function notify(Property $oldProperty, Property $updatedProperty)
    {
        try {
            $listing = Listing::where('property_id', $oldProperty->id)->first();

            // $originalValues = $oldProperty->getOriginal();
            // $newValues = $updatedProperty->getOriginal(); // Using getOriginal() because fresh() was called
            // // Calculate changed fields
            // $changes = [];
            // $watchedKeys = config('property.notify_on_update');

            // foreach ($watchedKeys as $key) {
            //     if (
            //         array_key_exists($key, $originalValues) &&
            //         array_key_exists($key, $newValues) &&
            //         $originalValues[$key] != $newValues[$key]
            //     ) {
            //         $changes[$key] =
            //             $newValues[$key];
            //     }
            // }
            // // send notification to the users who favourited this property
            // $users = $listing->favoritedBy;
            // $watchedKeys = config('property.notify_on_update');
            // // Notification::sendNow($users, new PropertyUpdated($updatedProperty, $changes));
            $users = $listing->favoritedBy;
            $watchedKeys = config('property.notify_on_update');
            $changes = get_model_update_chages(oldProperty: $oldProperty, updatedProperty: $updatedProperty, watchedKeys: $watchedKeys);
            if (! empty($changes)) {
                $this->send(notifiables: $users, notification: new PropertyUpdated($updatedProperty, $changes));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
