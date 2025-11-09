<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Service\Auth as AuthService;
use DI\Attribute\Inject;
use Exception;
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
     * @throws Exception
     */
    public function process(Request $request, callable $handler): Response
    {
        $controller = $request->controller;

        $code = 0;
        $msg = '';
        $loginUrl = '';

        if (!$this->auth::canAccess($controller, $code, $msg, $loginUrl)) {
            if ($request->expectsJson()) {
                $response = json(['code' => $code, 'msg' => $msg]);
            } else {
                if ($code === 401) {
                    return redirect($loginUrl);
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