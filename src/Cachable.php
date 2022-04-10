<?php

namespace Insomnicles\Laracache;

trait Cachable
{
    // Build wonderful things

    public function hello()
    {
	dd('hello');
    }
    public static function allInCache() : Collection
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $cacheKey = $reflectionClass->getShortName();

        $id = 1;
        $models = new Collection();
        while(1) {
            $modelStr = Cache::get($cacheKey.":".$id);
            if (is_null($modelStr))
                break;
            $model = unserialize($modelStr);
            $models->put($model->id, $model);
            $id++;
        }
        return $models;
    }
}
