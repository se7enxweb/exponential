<?php
/**
 * File containing the expLogRotator class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The one implementation of log rotation in the kernel (doc/eng-us/features/6.0/log-rotation.md). eZDebug and eZLog
 * rotate a log through it when it outgrows site.ini [FileSettings] LogFileMaxSize, the log viewer's rotate action
 * and the command line (./console exp:logs --rotate) rotate on demand, and prune() removes old archives.
 *
 * A rotation renames <file>.N-1 to <file>.N ... <file> to <file>.1, removes what is beyond the number of archives
 * kept, and creates the new, empty <file> at once with the owner, group and mode of the old one, so a log written
 * by two users (Velocity runs as root, PHP-FPM as the site's user) never becomes a file the other cannot write.
 * Archives from .2 on are compressed to <file>.N.gz when asked (.1 never is: a writer that opened the file before
 * the rename may still be appending to it).
 *
 * Two processes rotating the same file at once are serialized by an exclusive lock on the file being rotated
 * (flock); the second one finds the file already renamed (another inode) and does nothing. Writers append without
 * locking, as before, so a lock never blocks a request's logging.
 *
 * Pure file system: no settings are read here (the callers pass them), so it works on the earliest error paths; modes go
 * through eZFile::fileMode(), so the EZP_FILE_MODE_MAX limit of config.php applies.
 */
class expLogRotator
{
    /** The longest a rotation waits for another one of the same file, in microseconds */
    const LOCK_WAIT = 2000000;

    /** The most archives a rotation may keep: a limit against settings that would scan forever */
    const MAX_KEEP = 999;

    /**
     * Rotates a log file.
     *
     * @param string $path the current log file
     * @param int $keep how many archives are kept (<file>.1 .. <file>.$keep); 0: the file is not rotated
     * @param array $options
     *   - compress (bool, false): compress the archives from .2 on to .N.gz
     *   - minSize (int, 0): rotate only when the file is larger than this (checked again under the lock, so two
     *     processes that both saw the file too big rotate it once)
     *   - mode (int|null): the mode of a new file when the old one's cannot be read (default 0666 & ~umask)
     *   - wait (int, LOCK_WAIT): how long to wait for another rotation of the same file, in microseconds
     * @return array ok (bool), error ('' | disabled | missing | not_a_file | link | locked | rename | moved: another
     *               process rotated it meanwhile | skipped: not larger than minSize),
     *               renamed (list of "a -> b", "a -> -" for a removed archive, "a -> a.gz" for a compressed one)
     */
    public static function rotate( $path, $keep, array $options = array() )
    {
        $result = array( 'ok' => false, 'error' => '', 'renamed' => array() );
        $keep = min( (int)$keep, self::MAX_KEEP );
        if ( $keep <= 0 )
        {
            $result['error'] = 'disabled';
            return $result;
        }
        $path = (string)$path;
        if ( $path === '' || strpos( $path, "\0" ) !== false )
        {
            $result['error'] = 'missing';
            return $result;
        }
        if ( is_link( $path ) )
        {
            $result['error'] = 'link';
            return $result;
        }
        clearstatcache( true, $path );
        if ( !file_exists( $path ) )
        {
            $result['error'] = 'missing';
            return $result;
        }
        if ( !is_file( $path ) )
        {
            $result['error'] = 'not_a_file';
            return $result;
        }
        $compress = !empty( $options['compress'] ) && function_exists( 'gzopen' );
        $minSize = isset( $options['minSize'] ) ? (int)$options['minSize'] : 0;

        $handle = @fopen( $path, 'r' );
        if ( $handle === false )
        {
            $result['error'] = 'locked';
            return $result;
        }
        $locked = false;
        $waited = 0;
        $wait = isset( $options['wait'] ) ? max( 0, (int)$options['wait'] ) : self::LOCK_WAIT;
        while ( !( $locked = flock( $handle, LOCK_EX | LOCK_NB ) ) && $waited < $wait )
        {
            usleep( 20000 );
            $waited += 20000;
        }
        if ( !$locked )
        {
            fclose( $handle );
            $result['error'] = 'locked';
            return $result;
        }

        try
        {
            // Under the lock: is the file still the one that was opened (another process may have rotated it)?
            clearstatcache( true, $path );
            $held = fstat( $handle );
            $now = @stat( $path );
            if ( $now === false || $held === false || $now['ino'] !== $held['ino'] || $now['dev'] !== $held['dev'] )
            {
                $result['error'] = 'moved';
                return $result;
            }
            if ( $minSize > 0 && $now['size'] <= $minSize )
            {
                $result['error'] = 'skipped';
                return $result;
            }

            // The archives beyond the ones kept go, the others move one up: .N-1 -> .N ... .1 -> .2
            foreach ( array_merge( array( $keep ), self::rotationNumbersAbove( $path, $keep ) ) as $n )
            {
                foreach ( array( $path . '.' . $n, $path . '.' . $n . '.gz' ) as $old )
                {
                    if ( file_exists( $old ) && @unlink( $old ) )
                        $result['renamed'][] = basename( $old ) . ' -> -';
                }
            }
            for ( $i = $keep - 1; $i >= 1; --$i )
            {
                foreach ( array( '', '.gz' ) as $suffix )
                {
                    $from = $path . '.' . $i . $suffix;
                    if ( !file_exists( $from ) )
                        continue;
                    $to = $path . '.' . ( $i + 1 ) . $suffix;
                    if ( !@rename( $from, $to ) )
                    {
                        $result['error'] = 'rename';
                        return $result;
                    }
                    $result['renamed'][] = basename( $from ) . ' -> ' . basename( $to );
                }
            }

            if ( !@rename( $path, $path . '.1' ) )
            {
                $result['error'] = 'rename';
                return $result;
            }
            $result['renamed'][] = basename( $path ) . ' -> ' . basename( $path ) . '.1';
            self::recreate( $path, $now, isset( $options['mode'] ) ? $options['mode'] : null );

            if ( $compress )
            {
                for ( $i = 2; $i <= $keep; ++$i )
                {
                    $plain = $path . '.' . $i;
                    if ( file_exists( $plain ) && !file_exists( $plain . '.gz' ) && self::gzip( $plain ) )
                        $result['renamed'][] = basename( $plain ) . ' -> ' . basename( $plain ) . '.gz';
                }
            }
            $result['ok'] = true;
            return $result;
        }
        finally
        {
            flock( $handle, LOCK_UN );
            fclose( $handle );
        }
    }

