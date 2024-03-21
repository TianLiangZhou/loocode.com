<?php

namespace App;

use App\Controller\ToolController;
use OctopusPress\Bundle\Bridge\Bridger;
use OctopusPress\Bundle\Customize\AbstractControl;
use OctopusPress\Bundle\Entity\Post;
use OctopusPress\Bundle\OctopusPressKernel;
use OctopusPress\Bundle\Plugin\PluginInterface;
use OctopusPress\Bundle\Plugin\PluginProviderInterface;

class Kernel extends OctopusPressKernel implements PluginInterface
{

    public function launcher(Bridger $bridger): void
    {

        $devicons = json_decode(file_get_contents($this->getProjectDir() . '/devicons.json'), true);
        $bridger->getMeta()->registerPost(Post::TYPE_POST, 'devicon', [], [
            'type' => AbstractControl::SELECT_SEARCH,
            'label' => '主标签',
            'options' => $devicons,
        ]);
        $bridger->getHook()->add('_seo_sitemap_generator', function (array $urls, string $domain) {
            foreach (ToolController::$tools as $tool) {
                $urls[] = $tool['href'];
            }
            return $urls;
        });
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
