<?php


namespace App\Http\Controllers\Backend;


use App\Attributes\Route;
use App\Http\Result;
use App\Services\ExtensionService;
use Illuminate\Http\Request;

#[Route(title: "扩展", sort: 105, icon: "cube")]
class ExtensionController extends BackendController
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

    #[Route(title: "元信息", sort: 2, link: "/app/extension/meta")]
    public function meta(): Result
    {
        return Result::ok();
    }

    /**
     * @param string $taxonomy
     * @return Result
     */
    public function taxonomy(string $taxonomy): Result
    {
        return Result::ok($this->extensionService->findMetaTaxonomy($taxonomy));
    }

    /**
     * @param Request $request
     * @return Result
     */
    #[Route(title: "更新元信息", parent: "元信息")]
    public function saveMeta(Request $request): Result
    {
        if (($metas = $request->json()->all())) {
            $this->extensionService->saveTaxonomyMeta($metas);
        }
        return Result::ok(null, '元信息保存成功');
    }
}
