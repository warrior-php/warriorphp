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
    #[Route(path: "/", methods: ['GET'], middleware: 'AccessControl')]
    public function index(): Response
    {
        return view('index');
    }
}