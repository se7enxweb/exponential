<?php
/**
 * File containing the expImageAliasLock class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The lock of one image alias file while one process makes it, with a bounded wait.
 *
 * One lock per alias file: the aliases of one image are made in parallel, and two requests for the same alias make
 * it once (the second waits, then finds the file). Where the lock lives:
 *
 * - a database cluster (eZDFS, any ezpDatabaseBasedClusterFileHandler): the .generating entry of the alias file
 *   (the handler's startCacheGeneration()), shared by every web node;
 * - otherwise (eZFS): an exclusive flock() on <cache dir>/imagealias-locks/<md5 of the path>.lock. The lock file is
 *   removed when the lock is given back; a process that locked a file another one removed meanwhile tries again.
 *
 * The wait is bounded: image.ini [ImageConverterSettings] LockTimeout, or what setWaitLimit() sets for the process
 * (content/image waits at most [AliasSettings] MaxWait).
 *
 *   $lock = new expImageAliasLock( $aliasPath );
 *   if ( $lock->acquire() ) { ...make the alias...; $lock->release(); }
 *   elseif ( $lock->timedOut() ) { ...answer the placeholder... }
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageAliasLock
{
    /** Seconds between two tries while another process holds the lock */
    const POLL_INTERVAL = 0.05;

    /** @var float|null the wait limit of this process, over the setting */
    protected static $waitLimit = null;

    /** @var string|null a lock directory for this process (tests) */
    protected static $directory = null;

    /** @var string the alias file this lock is for */
    protected $path;

    /** @var resource|null the locked file (eZFS) */
    protected $handle = null;

    /** @var string|null the lock file (eZFS) */
    protected $lockFile = null;

    /** @var object|null the cluster handler holding the .generating entry (eZDFS) */
    protected $clusterHandler = null;

    /** @var bool another process held the lock when acquire() began */
    protected $waited = false;

    /** @var bool acquire() gave up after the wait limit */
    protected $timedOut = false;

    /** @var bool the lock is held */
    protected $held = false;

    /**
     * @param string $path the alias file (a storage path)
     */
    public function __construct( $path )
    {
        $this->path = (string)$path;
    }

    /**
     * Seconds a lock is waited for in this process: setWaitLimit(), else LockTimeout.
     *
     * @return float
     */
    public static function waitLimit()
    {
        if ( self::$waitLimit !== null )
            return self::$waitLimit;
        return class_exists( 'expImageAliasSettings' ) ? expImageAliasSettings::lockTimeout() : 60.0;
    }

    /**
     * Sets the wait limit for this process (content/image: MaxWait); null goes back to LockTimeout.
     *
     * @param float|null $seconds
     * @return float|null the limit before
     */
    public static function setWaitLimit( $seconds )
    {
        $before = self::$waitLimit;
        self::$waitLimit = $seconds === null ? null : max( 0.0, (float)$seconds );
        return $before;
    }

    /**
     * Sets the directory of the lock files for this process (tests); null: <cache dir>/imagealias-locks.
     *
     * @param string|null $directory
     */
    public static function setDirectory( $directory )
    {
        self::$directory = $directory === null ? null : rtrim( (string)$directory, '/' );
    }

    /**
     * @return string the directory of the lock files on eZFS
     */
    public static function directory()
    {
        if ( self::$directory !== null )
            return self::$directory;
        return eZSys::cacheDirectory() . '/imagealias-locks';
    }

    /**
     * Takes the lock, waiting at most $timeout seconds for another process.
     *
     * @param float|null $timeout null: waitLimit()
     * @return bool true when the lock is held now
     */
    public function acquire( $timeout = null )
    {
        if ( $this->held )
            return true;
        $timeout = $timeout === null ? self::waitLimit() : max( 0.0, (float)$timeout );
        $this->waited = false;
        $this->timedOut = false;
        $deadline = microtime( true ) + $timeout;

        $cluster = $this->clusterLockHandler();
        if ( $cluster !== null )
            return $this->acquireCluster( $cluster, $deadline );
        return $this->acquireFile( $deadline );
    }

    /**
     * Gives the lock back (removes the lock file, or the .generating entry).
     */
    public function release()
    {
        if ( !$this->held )
            return;
        if ( $this->clusterHandler !== null )
        {
            $this->clusterHandler->endCacheGeneration( false );
            $this->clusterHandler = null;
        }
        if ( $this->handle !== null )
        {
            // Removed while locked: a process waiting on this file finds it gone and tries again on a new one
            if ( $this->lockFile !== null )
                @unlink( $this->lockFile );
            @flock( $this->handle, LOCK_UN );
            @fclose( $this->handle );
            $this->handle = null;
        }
        $this->held = false;
    }

    /**
     * @return bool another process held the lock when acquire() was called (it may have made the alias)
     */
    public function waited()
    {
        return $this->waited;
    }

    /**
     * @return bool the last acquire() gave up after the wait limit
     */
    public function timedOut()
    {
        return $this->timedOut;
    }

    /**
     * @return bool
     */
    public function isHeld()
    {
        return $this->held;
    }

    /**
     * @return string the lock file of this alias on eZFS
     */
    public function lockFile()
    {
        return self::directory() . '/' . md5( $this->path ) . '.lock';
    }

    public function __destruct()
    {
        $this->release();
    }

    // ------------------------------------------------------------------------------------------------ eZFS

    /**
     * @param float $deadline
     * @return bool
     */
    protected function acquireFile( $deadline )
    {
        $directory = self::directory();
        if ( !is_dir( $directory ) )
        {
            eZDir::mkdir( $directory, false, true );
        }
        $file = $this->lockFile();
        while ( true )
        {
            $handle = @fopen( $file, 'c' );
            if ( $handle === false )
            {
                // A lock file another user made (Velocity runs as root, PHP-FPM as the site user): a shared
                // read handle can hold an exclusive flock() as well
                $handle = @fopen( $file, 'r' );
            }
            if ( $handle === false )
            {
                // No lock possible here (read-only cache directory): the alias is made without one, as before
                eZDebug::writeWarning( "Cannot open the image alias lock file $file, making the alias without a lock", __METHOD__ );
                return $this->held = true;
            }
            if ( @flock( $handle, LOCK_EX | LOCK_NB ) )
            {
                // The file locked must still be the one under that name (the holder before removed it on release)
                clearstatcache( true, $file );
                $locked = @fstat( $handle );
                $named = @stat( $file );
                if ( $locked && $named && $locked['ino'] === $named['ino'] && $locked['dev'] === $named['dev'] )
                {
                    $this->handle = $handle;
                    // No chmod: another user opens it read-only, which is enough for flock()
                    $this->lockFile = $file;
                    return $this->held = true;
                }
                @flock( $handle, LOCK_UN );
                @fclose( $handle );
                continue;
            }
            @fclose( $handle );
            $this->waited = true;
            if ( microtime( true ) >= $deadline )
            {
                $this->timedOut = true;
                return false;
            }
            usleep( (int)( self::POLL_INTERVAL * 1000000 ) );
        }
    }

    // ------------------------------------------------------------------------------------------------ eZDFS

    /**
     * The cluster handler that keeps the lock, or null when the lock is a file lock (eZFS).
     *
     * @return object|null
     */
    protected function clusterLockHandler()
    {
        if ( !class_exists( 'eZClusterFileHandler' ) || !interface_exists( 'ezpDatabaseBasedClusterFileHandler' ) )
            return null;
        $handler = eZClusterFileHandler::instance( $this->path );
        return $handler instanceof ezpDatabaseBasedClusterFileHandler ? $handler : null;
    }

    /**
     * @param object $handler
     * @param float $deadline
     * @return bool
     */
    protected function acquireCluster( $handler, $deadline )
    {
        while ( true )
        {
            $result = $handler->startCacheGeneration();
            if ( $result === true )
            {
                $this->clusterHandler = $handler;
                return $this->held = true;
            }
            if ( $result === false )
            {
                // The cluster database refused (an error, logged by the handler): made without a lock, as a
                // request that cannot lock must still be able to show the image
                eZDebug::writeWarning( "The cluster could not lock {$this->path}, making the alias without a lock", __METHOD__ );
                return $this->held = true;
            }
            $this->waited = true;
            if ( microtime( true ) >= $deadline )
            {
                $this->timedOut = true;
                return false;
            }
            usleep( 200000 );
        }
    }
}
