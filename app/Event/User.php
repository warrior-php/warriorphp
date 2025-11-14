<?php
declare(strict_types=1);

namespace App\Event;

class User
{
    /**
     * @param $user
     *
     * @return void
     */
    function register($user): void
    {
        dump('register');
        dump($user);
    }
}