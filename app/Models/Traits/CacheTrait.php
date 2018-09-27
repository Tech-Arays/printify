<?php

namespace App\Models\Traits;

use Cache;
use Carbon\Carbon;

trait CacheTrait
{
    public static $CACHE_FORCE_UPDATE = true;

    public $refreshCache = false;

    protected function cacheKey($key)
    {
        return static::class.':'.$this->id.':'.$key;
    }

    protected function hasCache($key)
    {
        return !$this->refreshCache && Cache::has($this->cacheKey($key));
    }

    protected function getCache($key)
    {
        return Cache::get($this->cacheKey($key));
    }

    protected function addToCache($key, $value, $minutes)
    {
        Cache::put(
            $this->cacheKey($key),
            $value,
            Carbon::now()->addMinutes($minutes)
        );
    }

    protected function putToCache($key, $value, $minutes)
    {
        Cache::put(
            $this->cacheKey($key),
            $value,
            Carbon::now()->addMinutes($minutes)
        );
    }

}
