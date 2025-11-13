<?php
declare(strict_types=1);

namespace App\Controller\Install;

use App\Controller\WebController;
use App\Route;

class Index extends WebController
{
    /**
     * @return string
     */
    #[Route(path: "/install/index", methods: ['GET'])]
    public function index(): string
    {
        return 'index';
    }
}