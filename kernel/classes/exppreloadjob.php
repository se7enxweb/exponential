<?php
/**
 * File containing the expPreloadJob class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and others. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A cache preload run: started in the background from Setup > Preload, or in the foreground from the shell
 * (bin/php/preload.php), with one lock, one status file and one list of runs for both.
 *
 * The page used to hold one request open for the whole crawl and read its
 * progress as it was streamed. A web server that answers from a pool of
 * workers hands a response over only when it is complete and ends a request
 * after its time limit (Exponential Velocity answered 504 after 30 seconds),
 * and a proxy may hold streamed output back: the Start button then did
 * nothing. The crawl runs in bin/php/preloadjob.php, started detached; the page
 * reads the run's status (expPreloadHistory) and its events
 * (var/<var dir>/preload/<id>.jsonl).
 *
 * execute() is the run itself, the same for both: it takes the lock (expPreloadLock, one run at a time), writes
 * the events and the status as it goes, counts the image aliases the run made, and records how it ended, whether
 * it finished, was stopped or failed.
 */
class expPreloadJob
{
    const ID_PATTERN = '#^[a-f0-9]{16}$#';

    /** The limits a run may be given, and the defaults of the page. */
    const MAX_PAGES_LIMIT = 5000;
    const MAX_DEPTH_LIMIT = 10;
    const DEFAULT_MAX_PAGES = 250;
    const DEFAULT_MAX_DEPTH = 3;

    public static function directory()
    {
        return eZSys::varDirectory() . '/preload';
    }

    public static function history()
    {
        return new expPreloadHistory( self::directory() );
    }

    public static function lock()
    {
        return new expPreloadLock( self::directory() . '/run.lock' );
    }

    public static function isID( $id )
    {
        return expPreloadHistory::isID( $id );
    }

    /**
     * The limits and choices of a run, checked: whole numbers inside the limits, images as a boolean.
     *
     * @param array $options max_pages, max_depth, images
     * @return array
     */
    public static function options( array $options )
    {
        $pages = isset( $options['max_pages'] ) && $options['max_pages'] !== '' && $options['max_pages'] !== null
               ? (int)$options['max_pages'] : self::DEFAULT_MAX_PAGES;
        $depth = isset( $options['max_depth'] ) && $options['max_depth'] !== '' && $options['max_depth'] !== null
               ? (int)$options['max_depth'] : self::DEFAULT_MAX_DEPTH;
        return array(
            'max_pages' => max( 1, min( self::MAX_PAGES_LIMIT, $pages ) ),
            'max_depth' => max( 0, min( self::MAX_DEPTH_LIMIT, $depth ) ),
            'images' => !empty( $options['images'] ),
        );
    }

    /**
     * The siteaccesses a preload may be asked for: those of RelatedSiteAccessList and AvailableSiteAccessList.
     * Anything else from a form or a command line is refused before it reaches one.
     *
     * @return array
     */
    public static function knownSiteaccesses()
    {
        $ini = eZINI::instance( 'site.ini' );
        $names = array();
        foreach ( array( 'RelatedSiteAccessList', 'AvailableSiteAccessList' ) as $list )
            if ( $ini->hasVariable( 'SiteAccessSettings', $list ) )
                foreach ( (array)$ini->variable( 'SiteAccessSettings', $list ) as $name )
                    if ( preg_match( '#^[A-Za-z0-9_-]+$#', (string)$name ) )
                        $names[(string)$name] = true;
        return array_keys( $names );
    }

    /**
     * The sites the page offers: RelatedSiteAccessList without the siteaccess the page is served from (an
     * administration siteaccess warms nothing a visitor sees), each with its address and whether the siteaccess
     * matching sends that address to it.
     *
     * @param string $current
     * @return array of hash name, url, prefix, reached
     */
    public static function targets( $current = '' )
    {
        $ini = eZINI::instance( 'site.ini' );
        $related = $ini->hasVariable( 'SiteAccessSettings', 'RelatedSiteAccessList' )
                 ? (array)$ini->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' ) : array();
        $targets = array();
        foreach ( $related as $name )
        {
            $name = (string)$name;
            if ( $name === '' || $name === $current || !preg_match( '#^[A-Za-z0-9_-]+$#', $name ) )
                continue;
            $plan = self::plan( $name, array() );
            if ( $plan['base_url'] !== '' )
                $targets[] = array( 'name' => $name, 'url' => $plan['start_urls'][0], 'prefix' => $plan['prefix'],
                                    'reached' => $plan['reached'], 'base_url' => $plan['base_url'] );
        }
        return $targets;
    }

