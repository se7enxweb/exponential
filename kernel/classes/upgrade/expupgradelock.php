<?php
/**
 * File containing the expUpgradeLock class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One upgrade at a time: an exclusive lock on var/<site>/upgrade.lock, held by a run that writes (a dry run takes
 * none). The lock goes with the process, so a run that dies leaves nothing to clean up.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeLock
{
    /** @var resource|null */
    protected static $handle;

    /** @return string */
    public static function file()
    {
        return rtrim( eZSys::varDirectory(), '/' ) . '/upgrade.lock';
    }

    /**
     * Takes the lock without waiting.
     *
     * @param string|null $file
     * @return bool false when another run holds it
     */
    public static function acquire( $file = null )
    {
        if ( self::$handle )
            return true;
        $file = $file ?: self::file();
        if ( !is_dir( dirname( $file ) ) )
            @mkdir( dirname( $file ), eZDir::dirMode( 0775 ), true );
        $handle = @fopen( $file, 'c' );
        if ( !$handle )
            return false;
        if ( !flock( $handle, LOCK_EX | LOCK_NB ) )
        {
            fclose( $handle );
            return false;
        }
        ftruncate( $handle, 0 );
        fwrite( $handle, getmypid() . "\n" );
        self::$handle = $handle;
        return true;
    }

    /** Lets go of the lock. */
    public static function release()
    {
        if ( !self::$handle )
            return;
        flock( self::$handle, LOCK_UN );
        fclose( self::$handle );
        self::$handle = null;
    }
}
