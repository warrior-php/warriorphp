<?php
declare(strict_types=1);

namespace extend\Utils;

// TODO 时间函数未完成
class Date
{
    /**
     * 返回今日开始和结束的时间戳
     *
     * @return array
     */
    public static function today(): array
    {
        $y = (int)date('Y');
        $m = (int)date('m');
        $d = (int)date('d');
        return [
            mktime(0, 0, 0, $m, $d, $y),
            mktime(23, 59, 59, $m, $d, $y)
        ];
    }

    /**
     * 返回昨日开始和结束的时间戳
     * mktime 自动处理跨月
     * @return array
     */
    public static function yesterday(): array
    {
        $y = (int)date('Y');
        $m = (int)date('m');
        $d = (int)date('d') - 1;

        return [
            mktime(0, 0, 0, $m, $d, $y),
            mktime(23, 59, 59, $m, $d, $y),
        ];
    }

    /**
     * 返回本周开始和结束的时间戳
     * 支持指定任何日期所在周
     *
     * @param null $time
     *
     * @return array
     */
    public static function week($time = null): array
    {
        $time = $time ?: time();
        $start = strtotime('monday this week', $time);
        $end = strtotime('sunday this week 23:59:59', $time);

        return [$start, $end];
    }
}