<?php

use Symfony\Config\TwigConfig;

return static function (TwigConfig $twigConfig) {
    $twigConfig->defaultPath('%kernel.project_dir%/templates');
    $twigConfig->strictVariables(true);
};
