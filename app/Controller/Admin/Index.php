<?php

namespace App\Controller\Admin;

use App\Controller\Common;
use App\Service\AuthService;
use DI\Attribute\Inject;
use Exception;
use extend\Attribute\Route;
use support\Request;
use support\Response;

class Index extends Common
{
    /**
     * @var AuthService
     */
    #[Inject]
    protected AuthService $authService;

    /**
     * @return Response
     */
    #[Route(path: "/admin/index", methods: ['GET'])]
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
    #[Route(path: "/admin/login", methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        if ($request->isAjax()) {
            $params = request()->post();
            $this->validateWith('Admin', $params, 'login');
            $this->authService->login($params);
        }
        return view('admin/login');
    }
}