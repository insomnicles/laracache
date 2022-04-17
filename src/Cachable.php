<?php

namespace Insomnicles\Laracache;

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
