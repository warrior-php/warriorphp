<?php

namespace App\Controller\Admin;

use App\Controller\Common;
use App\Route\RouteAttr;
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
    #[RouteAttr(path: "/admin/index", methods: ['GET'])]
    public function index(): Response
    {
        return view('index');
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    #[RouteAttr(path: "/admin/login", methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        if ($request->isAjax()) {
            $params = request()->post();
            $this->validateWith('Admin', $params, 'login');
            $this->admin->login($params);
        }
        return view('admin/login');
    }
}