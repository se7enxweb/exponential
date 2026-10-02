<?php
/**
 * The window state of one alert rule (doc/bc/6.0/audit.md, "Alerts (F5)"): <LogDir>/alerts/<rule>.json, read and
 * written under flock() only when a matching record was flushed, so a request without matching events pays
 * nothing.
 *
 * Content: groups => group key => array( events => array( event id => array( ms, weight ) ), fired_count,
 * fired_ms, first, last ). The event ids make evaluation idempotent: the same record seen at flush time and again by
 * the cronjob part counts once.
 *
 * A rule given a state without a file (expAuditAlertState::memory()) works in memory only: exp:audit alerts test
 * --replay runs a rule over past records that way without firing or touching the real state.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditAlertState
{
    /** @var string|null */
    protected $path;

    /** @var resource|null */
    protected $lock = null;

    /** @var array */
    public $data = array( 'groups' => array() );

    /**
     * @param string|null $path null: in memory only
     */
    public function __construct( $path = null )
    {
        $this->path = $path;
    }

    /** @return expAuditAlertState A state that is never stored */
    public static function memory()
    {
        return new self( null );
    }

    /**
     * The state of a rule, opened and locked.
     *
     * @param string $dir the alerts directory
     * @param string $rule
     * @return expAuditAlertState
     */
    public static function open( $dir, $rule )
    {
        $state = new self( rtrim( $dir, '/' ) . '/' . preg_replace( '/[^a-zA-Z0-9_.-]/', '_', $rule ) . '.json' );
        $state->lock();
        $state->load();
        return $state;
    }

    /** @return string|null */
    public function path()
    {
        return $this->path;
    }

    /** Takes the rule's lock (<rule>.json.lock). */
    public function lock()
    {
        if ( $this->path === null || $this->lock )
            return;
        if ( !expAuditWriter::ensureDirectory( dirname( $this->path ) ) )
            return;
        $lockFile = $this->path . '.lock';
        $created = !is_file( $lockFile );
        $this->lock = @fopen( $lockFile, 'c' );
        if ( $this->lock )
        {
            if ( $created )
                expAuditWriter::ownLikeParent( $lockFile, 0640 );
            flock( $this->lock, LOCK_EX );
        }
    }

    /** Reads the file. */
    public function load()
    {
        if ( $this->path === null )
            return;
        $s = @file_get_contents( $this->path );
        $d = $s !== false ? json_decode( $s, true ) : null;
        $this->data = is_array( $d ) ? $d + array( 'groups' => array() ) : array( 'groups' => array() );
    }

    /** Writes the file (atomic) and releases the lock. */
    public function close()
    {
        if ( $this->path !== null )
        {
            $created = !is_file( $this->path );
            $tmp = $this->path . '.' . getmypid() . '.tmp';
            if ( @file_put_contents( $tmp, json_encode( $this->data, JSON_UNESCAPED_SLASHES ) ) !== false )
                @rename( $tmp, $this->path );
            if ( $created )
                expAuditWriter::ownLikeParent( $this->path, 0640 );
        }
        if ( $this->lock )
        {
            flock( $this->lock, LOCK_UN );
            fclose( $this->lock );
            $this->lock = null;
        }
    }

    /**
     * A group's entry, created when missing.
     *
     * @param string $key
     * @return array
     */
    public function &group( $key )
    {
        $key = (string)$key;
        if ( !isset( $this->data['groups'][$key] ) )
            $this->data['groups'][$key] = array( 'events' => array(), 'fired_count' => 0, 'fired_ms' => 0, 'first' => null, 'last' => null );
        return $this->data['groups'][$key];
    }

    /**
     * Drops events older than the window and groups with nothing left that have not fired within $keepMs.
     *
     * @param int $nowMs
     * @param int $windowMs
     * @param int $keepMs
     */
    public function prune( $nowMs, $windowMs, $keepMs = 86400000 )
    {
        foreach ( $this->data['groups'] as $key => &$g )
        {
            foreach ( $g['events'] as $id => $e )
                if ( $e[0] < $nowMs - $windowMs )
                    unset( $g['events'][$id] );
            if ( !$g['events'] && ( !$g['fired_ms'] || $g['fired_ms'] < $nowMs - max( $keepMs, $windowMs ) ) )
                unset( $this->data['groups'][$key] );
        }
        unset( $g );
    }

    /** @return int The number of groups */
    public function count()
    {
        return count( $this->data['groups'] );
    }
}
