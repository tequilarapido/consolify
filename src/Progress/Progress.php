<?php

namespace Tequilarapido\Consolify\Progress;

use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Interface Progress.
 *
 * Wrapper for symfony console original progress bar. Add a way to persist
 * progress bar information in a store (ie. Redis).
 */
interface Progress
{
    /**
     * Set the original symfony console bar.
     *
     * @param ProgressBar $bar
     *
     * @return mixed
     */
    public function setProgressBar(ProgressBar $bar);

    /**
     * Set uid. Used as identifier for persisting progress in redis for instance.
     *
     * @param string $uid
     *
     * @return mixed
     */
    public function setUid($uid);

    /**
     * Advance progress bar.
     *
     * @param int $step
     *
     * @return mixed
     */
    public function advance($step = 1);

    /**
     * Starts progress bar.
     *
     * @param null $max
     *
     * @return mixed
     */
    public function start($max = null);

    /**
     * Set sleep mode state.
     *
     * @param SleepModeState $sleepModeState
     * 
     * @return $this
     */
    public function setSleepModeState(SleepModeState $sleepModeState);

    /**
     * Returns progress informations as array.
     * (what will be stored in Redis for instance).
     *
     * @return array
     */
    public function summary();

    /**
     * Return persisted progress information from store.
     *
     * @return
     */
    public function getPersisted();

    /**
     * Delete persisted progress information from store.
     */
    public function deletePersisted();

    /**
     * Forward calls to original progress bar.
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments);
}
