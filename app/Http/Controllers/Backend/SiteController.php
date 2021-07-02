<?php
declare(strict_types=1);

namespace App\Http\Controllers\Backend;


use App\Attributes\Route;
use App\Helpers\Helper;
use App\Http\Result;
use App\Services\OptionService;
use Corcel\Model\Option;
use Illuminate\Http\Request;

/**
 * Class SiteController
 * @package App\Http\Controllers\Backend
 */
#[Route(title: "设置", sort: 111, icon: "settings-2")]
class SiteController extends BackendController
{
    private array $defaultGeneralNames = [
        'site_title',
        'site_append_title',
        'site_url',
        'site_static_url',
        'site_description',
        'site_keyword',
        'timezone'
    ];
    private array $defaultAdNames = [
        'google_ad',
        'baidu_ad',
        'google_ad_open',
        'baidu_ad_open',

        'baidu_analysis',
        'google_analysis',
        'cnzz_analysis',
    ];

    /**
     * @var OptionService
     */
    private OptionService $optionService;

    public function __construct(OptionService $optionService)
    {
        $this->optionService = $optionService;
        parent::__construct();
    }

    /**
     * @return Result
     */
    #[Route(title: "站点", sort: 0, link: "/app/system/site")]
    public function anchor(): Result
    {
        return Result::ok();
    }

    /**
     * @return Result
     */
    #[Route(title: "配置信息", parent: "站点")]
    public function options(): Result
    {
        $option = $this->optionService->options($this->defaultGeneralNames);
        if (empty($option['timezone'])) {
            $option['timezone'] = date_default_timezone_get();
        }
        $data = new \stdClass();
        $data->timezone = timezone_identifiers_list();
        $data->option = $option;
        return Result::ok($data);
    }

    /**
     * @param Request $request
     * @return Result
     */
    #[Route(title: "保存配置", parent: "站点")]
    public function saveGeneral(Request $request): Result
    {
        return $this->save($request, $this->defaultGeneralNames);
    }

    /**
     * @return Result
     */
    #[Route(title: "广告统计配置", parent: "站点")]
    public function adOptions(): Result
    {
        $option = $this->optionService->options($this->defaultAdNames);
        foreach ($this->defaultAdNames as $name) {
            if (!isset($option[$name])) {
                $option[$name] = null;
            } else {
                $option[$name] = Helper::formatValue($option[$name]);
            }
        }
        return Result::ok($option);
    }

    /**
     * @param Request $request
     * @return Result
     */
    #[Route(title: "保存广告统计配置", parent: "站点")]
    public function saveAd(Request $request): Result
    {
        return $this->save($request, $this->defaultAdNames);
    }

    /**
     * @param Request $request
     * @param array $options
     * @return Result
     */
    private function save(Request $request, array $options): Result
    {
        $body = $request->json()->all();
        $enableOption = [];
        foreach ($options as $name) {
            if (isset($body[$name])) {
                $enableOption[$name] = $body[$name];
            }
        }
        $this->optionService->saveOptions($enableOption);
        return Result::ok(null, "创建成功");
    }
}
