<?php


namespace App\Http\Controllers\Frontend;

use App\Exceptions\InstallException;
use App\Http\Requests\InstallingRequest;
use App\Services\OpenService;
use Corcel\Model\Option;
use Corcel\Model\User;
use Corcel\Services\PasswordService;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class InstallController
 * @package App\Http\Controllers\Frontend
 */
class InstallController
{
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
        if (!$installed) {
            $this->stepLast($installedPath);
        }
        $admin = config('app.dashboard_domain');
        if (empty($admin)) {
            $admin = Option::get('site_url');
            $admin .= "/dashboard/index.html";
        }
        if (stripos($admin, 'http') !== 0) {
            $admin = '//' . $admin;
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
        $this->stepDatabase();
        $this->stepUser($validated, $request);
        $this->stepOption($validated, $request);
        return redirect("/install?step=last");
    }

    /**
     *
     */
    private function stepDatabase()
    {
        $table = config('database.migrations');;
        $db = app('db');
        $repository = new DatabaseMigrationRepository($db, $table);
        if (!$repository->repositoryExists()) {
            $repository->createRepository();
        }
        $migrator = new Migrator($repository, $db, app('files'), app('events'));
        $migrator->run(database_path('migrations'));
    }


    /**
     * @throws \ReflectionException
     */
    private function stepUser(array $body, Request $request): void
    {
        $nickname = $body['nickname'];
        if (empty($nickname)) {
            $nickname = explode('@', $body['email'])[0];
        }
        $user = new User();
        $user->user_login = $nickname;
        $user->user_pass = (new PasswordService())->makeHash($body['password']);
        $user->user_nicename = $nickname;
        $user->user_email = $body['email'];
        $user->user_url = $request->getBaseUrl();
        $user->user_activation_key = Str::random(8);
        $user->user_status = 0;
        $user->display_name = $nickname;
        try {
            $isSuccess = $user->save();
        } catch(\Exception $exception) {
            throw new InstallException("Installation failed: " . $exception->getMessage(), 0, $exception);
        }
        if (!$isSuccess) {
            throw new InstallException("Installation failed, failed to initialize data ");
        }
        (new OpenService())->firstBuilder($user, app_path('Http/Controllers/Backend'));
    }


    /**
     * @param array $body
     * @param Request $request
     */
    private function stepOption(array $body, Request $request)
    {
        $default = [
            'site_title' => $body['title'],
            'subtitle' => $body['subtitle'],
            'site_description' => '',
            'site_url' => $request->getBaseUrl(),
            'open_comment' => 'false',
            'open_register' => 'false',
            'admin_email' => $body['email'],
        ];
        foreach ($default as $name => $value) {
            Option::add($name, $value);
        }
    }

    /**
     * @param string $installed
     */
    private function stepLast(string $installed)
    {
        $processUser = [];
        if (extension_loaded('posix')) {
            $processUser = posix_getpwuid(posix_geteuid());
        }
        $data = sprintf(
            '{"user": "%s", "group": "%s", "date": "%s"}',
            $processUser['name'] ?? '', $processUser['gecos'] ?? '', date('Y-m-d H:i:s')
        );
        file_put_contents($installed, $data);
    }
}
