<?php

namespace Tequilarapido\Consolify\Output;

use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Output\ConsoleOutput;

class DualOutput extends StreamOutput
{
    /** @var string */
    protected $file;

    /** @var ConsoleOutput */
    protected $consoleOuput;

    /**
     * {@inheritdoc}
     */
    protected function doWrite($message, $newline)
    {
        parent::doWrite($message, $newline);

        $this->consoleOuput->write($message, $newline, true);
    }

    /**
     * Set console output from artisan console command.
     *
     * @param $consoleOutput
     *
     * @return $this
     */
    public function setConsoleOutput($consoleOutput)
    {
        $this->consoleOuput = $consoleOutput;

        return $this;
    }

    /**
     * Set the path to the file where the console output will be streamed.
     *
     * @param $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }
}
