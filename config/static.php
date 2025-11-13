<?php
declare(strict_types=1);

use App\Middleware\StaticFileMiddleware;

return [
    'enable'     => true,
    'middleware' => [
        StaticFileMiddleware::class,
    ],
];