    /**
     * What a run would do, without requesting anything: the address, the starting pages, the limits, and the
     * command that does the same from the shell. The dry run of the page and of the command.
     *
     * @param string $siteaccess
     * @param array $options as options()
     * @return array hash siteaccess, base_url, prefix, reached, start_urls, options, command
     */
    public static function plan( $siteaccess, array $options )
    {
        $options = self::options( $options );
        $runner = new expPreloadRunner( function () {}, array( 'siteaccess' => (string)$siteaccess ) );
        $base = $runner->baseUrl();
        $address = $base === false ? array( 'prefix' => '', 'reached' => false, 'how' => 'guess' ) : $runner->address();
        return array(
            'siteaccess' => (string)$siteaccess,
            'base_url' => $base === false ? '' : $base,
            'prefix' => $address['prefix'],
            'reached' => $address['reached'],
            'start_urls' => $base === false ? array() : $runner->startUrls( $base ),
            'options' => $options,
            'command' => self::commandLine( $siteaccess, $options ),
        );
    }

    /**
     * The shell command that runs the same preload, for the page to show.
     *
     * @return string
     */
    public static function commandLine( $siteaccess, array $options )
    {
        $options = self::options( $options );
        $parts = array( 'php bin/php/preload.php' );
        if ( preg_match( '#^[A-Za-z0-9_-]+$#', (string)$siteaccess ) )
            $parts[] = '--siteaccess=' . $siteaccess;
        // the limits always: the command's default page limit (1000) is not the page's
        $parts[] = '--max-pages=' . $options['max_pages'];
        $parts[] = '--max-depth=' . $options['max_depth'];
        if ( $options['images'] )
            $parts[] = '--images';
        return implode( ' ', $parts );
    }

    /**
     * Whether a preload is going on now, from the page or from the shell.
     *
     * @return array|false the running run's status
     */
    public static function running()
    {
        $current = self::history()->current();
        if ( $current !== false )
            return $current;
        // a run that holds the lock and wrote no status yet
        if ( self::lock()->isLocked() )
            return array( 'id' => '', 'state' => 'running', 'running' => true, 'siteaccess' => '', 'base_url' => '',
                          'counts' => expPreloadHistory::emptyCounts(), 'started' => time(), 'seconds' => 0,
                          'source' => 'shell', 'options' => array(), 'current' => '' );
        return false;
    }

