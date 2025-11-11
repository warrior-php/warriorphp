<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Service\Auth\Access;
use DI\Attribute\Inject;
use Exception;
use support\View;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AccessControl implements MiddlewareInterface
{
    /**
     * @var Access
     */
    #[Inject]
    protected Access $access;

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
        $action = $request->action;

        $code = 0;
        $msg = '';
        $url = '';
        $account = null;

        if (!$this->access::canAccess($controller, $action, $code, $msg, $url, $account)) {
            if ($request->expectsJson()) {
                $response = json(['code' => $code, 'msg' => $msg]);
            } else {
                if ($code === 401) {
                    return redirect($url);
                } else {
                    $request->app = '';
                    $request->plugin = 'admin';
                    $response = view('common/error/403')->withStatus(403);
                }
            }
        } else {
            View::assign([
                'account' => $account,
            ]);
            $response = $request->method() == 'OPTIONS' ? response() : $handler($request);
        }

        return $response;
    }
}