<?php

namespace App\Middleware;

use App\Service\AuthService;
use DI\Attribute\Inject;
use ReflectionException;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AccessControl implements MiddlewareInterface
{
    #[Inject]
    protected AuthService $authService;

    /**
     * @param Request  $request
     * @param callable $handler
     *
     * @return Response
     * @throws ReflectionException
     */
    public function process(Request $request, callable $handler): Response
    {
        $controller = $request->controller;
        $action = $request->action;

        $code = 0;
        $msg = '';

        if (!$this->authService::canAccess($controller, $action, $code, $msg)) {
            if ($request->expectsJson()) {
                $response = json(['code' => $code, 'msg' => $msg, 'data' => []]);
            } else {
                if ($code === 401) {
                    return redirect(url('admin.login'));
                } else {
                    $request->app = '';
                    $request->plugin = 'admin';
                    $response = view('common/error/403')->withStatus(403);
                }
            }
        } else {
            $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        }

        return $response;
    }
}