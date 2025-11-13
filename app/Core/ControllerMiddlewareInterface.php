<?php

namespace App\Core;

use Webman\Http\Request;
use Webman\Http\Response;

interface ControllerMiddlewareInterface
{
    /**
     * 控制器专用中间件接口
     *
     * @param Request $request
     *
     * @return Response|null
     */
    public function process(Request $request): ?Response;
}