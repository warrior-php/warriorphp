<?php

namespace App\Controller\User;

use App\Controller\Common;
use App\Core\Route;
use support\Response;

class Index extends Common
{
    /**
     * @return Response
     */
    #[Route(path: "/user/index", methods: ['GET'], middleware: 'Web', permission: 'user.index')]
    public function index(): Response
    {
        return view('user/index');
    }

    /**
     * @return Response
     */
    #[Route(path: "/user/login", methods: ['GET'])]
    public function login(): Response
    {
        return view('user/login');
    }
}