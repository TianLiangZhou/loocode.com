<?php

use Symfony\Config\SensioFrameworkExtraConfig;

return static function (SensioFrameworkExtraConfig $config) {
    $config->router()
        ->annotations(false);
    $config->request()
        ->autoConvert(false)
        ->converters(false);
    $config->view()
        ->annotations(false);
    $config->cache()
        ->annotations(false);
    $config->security()
        ->annotations(true);
};
