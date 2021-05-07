<?php


namespace App\Console;


use App\Services\InstallService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The Install Application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("您正在安装程序，完成以下交互即可安装成功");
        $siteName = $this->ask("请输入站点名称[必填]");
        $nickname = $this->ask("请输入昵称");
        $email = $this->ask("请输入邮箱[必填]");
        $password = $this->ask("请输入密码[必填]");
        if (empty($siteName) || empty($email) || empty($password)) {
            $this->error("数据错误，不能有空值");
            return;
        }
        $body = [
            'email' => $email,
            'password' => $password,
            'title' => $siteName,
            'subtitle' => '',
            'nickname' => $nickname,
            'site_url' => env('APP_URL'),
        ];
        $this->info("正在安装...");
        (new InstallService())->activate($body);
        $this->info("安装成功...");
    }
}
