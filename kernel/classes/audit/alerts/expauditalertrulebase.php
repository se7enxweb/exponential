<?php
/**
 * Shared code of the built-in alert rule classes: the record fields a rule groups by, the time of a record, and
 * the de-duplication (doc/bc/6.0/audit.md, "Alerts (F5)"): an alert for (rule, group) fires once per window;
 * while it is open, more matches update its count instead of firing again; it fires again when the count doubles,
 * or in a new window after the window closed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

abstract class expAuditAlertRuleBase implements expAuditAlertRule
{
    /**
     * Whether an open alert fires again when its count doubles (threshold rules); false: once per group and window
     * (schedule rules: a night of settings writes is one alert).
     *
     * @return bool
     */
    protected function refiresWhenDoubled()
    {
        return true;
    }

    /** @return array The rule class's defaults for the [AlertRule_*] block */
    public function defaults()
    {
        return array( 'Threshold' => '1', 'Window' => '0', 'GroupBy' => 'id', 'CountChildren' => 'disabled' );
    }

    /**
     * A record field by its dotted path (actor.ip, object.id, after.legacy.Role ID); null when absent.
     *
     * @param array $record
     * @param string $path
     * @return mixed
     */
    public static function field( array $record, $path )
    {
        $v = $record;
        foreach ( explode( '.', (string)$path ) as $part )
        {
            if ( !is_array( $v ) || !array_key_exists( $part, $v ) )
                return null;
            $v = $v[$part];
        }
        return $v;
    }

    /** @return string The group key of a record ('-' when the field is absent) */
    public static function groupOf( array $record, $groupBy )
    {
        $v = self::field( $record, $groupBy );
        if ( $v === null || $v === '' )
            return '-';
        return is_scalar( $v ) ? (string)$v : md5( json_encode( $v ) );
    }

    /** @return int The record's time in epoch milliseconds */
    public static function timeMs( array $record )
    {
        if ( isset( $record['time'] ) && preg_match( '/^(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2})(?:\.(\d{1,3}))?Z$/', $record['time'], $m ) )
            return strtotime( $m[1] . 'Z' ) * 1000 + ( isset( $m[2] ) ? (int)str_pad( $m[2], 3, '0' ) : 0 );
        return (int)( microtime( true ) * 1000 );
    }

    /**
     * Counts one event into its group and decides whether the alert fires.
     *
     * @param expAuditAlertState $state
     * @param string $group
     * @param array $record
     * @param int $weight
     * @param int $threshold
     * @param int $windowMs 0: every new event id counts once, ever (match rules)
     * @return array|null the alert, or null
     */
    protected function count( $state, $group, array $record, $weight, $threshold, $windowMs )
    {
        $id = isset( $record['id'] ) ? (string)$record['id'] : md5( json_encode( $record ) );
        $t = self::timeMs( $record );
        $g =& $state->group( $group );
        if ( isset( $g['events'][$id] ) )
            return null; // seen before (flush, then the cronjob part)
        if ( $windowMs > 0 )
        {
            foreach ( $g['events'] as $eid => $e )
                if ( $e[0] < $t - $windowMs )
                    unset( $g['events'][$eid] );
            // the window of the last firing has closed: a new one starts
            if ( $g['fired_count'] && $t - $g['fired_ms'] > $windowMs )
                $g['fired_count'] = 0;
        }
        $g['events'][$id] = array( $t, (int)$weight );
        if ( $windowMs <= 0 )
        {
            // remembered for a day so a record seen twice fires once; the count is the record's own weight
            $count = (int)$weight;
            if ( $count < $threshold )
                return null;
            $g['fired_count'] = $count;
            $g['fired_ms'] = $t;
            $g['first'] = $g['last'] = $id;
            return array( 'group' => $group, 'count' => $count, 'events' => array( $id ), 'first' => $id, 'last' => $id, 'time_ms' => $t );
        }
        $count = 0;
        $first = null;
        $firstT = PHP_INT_MAX;
        foreach ( $g['events'] as $eid => $e )
        {
            $count += $e[1];
            if ( $e[0] < $firstT )
            {
                $firstT = $e[0];
                $first = $eid;
            }
        }
        if ( $count < $threshold )
            return null;
        if ( $g['fired_count'] && ( !$this->refiresWhenDoubled() || $count < 2 * $g['fired_count'] ) )
        {
            // open: update, do not fire again
            $g['last'] = $id;
            return null;
        }
        $g['fired_count'] = $count;
        $g['fired_ms'] = $t;
        $g['first'] = $first;
        $g['last'] = $id;
        return array( 'group' => $group, 'count' => $count, 'events' => array( $first, $id ), 'first' => $first, 'last' => $id, 'time_ms' => $t );
    }
}
