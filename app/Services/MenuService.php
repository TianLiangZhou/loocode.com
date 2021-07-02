<?php


namespace App\Services;

use App\Models\Menu;

/**
 * Class MenuService
 * @package App\Services
 * @property Menu $model
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

    /**
     * @param string $title
     * @return ?Menu
     */
    public function name(string $title): ?Menu
    {
        try {
            /**
             * @var $model Menu
             */
            $model = $this->where("name", $title)->first();
        } catch (\Exception $exception) {
            return null;
        }
        return $model;
    }
}
