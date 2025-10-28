<?php
declare(strict_types=1);

/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use extend\Utils\View\TwigExtension;
use extend\Utils\View\TwigView;

return [
    'handler'   => TwigView::class,
    'options'   => [
        'debug'       => true, // 开发阶段建议开启 debug
        'cache'       => runtime_path() . '/views', // 缓存目录，正常设置即可
        'auto_reload' => true, // 启用自动重新编译模板
        'view_suffix' => 'twig',
        'charset'     => 'UTF-8',
    ],
    'extension' => function ($twig) {
        $twig->addExtension(new TwigExtension());
    }
];
