<?php
namespace OctopusPress\Plugin\PrettyLink;


use OctopusPress\Bundle\Bridge\Bridger;
use OctopusPress\Bundle\Entity\Post;
use OctopusPress\Bundle\Plugin\Manifest;
use OctopusPress\Bundle\Plugin\PluginInterface;
use OctopusPress\Bundle\Plugin\PluginProviderInterface;
use OctopusPress\Bundle\Event\FilterEvent;

class PrettyLink implements PluginInterface
{

    public static function manifest(): Manifest
    {
        // TODO: Implement manifest() method.
        return Manifest::builder()
            ->setName("链接")
            ->addAuthor('OctopusPress.dev', 'https://octopuspress.dev')
            ->setDescription("可帮助您缩小、美化、跟踪、管理和共享网站内外的任何URL。");
    }

    public function launcher(Bridger $bridger): void
    {
        // TODO: Implement launcher() method.
        $bridger->getPost()
            ->registerType('pretty-link', [
                'label' => '链接',
                'supports' => ['title', 'editor', 'name'],
            ]);
        $bridger->getPlugin()
            ->addTypeMenu('pretty-link', '链接', ['icon' => 'link-outline', 'sort' => 4]);
        $bridger->getHook()
            ->add('post_type_link', [$this, 'permalink']);
    }

    /**
     * @param string $url
     * @param Post $post
     * @param FilterEvent $event
     * @return string
     */
    public function permalink(string $url, Post $post, FilterEvent $event): string
    {
        if ($post->getType() === 'pretty-link') {
            $event->stopPropagation();
            return $post->getContent();
        }
        return $url;
    }

    public function activate(Bridger $bridger): void
    {
        // TODO: Implement activate() method.
    }

    public function uninstall(Bridger $bridger): void
    {
        // TODO: Implement uninstall() method.
    }

    public function getServices(Bridger $bridger): array
    {
        // TODO: Implement getServices() method.
        return [];
    }

    public function provider(Bridger $bridger): ?PluginProviderInterface
    {
        // TODO: Implement provider() method.
        return null;
    }
}
