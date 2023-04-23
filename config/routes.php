<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $router) {
    $router->import(__DIR__ . "/../src/Controller/*.php", 'attribute');

};
