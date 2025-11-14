<?php
declare(strict_types=1);

use App\Http;
use App\Process\Monitor;
use App\Process\Task;
use support\Log;
use support\Request;

global $argv;


return [
    'Warrior' => [
        'handler'     => Http::class,
        'listen'      => 'http://0.0.0.0:8888',
        'count'       => 1,//cpu_count() * 4,
        'user'        => '',
        'group'       => '',
        'reusePort'   => false,
        'eventLoop'   => '',
        'context'     => [],
        'constructor' => [
            'requestClass' => Request::class,
            'logger'       => Log::channel(),
            'appPath'      => app_path(),
            'publicPath'   => public_path()
        ]
    ],

    // 定时任务
    'Task'    => [
        'handler' => Task::class
    ],

    // Websocket
    'Pusher'  => [
        // 这里指定进程类，就是上面定义的Pusher类
        'handler' => App\Process\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:1234',
        'count'   => 1,
    ],

    // File update detection and automatic reload
    'Monitor' => [
        'handler'     => Monitor::class, // 热重载进程类
        'reloadable'  => false, // 是否允许子进程自动重载
        'constructor' => [
            'monitorDir'        => array_merge([
                app_path(), // app目录
                config_path(), // 配置目录
                base_path() . '/process', // 自定义进程
                base_path() . '/support', // 框架支持类
                base_path() . '/resource', // 静态资源
                base_path() . '/.env', // 环境变量
            ],
                glob(base_path() . '/plugin/*/app'),
                glob(base_path() . '/plugin/*/config'),
                glob(base_path() . '/plugin/*/api')),
            'monitorExtensions' => ['php', 'html', 'htm', 'env', 'twig'], // 监听的文件扩展名
            'options'           => [
                'enable_file_monitor'   => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // 启用文件变更监听（Linux/macOS）
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/', // 启用内存监控（Linux/macOS）
            ]
        ]
    ]
];
