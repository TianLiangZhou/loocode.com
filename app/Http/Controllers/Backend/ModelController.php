<?php


namespace App\Http\Controllers\Backend;


use App\Attributes\Route;
use App\Http\Result;
use App\Models\Menu;
use App\Services\ExtensionService;
use Corcel\Model\Post;
use Illuminate\Http\Request;
use stdClass;

#[Route(title: "扩展", sort: 105, icon: "cube")]
class ModelController extends BackendController
{

    /**
     * @var ExtensionService
     */
    private ExtensionService $extensionService;

    /**
     * ExtensionController constructor.
     * @param ExtensionService $extensionService
     */
    public function __construct(ExtensionService $extensionService)
    {
        $this->extensionService = $extensionService;
        parent::__construct();
    }

    #[Route(title: "模型", sort: 2, link: "/app/extension/model")]
    public function model(): Result
    {
        return Result::ok();
    }

    #[Route(title: "新建模型", sort: 2, link: "/app/extension/new-model")]
    public function createModel(): Result
    {
        return Result::ok();
    }

    /**
     * @return Result
     */
    public function topMenu(): Result
    {
        return Result::ok(
            Menu::select('id', 'name')->where('parent_id', 0)->get()
        );
    }

    /**
     * @param Request $request
     * @return Result
     */
    #[Route(title: "模型列表", parent: "模型", sort: 2)]
    public function main(Request $request): Result
    {
        $posts = Post::select('id', 'post_title', 'post_name', 'post_status', 'post_modified')
            ->type("model")
            ->orderBy('id', 'DESC');
        if ($request->query->has('id_like')) {
            $posts->where('ID',$request->query->get('id_like'));
        }
        if ($request->query->has('post_title_like')) {
            $posts->where('post_title', 'like', '%' . $request->query->get('post_title_like') . '%');
        }
        $posts = $posts->without("meta")->paginate(
            $request->query->getInt("data_per_page", 30),
            ['*'],
            'data_current_page',
        );
        return Result::ok($posts);
    }

    /**
     * @param Request $request
     * @return Result
     */
    #[Route(title: "创建模型", parent: "模型", sort: 2)]
    public function store(Request $request): Result
    {
        $body = $request->json()->all();
        if (empty($body['name'])) {
            return Result::err(500, "模型名称不能为空");
        }
        $model = $this->extensionService->saveModel($body);
        return Result::ok(["id" => $model->ID], "模型保存成功");
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Result
     */
    #[Route(title: "更新模型", parent: "模型", sort: 2)]
    public function update(int $id, Request $request): Result
    {
        $body = $request->json()->all();
        if (empty($body['name'])) {
            return Result::err(500, "模型名称不能为空");
        }
        $this->extensionService->saveModel($body);
        return Result::ok(null, '模型更新成功');
    }

    /**
     * @param int $id
     * @return Result
     * @throws \Exception
     */
    #[Route(title: "删除模型", parent: "模型", sort: 2)]
    public function delete(int $id): Result
    {
        $this->extensionService->deleteModel($id);
        return Result::ok(null, "模型删除成功");
    }

    /**
     * @param int $id
     * @return Result
     */
    public function show(int $id): Result
    {
        $model = $this->extensionService->getById($id);
        if ($model == null) {
            return Result::err();
        }
        $data = new stdClass();
        $data->id = $id;
        $data->identity = $model->post_name;
        $data->name = $model->post_title;
        $data->option = json_decode($model->post_content);
        return Result::ok($data);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Result
     */
    #[Route(title: "保存模型数据", parent: "模型", sort: 2)]
    public function saveDynamicModelData(int $id, Request $request): Result
    {
        $all = $request->json()->all();
        $newId = $this->extensionService->saveDynamicModelData($id, $all);
        return Result::ok([
            'id' => $newId
        ], "模型数据保存成功");
    }

    /**
     * @param int $modelId
     * @param int $id
     * @return Result
     */
    #[Route(title: "删除模型数据", parent: "模型", sort: 2)]
    public function deleteDynamicModelData(int $modelId, int $id): Result
    {
        $this->extensionService->deleteDynamicModelData($modelId, $id);
        return Result::ok(null, "删除成功");
    }



    /**
     * @param int $id
     * @param Request $request
     * @return Result
     */
    public function dynamicModelData(int $id, Request $request): Result
    {
        $dynamicModelData = $this->extensionService->dynamicModelData($id, $request);
        return Result::ok($dynamicModelData);
    }
}
