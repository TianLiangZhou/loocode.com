<?php


namespace App\Http\Controllers\Backend;


use App\Attributes\Route;
use App\Http\Result;
use App\Services\DecorationService;
use Corcel\Model\Menu;
use Illuminate\Http\Request;

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
     * @param Menu $menu
     * @return Result
     */
    #[Route(title: "导航结构数据", parent: "导航", sort: 2)]
    public function navigate(Menu $menu): Result
    {
        $data = $this->decorationService->navigate($menu);
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
        $this->decorationService->saveNavigate($body);
        return Result::ok();
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
