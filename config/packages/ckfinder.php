<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Config\CkfinderConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (CkfinderConfig $config, ContainerBuilder $builder): void {
    $connectorConfig = $config->connector();
    $connectorConfig
        ->licenseKey(env('CKFINDER_KEY'))
        ->debug($builder->getParameter('kernel.debug'));
    $connectorConfig->images([
        'maxWidth' => 2048,
        'maxHeight'=> 1024,
        'quality'  => 80,
        'sizes' => [],
    ]);
};
