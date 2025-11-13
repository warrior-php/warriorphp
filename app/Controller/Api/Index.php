<?php

namespace App\Controller\Api;

use App\ApiController;
use App\Core\Route;
use support\Response;

class Index extends ApiController
{
    /**
     * @return Response
     */
    #[Route(path: "/api/index", methods: ['GET'], middleware: 'Api')]
    public function index(): Response
    {
        return result(200, trans('key3'));
    }
}