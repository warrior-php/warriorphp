<?php
declare(strict_types=1);

namespace App\Model;

use DateTimeInterface;
use support\Model;

class BaseModel extends Model
{
    /**
     * 格式化日期
     *
     * @param DateTimeInterface $date
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}