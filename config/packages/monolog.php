<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\Monolog\HandlerConfig\ExcludedHttpCodeConfig;
use Symfony\Config\MonologConfig;

return static function(MonologConfig $monologConfig, ContainerConfigurator $configurator) {
    $monologConfig->channels([
        'deprecation'
    ]);
    switch ($configurator->env()) {
        case 'prod':
            $monologConfig->handler('main')
                ->type('fingers_crossed')
                ->actionLevel('error')
                ->handler('nested')
                ->excludedHttpCode(new ExcludedHttpCodeConfig(['code' => 404]))
                ->excludedHttpCode(new ExcludedHttpCodeConfig(['code' => 405]))
                ->bufferSize(50);
            $monologConfig->handler('nested')
                ->type('stream')
                ->path('%kernel.logs_dir%/%kernel.environment%.log')
                ->formatter('monolog.formatter.json')
                ->level('debug');
            $monologConfig->handler('console')
                ->type('console')
                ->processPsr3Messages(false)
                ->channels()->elements(['!event', '!doctrine']);
            $monologConfig->handler('deprecation')
                ->type('stream')
                ->path('php://stderr')
                ->channels()->elements(['deprecation']);
            break;
        case 'test':
            $monologConfig->handler('main')
                ->type('fingers_crossed')
                ->actionLevel('error')
                ->handler('nested')
                ->excludedHttpCode(new ExcludedHttpCodeConfig(['code' => 404]))
                ->excludedHttpCode(new ExcludedHttpCodeConfig(['code' => 405]))
                ->channels()->elements(['!event']);
            $monologConfig->handler('nested')
                ->type('stream')
                ->path('%kernel.logs_dir%/%kernel.environment%.log')
                ->level('debug');
            break;
        default:
            $monologConfig->handler('main')
                ->type('stream')
                ->path('%kernel.logs_dir%/%kernel.environment%.log')
                ->level('debug')
                ->channels()->elements(['!event']);
            $monologConfig->handler('console')
                ->type('console')
                ->processPsr3Messages(false)
                ->channels()->elements(['!event', '!doctrine', '!console']);
    }

};
