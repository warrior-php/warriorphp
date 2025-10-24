<?php

namespace App\Controller;

use App\Attributes\Route;
use App\Model\Admin;
use support\Response;

class Index extends Common
{
    /**
     * @return Response
     */
    #[Route(path: "/index")]
    public function index(): Response
    {
        Admin::findByIdentifier('admin');
        return view('index');
    }
}