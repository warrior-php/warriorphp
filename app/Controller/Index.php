<?php

namespace App\Controller;

use App\Model\Admin;
use extend\Attribute\Route;
use support\Response;

class Index extends Common
{
    /**
     * @return Response
     */
    #[Route(path: "/index", methods: ['GET'])]
    public function index(): Response
    {
        Admin::findByIdentifier('admin');
        return view('index');
    }
}