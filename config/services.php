<?php

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return function (ContainerConfigurator $configurator, ContainerBuilder $container) {
    if ($configurator->env() === 'prod') {
        $configurator->parameters()->set('container.dumper.inline_factories', true);
    }
    $container->register('Redis', \Redis::class)
        ->addMethodCall('connect', ['%env(REDIS_HOST)%', '%env(int:REDIS_PORT)%']);
    $container
        ->register(RedisSessionHandler::class)
        ->setArguments([
            new Reference('Redis'),
            ['prefix' => 'sess:', 'ttl' => 3600],
        ]);
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();
    $services->load("App\\", "../src/*")
         ->exclude('../src/{DependencyInjection,Entity,Tests,Kernel.php}');

    $services->set('app.cache.redis.provider', \Redis::class)
        ->factory([RedisAdapter::class, 'createConnection'])
        ->args([
            'redis://%env(REDIS_HOST)%:%env(int:REDIS_PORT)%',
            [
                'retry_interval' => 2,
                'timeout' => 10
            ]
        ]);
    $services->set('app.cache.adapter.redis')
        ->parent('cache.adapter.redis')
        ->tag('cache.pool', ['namespace' => '%env(SESSION_NAME)%']);
};
