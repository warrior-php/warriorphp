<?php

namespace App\Middleware;

use App\Middleware\Traits\Authorize;

class Api extends InitApp
{
    use Authorize;
}