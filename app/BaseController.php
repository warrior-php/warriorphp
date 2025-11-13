<?php

namespace App;

use App\Core\Validator;
use support\exception\BusinessException;

class BaseController
{
    /**
     * 通用验证方法
     *
     * @param string $ruleClass 类名（可传简写：User 或完整命名空间）
     * @param array  $data      待验证的数据
     * @param string $scene     场景名称（可选）
     *
     * @return void
     */
    protected function validate(string $ruleClass, array $data, string $scene = ''): void
    {
        // 拼接完整类名（如果没带命名空间）
        if (!str_contains($ruleClass, '\\')) {
            $ruleClass = 'App\\Validator\\' . $ruleClass;
        }

        // 检查类是否存在
        if (!class_exists($ruleClass)) {
            throw new BusinessException("Class not found: $ruleClass");
        }

        // 实例化验证器
        $validator = new $ruleClass();

        // 如果提供了场景，则设置
        if ($scene && method_exists($validator, 'scene')) {
            $validator->scene($scene);
        }

        /** @var Validator $validator */
        $validator->validate($data);
    }
}