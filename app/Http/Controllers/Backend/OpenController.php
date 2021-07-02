<?php
declare(strict_types=1);


namespace App\Http\Controllers\Backend;


use App\Http\Result;
use App\Services\OpenService;
use App\Services\OptionService;
use Illuminate\Http\Request;

/**
 * Class OpenController
 * @package App\Http\Controllers\Backend
 */
class OpenController extends BackendController
{
    /**
     * @var OpenService
     */
    private OpenService $openService;

    /**
     * @var OptionService
     */
    private OptionService $optionService;

    /**
     * OpenController constructor.
     * @param OpenService $openService
     * @param OptionService $optionService
     */
    public function __construct(OpenService $openService, OptionService $optionService)
    {
        $this->openService = $openService;
        $this->optionService = $optionService;
        parent::__construct();
    }

    /**
     * @return Result
     */
    public function configuration(): Result
    {
        $name = $this->optionService->oneByName("site_title");
        return Result::ok([
            // markdown | ckeditor | text
            'editor' => config('app.editor'),
            'timestamp' => time(),
            'taxonomy' => config('app.taxonomy'),
            'post_type'=> config('app.post_type'),
            'name' => $name ?? config('app.name'),
        ]);
    }

    /**
     * 读取当前登录用户菜单
     *
     * @param Request $request
     * @return Result
     */
    public function userMenu(Request $request): Result
    {
        return $this->openService->getUserMenu($request->user('backend'));
    }

    /**
     * 读取全部菜单
     *
     * @return Result
     */
    public function menus(): Result
    {
        return Result::ok($this->openService->menus());
    }

    /**
     * 刷新菜单
     *
     * @return Result
     * @throws \ReflectionException
     */
    public function menuRefresh(): Result
    {
        $this->openService->refreshMenu(__DIR__);
        return Result::ok();
    }



}
