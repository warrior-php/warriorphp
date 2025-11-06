<?php

namespace App\Controller\Admin;

use App\Controller\Common;
use extend\Attribute\Route;
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
     * @return Response
     */
    #[Route(path: "/admin/login", methods: ['GET', 'POST'])]
    public function login(): Response
    {
        return view('admin/login');
    }
}