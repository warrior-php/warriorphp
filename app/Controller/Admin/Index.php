<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Common;
use App\Core\Route;
use support\Response;

class Index extends Common
{
    /**
     * @return Response
     */
    #[Route(path: "/admin/index", methods: ['GET'], middleware: 'Admin', permission: 'admin.index')]
    public function index(): Response
    {
        return view('admin/index');
    }

}