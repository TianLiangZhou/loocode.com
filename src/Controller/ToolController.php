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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ToolController
 */
class ToolController extends Controller
{
    private array $tools = [
        'chinese-to-pinyin' => [
            'name' => '汉字转拼音',
            'href' => '/tool/chinese-to-pinyin',
            'title' => '在线工具汉字转拼音_汉字音标_汉字汉语转拼单_汉字音标_汉字首字母_中文转拼音_中文转音标',
            'description' => '在线汉字转拼音是通过将中文汉字转换成汉字拼音形式，它支持多种模式无音标、首字母、多音字等等',
        ],
        'simplified-chinese-to-traditional-chinese' => [
            'name' => '简体转繁体',
            'href' => '/tool/simplified-chinese-to-traditional-chinese',
            'title' => '在线工具简体转繁体_繁体转简体_简体转台湾繁体_简体转香港繁体_繁体转中文简体_opencc在线测试',
            'description' => '中文简体转繁体是通过OpenCC库快速将简体转成繁体、繁体转简体、简体转台湾繁体、简体转香港繁体的在线工具',
        ],
        'chinese-word-segmentation' => [
            'name' => '中文分词',
            'href' => '/tool/chinese-word-segmentation',
            'title' => '在线工具百度LAC中文分词_百度LAC分词_LAC分词在线体验_中文分词_中文在线分词_分词权重_在线智能分词_百度LAC在线测试',
            'description' => '中文分词可以帮助您对中文文本进行精确且快速的分词。无论您是从事中文自然语言处理的研究者，还是需要对中文文本进行分析的业务用户，都可以使用我的工具轻松地进行中文分词。它使用了最新的中文分词算法，能够识别中文词汇的各种不同形式和用法，并生成与之相应的分词结果。',
        ],
        'qr-code-generator' => [
            'name' => '二维码',
            'href' => '/tool/qr-code-generator',
            'title' => '在线工具二维码生成_快速生成二维码_SVG二维码生成_字符二维码生成_二维码生成器_PHP二维码生成',
            'description' => '在线二维码生成器可以帮助您快速生成用于各种用途的高质量二维码。它支持多种类型的二维码，例如URL、文本、电话号码、电子邮件、Wi-Fi设置、地理位置等，可以满足您的不同需求。您只需输入相关信息并选择二维码的类型，即可生成一个清晰、易于扫描的二维码图像。此外，该工具还支持自定义样式和颜色，让您可以根据需求自由设计二维码的外观，从而使其更能在各种场合使用。',
        ],
        'ocr-recognition' => [
            'name' => '图文识别',
            'href' => '/tool/ocr-recognition',
            'title' => '在线工具OCR识别_图文识别_百度paddleOCR在线测试_OCR识别_paddleOCR测试',
            'description' => '图文识别基于百度paddleOCR扩展库提取图片上的文字信息',
        ],
        'json-beautifier'=> [
            'name' => 'JSON美化',
            'href' => '/tool/json-beautifier',
            'title' => '在线工具JSON美化_JSON格式化_JSON解析',
            'description' => '在线JSON格式化可以帮助您轻松清晰地格式化和美化任何JSON数据。无论您是开发人员还是普通用户，只需粘贴您的JSON数据即可快速生成可读性更高的格式。它不仅能够识别错误和缺失的括号和逗号，还可以将嵌套的JSON数据更清晰地显示出来。',
        ],
        'json-to-golang-struct' => [
            'name' => 'JSON转Go结构体',
            'href' => '/tool/json-to-golang-struct',
            'title' => '在线工具JSON转Go结构体_JSON转go_JSON转golang结构体',
            'description' => '在线json转go结构体可以帮助您将JSON数据转换为对应的Go语言结构体。无论您是Go开发人员还是JSON数据清洗者，只需粘贴您的JSON数据即可快速生成对应的Go结构体代码。它可以将JSON字符串的键名和类型信息自动解析为Go结构体的字段名称和类型，省去了手动编写结构体的繁琐步骤。',
        ],
        'url-encode-decode' => [
            'name' => 'URL编码解码',
            'href' => '/tool/url-encode-decode',
            'title' => '在线工具URL编码_URL解码_URI编码_URI解码_URL编码解码',
            'description' => '在线URL编码解码可以帮助您将数据转换为对应的URL编码解码数据。只需粘贴您的数据即可快速转换成对应的数据。',
            'group' => 'codec',
        ],
        'base64-encode-decode' => [
            'name' => 'Base64编码解码',
            'href' => '/tool/base64-encode-decode',
            'title' => '在线工具base64编码_Base64解码_base64编码解码',
            'description' => '在线base64编码解码可以帮助您将base64数据转换为对应的base64编码解码数据。只需粘贴您的数据即可快速转换成对应的数据。',
            'group' => 'codec',
        ],
        'to-md5' => [
            'name' => 'MD5',
            'href' => '/tool/to-md5',
            'title' => '在线工具计算MD5、MD2、MD4值',
            'description' => '在线计算字符MD5、MD2、MD4工具可帮助您快速计算从字符串或二进制计算MD5哈希值。MD5消息摘要算法是一种广泛使用的哈希函数，可生成128位哈希值。MD5可用作校验和来验证数据完整性，防止意外损坏。',
            'group' => 'codec',
        ],
        'to-sha1' => [
            'name' => 'SHA1',
            'href' => '/tool/to-sha1',
            'title' => '在线工具计算sha1、sha225、sha256、sha512值',
            'description' => '在线计算字符sha1、sha225、sha256、sha512工具可帮助您快速计算从字符串或二进制计算sha1哈希值。',
            'group' => 'codec',
        ],
        'to-crc32' => [
            'name' => 'CRC32',
            'href' => '/tool/to-crc32',
            'title' => '在线工具计算crc32、crc32b、crc32c值',
            'description' => '在线计算字符crc32、crc32b、crc32c工具可帮助您快速计算从字符串或二进制计算crc32哈希值。',
            'group' => 'codec',
        ],
        'to-hash' => [
            'name' => 'HASH',
            'href' => '/tool/to-hash',
            'title' => '在线工具计算hash值',
            'description' => '在线计算字符Hash工具可帮助您快速计算从字符串或二进制计算哈希值。它支持md5、sha1、sha256、crc32、sha3、ripemd、tiger、haval、xxh等多种hash算法，同时也支持hmac形式计算。',
            'group' => 'codec',
        ],
        'aes-encryption-and-decryption' => [
            'name' => 'AES加密与解密',
            'href' => '/tool/aes-encryption-and-decryption',
            'title' => '在线工具AES加密与解密',
            'description' => '在线AES加密与解密工具可帮助您快速加密字符文本和解密加密文本，它支持AES-128-CBC、AES-192-CBC、AES-256-CBC、AES-128-ECB、AES-192-ECB、AES-256-ECB等多种加密解密算法。',
            'group' => 'codec',
        ],
        'des-encryption-and-decryption' => [
            'name' => 'DES、3DES加密与解密',
            'href' => '/tool/des-encryption-and-decryption',
            'title' => '在线工具DES加密与解密',
            'description' => '在线DES加密与解密工具可帮助您快速加密字符文本和解密加密文本，它支持DES-EDE-CBC、DES-EDE-CFB、DES-EDE-ECB、DES-EDE-OFB、DES-EDE3-CBC、DES-EDE3-CFB、DES-EDE3-ECB、DES-EDE3-OFB等多种加密解密算法。',
            'group' => 'codec',
        ],
        'sm4-encryption-and-decryption' => [
            'name' => 'SM4加密与解密',
            'href' => '/tool/sm4-encryption-and-decryption',
            'title' => '在线工具sm4加密与解密',
            'description' => '在线sm4加密与解密工具可帮助您快速加密字符文本和解密加密文本，它支持SM4-CBC、SM4-CCM、SM4-CFB、SM4-CTR、SM4-ECB、SM4-GCM、SM4-OFB等多种加密解密算法。',
            'group' => 'codec',
        ],
        'rc24-encryption-and-decryption' => [
            'name' => 'RC2、RC4加密与解密',
            'href' => '/tool/rc24-encryption-and-decryption',
            'title' => '在线工具RC2、RC4加密与解密',
            'description' => '在线RC2、RC4加密与解密工具可帮助您快速加密字符文本和解密加密文本，它支持RC2-40-CBC、RC4-64-CBC、RC2-CFB、RC2-ECB、RC2-CBC、RC2-OFB、RC4、RC4-40等多种加密解密算法。',
            'group' => 'codec',
        ],
        'rsa-encryption-and-decryption' => [
            'name' => 'RSA加密与解密',
            'href' => '/tool/rsa-encryption-and-decryption',
            'title' => '在线工具RSA加密与解密',
            'description' => '在线RSA加密与解密工具可帮助您快速加密字符文本和解密加密文本，它支持私钥加密、公钥加密、公钥解密、公钥解密。',
            'group' => 'codec',
        ],
        'rsa-sign-and-verify' => [
            'name' => 'RSA签名与校验',
            'href' => '/tool/rsa-sign-and-verify',
            'title' => '在线工具RSA签名与校验',
            'description' => '在线RSA签名与校验工具可帮助您快速创建签名和校验签名，它支持sha1、sha224、md5等多种签名算法',
            'group' => 'codec',
        ],
        'image-to-base64' => [
            'name' => '图片转Base64',
            'href' => '/tool/image-to-base64',
            'title' => '在线工具图片转Base64_图片Base64解码_图片Base64编码',
            'description' => '在线图片转Base64编码可以帮助您将图片转换为对应的Base64数据。只需选择您的图片文件即可快速转换成对应的Base64数据。',
            'group' => 'codec',
        ],
        'image-compression' => [
            'name' => 'PNG|WEBP|JPEG|JPG图片压缩',
            'href' => '/tool/image-compression',
            'title' => '在线工具压缩WebP、PNG、JPEG、JPG、GIF图像',
            'description' => '图片压缩将您的WebP、PNG 和 JPEG图片优化50-80%，同时保持完全透明！节省您的存储空间，节省您的带宽，更快加载时间，更快的访问速度！',
        ],
        'png-to-webp' => [
            'name' => 'PNG转WEBP',
            'href' => '/tool/png-to-webp',
            'title' => 'PNG转WEBP - 免费在线工具PNG图像转换webp图像',
            'description' => '在线图像转换工具将您的png图像转换成webp格式文件，同时支持jpeg,webp,jpg,heic,gif等格式转换!',
            'group' => 'image-convert',
        ],
        'png-to-jpeg' => [
            'name' => 'PNG转JPEG',
            'href' => '/tool/png-to-jpeg',
            'title' => 'PNG转JPEG - 免费在线工具PNG图像转换JPEG图像',
            'description' => '在线图像转换工具将您的png图像转换成jpeg格式文件，同时支持png,webp,jpg,heic,gif等格式转换!',
            'group' => 'image-convert',
        ],
        'png-to-jpg'  => [
            'name' => 'PNG转JPG',
            'href' => '/tool/png-to-jpg',
            'title' => 'PNG转JPG - 免费在线工具PNG图像转换jpg图像',
            'description' => '在线图像转换工具将您的png图像转换成jpg格式文件，同时支持jpeg,webp,png,heic,gif等格式转换!',
            'group' => 'image-convert',
        ],
        'webp-to-png' => [
            'name' => 'WEBP转PNG',
            'href' => '/tool/webp-to-png',
            'title' => 'WEBP转PNG - 免费在线工具WEBP图像转换PNG图像',
            'description' => '在线图像转换工具将您的webp图像转换成png格式文件，同时支持jpeg,webp,jpg,heic,gif等格式转换!',
            'group' => 'image-convert',
        ],
        'webp-to-jpeg' => [
            'name' => 'WEBP转JPEG',
            'href' => '/tool/webp-to-jpeg',
            'title' => 'WEBP转JPEG - 免费在线工具WEBP图像转换JPEG图像',
            'description' => '在线图像转换工具将您的webp图像转换成jpeg格式文件，同时支持png,webp,jpg,heic,gif等格式转换!',
            'group' => 'image-convert',
        ],
        'jpeg-to-png' => [
            'name' => 'JPEG转PNG',
            'href' => '/tool/jpeg-to-png',
            'title' => 'JPEG转PNG - 免费在线工具jpeg图像转换png图像',
            'description' => '在线图像转换工具将您的jpeg图像转换成png格式文件，同时支持jpeg,webp,jpg,heic,gif等格式转换!',
            'group' => 'image-convert',
        ],
        'jpeg-to-webp' => [
            'name' => 'JPEG转WEBP',
            'href' => '/tool/jpeg-to-webp',
            'title' => 'JPEG转WEBP - 免费在线工具jpeg图像转换webp图像',
            'description' => '在线图像转换工具将您的jpeg图像转换成webp格式文件，同时支持jpeg,png,jpg,heic,gif等格式转换!',
            'group' => 'image-convert',
        ],
        'heic-to-webp' => [
            'name' => 'HEIC转WEBP',
            'href' => '/tool/heic-to-webp',
            'title' => 'HEIC转WEBP - 免费在线工具heic图像转换WEBP图像',
            'description' => '在线图像转换工具将您的heic图像转换成webp格式文件，同时支持jpeg,png,jpg,webp,gif等格式转换!',
            'group' => 'image-convert',
        ],
        'heic-to-png' => [
            'name' => 'HEIC转PNG',
            'href' => '/tool/heic-to-png',
            'title' => 'HEIC转PNG - 免费在线工具heic图像转换png图像',
            'description' => '在线图像转换工具将您的heic图像转换成png格式文件，同时支持jpeg,heic,jpg,webp,gif等格式转换!',
            'group' => 'image-convert',
        ],
        'heic-to-jpeg' => [
            'name' => 'HEIC转JPEG',
            'href' => '/tool/heic-to-jpeg',
            'title' => 'HEIC转JPEG - 免费在线工具heic图像转换jpeg图像',
            'description' => '在线图像转换工具将您的heic图像转换成jpeg格式文件，同时支持png,heic,jpg,webp,gif等格式转换!',
            'group' => 'image-convert',
        ],
        'heic-to-jpg' => [
            'name' => 'HEIC转JPG',
            'href' => '/tool/heic-to-jpg',
            'title' => 'HEIC转JPG - 免费在线工具heic图像转换jpg图像',
            'description' => '在线图像转换工具将您的heic图像转换成jpeg格式文件，同时支持png,heic,jpeg,webp,gif等格式转换!',
            'group' => 'image-convert',
        ],
        'heic-to-avif' => [
            'name' => 'HEIC转AVIF',
            'href' => '/tool/heic-to-avif',
            'title' => 'HEIC转AVIF - 免费在线工具heic图像转换avif图像',
            'description' => '在线图像转换工具将您的heic图像转换成avif格式文件，同时支持png,heic,jpeg,webp,gif等格式转换!',
            'group' => 'image-convert',
        ],
        'avif-to-heic' => [
            'name' => 'AVIF转HEIC',
            'href' => '/tool/avif-to-heic',
            'title' => 'AVIF转HEIC - 免费在线工具avif图像转换heic图像',
            'description' => '在线图像转换工具将您的avif图像转换成heic格式文件，同时支持png,heic,avif,jpeg,webp,gif等格式转换!',
            'group' => 'image-convert',
        ],

        'avif-to-jpeg' => [
            'name' => 'AVIF转JPEG',
            'href' => '/tool/avif-to-jpeg',
            'title' => 'AVIF转JPEG - 免费在线工具avif图像转换jpeg图像',
            'description' => '在线图像转换工具将您的avif图像转换成jpeg格式文件，同时支持png,heic,avif,jpeg,webp,gif等格式转换!',
            'group' => 'image-convert',
        ],
        'avif-to-png' => [
            'name' => 'AVIF转PNG',
            'href' => '/tool/avif-to-png',
            'title' => 'AVIF转PNG - 免费在线工具avif图像转换png图像',
            'description' => '在线图像转换工具将您的avif图像转换成png格式文件，同时支持png,heic,avif,jpeg,webp,gif等格式转换!',
            'group' => 'image-convert',
        ],
        'avif-to-webp' => [
            'name' => 'AVIF转WEBP',
            'href' => '/tool/avif-to-webp',
            'title' => 'AVIF转WEBP - 免费在线工具avif图像转换webp图像',
            'description' => '在线图像转换工具将您的avif图像转换成webp格式文件，同时支持png,heic,avif,jpeg,webp,gif等格式转换!',
            'group' => 'image-convert',
        ],

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
        $hook->add('compare_url', [$this, 'compareUrl']);
    }

