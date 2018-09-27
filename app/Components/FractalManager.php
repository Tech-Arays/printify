<?php

namespace App\Components;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

use App\Transformers\Serializers\SimpleArraySerializer;

class FractalManager
{
    private static $manager;

    private function __construct() { /* ... @return Singleton */ }
    private function __clone() { /* ... @return Singleton */ }
    private function __wakeup() { /* ... @return Singleton */ }

    public static function i($serializer = null)
    {
        if (empty(static::$manager)) {
            static::$manager = new Manager();
        }

        if ($serializer) {
            static::$manager->setSerializer($serializer);
        }
        else {
            static::$manager->setSerializer(new ArraySerializer());
        }

        return static::$manager;
    }

    public static function collection($resources, $transformer)
    {
        return new Collection($resources, $transformer);
    }

    public static function item($resource, $transformer)
    {
        return new Item($resource, $transformer);
    }

    public static function serializeCollection($resources, $transformer)
    {
        $collection = static::collection($resources, $transformer);
        return static::i(new SimpleArraySerializer())
            ->createData($collection)
            ->toArray();
    }

    public static function serializePaginator($paginator, $transformer)
    {
        $resources = $paginator->getCollection();
        $resources = static::collection($resources, $transformer);
        $resources->setPaginator(new IlluminatePaginatorAdapter($paginator));

        return static::i(new ArraySerializer())
            ->createData($resources)
            ->toArray();
    }

    public static function serializeItem($resource, $transformer)
    {
        $item = static::item($resource, $transformer);
        return static::i(new SimpleArraySerializer())
            ->createData($item)
            ->toArray();
    }
}
