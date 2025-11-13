<?php

namespace App;

use App\Middleware\WebMiddleware;
use support\annotation\Middleware;

#[Middleware(WebMiddleware::class)]
class WebController extends BaseController
{

}