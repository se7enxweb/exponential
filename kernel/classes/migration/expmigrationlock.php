<?php
/**
 * File containing the expMigrationLock class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A named lock of the installation with a holder file: one writer at a time, and anyone can read who holds it.
 *
 *   var/<site>/<name>.lock          the lock: flock() on it, the holder's process id inside (as exp:upgrade has written
 *                                   it since 6.0.15)
 *   var/<site>/<name>.lock.holder   who holds it, as JSON: name, purpose, user, pid, host, started (a Unix time)
 *
 * The lock goes with the process (flock), so a run that dies frees it at once and leaves nothing to clean up but the
 * holder file. A holder file without a lock is stale: holder() says so, and the next acquire() takes over and writes
 * its own holder.
 *
 * exp:upgrade and migration runs share one lock, the installation lock (INSTALLATION, var/<site>/upgrade.lock):
 * expUpgradeLock is this class with that name, so an upgrade and a migration run never write at the same time.
 *
 * In one process a lock file is held once: acquiring it again while it is held returns true and changes nothing,
 * and one release() frees it.
 *
 *   $lock = expMigrationLock::installation();
 *   if ( !$lock->acquire( array( 'purpose' => 'migration run 20261031-120000-a1b2c3', 'user' => 'admin' ) ) )
 *       echo 'busy: ' . expMigrationLock::describe( $lock->holder() );
 *   ...
 *   $lock->release();
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationLock
{
    /** The name of the lock exp:upgrade and migration runs share. */
    const INSTALLATION = 'upgrade';

    /** @var resource[] lock file => the handle this process holds */
    protected static $handles = array();

    /** @var string */
    protected $name;

    /** @var string */
    protected $file;

    /**
     * @param string $name letters, digits, ".", "_", "-"
     * @param string|null $file the lock file (var/<site>/<name>.lock when null)
     * @throws InvalidArgumentException for a name that cannot name a file
     */
    public function __construct( $name, $file = null )
    {
        $name = (string)$name;
        if ( !preg_match( '/^[A-Za-z0-9_-][A-Za-z0-9_.-]{0,63}$/', $name ) )
            throw new InvalidArgumentException( 'not a lock name: ' . $name );
        $this->name = $name;
        $this->file = $file !== null && (string)$file !== '' ? (string)$file : self::defaultFile( $name );
    }

    /**
     * The installation lock that exp:upgrade and migration runs share.
     *
     * @param string|null $file another lock file (a test's)
     * @return expMigrationLock
     */
    public static function installation( $file = null )
    {
        return new self( self::INSTALLATION, $file );
    }

    /**
     * @param string $name
     * @return string var/<site>/<name>.lock
     */
    public static function defaultFile( $name )
    {
        return rtrim( eZSys::varDirectory(), '/' ) . '/' . $name . '.lock';
    }

    /** @return string */
    public function name()
    {
        return $this->name;
    }

    /** @return string */
    public function file()
    {
        return $this->file;
    }

    /** @return string */
    public function holderFile()
    {
        return $this->file . '.holder';
    }

    /**
     * Takes the lock without waiting and writes the holder file.
     *
     * @param array $holder what the holder file says besides name, pid, host and started: purpose, user ...
     * @return bool false when another process holds it (or the file cannot be opened)
     */
    public function acquire( array $holder = array() )
    {
        if ( $this->isHeld() )
            return true;
        $dir = dirname( $this->file );
        if ( !is_dir( $dir ) )
            @mkdir( $dir, eZDir::dirMode( 0775 ), true );
        $handle = @fopen( $this->file, 'c' );
        if ( !$handle )
            return false;
        if ( !flock( $handle, LOCK_EX | LOCK_NB ) )
        {
            fclose( $handle );
            return false;
        }
        ftruncate( $handle, 0 );
        fwrite( $handle, getmypid() . "\n" );
        fflush( $handle );
        self::$handles[$this->file] = $handle;
        $this->writeHolder( $holder );
        return true;
    }

    /** Lets go of the lock and removes the holder file. Nothing happens when this process does not hold it. */
    public function release()
    {
        if ( !$this->isHeld() )
            return;
        $handle = self::$handles[$this->file];
        unset( self::$handles[$this->file] );
        @unlink( $this->holderFile() );
        flock( $handle, LOCK_UN );
        fclose( $handle );
    }

    /**
     * Whether this process holds the lock.
     *
     * @return bool
     */
    public function isHeld()
    {
        return isset( self::$handles[$this->file] );
    }

    /**
     * Whether any process holds the lock (this one included). Asks flock() without keeping the lock.
     *
     * @return bool
     */
    public function isLocked()
    {
        if ( $this->isHeld() )
            return true;
        if ( !is_file( $this->file ) )
            return false;
        $handle = @fopen( $this->file, 'r' );
        if ( !$handle )
            return false;
        $free = flock( $handle, LOCK_EX | LOCK_NB );
        if ( $free )
            flock( $handle, LOCK_UN );
        fclose( $handle );
        return !$free;
    }

    /**
     * Who holds the lock: the holder file with 'stale' => true when no process holds the lock any more, or null when
     * there is no holder file. A lock held without a holder file (an older run) gives array( 'stale' => false ) with
     * the pid of the lock file.
     *
     * @return array|null
     */
    public function holder()
    {
        $locked = $this->isLocked();
        $text = is_file( $this->holderFile() ) ? @file_get_contents( $this->holderFile() ) : false;
        $holder = $text !== false ? json_decode( $text, true ) : null;
        if ( !is_array( $holder ) )
        {
            if ( !$locked )
                return null;
            $pid = is_file( $this->file ) ? (int)trim( (string)@file_get_contents( $this->file ) ) : 0;
            $holder = array( 'name' => $this->name, 'pid' => $pid );
        }
        $holder['stale'] = !$locked;
        return $holder;
    }

    /**
     * One line about a holder (holder()), for a message: "migration run 20261031-... by admin (pid 123 on web1) since
     * 2026-10-31 12:00:00".
     *
     * @param array|null $holder
     * @return string
     */
    public static function describe( $holder )
    {
        if ( !is_array( $holder ) )
            return 'nobody';
        $text = isset( $holder['purpose'] ) && $holder['purpose'] !== '' ? (string)$holder['purpose'] : ( isset( $holder['name'] ) ? (string)$holder['name'] : 'a run' );
        if ( !empty( $holder['user'] ) )
            $text .= ' by ' . $holder['user'];
        $where = array();
        if ( !empty( $holder['pid'] ) )
            $where[] = 'pid ' . (int)$holder['pid'];
        if ( !empty( $holder['host'] ) )
            $where[] = 'on ' . $holder['host'];
        if ( $where )
            $text .= ' (' . implode( ' ', $where ) . ')';
        if ( !empty( $holder['started'] ) )
            $text .= ' since ' . date( 'Y-m-d H:i:s', (int)$holder['started'] );
        if ( !empty( $holder['stale'] ) )
            $text .= ', stale: its process has ended';
        return $text;
    }

    /**
     * @param array $holder
     */
    protected function writeHolder( array $holder )
    {
        $holder = array_merge( array( 'name' => $this->name, 'purpose' => '', 'user' => '' ), $holder,
                               array( 'pid' => getmypid(), 'host' => (string)php_uname( 'n' ), 'started' => time() ) );
        $file = $this->holderFile();
        $tmp = $file . '.tmp' . getmypid();
        if ( @file_put_contents( $tmp, json_encode( $holder, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n" ) !== false )
        {
            @chmod( $tmp, eZFile::fileMode( 0664 ) );
            if ( !@rename( $tmp, $file ) )
                @unlink( $tmp );
        }
    }
}
