<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Route;
use App\WebController;
use support\Response;

class Index extends WebController
{
    /**
     * @return Response
     */
    #[Route(path: "/", methods: ['GET'])]
    public function index(): Response
    {
        return view('index');
    }
}