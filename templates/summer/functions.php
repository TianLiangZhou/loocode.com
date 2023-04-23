<?php

use OctopusPress\Bundle\Bridge\Bridger;
use OctopusPress\Bundle\Customize\Control;
use OctopusPress\Bundle\Event\FilterEvent;
use OctopusPress\Bundle\Model\CustomizeManager;

function registerThemeCustomize(CustomizeManager $manager): void
{
    $section = $manager->addDefaultSection('summer', [
        'label' => 'Summer'
    ]);
    $section->addControl(Control::create('beian', '备案号'));
}

return function (Bridger $bridger) {
    $bridger->getHook()
        ->add('body_class', function (array $classes) {
            return array_merge($classes, ['bg-slate-50', 'dark:bg-slate-900']);
        })
        ->add('setup_theme', function(string $theme, FilterEvent $event) {
            $event->getBridger()->getWidget()
                ->get('breadcrumb')
                ->addTemplate('summer/breadcrumb.html.twig');
            $event->getBridger()->getWidget()
                ->get('pagination')
                ->addTemplate('summer/pagination.html.twig')
            ;
        })
        ->add('customize_register', registerThemeCustomize(...))
        ->add('html_attributes', function(array $attributes = []) {
            $attributes[] = 'x-data';
            $attributes[] = ':class="{\'dark\': $store.theme.current == \'Dark\'||$store.theme.system==\'Dark\'}"';
            return $attributes;
        })
        ;

    $theme = $bridger->getTheme();
    $theme->registerThemeNavigation([
        'primary' => '主导航',
    ]);
};
