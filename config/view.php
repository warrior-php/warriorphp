<?php
declare(strict_types=1);

use App\View;

return [
    'handler' => View::class,
    'options' => [
        'debug'       => true,
        'cache'       => runtime_path() . '/views',
        'auto_reload' => true,
        'view_suffix' => 'twig',
        'charset'     => 'UTF-8',
    ],
];
