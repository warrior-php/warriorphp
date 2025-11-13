<?php

namespace App\Controller\Api;

use App\Controller\ApiController;
use App\Route;
use support\Response;

class Index extends ApiController
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