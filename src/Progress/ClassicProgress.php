<?php

namespace Tequilarapido\Consolify\Progress;

use Symfony\Component\Console\Helper\ProgressBar;

class ClassicProgress implements Progress
{
    /** @var ProgressBar */
    protected $bar;

    /** @var string */
    protected $uid;

    public function setProgressBar(ProgressBar $bar)
    {
        $this->bar = $bar;

        return $this;
    }

    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    public function advance($step = 1)
    {
        $this->bar->advance($step);
    }

    public function start($max = null)
    {
        $this->bar->start($max);
    }

    public function setSleepModeState($on, $message, $remaining)
    {
        // do nothing
    }

    public function summary()
    {
        return [];
    }

    public function getPersisted()
    {
    }

    public function deletePersisted()
    {
        // do nothing
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->bar, $name], $arguments);
    }
}