    /**
     * @param bool $current
     * @param string $link
     * @param string $pathInfo
     * @return bool
     */
    public function compareUrl(bool $current, string $link, string $pathInfo): bool
    {
        if ($link === '/tools' && $this->bridger->getActivatedRoute()->getRouteName() === 'tool_single_page') {
            return true;
        }
        return $current;
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

    /**
     * @param array $optionVariables
     * @return array
     */
    public function seo(array $optionVariables): array
    {
        $activatedRoute = $this->bridger->getActivatedRoute();
        $optionVariables['options']['description'] = '%description%';
        $optionVariables['options']['social_description'] = '%description%';
        if ($activatedRoute->getRouteName() == 'tools_page') {
            $optionVariables['variables'] = [
                'title' => '常用在线工具_汉语转拼音_汉字转拼音_繁体转简体_中文智能分词_二维码生成_json转go结构体_PNG|WEBP|JPEG图片压缩 - 在线工具',
                'description' => '常用在线工具包含了在线中文转拼音，繁体转中文简体，中文分词，二维码生成，图片识别，图片压缩等等在线工具',
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
    #[Route('/tool/{name}', name: 'tool_single_page', requirements: ['name' => '[a-z0-9\-_]{2,}',], methods: 'GET')]
    public function tool(string $name): Response
    {
        if (!isset($this->tools[$name])) {
            throw $this->createNotFoundException();
        }
        $tool = $this->tools[$name];
        $template = 'tools/' . $name . '.html.twig';
        $features = explode('-', $name);
        $data = [
            'tools' => $this->tools,
            'tool' => $tool,
            'name' => $name,
            'features'    => $features,
            'upload_size' => ini_get('upload_max_filesize'),
        ];
        if (isset($tool['group'])) {
            switch ($tool['group']) {
                case 'image-convert':
                    $template = 'tools/image-convert.html.twig';
                    break;
                case 'codec':
                    $template = 'tools/codec-form.html.twig';
                    if (in_array($name, ['base64-encode-decode', 'url-encode-decode', 'image-to-base64'])) {
                        $template = 'tools/codec.html.twig';
                    }
                    if ($name === 'aes-encryption-and-decryption') {
                        $data['algos'] = array_filter(openssl_get_cipher_methods(), function ($value) {
                            return str_starts_with($value, 'aes-');
                        });
                    } elseif ($name === 'des-encryption-and-decryption') {
                        $data['algos'] = array_filter(openssl_get_cipher_methods(), function ($value) {
                            return str_starts_with($value, 'des-');
                        });
                    } elseif ($name === 'sm4-encryption-and-decryption') {
                        $data['algos'] = array_filter(openssl_get_cipher_methods(), function ($value) {
                            return str_starts_with($value, 'sm4-');
                        });
                    } elseif ($name === 'rc24-encryption-and-decryption') {
                        $data['algos'] = array_filter(openssl_get_cipher_methods(), function ($value) {
                            return str_starts_with($value, 'rc');
                        });
                    } elseif ($name === 'rsa-sign-and-verify') {
                        $data['algos'] = openssl_get_md_methods();
                    } else {
                        $data['algos'] = hash_algos();
                        $data['hmac_algos'] = hash_hmac_algos();
                    }
                    break;
            }
        }
        return $this->render($template, $data);
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
        if (isset($this->tools[$tool]['group'])) {
            switch ($this->tools[$tool]['group']) {
                case 'image-convert':
                    return $this->imageConvert(explode('-', $tool), $request->files->get('image'));
                case 'codec':
                    return $this->toCodec($body);
            }
        }
        return match ($tool) {
            'chinese-to-pinyin' => $this->pinyin($body),
            'simplified-chinese-to-traditional-chinese' => $this->opencc($body),
            'chinese-word-segmentation' => $this->lac($body),
            'qr-code-generator' => $this->qrcode($body, $request->files->get('logo')),
            'ocr-recognition' => $this->ocr($cache, $request->files->get('logo')),
            'image-compression' => $this->imageCompression($request->files->get('image')),
            default => $this->json(null),
        };
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/tool/random_iv', methods: 'POST')]
    public function randomIv(Request $request): JsonResponse
    {
        $algo = $request->get('algo', '');
        if (in_array(!$algo, openssl_get_cipher_methods())) {
            return $this->json([
                'message' => '不支持的算法',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        $len = openssl_cipher_iv_length($algo);
        if ($len < 1) {
            return $this->json([
                'data' => '',
            ]);
        }
        return $this->json([
            'data' => base64_encode(openssl_random_pseudo_bytes($len)),
        ]);
    }

    /**
     * @param array $body
     * @return JsonResponse
     */
    private function toCodec(array $body): JsonResponse
    {
        $text = $body['text'];
        $value = "";
        switch ($body['tool']) {
            case 'to-md5':
            case 'to-sha1':
            case 'to-crc32':
                $mode = (int) ($body['mode'] ?? 1);
                $algo = ['md5', 'md4', 'md2', 'sha1', 'sha224', 'sha256', 'sha512', 'crc32b', 'crc32', 'crc32c',][$mode - 1] ?? 'md5';
                $value = hash($algo, $text);
                if (in_array($mode, [8, 9, 10])) {
                    $value = hexdec($value);
                }
                break;
            case 'to-hash':
                $algo = $body['algo'] ?? 'md5';
                $key = $body['key'] ?? '';
                if ($key) {
                    $value = hash_hmac($algo, $text, $key);
                } else {
                    $value = hash($algo, $text);
                }
                break;
            case 'aes-encryption-and-decryption':
            case 'des-encryption-and-decryption':
            case 'sm4-encryption-and-decryption':
            case 'rc24-encryption-and-decryption':
                $mode = (int) ($body['mode'] ?? 1);
                $algo = $body['algo'] ?? '';
                if (in_array(!$algo, openssl_get_cipher_methods())) {
                    return $this->json([
                        'message' => '不支持的算法',
                    ], Response::HTTP_NOT_ACCEPTABLE);
                }
                $iv = $body['iv'] ?? '';
                if ($iv && isset($body['iv_base64']) && $body['iv_base64'] === "1") {
                    $iv = base64_decode($iv);
                }
                $ivLen = openssl_cipher_iv_length($algo);
                if ($ivLen > 0 && $iv && strlen($iv) !== $ivLen) {
                    return $this->json([
                        'message' => sprintf('算法[%s]IV长度必须是: %d', $algo, $ivLen),
                    ], Response::HTTP_NOT_ACCEPTABLE);
                }
                $key = $body['key'] ?? '';
                if ($key && isset($body['key_base64']) && $body['key_base64'] === "1") {
                    $key = base64_decode($key);
                }
                $option = (int) ($body['option'] ?? 0);
                if ($mode === 1) {
                    if ($option === 1) {
                        $text = base64_decode($text);
                    }
                    $value = openssl_decrypt($text, $algo, $key, $option, $ivLen > 0 ? $iv : '');
                } else {
                    $value = openssl_encrypt($text, $algo, $key, $option, $ivLen > 0 ? $iv : '');
                    if ($option === 1) {
                        $value = base64_encode($value);
                    }
                }
                if ($value === false) {
                    return $this->json([
                        'message' => openssl_error_string(),
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            case 'rsa-encryption-and-decryption':
                $certMode = (int) ($body['cert_mode'] ?? 4);
                $mode = ((int) ($body['mode'] ?? 1)) + $certMode;
                $certContent = $body['cert'] ?? '';
                $cert = $certMode === 4
                        ? $this->getPrivateKey($certContent, $body['cert_pass'] ?? '')
                        : $this->getPublicKey($certContent);
                if ($cert === false) {
                    return $this->json([
                        'data' => openssl_error_string(),
                    ]);
                }
                $encryptedData = $decryptedData = "";
                switch ($mode) {
                    case 5:
                        openssl_private_decrypt(base64_decode($text), $decryptedData, $cert); // 解密由public_key加密的数据
                        break;
                    case 6:
                        openssl_private_encrypt($text, $encryptedData, $cert);
                        break;
                    case 9:
                        openssl_public_decrypt(base64_decode($text), $decryptedData, $cert); // 解密由private_key加密的数据
                        break;
                    case 10:
                        openssl_public_encrypt($text, $encryptedData, $cert);
                        break;
                }
                if ($encryptedData) {
                    $value = base64_encode($encryptedData);
                }
                if ($decryptedData) {
                    $value = $decryptedData;
                }
                break;
            case 'rsa-sign-and-verify':
                if ((int) $body['mode'] == 1) {
                    if (openssl_sign($text, $signature, $this->getPrivateKey($body['cert'], $body['cert_pass'] ?? ''), $body['algo']) === false) {
                        return $this->json([
                            'data' => openssl_error_string(),
                        ]);
                    }
                    return $this->json([
                        'data' => base64_encode($signature),
                    ]);
                }
                if (openssl_verify($text, base64_decode($body['sign']), $this->getPublicKey($body['cert']), $body['algo']) === 1) {
                    return $this->json([
                        'data' => '校验成功',
                    ]);
                }
                return $this->json([
                    'data' => '校验失败:' . openssl_error_string(),
                ]);
        }
        return $this->json([
            'data' => $value,
        ]);
    }

    /**
     * @param string $certContent
     * @param string $pass
     * @return false|\OpenSSLAsymmetricKey
     */
    private function getPrivateKey(string $certContent, string $pass): \OpenSSLAsymmetricKey|bool
    {
        if (!str_starts_with($certContent, '-----BEGIN')) {
            $certContent = <<<EOF
-----BEGIN PRIVATE KEY-----
$certContent
-----END PRIVATE KEY-----
EOF;
        }
        return openssl_get_privatekey($certContent, $pass);
    }

    /**
     * @param string $certContent
     * @return false|\OpenSSLAsymmetricKey
     */
    private function getPublicKey(string $certContent): \OpenSSLAsymmetricKey|bool
    {
        if (!str_starts_with($certContent, '-----BEGIN')) {
            $certContent = <<<EOF
-----BEGIN PUBLIC KEY-----
$certContent
-----END PUBLIC KEY-----
EOF;
        }
        return openssl_get_publickey($certContent);
    }

        /**
     * @param UploadedFile $uploadedFile
     * @return JsonResponse
     */
    private function imageCompression(UploadedFile $uploadedFile): JsonResponse
    {
        $file = $this->uploadLogo($uploadedFile, 5);
        if ($file === 'error-type') {
            return $this->json([
                'message' => '错误的图片类型',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        if ($file === 'error-max') {
            return $this->json([
                'message' => '超出文件大小',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        $pathInfo = pathinfo($file);
        $name = $pathInfo['basename'];
        $path = ($this->bridger->getBuildAssetsDir() ? : $this->bridger->getPublicDir())
        . '/upload/images/compression/' . date('Ymd');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        $output = $path . '/' . $name;
        switch (strtolower($pathInfo['extension'])) {
            case 'webp':
                $process = Process::fromShellCommandline('cwebp '. $file . ' -o ' . $output . ' -q 100 -m 6 -lossless');
                break;
            case 'png':
                $process = Process::fromShellCommandline('oxipng '. $file . ' --out ' . $output . ' --opt max --strip safe');
                break;
            case 'jpeg':
            case 'jpg':
                $process = Process::fromShellCommandline('convert '. $file . ' -quality 70% ' . $output);
                break;
            default:
                return $this->json([
                    'message' => '错误的图片类型',
                ], Response::HTTP_NOT_ACCEPTABLE);
        }
        if (0 !== $process->run()) {
            return $this->json([
                'message' => $process->getErrorOutput(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        unlink($file);
        return $this->json([
            'size'     => filesize($output),
            'download' => $this->bridger->getPackages()->getUrl('/upload/images/compression/' .date('Ymd').'/'.$name),
        ]);
    }

    /**
     * @param array $features
     * @param UploadedFile $uploadedFile
     * @return JsonResponse
     */
    private function imageConvert(array $features, UploadedFile $uploadedFile): JsonResponse
    {
        $file = $this->uploadLogo($uploadedFile, 5);
        if ($file === 'error-type') {
            return $this->json([
                'message' => '错误的图片类型',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        if ($file === 'error-max') {
            return $this->json([
                'message' => '超出文件大小',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        $pathInfo = pathinfo($file);
        $name = $pathInfo['filename'] . '.' . $features[2];
        $path = ($this->bridger->getBuildAssetsDir() ?: $this->bridger->getPublicDir())
            . '/upload/images/compression/' . date('Ymd');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        $output = $path . '/' . $name;
        $process = Process::fromShellCommandline('convert '. $file . ' ' . $output);
        if (0 !== $process->run()) {
            return $this->json([
                'message' => $process->getErrorOutput(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        unlink($file);
        return $this->json([
            'size'     => filesize($output),
            'download' => $this->bridger->getPackages()->getUrl('/upload/images/compression/' .date('Ymd').'/'.$name),
        ]);
    }

    /**
     * @param CacheItemPoolInterface $cache
     * @param UploadedFile|null $uploadedFile
     * @return Response
     * @throws InvalidArgumentException
     */
    private function ocr(CacheItemPoolInterface $cache, UploadedFile $uploadedFile = null): Response
    {
        if ($cache->hasItem('ocr_threading')) {
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
            $cacheItem = $cache->getItem('ocr_threading');
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

    private function uploadLogo(UploadedFile $logo, int $max = 2): string
    {
        if ($logo->getSize() > $max * 1024 * 1024) {
            // 超出大小
            return "error-max";
        }
        if (empty($logo->getPath()) || !in_array($logo->guessExtension(), ['heic', 'avif', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'])) {
            // mime类型错误;
            return "error-type";
        }
        $filename = $logo->getClientOriginalName();
        $logo->move($this->bridger->getTempDir(), $filename);
        return $this->bridger->getTempDir() . '/' . $filename;
    }


}
