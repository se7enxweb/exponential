<?php
/**
 * The records of one request (or one stretch of a command), per channel, until they are flushed
 * (doc/bc/6.0/audit.md, "Buffering and flushing (F3)").
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditBuffer
{
    /** @var array channel => record[] */
    protected $records = array();

    /** @var int */
    protected $count = 0;

    /** @var int Approximate size in bytes */
    protected $bytes = 0;

    /**
     * @param string $channel
     * @param array $record
     */
    public function add( $channel, array $record )
    {
        $this->records[$channel][] = $record;
        $this->count++;
        $json = json_encode( $record, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR );
        $this->bytes += $json === false ? 512 : strlen( $json ) + 120;
    }

    /** @return bool */
    public function isEmpty()
    {
        return $this->count === 0;
    }

    /** @return int */
    public function count()
    {
        return $this->count;
    }

    /** @return int */
    public function bytes()
    {
        return $this->bytes;
    }

    /**
     * @param int $maxEvents
     * @param int $maxBytes
     * @return bool The buffer has reached a limit
     */
    public function isFull( $maxEvents, $maxBytes )
    {
        return $this->count >= $maxEvents || $this->bytes >= $maxBytes;
    }

    /**
     * Every record, emptying the buffer.
     *
     * @return array channel => record[]
     */
    public function take()
    {
        $records = $this->records;
        $this->records = array();
        $this->count = 0;
        $this->bytes = 0;
        return $records;
    }

    /**
     * Puts records back (a flush that failed), before anything added since.
     *
     * @param array $records channel => record[]
     */
    public function restore( array $records )
    {
        foreach ( $records as $channel => $list )
        {
            $this->records[$channel] = array_merge( $list, isset( $this->records[$channel] ) ? $this->records[$channel] : array() );
            $this->count += count( $list );
            $this->bytes += 600 * count( $list );
        }
    }
}
