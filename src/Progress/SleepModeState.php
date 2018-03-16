<?php


namespace Tequilarapido\Consolify\Progress;

use Illuminate\Contracts\Support\Arrayable;

class SleepModeState implements Arrayable
{
    /**
     * Is sleep mode active ?
     *
     * @var bool
     */
    private $on = false;

    /**
     * Message describing the sleep mode state
     *
     * @var string
     */
    private $message = null;

    /**
     * How much remaining seconds before sleep mode goes off ?
     *
     * @var int
     */
    private $remaining = 0;

    /**
     * SleepModeState constructor.
     *
     * @param $on
     * @param $message
     * @param int $remaining
     */
    public function __construct($on, $message, $remaining = 0)
    {
        $this->on = $on;
        $this->message = $message;
        $this->remaining = $remaining;
    }

    /**
     * Alias : Static constructor for entering sleep mode.
     *
     * @return static
     */
    public static function entering()
    {
        return new static(true, 'Entering sleep mode');
    }

    /**
     * Alias : Static constructor for updating remaining time
     *
     * @return static
     */
    public static function remaining($remaining)
    {
        return new static(
            true,
            'Remaining : ' . static::formatRemainingSeconds($remaining),
            $remaining
        );
    }

    /**
     * Alias : Static constructor for leaving sleep mode.
     *
     * @return static
     */
    public static function leaving()
    {
        return new static(false, 'leaving sleep mode');
    }

    /**
     * Return message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Return remaining
     *
     * @return int
     */
    public function getRemaining()
    {
        return $this->remaining;
    }

    /**
     * Is on ?
     *
     * @return bool
     */
    public function isOn()
    {
        return $this->on;
    }

    /**
     * Return array representing the sleep mode state.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'on' => $this->on,
            'message' => $this->message,
            'remaining' => $this->remaining
        ];
    }

    /**
     * Format seconds.
     *
     * @param $seconds
     * @return string
     */
    private static function formatRemainingSeconds($seconds)
    {
        return gmdate('i', $seconds) . 'm, ' . gmdate('s', $seconds) . 's';
    }

}