<?php

namespace Insomnicles\Laracache;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

trait Cachable
{

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
