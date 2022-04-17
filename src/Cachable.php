<?php

namespace Insomnicles\Laracache;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

trait Cachable
{
    public static function allInCache(): Collection
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();

        $total = \App\Models\User::all()->count();
        $count = 0;
        $id = 1;

        $models = new Collection();

        while (1) {
            $modelStr = Cache::get($cacheKey . ':' . $id);

            if (!is_null($modelStr)) {
                $model = unserialize($modelStr);
                $models->put($model->id, $model);
            }
            $count++;
            $id++;
            if ($count >= $total)
                break;
        }
        return $models;
    }

    public static function findInCache(int $id) : mixed
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();
        $modelStr = Cache::get($cacheKey.":".$id);
        return is_null($modelStr) ? null : unserialize($modelStr);
    }

    public static function findInCacheOrFail(int $id) : mixed
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();
        $modelStr = Cache::get($cacheKey.":".$id);

        if (is_null($modelStr))
            throw new Exception('Model not found in cache');
        else
            unserialize($modelStr);
    }

    public static function findInCacheOrNew(int $id) : mixed
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();
        $modelStr = Cache::get($cacheKey.":".$id);

        return is_null($modelStr) ? $reflectionClass->newInstanceWithoutConstructor() : unserialize($modelStr);
    }

    public function deleteInCache() : void
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();
        Cache::forget($cacheKey.":".$this->id);
    }

    public function deleteInCacheOrFail() : void
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();

        if (is_null(Cache::get($cacheKey.":".$this->id)))
            throw new Exception('Model not Found in Cache');
        else
            Cache::forget($cacheKey.":".$this->id);
    }

    public static function refreshCache(int $fromId = 1, int $toId = null): void
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();
        $model = $reflectionClass->newInstanceWithoutConstructor();

        if ($toId == null) {
            $count = (self::class)::count();
            $models = (self::class)::where([['id', '>=', $fromId], ['id', '<=', $count]])->get();
        } else
            $models = (self::class)::where([['id', '>=', $fromId], ['id', '<=', $toId]])->get();

        foreach ($models as $model)
            Cache::set($cacheKey . ":" . $model->id, serialize($model));
    }
}
