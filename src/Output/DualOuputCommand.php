<?php

namespace Tequilarapido\Consolify\Output;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

abstract class DualOuputCommand extends Command
{
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->beforeRun($input);

        // If we run the manually with -vvv option
        // we will execute the command without exception handling
        // to be able to debug as usual.
        if ($this->isVerbose($input)) {
            return $this->effectiveRun($input, $output);
        }

        try {
            return $this->effectiveRun($input, $output);
        } catch (Exception $e) {
            $this->handeException($e);
        }
    }

    /**
     * Override this method to be able to do some wide system setup
     * before the command runs.
     *
     *  ie. Disable query loging for performance response (DB::disableQueryLog())
     * @param InputInterface $input
     * @return
     */
    abstract protected function beforeRun(InputInterface $input);

    /**
     * Must return the full path for the output file where the console
     * output will be streamed.
     *
     * @param InputInterface $input
     * @return mixed
     */
    abstract protected function outputFilePath(InputInterface $input);

    /**
     * Run the command
     *
     * @param $input
     * @param $output
     * @return int
     */
    protected function effectiveRun($input, $output)
    {
        $outputFile = $this->outputFilePath($input);

        $dualOutput = (new DualOutput(fopen($outputFile, 'a', false)))
            ->setFile($outputFile)
            ->setConsoleOutput($output);

        return parent::run(
            $this->input = $input, $this->output = $dualOutput
        );
    }

    /**
     * Handles exceptions that occurs while running the command.
     *
     * This give us a way to customize the actions that must be taken when something
     * went sideways. (ie. flag something as failed in database).
     *
     * @param Exception $e
     */
    protected function handeException(Exception $e)
    {
        $this->error(TraceReserved::FAILED . " {$e->getMessage()}");

        app()->log->error($e);
    }


    /**
     * For some operations we need to get the value of an argument passed to the command.
     * The issue here is that parent::__constructor() is not called yet, so the symfony/console
     * has not parsed yet the arguments, so we need to to hack it manually.
     *
     * @param $input
     * @param $index
     *
     * @return string
     */
    protected function getNotParsedArgument(InputInterface $input, $index)
    {
        return explode(' ', (string)$input)[$index];
    }

    /**
     * Do `-vvv` was used when calling the command
     *
     * @param InputInterface $input
     * @return bool
     */
    protected function isVerbose(InputInterface $input)
    {
        return (bool)substr_count((string)$input, '-vvv');
    }
}