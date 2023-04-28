<?php
declare(strict_types=1);

namespace App\Controller;


use FastFFI\LAC\LAC;
use FastFFI\OCR\OCR;
use FastFFI\Opencc\OpenCC;
use FastFFI\Pinyin\Pinyin;
use FastFFI\QrCode\QrCode;
use OctopusPress\Bundle\Bridge\Bridger;
use OctopusPress\Bundle\Controller\Controller;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ToolController
 */
class ToolController extends Controller
{
    private $tools = [
        'pinyin' => [
            'name' => '汉字拼音',
            'href' => '/tool/pinyin/chinese-to-pinyin',
            'title' => '在线汉字转拼音_汉字音标_汉字汉语转拼单_汉字音标_汉字首字母_中文转拼音_中文转音标',
            'description' => '在线汉字转拼音是通过将中文汉字转换成汉字拼音形式，它支持多种模式无音标、首字母、多音字等等',
        ],
        'opencc' => [
            'name' => '简体繁体',
            'href' => '/tool/opencc/simplified-chinese-to-traditional-chinese',
            'title' => '在线简体转繁体_繁体转简体_简体转台湾繁体_简体转香港繁体_繁体转中文简体_opencc在线测试',
            'description' => '中文简体转繁体是通过OpenCC库快速将简体转成繁体、繁体转简体、简体转台湾繁体、简体转香港繁体的在线工具',
        ],
        'lac' => [
            'name' => '中文分词',
            'href' => '/tool/lac/chinese-word-segmentation',
            'title' => '在线百度LAC中文分词_百度LAC分词_LAC分词在线体验_中文分词_中文在线分词_分词权重_在线智能分词_百度LAC在线测试',
            'description' => '中文分词可以帮助您对中文文本进行精确且快速的分词。无论您是从事中文自然语言处理的研究者，还是需要对中文文本进行分析的业务用户，都可以使用我的工具轻松地进行中文分词。它使用了最新的中文分词算法，能够识别中文词汇的各种不同形式和用法，并生成与之相应的分词结果。',
        ],
        'qrcode' => [
            'name' => '二维码',
            'href' => '/tool/qrcode/qr-code-generator',
            'title' => '在线二维码生成_快速生成二维码_SVG二维码生成_字符二维码生成_二维码生成器_PHP二维码生成',
            'description' => '在线二维码生成器可以帮助您快速生成用于各种用途的高质量二维码。它支持多种类型的二维码，例如URL、文本、电话号码、电子邮件、Wi-Fi设置、地理位置等，可以满足您的不同需求。您只需输入相关信息并选择二维码的类型，即可生成一个清晰、易于扫描的二维码图像。此外，该工具还支持自定义样式和颜色，让您可以根据需求自由设计二维码的外观，从而使其更能在各种场合使用。',
        ],
        'ocr' => [
            'name' => '图文识别',
            'href' => '/tool/ocr/ocr-recognition',
            'title' => '在线OCR识别_图文识别_百度paddleOCR在线测试_OCR识别_paddleOCR测试',
            'description' => '图文识别基于百度paddleOCR扩展库提取图片上的文字信息',
        ],
        'json'=> [
            'name' => 'JSON美化',
            'href' => '/tool/json/json-beautifier',
            'title' => '在线JSON美化_JSON格式化_JSON解析',
            'description' => '在线JSON格式化可以帮助您轻松清晰地格式化和美化任何JSON数据。无论您是开发人员还是普通用户，只需粘贴您的JSON数据即可快速生成可读性更高的格式。它不仅能够识别错误和缺失的括号和逗号，还可以将嵌套的JSON数据更清晰地显示出来。',
        ],
        'json-go' => [
            'name' => 'JSON转Go结构体',
            'href' => '/tool/json-go/json-to-golang-struct',
            'title' => '在线JSON转Go结构体_JSON转go_JSON转golang结构体',
            'description' => '在线json转go结构体可以帮助您将JSON数据转换为对应的Go语言结构体。无论您是Go开发人员还是JSON数据清洗者，只需粘贴您的JSON数据即可快速生成对应的Go结构体代码。它可以将JSON字符串的键名和类型信息自动解析为Go结构体的字段名称和类型，省去了手动编写结构体的繁琐步骤。',
        ]
    ];

