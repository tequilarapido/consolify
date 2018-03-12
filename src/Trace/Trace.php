<?php

namespace Tequilarapido\Consolify\Trace;

class Trace
{
    protected $uid;

    protected $basePath;

    protected $truncateLines = 3;

    public static function fromFile($file)
    {
        $instance = new static();

        return $instance->setUid($instance->fileToUid($file));
    }

    public static function fromUid($uid)
    {
        return (new static())->setUid($uid);
    }

    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;

        return $this;
    }

    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    public function get($from = null, $truncate = true)
    {
        if (!$file = $this->getFile()) {
            throw new TraceException('Cannot find file to trace.');
        }

        return $this->readLast($file, $from, $truncate);
    }

    protected function getFile()
    {
        if (!file_exists($file = $this->uidToFile($this->uid))) {
            return;
        }

        return $file;
    }

    protected function readLast($file, $last_position, $truncate)
    {
        clearstatcache(false, $file);
        $len = filesize($file);

        // First time or file was deleted/reset
        if ($len < $last_position) {
            return [
                'last_position' => 0,
                'operation'     => 'ERASE',
            ];
        }

        // If there is new content
        if ($len > $last_position) {
            $f = fopen($file, 'rb');
            if (!$f) {
                throw new \LogicException("Cannot read file. [$file]");
            }

            $lines = [];
            fseek($f, $last_position);
            while (!feof($f)) {
                $lines = array_merge(
                    $lines,
                    explode("\n", fread($f, 4096))
                );
            }
            $last_position = ftell($f);
            fclose($f);
        } else {
            $lines = [];
        }

        if ($truncate) {
            if (count($lines) > $this->truncateLines) {
                $lines = array_slice($lines, -1 * $this->truncateLines);
                array_unshift($lines, '...');
            }
        }

        return [
            'last_position' => $last_position,
            'operation'     => 'APPEND',
            'lines'         => $this->format($lines),
        ];
    }

    protected function format($lines)
    {
        /*
         * @todo format using an ansi converer to preserve colors ?
         */
        return $lines;
    }

    public function addEraseLine()
    {
        $this->addCommandLine(TraceReserved::ERASE);
    }

    public function addTerminateLine()
    {
        $this->addCommandLine(TraceReserved::COMPLETED);
    }

    public function addCommandLine($command)
    {
        file_put_contents($this->getFile(), "[{$command}]".PHP_EOL, FILE_APPEND);
    }

    /**
     * Converts uid to filepath.
     *
     *      UID     = {SUBFOLDER}_{NAME}
     *          ->
     *      PATH    = {storage_path}/{SUBFOLDER}/{NAME}.log
     *
     * @param $uid
     *
     * @return null|string
     */
    public function uidToFile($uid)
    {
        list($subfolder, $filename) = explode('_', $uid);

        return str_replace('//', '/', "{$this->basePath}/{$subfolder}/{$filename}.log");
    }

    /**
     * Convert full log path to UID.
     *
     *      PATH    = {storage_path}/{SUBFOLDER}/{NAME}.log
     *          ->
     *      UID     = {SUBFOLDER}_{NAME}
     *
     * @param $fullPath
     *
     * @return mixed
     */
    public function fileToUid($fullPath)
    {
        return $this->relativeFileToUid(
            str_replace($this->basePath.'/', '', $fullPath)
        );
    }

    /**
     * Convert relative log path to UID.
     *
     *      PATH    = {SUBFOLDER}/{NAME}.log
     *          ->
     *      UID     = {SUBFOLDER}_{NAME}
     *
     * @param $relativePath
     *
     * @return mixed
     */
    public function relativeFileToUid($relativePath)
    {
        $uid = implode('_', explode('/', trim($relativePath, '/')));

        return str_replace('.log', '', $uid);
    }
}
