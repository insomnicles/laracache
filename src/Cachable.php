<?php

namespace Insomnicles\Laracache;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Redis;

trait Cachable
{
    public static function allInCache(): Collection
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();
        $models = new Collection();

        $keys = Redis::keys($cacheKey.':*');

        if (empty($keys)) {
            return $models;
        }

        for ($i = 0; $i < count($keys); $i++) {
            $keys[$i] = strstr($keys[$i], $cacheKey);
        }

        $values = Redis::mget($keys);

        foreach ($values as $value) {
            $model = unserialize($value);
            $models->put($model->id, $model);
        }

        return $models;
    }

    public static function findInCache(int $id): mixed
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();
        $modelStr = Redis::get($cacheKey.':'.$id);

        return is_null($modelStr) ? null : unserialize($modelStr);
    }

    public static function findInCacheOrFail(int $id): mixed
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();
        $modelStr = Redis::get($cacheKey.':'.$id);

        if (is_null($modelStr)) {
            throw new Exception('Model not found in cache');
        } else {
            return unserialize($modelStr);
        }
    }

    public static function findInCacheOrNew(int $id): mixed
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();
        $modelStr = Redis::get($cacheKey.':'.$id);

        return is_null($modelStr) ? $reflectionClass->newInstanceWithoutConstructor() : unserialize($modelStr);
    }

    public function saveInCache(): mixed
    {
        if (isset($this->id)) {
            throw new Exception('id not set');
        }

        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();
        Redis::set($cacheKey.':'.$this->id, serialize($this));

        $modelStr = Redis::get($cacheKey.':'.$this->id);
        if (is_null($modelStr)) {
            self::class::refreshCache();
            $modelStr = Redis::get($cacheKey.':'.$this->id);
        }

        return unserialize($modelStr);
    }

    public function saveFromCache(): void
    {
        if (isset($this->id)) {
            throw new Exception('id not set');
        }

        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();
        $modelStr = Redis::get($cacheKey.':'.$this->id);

        if (is_null($modelStr)) {
            throw new Exception('Model not Found in Cache');
        }

        $model = unserialize($modelStr);
        $model->save();
    }

    public function updateInCacheOrRefresh(): mixed
    {
        if (isset($this->id)) {
            throw new Exception('id not set');
        }

        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();

        Redis::set($cacheKey.':'.$this->id, serialize($this));
        $modelStr = Redis::get($cacheKey.':'.$this->id);

        if (is_null($modelStr)) {
            self::class::refreshCache();
            $modelStr = Redis::get($cacheKey.':'.$this->id);
        }

        return unserialize($modelStr);
    }

    public function deleteInCache(): void
    {
        if (isset($this->id)) {
            throw new Exception('id not set');
        }

        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();
        Redis::del([$cacheKey.':'.$this->id]);
    }

    public function deleteInCacheOrFail(): void
    {
        if (isset($this->id)) {
            throw new Exception('id not set');
        }

        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();

        if (is_null(Redis::get($cacheKey.':'.$this->id))) {
            throw new Exception('Model not Found in Redis');
        } else {
            Redis::del([$cacheKey.':'.$this->id]);
        }
    }

    public static function deleteAllInCache(): void
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();

        $keys = Redis::keys($cacheKey.':*');
        for ($i = 0; $i < count($keys); $i++) {
            $keys[$i] = strstr($keys[$i], $cacheKey);
        }

        Redis::del($keys);
    }

    public static function refreshCache(int $fromId = 1, int $toId = null): void
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();
        $model = $reflectionClass->newInstanceWithoutConstructor();

        if ($toId == null) {
            $count = (self::class)::count();
            $models = (self::class)::where([['id', '>=', $fromId], ['id', '<=', $count]])->get();
        } else {
            $models = (self::class)::where([['id', '>=', $fromId], ['id', '<=', $toId]])->get();
        }

        foreach ($models as $model) {
            Redis::set($cacheKey.':'.$model->id, serialize($model));
        }
    }
}
