<?php

namespace App\Controller\Admin;

use App\Controller\Common;
use extend\Attribute\Route;
use support\Request;
use support\Response;

class Index extends Common
{
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
     */
    #[Route(path: "/admin/login", methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        if ($request->isAjax()) {
            $data = request()->post();
            $this->validateWith('Admin', $data, 'login');
        }
        return view('admin/login');
    }
}