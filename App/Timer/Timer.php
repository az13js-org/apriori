<?php
namespace Timer;

/**
 * 性能计算用时间对象
 *
 * 使用方法:
 *
 * $start = new Timer();
 * // 在这里放一些需要被计算执行时间的代码
 * $end = new Timer();
 *
 * // 计算$end减去$start后的秒数
 * echo $end->sub($start);
 *
 * @author MengShaoying <mengshaoying@aliyun.com>
 */
class Timer
{
    /** @var string 用来获取当前时间的函数名称，例如hrtime或者microtime，自动的 */
    private $timeType;
    /** @var int 当前时间的秒数的整数部分 */
    private $timeSecond;
    /** @var float [0, 1) 当前时间的秒数的小数部分 */
    private $timeFloat;

    public function __construct()
    {
        if (version_compare(phpversion(), '7.3.0', '>=')) {
            $this->timeType = 'hrtime';
            $timeData = hrtime();
            $this->timeSecond = $timeData[0];
            $this->timeFloat = $timeData[1] / 1E+9;
        } else {
            $this->timeType = 'microtime';
            $timeData = explode(' ', microtime());
            $this->timeSecond = intval($timeData[1]);
            $this->timeFloat = floatval($timeData[0]);
        }
    }

    /**
     * 获取时间获得函数的使用情况
     *
     * 目前返回值只有hrtime或者microtime
     *
     * @return string
     */
    public function getTimeType(): string
    {
        return $this->timeType;
    }

    /**
     * 获取时间戳的秒数
     *
     * @return int
     */
    public function second(): int
    {
        return $this->timeSecond;
    }

    /**
     * 获取时间戳的秒数
     *
     * @return float
     */
    public function floatTime(): float
    {
        return $this->timeFloat;
    }

    /**
     * 利用当前时间减去指定的一个时间对象的时间，返回秒数
     *
     * @param Timer $timer
     * @return float
     */
    public function sub(Timer $timer): float
    {
        return $this->second() - $timer->second() + ($this->floatTime() - $timer->floatTime());
    }
}
