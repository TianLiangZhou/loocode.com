<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

return static function(FrameworkConfig $config, ContainerConfigurator $configurator) {
    $config->router()
        ->utf8(true)
        ->defaultUri('');

    if ($configurator->env() == 'prod') {
        $config->router()
            ->strictRequirements(null);
    }
};
