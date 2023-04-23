<?php

namespace OctopusPress\Plugin\StatisticalAnalysis\Controller;

use OctopusPress\Bundle\Controller\Admin\AdminController;
use OctopusPress\Bundle\Customize\AbstractControl;
use OctopusPress\Bundle\Customize\Draw;
use OctopusPress\Bundle\Entity\Option;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StatisticalController extends AdminController
{
    #[Route("/statistical/analysis-option", name: 'statistical_analysis_option', options: ['name' => '统计分析', 'parent' => 'setting', 'sort' => 3, 'link' => '/app/plugin/feature'], methods: Request::METHOD_GET)]
    public function analysisForm(): JsonResponse
    {
        $option = $this->bridger->getOptionRepository();
        $draw = Draw::builder()
            ->title('统计分析参数配置');
        $form = $draw->form()->setDirection('row');
        $form->add('_baidu_analysis_identity', '百度统计标识', AbstractControl::INPUT, [
            'default' => $option->value('_baidu_analysis_identity', ''),
            'description' => '查看https://tongji.baidu.com/，登录后查看代码获取，使用"hm.js?"后面这串标识。',
        ]);
        $form->add('_google_analysis_identity', '谷歌统计标识', AbstractControl::INPUT, [
            'default' => $option->value('_google_analysis_identity', ''),
            'description' => '查看https://analytics.google.com/analytics/web/。使用的是"衡量 ID"。',
        ]);
        $form->add('_baidu_analysis', '开启百度统计', 'switch', ['default' => $option->value('_baidu_analysis', false)]);
        $form->add('_google_analysis', '开启谷歌统计', 'switch', ['default' => $option->value('_google_analysis', false)]);
        $form->setSubmit('/statistical/analysis');
        return $this->json($draw);
    }

    #[Route("/statistical/analysis", name: 'statistical_analysis', options: ['name' => '保存统计分析配置', 'parent' => 'statistical_analysis_option'], methods: Request::METHOD_POST)]
    public function analysis(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $option = $this->bridger->getOptionRepository();
        $keys = ['_baidu_analysis_identity', '_google_analysis_identity', '_baidu_analysis', '_google_analysis'];
        $entities = [];
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                continue;
            }
            $op = $option->findOneByName($key);
            if ($op == null) {
                $op = new Option();
                $op->setName($key);
            }
            $op->setValue($data[$key]);
            $entities[] = $op;
        }
        $c = count($entities);
        foreach ($entities as $i => $entity) {
            $option->add($entity, $i === ($c - 1));
        }
        return $this->json([]);
    }
}
