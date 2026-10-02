<?php
/**
 * File containing the expContentJobStore class.
 *
 * The store of the content jobs: one JSON file per job in var/<site var dir>/jobs/content/, written atomically
 * (a temporary file in the same directory, flushed to disk, then renamed over the old one), changed under an
 * exclusive flock() of the job's own lock file, so a reader never sees half a file and two writers never lose
 * each other's change. Every file the store creates belongs to the site user (the owner of the var directory),
 * also when root creates it (Exponential Velocity runs as root), so the web server can always read and replace it.
 *
 * A cluster needs a shared var directory for the jobs, as it does for the repair queue.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobStore
{
    /** @var string|null the directory, when set by setDirectory() (tests) */
    protected static $directory = null;

    /** @var array|null array( uid, gid ) of the site user, null when not known yet */
    protected static $owner = null;

    /**
     * Uses another directory (tests, or a shared directory of a cluster); null goes back to the default.
     *
     * @param string|null $directory
     */
    public static function setDirectory( $directory )
    {
        self::$directory = $directory === null ? null : rtrim( $directory, '/' );
        self::$owner = null;
    }

    /**
     * The directory of the jobs, created on first use (0770, with a .htaccess that denies web access).
     *
     * @return string
     */
    public static function directory()
    {
        $dir = self::$directory;
        if ( $dir === null )
        {
            $varDir = eZSys::varDirectory();
            if ( $varDir === '' || $varDir === null )
                $varDir = 'var';
            if ( $varDir[0] !== '/' )
                $varDir = eZSys::rootDir() . '/' . $varDir;
            $dir = $varDir . '/jobs/content';
        }
        if ( !is_dir( $dir ) )
        {
            $old = umask( 0007 );
            @mkdir( $dir, 0770, true );
            umask( $old );
            if ( !is_dir( $dir ) )
                throw new expContentJobException( "The content job directory $dir cannot be created." );
            self::fixOwner( dirname( $dir ) );
            self::fixOwner( $dir );
        }
        if ( !is_file( "$dir/.htaccess" ) )
        {
            @file_put_contents( "$dir/.htaccess", "Require all denied\n" );
            self::fixOwner( "$dir/.htaccess" );
        }
        return $dir;
    }

    /**
     * A job id is safe as a file name: lower case letters, digits and dashes.
     *
     * @param mixed $id
     * @return bool
     */
    public static function validID( $id )
    {
        return is_string( $id ) && preg_match( '/^[0-9a-z][0-9a-z-]{5,63}\z/', $id ) === 1;
    }

    /**
     * A new id: the time it was made (sortable) and random bytes.
     *
     * @return string
     */
    public static function newID()
    {
        return gmdate( 'Ymd-His' ) . '-' . bin2hex( random_bytes( 4 ) );
    }

    /**
     * @param string $id
     * @param string $suffix '.json', '.log', '.map', ...
     * @return string
     */
    public static function path( $id, $suffix = '.json' )
    {
        if ( !self::validID( $id ) )
            throw new expContentJobException( 'Invalid content job id.' );
        return self::directory() . '/' . $id . $suffix;
    }

    /**
     * The job's data, or null when there is no such job (or the id is not valid).
     *
     * @param string $id
     * @return array|null
     */
    public static function read( $id )
    {
        if ( !self::validID( $id ) )
            return null;
        $file = self::path( $id );
        if ( !is_file( $file ) )
            return null;
        $data = json_decode( (string) @file_get_contents( $file ), true );
        return is_array( $data ) ? $data : null;
    }

    /**
     * Writes a JSON file atomically: temporary file, flush, fsync, rename.
     *
     * @param string $file
     * @param mixed $data
     */
    public static function writeJSON( $file, $data )
    {
        $json = json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR );
        if ( $json === false )
            throw new expContentJobException( 'The content job could not be encoded: ' . json_last_error_msg() );
        $tmp = $file . '.tmp.' . getmypid() . '.' . bin2hex( random_bytes( 3 ) );
        $fh = @fopen( $tmp, 'x' );
        if ( !$fh )
            throw new expContentJobException( "The content job file $tmp cannot be written." );
        $ok = fwrite( $fh, $json ) === strlen( $json );
        $ok = fflush( $fh ) && $ok;
        if ( function_exists( 'fsync' ) )
            @fsync( $fh );
        fclose( $fh );
        if ( !$ok )
        {
            @unlink( $tmp );
            throw new expContentJobException( "The content job file $file could not be written completely (disk full?)." );
        }
        @chmod( $tmp, 0660 );
        self::fixOwner( $tmp );
        if ( !@rename( $tmp, $file ) )
        {
            @unlink( $tmp );
            throw new expContentJobException( "The content job file $file cannot be replaced." );
        }
    }

    /**
     * Writes the job (a new job, or a replacement under update()).
     *
     * @param string $id
     * @param array $data
     */
    public static function write( $id, array $data )
    {
        self::writeJSON( self::path( $id ), $data );
    }

    /**
     * Runs $callback with an exclusive lock on $name in the store's directory (blocking).
     *
     * @param string $name lock file name, e.g. 'locks' or a job id
     * @param callable $callback
     * @return mixed what $callback returns
     */
    public static function withLock( $name, $callback )
    {
        $file = self::directory() . '/' . $name . '.lock';
        $fh = @fopen( $file, 'c' );
        if ( !$fh )
            throw new expContentJobException( "The lock file $file cannot be opened." );
        self::fixOwner( $file );
        if ( !flock( $fh, LOCK_EX ) )
        {
            fclose( $fh );
            throw new expContentJobException( "The lock file $file cannot be locked." );
        }
        try
        {
            return call_user_func( $callback );
        }
        finally
        {
            flock( $fh, LOCK_UN );
            fclose( $fh );
        }
    }

    /**
     * Read, change and write a job under its lock. $callback gets the data by reference and returns false to
     * leave the file unchanged.
     *
     * @param string $id
     * @param callable $callback function( array &$data ) : bool|null
     * @return array|null the data as written (or as read when nothing was written), null when there is no job
     */
    public static function update( $id, $callback )
    {
        if ( !self::validID( $id ) )
            return null;
        return self::withLock( $id, function () use ( $id, $callback ) {
            $data = self::read( $id );
            if ( $data === null )
                return null;
            $result = call_user_func_array( $callback, array( &$data ) );
            if ( $result !== false )
                self::write( $id, $data );
            return $data;
        } );
    }

    /**
     * The ids of all jobs, newest first.
     *
     * @return string[]
     */
    public static function ids()
    {
        $ids = array();
        foreach ( (array) glob( self::directory() . '/*.json' ) as $file )
        {
            $id = basename( $file, '.json' );
            if ( self::validID( $id ) && substr( $id, -6 ) !== '.nodes' && strpos( $id, '.' ) === false )
                $ids[] = $id;
        }
        rsort( $ids );
        return $ids;
    }

    /**
     * Appends lines to a file of the job ('.log', '.map'), under an exclusive lock, flushed to disk.
     *
     * @param string $id
     * @param string $suffix
     * @param string[] $lines
     */
    public static function append( $id, $suffix, array $lines )
    {
        if ( !$lines )
            return;
        $file = self::path( $id, $suffix );
        $new = !is_file( $file );
        $fh = @fopen( $file, 'a' );
        if ( !$fh )
            throw new expContentJobException( "The content job file $file cannot be written." );
        flock( $fh, LOCK_EX );
        $text = implode( "\n", $lines ) . "\n";
        $ok = fwrite( $fh, $text ) === strlen( $text );
        fflush( $fh );
        if ( $suffix !== '.log' && function_exists( 'fsync' ) )
            @fsync( $fh );
        flock( $fh, LOCK_UN );
        fclose( $fh );
        if ( $new )
        {
            @chmod( $file, 0660 );
            self::fixOwner( $file );
        }
        if ( !$ok )
            throw new expContentJobException( "The content job file $file could not be written completely (disk full?)." );
    }

    /**
     * The lines of a job file; the last $last lines only when $last > 0.
     *
     * @param string $id
     * @param string $suffix
     * @param int $last
     * @return string[]
     */
    public static function lines( $id, $suffix, $last = 0 )
    {
        if ( !self::validID( $id ) )
            return array();
        $file = self::path( $id, $suffix );
        if ( !is_file( $file ) )
            return array();
        if ( $last > 0 )
        {
            // the end of the file only: a log can be long
            $size = filesize( $file );
            $fh = fopen( $file, 'r' );
            $chunk = min( $size, max( 8192, $last * 400 ) );
            fseek( $fh, $size - $chunk );
            $text = (string) fread( $fh, $chunk );
            fclose( $fh );
            $lines = preg_split( '/\r?\n/', rtrim( $text, "\n" ) );
            if ( $chunk < $size )
                array_shift( $lines );
            return array_slice( $lines, -$last );
        }
        $text = rtrim( (string) file_get_contents( $file ), "\n" );
        return $text === '' ? array() : preg_split( '/\r?\n/', $text );
    }

    /**
     * Reads a JSON side file of a job ('.nodes.json').
     *
     * @param string $id
     * @param string $suffix
     * @return mixed|null
     */
    public static function readSide( $id, $suffix )
    {
        $file = self::path( $id, $suffix );
        if ( !is_file( $file ) )
            return null;
        return json_decode( (string) file_get_contents( $file ), true );
    }

    /**
     * Writes a JSON side file of a job atomically.
     *
     * @param string $id
     * @param string $suffix
     * @param mixed $data
     */
    public static function writeSide( $id, $suffix, $data )
    {
        self::writeJSON( self::path( $id, $suffix ), $data );
    }

    /**
     * Removes a job and its files (retention of finished jobs).
     *
     * @param string $id
     */
    public static function remove( $id )
    {
        if ( !self::validID( $id ) )
            return;
        $dir = self::directory();
        foreach ( array( '.json', '.log', '.map', '.nodes.json', '.out', '.lock', '.run.lock' ) as $suffix )
            if ( is_file( "$dir/$id$suffix" ) )
                @unlink( "$dir/$id$suffix" );
    }

    /**
     * The site user: the owner of the var directory the jobs live in (the installation's index.php may belong
     * to root on a managed host, the var directory never does when the web server writes it).
     *
     * @return array( uid, gid )
     */
    public static function owner()
    {
        if ( self::$owner === null )
        {
            $dir = self::$directory !== null ? self::$directory : null;
            $candidates = array();
            if ( $dir !== null )
            {
                // a directory of its own (tests, a shared cluster directory): the nearest owner that is not root,
                // from the directory up
                for ( $p = $dir; $p !== '/' && $p !== '.' && $p !== ''; $p = dirname( $p ) )
                    $candidates[] = $p;
            }
            else
            {
                $varDir = eZSys::varDirectory();
                if ( $varDir && $varDir[0] !== '/' )
                    $varDir = eZSys::rootDir() . '/' . $varDir;
                $candidates[] = $varDir;
                $candidates[] = eZSys::rootDir() . '/var';
                $candidates[] = eZSys::rootDir() . '/index.php';
            }
            self::$owner = array( 0, 0 );
            foreach ( $candidates as $candidate )
            {
                $p = $candidate;
                while ( $p && !file_exists( $p ) && $p !== '/' )
                    $p = dirname( $p );
                if ( $p && file_exists( $p ) && @fileowner( $p ) !== 0 )
                {
                    self::$owner = array( (int) fileowner( $p ), (int) filegroup( $p ) );
                    break;
                }
            }
        }
        return self::$owner;
    }

    /**
     * Gives a file the site user as owner when the process runs as root; nothing otherwise.
     *
     * @param string $path
     */
    public static function fixOwner( $path )
    {
        if ( !function_exists( 'posix_geteuid' ) || posix_geteuid() !== 0 || !file_exists( $path ) )
            return;
        list( $uid, $gid ) = self::owner();
        if ( $uid === 0 )
            return;
        if ( @fileowner( $path ) !== $uid )
            @chown( $path, $uid );
        if ( @filegroup( $path ) !== $gid )
            @chgrp( $path, $gid );
    }
}
