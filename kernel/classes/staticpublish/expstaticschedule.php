<?php
/**
 * File containing the expStaticSchedule class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The schedule of a target (Triggers[]=schedule, Schedule=<cron expression>): when the cronjob part staticpublish
 * builds and publishes it in full. Pure.
 *
 * The expression has the five fields of cron (minute, hour, day of month, month, day of week; Sunday is 0 or 7) with
 * *, lists (1,15), ranges (1-5) and steps (star/15, 0-30/10), or one of @hourly, @daily, @midnight, @weekly,
 * @monthly. As in cron, when both the day of month and the day of week are restricted, either one matches. Times are
 * the server's time zone.
 *
 * The cronjob runs every few minutes, not every minute, so a target is due when the expression matched at some
 * minute after its last scheduled run (and not more than LOOKBACK minutes ago).
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticSchedule
{
    /** How far back a missed time is still caught up, in minutes (one day) */
    const LOOKBACK = 1440;

    const MACROS = array( '@hourly' => '0 * * * *', '@daily' => '0 0 * * *', '@midnight' => '0 0 * * *', '@weekly' => '0 0 * * 0', '@monthly' => '0 0 1 * *' );

    /** The range of each field */
    const RANGES = array( array( 0, 59 ), array( 0, 23 ), array( 1, 31 ), array( 1, 12 ), array( 0, 7 ) );

    /**
     * The parsed expression, or null when it is not one.
     *
     * @param string $expression
     * @return array|null five lists of allowed values (value => true), and dom_any, dow_any
     */
    public static function parse( $expression )
    {
        $expression = trim( (string)$expression );
        if ( isset( self::MACROS[strtolower( $expression )] ) )
            $expression = self::MACROS[strtolower( $expression )];
        $fields = preg_split( '/\s+/', $expression );
        if ( count( $fields ) !== 5 )
            return null;
        $out = array();
        foreach ( $fields as $i => $field )
        {
            $values = self::field( $field, self::RANGES[$i][0], self::RANGES[$i][1] );
            if ( $values === null )
                return null;
            if ( $i === 4 && isset( $values[7] ) )
            {
                $values[0] = true;
                unset( $values[7] );
            }
            $out[$i] = $values;
        }
        $out['dom_any'] = $fields[2] === '*';
        $out['dow_any'] = $fields[4] === '*';
        return $out;
    }

    /** @return array|null the values of one field */
    private static function field( $field, $min, $max )
    {
        $values = array();
        foreach ( explode( ',', $field ) as $part )
        {
            if ( !preg_match( '#^(\*|(\d{1,2})(?:-(\d{1,2}))?)(?:/(\d{1,2}))?$#', $part, $m ) )
                return null;
            $step = isset( $m[4] ) && $m[4] !== '' ? (int)$m[4] : 1;
            if ( $m[1] === '*' )
                list( $from, $to ) = array( $min, $max );
            else
            {
                $from = (int)$m[2];
                $to = isset( $m[3] ) && $m[3] !== '' ? (int)$m[3] : ( $step > 1 ? $max : $from );
            }
            if ( $step < 1 || $from < $min || $to > $max || $from > $to )
                return null;
            for ( $v = $from; $v <= $to; $v += $step )
                $values[$v] = true;
        }
        return $values;
    }

    /** Whether an expression is valid. */
    public static function valid( $expression )
    {
        return self::parse( $expression ) !== null;
    }

    /**
     * Whether a parsed expression matches a minute.
     *
     * @param array $parsed
     * @param int $time unix time
     * @return bool
     */
    public static function matches( array $parsed, $time )
    {
        list( $minute, $hour, $dom, $month, $dow ) = array_map( 'intval', explode( ' ', date( 'i G j n w', (int)$time ) ) );
        if ( !isset( $parsed[0][$minute], $parsed[1][$hour], $parsed[3][$month] ) )
            return false;
        $domOk = isset( $parsed[2][$dom] );
        $dowOk = isset( $parsed[4][$dow] );
        if ( $parsed['dom_any'] || $parsed['dow_any'] )
            return $domOk && $dowOk;
        return $domOk || $dowOk;
    }

    /**
     * The last minute at or before $now the expression matched, within LOOKBACK.
     *
     * @param string $expression
     * @param int $now
     * @return int|null unix time of the minute
     */
    public static function lastMatch( $expression, $now )
    {
        $parsed = self::parse( $expression );
        if ( $parsed === null )
            return null;
        $minute = (int)floor( (int)$now / 60 ) * 60;
        for ( $i = 0; $i <= self::LOOKBACK; $i++, $minute -= 60 )
            if ( self::matches( $parsed, $minute ) )
                return $minute;
        return null;
    }

    /**
     * Whether a target is due: its expression matched after its last scheduled run.
     *
     * @param string $expression
     * @param int $lastRun unix time of the last scheduled run (0: never)
     * @param int|null $now
     * @return bool
     */
    public static function due( $expression, $lastRun, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $match = self::lastMatch( $expression, $now );
        return $match !== null && $match > (int)$lastRun;
    }
}
