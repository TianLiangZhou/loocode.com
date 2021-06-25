<?php


namespace App\Services;

use App\Models\Menu;

/**
 * Class MenuService
 * @package App\Services
 */
class MenuService extends BaseService
{
    /**
     * MenuService constructor.
     * @param Menu $menu
     */
    public function __construct(Menu $menu)
    {
        $this->model = $menu;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function deleteObjectId(int $id)
    {
        return $this->where("object_id", $id)->delete();
    }
}
