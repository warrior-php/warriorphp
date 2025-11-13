<?php
declare(strict_types=1);

use DI\ContainerBuilder;

$builder = new ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAttributes(true);
try {
    return $builder->build();
} catch (Exception $e) {
    echo '[容器初始化失败] Webman 依赖注入容器构建异常' . PHP_EOL;
    echo "错误位置: " . $e->getFile() . " (第 " . $e->getLine() . " 行)" . PHP_EOL;
    echo "请检查 config/dependence.php 配置 或 服务类是否存在语法错误。" . PHP_EOL;
    echo PHP_EOL;
    exit(1);
}