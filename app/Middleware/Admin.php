<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Middleware\Traits\Authorize;
use Exception;
use Webman\Http\Request;
use Webman\Http\Response;

class Admin extends InitApp
{
    use Authorize;

//
//    /**
//     * @var Admin
//     */
//    #[Inject]
//    protected Admin $adminAuthorize;

    /**
     * 对外提供的鉴权中间件
     *
     * @param Request  $request
     * @param callable $handler
     *
     * @return Response
     * @throws Exception
     */
    public function process(Request $request, callable $handler): Response
    {
        // 调用父类公共逻辑
        $response = parent::process($request, $handler);
        $controller = $request->controller;
        $action = $request->action;

        $code = 0;
        $msg = '';
        $redirectUrl = '';
        $account = null;

//        if (!$this->adminAuthorize::access($controller, $action, $code, $msg, $redirectUrl, $account)) {
//            if ($request->expectsJson()) {
//                $response = json(['code' => $code, 'msg' => $msg]);
//            } else {
//                if ($code === 401) {
//                    return redirect($redirectUrl);
//                } else {
//                    $response = view('error', [], 'public')->withStatus(403);
//                }
//            }
//        } else {
//            View::assign([
//                'account' => $account,
//            ]);
//            $response = $request->method() == 'OPTIONS' ? response() : $handler($request);
//        }

        return $response;
    }
}