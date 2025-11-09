<?php

namespace App\Controller;

use App\Model\Admin;
use App\Route\RouteAttr;
use support\Response;

class Index extends Common
{
    /**
     * @return Response
     */
    #[RouteAttr(path: "/index", methods: ['GET'])]
    public function index(): Response
    {
        Admin::findByIdentifier('admin');
        return view('index');
    }
}