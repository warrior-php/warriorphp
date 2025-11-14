<?php
declare(strict_types=1);

namespace App\Middleware;

use Webman\Http\Request;
use Webman\Http\Response;

class Api extends BaseMiddleware
{
    /**
     * 处理请求
     *
     * @param Request  $request
     * @param callable $handler
     *
     * @return Response
     */
    public function process(Request $request, callable $handler): Response
    {
        return $request->method() == 'OPTIONS' ? response('') : $handler($request);
    }
}