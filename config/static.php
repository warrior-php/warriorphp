<?php
declare(strict_types=1);

use App\Middleware\StaticFile;

return [
    'enable'     => true,
    'middleware' => [
        StaticFile::class,
    ],
];