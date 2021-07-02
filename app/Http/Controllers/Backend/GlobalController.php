<?php


namespace App\Http\Controllers\Backend;

use App\Attributes\Route;
use App\Helpers\Helper;
use App\Http\Result;
use App\Services\OptionService;
use Corcel\Model\Option;
use Illuminate\Http\Request;

/**
 * Class GlobalController
 * @package App\Http\Controllers\Backend
 */
#[Route(title: "设置", sort: 111, icon: "settings-2")]
class GlobalController extends BackendController
{
    /**
     * @var OptionService
     */
    private OptionService $optionService;

    /**
     * GlobalController constructor.
     * @param OptionService $optionService
     */
    public function __construct(OptionService $optionService)
    {
        $this->optionService = $optionService;
        parent::__construct();
    }

    /**
     * @return Result
     */
    #[Route(title: "全局", sort: 0, link: "/app/system/configuration")]
    public function anchor(): Result
    {
        return Result::ok();
    }

    /**
     * @param Request $request
     * @return Result
     */
    #[Route(title: "配置列表", parent: "全局")]
    public function options(Request $request): Result
    {
        $options = $this->optionService->getPaginator($request);
        foreach ($options as $option) {
            $value = Helper::formatValue($option->option_value);
            $option->type = 5;
            if (is_bool($value)) {
                $option->type = 1;
            }
            if (is_object($value)) {
                $option->type = 3;
                $option->option_value = $value;
            }
            if (is_array($value)) {
                $option->type = 2;
                if (is_object($value[0])) {
                    $option->type = 4;
                }
                $option->option_value = $value;
            }
            $option->description = "";
        }
        return Result::ok($options);
    }

    /**
     * @param Request $request
     * @return Result
     */
    #[Route(title: "添加配置", parent: "全局")]
    public function store(Request $request): Result
    {
        $data = $request->json()->all();
        if (empty($data['option_name'])) {
            return Result::err(600, "名称不能为空");
        }
        $item = $this->optionService->oneByName($data['option_name']);
        if ($item) {
            return Result::err(603, "已存在相同名称配置");
        }
        $this->optionService->create($data);
        return Result::ok(null, "创建成功");
    }

    /**
     * @param Option $option
     * @param Request $request
     * @return Result
     */
    #[Route(title: "更新配置", parent: "全局")]
    public function update(Option $option, Request $request): Result
    {
        $this->optionService->update($option, $request->json()->all());
        return Result::ok(null, "更新成功");
    }

}
