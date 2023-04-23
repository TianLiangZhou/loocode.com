<?php

namespace OctopusPress\Plugin\OctopusSeo\Controller;

use OctopusPress\Bundle\Controller\Admin\AdminController;
use OctopusPress\Bundle\Customize\Control;
use OctopusPress\Bundle\Customize\Draw;
use OctopusPress\Bundle\Customize\GroupControl;
use OctopusPress\Bundle\Customize\ImageControl;
use OctopusPress\Bundle\Customize\Layout\Form;
use OctopusPress\Bundle\Customize\Layout\Tabs;
use OctopusPress\Bundle\Entity\Option;
use OctopusPress\Bundle\Util\Formatter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AdminController
{

    #[Route("/octopus_seo", name: 'octopus_seo', options: ['name' => '章鱼 SEO', 'icon' => 'options-2-outline', 'sort' => 5], methods: Request::METHOD_GET)]
    #[Route("/octopus_seo/default", name: 'octopus_seo_default', options: ['name' => '常规', 'parent'=>'octopus_seo', 'icon' => 'options-2-outline', 'sort' => 1, 'link' => '/app/plugin/feature'], methods: Request::METHOD_GET)]
    public function default(): JsonResponse
    {
        $maps = $this->getValue($this->getOption());
        $draw = Draw::builder();
        $draw->title('章鱼 SEO - 常规');
        $tabs = $draw->tabs();
        $this->addBasics($tabs, $maps);
        $this->addConnection($tabs, $maps);
        return $this->json($draw);
    }

    #[Route("/octopus_seo/type", name: 'octopus_seo_type', options: ['name' => '内容', 'icon' => 'options-2-outline', 'sort' => 2, 'parent'=>'octopus_seo', 'link' => '/app/plugin/feature'], methods: Request::METHOD_GET)]
    public function type(): JsonResponse
    {
        $maps = $this->getValue($this->getOption());
        $draw = Draw::builder();
        $draw->title('章鱼 SEO - 内容');
        $tabs = $draw->tabs();
        $this->addHome($tabs, $maps);
        $this->addTypes($tabs, $maps);
        return $this->json($draw);
    }


    #[Route("/octopus_seo/taxonomy", name: 'octopus_seo_taxonomy', options: ['name' => '归类', 'icon' => 'options-2-outline', 'sort' => 3, 'parent'=>'octopus_seo', 'link' => '/app/plugin/feature'], methods: Request::METHOD_GET)]
    public function taxonomy(): JsonResponse
    {
        $maps = $this->getValue($this->getOption());
        $draw = Draw::builder();
        $draw->title('章鱼 SEO - 归类');
        $tabs = $draw->tabs();
        $this->addTaxonomies($tabs, $maps);
        return $this->json($draw);
    }



    #[Route("/octopus_seo/save", name: 'octopus_seo_save', options: ['name' => '保存配置', 'parent' => 'octopus_seo'], methods: Request::METHOD_POST)]
    public function save(Request $request): JsonResponse
    {
        $maps = $request->toArray();
        $seo = $this->getOption();
        $defaults = $this->getValue($seo);
        foreach ($maps as $key => $value) {
            if (!isset($defaults[$key])) {
                continue;
            }
            $defaults[$key] = $value;
        }
        $seo->setValue($defaults);
        $optionRepository = $this->bridger->getOptionRepository();
        foreach (['site_title', 'site_subtitle', 'site_description'] as $key) {
            if (isset($maps[$key]) && $maps[$key]) {
                $option = $optionRepository->findOneByName($key);
                if ($option == null) {
                    $option = new Option();
                    $option->setName($key)->setAutoload('yes');
                }
                $option->setValue((string) $maps[$key]);
                $optionRepository->add($option, false);
            }
        }
        $optionRepository->add($seo);
        return $this->json(null);
    }

    /**
     * @return Option
     */
    private function getOption(): Option
    {
        $option = $this->bridger->getOptionRepository()->findOneByName('_octopus_seo');
        if ($option == null) {
            $option = new Option();
            $option->setName('_octopus_seo');
            $option->setAutoload('no');
        }
        return $option;
    }

    /**
     * @param Option $option
     * @return array
     */
    private function getValue(Option $option): array
    {
        $value = $option->getValue();
        $v = [];
        if (!empty($value)) {
            $v = Formatter::reverseTransform($value, true);
        }
        return array_merge($this->getDefaults(), $v);
    }

    private function addBasics(Tabs $tabs, array $values): void
    {
        $basics = new Form();
        $basics->setDirection('row');
        $basics->add('site_title', '站点名称', 'input', [
            'default' => $this->bridger->getOptionRepository()->title(),
        ]);
        $basics->add('site_subtitle', '站点短语', 'input', [
            'default' => $this->bridger->getOptionRepository()->subtitle(),
        ]);
        $basics->add('site_description', '标语', 'input', [
            'default' => $this->bridger->getOptionRepository()->description(),
            'description' => '建议长度小于155个字符',
        ]);
        $basics->add('separator', '分隔符', 'select', [
            'default' => $values['separator'] ?? '-',
            'options' => [
                ['label' => '-', 'value' => '-'],
                ['label' => '|', 'value' => '|'],
                ['label' => '~', 'value' => '~'],
                ['label' => '~', 'value' => '~'],
                ['label' => '—', 'value' => '&ndash;'],
                ['label' => '——', 'value' => '&mdash;'],
            ],
        ]);
        $basics->setSubmit('/octopus_seo/save');
        $tabs->addTab('基本', $basics);
    }

    private function addConnection(Tabs $tabs, array $values): void
    {
        $connections = new Form();
        $connections->add('baidu_verify', '百度', 'input', [
            'default' => $values['baidu_verify'] ?? '',
            'description' => '获取验证码: <a target="_blank" href="https://ziyuan.baidu.com/site">Baidu Webmaster tools</a>.'
        ]);
        $connections->add('google_verify', 'Google', 'input', [
            'default' => $values['google_verify'] ?? '',
            'description' => '获取验证码: <a target="_blank" href="https://www.google.com/webmasters/verification/verification?hl=en&tid=alternate">Google Webmaster tools</a>.'
        ]);
        $connections->add('bing_verify', 'Bing', 'input', [
            'default' => $values['bing_verify'] ?? '',
            'description' => '获取验证码: <a target="_blank" href="https://www.bing.com/toolbox/webmaster/#/Dashboard">Bing Webmaster tools</a>.'
        ]);
        $connections->add('yandex_verify', 'Yandex', 'input', [
            'default' => $values['yandex_verify'] ?? '',
            'description' => '获取验证码: <a target="_blank" href="https://webmaster.yandex.com/sites/add/">Yandex Webmaster tools</a>.'
        ]);
        $connections->add('360_verify', '360', 'input', [
            'default' => $values['360_verify'] ?? '',
            'description' => '获取验证码: <a target="_blank" href="https://zhanzhang.so.com/sitetool/site_manage">360 Webmaster tools</a>.'
        ]);
        $connections->add('sogou_verify', '搜狗', 'input', [
            'default' => $values['sogou_verify'] ?? '',
            'description' => '获取验证码: <a target="_blank" href="https://zhanzhang.sogou.com/index.php/dashboard/index">Sogou Webmaster tools</a>.'
        ]);
        $connections->add('sm_verify', '神马', 'input', [
            'default' => $values['sm_verify'] ?? '',
            'description' => '获取验证码: <a target="_blank" href="https://zhanzhang.sm.cn/">神马 Webmaster tools</a>.'
        ]);
        $connections->setDirection('row');
        $connections->setSubmit('/octopus_seo/save');
        $tabs->addTab('站长平台', $connections);
    }

    private function addHome(Tabs $tabs, array $values): void
    {
        $home = new Form();
        $home->setDirection('row');
        $home->addControl($this->buildGroup('home', 'index', $values['home'] ?? []));
        $home->setSubmit('/octopus_seo/save');
        $tabs->addTab('首页', $home);
    }

    /**
     * @param Tabs $tabs
     * @param array $values
     * @return void
     * @throws \Exception
     */
    private function addTypes(Tabs $tabs, array $values): void
    {
        $postTypes = $this->bridger->getPost()->getTypes();

        foreach ($postTypes as $type) {
            if (!$type->isShowUi() || !$type->isShowOnFront()) {
                continue;
            }
            $group = $this->buildGroup('type_' . $type->getName(), 'type', $values['type_' . $type->getName()] ?? []);
            $form = new Form();
            $form->setDirection('row');
            $form->addControl($group);
            $form->setSubmit('/octopus_seo/save');
            $tabs->addTab($type->getLabel(), $form);
        }
    }


    /**
     * @param Tabs $tabs
     * @param array $maps
     * @return void
     * @throws \Exception
     */
    private function addTaxonomies(Tabs $tabs, array $maps): void
    {
        $taxonomies = $this->bridger->getTaxonomy()->getTaxonomies();

        foreach ($taxonomies as $taxonomy) {
            if (!$taxonomy->isShowUi()) {
                continue;
            }
            $group = $this->buildGroup('taxonomy_' . $taxonomy->getName(), 'taxonomy', $maps['taxonomy_' . $taxonomy->getName()] ?? []);
            $form = new Form();
            $form->setDirection('row');
            $form->addControl($group);
            $form->setSubmit('/octopus_seo/save');
            $tabs->addTab($taxonomy->getLabel(), $form);
        }
    }

    private function buildGroup($name, $type, array $maps = []): GroupControl
    {
        $groupControl = new GroupControl($name, [
            'default' => $maps,
        ]);
        $groupControl->addResource('/plugins/octopus-seo/js/seo.js');
        $title = Control::create('title', 'SEO标题', [
            'description' => $this->getDescription($name, 'title'),
        ]);
        $description = Control::create('description', '元描述', [
            'description' => $this->getDescription($name, 'description'),
        ]);
        $image = ImageControl::create('social_image', '社交图像');

        $socialTitle = Control::create('social_title', '社交标题', [
            'description' => $this->getDescription($name, 'social_title'),
        ]);
        $socialDescription = Control::create('social_description', '社交描述', [
            'description' => $this->getDescription($name, 'social_description'),
        ]);


        $groupControl->addChild($title)
            ->addChild($description)
            ->addChild($image)
            ->addChild($socialTitle)
            ->addChild($socialDescription)
            ;
        if ($type === 'type') {
            $pageType = Control::create('page_type', '页面类型', [
                'type' => 'select',
                'default' => 'WebPage',
                'options' => [
                    ['label' => '网页(default)', 'value' => 'WebPage'],
                    ['label' => '项目页面', 'value' => 'ItemPage'],
                    ['label' => '关于页面', 'value' => 'AboutPage'],
                    ['label' => 'FAQ页面', 'value' => 'FAQPage'],
                    ['label' => '问答页面', 'value' => 'QAPage'],
                    ['label' => '资料页面', 'value' => 'ProfilePage'],
                    ['label' => '联系页面', 'value' => 'ContactPage'],
                    ['label' => '医学页面', 'value' => 'MedicalWebPage'],
                    ['label' => '收藏页面', 'value' => 'CollectionPage'],
                    ['label' => '结算页面', 'value' => 'CheckoutPage'],
                    ['label' => '房地产列表', 'value' => 'RealEstateListing'],
                    ['label' => '搜索结果页', 'value' => 'SearchResultsPage'],
                ],
            ]);


            $articleType = Control::create('article_type', '内容类型', [
                'type' => 'select',
                'default' => 'Article',
                'options' => [
                    ['label' => '文章(default)', 'value' => 'Article'],
                    ['label' => 'Blog Post', 'value' => 'BlogPosting'],
                    ['label' => '社交文章', 'value' => 'SocialMediaPosting'],
                    ['label' => '新闻文章', 'value' => 'NewsArticle'],
                    ['label' => '广告软文', 'value' => 'AdvertiserContentArticle'],
                    ['label' => '学术文章', 'value' => 'ScholarlyArticle'],
                    ['label' => '技术文章', 'value' => 'TechArticle'],
                    ['label' => '报告', 'value' => 'Report'],
                    ['label' => '无', 'value' => 'None'],
                ],
            ]);
            $groupControl->addChild($pageType)
                ->addChild($articleType);
        }


        return $groupControl;
    }

    /**
     * @param string $parent
     * @param string $controlId
     * @return string
     */
    private function getDescription(string $parent, string $controlId): string
    {
        $buttons = '<button type="button" nbbutton data-parent="'.$parent.'" data-control="'.$controlId.'" data-value="%name%" class="seo-variable mx-2 appearance-filled size-tiny shape-rectangle status-control nb-transition">网站标题</button>';
        if (str_starts_with($parent, 'type_')) {
            $buttons .= '<button type="button" nbbutton data-parent="'.$parent.'" data-control="'.$controlId.'" data-value="%title%" class="seo-variable mx-2 appearance-filled size-tiny shape-rectangle status-control nb-transition">标题</button>';
            $buttons .= '<button type="button" nbbutton data-parent="'.$parent.'" data-control="'.$controlId.'" data-value="%category%" class="seo-variable mx-2 appearance-filled size-tiny shape-rectangle status-control nb-transition">主要类别</button>';
            $buttons .= '<button type="button" nbbutton data-parent="'.$parent.'" data-control="'.$controlId.'" data-value="%excerpt%" class="seo-variable mx-2 appearance-filled size-tiny shape-rectangle status-control nb-transition">概要</button>';
        } elseif (str_starts_with($parent, 'taxonomy_')) {
            $buttons .= '<button type="button" nbbutton data-parent="'.$parent.'" data-control="'.$controlId.'" data-value="%title%" class="seo-variable mx-2 appearance-filled size-tiny shape-rectangle status-control nb-transition">述语标题</button>';
            $buttons .= '<button type="button" nbbutton data-parent="'.$parent.'" data-control="'.$controlId.'" data-value="%level%" class="seo-variable mx-2 appearance-filled size-tiny shape-rectangle status-control nb-transition">述语层次结构</button>';
            $buttons .= '<button type="button" nbbutton data-parent="'.$parent.'" data-control="'.$controlId.'" data-value="%desc%" class="seo-variable mx-2 appearance-filled size-tiny shape-rectangle status-control nb-transition">描述</button>';
        } else {
            $buttons .= '<button type="button" nbbutton data-parent="'.$parent.'" data-control="'.$controlId.'" data-value="%slogan%" class="seo-variable mx-2 appearance-filled size-tiny shape-rectangle status-control nb-transition">网站副标题</button>';
            $buttons .= '<button type="button" nbbutton data-parent="'.$parent.'" data-control="'.$controlId.'" data-value="%description%" class="seo-variable mx-2 appearance-filled size-tiny shape-rectangle status-control nb-transition">标语</button>';
        }
        $buttons .= '<button type="button" nbbutton data-parent="'.$parent.'" data-control="'.$controlId.'" data-value="%separator%" class="seo-variable mx-2 appearance-filled size-tiny shape-rectangle status-control nb-transition">分隔线</button>';
        return '<div class="d-flex align-items-center">可用变量: '. $buttons.'</div>';
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        $default = [
            'baidu_verify' => '',
            'google_verify' => '',
            'bing_verify' => '',
            'yandex_verify' => '',
            '360_verify' => '',
            'sogou_verify' => '',
            'sm_verify' => '',
            'separator' => '-',
            'home' => [
                'title' => '%name%%separator%%description%',
                'description' => '%name%%separator%%description%',
                'social_image' => null,
                'social_title' => '%name%',
                'social_description' => '%name%%separator%%description%',
            ],
            'author' => [
                'title' => '%title%%separator%%name%',
                'description' => '%intro%',
                'social_image' => null,
                'social_title' => '%title%%separator%%name%',
                'social_description' => '%intro%',
            ],
        ];
        $postTypes = $this->bridger->getPost()->getTypes();
        foreach ($postTypes as $type) {
            if (!$type->isShowUi() || !$type->isShowOnFront()) {
                continue;
            }
            $default['type_' . $type->getName()] = [
                'title' => '%title%%separator%%name%',
                'description' => '%title%%separator%%name%',
                'social_image' => null,
                'social_title' => '%title%%separator%%name%',
                'social_description' => '%title%%separator%%name%',
                'page_type' => 'WebPage',
                'article_type' => 'Article',
            ];
        }
        $taxonomies = $this->bridger->getTaxonomy()->getTaxonomies();
        foreach ($taxonomies as $taxonomy) {
            if (!$taxonomy->isShowUi()) {
                continue;
            }
            $default['taxonomy_' . $taxonomy->getName()] = [
                'title' => '%title%%separator%%name%',
                'description' => '%title%%separator%%name%',
                'social_image' => null,
                'social_title' => '%title%%separator%%name%',
                'social_description' => '%title%%separator%%name%',
            ];
        }

        return $default;
    }

}
