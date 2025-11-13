<?php

namespace App\Controller\User;

use App\Core\Route;
use App\WebController;
use support\Response;

class Index extends WebController
{
    /**
     * @return Response
     */
    #[Route(path: "/user/index", methods: ['GET'], permission: 'user.index')]
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