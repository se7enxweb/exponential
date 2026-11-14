<?php
/**
 * File containing the expImageAliasThrottle class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * How much image conversion runs at once on one server, and how bulk work gives way to the rest.
 *
 * - **Slots** ([AliasSettings] MaxParallel, default 2): a conversion (an alias, a WebP/AVIF variant) runs only while
 *   its process holds one of MaxParallel slots. A slot is an exclusive flock() on
 *   <cache dir>/imagealias-slots/<host>/slot-<n>.lock, so PHP-FPM, Velocity's workers and the command line (queue
 *   workers, scripts) on one server count together, whatever user they run as; the host name in the path keeps the
 *   count per server when var is shared between web nodes. A process that ends or dies gives its slot back (the
 *   kernel drops the flock). The wait for a slot is bounded like the wait for the lock of an alias: what is left of
 *   MaxWait in a request (setDeadline()), [ImageConverterSettings] LockTimeout elsewhere. A request that gets no slot
 *   in time answers the placeholder and queues the work (expImageAliasJob::queueMiss()).
 *   The slots apply while image.ini [AliasSettings] AliasGeneration is on-demand; render mode converts as it always
 *   did. A process takes one slot at a time: the reference alias made on the way to another alias uses the slot
 *   already held.
 * - **Pause** of bulk work: pauseBulk() / resumeBulk() set a flag in the site data table (ezsite_data
 *   imagealias_bulk_pause), so every web node and worker sees it. Bulk runs (image.alias.bulk) look at it between
 *   two images and wait (a queued run is deferred and goes on where it stopped). Visitor misses and pre-generation
 *   after publishing are not paused.
 * - **Priority** of bulk work: lowerPriority() raises the nice value of the process (command line only) to
 *   [AliasSettings] BulkNice for a bulk run; a shell converter it starts (the ImageMagick command) inherits it.
 *   restorePriority() sets it back where the process may (as root); otherwise the worker keeps the lower priority
 *   until it ends.
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageAliasThrottle
{
    /** Seconds between two tries while every slot is taken */
    const POLL_INTERVAL = 0.05;

    /** The site data row of the bulk pause */
    const PAUSE_NAME = 'imagealias_bulk_pause';

    /** @var string|null the slot directory for this process (tests) */
    protected static $directory = null;

    /** @var int|null the number of slots for this process (tests) */
    protected static $limit = null;

    /** @var bool|null whether the slots apply, over the mode (tests) */
    protected static $applies = null;

    /** @var resource|null the slot file locked by this process */
    protected static $handle = null;

    /** @var int|null the number of the slot held */
    protected static $slot = null;

    /** @var int nesting depth of acquire() in this process */
    protected static $depth = 0;

    /** @var bool the last acquire() gave up */
    protected static $timedOut = false;

    /** @var float|null no new conversion starts after this time (a request: its start + MaxWait) */
    protected static $deadline = null;

    /** @var array|null|false the pause flag for this process (tests): false = read the database */
    protected static $pauseStore = false;

    /** @var array|null the last slot held: slot, from, until, wait_ms */
    protected static $lastHold = null;

    // ------------------------------------------------------------------------------------------------ slots

    /**
     * Whether conversions take a slot: on-demand mode ([AliasSettings] AliasGeneration), whatever the process
     * (a command line script works in render mode but counts against the same slots).
     *
     * @return bool
     */
    public static function applies()
    {
        if ( self::$applies !== null )
            return self::$applies;
        return class_exists( 'expImageAliasSettings' ) && expImageAliasSettings::configuredMode() === expImageAliasSettings::MODE_ON_DEMAND;
    }

    /**
     * Sets for this process whether slots apply (tests); null goes back to the mode.
     *
     * @param bool|null $applies
     */
    public static function setApplies( $applies )
    {
        self::$applies = $applies === null ? null : (bool)$applies;
    }

    /**
     * @return int the number of slots ([AliasSettings] MaxParallel)
     */
    public static function limit()
    {
        if ( self::$limit !== null )
            return self::$limit;
        return expImageAliasSettings::maxParallel();
    }

    /**
     * Sets the number of slots for this process (tests); null goes back to MaxParallel.
     *
     * @param int|null $limit
     */
    public static function setLimit( $limit )
    {
        self::$limit = $limit === null ? null : max( 1, (int)$limit );
    }

    /**
     * Sets the slot directory for this process (tests); null: <cache dir>/imagealias-slots/<host>.
     *
     * @param string|null $directory
     */
    public static function setDirectory( $directory )
    {
        self::$directory = $directory === null ? null : rtrim( (string)$directory, '/' );
    }

    /**
     * @return string the directory of the slot files of this server
     */
    public static function directory()
    {
        if ( self::$directory !== null )
            return self::$directory;
        return eZSys::cacheDirectory() . '/imagealias-slots/' . self::hostKey();
    }

    /**
     * @return string this server's name, as a directory name
     */
    public static function hostKey()
    {
        $host = preg_replace( '/[^A-Za-z0-9_.-]/', '_', (string)php_uname( 'n' ) );
        return $host !== '' && $host !== '.' && $host !== '..' ? substr( $host, 0, 64 ) : 'local';
    }

    /**
     * Sets the time after which no conversion starts in this process (a request: its start plus MaxWait); null:
     * none (the wait is bounded by the lock's wait limit only).
     *
     * @param float|null $deadline a microtime(true) value
     * @return float|null the deadline before
     */
    public static function setDeadline( $deadline )
    {
        $before = self::$deadline;
        self::$deadline = $deadline === null ? null : (float)$deadline;
        return $before;
    }

    /**
     * @return float|null
     */
    public static function deadline()
    {
        return self::$deadline;
    }

    /**
     * Takes a conversion slot, waiting at most $timeout seconds (and never past the deadline). Reentrant: a process
     * that holds a slot gets it again (release() as often as acquire() succeeded).
     *
     * @param float|null $timeout null: expImageAliasLock::waitLimit()
     * @return bool true when the conversion may run now
     */
    public static function acquire( $timeout = null )
    {
        self::$timedOut = false;
        if ( self::$depth > 0 )
        {
            self::$depth++;
            return true;
        }
        if ( !self::applies() )
        {
            self::$depth = 1;
            return true;
        }
        $timeout = $timeout === null ? expImageAliasLock::waitLimit() : max( 0.0, (float)$timeout );
        $asked = microtime( true );
        $end = $asked + $timeout;
        if ( self::$deadline !== null )
        {
            $end = min( $end, self::$deadline );
            // The time of the request is used up: no new conversion starts
            if ( microtime( true ) >= self::$deadline )
            {
                self::$timedOut = true;
                return false;
            }
        }
        $directory = self::directory();
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );
        $limit = self::limit();
        while ( true )
        {
            $opened = 0;
            for ( $slot = 0; $slot < $limit; $slot++ )
            {
                $handle = self::open( $directory . '/slot-' . $slot . '.lock' );
                if ( $handle === false )
                    continue;
                $opened++;
                if ( @flock( $handle, LOCK_EX | LOCK_NB ) )
                {
                    self::$handle = $handle;
                    self::$slot = $slot;
                    self::$depth = 1;
                    $now = microtime( true );
                    self::$lastHold = array( 'slot' => $slot, 'from' => $now, 'until' => null,
                                             'wait_ms' => (int)round( ( $now - $asked ) * 1000 ) );
                    return true;
                }
                @fclose( $handle );
            }
            if ( $opened === 0 )
            {
                // No slot file can be opened here (a read-only cache directory): converted without a slot, as a
                // request that cannot count must still be able to show the image
                eZDebug::writeWarning( "Cannot open the image conversion slots in $directory, converting without a limit", __METHOD__ );
                self::$depth = 1;
                return true;
            }
            if ( microtime( true ) >= $end )
            {
                self::$timedOut = true;
                return false;
            }
            usleep( (int)( self::POLL_INTERVAL * 1000000 ) );
        }
    }

    /**
     * Gives a slot back (the last release() of nested acquire() calls frees it).
     */
    public static function release()
    {
        if ( self::$depth <= 0 )
            return;
        if ( --self::$depth > 0 )
            return;
        if ( self::$handle !== null )
        {
            @flock( self::$handle, LOCK_UN );
            @fclose( self::$handle );
            if ( self::$lastHold !== null && self::$lastHold['until'] === null )
                self::$lastHold['until'] = microtime( true );
        }
        self::$handle = null;
        self::$slot = null;
    }

    /**
     * The last slot this process held: slot, from and until (microtime), wait_ms (the wait for it); null when none
     * was taken (render mode, no slot files). For the log, so the conversions at once can be counted afterwards.
     *
     * @return array|null
     */
    public static function lastHold()
    {
        return self::$lastHold;
    }

    /**
     * Forgets the last slot held (before a new piece of work whose hold is to be reported).
     */
    public static function forgetLastHold()
    {
        self::$lastHold = null;
    }

    /**
     * Gives every slot of this process back at once (a persistent worker between two requests, tests).
     */
    public static function reset()
    {
        self::$depth = min( self::$depth, 1 );
        self::release();
        self::$depth = 0;
        self::$timedOut = false;
        self::$deadline = null;
    }

    /**
     * @return bool the last acquire() gave up (every slot taken until the wait ran out, or the deadline passed)
     */
    public static function timedOut()
    {
        return self::$timedOut;
    }

    /**
     * @return int|null the number of the slot this process holds
     */
    public static function heldSlot()
    {
        return self::$slot;
    }

    /**
     * How many slots other processes hold now (the dashboard, checks). Probes each slot without waiting.
     *
     * @return int
     */
    public static function busy()
    {
        $directory = self::directory();
        $busy = 0;
        for ( $slot = 0; $slot < self::limit(); $slot++ )
        {
            if ( $slot === self::$slot )
            {
                $busy++;
                continue;
            }
            $file = $directory . '/slot-' . $slot . '.lock';
            if ( !is_file( $file ) )
                continue;
            $handle = @fopen( $file, 'r' );
            if ( $handle === false )
                continue;
            if ( @flock( $handle, LOCK_EX | LOCK_NB ) )
                @flock( $handle, LOCK_UN );
            else
                $busy++;
            @fclose( $handle );
        }
        return $busy;
    }

    /**
     * @param string $file
     * @return resource|false
     */
    protected static function open( $file )
    {
        $handle = @fopen( $file, 'c' );
        // A slot file another user made (Velocity runs as root, PHP-FPM as the site user): a read handle can hold an
        // exclusive flock() as well
        if ( $handle === false )
            $handle = @fopen( $file, 'r' );
        return $handle;
    }

    // ------------------------------------------------------------------------------------------------ pause

    /**
     * Pauses bulk runs on every server (they wait between two images until resumeBulk()).
     *
     * @param string $reason
     * @param string $by who paused (a login, "cli")
     * @return bool
     */
    public static function pauseBulk( $reason = '', $by = '' )
    {
        $value = array( 'since' => time(), 'by' => substr( (string)$by, 0, 100 ), 'reason' => substr( (string)$reason, 0, 255 ) );
        if ( self::$pauseStore !== false )
        {
            self::$pauseStore = $value;
            return true;
        }
        eZSiteData::createNew( self::PAUSE_NAME, json_encode( $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
        expImageAliasLog::write( 'pause', array( 'result' => 'paused', 'alias' => '-', 'by' => $value['by'], 'path' => $value['reason'] ) );
        return true;
    }

    /**
     * Lets bulk runs go on.
     *
     * @param string $by
     * @return bool whether they were paused
     */
    public static function resumeBulk( $by = '' )
    {
        if ( self::$pauseStore !== false )
        {
            $was = self::$pauseStore !== null;
            self::$pauseStore = null;
            return $was;
        }
        $row = eZSiteData::fetchByName( self::PAUSE_NAME );
        if ( !$row instanceof eZSiteData )
            return false;
        $row->remove();
        expImageAliasLog::write( 'resume', array( 'result' => 'resumed', 'alias' => '-', 'by' => substr( (string)$by, 0, 100 ) ) );
        return true;
    }

    /**
     * @return array|null since, by, reason of the pause; null while bulk runs may go on
     */
    public static function bulkPause()
    {
        if ( self::$pauseStore !== false )
            return self::$pauseStore;
        try
        {
            $row = eZSiteData::fetchByName( self::PAUSE_NAME );
        }
        catch ( Throwable $e )
        {
            return null;
        }
        if ( !$row instanceof eZSiteData )
            return null;
        $value = json_decode( (string)$row->attribute( 'value' ), true );
        return is_array( $value ) ? $value + array( 'since' => 0, 'by' => '', 'reason' => '' ) : array( 'since' => 0, 'by' => '', 'reason' => '' );
    }

    /**
     * @return bool
     */
    public static function isBulkPaused()
    {
        return self::bulkPause() !== null;
    }

    /**
     * Keeps the pause flag in this process instead of the database (tests); false goes back to the database.
     *
     * @param array|null|false $value
     */
    public static function setPauseStore( $value )
    {
        self::$pauseStore = $value;
    }

    // ------------------------------------------------------------------------------------------------ priority

    /**
     * @return int the nice value of bulk runs ([AliasSettings] BulkNice, 0..19; 0 = unchanged)
     */
    public static function bulkNice()
    {
        return (int)expImageAliasSettings::bulkNice();
    }

    /**
     * Lowers the priority of this process to $nice (never raises it). Command line only (never a web server worker).
     *
     * @param int|null $nice null: bulkNice()
     * @return int|null the priority before, for restorePriority(); null when nothing changed
     */
    public static function lowerPriority( $nice = null )
    {
        $nice = $nice === null ? self::bulkNice() : max( 0, min( 19, (int)$nice ) );
        if ( $nice <= 0 || PHP_SAPI !== 'cli' || expImageAliasSettings::isWebProcess()
             || !function_exists( 'pcntl_getpriority' ) || !function_exists( 'pcntl_setpriority' ) )
            return null;
        $before = @pcntl_getpriority();
        if ( !is_int( $before ) || $before >= $nice )
            return null;
        return @pcntl_setpriority( $nice ) ? $before : null;
    }

    /**
     * Sets the priority back (only a process allowed to, such as root, can raise it again).
     *
     * @param int|null $before what lowerPriority() returned
     * @return bool
     */
    public static function restorePriority( $before )
    {
        if ( $before === null || !function_exists( 'pcntl_setpriority' ) )
            return false;
        return (bool)@pcntl_setpriority( (int)$before );
    }
}
