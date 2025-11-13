<?php
declare(strict_types=1);

namespace App\Controller;

use App\Middleware\Api;
use support\annotation\Middleware;

#[Middleware(Api::class)]
class ApiController extends BaseController
{

}