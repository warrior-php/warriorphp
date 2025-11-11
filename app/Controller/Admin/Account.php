<?php

namespace App\Controller\Admin;

use App\Controller\Common;
use App\Route;
use App\Service\Auth\Admin as AdminService;
use DI\Attribute\Inject;
use Exception;
use support\Request;
use support\Response;

class Account extends Common
{
    /**
     * @var AdminService
     */
    #[Inject]
    protected AdminService $admin;

    /**
     * 管理员资料
     * @return Response
     */
    #[Route(path: "/admin/account/index", methods: ['GET'], permission: 'admin.account.index')]
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
        if ($this->admin->getSessionData()) {
            return redirect(url('admin.index'));
        }

        if ($request->isAjax()) {
            $params = request()->post();
            $this->validate('Admin', $params, 'login');
            $this->admin->login($params);
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
    #[Route(path: "/admin/account/logout", methods: ['GET'], permission: 'admin.logout')]
    public function logout(Request $request): Response
    {
        $this->admin->logout();
        return result(302, trans('key27'), ['url' => url('admin.account.login')]);
    }
}