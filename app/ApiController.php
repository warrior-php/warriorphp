<?php

namespace App;

use App\Middleware\ApiMiddleware;
use support\annotation\Middleware;

#[Middleware(ApiMiddleware::class)]
class ApiController extends BaseController
{

}