<?php
declare(strict_types=1);

namespace App;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Webman\View as WebmanView;

class View extends AbstractExtension implements WebmanView
{
    /**
     * 注册自定义 Twig 函数
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('__ASSETS__', [$this, 'getAssetsPath']),
            new TwigFunction('url', [$this, 'generateUrl']),
            new TwigFunction('trans', [$this, 'generateTrans']),
        ];
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getAssetsPath(string $path = ''): string
    {
        return '/assets/' . ltrim($path, '/');
    }

    /**
     * @param string $path
     * @param array  $params
     *
     * @return string
     */
    public function generateUrl(string $path, array $params = []): string
    {
        return url($path, $params);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function generateTrans(string $key): string
    {
        return trans($key);
    }

    /**
     * 变量注入
     *
     * @param string|array $name
     * @param mixed|null   $value
     *
     * @return void
     */
    public static function assign(string|array $name, mixed $value = null): void
    {
        $request = request();
        $request->view_vars = array_merge((array)$request->view_vars, is_array($name) ? $name : [$name => $value]);
    }

    /**
     * 渲染模板
     *
     * @param string      $template
     * @param array       $vars
     * @param string|null $app
     * @param string|null $plugin
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public static function render(string $template, array $vars, ?string $app = null, ?string $plugin = null): string
    {
        static $views = [];
        $request = request();
        $plugin = $plugin === null ? ($request->plugin ?? '') : $plugin;
        $app = $app === null ? ($request->app ?? '') : $app;
        $configPrefix = $plugin ? "plugin.$plugin." : '';
        $viewSuffix = config("{$configPrefix}view.options.view_suffix", 'html');
        $baseViewPath = $plugin
            ? base_path() . "/plugin/$plugin/app"
            : ($app == 'public' ? public_path() : views_path());

        if ($template[0] === '/') {
            $template = ltrim($template, '/');
            if (str_contains($template, '/view/')) {
                [$viewPath, $template] = explode('/view/', $template, 2);
                $viewPath = base_path("$viewPath/view");
            } else {
                $viewPath = base_path();
            }
        } else {
            $viewPath = $app === '' ? "$baseViewPath" : $baseViewPath;
        }

        if (!isset($views[$viewPath])) {
            $twig = new Environment(new FilesystemLoader($viewPath), config("{$configPrefix}view.options", []));
            $twig->addExtension(new self()); // 注册自定义函数
            $views[$viewPath] = $twig;
        }

        if (isset($request->view_vars)) {
            $vars = array_merge((array)$request->view_vars, $vars);
        }

        return $views[$viewPath]->render("$template.$viewSuffix", $vars);
    }
}
