<?php
declare(strict_types=1);

/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use App\Process\Http;
use support\Log;
use Support\Request;

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
    // File update detection and automatic reload
    'Monitor' => [
        'handler'     => \App\Process\Monitor::class,                // 热重载进程类
        'reloadable'  => false,                         // 是否允许子进程自动重载
        'constructor' => [
            'monitorDir'        => array_merge([
                app_path(),                                // app目录
                config_path(),                             // 配置目录
                base_path() . '/process',                  // 自定义进程
                base_path() . '/support',                  // 框架支持类
                base_path() . '/resource',                // 静态资源
                base_path() . '/.env',                     // 环境变量
            ],
                glob(base_path() . '/plugin/*/app'),
                glob(base_path() . '/plugin/*/config'),
                glob(base_path() . '/plugin/*/api')),
            'monitorExtensions' => ['php', 'html', 'htm', 'env', 'twig'], // 监听的文件扩展名
            'options'           => [
                'enable_file_monitor'   => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/',   // 启用文件变更监听（Linux/macOS）
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                                     // 启用内存监控（Linux/macOS）
            ]
        ]
    ]
];
