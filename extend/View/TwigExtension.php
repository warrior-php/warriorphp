<?php

namespace extend\View;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    /**
     * 注册自定义 TwigExtension 函数
     *
     * @return array 返回包含 TwigFunction 实例的数组
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('__ASSETS__', [$this, 'getAssetsPath']),
            new TwigFunction('trans', [$this, 'generateTrans'])
        ];
    }


    /**
     * 获取静态资源路径
     *
     * @param string $path 可选的相对资源路径，默认值为空字符串
     *
     * @return string 返回格式化后的路径字符串，确保前缀为单个 `/`
     */
    public function getAssetsPath(string $path = ''): string
    {
        return '/assets/' . ltrim($path, '/');
    }

    /**
     * Trans
     *
     * @param string $key
     *
     * @return string
     */
    public function generateTrans(string $key): string
    {
        return trans($key);
    }
}