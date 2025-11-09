<?php
declare(strict_types=1);

use App\RouteLoader as AppRoute;
use Support\Request;
use Webman\Route;

// 禁用默认路由
Route::disableDefaultRoute();

// 注册应用路由
AppRoute::registerRoutes();

// 错误处理路由
Route::fallback(function (Request $request, $status) {
    $map = [
        404 => [
            'page_title' => "Page Not Found",
            'title'      => "Opps!!!",
            'content'    => "This page you are looking for could not be found.",
        ],
        405 => [
            'page_title' => "Method Not Allowed",
            'title'      => "Opps!!!",
            'content'    => "The requested HTTP method is not allowed for this endpoint.",
        ],
    ];

    $responseData = [
        'code'        => $status,
        'data'        => $map[$status] ?? [],
        'request_url' => $request->uri(),
        'timestamp'   => time()
    ];

    // 分别处理 404 与 405
    if ($status === 405) {
        // 405 强制返回 JSON
        return json($responseData)->withStatus(405);
    }

    // 404 返回模板
    return view('error', $responseData, 'public')->withStatus(404);
});
