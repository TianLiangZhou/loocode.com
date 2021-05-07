<?php


namespace App\Services;


use App\Exceptions\InstallException;
use Corcel\Model\Option;
use Corcel\Model\User;
use Corcel\Services\PasswordService;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class InstallService extends BaseService
{
    /**
     * @param array $body
     * @throws \ReflectionException
     */
    public function activate(array $body)
    {
        $this->stepDatabase();
        $this->stepUser($body);
        $this->stepOption($body);
        $installedPath = base_path('bootstrap/cache/__installed');
        $this->stepLast($installedPath);
    }

    /**
     *
     */
    private function stepDatabase()
    {
        if (PHP_SAPI === 'cli') {
            Artisan::call("migrate:install");
        } else {
            $table = config('database.migrations');
            $db = app('db');
            $repository = new DatabaseMigrationRepository($db, $table);
            if (!$repository->repositoryExists()) {
                $repository->createRepository();
            }
            $migrator = new Migrator($repository, $db, app('files'), app('events'));
            $migrator->run(database_path('migrations'));
        }
    }


    /**
     * @throws \ReflectionException
     */
    private function stepUser(array $body): void
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
        $user->user_url = $body['site_url'];
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
     */
    private function stepOption(array $body)
    {
        $default = [
            'site_title' => $body['title'],
            'subtitle' => $body['subtitle'],
            'site_description' => '',
            'site_url' => $body['site_url'],
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
