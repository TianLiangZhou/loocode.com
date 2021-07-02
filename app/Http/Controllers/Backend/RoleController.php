<?php


namespace App\Http\Controllers\Backend;


use App\Attributes\Route;
use App\Http\Result;
use App\Models\Permission;
use App\Models\Role;
use App\Services\RBACService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class RoleController
 * @package App\Http\Controllers\Backend
 */
#[Route(title: "设置", sort: 111, icon: "settings-2")]
class RoleController extends BackendController
{
    /**
     * @var RBACService
     */
    private RBACService $service;

    /**
     * RoleController constructor.
     * @param RBACService $service
     */
    public function __construct(RBACService $service)
    {
        $this->service = $service;
        parent::__construct();
    }

    /**
     * @return Result
     */
    #[Route(title: "角色", sort: 2, link: "/app/system/roles")]
    public function anchor(): Result
    {
        return Result::ok();
    }

    #[Route(title: "角色列表", parent: "角色", sort: 1)]
    public function roles(Request $request): Result
    {
        return Result::ok($this->service->getRolePaginator($request));
    }

    #[Route(title: "添加角色", parent: "角色", sort: 2)]
    public function store(Request $request): Result
    {
        $body = $request->json()->all();
        if (empty($body['name'])) {
            return Result::err(500, "名称不能为空");
        }
        if ($this->service->getRole()->name($body['name']) != null) {
            return Result::err(500, "存在相同名称角色");
        }
        return Result::ok([
            'id' => $this->service->createRole($body)->id ?? 0,
        ]);
    }

    #[Route(title: "更新角色", parent: "角色", sort: 3)]
    public function update(Role $role, Request $request): Result
    {
        $body = $request->json()->all();
        if (empty($body['name'])) {
            return Result::err(500, "名称不能为空");
        }
        $nameRole = $this->service->getRole()->name($body['name']);
        if ($nameRole && $nameRole->id != $role->id) {
            return Result::err(500, "存在相同名称角色");
        }
        if (empty($body['permission']) || !is_array($body['permission'])) {
            return Result::ok();
        }
        $this->service->updateRole($role, $body);
        return Result::ok(null, "更新成功");
    }

    /**
     * @param Role $role
     * @return Result
     * @throws \Exception
     */
    #[Route(title: "删除角色", parent: "角色", sort: 4)]
    public function delete(Role $role): Result
    {
        $role->permission()->delete();
        $role->delete();
        return Result::ok(null, "删除成功");
    }
}
