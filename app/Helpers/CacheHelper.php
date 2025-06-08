<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    /**
     * Generate consistent cache key pattern
     */
    public function generateKey(string $model, array $params = []): string
    {
        ksort($params);

        return sprintf(
            '%s:%s',
            strtolower(class_basename($model)),
            md5(json_encode($params).request()->getQueryString())
        );
    }

    /**
     * Cache manager with optional custom tagging
     */
    public function manage(
        string $key,
        callable $callback,
        int $ttl = 3600,
        bool $useTags = true
    ) {
        if ($useTags) {
            $tag = $this->getModelTag($key);
            $this->storeKeyUnderTag($tag, $key);
        }

        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Clear all cache keys associated with a model tag
     */
    public function clearForModel(string $modelClass): void
    {
        $tag = $this->getModelTag($modelClass);
        $keys = Cache::get($tag, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget($tag);
    }

    /**
     * Get custom tag name from model
     */
    private function getModelTag(string $modelClass): string
    {
        return 'tag:'.strtolower(class_basename($modelClass));
    }

    /**
     * Track keys under a model tag
     */
    private function storeKeyUnderTag(string $tag, string $key): void
    {
        $existingKeys = Cache::get($tag, []);

        if (! in_array($key, $existingKeys)) {
            $existingKeys[] = $key;
            Cache::put($tag, $existingKeys, 3600); // Optional TTL
        }
    }
}