    static array $tags = [
        'n' => '普通名词', 'f' => '方位名词', 's' => '处所名词', 'nw' => '作品名',
        'nz' => '其他专名', 'v' => '普通动词', 'vd' => '动副词', 'vn' => '名动词',
        'a' => '形容词', 'ad' => '副形词', 'an' => '名形词', 'd' => '副词',
        'm' => '数量词', 'q' => '量词', 'r' => '代词', 'p' => '介词',
        'c' => '连词', 'u' => '助词', 'xc' => '其他虚词', 'w' => '标点符号',
        'PER' => '人名', 'LOC' => '地名', 'ORG' => '机构名', 'TIME' => '时间',
    ];

    static array $weight = [
        0 => '表述的冗余词', 1 => '限定较弱的词', 2 => '强限定的词', 3 => '核心词',
    ];

    /**
     * @param Bridger $bridger
     */
    public function __construct(Bridger $bridger)
    {
        parent::__construct($bridger);
        $hook = $bridger->getHook();
        $hook->add('_seo_tools_page', [$this, 'seo']);
        $hook->add('_seo_tool_single_page', [$this, 'seo']);
        $hook->add('breadcrumb', [$this, 'breadcrumb']);
    }

    /**
     * @param array $crumbs
     * @return array
     */
    public function breadcrumb(array $crumbs): array
    {

        $activatedRoute = $this->bridger->getActivatedRoute();
        array_unshift($crumbs, (object) [
            'title' => '在线工具',
            'route' => 'tools_page',
        ]);

        if ($activatedRoute->getRouteName() !== 'tools_page') {
            $name = $this->bridger->getRequest()->attributes->get('name');
            $crumbs[] = (object) [
                'title' => $this->tools[$name]['name'],
            ];
        }
        return $crumbs;
    }



    public function seo(array $optionVariables)
    {
        $activatedRoute = $this->bridger->getActivatedRoute();
        $optionVariables['options']['description'] = '%description%';
        $optionVariables['options']['social_description'] = '%description%';
        if ($activatedRoute->getRouteName() == 'tools_page') {
            $optionVariables['variables'] = [
                'title' => '常用在线工具_汉语转拼音_汉字转拼音_繁体转简体_中文智能分词_二维码生成_json转go结构体',
                'description' => '常用在线工具包含了在线中文转拼音，繁体转中文简体，中文分词，二维码生成，图片识别等等在线工具',
            ];
        } else {
            $name = $this->bridger->getRequest()->attributes->get('name');
            $optionVariables['variables'] = [
                'title' => $this->tools[$name]['title'],
                'description' =>  $this->tools[$name]['description'],
            ];
        }
        return $optionVariables;
    }

    /**
     * @return Response
     */
    #[Route('/tools', name: 'tools_page')]
    public function index(): Response
    {
        return $this->render('tools/index.html.twig', [
            'tools' => $this->tools,
        ]);
    }

