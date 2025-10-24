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

return [
    'event_loop'       => '',
    'stop_timeout'     => 2,
    'pid_file'         => runtime_path() . '/warrior.pid',
    'status_file'      => runtime_path() . '/warrior.status',
    'stdout_file'      => runtime_path() . '/logs/warrior.log',
    'log_file'         => runtime_path() . '/logs/warrior.log',
    'max_package_size' => 10 * 1024 * 1024
];
