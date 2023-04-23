<?php

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework) {
    $cache = $framework->cache();
    if (($_ENV['CACHE_DRIVER'] ?? '') === 'redis') {
        $cache->app('app.cache.adapter.redis')
            ->defaultRedisProvider('app.cache.redis.provider')
            ->system('cache.adapter.apcu');
    } else {
        $cache->app('cache.adapter.filesystem')
            ->system('cache.adapter.system')
            ->directory('%kernel.cache_dir%/pools');
    }
    $cache->pool('doctrine.metadata_cache_driver')
        ->adapters('cache.system');
    $cache->pool('doctrine.system_cache_pool')
        ->adapters('cache.system');
    $cache->pool('doctrine.result_cache_pool')
        ->adapters('cache.app');

};
