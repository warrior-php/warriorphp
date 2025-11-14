<?php
declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\WebController;
use App\Route;
use support\Response;
use WarriorPHP\Event\Event;

class Index extends WebController
{
    /**
     * @return Response
     */
    #[Route(path: "/user/index", methods: ['GET'], permission: 'user.index')]
    public function index(): Response
    {
        return view('user/index');
    }

    /**
     * @return Response
     */
    #[Route(path: "/user/login", methods: ['GET'])]
    public function login(): Response
    {
        $user = [
            'name' => 'webman',
            'age'  => 2
        ];
        Event::dispatch('user.register', $user);
        return view('user/login');
    }
}