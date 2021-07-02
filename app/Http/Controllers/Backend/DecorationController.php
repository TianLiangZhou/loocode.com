<?php


namespace App\Http\Controllers\Backend;


use App\Attributes\Route;
use App\Http\Result;
use App\Services\DecorationService;
use App\Services\PostService;
use Corcel\Model\Menu;
use Corcel\Model\Option;
use Corcel\Model\Post;
use Corcel\Model\Taxonomy;
use Corcel\Model\Term;
use Corcel\Model\TermRelationship;
use Illuminate\Http\Request;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

#[Route(title: "外观", sort: 2, icon: "layout")]
class DecorationController extends BackendController
{
    private DecorationService $decorationService;

    /**
     * DecorationController constructor.
     * @param DecorationService $decorationService
     */
    public function __construct(DecorationService $decorationService)
    {
        parent::__construct();
        $this->decorationService = $decorationService;
    }

    /**
     * @return Result
     */
    #[Route(title: "导航", sort: 2, link: "/app/decoration/navigation")]
    public function navigation(): Result
    {
        return Result::ok();
    }

    /**
     * @return Result
     */
    #[Route(title: "小挂件", sort: 3, link: "/app/decoration/widget")]
    public function widget(): Result
    {
        return Result::ok();
    }

    /**
     * @return Result
     */
    #[Route(title: "主题", sort: 1, link: "/app/decoration/theme")]
    public function theme(): Result
    {
        return Result::ok();
    }


    /**
     * @return Result
     */
    #[Route(title: "主题列表", parent: "主题", sort: 1)]
    public function themes(): Result
    {
        $themes = $this->decorationService->themes(resource_path('views/themes'));
        return Result::ok($themes);
    }


    /**
     * @return Result
     */
    public function navigateStructData(): Result
    {
        $data = $this->decorationService->navigateStructData();
        return Result::ok($data);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Result
     */
    #[Route(title: "导航结构数据", parent: "导航", sort: 2)]
    public function navigate(Request $request, int $id): Result
    {
        $menu = Menu::find($id);
        if ($menu == null) {
            return Result::err(404, "导航不存在");
        }
        $menus = $menu->items()->get();
        $items = [];

        $children = [];
        foreach ($menus as $key => $item) {
            $parentId = (int) $item->meta->_menu_item_menu_item_parent;
            $items[$key] = [
                'id' => $item->ID,
                'name' => $item->post_name,
                'type' => $item->meta->_menu_item_object,
                'parent' => $parentId,
                'objectId' => (int) $item->meta->_menu_item_object_id,
                'url' => $item->meta->_menu_item_url,
                'children' => [],
            ];
            if ($parentId > 0) {
                $children[$parentId][] = $key;
            }
        }
        foreach ($items as &$item) {
            if (isset($children[$item['id']])) {
                foreach ($children[$item['id']] as $k) {
                    $item['children'][] = $items[$k];
                    unset($items[$k]);
                }
            }
            unset($item['parent']);
        }
        $data = new \stdClass();
        $data->id = $id;
        $data->name = $menu->term->name;
        $data->nodes = array_values($items);
        return Result::ok($data);
    }

    /**
     * @param Request $request
     * @return Result
     */
    #[Route(title: "保存导航", parent: "导航", sort: 2)]
    public function saveNavigate(Request $request): Result
    {
        $body = $request->json()->all();
        if (isset($body['id']) && $body['id']) {
            $menu = Menu::find((int)$body['id']);
            if ($menu == null) {
                return Result::err(404, "导航不存在");
            }
            $menus = $menu->items()->get();
            foreach ($menus as $item) {
                $item->meta()->delete();
                $item->delete();
            }
            TermRelationship::where('term_taxonomy_id', (int) $body['id'])->delete();
            $this->createMenuItem($body['nodes'], $body['id']);
        } else {
            $term = Term::firstOrCreate(['name' => $body['name']], ['slug' => $body['name']]);
            if ($term->taxonomy == null || $term->taxonomy->taxonomy != 'nav_menu') {
                $taxonomy = new Taxonomy();
                $taxonomy->taxonomy = 'nav_menu';
                $taxonomy->description = "";
                $taxonomy->parent = 0;
                $taxonomyId = $term->taxonomy()->save($taxonomy)->term_taxonomy_id;
            } else {
                $taxonomyId = $term->taxonomy->term_taxonomy_id;
            }
            $this->createMenuItem($body['nodes'], $taxonomyId);
        }
        return Result::ok();
    }

    /**
     * @param array $nodes
     * @param int $taxonomyId
     * @param int $parent
     */
    private function createMenuItem(array $nodes, int $taxonomyId, int $parent = 0)
    {
        foreach ($nodes as $key => $node) {
            $data = [
                'post_author' => auth('backend')->id(),
                'post_date' => now(), 'to_ping' => '', 'pinged' => '', 'post_content_filtered' => '',
                'post_title'=> $node['name'],
                'post_type' => 'nav_menu_item',
                'post_status' => 'publish',
                'menu_order' => $key + 1,
                'post_content' => '',
                'post_excerpt' => '',
            ];
            $post = new Post($data);
            $post->post_name = $node['name'];
            if ($post->save()) {
                $post->createMeta([
                    '_menu_item_type' => $node['type'] == 'category'
                        ? 'taxonomy'
                        : ($node['type'] == 'post' || $node['type'] == 'page' ? 'post_type' : $node['type']),
                    '_menu_item_menu_item_parent' => $parent,
                    '_menu_item_object_id' => (int) ($node['objectId'] ?? 0),
                    '_menu_item_object' => $node['type'],
                    '_menu_item_url' => $node['url'] ?? '',
                ]);
                $relation = new TermRelationship();
                $relation->object_id = $post->ID;
                $relation->term_taxonomy_id = $taxonomyId;
                $relation->term_order = 0;
                $relation->save();
            }
            if (!empty($node['children'])) {
                $this->createMenuItem($node['children'], $taxonomyId, $post->ID);
            }
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Result
     */
    #[Route(title: "删除导航", parent: "导航", sort: 2)]
    public function deleteNavigate(Request $request, int $id): Result
    {
        $menu = Menu::find($id);
        if ($menu == null) {
            return Result::err(404, "导航不存在");
        }
        $menus = $menu->items()->get();
        foreach ($menus as $item) {
            $item->meta()->delete();
            $item->taxonomies()->delete();
            $item->delete();
        }
        return Result::ok();
    }
}
