<?php

use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return function (FrameworkConfig $config) {
    $config->mailer()
        ->dsn(env('MAILER_DSN'));
};
