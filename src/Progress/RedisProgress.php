<?php

namespace Tequilarapido\Consolify\Progress;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Helper;

class RedisProgress implements Progress
{
    /** @var ProgressBar */
    protected $bar;

    /** @var string */
    protected $uid;

    /** @var string|null */
    protected $sleepModeState;

    public function setProgressBar(ProgressBar $bar)
    {
        $this->bar = $bar;

        return $this;
    }

    public function setUid($uid)
    {
        $this->uid = $uid;

        return $uid;
    }

    public function advance($step = 1)
    {
        $this->bar->advance($step);
        $this->persistProgress();
    }

    public function start($max = null)
    {
        $this->bar->start($max);
        $this->persistProgress();
    }

    public function setSleepModeState($on, $message, $remaining)
    {
        $this->sleepModeState = $on ? $message : null;

        // Progress bar is not advanced here as we are sleep
        // so we need to persist progress manually, so the UI can keep up with sleep timing.
        $this->persistProgress();
    }

    public function summary()
    {
        return [
            'elapsed' => Helper::formatTime($this->elapsed()),
            'estimated' => Helper::formatTime($this->estimated()),
            'memory' => Helper::formatMemory(memory_get_usage(true)),
            'current' => $this->bar->getProgress(),
            'max' => $this->bar->getMaxSteps(),
            'percent' => $this->percent(),
            'message' => $this->bar->getMessage(),
            'sleepMode' => $this->sleepModeState
        ];
    }

    public static function getPersisted($uid)
    {
        $result = app('redis')->connection()->get(static::prefixedKey($uid));

        return $result ? json_decode($result) : null;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->bar, $name], $arguments);
    }

    protected function persistProgress()
    {
        app('redis')->connection()->set(
            static::prefixedKey($this->uid),
            json_encode($this->summary())
        );
    }

    protected static function prefixedKey($key)
    {
        return config('consolify.progress.redis_prefix') . $key;
    }

    protected function elapsed()
    {
        return time() - $this->bar->getStartTime();
    }

    protected function estimated()
    {
        if (!$this->bar->getMaxSteps()) {
            return null;
        }

        return !$this->bar->getProgress()
            ? 0
            : round((time() - $this->bar->getStartTime()) / $this->bar->getProgress() * $this->bar->getMaxSteps());
    }

    protected function percent()
    {
        return floor($this->bar->getProgressPercent() * 100);
    }

}