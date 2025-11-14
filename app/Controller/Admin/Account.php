<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Route;
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
     * 管理员
     * @return Response
     */
    #[Route(path: "/admin/account/index", methods: ['GET'], permission: 'admin.account.index',middleware: 'XXXX')]
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
        if (session('admin')) {
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
     * @return Response
     * @throws Exception
     */
    #[Route(path: "/admin/account/logout", methods: ['GET'], permission: 'admin.logout')]
    public function logout(): Response
    {
        $this->loginService->logout();
        return result(302, trans('key27'), ['url' => url('admin.account.login')]);
    }
}