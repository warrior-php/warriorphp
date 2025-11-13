<?php
declare(strict_types=1);

namespace App\Controller\Web;

use App\Controller\WebController;
use App\Route;
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