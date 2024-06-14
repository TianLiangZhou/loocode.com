<?php

namespace App;

use App\Controller\ToolController;
use OctopusPress\Bundle\Bridge\Bridger;
use OctopusPress\Bundle\Customize\AbstractControl;
use OctopusPress\Bundle\Customize\ImageControl;
use OctopusPress\Bundle\Entity\Post;
use OctopusPress\Bundle\OctopusPressKernel;
use OctopusPress\Bundle\Plugin\PluginInterface;
use OctopusPress\Bundle\Plugin\PluginProviderInterface;

class Kernel extends OctopusPressKernel implements PluginInterface
{

    public function launcher(Bridger $bridger): void
    {
        $page = $bridger->getPost()->getType(Post::TYPE_PAGE);
        $page->addSupport('name')->addSupports();

        $devicons = json_decode(file_get_contents($this->getProjectDir() . '/devicons.json'), true);
        $meta = $bridger->getMeta();
        $meta->registerPost(Post::TYPE_POST, 'devicon', [], [
            'type' => AbstractControl::SELECT_SEARCH,
            'label' => '主标签',
            'options' => $devicons,
        ]);
        $meta->registerUser('donate_alipay', [], ImageControl::create('donate_alipay', '捐赠支付宝', [
            "width" => 200,
            "height"=> 0,
        ]))->registerUser('donate_wechat', [], ImageControl::create('donate_wechat', '捐赠微信', [
            "width" => 200,
            "height"=> 0,
        ]));
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
