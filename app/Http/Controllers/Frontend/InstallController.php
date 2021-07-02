<?php


namespace App\Http\Controllers\Frontend;

use App\Http\Requests\InstallingRequest;
use App\Services\InstallService;
use Corcel\Model\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class InstallController
 * @package App\Http\Controllers\Frontend
 */
class InstallController
{

    /**
     * @var InstallService
     */
    private InstallService $installService;

    /**
     * InstallController constructor.
     * @param InstallService $installService
     */
    public function __construct(InstallService $installService)
    {
        $this->installService = $installService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function step(Request $request)
    {
        $installedPath = base_path('bootstrap/cache/__installed');
        $installed = file_exists($installedPath);
        // 调用pdo 来检测数据库是否配置正确
        DB::getPdo();
        $step = $request->get('step');
        if ($step !== 'last' && !$installed) {
            return view("default/install/step");
        }
        $admin = config('app.dashboard_domain');
        if (empty($admin)) {
            $admin = Option::get('site_url');
            $admin .= "/dashboard/index.html";
        }
        if (stripos($admin, 'http') !== 0) {
            $admin = 'http://' . $admin;
        }
        return view("default/install/last", [
            'dashboard' => $admin,
        ]);
    }

    /**
     * @param InstallingRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \ReflectionException
     */
    public function activate(InstallingRequest $request)
    {
        $installed = base_path('bootstrap/cache/__installed');
        if (file_exists($installed)) {
            return redirect("/");
        }
        $validated = $request->validated();
        $validated['site_url'] = $request->getBaseUrl();
        $this->installService->activate($validated);
        return redirect("/install?step=last");
    }
}
