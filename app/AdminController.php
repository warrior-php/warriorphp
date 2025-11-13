<?php

namespace App;

use App\Core\Route;
use Exception;
use support\Request;
use support\Response;
use Webman\Captcha\CaptchaBuilder;
use Webman\Captcha\PhraseBuilder;

class AdminController extends BaseController
{
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