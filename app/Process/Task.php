<?php
declare(strict_types=1);

namespace App\Process;

use Workerman\Crontab\Crontab;

class Task
{
    /**
     * 注意：定时任务不会马上执行，所有定时任务进入下一分钟才会开始计时执行
     * crontab并不是异步的，例如一个task进程里设置了A和B两个定时器，都是每秒执行一次任务，但是A任务耗时10秒，那么B需要等待A执行完才能被执行，导致B执行会有延迟。
     * 如果业务对于时间间隔很敏感，需要将敏感的定时任务放到单独的进程去运行，防止被其它定时任务影响。
     * @return void
     */
    public function onWorkerStart(): void
    {
        // 每秒钟执行一次
        new Crontab('*/1 * * * * *', function () {
            dump(date('Y-m-d H:i:s'));
        });

        // 每5秒执行一次
        new Crontab('*/5 * * * * *', function () {
            dump(date('Y-m-d H:i:s'));
        });

        // 每分钟执行一次
        new Crontab('0 */1 * * * *', function () {
            dump(date('Y-m-d H:i:s'));
        });

        // 每5分钟执行一次
        new Crontab('0 */5 * * * *', function () {
            dump(date('Y-m-d H:i:s'));
        });

        // 每分钟的第一秒执行
        new Crontab('1 * * * * *', function () {
            dump(date('Y-m-d H:i:s'));
        });

        // 每天的7点50执行，注意这里省略了秒位
        new Crontab('50 7 * * *', function () {
            dump(date('Y-m-d H:i:s'));
        });

    }
}