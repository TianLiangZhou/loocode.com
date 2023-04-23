<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DoctrineConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (DoctrineConfig $doctrine, ContainerConfigurator $configurator) {
    $doctrine->dbal()
        ->connection('default')
        ->option(\PDO::ATTR_TIMEOUT, 1.3)
        ->driver('pdo_mysql')
        ->url(env('DATABASE_URL')->resolve())
        ->logging('%kernel.debug%');

    $doctrine->orm()
        ->autoGenerateProxyClasses(true)
        ->defaultEntityManager('default');

    $doctrine->orm()->entityManager('default')
        ->connection('default')
        ->autoMapping(true)
        ->namingStrategy('doctrine.orm.naming_strategy.underscore')
        ->mapping('App')
        ->isBundle(false)
        ->type('attribute')
        ->dir('%kernel.project_dir%/src/Entity')
        ->prefix('App\Entity')
        ->alias('App');
    if ($configurator->env() === 'prod') {
        $doctrine->orm()
            ->entityManager('default')
            ->metadataCacheDriver()
            ->type('pool')
            ->pool('doctrine.system_cache_pool');
        $doctrine->orm()
            ->entityManager('default')
            ->queryCacheDriver()
            ->type('pool')
            ->pool('doctrine.system_cache_pool');
        $doctrine->orm()->entityManager('default')
            ->resultCacheDriver()
            ->type('pool')
            ->pool('doctrine.result_cache_pool');
    }
};
