<?php
declare(strict_types=1);

/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use App\Service\RouteService;
use Support\Request;
use Webman\Route;

//// 禁用默认路由
Route::disableDefaultRoute();
//// 注册应用路由
RouteService::registerRoutes();

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
        'data'        => $map[$status],
        'request_url' => $request->uri(),
        'timestamp'   => time()
    ];
    return $request->expectsJson() ? json($responseData)->withStatus($status) : view('error', $responseData, 'public')->withStatus($status);
});