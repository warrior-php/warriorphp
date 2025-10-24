<?php
declare(strict_types=1);
/**
 * Here is functions.
 */

if (!function_exists('views_path')) {
    /**
     * views path
     *
     * @param string $path
     *
     * @return string
     */
    function views_path(string $path = 'views'): string
    {
        return path_combine(BASE_PATH . DIRECTORY_SEPARATOR . 'resources', $path);
    }
}