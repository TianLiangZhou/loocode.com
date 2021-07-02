<?php


namespace App\Services;


use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class RBACService
 * @package App\Services
 */
class RBACService extends BaseService
{
    private RoleService $role;
    private MenuService $menu;
    private PermissionService $permission;

    /**
     * RBACService constructor.
     * @param MenuService $menu
     * @param RoleService $role
     * @param PermissionService $permission
     */
    public function __construct(MenuService $menu, RoleService $role, PermissionService $permission)
    {
        $this->menu = $menu;
        $this->role = $role;
        $this->permission = $permission;
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getRolePaginator(Request $request): LengthAwarePaginator
    {
        $size = $request->query->getInt("data_per_page", 30);
        $roles = $this->role->getRoles($request, $size);
        foreach ($roles as $role) {
            $role['permission'] = $size > 30 ? [] : $this->permission->menuByRoleId($role->id);
        }
        return $roles;
    }

    /**
     * @param array $data
     * @return Role
     */
    public function createRole(array $data): Role
    {
        /**
         * @var $role Role
         */
        $role = $this->role->create(['name' => $data['name']]);
        if (!empty($data['permission']) && is_array($data['permission'])) {
            $permissions = [];
            foreach ($data['permission'] as $id) {
                $permissions[] = (clone $this->permission->getModel())->fill(['menu_id' => $id]);
            }
            $role->permission()->saveMany($permissions);
        }
        return $role;
    }


    /**
     * @param Role $role
     * @param array $body
     * @return bool
     */
    public function updateRole(Role $role, array $body): bool
    {
        $role->name = $body['name'];
        $role->permission()->delete();
        $permissions = [];
        foreach ($body['permission'] as $id) {
            $permissions[] = (clone $this->permission->getModel())->fill(['menu_id' => $id]);
        }
        $role->permission()->saveMany($permissions);
        return $role->save();
    }

    /**
     * @return PermissionService
     */
    public function getPermission(): PermissionService
    {
        return $this->permission;
    }

    /**
     * @return MenuService
     */
    public function getMenu(): MenuService
    {
        return $this->menu;
    }

    /**
     * @return RoleService
     */
    public function getRole(): RoleService
    {
        return $this->role;
    }
}
