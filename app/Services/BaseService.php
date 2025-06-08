<?php

namespace App\Services;

use Illuminate\Support\Str;  // Add this line
use App\Helpers\CacheHelper;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    protected Model $model;

    protected CacheHelper $cache;

    protected bool $cacheEnabled = true;

    protected int $defaultCacheTtl = 3600; // 10 minutes

    public function __construct(
        Model $model,
        CacheHelper $cache
    ) {
        $this->model = $model;
        $this->cache = $cache;
    }
    /**
     * Get paginated results with caching
     */
    public function collection(
        array $filters = [],
        array $relations = [],
        array $columns = ['*'],
        array $withCount = []
    ): Collection {
        $cacheKey = $this->cache->generateKey(
            $this->model::class,
            compact('filters', 'perPage', 'withCount')
        );

        foreach ($filters as $key => $value) {
            if (is_string($value) && Str::startsWith($value, '[') && Str::endsWith($value, ']')) {
                $filters[$key] = json_decode($value, true);
            }
        }
        $callback = function () use ($filters, $relations, $columns, $withCount) {
            $query = $this->model->with($relations);

            if (! empty($withCount)) {
                $query->withCount($withCount);
            }

            return $query
                ->filter($filters);
        };

        return $this->cacheEnabled
            ? $this->cache->manage($cacheKey, $callback, $this->defaultCacheTtl)
            : $callback();
    }
    /**
     * Get paginated results with caching
     */
    public function paginatedList(
        array $filters = [],
        array $relations = [],
        int $perPage = 5,
        array $columns = ['*'],
        array $withCount = []
    ): LengthAwarePaginator {
        $cacheKey = $this->cache->generateKey(
            $this->model::class,
            compact('filters',  'withCount')
        );
        $callback = function () use ($filters, $relations, $perPage, $columns, $withCount) {
            return $this->collection($filters, $relations, $columns, $withCount)
                ->paginate($perPage, $columns);
        };

        return $this->cacheEnabled
            ? $this->cache->manage($cacheKey, $callback, $this->defaultCacheTtl)
            : $callback();
    }

    /**
     * Find record with caching
     */
    public function findById(
        int $id,
        array $relations = [],
        array $columns = ['*'],
        array $withCount = []
    ): ?Model {
        $cacheKey = $this->cache->generateKey(
            $this->model::class,
            ['single' => $id]
        );
        $callback = function () use ($id, $relations, $columns) {
            $query = $this->model->with($relations);

            if (! empty($withCount)) {
                $query->withCount($withCount);
            }
            return $query
                ->find($id, $columns);
        };

        return $this->cacheEnabled
            ? $this->cache->manage($cacheKey, $callback, $this->defaultCacheTtl)
            : $callback();
    }

    /**
     * Create new record with cache invalidation
     */
    public function create(array $data): Model
    {
        $model = $this->model->create($data);
        $this->invalidateCache();

        return $model;
    }

    /**
     * Update record with cache invalidation
     */
    public function update(int $id, array $data)
    {

        $model = $this->model->findOrFail($id);
        $model->update($data);

        $this->invalidateCache();

        return $model;
    }

    /**
     * Delete record with cache invalidation
     */
    public function delete(int $id): void
    {
        $model = $this->model->findOrFail($id);
        $model->delete();
        $this->invalidateCache();
    }

    /**
     * Invalidate all cached data for this model
     */
    protected function invalidateCache(): void
    {
        $this->cache->clearForModel($this->model::class);
    }

    /**
     * Enable/disable caching for this service instance
     */
    public function toggleCache(bool $status): self
    {
        $this->cacheEnabled = $status;

        return $this;
    }
}
