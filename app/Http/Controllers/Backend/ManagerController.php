<?php


namespace App\Http\Controllers\Backend;


use App\Attributes\Route;
use App\Http\Result;
use Corcel\Model\User;
use Illuminate\Http\Request;

/**
 * Class ManagerController
 * @package App\Http\Controllers\Backend
 */
#[Route(title: "设置", sort: 111, icon: "settings-2")]
class ManagerController extends UserController
{

    /**
     * @return Result
     */
    #[Route(title: "管理员", sort: 1, link: "/app/system/managers")]
    public function anchor(): Result
    {
        return Result::ok();
    }

    /**
     * @param Request $request
     * @return Result
     */
    #[Route(title: "管理员列表", parent: "管理员")]
    public function members(Request $request): Result
    {
        return parent::members($request);
    }

    #[Route(title: "添加管理员", parent: "管理员")]
    public function store(Request $request): Result
    {
        return parent::store($request);
    }

    #[Route(title: "更新管理员", parent: "管理员")]
    public function update(User $user, Request $request): Result
    {
        return parent::update($user, $request);
    }

    /**
     * @param User $user
     * @return Result
     * @throws \Exception
     */
    #[Route(title: "删除管理员", parent: "管理员")]
    public function delete(User $user): Result
    {
        return parent::delete($user);
    }
}
