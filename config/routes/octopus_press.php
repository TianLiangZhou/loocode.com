<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function(RoutingConfigurator $configurator) {
    $configurator->import('@OctopusPressBundle/config/routes.php');
};