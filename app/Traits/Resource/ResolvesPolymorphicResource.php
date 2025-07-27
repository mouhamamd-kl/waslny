<?php

namespace App\Traits;

trait ResolvesPolymorphicResource
{
    /**
     * Resolve polymorphic resource dynamically
     *
     * @param mixed $model
     * @param string $namespace
     * @return mixed
     */
    protected function resolvePolymorphicResource($model, string $namespace = 'App\\Http\\Resources\\')
    {
        if (!$model) {
            return null;
        }

        $className = class_basename($model);
        $resourceClass = $namespace . $className . 'Resource';

        if (class_exists($resourceClass)) {
            return new $resourceClass($model);
        }

        // Fallback for unsupported types
        return [
            'id' => $model->id,
            'type' => $className,
            // Add other common fields as needed
        ];
    }
}
