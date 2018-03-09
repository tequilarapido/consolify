<?php


namespace Tequilarapido\Consolify\Progress;

use Symfony\Component\Console\Output\OutputInterface;

class ProgressHelper
{
    public static function newProgressInstance($max, $uid, OutputInterface $output)
    {
        if (!class_exists($progressClass = config('consolify.progress.concrete_class'))) {
            throw new \LogicException("Cannot find progress class [$progressClass]");
        }

        $progress = (new $progressClass)
            ->setProgressBar($output->createProgressBar($max))
            ->setUid($uid);

        // Reset progress
        $progress->deletePersisted();

        return $progress;
    }

    /** @return Progress */
    public static function progressFor($uid)
    {
        if (!class_exists($progressClass = config('consolify.progress.concrete_class'))) {
            throw new \LogicException("Cannot find progress class [$progressClass]");
        }

        return (new $progressClass)->setUid($uid);
    }

}