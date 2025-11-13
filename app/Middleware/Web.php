<?php

namespace App\Middleware;

use App\Middleware\Traits\Authorize;

class Web extends InitApp
{
    use Authorize;
}