<?php
declare(strict_types=1);

namespace App\Http\Controllers\Backend;


use App\Attributes\Route;
use App\Http\Result;
use App\Models\Role;
use App\Services\RoleService;
use App\Services\UserService;
use Corcel\Model\User;
use Corcel\Services\PasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class UserController
 * @package App\Http\Controllers\Backend
 */
#[Route(title: "用户", sort: 100, icon: "person")]
class UserController extends BackendController
{
    /**
     * @var UserService
     */
    private UserService $userService;
    private RoleService $roleService;

    /**
     * UserController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        parent::__construct();
    }

    /**
     * @return Result
     */
    #[Route(title: "所有用户", sort: 0, link: "/app/user/members")]
    public function anchor(): Result
    {
        return Result::ok();
    }

    /**
     * @param Request $request
     * @return Result
     */
    #[Route(title: "会员列表", parent: "所有用户")]
    public function members(Request $request): Result
    {
        $users = $this->userService->getPaginator($request);
        foreach ($users as $user) {
            $user['roles'] = json_decode($user->meta->roles ?? '[]', true);
            /**
             * @var $roles Collection
             */
            $roles = $user['roles']
                ? $this->roleService->idArray($user['roles'])->map(function($item) {
                    return $item->name;
                })
                : collect([]);
            $user['role_name'] = $roles ? $roles->implode(',') : "";
            $user['lasted_date'] = $user->meta->lasted_date;
        }
        return Result::ok($users);
    }

    #[Route(title: "添加会员", parent: "所有用户")]
    public function store(Request $request): Result
    {
        $body = $request->json()->all();
        $user = $this->userService->email($body['email']);
        if ($user) {
            return Result::err(500, "邮箱地址已存在");
        }
        $user = $this->userService->create($body, is_array($body['roles']) && $this instanceof ManagerController);
        if ($user == null) {
            return Result::err(500, "添加失败");
        }
        return Result::ok(null, "添加成功");
    }

    #[Route(title: "更新会员", parent: "所有用户")]
    public function update(User $user, Request $request): Result
    {
        $body = $request->json()->all();
        $email = $this->userService->email($body['email']);
        if ($email && $email->ID != $user->ID) {
            return Result::err(500, "邮箱地址已存在");
        }
        if (!$this->userService->update($user, $body, is_array($body['roles']) && $this instanceof ManagerController)) {
            return Result::err(500, "添加失败");
        }
        return Result::ok(null, "更新成功");
    }

    #[Route(title: "删除会员", parent: "所有用户")]
    public function delete(User $user): Result
    {
        $user->meta()->delete();
        $user->delete();
        return Result::ok(null, '删除成功');
    }
}