    /**
     * Starts a run in the background. Returns its id, or false with $error set.
     *
     * @param string $siteaccess one of knownSiteaccesses(), or '' for the default
     * @param int $maxPages
     * @param int $maxDepth
     * @param string $error
     * @param array $more images
     * @return string|false
     */
    public static function start( $siteaccess, $maxPages, $maxDepth, &$error, array $more = array() )
    {
        $siteaccess = (string)$siteaccess;
        if ( $siteaccess !== '' && !in_array( $siteaccess, self::knownSiteaccesses(), true ) )
        {
            $error = 'Unknown siteaccess.';
            return false;
        }
        $options = self::options( array( 'max_pages' => $maxPages, 'max_depth' => $maxDepth ) + $more );

        $running = self::running();
        if ( $running !== false )
        {
            $error = 'A preload is already running' . ( $running['siteaccess'] !== '' ? ' (' . $running['siteaccess'] . ')' : '' )
                   . '. Wait for it to end or stop it first.';
            return false;
        }

        $history = self::history();
        $history->prune( expPreloadHistory::KEEP - 1 );
        $id = expPreloadHistory::newID();
        $plan = self::plan( $siteaccess, $options );
        if ( $history->create( $id, array( 'source' => 'page', 'siteaccess' => $siteaccess, 'base_url' => $plan['start_urls'] ? $plan['start_urls'][0] : '',
                                           'options' => $options ) ) === false )
        {
            $error = 'Cannot write to ' . self::directory() . '.';
            return false;
        }

        $php = self::phpBinary();
        $setsid = class_exists( 'expProcessTools' ) ? expProcessTools::setsid()
            : ( is_executable( '/usr/bin/setsid' ) ? '/usr/bin/setsid' : ( is_executable( '/bin/setsid' ) ? '/bin/setsid' : false ) );
        if ( !$php || !$setsid || !function_exists( 'proc_open' ) )
        {
            $error = class_exists( 'expProcessTools' ) && expProcessTools::error() !== ''
                ? 'The preloader cannot start: ' . expProcessTools::error()
                : 'The preloader needs proc_open, setsid and the PHP command line.';
            $history->update( $id, array( 'state' => 'failed', 'message' => $error, 'finished' => microtime( true ) ) );
            return false;
        }
        // An argument list, not a shell line: nothing typed reaches a shell. The values are checked above.
        $command = array( $setsid, '-f', $php, 'bin/php/preloadjob.php', '--id=' . $id,
                          '--max-pages=' . $options['max_pages'], '--max-depth=' . $options['max_depth'] );
        if ( $siteaccess !== '' )
            $command[] = '--target=' . $siteaccess;
        if ( $options['images'] )
            $command[] = '--images';
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
            $command[] = '--allow-root-user';

        // Only pipes go to the child: under a server that serves files
        // through its own stream wrapper a file descriptor cannot be handed
        // on, and the server's own sockets must not be inherited by a run
        // that outlives the request.
        $spec = array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) );
        $engine = class_exists( 'Q_WebServer_Shell_Exec', false );
        if ( $engine )
            $spec = Q_WebServer_Shell_Exec::descriptors( $spec );
        $env = getenv();
        $env = is_array( $env ) ? $env : array();
        $env['EXP_PRELOAD_DETACHED'] = '1';
        // where the run's errors go until it can write its own status (bin/php/preloadjob.php)
        $env['EXP_PRELOAD_ERRORS'] = $history->errorFile( $id );
        if ( empty( $env['PATH'] ) )
            $env['PATH'] = '/usr/local/bin:/usr/bin:/bin';
        $process = @proc_open( $command, $spec, $pipes, eZSys::rootDir(), $env );
        if ( !is_resource( $process ) )
        {
            $error = 'The preloader could not be started.';
            $history->update( $id, array( 'state' => 'failed', 'message' => $error, 'finished' => microtime( true ) ) );
            return false;
        }
        if ( $engine )
            Q_WebServer_Shell_Exec::closeExtra( $pipes );
        foreach ( $pipes as $pipe )
            fclose( $pipe );
        proc_close( $process );
        return $id;
    }

    /**
     * Runs one preload in this process: the body of bin/php/preloadjob.php (in the background, for the page) and
     * of bin/php/preload.php (in the foreground, from the shell).
     *
     * @param string $id a new id, or the one the page recorded
     * @param string $siteaccess
     * @param array $options as options()
     * @param callable|null $echo also handed every event, as ( $type, $message, $data ), for a terminal
     * @param string $source page or shell
     * @return int exit code: 0 finished or stopped, 1 failed, 2 another run holds the lock
     */
    public static function execute( $id, $siteaccess, array $options, $echo = null, $source = 'shell' )
    {
        $history = self::history();
        $options = self::options( $options );

        // The lock first: a run refused because another holds it leaves no record behind, unless the page made one
        // for it already, which then says why it did not start.
        $lock = self::lock();
        if ( !$lock->acquire() )
        {
            $message = 'Another preload is running; this one did not start.';
            if ( $history->load( $id ) !== false )
            {
                file_put_contents( $history->eventsFile( $id ), json_encode( array( 'type' => 'error', 'message' => $message, 'time' => time() ) ) . "\n"
                                   . json_encode( array( 'type' => 'end', 'message' => '', 'time' => time() ) ) . "\n", FILE_APPEND );
                $history->update( $id, array( 'state' => 'failed', 'message' => $message, 'finished' => microtime( true ) ) );
            }
            return 2;
        }

        $plan = self::plan( $siteaccess, $options );
        if ( $history->load( $id ) === false )
        {
            $history->prune( expPreloadHistory::KEEP - 1 );
            $history->create( $id, array( 'source' => $source, 'siteaccess' => (string)$siteaccess,
                                          'base_url' => $plan['start_urls'] ? $plan['start_urls'][0] : '', 'options' => $options ) );
        }

        $handle = @fopen( $history->eventsFile( $id ), 'a' );
        $write = function ( $type, $message, array $data = array() ) use ( &$handle, $echo )
        {
            if ( $handle )
            {
                fwrite( $handle, json_encode( array( 'type' => $type, 'message' => $message, 'time' => time() ) + $data,
                                              JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR ) . "\n" );
                fflush( $handle );
            }
            if ( $echo && $type !== 'end' )
                call_user_func( $echo, $type, $message, $data );
        };

        $runner = null;
        $finished = false;
        $lastUpdate = 0;
        $send = function ( $type, $message, array $data = array() ) use ( $write, $history, $id, &$runner, &$lastUpdate )
        {
            $write( $type, $message, $data );
            $now = microtime( true );
            if ( $runner && ( $now - $lastUpdate >= 1 || $type === 'phase' ) )
            {
                $lastUpdate = $now;
                $fields = array( 'counts' => $runner->counts() );
                if ( isset( $data['url'] ) )
                    $fields['current'] = (string)$data['url'];
                $history->update( $id, $fields );
            }
            if ( $type !== 'done' && $type !== 'report' && $history->stopRequested( $id ) )
                throw new RuntimeException( 'stopped from the administration' );
        };

        // A fatal error ends the process without a catch: the run is still recorded as failed, with the error.
        register_shutdown_function( function () use ( &$finished, $write, $history, $id, $lock, &$handle )
        {
            if ( $finished )
                return;
            $last = error_get_last();
            $message = 'The preload stopped with an error' . ( $last ? ': ' . $last['message'] . ' in ' . $last['file'] . ':' . $last['line'] : '.' );
            $write( 'error', $message );
            $write( 'done', 'Stopped.' );
            $write( 'end', '' );
            $history->update( $id, array( 'state' => 'failed', 'message' => $message, 'finished' => microtime( true ) ) );
            $lock->release();
        } );

        $aliasesBefore = self::imageFileCount();
        $history->update( $id, array( 'state' => 'running', 'pid' => getmypid(), 'started' => microtime( true ) ) );
        $state = 'finished';
        $message = '';
        try
        {
            $write( 'info', 'Preloader started at ' . date( 'Y-m-d H:i:s T' ) . '.' );
            $runner = new expPreloadRunner( $send, array(
                'siteaccess' => (string)$siteaccess,
                'max_pages'  => $options['max_pages'],
                'max_depth'  => $options['max_depth'],
                'images'     => $options['images'],
            ) );
            if ( $runner->baseUrl() === false )
                $state = 'failed';
            $runner->run();
        }
        catch ( Throwable $e )
        {
            $stopped = $history->stopRequested( $id );
            $state = $stopped ? 'stopped' : 'failed';
            $message = $stopped ? 'Stopped from the administration.' : 'The preload stopped with an error: ' . $e->getMessage();
            $write( $stopped ? 'warn' : 'error', 'Preloader stopped: ' . $e->getMessage() );
            $write( 'done', 'Stopped early.', $runner ? array( 'counts' => $runner->counts() ) : array() );
        }

        $aliasesAfter = self::imageFileCount();
        $status = $history->load( $id );
        $history->update( $id, array(
            'state' => $state,
            'message' => $message,
            'finished' => microtime( true ),
            'seconds' => round( microtime( true ) - (float)( $status ? $status['started'] : microtime( true ) ), 1 ),
            'counts' => $runner ? $runner->counts() : expPreloadHistory::emptyCounts(),
            'failures' => $runner ? expPreloadHistory::failuresOf( $runner->problems() ) : array(),
            'aliases' => $aliasesBefore === null || $aliasesAfter === null ? null : max( 0, $aliasesAfter - $aliasesBefore ),
            'current' => '',
        ) );
        $write( 'end', '' );
        $finished = true;
        if ( $handle )
            fclose( $handle );
        if ( is_file( $history->stopFile( $id ) ) )
            @unlink( $history->stopFile( $id ) );
        $lock->release();
        return $state === 'failed' ? 1 : 0;
    }

    /**
     * The events after line $offset, whether the run has ended, and its status.
     *
     * @return array|false hash events (list), offset (next line), done (bool), status
     */
    public static function progress( $id, $offset )
    {
        $history = self::history();
        if ( !self::isID( $id ) )
            return false;
        $status = $history->read( $id );
        $file = $history->eventsFile( $id );
        if ( $status === false || !is_file( $file ) )
            return false;
        $lines = file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
        $events = array();
        $done = false;
        foreach ( array_slice( $lines, max( 0, (int)$offset ) ) as $line )
        {
            $event = json_decode( $line, true );
            if ( !is_array( $event ) || !isset( $event['type'] ) )
                continue;
            if ( $event['type'] === 'end' )
            {
                $done = true;
                continue;
            }
            $events[] = $event;
        }
        // A run whose process is gone (or that never started) has ended, whether or not it said so.
        if ( !$done && !$status['running'] )
        {
            $events[] = array( 'type' => 'error', 'message' => $status['message'] !== '' ? $status['message'] : 'The preloader stopped without finishing.' );
            $events[] = array( 'type' => 'done', 'message' => 'Stopped.' );
            $done = true;
        }
        return array( 'events' => $events, 'offset' => count( $lines ), 'done' => $done, 'status' => self::publicStatus( $status ) );
    }

    /**
     * Asks a run to stop; it ends after the page it is fetching.
     */
    public static function stop( $id )
    {
        return self::isID( $id ) && self::history()->requestStop( $id );
    }

    /**
     * A status as the page's script gets it: what it shows, nothing of the process.
     *
     * @return array
     */
    public static function publicStatus( array $status )
    {
        $options = (array)( isset( $status['options'] ) ? $status['options'] : array() );
        return array(
            'id' => (string)$status['id'],
            'state' => (string)$status['state'],
            'running' => !empty( $status['running'] ),
            'source' => (string)$status['source'],
            'siteaccess' => (string)$status['siteaccess'],
            'base_url' => (string)$status['base_url'],
            'started' => (int)$status['started'],
            'seconds' => $status['seconds'] === null ? null : (float)$status['seconds'],
            'counts' => (array)$status['counts'],
            'max_pages' => isset( $options['max_pages'] ) ? (int)$options['max_pages'] : 0,
            'images' => !empty( $options['images'] ),
            'aliases' => $status['aliases'] === null ? null : (int)$status['aliases'],
            'current' => (string)$status['current'],
            'failures' => (array)$status['failures'],
            'failures_more' => isset( $status['failures_more'] ) ? (int)$status['failures_more'] : 0,
            'message' => (string)$status['message'],
        );
    }

    /**
     * How many image files the storage holds: the run counts them before and after, and the difference is the
     * image aliases the pages it requested made. Null where the files are not on this disk (a clustered file
     * handler) and cannot be counted.
     *
     * @return int|null
     */
    public static function imageFileCount()
    {
        $handler = eZINI::instance( 'file.ini' )->variable( 'ClusteringSettings', 'FileHandler' );
        if ( !in_array( $handler, array( 'eZFSFileHandler', 'eZFS2FileHandler' ), true ) )
            return null;
        $dir = eZSys::storageDirectory() . '/images';
        if ( !is_dir( $dir ) )
            return 0;
        $count = 0;
        try
        {
            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $files as $file )
                if ( $file->isFile() )
                    ++$count;
        }
        catch ( Exception $e )
        {
            return null;
        }
        return $count;
    }

    /**
     * Which server a run's requests reach, for the page to say: the site's own address, and whether Exponential
     * Velocity serves this installation too, with the seconds its response cache keeps a page.
     *
     * @return array hash velocity (bool), velocity_ttl (int|null), velocity_port (string|null: 'http / https')
     */
    public static function servers()
    {
        $info = array( 'velocity' => false, 'velocity_ttl' => null, 'velocity_port' => null );
        if ( !class_exists( 'expVelocity' ) || !file_exists( 'settings/velocity.ini' ) )
            return $info;
        try
        {
            $velocity = expVelocity::create();
            $info['velocity'] = (bool)$velocity->isRunning();
            // both: HTTPS may be set up in /etc/vc rather than in velocity.ini
            $info['velocity_port'] = $velocity->httpPort() . ' / ' . $velocity->configuredHttpsPort();
            $ini = eZINI::instance( 'velocity.ini' );
            if ( $ini->hasVariable( 'CacheSettings', 'Enabled' ) && $ini->variable( 'CacheSettings', 'Enabled' ) === 'enabled' )
                $info['velocity_ttl'] = $ini->hasVariable( 'CacheSettings', 'DefaultTtl' ) ? (int)$ini->variable( 'CacheSettings', 'DefaultTtl' ) : null;
        }
        catch ( Exception $e )
        {
        }
        return $info;
    }

    /**
     * The PHP command line: PHP_BINARY on the command line, the php next to
     * the running PHP (PHP_BINDIR) under PHP-FPM.
     */
    private static function phpBinary()
    {
        if ( class_exists( 'expProcessTools' ) )
            return expProcessTools::phpCli();
        if ( PHP_SAPI === 'cli' && PHP_BINARY !== '' )
            return PHP_BINARY;
        foreach ( array( PHP_BINDIR . '/php', '/usr/local/bin/php', '/usr/bin/php' ) as $file )
            if ( is_executable( $file ) )
                return $file;
        return false;
    }
}
