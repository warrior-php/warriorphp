<?php

namespace App\Controller\Admin;

use App\Attributes\Route;
use App\Controller\Common;
use support\Response;

class Index extends Common
{
    /**
     * @return Response
     */
    #[Route(path: "/admin/index")]
    public function index(): Response
    {
        return view('index');
    }

    /**
     * @return Response
     */
    #[Route(path: "/admin/login")]
    public function login(): Response
    {
        return view('index');
    }
}