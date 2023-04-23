<?php

use Octopus\Entity\User;
use Octopus\Security\AuthenticationEntryPoint;
use Octopus\Security\Handler\AccessDeniedHandler;
use Octopus\Security\Handler\LoginFailureHandler;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security, ContainerConfigurator $configurator) {

};
