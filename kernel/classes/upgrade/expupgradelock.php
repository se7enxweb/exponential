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
 * It is the installation lock of expMigrationLock (expMigrationLock::installation()), which migration runs take
 * too: an upgrade and a migration run never write at the same time. The holder file next to the lock
 * (upgrade.lock.holder) says who holds it.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeLock
{
    /** @var expMigrationLock|null the lock this process holds through this class */
    protected static $lock;

    /** @var bool whether acquire() took the lock (false: this process held it already) */
    protected static $owned = false;

    /** @return string */
    public static function file()
    {
        return expMigrationLock::defaultFile( expMigrationLock::INSTALLATION );
    }

    /**
     * Takes the lock without waiting.
     *
     * @param string|null $file
     * @return bool false when another run holds it
     */
    public static function acquire( $file = null )
    {
        if ( self::$lock )
            return true;
        $lock = expMigrationLock::installation( $file ?: self::file() );
        // held already by this process (a migration run that runs upgrade steps): used, and left to its holder
        $heldBefore = $lock->isHeld();
        if ( !$lock->acquire( array( 'purpose' => 'exp:upgrade', 'user' => self::user() ) ) )
            return false;
        self::$lock = $lock;
        self::$owned = !$heldBefore;
        return true;
    }

    /** Lets go of the lock (not one this process held before acquire() was called). */
    public static function release()
    {
        if ( !self::$lock )
            return;
        if ( self::$owned )
            self::$lock->release();
        self::$lock = null;
        self::$owned = false;
    }

    /**
     * Who holds the lock (expMigrationLock::holder()), or null.
     *
     * @param string|null $file
     * @return array|null
     */
    public static function holder( $file = null )
    {
        return expMigrationLock::installation( $file ?: self::file() )->holder();
    }

    /** @return string the system user of the process */
    protected static function user()
    {
        if ( function_exists( 'posix_geteuid' ) && function_exists( 'posix_getpwuid' ) )
        {
            $info = @posix_getpwuid( posix_geteuid() );
            if ( is_array( $info ) && isset( $info['name'] ) )
                return 'system user ' . $info['name'];
        }
        return '';
    }
}
