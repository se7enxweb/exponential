<?php
/**
 * File containing the expKernelTransferContext class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What a transfer task (expKernelTransferTask) gets from the engine that runs it: the progress it reports (to the
 * engine's job and to the run's history row), a heartbeat for long steps, whether the run was cancelled, a log
 * line. The same calls whatever the engine is; each engine passes its own callables (expKernelTransferRunner).
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelTransferContext
{
    /** @var string */
    protected $engine;

    /** @var int */
    protected $runID;

    /** @var string */
    protected $jobRef;

    /** @var string */
    protected $tool;

    /** @var array name => callable|null: progress (done, total, message), heartbeat (), cancelled () => bool, log (line) */
    protected $hooks;

    /** @var array the last progress: done, total, failed, message */
    protected $last = array( 'done' => 0, 'total' => null, 'failed' => null, 'message' => '' );

    /** @var string[] */
    protected $lines = array();

    /**
     * @param string $engine expqueue, contentjob, process, inline
     * @param string $tool the registry id
     * @param int $runID the history row (0 when there is none)
     * @param string $jobRef the engine's job
     * @param array $hooks progress, heartbeat, cancelled, log (each optional)
     */
    public function __construct( $engine, $tool, $runID = 0, $jobRef = '', array $hooks = array() )
    {
        $this->engine = (string)$engine;
        $this->tool = (string)$tool;
        $this->runID = (int)$runID;
        $this->jobRef = (string)$jobRef;
        $this->hooks = $hooks;
    }

    /** @return string expqueue, contentjob, process or inline */
    public function engine()
    {
        return $this->engine;
    }

    /** @return string the tool's registry id */
    public function tool()
    {
        return $this->tool;
    }

    /** @return int the run's history row (0 when there is none) */
    public function runID()
    {
        return $this->runID;
    }

    /** @return string the engine's job (an expqueue uuid, a content job id) */
    public function jobRef()
    {
        return $this->jobRef;
    }

    /**
     * Reports the progress: to the engine's job, and to the history row (at most every few seconds).
     *
     * @param int $done units done (rows, records, objects)
     * @param int|null $total
     * @param string $message
     * @param int|null $failed units that failed so far
     */
    public function progress( $done, $total = null, $message = '', $failed = null )
    {
        $this->last = array( 'done' => (int)$done, 'total' => $total === null ? null : (int)$total,
                             'failed' => $failed === null ? null : (int)$failed, 'message' => (string)$message );
        if ( isset( $this->hooks['progress'] ) )
            call_user_func( $this->hooks['progress'], (int)$done, $total === null ? null : (int)$total, (string)$message );
        if ( $this->runID > 0 )
            expKernelTransferHistory::progress( $this->runID, (int)$done, $total, $failed );
    }

    /** @return array the last progress reported: done, total, failed, message */
    public function lastProgress()
    {
        return $this->last;
    }

    /** Tells the engine the run is alive (long steps without progress). */
    public function heartbeat()
    {
        if ( isset( $this->hooks['heartbeat'] ) )
            call_user_func( $this->hooks['heartbeat'] );
    }

    /**
     * @return bool whether the run was cancelled meanwhile (asked again on every call)
     * @phpstan-impure
     */
    public function isCancelled()
    {
        return isset( $this->hooks['cancelled'] ) && (bool)call_user_func( $this->hooks['cancelled'] );
    }

    /**
     * @throws expKernelTransferCancelledException when the run was cancelled
     */
    public function throwIfCancelled()
    {
        if ( $this->isCancelled() )
            throw new expKernelTransferCancelledException( 'The run was cancelled.' );
    }

    /**
     * A line for the engine's log.
     *
     * @param string $line
     */
    public function log( $line )
    {
        $this->lines[] = (string)$line;
        if ( count( $this->lines ) > 200 )
            $this->lines = array_slice( $this->lines, -200 );
        if ( isset( $this->hooks['log'] ) )
            call_user_func( $this->hooks['log'], (string)$line );
    }

    /** @return string[] the last log lines */
    public function lines()
    {
        return $this->lines;
    }
}
