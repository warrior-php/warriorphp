<?php

namespace App\Controller;

use Exception;
use extend\Attribute\Route;
use support\Request;
use support\Response;

class Common
{
    /**
     * 无需登录的操作列表
     *
     * 控制器中定义的动作名称（如方法名），列在此数组中时，
     * 用户即使未登录也可以访问这些动作。
     *
     * @var string[]
     */
    protected array $noNeedLogin = ['register', 'login', 'setLang'];

    /**
     * 无需鉴权的操作列表
     *
     * 用户虽然需要登录，但不必验证具体权限的操作列表。
     * 通常用于首页仪表盘、公告页等不敏感模块。
     *
     * 示例：
     * - dashboard：系统仪表盘页面
     *
     * @var string[]
     */
    protected array $noNeedAuth = ['logout'];

    /**
     * 设置语言
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    #[Route(path: "/setLang", methods: ['POST'])]
    public function setLang(Request $request): Response
    {
        $postData = request()->post();
        $lang = $postData['lang'] ?? null;
        session()->set('lang', $lang);
        return result(200);
    }
}