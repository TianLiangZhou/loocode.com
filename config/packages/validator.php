<?php

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $config) {
    $config->validation()
        ->emailValidationMode('html5')
        ->enableAnnotations(true);
};
