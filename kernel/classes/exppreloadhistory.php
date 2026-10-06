<?php
/**
 * File containing the expPreloadHistory class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The cache preloader's runs: one status file per run, <id>.json, next to its events (<id>.jsonl) in
 * var/<var dir>/preload.
 *
 * A run writes its own status as it goes (state, counts, the address it is on) and once more when it ends
 * (duration, failures with their addresses and status codes, image aliases made). Setup > Preload reads them for
 * the progress of the run going on and for the list of the last runs; the preload command writes them as well,
 * so a run from the shell or from cron is listed with the others.
 *
 * Whether a run that says it is running still is, is worked out when it is read: a status file cannot say that its
 * process died. A run whose process is gone is reported as stopped without finishing.
 */
class expPreloadHistory
{
    const ID_PATTERN = '#^[a-f0-9]{16}$#';

    /** How many runs are kept. */
    const KEEP = 20;

    /** How many failures a status keeps; the events file has all of them. */
    const MAX_FAILURES = 50;

    /** Seconds a run may take to start (to write its first status) before it counts as not started. */
    const START_GRACE = 60;

    private $dir;

    /**
     * @param string $dir
     */
    public function __construct( $dir )
    {
        $this->dir = rtrim( (string)$dir, '/' );
    }

    public function directory()
    {
        return $this->dir;
    }

    public static function isID( $id )
    {
        return is_string( $id ) && preg_match( self::ID_PATTERN, $id ) === 1;
    }

    public static function newID()
    {
        return bin2hex( random_bytes( 8 ) );
    }

    public function statusFile( $id )
    {
        return $this->dir . '/' . $id . '.json';
    }

    public function eventsFile( $id )
    {
        return $this->dir . '/' . $id . '.jsonl';
    }

    /**
     * Where a run started in the background writes its errors until it can write its own status; absolute, as the
     * run is handed it before it knows its directory.
     */
    public function errorFile( $id )
    {
        $dir = $this->dir[0] === '/' ? $this->dir : getcwd() . '/' . $this->dir;
        return $dir . '/' . $id . '.err';
    }

    /**
     * The end of a run's error file, '' when there is none.
     *
     * @return string
     */
    public function errors( $id )
    {
        $file = $this->errorFile( $id );
        if ( !self::isID( $id ) || !is_file( $file ) || !filesize( $file ) )
            return '';
        $text = (string)@file_get_contents( $file, false, null, max( 0, filesize( $file ) - 600 ) );
        return trim( preg_replace( '#\s+#', ' ', strip_tags( $text ) ) );
    }

    public function stopFile( $id )
    {
        return $this->dir . '/' . $id . '.stop';
    }

    /**
     * Records a new run, before it starts.
     *
     * @param string $id
     * @param array $fields siteaccess, base_url, options, source ...
     * @return array|false the status written
     */
    public function create( $id, array $fields )
    {
        if ( !self::isID( $id ) || !$this->ensureDirectory() )
            return false;
        $status = $fields + array(
            'id' => $id,
            'state' => 'starting',
            'source' => 'page',
            'siteaccess' => '',
            'base_url' => '',
            'options' => array(),
            'pid' => 0,
            'started' => microtime( true ),
            'updated' => microtime( true ),
            'finished' => null,
            'seconds' => null,
            'counts' => self::emptyCounts(),
            'aliases' => null,
            'current' => '',
            'failures' => array(),
            'failures_more' => 0,
            'message' => '',
        );
        $status['id'] = $id;
        if ( !is_file( $this->eventsFile( $id ) ) )
            @touch( $this->eventsFile( $id ) );
        return $this->write( $id, $status ) ? $status : false;
    }

    /**
     * Merges $fields into a run's status.
     *
     * @return array|false the new status
     */
    public function update( $id, array $fields )
    {
        $status = $this->load( $id );
        if ( $status === false )
            return false;
        if ( isset( $fields['failures'] ) )
        {
            $all = $fields['failures'];
            $fields['failures'] = array_slice( $all, 0, self::MAX_FAILURES );
            $fields['failures_more'] = max( 0, count( $all ) - self::MAX_FAILURES );
        }
        $status = array_merge( $status, $fields, array( 'updated' => microtime( true ) ) );
        return $this->write( $id, $status ) ? $status : false;
    }

