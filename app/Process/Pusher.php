<?php
declare(strict_types=1);

namespace App\Process;

use Workerman\Connection\TcpConnection;

class Pusher
{
    /**
     * onConnect
     *
     * @param TcpConnection $connection
     *
     * @return void
     */
    public function onConnect(TcpConnection $connection): void
    {
        dump('onConnect');
    }

    /**
     * onWebSocketConnect
     *
     * @param TcpConnection $connection
     * @param               $http_buffer
     *
     * @return void
     */
    public function onWebSocketConnect(TcpConnection $connection, $http_buffer): void
    {
        dump('onWebSocketConnect');
    }

    /**
     * onMessage
     *
     * @param TcpConnection $connection
     * @param               $data
     *
     * @return void
     */
    public function onMessage(TcpConnection $connection, $data): void
    {
        dump('onMessage');
        $connection->send($data);
    }

    /**
     * onClose
     *
     * @param TcpConnection $connection
     *
     * @return void
     */
    public function onClose(TcpConnection $connection): void
    {
        dump('onClose');
    }
}