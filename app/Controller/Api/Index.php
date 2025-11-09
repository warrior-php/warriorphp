<?php

namespace App\Controller\Api;

use App\Controller\Common;
use App\Route;
use support\Response;

class Index extends Common
{
    /**
     * @return Response
     */
    #[Route(path: "/api/index", methods: ['GET'])]
    public function index(): Response
    {
        return result(200, trans('key3'));
    }
}