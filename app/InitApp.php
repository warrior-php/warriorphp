<?php

namespace App;

use Webman\Bootstrap;
use Workerman\Worker;

class InitApp implements Bootstrap
{
    /**
     * 系统版本
     * @var string
     */
    protected static string $version = 'v2.7.0';

    /**
     * start
     *
     * @param Worker|null $worker
     *
     * @return void
     */
    public static function start(?Worker $worker): void
    {
        self::initPathConst();
    }

    /**
     * 初始化常量
     * @return void
     */
    private static function initPathConst(): void
    {
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        defined('VERSION') or define('VERSION', self::$version);
    }
}