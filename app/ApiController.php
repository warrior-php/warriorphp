<?php

namespace App;

use App\Middleware\Api;
use support\annotation\Middleware;

#[Middleware(Api::class)]
class ApiController extends BaseController
{

}