    /**
     * A run's status as written, without the liveness check.
     *
     * @return array|false
     */
    public function load( $id )
    {
        if ( !self::isID( $id ) )
            return false;
        $file = $this->statusFile( $id );
        if ( !is_file( $file ) )
            return $this->fromEvents( $id );
        $status = json_decode( (string)@file_get_contents( $file ), true );
        return is_array( $status ) ? $status : false;
    }

    /**
     * A run's status, with 'running' (bool) and the state corrected for a run whose process is gone.
     *
     * @param string $id
     * @param int|null $now
     * @return array|false
     */
    public function read( $id, $now = null )
    {
        $status = $this->load( $id );
        if ( $status === false )
            return false;
        return $this->settle( $status, $now === null ? time() : $now );
    }

    /**
     * The runs, newest first.
     *
     * @param int $limit
     * @return array of statuses as read()
     */
    public function runs( $limit = self::KEEP )
    {
        $runs = array();
        foreach ( $this->ids() as $id )
        {
            $status = $this->read( $id );
            if ( $status !== false )
                $runs[] = $status;
        }
        usort( $runs, function ( $a, $b ) { return $b['started'] <=> $a['started']; } );
        return array_slice( $runs, 0, max( 0, (int)$limit ) );
    }

    /**
     * The run going on, or false.
     *
     * @return array|false
     */
    public function current()
    {
        foreach ( $this->runs() as $run )
            if ( $run['running'] )
                return $run;
        return false;
    }

    /**
     * Asks a run to end; it does after the request it is making.
     *
     * @return bool
     */
    public function requestStop( $id )
    {
        $status = $this->read( $id );
        if ( $status === false || !$status['running'] )
            return false;
        return @touch( $this->stopFile( $id ) );
    }

    public function stopRequested( $id )
    {
        return self::isID( $id ) && is_file( $this->stopFile( $id ) );
    }

    /**
     * Keeps the newest $keep runs and removes the files of the others. A run going on is never removed.
     *
     * @return int how many runs were removed
     */
    public function prune( $keep = self::KEEP )
    {
        $runs = $this->runs( PHP_INT_MAX );
        $removed = 0;
        foreach ( array_slice( $runs, max( 0, (int)$keep ) ) as $run )
        {
            if ( $run['running'] )
                continue;
            foreach ( array( $this->statusFile( $run['id'] ), $this->eventsFile( $run['id'] ), $this->stopFile( $run['id'] ), $this->errorFile( $run['id'] ) ) as $file )
                if ( is_file( $file ) )
                    @unlink( $file );
            ++$removed;
        }
        return $removed;
    }

    public static function emptyCounts()
    {
        return array( 'fetched' => 0, 'skipped' => 0, 'broken' => 0, 'denied' => 0, 'bytes' => 0, 'images' => 0, 'images_broken' => 0 );
    }

    /**
     * Whether a process is there: /proc on Linux, which answers for a process of another user as well (the web
     * server and the shell may be different users), posix_kill() otherwise.
     *
     * @param int $pid
     * @return bool
     */
    public static function processAlive( $pid )
    {
        $pid = (int)$pid;
        if ( $pid <= 0 )
            return false;
        if ( is_dir( '/proc/self' ) )
            return is_dir( '/proc/' . $pid );
        if ( function_exists( 'posix_kill' ) )
            return posix_kill( $pid, 0 ) || ( function_exists( 'posix_get_last_error' ) && posix_get_last_error() === 1 );
        return true;
    }

    /**
     * Adds 'running' and corrects the state of a run that cannot be running any more.
     */
    private function settle( array $status, $now )
    {
        $status += array( 'state' => 'finished', 'pid' => 0, 'started' => 0, 'updated' => 0, 'message' => '',
                          'counts' => self::emptyCounts(), 'failures' => array(), 'failures_more' => 0, 'aliases' => null,
                          'seconds' => null, 'finished' => null, 'options' => array(), 'siteaccess' => '', 'base_url' => '',
                          'current' => '', 'source' => 'page' );
        $status['counts'] = (array)$status['counts'] + self::emptyCounts();
        $status['running'] = false;
        if ( $status['state'] === 'running' )
        {
            if ( self::processAlive( $status['pid'] ) )
                $status['running'] = true;
            else
            {
                $status['state'] = 'died';
                $errors = isset( $status['id'] ) ? $this->errors( $status['id'] ) : '';
                $status['message'] = 'The preload stopped without finishing: its process is gone.' . ( $errors !== '' ? ' ' . $errors : '' );
            }
        }
        else if ( $status['state'] === 'starting' )
        {
            if ( $now - (float)$status['started'] <= self::START_GRACE )
                $status['running'] = true;
            else
            {
                $status['state'] = 'failed';
                $errors = isset( $status['id'] ) ? $this->errors( $status['id'] ) : '';
                $status['message'] = $errors !== '' ? 'The preload did not start: ' . $errors
                                   : 'The preload did not start. Run it from the shell to see why: php bin/php/preload.php --help';
            }
        }
        if ( $status['seconds'] === null && !$status['running'] && $status['updated'] )
            $status['seconds'] = round( max( 0, (float)$status['updated'] - (float)$status['started'] ), 1 );
        if ( $status['running'] )
            $status['seconds'] = round( max( 0, $now - (float)$status['started'] ), 1 );
        return $status;
    }

