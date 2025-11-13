<?php
declare(strict_types=1);

namespace App\Controller\Install;

use App\Controller\Common;
use App\Core\Route;

class Index extends Common
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