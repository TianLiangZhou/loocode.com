<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;
use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $frameworkConfig, ContainerBuilder $container) {
    $sessionConfig = $frameworkConfig->secret(env('APP_SECRET'))
        ->httpMethodOverride(false)
        ->session();
    $sessionConfig->enabled(true)
        ->name(env('SESSION_NAME'))
        ->cookieLifetime(3600)
        ->cookieHttponly(true)
        ->cookieSecure('auto')
        ->cookieSamesite('lax');
    if (($_ENV['SESSION_DRIVER'] ?? '') === 'redis') {
        $sessionConfig->handlerId(RedisSessionHandler::class);
    } else {
        $sessionConfig->handlerId(null);
        $sessionConfig->storageFactoryId('session.storage.factory.native');
    }

    $frameworkConfig->phpErrors()
        ->log();
    $frameworkConfig->csrfProtection()
        ->enabled(false);
    $env = $container->getParameter('kernel.environment');
    if ($env === 'test') {
        $frameworkConfig->test(true);
        $sessionConfig->storageFactoryId('session.storage.factory.mock_file');
    }
    $assetsConfig = $frameworkConfig->assets();
    $assetsConfig->baseUrls(env('ASSETS_URL'));
    $assetsConfig->version(OctopusPress\Bundle\OctopusPressKernel::OCTOPUS_PRESS_VERSION);
    $assetsConfig->versionFormat("%%s?v=%%s");

    $frameworkConfig->httpClient()
        ->maxHostConnections(3)
    ;
};
