<?php

namespace App;

use App\Middleware\Web;
use support\annotation\Middleware;

#[Middleware(Web::class)]
class WebController extends BaseController
{

}