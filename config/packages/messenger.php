<?php

use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return function (FrameworkConfig $config) {
    $config->messenger()
        ->transport('amqp')
        ->dsn(env('MESSENGER_TRANSPORT_DSN'));
};
