<?php


namespace App\Services;


use App\Exceptions\NotFoundResourceException;
use Corcel\Model\Menu;
use Corcel\Model\Option;
use Corcel\Model\Post;
use Corcel\Model\TermRelationship;
use stdClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class DecorationService extends BaseService
{
    /**
     * @var PostService
     */
    private PostService $postService;
    /**
     * @var TaxonomyService
     */
    private TaxonomyService $taxonomyService;

    /**
     * DecorationService constructor.
     * @param PostService $postService
     * @param TaxonomyService $taxonomyService
     */
    public function __construct(PostService $postService, TaxonomyService $taxonomyService)
    {
        $this->postService = $postService;
        $this->taxonomyService = $taxonomyService;
    }

    /**
     * @param string $path
     * @return array
     */
    public function themes(string $path): array
    {
        $iterator = Finder::create()->files()->name('theme.json')->in($path);
        $themes = [];
        $theme = Option::get('theme') ?? 'default';
        foreach ($iterator as $file) {
            /**
             * @var $file SplFileInfo
             */
            $manifest = json_decode(file_get_contents($file->getPathname()));
            if (isset($manifest->image)
                && $manifest->image
                && (strpos($manifest->image, '/') === 0 || strpos($manifest->image, '.') === 0)
            ) {
                $manifest->image = 'data:image/png;base64, ' . base64_encode(file_get_contents(realpath($manifest->image)));
            }
            $manifest->token = $file->getRelativePath();
            $manifest->enable = $theme === $manifest->token;
            $themes[] = $manifest;
        }
        return $themes;
    }

    /**
     * @return stdClass
     */
    public function navigateStructData(): stdClass
    {
        // $tag = Taxonomy::name("post_tag")->orderBy("term_taxonomy_id", "DESC")->limit(20)->get();
        $data = new stdClass();
        $data->page = $this->postService->getTypeLimit("page");
        $data->post = $this->postService->getTypeLimit("post");
        $data->categories = $this->taxonomyService->taxonomy("category");
        // $data->tag = $tag;
        $data->menu = $this->taxonomyService->taxonomy("nav_menu");
        return $data;
    }

    /**
     * @param Menu $menu
     * @return stdClass
     */
    public function navigate(Menu $menu): stdClass
    {
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
        $data = new stdClass();
        $data->id = $menu->term_taxonomy_id;
        $data->name = $menu->term->name;
        $data->nodes = array_values($items);
        return $data;
    }

    public function saveNavigate(array $body)
    {
        if (isset($body['id']) && $body['id']) {
            $menu = Menu::find((int) $body['id']);
            if ($menu == null) {
                throw new NotFoundResourceException("导航不存在");
            }
            $menus = $menu->items()->get();
            foreach ($menus as $item) {
                $item->meta()->delete();
                $item->delete();
            }
            TermRelationship::where('term_taxonomy_id', (int) $body['id'])->delete();
        }
        $menu = $this->taxonomyService->createTaxonomy('nav_menu', ['name' => $body['name'], 'slug' => $body['name']]);
        if ($menu == null) {
            throw new NotFoundResourceException("导航保存失败");
        }
        $this->createMenuItem($body['nodes'], $menu->term_taxonomy_id);
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

}