    /**
     * A status for a run of an older version that wrote only its events: when it started, how it ended, its counts
     * and broken links.
     *
     * @return array|false
     */
    private function fromEvents( $id )
    {
        $file = $this->eventsFile( $id );
        if ( !is_file( $file ) )
            return false;
        $status = array( 'id' => $id, 'state' => 'died', 'source' => 'page', 'started' => (float)filemtime( $file ),
                         'updated' => (float)filemtime( $file ), 'counts' => self::emptyCounts(), 'failures' => array(),
                         'message' => '', 'base_url' => '' );
        $first = true;
        foreach ( (array)@file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line )
        {
            $event = json_decode( $line, true );
            if ( !is_array( $event ) || !isset( $event['type'] ) )
                continue;
            if ( $first && isset( $event['time'] ) )
                $status['started'] = (float)$event['time'];
            $first = false;
            if ( isset( $event['time'] ) )
                $status['updated'] = (float)$event['time'];
            if ( $event['type'] === 'info' && isset( $event['message'] ) && strpos( $event['message'], 'Base url: ' ) === 0 )
                $status['base_url'] = (string)preg_replace( '#,.*$#', '', substr( $event['message'], 10 ) );
            if ( $event['type'] === 'done' )
            {
                if ( isset( $event['counts'] ) )
                    $status['counts'] = (array)$event['counts'] + self::emptyCounts();
                if ( isset( $event['seconds'] ) )
                    $status['seconds'] = (float)$event['seconds'];
                $status['state'] = isset( $event['counts'] ) ? 'finished' : 'stopped';
            }
            if ( $event['type'] === 'report' && isset( $event['broken'] ) )
                $status['failures'] = self::failuresOf( (array)$event['broken'] );
        }
        return $status;
    }

    /**
     * The failures a status keeps, from the runner's problems (url, status, reason, referrers, more).
     *
     * @param array $problems
     * @return array
     */
    public static function failuresOf( array $problems )
    {
        $failures = array();
        foreach ( $problems as $problem )
        {
            $referrers = isset( $problem['referrers'] ) ? array_values( (array)$problem['referrers'] ) : array();
            $failures[] = array(
                'url' => (string)( isset( $problem['url'] ) ? $problem['url'] : '' ),
                'status' => (int)( isset( $problem['status'] ) ? $problem['status'] : 0 ),
                'reason' => (string)( isset( $problem['reason'] ) ? $problem['reason'] : '' ),
                'referrers' => array_slice( $referrers, 0, 3 ),
                'more' => max( 0, count( $referrers ) - 3 ) + (int)( isset( $problem['more'] ) ? $problem['more'] : 0 ),
            );
        }
        return $failures;
    }

    private function ids()
    {
        $ids = array();
        foreach ( array_merge( glob( $this->dir . '/*.json' ) ?: array(), glob( $this->dir . '/*.jsonl' ) ?: array() ) as $file )
        {
            $id = preg_replace( '#\.jsonl?$#', '', basename( $file ) );
            if ( self::isID( $id ) )
                $ids[$id] = true;
        }
        return array_keys( $ids );
    }

    private function ensureDirectory()
    {
        return is_dir( $this->dir ) || @mkdir( $this->dir, 0775, true ) || is_dir( $this->dir );
    }

    /**
     * Written to a temporary file and renamed, so a reader never sees half a status.
     */
    private function write( $id, array $status )
    {
        $file = $this->statusFile( $id );
        $temp = $file . '.' . getmypid() . '.tmp';
        if ( @file_put_contents( $temp, json_encode( $status, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR ) ) === false )
            return false;
        @chmod( $temp, 0664 );
        if ( !@rename( $temp, $file ) )
        {
            @unlink( $temp );
            return false;
        }
        return true;
    }
}
