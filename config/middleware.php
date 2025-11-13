<?php
declare(strict_types=1);

use App\Middleware\ControllerMiddleware;

return [
    // 超全局中间件
    '@' => [
        // 控制器中间件
        ControllerMiddleware::class
    ]
];