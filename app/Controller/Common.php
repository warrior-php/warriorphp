<?php

namespace App\Controller;

use extend\Route\Route;
use App\Validator\BaseValidator;
use Exception;
use support\exception\BusinessException;
use support\Request;
use support\Response;
use Webman\Captcha\CaptchaBuilder;
use Webman\Captcha\PhraseBuilder;

class Common
{
    /**
     * 无需登录的操作列表
     *
     * 控制器中定义的动作名称（如方法名），列在此数组中时，
     * 用户即使未登录也可以访问这些动作。
     *
     * @var string[]
     */
    protected array $noNeedLogin = ['register', 'login', 'setLang', 'captcha'];

    /**
     * 无需鉴权的操作列表
     *
     * 用户虽然需要登录，但不必验证具体权限的操作列表。
     * 通常用于首页仪表盘、公告页等不敏感模块。
     *
     * 示例：
     * - dashboard：系统仪表盘页面
     *
     * @var string[]
     */
    protected array $noNeedAuth = ['logout'];

    /**
     * 通用验证方法
     *
     * @param string $ruleClass 类名（可传简写：User 或完整命名空间）
     * @param array  $data      待验证的数据
     * @param string $scene     场景名称（可选）
     *
     * @return void
     */
    protected function validateWith(string $ruleClass, array $data, string $scene = ''): void
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

        /** @var BaseValidator $validator */
        $validator->validate($data);
    }

    /**
     * 设置语言
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    #[Route(path: "/setLang", methods: ['POST'])]
    public function setLang(Request $request): Response
    {
        $postData = $request->post();
        $lang = $postData['lang'] ?? '';
        $check = normalizeLang($lang);
        if (!$check['supported']) {
            return result(400, 'Unsupported language');
        }
        session()->set('lang', $check['lang']);
        return result(200, 'Language set successfully', ['lang' => $check['lang']]);
    }

    /**
     * @param Request $request
     * @param string  $type
     *
     * @return Response
     * @throws Exception
     */
    #[Route(path: "/captcha", methods: ['GET'])]
    public function captcha(Request $request, string $type = 'captcha'): Response
    {
        $builder = new PhraseBuilder(4, 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ');
        $captcha = new CaptchaBuilder(null, $builder);
        $captcha->build(142, 37);
        $request->session()->set($type, strtolower($captcha->getPhrase()));
        $img_content = $captcha->get();
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

}