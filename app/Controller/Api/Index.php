<?php

namespace App\Controller\Api;

use App\Controller\Common;
use App\Route;
use support\Response;

class Index extends Common
{
    /**
     * 无需登录的操作列表
     *
     * 控制器中定义的动作名称（如方法名），列在此数组中时，
     * 用户即使未登录也可以访问这些动作。
     *
     * @var string[]
     */
    protected array $noNeedLogin = ['index'];

    /**
     * @return Response
     */
    #[Route(path: "/api/index", methods: ['GET'])]
    public function index(): Response
    {
        return result(200, trans('key3'));
    }
}