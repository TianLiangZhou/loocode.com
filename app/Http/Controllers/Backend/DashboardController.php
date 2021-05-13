<?php
declare(strict_types=1);

namespace App\Http\Controllers\Backend;


use App\Attributes\Route;
use App\Http\Result;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

/**
 * Class DashboardController
 * @package App\Http\Controllers\Backend
 */
#[Route(title: "面板", sort: 0, icon: "home", link: "/app/dashboard", home: true)]
class DashboardController extends BackendController
{
    /**
     * @param Request $request
     * @return Result
     */
    public function main(Request $request): Result
    {
        $card = [];
        $card[] = $this->getSystemInfo($request);
        if (($memory = $this->getMemory())) {
            $card[] = $memory;
        }
        $card[] = $this->getPHPInfo($request);
        $card[] = $this->getPHPExtension();
        return Result::ok($card);
    }

    /**
     * @param Request $request
     * @return string[]
     */
    private function getSystemInfo(Request $request): array
    {
        $data = [
            ['name' => '主机名', 'value' => php_uname('n')],
            ['name' => '内核', 'value' => php_uname('r')],
            ['name' => 'OS', 'value' => PHP_OS],
        ];
        $process = new Process(['uptime']);
        $process->run();
        if ($process->isSuccessful()) {
            $output = $process->getOutput();
            $data[] = ['name' => 'uptime', 'value' => $output];
        }
        $process = new Process(['cat', '/etc/timezone']);
        $process->run();
        if ($process->isSuccessful()) {
            $output = $process->getOutput();
            $data[] = ['name' => '时区', 'value' => $output];
        }
        $process = Process::fromShellCommandline('top -b -n 1 | grep Tasks');
        $process->run();
         if ($process->isSuccessful()) {
            $output = $process->getOutput();
            $data[] = ['name' => '进程', 'value' => substr($output, 6)];
        }
        $process = Process::fromShellCommandline('cat /proc/cpuinfo | grep "model name"');
        $process->run();
        if ($process->isSuccessful()) {
            $output = $process->getOutput();
            $data[] = ['name' => 'CPU', 'value' => substr(explode("\n", $output)[0], 12)];
        }

        return [
            'type' => 'table',
            'class' => 'col-6',
            'title' => '系统信息',
            'body'  => $data,
            'settings' => [
                'actions' => [
                    'add' => false,
                    'delete' => false,
                    'edit' => false,
                    'position' => 'right',
                ],
                'columns' => [
                    'name' => [
                        'title' => '名称',
                        'filter' => false,
                    ],
                    'value' => [
                        'title' => '值',
                        'filter' => false,
                    ]
                ]
            ],
        ];
    }

    /**
     * @return array|null
     */
    private function getMemory(): array
    {
        $process = new Process(['free', '-m']);
        $process->run();
        if ($process->isSuccessful()) {
            $output = $process->getOutput();
            $table = explode("\n", $output);
            $columns = str_split(substr($table[0], 8), 12);
            $trColumns = [
                'name0' => [
                'title' => '',
                'filter' => false,
            ]];
            foreach ($columns as $key => $column) {
                $trColumns['name' . ($key + 1)] = [
                    'title' => preg_replace("/\s+/", '', $column),
                    'filter' => false,
                ];
            }
            $data = [];
            foreach ($table as $key => $tr) {
                if ($key == 0 || $key + 1 == count($table)) {
                    continue;
                }
                $columns = str_split(substr($tr, 8), 12);
                $data[$key - 1] = [
                    'name0' => substr($tr, 0,8),
                ];
                foreach ($columns as $k => $column) {
                    $data[$key - 1]['name' . $k+1] = preg_replace("/\s+/", '', $column) . ' MB';
                }
            }

            return [
                'type' => 'table',
                'class' => 'col-6',
                'title' => '内存',
                'body'  => $data,
                'settings' => [
                    'actions' => [
                        'add' => false,
                        'delete' => false,
                        'edit' => false,
                        'position' => 'right',
                    ],
                    'columns' => $trColumns,
                ],
            ];
        }
        return [];
    }

    /**
     * @param Request $request
     * @return string[]
     */
    private function getPHPInfo(Request $request)
    {
        $os = php_uname('s'). php_uname('r');
        $phpVersion = PHP_VERSION;
        $mysqlVersion = DB::getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION);
        $laravelVersion = Application::VERSION;
        $runEnv = php_sapi_name();
        $serverSoft = $request->server('SERVER_SOFTWARE');
        $zendVersion = zend_version();
        $iniDir = php_ini_loaded_file();
        $iniConfig = ini_get_all(null, false);
        $displayError = $iniConfig['display_errors'] == 'Off' ? ['close-outline', 'danger'] : ['checkmark-outline', 'success'];
        $timezone = date_default_timezone_get();
        $data = [
            [
                'type' => 'input',
                'value' => $phpVersion,
                'name' => 'PHP版本',
            ],
            [
                'type' => 'input',
                'value' => $zendVersion,
                'name' => 'Zend引擎版本',
            ],
            [
                'type' => 'input',
                'value' => $serverSoft,
                'name' => 'Web服务',
            ],
            [
                'type' => 'input',
                'value' => $mysqlVersion,
                'name' => 'MYSQL版本',
            ],
            [
                'type' => 'input',
                'value' => $laravelVersion,
                'name' => 'Laravel版本',
            ],
            [
                'type' => 'input',
                'value' => $runEnv,
                'name' => '运行模式',
            ],
            [
                'type' => 'input',
                'value' => $iniDir,
                'name' => 'INI路径',
            ],
            [
                'type' => 'input',
                'value' => $iniConfig['memory_limit'],
                'name' => '脚本最大内存',
            ],
            [
                'type' => 'input',
                'value' => $iniConfig['upload_max_filesize'],
                'name' => '最大上传大小',
            ],
            [
                'type' => 'input',
                'value' => $iniConfig['post_max_size'],
                'name' => 'POST提交大小',
            ],
            [
                'type' => 'icon',
                'value' => $displayError[0],
                'status' => $displayError[1],
                'name' => '显示错误',
            ],
            [
                'type' => 'input',
                'value' => $iniConfig['max_execution_time'],
                'name' => '脚本超时时间',
            ],
            [
                'type' => 'input',
                'value' => $timezone,
                'name' => '当前时区',
            ],
        ];
        return [
            'type' => 'form',
            'class' => 'col-6',
            'title' => 'PHP信息',
            'body'  => $data,
        ];
    }

    /**
     * @return string[]
     */
    private function getPHPExtension()
    {
        $extensions  = get_loaded_extensions();
        $body = [];
        foreach ($extensions as $ext) {
            $body[] = $ext;
        }
        return [
            'type' => 'grid',
            'class' => 'col-6',
            'title' => 'PHP扩展',
            'body'  => $body,
        ];

    }
}
