<?php


namespace App\Services;

use App\Attributes\Route;
use App\Http\Result;
use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use Corcel\Model\Meta\UserMeta;
use Corcel\Model\Term;
use Corcel\Model\User;
use Illuminate\Database\Eloquent\Collection;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Finder;

/**
 * Class OpenService
 * @package App\Services
 */
class OpenService extends BaseService
{
    /**
     * @var MenuService
     */
    private MenuService $menuService;

    /**
     * OpenService constructor.
     * @param MenuService $menuService
     */
    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * @param User $user
     * @param string $dir
     * @throws ReflectionException
     */
    public function firstBuilder(User $user, string $dir)
    {
        /**
         * @var $role Role
         */
        $role = Role::firstOrCreate(['name' => '超级管理员']);
        $this->refreshMenu($dir);
        $menus = $this->menuService::all();
        $permission = [];
        foreach ($menus as $menu) {
            $permission[] = new Permission(['menu_id' => $menu->id]);
        }
        $role->permission()->saveMany($permission);
        $user->meta()->save(new UserMeta([
            'meta_key' => 'roles',
            'meta_value' => sprintf("[%d]", $role->id),
        ]));
    }

    /**
     * @param string $dir
     * @throws ReflectionException
     */
    public function refreshMenu(string $dir)
    {
        $routes = $this->readFileMenu($dir);
        $this->saveMenu($routes);
    }


    /**
     * @param ?User $user
     * @return Result
     */
    public function getUserMenu(?User $user): Result
    {
        if ($user == null) {
            return Result::ok();
        }
        $roles = json_decode($user->meta->roles ?? '[]', true);
        if (empty($roles)) {
            return Result::ok();
        }
        /**
         * @var $permissions \Illuminate\Support\Collection
         */
        $permissions = Permission::select('menu_id')->whereIn('role_id', $roles)->get()->map(function($item) {
            return $item->menu_id;
        });
        if ($permissions->count() < 1) {
            return Result::ok();
        }
        return Result::ok($this->menus($permissions->toArray()));
    }

    /**
     * @param array $permission
     * @return mixed
     */
    public function menus(array $permission = []): Collection
    {
        $menu = $this->menuService->where("parent_id", 0);
        if ($permission) {
            $menu->whereIn('id', $permission);
        }
        $menus = $menu->orderBy('weight')->get();
        $this->getChildMenu($menus, $permission);
        return $menus;
    }

    /**
     * 保存菜单
     *
     * @param array $menus
     * @param int $parentId
     * @param array $collection
     */
    private function saveMenu(array $menus, int $parentId = 0, array &$collection = [])
    {
        foreach ($menus as $item) {
            $menu = $this->menuService->name($item->title);
            if ($menu == null) {
                $menu = $this->menuService->getModel();
                $menu->name = $item->title;
            }
            $menu->parent_id = $parentId;
            $menu->hidden = (int) $item->hidden;
            $menu->weight = $item->sort;
            $menu->class = $item->icon;
            $menu->url = "";
            $menu->link = $item->link;
            if (!$menu->save()) {
                continue;
            }
            $collection[] = $menu->id;
            if (count($item->children) > 0) {
                $this->saveMenu($item->children, $menu->id, $collection);
            }
        }
        if ($parentId == 0 && $collection) {
            /**
             * @var $collection Collection
             */
            $collection = Menu::whereNotIn('id', $collection)->where("object_id", 0)->get();
            foreach ($collection as $item) {
                Permission::where("menu_id")->delete();
                $item->delete();
            }
        }
    }

    /**
     * @param string $dir
     * @return Route[]
     * @throws ReflectionException
     */
    private function readFileMenu(string $dir): array
    {
        $files = Finder::create()->in([$dir])->name(['*.php'])->files();
        /**
         * @var $routes Route[]
         */
        $routes = $routeSorts = [];
        $namespace = str_replace(
            [base_path(), DIRECTORY_SEPARATOR, 'app'],
            ['', '\\', 'App'],
            $dir
        );
        foreach ($files as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $controller = $namespace . '\\' . $file->getBasename(".php");
            $ref = new ReflectionClass($controller);
            $attributes = $ref->getAttributes(Route::class);
            if (count($attributes) < 1) {
                continue;
            }
            $arguments = $attributes[0]->getArguments();
            if (empty($arguments['title'])) {
                continue;
            }
            /**
             * @var $route Route
             */
            if (isset($routes[$arguments['title']])) {
                $route = $routes[$arguments['title']];
                if (isset($arguments['sort']) && $route->sort != $arguments['sort']) {
                    $route->sort = $arguments['sort'];
                }
            } else {
                $route = $attributes[0]->newInstance();
            }
            $methods = $ref->getMethods();
            foreach ($methods as $method) {
                $attr = $method->getAttributes(Route::class);
                if (count($attr) < 1) {
                    continue;
                }
                $args = $attr[0]->getArguments();
                if (empty($args['title'])) {
                    continue;
                }
                /**
                 * @var $childRoute Route
                 */
                $childRoute = $attr[0]->newInstance();
                $route->appendChild($childRoute);
            }
            if (!isset($routes[$arguments['title']])) {
                $routes[$arguments['title']] = $route;
            }
        }
        $routes = array_values($routes);
        foreach ($routes as $route) {
            $this->traverseChildren($route);
            $routeSorts[] = $route->sort;
        }
        array_multisort($routeSorts, SORT_ASC, $routes);
        return $routes;
    }

    /**
     * @param Route $route
     */
    private function traverseChildren(Route $route)
    {
        $children = &$route->getChildren();
        $parents = [];
        foreach ($children as $name => $childRoute) {
            $parent = $childRoute->getParent();
            if ($parent) {
                $parents[$parent][] = $childRoute;
            }
            $routeSorts[] = $childRoute->sort;
        }
        foreach ($children as $name => $item) {
            if (isset($parents[$item->title])) {
                foreach ($parents[$item->title] as $ele) {
                    $item->appendChild($ele);
                    unset($children[$ele->title]);
                }
            }
        }
    }

    /**
     * @param Collection $menus
     * @param array $permission
     */
    private function getChildMenu(Collection $menus, array $permission = [])
    {
        foreach ($menus as $menu) {
            $query = $menu->children()
                ->select(['id', 'parent_id', 'name', 'hidden', 'class', 'link', 'url']);
            if ($permission) {
                $query->whereIn('id', $permission);
            }
            $menu['children'] = $query->orderBy('weight')->get();
            if (count($menu['children']) > 0) {
                $this->getChildMenu($menu['children'], $permission);
            }
        }
    }
}
