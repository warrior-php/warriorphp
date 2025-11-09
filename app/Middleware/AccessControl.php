<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Service\Auth as AuthService;
use DI\Attribute\Inject;
use ReflectionException;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AccessControl implements MiddlewareInterface
{
    /**
     * @var AuthService
     */
    #[Inject]
    protected AuthService $auth;

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

        if (!$this->auth::canAccess($controller, $action, $code, $msg)) {
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