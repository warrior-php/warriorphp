<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\AdminController;
use App\Core\Route;
use support\Response;

class Index extends AdminController
{
    /**
     * @return Response
     */
    #[Route(path: "/admin/index", methods: ['GET'], permission: 'admin.index')]
    public function index(): Response
    {
        return view('admin/index');
    }

}