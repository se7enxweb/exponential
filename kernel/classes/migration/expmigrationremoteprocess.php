<?php
/**
 * File containing the expMigrationRemoteProcess class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Runs the operations of expMigrationRemoteTransport for many remotes at once: the operations of one remote one after
 * the other (the first failure ends that remote's list), the remotes in parallel (at most $parallel at a time), each
 * remote with a time limit. No shell (proc_open() with an array); stdout and stderr are kept apart.
 *
 * A test replaces the runner with setRunner( callable( array $jobs, $parallel, $timeout ) ): it gets the same jobs and
 * returns the same results.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRemoteProcess
{
    /** @var callable|null */
    protected static $runner = null;

    /** @param callable|null $runner */
    public static function setRunner( $runner )
    {
        self::$runner = $runner;
    }

    /**
     * @param array $jobs key => array of operations (expMigrationRemoteTransport)
     * @param int $parallel
     * @param int $timeout seconds per key
     * @return array key => array( exit (int, -1: timed out or not started), stdout (of the last operation), stderr (all),
     *               duration_ms, done (how many operations ended with 0) )
     */
    public static function runAll( array $jobs, $parallel = 4, $timeout = 900 )
    {
        if ( self::$runner !== null )
            return call_user_func( self::$runner, $jobs, $parallel, $timeout );
        $results = array();
        $queue = array_keys( $jobs );
        $running = array();
        $parallel = max( 1, (int)$parallel );
        while ( $queue || $running )
        {
            while ( $queue && count( $running ) < $parallel )
            {
                $key = array_shift( $queue );
                $results[$key] = array( 'exit' => 0, 'stdout' => '', 'stderr' => '', 'duration_ms' => 0, 'done' => 0 );
                $running[$key] = array( 'ops' => array_values( $jobs[$key] ), 'index' => -1, 'proc' => null, 'pipes' => array(),
                                        'started' => microtime( true ) );
                if ( !self::next( $key, $running, $results ) )
                {
                    // nothing to wait for: only copies, or the first operation could not start
                    $results[$key]['duration_ms'] = (int)round( ( microtime( true ) - $running[$key]['started'] ) * 1000 );
                    unset( $running[$key] );
                }
            }
            if ( !$running )
                break;
            $read = array();
            foreach ( $running as $key => $state )
                foreach ( array( 1, 2 ) as $n )
                    if ( isset( $state['pipes'][$n] ) && is_resource( $state['pipes'][$n] ) && !feof( $state['pipes'][$n] ) )
                        $read[] = $state['pipes'][$n];
            if ( $read )
            {
                $write = $except = null;
                @stream_select( $read, $write, $except, 0, 200000 );
            }
            else
                usleep( 50000 );
            foreach ( array_keys( $running ) as $key )
            {
                $state =& $running[$key];
                foreach ( array( 1 => 'stdout', 2 => 'stderr' ) as $n => $name )
                    if ( isset( $state['pipes'][$n] ) && is_resource( $state['pipes'][$n] ) )
                    {
                        $chunk = stream_get_contents( $state['pipes'][$n] );
                        if ( $chunk !== false && $chunk !== '' )
                            $results[$key][$name] .= $chunk;
                    }
                $status = proc_get_status( $state['proc'] );
                $over = microtime( true ) - $state['started'] > $timeout;
                if ( $status['running'] && $over )
                {
                    proc_terminate( $state['proc'], 9 );
                    $results[$key]['stderr'] .= "\ntimed out after {$timeout} s";
                    $results[$key]['exit'] = -1;
                    self::close( $state );
                    $results[$key]['duration_ms'] = (int)round( ( microtime( true ) - $state['started'] ) * 1000 );
                    unset( $running[$key] );
                    continue;
                }
                if ( !$status['running'] )
                {
                    foreach ( array( 1 => 'stdout', 2 => 'stderr' ) as $n => $name )
                        if ( isset( $state['pipes'][$n] ) && is_resource( $state['pipes'][$n] ) )
                            $results[$key][$name] .= (string)stream_get_contents( $state['pipes'][$n] );
                    self::close( $state );
                    $results[$key]['exit'] = (int)$status['exitcode'];
                    if ( $results[$key]['exit'] === 0 )
                        $results[$key]['done']++;
                    if ( $results[$key]['exit'] !== 0 || !self::next( $key, $running, $results ) )
                    {
                        $results[$key]['duration_ms'] = (int)round( ( microtime( true ) - $state['started'] ) * 1000 );
                        unset( $running[$key] );
                    }
                }
                unset( $state );
            }
        }
        return $results;
    }

    /**
     * Starts the next operation of a key (copies are done at once).
     *
     * @return bool false when there is none left or one failed
     */
    protected static function next( $key, array &$running, array &$results )
    {
        $state =& $running[$key];
        while ( ++$state['index'] < count( $state['ops'] ) )
        {
            $op = $state['ops'][$state['index']];
            if ( isset( $op['copy'] ) )
            {
                foreach ( $op['copy'] as $from => $to )
                {
                    if ( !is_dir( dirname( $to ) ) )
                        @mkdir( dirname( $to ), eZDir::dirMode( 0775 ), true );
                    if ( !@copy( $from, $to ) )
                    {
                        $results[$key]['stderr'] .= "could not copy to $to\n";
                        $results[$key]['exit'] = 1;
                        return false;
                    }
                }
                $results[$key]['done']++;
                continue;
            }
            $results[$key]['stdout'] = '';
            if ( isset( $op['http'] ) )
            {
                // signed now, so that a request tried again carries a fresh signature (the trigger takes a nonce once);
                // the signature and the body go to curl on its standard input, never on its command line
                $op = array( 'argv' => array( 'curl', '-sS', '--connect-timeout', '20', '-K', '-' ), 'env' => array(), 'cwd' => null,
                             'stdin' => expMigrationTrigger::curlConfig( $op['http']['url'], $op['http']['key'], $op['http']['body'] ) );
            }
            $env = !empty( $op['env'] ) ? array_merge( self::environment(), $op['env'] ) : null;
            $spec = array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) );
            $proc = @proc_open( $op['argv'], $spec, $pipes, isset( $op['cwd'] ) ? $op['cwd'] : null, $env );
            if ( !is_resource( $proc ) )
            {
                $results[$key]['stderr'] .= $op['argv'][0] . " could not be started\n";
                $results[$key]['exit'] = -1;
                return false;
            }
            if ( isset( $op['stdin'] ) && $op['stdin'] !== null )
                fwrite( $pipes[0], (string)$op['stdin'] );
            fclose( $pipes[0] );
            stream_set_blocking( $pipes[1], false );
            stream_set_blocking( $pipes[2], false );
            $state['proc'] = $proc;
            $state['pipes'] = array( 1 => $pipes[1], 2 => $pipes[2] );
            return true;
        }
        return false;
    }

    /** Closes the pipes and the process of a key. */
    protected static function close( array &$state )
    {
        foreach ( $state['pipes'] as $pipe )
            if ( is_resource( $pipe ) )
                fclose( $pipe );
        $state['pipes'] = array();
        if ( is_resource( $state['proc'] ) )
            proc_close( $state['proc'] );
        $state['proc'] = null;
    }

    /** @return array the environment of this process, for an operation that adds variables */
    protected static function environment()
    {
        $env = getenv();
        return is_array( $env ) ? $env : array();
    }
}
