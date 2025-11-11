<?php

namespace App\Controller;

use App\Route;
use support\Response;

class Icon
{
    /**
     * 图标预览页
     *
     * @return Response
     */
    #[Route(path: "/icon", methods: ['GET'])]
    public function index(): Response
    {
        // CSS 文件路径（相对项目根目录）
        $cssFile = public_path('assets/css/styles.css');

        if (!file_exists($cssFile)) {
            return response('找不到 CSS 文件: ' . $cssFile, 404);
        }

        $css = file_get_contents($cssFile);

        // 正则匹配 .ti-xxxx:before
        preg_match_all('/\.ti-([a-z0-9\-]+):before\s*\{/', $css, $matches);

        $icons = $matches[1] ?? [];
        $count = count($icons);

        // 拼接 HTML
        $html = <<<HTML
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>Tabler Icons 预览</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <style>
        body { font-family: sans-serif; background: #fafafa; padding: 20px; }
        h1 { font-size: 20px; margin-bottom: 15px; }
        #search { padding: 8px 12px; width: 300px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #ccc; }
        .icon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
        }
        .icon-item {
            text-align: center;
            background: #fff;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: 0.2s;
        }
        .icon-item:hover { transform: scale(1.05); }
        .icon-item i {
            font-size: 28px;
            display: block;
            margin-bottom: 8px;
            color: #333;
        }
        .icon-name {
            font-size: 12px;
            color: #666;
            word-break: break-all;
        }
    </style>
</head>
<body>
<h1>Tabler Icons 预览（共 $count 个）</h1>
<input type="text" id="search" placeholder="搜索图标...">
<div class="icon-grid">
HTML;

        foreach ($icons as $icon => $v) {
            $class = "ti ti-$v";
            $html .= "<div class='icon-item'><i class='$class'></i><div class='icon-name'>$class</div></div>\n";
        }

        $html .= <<<HTML
</div>
<script>
const input = document.getElementById('search');
input.addEventListener('input', function() {
  const query = this.value.toLowerCase();
  document.querySelectorAll('.icon-item').forEach(item => {
    const name = item.querySelector('.icon-name').textContent.toLowerCase();
    item.style.display = name.includes(query) ? '' : 'none';
  });
});
</script>
</body>
</html>
HTML;
        return new Response(200, ['Content-Type' => 'text/html; charset=utf-8'], $html);
    }
}