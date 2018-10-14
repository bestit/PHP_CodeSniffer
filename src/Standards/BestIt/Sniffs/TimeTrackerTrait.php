<?php

declare(strict_types=1);

namespace BestIt\Sniffs;

use BestIt\CodeSniffer\File;
use function implode;
use function is_array;
use function microtime;

/**
 * Helps you recording the sniff runtime.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs
 */
trait TimeTrackerTrait
{
    /**
     * The starting time of this sniff in seconds.
     *
     * @var float|null
     */
    private $startingTime;

    /**
     * The time in seconds which the sniff took.
     *
     * @var float|null
     */
    private $stoppedTime;

    /**
     * Type-safe getter for the file.
     *
     * @return File
     */
    abstract protected function getFile(): File;

    /**
     * Type-safe getter for the stack position.
     *
     * @return int
     */
    abstract protected function getStackPos(): int;

    /**
     * Records a time interval for the running of this sniff.
     *
     * @return void
     */
    protected function recordTime(): void
    {
        $this->stopTimeTracker();

        $trackedTimes = [
            ['<', 0.0001],
            0.0001,
            0.001,
            0.01,
            0.1,
            0.5,
            1,
            2,
            3,
            4,
            5,
            ['>', 5]
        ];

        $timeCheck = false;

        foreach ($trackedTimes as $compareTime) {
            if (!is_array($compareTime)) {
                $compareTime = ['<=', $compareTime];
            };

            eval('$timeCheck = ' . $this->stoppedTime . ' ' . implode(' ', $compareTime) . ';');

            if ($timeCheck) {
                $this->getFile()->recordMetric(
                    $this->getStackPos(),
                    static::class . ' runtimes in seconds',
                    implode(' ', $compareTime)
                );
                break;
            }
        }
    }

    /**
     * Starts the stop watch for the time tracking.
     *
     * @return void
     */
    protected function startTimeTracker(): void
    {
        $this->startingTime = microtime(true);
    }

    /**
     * Stops the stop watch for the time tracking.
     *
     * @return void
     */
    protected function stopTimeTracker(): void
    {
        $this->stoppedTime = microtime(true) - $this->startingTime;
    }
}