    /**
     * @param string $name
     * @return Response
     */
    #[Route('/tool/{name}/{summary}', name: 'tool_single_page', requirements: [
        'name' => '(pinyin|opencc|lac|qrcode|ocr|json|json-go)',
        'summary' => '(chinese-to-pinyin|simplified-chinese-to-traditional-chinese|chinese-word-segmentation|qr-code-generator|ocr-recognition|json-beautifier|json-to-golang-struct)',
    ])]
    public function tool(string $name): Response
    {
        $tool = $this->tools[$name];
        return $this->render('tools/' . $name . '.html.twig', [
            'tools' => $this->tools,
            'tool' => $tool,
            'name' => $name,
        ]);
    }

    /**
     * @param Request $request
     * @param CacheItemPoolInterface $cache
     * @return Response
     * @throws InvalidArgumentException
     */
    #[Route('/tool/convert', methods: 'POST')]
    public function convert(Request $request, CacheItemPoolInterface $cache): Response
    {
        /**
         * @var $tool 'pinyin' | 'opencc' | 'lac' | 'qrcode' | 'ocr'
         */
        $tool = $request->request->get('tool', '');
        $body = $request->request->all();
        return match ($tool) {
            'pinyin' => $this->pinyin($body),
            'opencc' => $this->opencc($body),
            'lac' => $this->lac($body),
            'qrcode' => $this->qrcode($body, $request->files->get('logo')),
            'ocr' => $this->ocr($cache, $request->files->get('logo')),
            default => $this->json(null),
        };
    }

    /**
     * @param CacheItemPoolInterface $cache
     * @param UploadedFile|null $uploadedFile
     * @return Response
     * @throws InvalidArgumentException
     */
    private function ocr(CacheItemPoolInterface $cache, UploadedFile $uploadedFile = null): Response
    {
        if ($cache->hasItem('ocr:threading')) {
            return $this->json([
                'message' => '请等待其它任务完成!',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
        if ($uploadedFile && ($logo = $this->uploadLogo($uploadedFile))) {
            switch ($logo) {
                case "error-max":
                    return $this->json([
                        'message' => '文件不能大于2M',
                    ], Response::HTTP_NOT_ACCEPTABLE);
                case "error-type":
                    return $this->json([
                        'message' => '必须是图片类型文件',
                    ], Response::HTTP_NOT_ACCEPTABLE);
                case "error":
                    return $this->json([
                        'message' => '上传失败',
                    ], Response::HTTP_SERVICE_UNAVAILABLE);
            }
            $OCR = OCR::new(['use_mkldnn' => 0]);
            $cacheItem = $cache->getItem('ocr:threading');
            $cacheItem->expiresAfter(60);
            $cacheItem->set(1);
            $cache->save($cacheItem);
            $result = $OCR->run($logo);
            unlink($logo);
            return $this->json(['data' => $result ? implode("\n", $result) : ""]);
        }
        return $this->json(null);
    }

    /**
     * @param array $body
     * @return Response
     */
    private function pinyin(array $body): Response
    {
        if (empty($body['text'])) {
            return $this->json([
                'message' => '内容不能为空',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        $text = $body['text'];
        $mode = (int)$body['mode'];
        $obj = Pinyin::new();
        $r = match ($mode) {
            1 => $obj->tone($text, false),
            2 => $obj->plain($text, false),
            3 => $obj->letter($text, false),
            4 => $obj->tone($text, false, true),
            default => "",
        };
        return $this->json(['data' => $r,]);
    }

    /**
     * @param array $body
     * @return Response
     */
    private function opencc(array $body): Response
    {
        if (empty($body['text'])) {
            return $this->json([
                'message' => '内容不能为空',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        $text = $body['text'];
        $mode = (int)$body['mode'];
        $obj = OpenCC::new();
        $r = match ($mode) {
            1 => $obj->s2t($text),
            2 => $obj->t2s($text),
            3 => $obj->s2tw($text),
            4 => $obj->tw2s($text),
            5 => $obj->s2hk($text),
            6 => $obj->hk2s($text),
            default => "",
        };
        return $this->json(['data' => $r,]);
    }


    /**
     * @param array $body
     * @return Response
     */
    private function lac(array $body): Response
    {
        if (empty($body['text'])) {
            return $this->json([
                'message' => '内容不能为空',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        $text = $body['text'];
        $mode = (int)$body['mode'];
        $model = "seg_model";
        switch ($mode) {
            case 2:
                $model = "lac_model";
                break;
            case 3:
                $model = "rank_model";
                break;
        }
        $path = dirname($this->getParameter('kernel.cache_dir')) . '/model/' . $model;
        $obj = LAC::new($path);
        $result = $obj->parse($text);
        $table = $thead = "";
        if (!empty($result['words'])) {
            $words = explode(" ", $result['words']);
            $tags = $weight = [];
            if (!empty($result['tags'])) {
                $tags = explode(" ", $result['tags']);
            }
            if ($mode == 3) {
                $weight = explode(" ", $result['weight']);
            }
            $tr = "";
            unset($words[count($words) - 1]);
            foreach ($words as $key => $word) {
                if (($mode < 3 && ($key + 1) % ($mode == 1 ? 4 : 2) == 1) || $mode == 3) {
                    $tr .= '<tr>';
                }
                $tr .= sprintf('<td class="text-center border dark:border-slate-900 px-4 py-2 dark:text-slate-300 font-medium">%s</td>', $word);
                if ($mode > 1) {
                    $tr .= sprintf(
                        '<td class="text-center border dark:border-slate-900 px-4 py-2 dark:text-slate-300 font-medium">%s</td>',
                        self::$tags[$tags[$key]] ?? '',
                    );
                    if ($mode == 3) {
                        $tr .= sprintf(
                            '<td class="text-center border dark:border-slate-900 px-4 py-2 dark:text-slate-300 font-medium">%s</td>',
                            self::$weight[$weight[$key]] ?? "无",
                        );
                    }
                }
                if (($mode < 3 && ($key + 1) % ($mode == 1 ? 4 : 2) == 0) || $mode == 3) {
                    $tr .= '</tr>';
                }
            }
            $th = "";
            if ($mode > 1) {
                $th = '<th>词</th><th>标注</th>';
                if ($mode == 3) {
                    $th .= '<th>权重</th>';
                } else {
                    $th .= $th;
                }
            }
            if ($th) {
                $thead = <<<EOF
 <thead>
    <tr>
        $th
    </tr>
  </thead>
EOF;
            }
            $table = <<<EOF
<table class="table-auto w-full">
  $thead
  <tbody>
       $tr
  </tbody>
</table>
EOF;
        }
        return $this->json(['data' => $table,]);
    }

    /**
     * @param array $body
     * @return Response
     */
    private function qrcode(array $body, UploadedFile $uploadedFile = null): Response
    {
        if (empty($body['text'])) {
            return $this->json([
                'message' => '内容不能为空',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        try {
            $qrCode = QrCode::new($body['text']);
        } catch (\Throwable $exception) {
            return $this->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $qrCode->withZone(true);
        $image = "";
        switch ($body['mode']) {
            case 1:
                $uploadPath = $this->getParameter('public_dir');
                $path = $uploadPath . '/upload/images/qrcode/' . date('Ymd');
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $filename = $path . '/' . time() . '_' . ord($body['text']) . ".png";
                $qrCode->withFilename($filename);
                if (!empty($body['bg_color']) && preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $body['bg_color'], $bgColor) && $bgColor) {
                    $qrCode->withBgColor($body['bg_color']);
                }
                if (!empty($body['fg_color']) && preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $body['fg_color'], $fgColor) && $fgColor) {
                    $qrCode->withFgColor($body['fg_color']);
                }
                if ($uploadedFile && ($logo = $this->uploadLogo($uploadedFile))) {
                    switch ($logo) {
                        case "error-max":
                            return $this->json([
                                'message' => '文件不能大于2M',
                            ], Response::HTTP_NOT_ACCEPTABLE);
                        case "error-type":
                            return $this->json([
                                'message' => '必须是图片类型文件',
                            ], Response::HTTP_NOT_ACCEPTABLE);
                        case "error":
                            return $this->json([
                                'message' => '上传失败',
                            ], Response::HTTP_SERVICE_UNAVAILABLE);
                    }
                    $qrCode->withLogo($logo, true, false);
                }
                $name = $qrCode->image();
                if ($name) {
                    $image = sprintf(
                        "<img src=\"%s\" alt=\"%s\" />",
                        str_replace($uploadPath, $this->bridger->getAssetUrl(), $name),
                        pathinfo($name, PATHINFO_BASENAME)
                    );
                }
                if (!empty($logo)) {
                    unlink($logo);
                }
                break;
            case 3:
                if (!empty($body['char'])) {
                    $qrCode->withFgColor($body['char']);
                }
                $qrCode->withZone(false);
                $qrCode->withDimension(2);
                $chars = $qrCode->character();
                $image = sprintf("<pre>%s</pre>", $chars);
                break;
            default:
                if (!empty($body['bg_color']) && preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $body['bg_color'], $bgColor) && $bgColor) {
                    $qrCode->withBgColor($body['bg_color']);
                }
                if (!empty($body['fg_color']) && preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $body['fg_color'], $fgColor) && $fgColor) {
                    $qrCode->withFgColor($body['fg_color']);
                }
                $image = $qrCode->svg();
                if (!empty($image)) {
                    $image = sprintf(
                        "%s <textarea rows='10' class='w-full text-sm border-2 border-gray-200'>%s</textarea>",
                        $image,
                        $image,
                    );
                }
        }
        return $this->json([
            'data' => $image,
        ]);
    }

    private function uploadLogo(UploadedFile $logo): string
    {
        if ($logo->getSize() > 2 * 1024 * 1024) {
            // 超出大小
            return "error-max";
        }
        if (empty($logo->getPath()) || !in_array($logo->guessExtension(), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'])) {
            // mime类型错误;
            return "error-type";
        }
        $filename = md5($logo->getClientOriginalName()) . '.' . $logo->getClientOriginalExtension();
        $path = dirname($this->bridger->getCacheDir(), 2) . '/temp';
        $logo->move($path, $filename);
        return $path . '/' . $filename;
    }


}
