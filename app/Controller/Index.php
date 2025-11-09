<?php
declare(strict_types=1);

namespace App\Controller;

use App\Route;
use support\Response;

class Index extends Common
{
    /**
     * @return Response
     */
    #[Route(path: "/", methods: ['GET'])]
    public function index(): Response
    {
        return view('index');
    }

    /**
     * @return Response
     */
    #[Route(path: "/user/login", methods: ['GET'])]
    public function login(): Response
    {
        return view('index');
    }
}