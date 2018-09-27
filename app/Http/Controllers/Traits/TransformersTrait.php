<?php

namespace App\Http\Controllers\Traits;

use FractalManager;

trait TransformersTrait
{
    protected function serializeItem($model, $transformer, $includes = [])
    {
        return FractalManager::serializeItem($model, $transformer, $includes);
    }

    protected function serializeCollection($models, $transformer, $includes = [])
    {
        return FractalManager::serializeCollection($models, $transformer, $includes);
    }

    protected function serializePaginator($query, $transformer, $includes = [], $perPage = 8)
    {
        $paginator = $query->paginate($perPage);
        return FractalManager::serializePaginator($paginator, $transformer, $includes);
    }
}
