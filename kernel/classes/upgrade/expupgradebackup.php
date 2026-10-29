<?php
/**
 * File containing the expUpgradeBackup class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The backup exp:upgrade makes before its first write, and exp:upgrade:backup makes on its own: one directory
 * var/backup/upgrade/<YYYYmmdd-HHMMSS>/ with
 *
 *   database.sqlite   SQLite: VACUUM INTO (a consistent copy while the site runs), the file copied as a fallback
 *   database.sql      MySQL/MariaDB (mysqldump --single-transaction) and PostgreSQL (pg_dump)
 *   mongodb/          MongoDB (mongodump)
 *   settings/override a copy of the installation's settings overrides
 *   manifest.json     what is there, the engine, the time, the version the database said
 *
 * Oracle has no dump here: the result says so and the run refuses to write without --no-backup (make one with
 * expdp first). The password of a connection is passed to the dump tool in a file readable by its owner only (MySQL)
 * or its environment (PostgreSQL), never on the command line, and is never printed.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeBackup
{
    /**
     * The default parent directory.
     *
     * @return string
     */
    public static function baseDir()
    {
        return rtrim( eZSys::rootDir(), '/' ) . '/var/backup/upgrade';
    }

    /**
     * Makes a backup.
     *
     * @param eZDBInterface $db
     * @param string|null $dir the parent directory (baseDir() when null); the backup goes into <dir>/<time>
     * @return array( 'ok' => bool, 'dir' => string, 'files' => string[], 'messages' => string[] )
     */
    public static function create( $db, $dir = null )
    {
        $parent = rtrim( $dir ?: self::baseDir(), '/' );
        $target = $parent . '/' . date( 'Ymd-His' );
        $n = 1;
        while ( file_exists( $target ) )
            $target = $parent . '/' . date( 'Ymd-His' ) . '-' . ( ++$n );
        $result = array( 'ok' => false, 'dir' => $target, 'files' => array(), 'messages' => array() );
        if ( !@mkdir( $target, 0700, true ) && !is_dir( $target ) )
        {
            $result['messages'][] = "cannot create $target";
            return $result;
        }
        $engine = expKernelTableSchema::type( $db );
        $ok = false;
        try
        {
            switch ( $engine )
            {
                case 'sqlite':
                    $ok = self::sqlite( $db, $target, $result );
                    break;
                case 'mysql':
                    $ok = self::mysql( $db, $target, $result );
                    break;
                case 'postgresql':
                    $ok = self::postgresql( $db, $target, $result );
                    break;
                case 'mongodb':
                    $ok = self::mongodb( $db, $target, $result );
                    break;
                default:
                    $result['messages'][] = "no built-in dump for $engine: make one with the engine's own tool (Oracle: expdp) and run with --no-backup";
            }
        }
        catch ( Throwable $e )
        {
            $result['messages'][] = 'the database dump failed: ' . $e->getMessage();
            $ok = false;
        }
        $override = rtrim( eZSys::rootDir(), '/' ) . '/settings/override';
        if ( is_dir( $override ) )
        {
            $settings = $target . '/settings';
            eZDir::mkdir( $settings, 0700, true );
            eZDir::copy( $override, $settings, true, true );
            $result['files'][] = 'settings/override';
        }
        $stored = expUpgradeStepSiteVersion::stored( $db );
        file_put_contents( $target . '/manifest.json', json_encode( array(
            'format' => 'exp-upgrade-backup/1',
            'created' => date( 'c' ),
            'engine' => $engine,
            'database_version' => $stored,
            'files' => $result['files'],
            'complete' => $ok,
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
        @chmod( $target . '/manifest.json', 0600 );
        $result['ok'] = $ok;
        return $result;
    }

    protected static function sqlite( $db, $target, array &$result )
    {
        $file = $target . '/database.sqlite';
        $quoted = "'" . str_replace( "'", "''", $file ) . "'";
        $before = expAuditIndexSchema::errorHandling( $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $db->query( "VACUUM INTO $quoted" );
        }
        catch ( Throwable $e )
        {
            $source = eZSQLite3DB::filePath( (string)$db->DB );
            if ( $source[0] !== '/' )
                $source = rtrim( eZSys::rootDir(), '/' ) . '/' . $source;
            if ( !is_file( $source ) || !copy( $source, $file ) )
            {
                $result['messages'][] = 'cannot copy the SQLite database: ' . $e->getMessage();
                $db->setErrorHandling( $before );
                return false;
            }
            $result['messages'][] = 'VACUUM INTO is not supported by this SQLite; the file was copied';
        }
        $db->setErrorHandling( $before );
        @chmod( $file, 0600 );
        $result['files'][] = 'database.sqlite';
        return is_file( $file ) && filesize( $file ) > 0;
    }

    protected static function mysql( $db, $target, array &$result )
    {
        $file = $target . '/database.sql';
        $cnf = $target . '/.my.cnf';
        $lines = array( '[client]', 'user=' . self::cnf( $db->User ), 'password=' . self::cnf( $db->Password ) );
        if ( $db->SocketPath )
            $lines[] = 'socket=' . self::cnf( $db->SocketPath );
        else
            $lines[] = 'host=' . self::cnf( $db->Server ?: 'localhost' );
        if ( $db->Port )
            $lines[] = 'port=' . (int)$db->Port;
        file_put_contents( $cnf, '' );
        chmod( $cnf, 0600 );
        file_put_contents( $cnf, implode( "\n", $lines ) . "\n" );
        $cmd = 'mysqldump --defaults-extra-file=' . escapeshellarg( $cnf ) . ' --single-transaction --routines --triggers '
             . escapeshellarg( (string)$db->DB ) . ' > ' . escapeshellarg( $file ) . ' 2>&1';
        exec( $cmd, $out, $code );
        @unlink( $cnf );
        return self::finish( $file, $code, 'mysqldump', $result );
    }

    protected static function postgresql( $db, $target, array &$result )
    {
        $file = $target . '/database.sql';
        $cmd = 'pg_dump --no-owner -h ' . escapeshellarg( $db->Server ?: 'localhost' )
             . ( $db->Port ? ' -p ' . (int)$db->Port : '' )
             . ' -U ' . escapeshellarg( (string)$db->User ) . ' -f ' . escapeshellarg( $file ) . ' ' . escapeshellarg( (string)$db->DB );
        // the password in the environment of pg_dump only, never on a command line another user can read
        $env = getenv();
        $env['PGPASSWORD'] = (string)$db->Password;
        $process = proc_open( $cmd, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, null, $env );
        if ( !is_resource( $process ) )
        {
            $result['messages'][] = 'cannot start pg_dump';
            return false;
        }
        $err = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $code = proc_close( $process );
        if ( $code !== 0 && $err !== '' )
            $result['messages'][] = 'pg_dump: ' . preg_replace( '/\s+/', ' ', trim( $err ) );
        return self::finish( $file, $code, 'pg_dump', $result );
    }

    protected static function mongodb( $db, $target, array &$result )
    {
        $dir = $target . '/mongodb';
        $uri = 'mongodb://' . ( $db->User !== '' && $db->User !== null ? rawurlencode( $db->User ) . ':' . rawurlencode( (string)$db->Password ) . '@' : '' )
             . ( $db->Server ?: 'localhost' ) . ( $db->Port ? ':' . (int)$db->Port : '' ) . '/' . rawurlencode( (string)$db->DB );
        $cnf = $target . '/.mongo.yaml';
        file_put_contents( $cnf, '' );
        chmod( $cnf, 0600 );
        file_put_contents( $cnf, 'uri: "' . str_replace( '"', '\\"', $uri ) . "\"\n" );
        exec( 'mongodump --config=' . escapeshellarg( $cnf ) . ' --out=' . escapeshellarg( $dir ) . ' 2>&1', $out, $code );
        @unlink( $cnf );
        if ( $code !== 0 || !is_dir( $dir ) )
        {
            $result['messages'][] = 'mongodump failed (exit ' . $code . ')';
            return false;
        }
        $result['files'][] = 'mongodb/';
        return true;
    }

    protected static function finish( $file, $code, $tool, array &$result )
    {
        @chmod( $file, 0600 );
        if ( $code !== 0 || !is_file( $file ) || filesize( $file ) === 0 )
        {
            $tail = is_file( $file ) ? trim( (string)file_get_contents( $file, false, null, max( 0, filesize( $file ) - 400 ) ) ) : '';
            $result['messages'][] = "$tool failed (exit $code)" . ( $tail !== '' ? ': ' . preg_replace( '/\s+/', ' ', $tail ) : '' );
            return false;
        }
        $result['files'][] = basename( $file );
        return true;
    }

    /** A value for an option file: quoted, so a # or ; in a password is kept. */
    protected static function cnf( $value )
    {
        return '"' . str_replace( array( '\\', '"' ), array( '\\\\', '\\"' ), (string)$value ) . '"';
    }

    /**
     * The backups made so far, newest first: name => manifest.
     *
     * @param string|null $dir
     * @return array
     */
    public static function all( $dir = null )
    {
        $parent = rtrim( $dir ?: self::baseDir(), '/' );
        $out = array();
        foreach ( glob( $parent . '/*/manifest.json' ) ?: array() as $manifest )
        {
            $data = json_decode( (string)file_get_contents( $manifest ), true );
            $out[basename( dirname( $manifest ) )] = is_array( $data ) ? $data : array();
        }
        krsort( $out );
        return $out;
    }
}
