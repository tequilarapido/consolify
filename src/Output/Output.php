<?php

namespace Tequilarapido\Consolify\Output;

use Tequilarapido\Consolify\Progress\Progress;
use Tequilarapido\Consolify\Progress\ProgressHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console trait helpers.
 *
 * Brings the console methods to any service worker class.
 */
trait Output
{
    /**
     * Output interface.
     *
     * @var OutputInterface
     */
    protected $output;

    /**
     * Progress handler.
     *
     * @var Progress
     */
    protected $progress;

    /**
     * Set output interface.
     *
     * @param OutputInterface $output
     *
     * @return $this
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    public function createProgress($max, $uid, $advance = 0)
    {
        $this->progress = ProgressHelper::newProgressInstance($max, $uid, $this->output);

        $this->progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% -- %message%');
        $this->progress->setRedrawFrequency(1);
        $this->progress->setMessage('');
        $this->progress->start();
        $this->progress->advance($advance);
    }

    public function advanceProgress($step = 1)
    {
        if (! $this->progress) {
            throw new \Exception('No progress was created.');
        }

        $this->progress->advance($step);
    }

    public function setProgressMessage($message)
    {
        $this->progress->setMessage($message);
    }

    public function finishProgress()
    {
        $this->progress->finish();
    }

    /**
     * Write a string as information output.
     *
     * @param string $string
     */
    public function info($string)
    {
        if (! $this->output) {
            return;
        }

        $this->output->writeln("<info>$string</info>");
    }

    /**
     * Write a string as standard output.
     *
     * @param string $string
     */
    public function line($string)
    {
        if (! $this->output) {
            return;
        }

        $this->output->writeln($string);
    }

    /**
     * Write a string as comment output.
     *
     * @param string $string
     */
    public function comment($string)
    {
        if (! $this->output) {
            return;
        }

        $this->output->writeln("<comment>$string</comment>");
    }

    /**
     * Write a string as error output.
     *
     * @param string $string
     */
    public function error($string)
    {
        if (! $this->output) {
            return;
        }

        $this->output->writeln("<error>$string</error>");
    }

    /**
     * Write a string prefixed by a check mark.
     *
     * @param   $string
     */
    public function done($string)
    {
        if (! $this->output) {
            return;
        }

        $this->output->writeln("\n\n<info>✔</info> $string");
    }

    /**
     * Simple table.
     *
     * @param $headers
     * @param $rows
     */
    public function table($headers, $rows)
    {
        $this->output->table($headers, $rows);
    }

    /**
     * Overwrite previous line.
     *
     * @param $string
     */
    public function overwrite($string)
    {
        $this->overwriteLine(1);
        $this->line($string);
    }

    /**
     * Overwrite line (or lines).
     *
     * @param int $numberLines
     */
    public function overwriteLine($numberLines = 1)
    {
        $this->output->write(sprintf("\033[%dA", $numberLines));
    }

    protected function sleepFor($seconds, $cycle = 1)
    {
        $this->line($state = 'Enterting sleep mode');
        $this->setSleepModeStateInProgressBar($state);

        $remaining = $seconds;
        while ($remaining > $cycle) {
            $remaining -= $cycle;

            $this->setSleepModeStateInProgressBar($state = 'Remaining : '.$this->formatSeconds($remaining));
            $this->line($state);

            sleep($cycle);
        }

        $this->line('Leaving sleep mode');
        $this->setSleepModeStateInProgressBar(null);
    }

    /**
     * Format seconds.
     *
     * @param $seconds
     *
     * @return string
     */
    protected function formatRemainingSeconds($seconds)
    {
        return gmdate('i', $seconds).'m, '.gmdate('s', $seconds).'s';
    }

    /**
     * Set sleep mode state in progress.
     *
     * @param $on
     * @param string $message
     * @param null   $remaining
     */
    protected function setSleepModeStateInProgressBar($on, $message = '', $remaining = null)
    {
        $this->progress->setSleepModeState($on, $message, $remaining);
    }

    /** @return Progress */
    protected function createProgressInstance($max, $uid)
    {
        if (! class_exists($progressClass = config('consolify.progress.concrete_class'))) {
            throw new \LogicException("Cannot find progress class [$progressClass]");
        }

        return new $progressClass($this->output->createProgressBar($max), $uid);
    }
}
