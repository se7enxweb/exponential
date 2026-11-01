<?php
/**
 * File containing the expStaticLock class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One build, publish, rollback or push of a target at a time (static publishing): an exclusive lock on
 * <WorkDir>/.locks/<target>.lock, outside the target's checkout. The lock file holds the process id and the time of
 * the holder for the message the second caller gets. The operating system releases the lock when the process ends,
 * so a crashed build never blocks the next one. flock() works on Windows as well.
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticLock
{
    /** @var resource|null */
    protected $handle;

    /** @var string */
    protected $file;

    protected function __construct( $handle, $file )
    {
        $this->handle = $handle;
        $this->file = $file;
    }

    /**
     * Takes the lock of a target.
     *
     * @param string $workDir the folder of the targets
     * @param string $targetId
     * @param string $holder what holds it (publish, build ...), for the message of a second caller
     * @param string $problem why the lock was not taken
     * @return expStaticLock|null null when another process holds it or the file cannot be opened
     */
    public static function acquire( $workDir, $targetId, $holder, &$problem = '' )
    {
        $problem = '';
        $dir = rtrim( str_replace( '\\', '/', (string)$workDir ), '/' ) . '/.locks';
        if ( !is_dir( $dir ) && !@mkdir( $dir, eZDir::dirMode( 0775 ), true ) && !is_dir( $dir ) )
        {
            $problem = "the lock folder $dir cannot be created";
            return null;
        }
        $file = $dir . '/' . $targetId . '.lock';
        $handle = @fopen( $file, 'c+' );
        if ( !$handle )
        {
            $problem = "the lock file $file cannot be opened";
            return null;
        }
        if ( !flock( $handle, LOCK_EX | LOCK_NB ) )
        {
            $held = trim( (string)stream_get_contents( $handle ) );
            fclose( $handle );
            $problem = "another process works on the target $targetId" . ( $held !== '' ? " ($held)" : '' ) . '; try again when it has finished';
            return null;
        }
        ftruncate( $handle, 0 );
        rewind( $handle );
        fwrite( $handle, $holder . ' by process ' . getmypid() . ' since ' . gmdate( 'Y-m-d H:i:s' ) . " UTC\n" );
        fflush( $handle );
        return new self( $handle, $file );
    }

    /** Releases the lock. */
    public function release()
    {
        if ( $this->handle )
        {
            ftruncate( $this->handle, 0 );
            flock( $this->handle, LOCK_UN );
            fclose( $this->handle );
            $this->handle = null;
        }
    }

    public function __destruct()
    {
        $this->release();
    }
}
