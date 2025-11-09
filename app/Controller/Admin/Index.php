<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Common;
use App\Route;
use App\Service\Admin as AdminService;
use DI\Attribute\Inject;
use Exception;
use support\Request;
use support\Response;

class Index extends Common
{
    /**
     * @var AdminService
     */
    #[Inject]
    protected AdminService $admin;

    /**
     * @return Response
     */
    #[Route(path: "/admin/index", methods: ['GET'])]
    public function index(): Response
    {
        return view('admin/index');
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    #[Route(path: "/admin/login", methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        // 判断是否登录
        if ($this->auth->getCurrentAccount()) {
            return redirect(url('admin.index'));
        }

        if ($request->isAjax()) {
            $params = request()->post();
            $this->validate('Admin', $params, 'login');
            $this->admin->login($params);
            return result(302, trans('key27'), ['url' => url('admin.index')]);
        }
        return view('admin/login');
    }
}