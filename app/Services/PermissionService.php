<?php


namespace App\Services;


use App\Models\Permission;

/**
 * Class PermissionService
 * @package App\Services
 * @property Permission $model
 */
class PermissionService extends BaseService
{
    /**
     * PermissionService constructor.
     * @param Permission $permission
     */
    public function __construct(Permission $permission)
    {
        $this->model = $permission;
    }

    /**
     * @param int $roleId
     * @return mixed
     */
    public function menuByRoleId(int $roleId)
    {
        return $this->model->select('menu_id')
            ->where('role_id', $roleId)
            ->get()
            ->map(function ($item) {
                return $item->menu_id;
            });
    }
}
