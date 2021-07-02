<?php


namespace App\Services;


use Corcel\Model\Post;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use RuntimeException;
use stdClass;

class ExtensionService extends BaseService
{
    /**
     * @var Post
     */
    protected $model;

    /**
     * @var MenuService
     */
    private MenuService $menuService;
    /**
     * @var TaxonomyService
     */
    private TaxonomyService $taxonomyService;
    /**
     * @var PostService
     */
    private PostService $postService;

    /**
     * @var OptionService
     */
    private OptionService $optionService;

    /**
     * ExtensionService constructor.
     * @param PostService $postService
     * @param MenuService $menuService
     * @param TaxonomyService $taxonomyService
     * @param OptionService $optionService
     */
    public function __construct(PostService $postService, MenuService $menuService, TaxonomyService $taxonomyService, OptionService $optionService)
    {
        $this->postService = $postService;
        $this->menuService = $menuService;
        $this->taxonomyService = $taxonomyService;
        $this->optionService = $optionService;
        $this->model = $postService->getModel();
    }


    /**
     * @param string $taxonomy
     * @return stdClass|null
     */
    public function findMetaTaxonomy(string $taxonomy): ?stdClass
    {
        $model = $this->postService->getOneNameByType($taxonomy, "meta");
        if ($model) {
            return json_decode($model->post_content);
        }
        return null;
    }

    /**
     * @param array $metas
     * @return bool
     */
    public function saveTaxonomyMeta(array $metas): bool
    {
        foreach ($metas as $meta) {
            $this->postService->where("post_type", "meta")
                ->where("post_name", $meta['taxonomy']['value'])
                ->delete();
            $this->postService->save(
                $meta['taxonomy']['value'],
                'meta',
                [
                    'post_excerpt' => '',
                    'post_title' => $meta['taxonomy']['value'],
                    'post_content' => json_decode($meta)
                ]
            );
        }
        return true;
    }

