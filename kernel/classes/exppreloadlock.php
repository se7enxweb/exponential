<?php
/**
 * File containing the expPreloadLock class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One cache preload at a time.
 *
 * A preload requests every page of a site; two at once double the load and warm nothing more. The run holds an
 * exclusive flock() on one file for as long as it runs, whether it was started from Setup > Preload, from the
 * shell or from cron. The lock goes with the process: a run that dies leaves no stale lock behind, so nobody has
 * to remove a file before the next run.
 *
 * The file is opened for reading when it cannot be opened for writing: the web server and the shell may run as
 * different users, and a shared lock file belongs to whoever made it first. flock() needs only an open file.
 */
class expPreloadLock
{
    private $file;
    private $handle = null;

    /**
     * @param string $file the lock file, var/<var dir>/preload/run.lock for the preloader
     */
    public function __construct( $file )
    {
        $this->file = (string)$file;
    }

    /**
     * Takes the lock without waiting.
     *
     * @return bool false when another process holds it, or the file cannot be opened
     */
    public function acquire()
    {
        if ( $this->handle )
            return true;
        $handle = $this->open();
        if ( !$handle )
            return false;
        if ( !flock( $handle, LOCK_EX | LOCK_NB ) )
        {
            fclose( $handle );
            return false;
        }
        $this->handle = $handle;
        return true;
    }

    /**
     * Gives the lock up; nothing happens when it is not held.
     */
    public function release()
    {
        if ( !$this->handle )
            return;
        flock( $this->handle, LOCK_UN );
        fclose( $this->handle );
        $this->handle = null;
    }

    /**
     * Whether this object holds the lock.
     *
     * @return bool
     */
    public function isHeld()
    {
        return $this->handle !== null;
    }

    /**
     * Whether some process holds the lock now: true while a preload runs, here or in another process.
     *
     * @return bool
     */
    public function isLocked()
    {
        if ( $this->handle )
            return true;
        if ( !is_file( $this->file ) )
            return false;
        $handle = $this->open();
        if ( !$handle )
            return false;
        $free = flock( $handle, LOCK_EX | LOCK_NB );
        if ( $free )
            flock( $handle, LOCK_UN );
        fclose( $handle );
        return !$free;
    }

    public function __destruct()
    {
        $this->release();
    }

    private function open()
    {
        $dir = dirname( $this->file );
        if ( !is_dir( $dir ) && !@mkdir( $dir, eZDir::dirMode( 0775 ), true ) && !is_dir( $dir ) )
            return false;
        $handle = @fopen( $this->file, 'c' );
        if ( !$handle )
            $handle = @fopen( $this->file, 'r' );
        return $handle ? $handle : false;
    }
}
