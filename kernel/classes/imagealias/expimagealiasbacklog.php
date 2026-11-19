<?php
/**
 * File containing the expImageAliasBacklog class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The image aliases a request answered with the placeholder because every conversion slot was taken, made right after
 * the answer ([AliasSettings] Backlog).
 *
 * A page with hundreds of new images asks for all of them at once. With the backlog a request never waits for a slot:
 * it makes its alias at once when a slot is free, and otherwise answers the placeholder in a few milliseconds and
 * writes the alias here, one small file per alias in <cache dir>/imagealias-backlog/<server name>/. After its answer
 * has gone out (PHP-FPM, LiteSpeed and FrankenPHP finish the request first; Velocity runs it once the answer is sent)
 * a request that gets a free slot makes the aliases of the backlog, oldest first, for at most [AliasSettings]
 * BacklogTime seconds, then gives the slot back; so at most MaxParallel processes work on the backlog of a server at
 * once, and the others are free for the next request at once. The swap script then finds the real images.
 *
 * Every alias is made as a job of the queue makes it (expImageAliasJob::runGenerate(), the image.ini of the siteaccess
 * the page was shown in): the same files and the same database writes as render mode. What no request takes (a
 * server that stops getting requests, a process that died while working) is made by the cronjob imagealiasbacklog.php.
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageAliasBacklog
{
    /** Seconds after which an entry a process took but did not finish is given back (that process died) */
    const STALE_WORK = 300;

    /** @var string|null the backlog directory for this process (tests) */
    protected static $directory = null;

    /** @var bool|null whether this process can answer before it makes the backlog, over the server (tests) */
    protected static $answerFirst = null;

    /** @var callable|null what drain() does with an entry, over work() (tests) */
    protected static $worker = null;

    /** @var bool the drain after the answer is registered for this request */
    protected static $scheduled = false;

    /** @var string the request the drain was registered for (a persistent worker serves many) */
    protected static $scheduledFor = '';

    /** @var string the millisecond of the last entry this process added */
    protected static $sequenceMs = '';

    /** @var array entries to write after the answer (addAfterAnswer()), in the order they were added */
    protected static $pending = array();

    /** @var string|null the request the pending entries belong to (a persistent worker serves many) */
    protected static $pendingFor = null;

    /** @var int entries this process added within that millisecond, less one */
    protected static $sequence = 0;

    /** @var callable|null what startDrainers() runs to start a drainer, over the process (tests) */
    protected static $spawner = null;

    /** Waiting entries per drainer: fewer start no further drainer */
    const DRAINER_ENTRIES = 4;

    /** Seconds a drainer counts as running after it was started, until it holds its lock */
    const DRAINER_START = 10;

    /** Seconds a drainer waits for new entries before it ends */
    const DRAINER_IDLE = 2;

    /**
     * Sets the backlog directory for this process (tests); null: <cache dir>/imagealias-backlog/<server name>.
     *
     * @param string|null $directory
     */
    public static function setDirectory( $directory )
    {
        self::$directory = $directory === null ? null : rtrim( (string)$directory, '/' );
    }

    /**
     * @return string the backlog directory of this server
     */
    public static function directory()
    {
        if ( self::$directory !== null )
            return self::$directory;
        return eZSys::cacheDirectory() . '/imagealias-backlog/' . expImageAliasThrottle::hostKey();
    }

    /**
     * Sets what drain() does with an entry (tests, without a database); null goes back to work().
     *
     * @param callable|null $worker called with the entry
     */
    public static function setWorker( $worker )
    {
        self::$worker = $worker;
    }

    /**
     * Sets for this process whether it can answer first (tests); null goes back to the server's abilities.
     *
     * @param bool|null $answerFirst
     */
    public static function setAnswerFirst( $answerFirst )
    {
        self::$answerFirst = $answerFirst === null ? null : (bool)$answerFirst;
    }

    /**
     * Whether this process can send its answer and go on working afterwards: PHP-FPM (fastcgi_finish_request()),
     * LiteSpeed (litespeed_finish_request()), FrankenPHP (frankenphp_finish_request()), and a worker of Velocity
     * (it runs the shutdown functions once the answer is sent). Not the Apache module or the command line.
     *
     * @return bool
     */
    public static function canAnswerFirst()
    {
        if ( self::$answerFirst !== null )
            return self::$answerFirst;
        if ( PHP_SAPI === 'fpm-fcgi' && function_exists( 'fastcgi_finish_request' ) )
            return true;
        if ( PHP_SAPI === 'litespeed' && function_exists( 'litespeed_finish_request' ) )
            return true;
        if ( PHP_SAPI === 'frankenphp' && function_exists( 'frankenphp_finish_request' ) )
            return true;
        return PHP_SAPI === 'cli' && ( defined( 'QBIX_WEBSERVER' ) || isset( $_SERVER['QBIX_WORKER'] ) || isset( $_SERVER['VELOCITY'] )
                                       || class_exists( 'Q_WebServer', false ) )
               && !empty( $_SERVER['REQUEST_URI'] );
    }

    /**
     * Whether a request uses the backlog: [AliasSettings] Backlog enabled, on-demand mode, and a server that can answer
     * first (canAnswerFirst()).
     *
     * @return bool
     */
    public static function applies()
    {
        return expImageAliasSettings::backlogEnabled() && expImageAliasThrottle::applies() && self::canAnswerFirst();
    }

    // ------------------------------------------------------------------------------------------------ entries

    /**
     * The file name of an entry: when it was added (milliseconds, so the names sort oldest first) and a key of what
     * it makes (one entry per alias, variant formats and siteaccess).
     *
     * @param array $entry
     * @return string
     */
    protected static function key( array $entry )
    {
        return md5( json_encode( array( $entry['s'], $entry['a'], $entry['v'], $entry['l'], $entry['n'], $entry['f'] ) ) );
    }

    /**
     * Adds an alias to the backlog of this server, once: an alias already waiting there is not added again.
     *
     * @param int $attribute
     * @param int $version
     * @param string $language
     * @param string $aliasName
     * @param string[] $formats variants to make with it
     * @param string|null $target the file expected (the log)
     * @param string|null $siteAccess null: this request's
     * @return bool added (false: already waiting, or the directory cannot be written)
     */
    public static function add( $attribute, $version, $language, $aliasName, array $formats = array(), $target = null, $siteAccess = null )
    {
        $entry = self::entry( $attribute, $version, $language, $aliasName, $formats, $target, $siteAccess );
        $directory = self::directory();
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );
        $key = self::key( $entry );
        if ( glob( $directory . '/*-' . $key . '.json', GLOB_NOSORT ) )
            return false;
        return self::write( $directory, $entry, $key );
    }

    /**
     * The entry of an alias (see add()).
     *
     * @return array
     */
    protected static function entry( $attribute, $version, $language, $aliasName, array $formats, $target, $siteAccess )
    {
        return array( 's' => $siteAccess !== null ? (string)$siteAccess : expImageAliasSettings::currentSiteAccess(),
                      'a' => (int)$attribute, 'v' => (int)$version, 'l' => (string)$language, 'n' => (string)$aliasName,
                      'f' => array_values( array_map( 'strval', $formats ) ), 'u' => $target !== null ? (string)$target : '', 't' => time() );
    }

    /**
     * Adds an alias to the backlog once this request has sent its answer (flushPending() in drainAfterAnswer()): a page
     * that queues hundreds of aliases (page placeholders) does not write them before its first byte. Where the
     * request cannot answer first, at once (add()). What a request that dies first leaves unwritten the swap script
     * asks for through content/image, which adds it then.
     *
     * @see add()
     * @return bool kept for after the answer, or added (false: already waiting, or not written)
     */
    public static function addAfterAnswer( $attribute, $version, $language, $aliasName, array $formats = array(), $target = null, $siteAccess = null )
    {
        if ( !self::canAnswerFirst() )
            return self::add( $attribute, $version, $language, $aliasName, $formats, $target, $siteAccess );
        $request = class_exists( 'eZExecution' ) ? eZExecution::requestKey() : '';
        if ( self::$pendingFor !== $request )
        {
            self::$pending = array();
            self::$pendingFor = $request;
        }
        $entry = self::entry( $attribute, $version, $language, $aliasName, $formats, $target, $siteAccess );
        self::$pending[self::key( $entry )] = $entry;
        self::scheduleDrain();
        return true;
    }

    /**
     * Writes the entries addAfterAnswer() kept for this request, in their order, each once: one listing of the
     * directory finds those already waiting or being made.
     *
     * @return int entries written
     */
    public static function flushPending()
    {
        $request = class_exists( 'eZExecution' ) ? eZExecution::requestKey() : '';
        $pending = self::$pendingFor === $request ? self::$pending : array();
        self::$pending = array();
        self::$pendingFor = null;
        if ( !$pending )
            return 0;
        $directory = self::directory();
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );
        $known = array();
        foreach ( (array)@scandir( $directory ) as $name )
        {
            if ( preg_match( '/-([0-9a-f]{32})\.json(?:\.\d+\.work)?$/', (string)$name, $match ) )
                $known[$match[1]] = true;
        }
        $written = 0;
        foreach ( $pending as $key => $entry )
        {
            if ( isset( $known[$key] ) )
                continue;
            if ( self::write( $directory, $entry, $key ) )
                $written++;
        }
        return $written;
    }

    /**
     * Writes an entry file (whole, then renamed into place).
     *
     * @param string $directory
     * @param array $entry
     * @param string $key
     * @return bool
     */
    protected static function write( $directory, array $entry, $key )
    {
        // Milliseconds, then this process's count within the millisecond: a page adds hundreds of entries at once,
        // and they are made in the order it added them
        $ms = sprintf( '%014.0f', microtime( true ) * 1000 );
        self::$sequence = $ms === self::$sequenceMs ? self::$sequence + 1 : 0;
        self::$sequenceMs = $ms;
        $file = $directory . '/' . $ms . sprintf( '%03d', min( 999, self::$sequence ) ) . '-' . $key . '.json';
        $temporary = $file . '.' . getmypid() . '.tmp';
        if ( @file_put_contents( $temporary, json_encode( $entry ) ) === false )
        {
            eZDebug::writeWarning( "Cannot write the image alias backlog in $directory", __METHOD__ );
            return false;
        }
        // Velocity runs as root, PHP-FPM as the site user: both take entries the other wrote
        @chmod( $temporary, eZFile::fileMode( 0666 ) );
        if ( !@rename( $temporary, $file ) )
        {
            @unlink( $temporary );
            return false;
        }
        return true;
    }

    /**
     * @return int the entries waiting (not taken by a process)
     */
    public static function count()
    {
        $files = glob( self::directory() . '/*.json', GLOB_NOSORT );
        return is_array( $files ) ? count( $files ) : 0;
    }

    /**
     * Takes the oldest waiting entry for this process (renamed to <name>.<pid>.work, so no other process takes it).
     *
     * @return array|null the entry, with 'file' (the taken file); null when none is waiting
     */
    public static function take()
    {
        $files = glob( self::directory() . '/*.json' );
        if ( !$files )
            return null;
        foreach ( $files as $file )
        {
            $work = $file . '.' . getmypid() . '.work';
            if ( !@rename( $file, $work ) )
                continue; // another process took it first
            // The time it was taken (a rename keeps the time it was added): recoverStale() gives back only the entries
            // of processes that died, not one that waited long and is being made now
            @touch( $work );
            $entry = json_decode( (string)@file_get_contents( $work ), true );
            if ( !is_array( $entry ) || !isset( $entry['a'], $entry['v'], $entry['l'], $entry['n'] ) )
            {
                @unlink( $work );
                continue;
            }
            $entry['file'] = $work;
            return $entry;
        }
        return null;
    }

    /**
     * Removes an entry this process took and finished.
     *
     * @param array $entry from take()
     */
    public static function finish( array $entry )
    {
        if ( isset( $entry['file'] ) )
            @unlink( $entry['file'] );
    }

    /**
     * Gives back the entries a process took and never finished (it died): waiting again after STALE_WORK seconds.
     *
     * @return int entries given back
     */
    public static function recoverStale()
    {
        $given = 0;
        foreach ( (array)glob( self::directory() . '/*.json.*.work', GLOB_NOSORT ) as $work )
        {
            // the time another process (or touch() before PHP 8.3) set, not the one PHP's stat cache kept
            clearstatcache( true, $work );
            $mtime = @filemtime( $work );
            if ( $mtime === false || time() - $mtime < self::STALE_WORK )
                continue;
            if ( @rename( $work, preg_replace( '/\.\d+\.work$/', '', $work ) ) )
                $given++;
        }
        return $given;
    }

    // ------------------------------------------------------------------------------------------------ making

    /**
     * Makes the aliases of the backlog while this process gets a free conversion slot, oldest first, for at most
     * $seconds. Takes no slot it has to wait for (a request: the slots are the limit, and a process that finds them
     * taken is free for the next request at once) unless $slotWait says so (the cronjob). A slot is given back and
     * taken again between two rounds, so an entry added while it was held is not left behind.
     *
     * @param float $seconds
     * @param float $slotWait seconds to wait for a slot
     * @return int aliases (with their variants) worked on
     */
    public static function drain( $seconds, $slotWait = 0.0 )
    {
        $end = microtime( true ) + max( 0.0, (float)$seconds );
        $worked = 0;
        $deadlineBefore = expImageAliasThrottle::setDeadline( null );
        $waitBefore = expImageAliasLock::setWaitLimit( null );
        try
        {
            while ( microtime( true ) < $end && self::count() > 0 )
            {
                if ( !expImageAliasThrottle::acquire( $slotWait ) )
                    break;
                try
                {
                    while ( microtime( true ) < $end && ( $entry = self::take() ) !== null )
                    {
                        if ( self::$worker !== null )
                            call_user_func( self::$worker, $entry );
                        else
                            self::work( $entry );
                        self::finish( $entry );
                        $worked++;
                    }
                }
                finally
                {
                    expImageAliasThrottle::release();
                }
            }
        }
        finally
        {
            expImageAliasThrottle::setDeadline( $deadlineBefore );
            expImageAliasLock::setWaitLimit( $waitBefore );
        }
        return $worked;
    }

    /**
     * Makes one entry as a job of the queue does: the published image (the version the request named when it is
     * the published one), the alias as the entry's siteaccess defines it, and its variants.
     *
     * @param array $entry
     * @return array the counters of expImageAliasJob::runGenerate()
     */
    public static function work( array $entry )
    {
        try
        {
            $summary = expImageAliasJob::runGenerate( array( 'items' => array( array( (int)$entry['a'], (int)$entry['v'], (string)$entry['l'] ) ),
                                                             'aliases' => array( (string)$entry['s'] !== '' ? (string)$entry['s'] : '-' => array( (string)$entry['n'] ) ),
                                                             'formats' => isset( $entry['f'] ) && is_array( $entry['f'] ) ? $entry['f'] : array(),
                                                             'origin' => 'visitor' ) );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Making the image alias ' . $entry['n'] . ' of attribute ' . $entry['a'] . ' from the backlog failed: '
                                 . $e->getMessage(), __METHOD__ );
            $summary = array( 'result' => 'failed', 'counts' => expImageAliasJob::emptyCounts() );
        }
        return $summary;
    }

    // ------------------------------------------------------------------------------------------------ after the answer

    /**
     * Makes the backlog once this request has sent its answer (registered once per request): see drain().
     */
    public static function scheduleDrain()
    {
        $request = class_exists( 'eZExecution' ) ? eZExecution::requestKey() : '';
        if ( self::$scheduled && self::$scheduledFor === $request )
            return;
        self::$scheduled = true;
        self::$scheduledFor = $request;
        // The installation's directory: PHP-FPM runs shutdown functions elsewhere, and storage paths are relative
        $directory = getcwd();
        register_shutdown_function( array( __CLASS__, 'drainAfterAnswer' ), $request, $directory !== false ? $directory : '' );
    }

    /**
     * The shutdown function of scheduleDrain(): finishes the request (the browser has its answer), then drains for
     * [AliasSettings] BacklogTime seconds. Nothing for a request other than the one that registered it (a persistent
     * worker can still hold the shutdown functions of an earlier one). Works in $directory, the directory the request
     * ran in, and goes back afterwards.
     *
     * @param string $request
     * @param string $directory
     */
    public static function drainAfterAnswer( $request = '', $directory = '' )
    {
        if ( class_exists( 'eZExecution' ) && (string)$request !== eZExecution::requestKey() )
            return;
        self::$scheduled = false;
        if ( !self::canAnswerFirst() )
            return;
        $before = getcwd();
        try
        {
            self::finishRequest();
            if ( (string)$directory !== '' && $directory !== $before )
                @chdir( $directory );
            // The entries the page kept for after its answer (page placeholders), before anything drains
            self::flushPending();
            // A process of its own makes the backlog ([AliasSettings] BacklogDrainer=process): this one is free for
            // the next request at once. Velocity hands a worker its next request as soon as it has answered, so a
            // worker that drained here kept that request waiting for BacklogTime.
            if ( !self::usesDrainers() || self::startDrainers() === null )
                self::drain( expImageAliasSettings::backlogTime() );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'The image alias backlog could not be made: ' . $e->getMessage(), __METHOD__ );
        }
        finally
        {
            if ( $before !== false && getcwd() !== $before )
                @chdir( $before );
        }
    }

    // ------------------------------------------------------------------------------------------------ drainer processes

    /**
     * Sets what startDrainers() runs to start drainer $slot (tests): callable( int $slot ) => bool started; null: a
     * detached process of bin/php/expimagealiasdrain.php.
     *
     * @param callable|null $spawner
     */
    public static function setSpawner( $spawner )
    {
        self::$spawner = $spawner;
    }

    /**
     * Whether the backlog is made by drainer processes ([AliasSettings] BacklogDrainer=process) and this PHP can
     * start one (proc_open() allowed).
     *
     * @return bool
     */
    public static function usesDrainers()
    {
        if ( expImageAliasSettings::backlogDrainer() !== 'process' )
            return false;
        if ( self::$spawner !== null )
            return true;
        $disabled = array_map( 'trim', explode( ',', (string)ini_get( 'disable_functions' ) ) );
        return function_exists( 'proc_open' ) && !in_array( 'proc_open', $disabled, true );
    }

    /**
     * @return string the directory of the drainer locks: <backlog directory>/.drainers
     */
    public static function drainerDirectory()
    {
        return self::directory() . '/.drainers';
    }

    /**
     * Starts drainer processes for the backlog: one per MaxParallel slot that no drainer holds (a lock file each),
     * while there are at least DRAINER_ENTRIES waiting entries per drainer. A drainer just started counts as running
     * for DRAINER_START seconds (its marker file), until it holds its lock.
     *
     * @return int|null drainers started (0: enough run already, or nothing waits); null when none could be started
     */
    public static function startDrainers()
    {
        $waiting = self::count();
        if ( $waiting === 0 )
            return 0;
        $directory = self::drainerDirectory();
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );
        $running = 0;
        $free = array();
        for ( $slot = 0; $slot < expImageAliasSettings::maxParallel(); $slot++ )
        {
            if ( self::drainerRunning( $slot ) )
                $running++;
            else
                $free[] = $slot;
        }
        $started = 0;
        $failed = false;
        foreach ( $free as $slot )
        {
            if ( ( $running + $started ) * self::DRAINER_ENTRIES >= $waiting )
                break;
            $marker = $directory . '/drainer-' . $slot . '.start';
            @touch( $marker );
            @chmod( $marker, eZFile::fileMode( 0666 ) );
            $ok = self::$spawner !== null ? ( call_user_func( self::$spawner, $slot ) ? true : 'the spawner refused' )
                                          : self::spawnDrainer( $slot );
            if ( $ok === true )
                $started++;
            else
            {
                @unlink( $marker );
                $failed = true;
                // The request makes the backlog itself then; said once per request in imagealias.log
                expImageAliasLog::write( 'queue', array( 'result' => 'no-drainer', 'slot' => $slot, 'reason' => (string)$ok ) );
                break;
            }
        }
        return $failed && $started === 0 && $running === 0 ? null : $started;
    }

    /**
     * Whether a drainer holds slot $slot, or was started for it less than DRAINER_START seconds ago.
     *
     * @param int $slot
     * @return bool
     */
    public static function drainerRunning( $slot )
    {
        $directory = self::drainerDirectory();
        $marker = $directory . '/drainer-' . (int)$slot . '.start';
        clearstatcache( true, $marker );
        $started = @filemtime( $marker );
        if ( $started !== false && time() - $started < self::DRAINER_START )
            return true;
        $file = $directory . '/drainer-' . (int)$slot . '.lock';
        if ( !is_file( $file ) )
            return false;
        // read-only is enough for flock(): a lock file another user made works as well
        $handle = @fopen( $file, 'r' );
        if ( !$handle )
            return false;
        $free = flock( $handle, LOCK_EX | LOCK_NB );
        if ( $free )
            flock( $handle, LOCK_UN );
        fclose( $handle );
        return !$free;
    }

    /**
     * Starts bin/php/expimagealiasdrain.php --slot=$slot detached (its own session, no terminal, output discarded),
     * in this request's siteaccess.
     *
     * @param int $slot
     * @return true|string true when started, else why not
     */
    protected static function spawnDrainer( $slot )
    {
        $php = self::phpBinary();
        if ( $php === '' )
            return 'no PHP command line binary found';
        $argv = array( $php, 'bin/php/expimagealiasdrain.php', '--slot=' . (int)$slot, '-q' );
        $siteAccess = expImageAliasSettings::currentSiteAccess();
        if ( $siteAccess !== '' )
            array_push( $argv, '-s', $siteAccess );
        // Started by root (Velocity): the drainer becomes the owner of var/ itself
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
            $argv[] = '--allow-root-user';
        $setsid = class_exists( 'expProcessTools' ) ? (string)expProcessTools::setsid() : '';
        $shell = is_executable( '/bin/bash' ) ? '/bin/bash' : '/bin/sh';
        // close what the parent left open above 2 (a web worker's sockets), then start it detached
        $script = 'for f in /proc/$$/fd/*; do n=${f##*/}; case $n in 0|1|2) ;; *[!0-9]*) ;; *) eval "exec $n>&-" 2>/dev/null ;; esac; done; '
                . ( $setsid !== '' ? 'exec ' . $setsid . ' -f "$@" < /dev/null > /dev/null 2>&1'
                                   : '"$@" < /dev/null > /dev/null 2>&1 & exit 0' );
        // Pipes, not files: Velocity's workers replace the file:// stream wrapper, and proc_open() cannot hand a
        // wrapped file to a process (it failed there). The drainer itself reads and writes /dev/null (above).
        $pipes = array();
        error_clear_last();
        $process = @proc_open( array_merge( array( $shell, '-c', $script, 'expimagealiasdrain-spawn' ), $argv ),
                               array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ),
                               $pipes, getcwd() !== false ? getcwd() : null );
        if ( !is_resource( $process ) )
        {
            $last = error_get_last();
            return 'proc_open() failed' . ( $last ? ': ' . $last['message'] : '' );
        }
        foreach ( $pipes as $pipe )
            fclose( $pipe );
        $code = proc_close( $process );
        // -1: the status could not be read (another part of the process reaps its children); the start went out
        if ( $code !== 0 && $code !== -1 )
            return 'the start of ' . $php . ' bin/php/expimagealiasdrain.php ended with exit code ' . $code;
        return true;
    }

    /**
     * The PHP command line binary for drainers: the one this process runs when it is the command line (Velocity),
     * else the one found once and kept for a day in <drainer directory>/php (finding it runs PHP, too slow for
     * every request of PHP-FPM).
     *
     * @return string '' when none is found
     */
    protected static function phpBinary()
    {
        if ( PHP_SAPI === 'cli' && PHP_BINARY !== '' )
            return PHP_BINARY;
        $file = self::drainerDirectory() . '/php';
        clearstatcache( true, $file );
        $kept = @filemtime( $file );
        if ( $kept !== false && time() - $kept < 86400 )
        {
            $php = trim( (string)@file_get_contents( $file ) );
            if ( $php !== '' && is_executable( $php ) )
                return $php;
        }
        $php = class_exists( 'expProcessTools' ) ? (string)expProcessTools::phpCli() : '';
        if ( $php === '' && is_executable( PHP_BINDIR . '/php' ) )
            $php = PHP_BINDIR . '/php';
        if ( $php !== '' && @file_put_contents( $file . '.' . getmypid() . '.tmp', $php ) !== false )
        {
            @chmod( $file . '.' . getmypid() . '.tmp', eZFile::fileMode( 0666 ) );
            @rename( $file . '.' . getmypid() . '.tmp', $file );
        }
        return $php;
    }

    /**
     * What a drainer process does (bin/php/expimagealiasdrain.php): holds the lock of $slot, makes the backlog in a
     * conversion slot until it is empty or BacklogTime seconds have passed, waiting DRAINER_IDLE seconds for new
     * entries before it ends.
     *
     * @param int $slot
     * @return int|false aliases made; false when another drainer holds $slot
     */
    public static function runDrainer( $slot )
    {
        $directory = self::drainerDirectory();
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );
        $file = $directory . '/drainer-' . (int)$slot . '.lock';
        $handle = @fopen( $file, 'c' );
        if ( !$handle )
            $handle = @fopen( $file, 'r' );
        if ( !$handle || !flock( $handle, LOCK_EX | LOCK_NB ) )
        {
            if ( $handle )
                fclose( $handle );
            return false;
        }
        @chmod( $file, eZFile::fileMode( 0666 ) );
        @unlink( $directory . '/drainer-' . (int)$slot . '.start' );
        $worked = 0;
        try
        {
            if ( !expImageAliasThrottle::applies() )
                return 0;
            $end = microtime( true ) + expImageAliasSettings::backlogTime();
            $idleUntil = microtime( true ) + self::DRAINER_IDLE;
            while ( microtime( true ) < $end )
            {
                if ( self::count() === 0 )
                {
                    if ( microtime( true ) >= $idleUntil )
                        break;
                    usleep( 200000 );
                    continue;
                }
                $made = self::drain( $end - microtime( true ), 1.0 );
                $worked += $made;
                if ( $made > 0 )
                    $idleUntil = microtime( true ) + self::DRAINER_IDLE;
            }
        }
        finally
        {
            flock( $handle, LOCK_UN );
            fclose( $handle );
        }
        return $worked;
    }

    /**
     * Sends what the request has answered and closes the connection, where the server can (PHP-FPM, LiteSpeed,
     * FrankenPHP); Velocity has sent the answer already when it runs the shutdown functions.
     */
    public static function finishRequest()
    {
        if ( PHP_SAPI === 'fpm-fcgi' && function_exists( 'fastcgi_finish_request' ) )
            @fastcgi_finish_request();
        elseif ( PHP_SAPI === 'litespeed' && function_exists( 'litespeed_finish_request' ) )
            @litespeed_finish_request();
        elseif ( PHP_SAPI === 'frankenphp' && function_exists( 'frankenphp_finish_request' ) )
            @frankenphp_finish_request();
    }
}
