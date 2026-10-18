<?php
/**
 * The health of the logs (Setup > Log files > Health, setup/logshealth; doc/features/6.0/log-viewer.md, "Health"):
 * the disk the logs and their archives use against a budget ([HealthSettings] DiskBudget), the fastest growing logs,
 * the logs over their size limit, the files the web server's user cannot write (a log written by root, from a
 * command line or Exponential Velocity, that PHP-FPM can no longer append to), the newest error of each log, and the
 * effective rotation settings with where each one is set.
 *
 * summary() is pure over the manager's logs() (expLogManagerInterface, see expLogManagerFactory); newestError()
 * reads at most the last TAIL_BYTES of a file once, without a lock.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogHealth
{
    /** The disk budget of all logs and archives when [HealthSettings] DiskBudget is not set: 256 MB */
    const DEFAULT_BUDGET = 268435456;

    /** The share of the budget from which the page warns */
    const WARN_SHARE = 0.8;

    /** How many logs the "fastest growing" list shows */
    const FASTEST = 5;

    /** The bytes at the end of a file read for its newest error */
    const TAIL_BYTES = 65536;

    /**
     * The disk budget of a configuration, in bytes (0: none).
     *
     * @param expLogViewerConfig $config
     * @return int
     */
    public static function budget( expLogViewerConfig $config )
    {
        $value = $config->get( 'HealthSettings', 'DiskBudget' );
        if ( $value === null || $value === '' )
            return self::DEFAULT_BUDGET;
        return is_numeric( $value ) ? max( 0, (int)$value ) : self::DEFAULT_BUDGET;
    }

    /**
     * The overview of the logs. Pure.
     *
     * @param array $logs key => log, as expLogManagerInterface::logs() gives them
     * @param int $budget bytes, 0 for none
     * @return array total (bytes of the logs and their archives), logs_bytes, archive_bytes, budget, share (0..n, or
     *               null without a budget), state (ok | warn | bad), fastest (up to FASTEST logs with a growth, the
     *               fastest first), over_limit (logs larger than their limits.max_size), not_writable (logs the web
     *               server's user cannot write), owners (the distinct owners), mixed_owners (bool), rows (every log with
     *               total, over_limit, share_of_limit); owners are "owner:owner_group" (the file owner and its group)
     */
    public static function summary( array $logs, $budget )
    {
        $budget = max( 0, (int)$budget );
        $rows = array();
        $logsBytes = 0;
        $archiveBytes = 0;
        $owners = array();
        foreach ( $logs as $key => $log )
        {
            $size = isset( $log['size'] ) ? (int)$log['size'] : 0;
            $archives = isset( $log['archive_bytes'] ) ? (int)$log['archive_bytes'] : self::archiveBytes( $log );
            $max = isset( $log['limits']['max_size'] ) ? (int)$log['limits']['max_size'] : 0;
            $logsBytes += $size;
            $archiveBytes += $archives;
            // the file's owner and its group ("owner_group"; "group" is the viewer's group: kernel, site ...)
            if ( isset( $log['owner'] ) && $log['owner'] !== '' && $log['owner'] !== null )
                $owners[(string)$log['owner'] . ( isset( $log['owner_group'] ) && $log['owner_group'] !== '' && $log['owner_group'] !== null ? ':' . $log['owner_group'] : '' )] = true;
            $row = $log;
            $row['key'] = isset( $log['key'] ) ? (string)$log['key'] : (string)$key;
            $row['archive_bytes'] = $archives;
            $row['total'] = $size + $archives;
            $row['over_limit'] = $max > 0 && $size > $max;
            $row['share_of_limit'] = $max > 0 ? round( $size / $max, 3 ) : null;
            $row['growth'] = isset( $log['growth'] ) && $log['growth'] !== null ? (float)$log['growth'] : null;
            $row['writable'] = !isset( $log['writable'] ) || (bool)$log['writable'];
            $rows[$row['key']] = $row;
        }
        $total = $logsBytes + $archiveBytes;
        $share = $budget > 0 ? round( $total / $budget, 3 ) : null;

        $fastest = array_values( array_filter( $rows, function ( $r ) { return $r['growth'] !== null && $r['growth'] > 0; } ) );
        usort( $fastest, function ( $a, $b ) {
            return $a['growth'] === $b['growth'] ? strcmp( $a['key'], $b['key'] ) : ( $a['growth'] > $b['growth'] ? -1 : 1 );
        } );
        $fastest = array_slice( $fastest, 0, self::FASTEST );
        $overLimit = array_values( array_filter( $rows, function ( $r ) { return $r['over_limit']; } ) );
        usort( $overLimit, function ( $a, $b ) {
            return $a['share_of_limit'] === $b['share_of_limit'] ? strcmp( $a['key'], $b['key'] ) : ( $a['share_of_limit'] > $b['share_of_limit'] ? -1 : 1 );
        } );
        $notWritable = array_values( array_filter( $rows, function ( $r ) { return !$r['writable']; } ) );

        $state = 'ok';
        if ( $overLimit || $notWritable || ( $share !== null && $share >= self::WARN_SHARE ) )
            $state = 'warn';
        if ( $share !== null && $share >= 1 )
            $state = 'bad';

        ksort( $owners );
        return array( 'total' => $total, 'logs_bytes' => $logsBytes, 'archive_bytes' => $archiveBytes, 'budget' => $budget,
                      'share' => $share, 'percent' => $share !== null ? (int)min( 999, round( $share * 100 ) ) : null,
                      'state' => $state, 'fastest' => $fastest, 'over_limit' => $overLimit, 'not_writable' => $notWritable,
                      'owners' => array_keys( $owners ), 'mixed_owners' => count( $owners ) > 1, 'rows' => $rows );
    }

    /**
     * The bytes of a log's archives, when the manager did not add them up.
     *
     * @param array $log
     * @return int
     */
    public static function archiveBytes( array $log )
    {
        $sum = 0;
        if ( isset( $log['archives'] ) && is_array( $log['archives'] ) )
            foreach ( $log['archives'] as $a )
                $sum += isset( $a['size'] ) ? (int)$a['size'] : 0;
        return $sum;
    }

    /**
     * The newest error of a file: the last entry of level error in its last TAIL_BYTES (one stat, one read, no lock).
     *
     * @param string $path
     * @param string $fileLevel the level of the file's lines that name none (expLogFileLocator::fileLevel())
     * @return array|null the entry as expLogParser gives it (offset, time, time_text, level, source, message ...)
     */
    public static function newestError( $path, $fileLevel = '' )
    {
        clearstatcache( true, $path );
        $size = @filesize( $path );
        if ( $size === false || $size === 0 )
            return null;
        $handle = @fopen( $path, 'rb' );
        if ( $handle === false )
            return null;
        $start = max( 0, $size - self::TAIL_BYTES );
        if ( $start > 0 )
            fseek( $handle, $start );
        $data = (string)fread( $handle, self::TAIL_BYTES );
        fclose( $handle );
        if ( $start > 0 )
        {
            $nl = strpos( $data, "\n" );
            if ( $nl === false )
                return null;
            $data = substr( $data, $nl + 1 );
            $start += $nl + 1;
        }
        $found = null;
        foreach ( expLogParser::parse( $data, $start, $fileLevel ) as $entry )
            if ( !$entry['cont'] && $entry['level'] === 'error' )
                $found = $entry;
        return $found;
    }

    /**
     * The effective rotation settings of every log, for the read-only table: max size, archives kept, compression and
     * age, each with the file, section and variable it comes from (from the manager's effectiveSettings(), else the
     * log's limits). Pure but for the manager call.
     *
     * @param array $logs key => log (logs())
     * @param object|null $manager an expLogManagerInterface, or null
     * @return array key => list of {name, value, file, section, var}
     */
    public static function settings( array $logs, $manager = null )
    {
        $names = array( 'max_size', 'keep', 'compress', 'max_age_days' );
        $out = array();
        foreach ( $logs as $key => $log )
        {
            $limits = isset( $log['limits'] ) && is_array( $log['limits'] ) ? $log['limits'] : array();
            if ( $manager !== null && method_exists( $manager, 'effectiveSettings' ) )
            {
                try
                {
                    // (string): a key of digits only (404) is an integer as the array key
                    $effective = $manager->effectiveSettings( (string)$key );
                    if ( is_array( $effective ) && $effective )
                        $limits = $effective + $limits;
                }
                catch ( Exception $e )
                {
                }
            }
            $source = isset( $limits['source'] ) && is_array( $limits['source'] ) ? $limits['source'] : array();
            $list = array();
            foreach ( $names as $name )
            {
                $from = isset( $source[$name] ) && is_array( $source[$name] ) ? $source[$name] : array();
                $list[] = array( 'name' => $name, 'value' => isset( $limits[$name] ) ? $limits[$name] : null,
                                 'file' => isset( $from['file'] ) ? (string)$from['file'] : '',
                                 'section' => isset( $from['section'] ) ? (string)$from['section'] : '',
                                 'var' => isset( $from['var'] ) ? (string)$from['var'] : '' );
            }
            $out[$key] = $list;
        }
        return $out;
    }
}
