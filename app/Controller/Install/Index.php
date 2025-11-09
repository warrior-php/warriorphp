<?php
declare(strict_types=1);

namespace App\Controller\Install;

use App\Controller\Common;
use App\Route;

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
     * @return string
     */
    #[Route(path: "/install/index", methods: ['GET'])]
    public function index(): string
    {
        return 'index';
    }
}