<?php

namespace App\Controller\Admin;

use App\AdminController;
use App\Core\Route;
use App\Service\Admin\LoginService;
use DI\Attribute\Inject;
use Exception;
use support\Request;
use support\Response;

class Account extends AdminController
{
    /**
     * @var LoginService
     */
    #[Inject]
    protected LoginService $loginService;

    /**
     * 管理员资料
     * @return Response
     */
    #[Route(path: "/admin/account/index", methods: ['GET'], middleware: 'Admin', permission: 'admin.account.index')]
    public function profile(): Response
    {
        return view('admin/account/index');
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    #[Route(path: "/admin/account/login", methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        if ($this->loginService::getSessionData('admin')) {
            return redirect(url('admin.index'));
        }

        if ($request->isAjax()) {
            $params = request()->post();
            $this->validate('Admin', $params, 'login');
            $this->loginService->login($params);
            return result(302, trans('key27'), ['url' => url('admin.index')]);
        }

        return view('admin/account/login');
    }

    /**
     * 管理员退出
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    #[Route(path: "/admin/account/logout", methods: ['GET'], middleware: 'Admin', permission: 'admin.logout')]
    public function logout(Request $request): Response
    {
        $this->loginService->logout();
        return result(302, trans('key27'), ['url' => url('admin.account.login')]);
    }
}