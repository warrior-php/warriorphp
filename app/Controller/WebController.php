<?php
declare(strict_types=1);

namespace App\Controller;

use App\Middleware\Web;
use support\annotation\Middleware;

#[Middleware(Web::class)]
class WebController extends BaseController
{

}