    /**
     * @param array $body
     * @return Model
     */
    public function saveModel(array $body)
    {
        $option = $body['option'];
        $data = [
            'post_title'=> $body['name'],
            'post_excerpt' => '',
            'post_type' => 'model',
            'post_content' => json_encode($option),
            'to_ping' => '',
            'pinged' => '',
            'post_content_filtered' => '',
        ];
        $model = $this->postService->getModel();
        if (isset($body['id']) && $body['id'] > 0) {
            $model = $this->postService->id($body['id']);
        }
        if ($model == null) {
            throw new RuntimeException("模型操作失败");
        }
        if (!empty($body['identity'])) {
            $existModel = $this->postService->getOneName($body['identity']);
            if ($existModel && $existModel->ID != $model->ID) {
                throw new RuntimeException("标识已存在");
            }
        }
        $model->post_name = $body['identity'];
        $model->fill($data);
        $model->save();
        $menuName = $body['name'];
        if (!empty($option['menu_name'])) {
            $menuName = $option['menu_name'];
        }
        $menu = [
            "parent_id" => 0,
            "name" => $menuName,
            "hidden" => 0,
            "weight" => 1,
            "class" => $option['menu_level'] == 1 ? "cube" : "",
            "url"   => "",
            "link"  => "/app/extension/dynamic-model",
            "object_id" => isset($body['id']) && $body['id'] > 0 ? $body['id'] : 0,
        ];
        if (isset($option['parent_menu_id']) && $option['parent_menu_id'] > 0) {
            $menu['parent_id'] = (int) $option['parent_menu_id'];
        }
        $menu['link'] .= "/" . $model->ID;
        $menu["object_id"] = $model->ID;
        $parentMenu = [];
        if ($option['menu_level'] == 2) {
            $parentMenu = $menu;
            $parentMenu['parent_id'] = 0;
            $parentMenu['link'] = "";
            $parentMenu['class'] = "cube";
            $parentMenu['name'] = $option['parent_menu_name'] ?? $body['name'];
        }
        $collection = $this->menuService->where("object_id", $model->ID)->orderBy("id")->get();
        if ($collection->count() < 1) {
            if ($parentMenu) {
                $parent = $this->menuService->create($parentMenu);
                $menu['parent_id'] = $parent->id;
            }
            $this->menuService->create($menu);
        } else {
            if ($collection->count() > 1 && empty($parentMenu)) {
                $collection->first()->delete();
            }
            if ($collection->count() == 1 && $parentMenu) {
                $parent = $this->menuService->create($parentMenu);
                $menu['parent_id'] = $parent->id;
            }
            $this->menuService->updateById($collection->last()->id, $menu);
        }
        return $model;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteModel(int $id): bool
    {
        $model = $this->postService->id($id);
        if ($model == null) {
            throw new RuntimeException("模型操作失败");
        }
        $option = json_decode($model->post_content);
        switch ($option->template) {
            case 1:
                $this->postService->deleteByType($model->post_name);
                break;
            case 2:
                $this->taxonomyService->deleteTaxonomy($option->taxonomy);
                break;
            case 3:
                /**
                 * @var $forms array
                 */
                $forms = $option->meta->forms;
                $columns = [];
                foreach ($forms as $form) {
                    $columns[] = $form->keyword;
                }
                $this->optionService->deleteOptions($columns);
                break;
        }
        $this->menuService->deleteObjectId($id);
        $model->delete();
        return true;
    }

    /**
     * @param int $id
     * @param Request $request
     * @return mixed
     */
    public function dynamicModelData(int $id, Request $request)
    {
        $model = $this->postService->id($id);
        if ($model == null) {
            throw new RuntimeException("模型不存在");
        }
        $option = json_decode($model->post_content);
        if ($option->template == 2) {
            return $this->taxonomyService->getPaginatorTaxonomy($request, $option->taxonomy);
        }
        /**
         * @var $forms array
         */
        $forms = $option->meta->forms;
        $columns = [];
        foreach ($forms as $form) {
            $columns[] = $form->keyword;
        }
        if ($option->template == 3) {
            return $this->optionService->options($columns);
        }
        $paginator = $this->postService->getPaginator($request, $model->post_name);
        if ($paginator->count() < 1) {
            return $paginator;
        }
        $paginator = $paginator->toArray();
        $items = [];
        foreach ($paginator['data'] as $item) {
            $data = [
                'id' => $item['ID'],
            ];
            foreach ($columns as $column) {
                $data[$column] = $item[$column] ?? null;
            }
            $content = json_decode($item['post_content'], true);
            foreach ($columns as $column) {
                $data[$column] = $content[$column] ?? null;
            }
            $items[] = $data;
        }
        $paginator['data'] = $items;
        return $paginator;
    }


    /**
     * @param int $id
     * @param array $all
     * @return int
     */
    public function saveDynamicModelData(int $id, array $all)
    {
        $model = $this->postService->id($id);
        if ($model == null) {
            throw new RuntimeException("模型不存在");
        }
        $option = json_decode($model->post_content);
        $objectId = isset($all['id']) && $all['id'] > 0 ? (int) $all['id'] : 0;
        unset($all['id']);
        switch ($option->template) {
            case 2:
                if ($objectId > 0) {
                    return $this->taxonomyService->updateTaxonomy($objectId, $all)->term_taxonomy_id ?? 0;
                }
                return $this->taxonomyService->createTaxonomy($option->taxonomy, $all)->term_taxonomy_id ?? 0;
            case 3:
                $this->optionService->saveOptions($all);
                return 0;
            default:
                $all['post_type'] = $model->post_name;
                return $this->postService->dynamicModelSave($all, $objectId)->ID ?? 0;
        }
    }

    /**
     * @param int $modelId
     * @param int $id
     * @return bool
     */
    public function deleteDynamicModelData(int $modelId, int $id): bool
    {
        $model = $this->postService->id($modelId);
        if ($model == null) {
            throw new RuntimeException("模型不存在");
        }
        $option = json_decode($model->post_content);
        switch ($option->template) {
            case 1:
                $data = $this->postService->id($id);
                if ($data == null) {
                    throw new RuntimeException("模型数据不存在");
                }
                $data->meta()->delete();
                $data->delete();
                break;
            case 2:
                try {
                    $data = $this->taxonomyService->getById($id);
                } catch (Exception $e) {
                }
                $data->delete();
                break;
        }
        return true;
    }

    /**
     * @return PostService
     */
    public function getPostService(): PostService
    {
        return $this->postService;
    }
}
