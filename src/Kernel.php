<?php

namespace App;

use OctopusPress\Bundle\Bridge\Bridger;
use OctopusPress\Bundle\OctopusPressKernel;
use OctopusPress\Bundle\Plugin\Manifest;
use OctopusPress\Bundle\Plugin\PluginInterface;
use OctopusPress\Bundle\Plugin\PluginProviderInterface;

class Kernel extends OctopusPressKernel implements PluginInterface
{

    public function launcher(Bridger $bridger): void
    {

    }

    public static function manifest(): Manifest
    {
        return Manifest::builder();
    }

    public function activate(Bridger $bridger): void
    {

    }

    public function uninstall(Bridger $bridger): void
    {
    }

    public function getServices(Bridger $bridger): array
    {
        return [];
    }

    public function provider(Bridger $bridger): ?PluginProviderInterface
    {
        return null;
    }

}
