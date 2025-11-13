<?php
declare(strict_types=1);

return [
    'event_loop'       => '',
    'stop_timeout'     => 2,
    'pid_file'         => runtime_path() . '/warrior.pid',
    'status_file'      => runtime_path() . '/warrior.status',
    'stdout_file'      => runtime_path() . '/logs/warrior.log',
    'log_file'         => runtime_path() . '/logs/warrior.log',
    'max_package_size' => 10 * 1024 * 1024
];
