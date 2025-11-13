<?php
declare(strict_types=1);

namespace App\Controller\Install;

use App\Core\Route;
use App\WebController;

class Index extends WebController
{
    /**
     * @return string
     */
    #[Route(path: "/install/index", methods: ['GET'], middleware: 'Web')]
    public function index(): string
    {
        return 'index';
    }
}