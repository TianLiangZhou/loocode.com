<?php
declare(strict_types=1);


namespace App\Http\Controllers\Backend;


use App\Http\Result;
use App\Services\OpenService;
use Illuminate\Http\Request;

/**
 * Class OpenController
 * @package App\Http\Controllers\Backend
 */
class OpenController extends BackendController
{
    public function __construct(public OpenService $openService)
    {
        parent::__construct();
    }

    /**
     * @return Result
     */
    public function configuration(): Result
    {
        return Result::ok([
            // markdown | ckeditor | text
            'editor' => config('app.editor'),
            'timestamp' => time(),
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
