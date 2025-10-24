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
            new TwigFunction('trans', [$this, 'generateTrans'])
        ];
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