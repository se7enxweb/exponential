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
        $entry = array( 's' => $siteAccess !== null ? (string)$siteAccess : expImageAliasSettings::currentSiteAccess(),
                        'a' => (int)$attribute, 'v' => (int)$version, 'l' => (string)$language, 'n' => (string)$aliasName,
                        'f' => array_values( array_map( 'strval', $formats ) ), 'u' => $target !== null ? (string)$target : '', 't' => time() );
        $directory = self::directory();
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );
        $key = self::key( $entry );
        if ( glob( $directory . '/*-' . $key . '.json', GLOB_NOSORT ) )
            return false;
        $file = $directory . '/' . sprintf( '%014.0f', microtime( true ) * 1000 ) . '-' . $key . '.json';
        $temporary = $file . '.' . getmypid() . '.tmp';
        if ( @file_put_contents( $temporary, json_encode( $entry ) ) === false )
        {
            eZDebug::writeWarning( "Cannot write the image alias backlog in $directory", __METHOD__ );
            return false;
        }
        // Velocity runs as root, PHP-FPM as the site user: both take entries the other wrote
        @chmod( $temporary, 0666 );
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