    /**
     * Creates the new, empty log file with the owner, group and mode of the old one (from its stat), so the
     * user who did not rotate can still write it. Changing the owner needs root; anyone else creates the file as
     * itself with the old mode (0666 by default for logs: site.ini [FileSettings] LogFilePermissions).
     *
     * @param string $path
     * @param array|false $old stat() of the file before it was renamed
     * @param int|null $mode the mode when $old has none
     * @return bool
     */
    public static function recreate( $path, $old, $mode = null )
    {
        $handle = @fopen( $path, 'x' );
        if ( $handle === false )
        {
            // A writer was faster and created it already: leave it, but give it the old owner and mode as well
            if ( !file_exists( $path ) )
                return false;
        }
        else
        {
            fclose( $handle );
        }
        $wantMode = is_array( $old ) && isset( $old['mode'] ) ? ( $old['mode'] & 0777 ) : ( $mode !== null ? ( (int)$mode & 0777 ) : null );
        if ( $wantMode !== null )
            @chmod( $path, eZFile::fileMode( $wantMode ) ); // the old mode, within EZP_FILE_MODE_MAX
        if ( is_array( $old ) && function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
        {
            @chown( $path, (int)$old['uid'] );
            @chgrp( $path, (int)$old['gid'] );
        }
        return true;
    }

    /**
     * Compresses a file to <file>.gz with the same owner, group and mode, and removes the file.
     *
     * @param string $plain
     * @return bool
     */
    public static function gzip( $plain )
    {
        $stat = @stat( $plain );
        $in = @fopen( $plain, 'rb' );
        if ( $stat === false || $in === false )
            return false;
        $target = $plain . '.gz';
        $tmp = $target . '.tmp' . getmypid();
        $out = @gzopen( $tmp, 'wb6' );
        if ( $out === false )
        {
            fclose( $in );
            return false;
        }
        while ( !feof( $in ) )
        {
            $chunk = fread( $in, 1048576 );
            if ( $chunk === false || ( $chunk !== '' && gzwrite( $out, $chunk ) === false ) )
            {
                fclose( $in );
                gzclose( $out );
                @unlink( $tmp );
                return false;
            }
        }
        fclose( $in );
        gzclose( $out );
        if ( !@rename( $tmp, $target ) )
        {
            @unlink( $tmp );
            return false;
        }
        @chmod( $target, eZFile::fileMode( $stat['mode'] & 0777 ) );
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
        {
            @chown( $target, (int)$stat['uid'] );
            @chgrp( $target, (int)$stat['gid'] );
        }
        @unlink( $plain );
        return true;
    }

    /**
     * The archives of a log file that exist: <file>.N and <file>.N.gz, and a bare <file>.gz, by number.
     *
     * @param string $path the current log file (it need not exist)
     * @return array list of rotation (int; -1 for a bare .gz), path, name, size, mtime, gz (bool)
     */
    public static function archives( $path )
    {
        $dir = dirname( $path );
        $base = basename( $path );
        $names = @scandir( $dir );
        $out = array();
        if ( !is_array( $names ) )
            return $out;
        $pattern = '/^' . preg_quote( $base, '/' ) . '(?:\.(\d{1,4}))?(\.gz)?$/';
        foreach ( $names as $name )
        {
            if ( $name === $base || !preg_match( $pattern, $name, $m ) )
                continue;
            $number = isset( $m[1] ) && $m[1] !== '' ? (int)$m[1] : -1;
            $gz = !empty( $m[2] );
            if ( $number === -1 && !$gz )
                continue;
            $file = $dir . '/' . $name;
            if ( is_link( $file ) || !is_file( $file ) )
                continue;
            $stat = @stat( $file );
            $out[] = array( 'rotation' => $number, 'path' => $file, 'name' => $name,
                            'size' => $stat ? (int)$stat['size'] : 0, 'mtime' => $stat ? (int)$stat['mtime'] : 0, 'gz' => $gz );
        }
        usort( $out, function ( $a, $b ) {
            $ra = $a['rotation'] < 0 ? PHP_INT_MAX : $a['rotation'];
            $rb = $b['rotation'] < 0 ? PHP_INT_MAX : $b['rotation'];
            return $ra === $rb ? strcmp( $a['name'], $b['name'] ) : ( $ra < $rb ? -1 : 1 );
        } );
        return $out;
    }

    /**
     * Removes the archives of a log file beyond the limits: a number above $keep, a bare <file>.gz, or (with
     * $maxAge) older than that many seconds. The current file is never removed.
     *
     * @param string $path the current log file
     * @param int $keep the archives kept by number (0: none)
     * @param int $maxAge seconds; 0: no age limit
     * @param bool $dryRun only report
     * @param int|null $now the time (tests)
     * @return array list of name, size, reason (number|age|bare), removed (bool)
     */
    public static function prune( $path, $keep, $maxAge = 0, $dryRun = false, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $keep = max( 0, (int)$keep );
        $out = array();
        foreach ( self::archives( $path ) as $archive )
        {
            $reason = '';
            if ( $archive['rotation'] < 0 )
                $reason = 'bare';
            else if ( $archive['rotation'] > $keep )
                $reason = 'number';
            else if ( $maxAge > 0 && $archive['mtime'] < $now - (int)$maxAge )
                $reason = 'age';
            if ( $reason === '' )
                continue;
            $removed = $dryRun ? false : @unlink( $archive['path'] );
            $out[] = array( 'name' => $archive['name'], 'size' => $archive['size'], 'reason' => $reason, 'removed' => $removed );
        }
        return $out;
    }

    /**
     * The archive numbers above $keep that exist (left from a larger setting), so a rotation removes them too.
     *
     * @param string $path
     * @param int $keep
     * @return int[]
     */
    protected static function rotationNumbersAbove( $path, $keep )
    {
        $numbers = array();
        foreach ( self::archives( $path ) as $archive )
        {
            if ( $archive['rotation'] > $keep )
                $numbers[$archive['rotation']] = $archive['rotation'];
        }
        return array_values( $numbers );
    }
}
