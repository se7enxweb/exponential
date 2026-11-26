<?php
/**
 * Shared pieces of the ODBC and MS Access driver tests: the drivers' files, quiet debug output, and the live targets
 * from the environment:
 *
 *   EXP_TEST_ODBC_<DIALECT>           the connection string keywords after the driver (Server=...;Database=...), for
 *                                     SQLSERVER, POSTGRESQL, MYSQL, SQLITE (a file name is made when it is "1")
 *   EXP_TEST_ODBC_<DIALECT>_USER, _PASSWORD, _DRIVER (default expODBCDB::defaultDriver())
 *   EXP_TEST_ODBC_LAYER               pdo_odbc, odbc or both (the default: every layer PHP has)
 *   EXP_TEST_MSACCESS_FILE            a writable .accdb or .mdb for the MS Access live tests (Windows, ACE)
 *   EXP_TEST_MSACCESS_DRIVER          default: Microsoft Access Driver (*.mdb, *.accdb)
 *   EXP_TEST_ACCESS_FIXTURES          a directory with Access files to import (nwind.mdb, ASampleDatabase.accdb ...)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'expsqlserverlexer', 'expsqlserverdialect', 'expdbsqlhelpers', 'expdbunsupportedexception', 'expmsaccessdialect',
                 'expodbcnativeconnection', 'exppdodb', 'expodbcdb', 'expmsaccessdb' ) as $file )
    require_once dirname( __DIR__, 5 ) . '/lib/ezdb/classes/' . $file . '.php';

class expODBCTestKit
{
    /** The dialects a live server can be given for */
    const LIVE_DIALECTS = array( 'sqlserver', 'postgresql', 'mysql', 'sqlite' );

    /**
     * Silences eZDebug for a test; returns what to restore.
     *
     * @return array
     */
    public static function quietDebug()
    {
        $saved = array();
        foreach ( array( 'eZDebugGlobalInstance', 'eZDebugEnabled' ) as $key )
            $saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $GLOBALS['eZDebugGlobalInstance'] = new eZDebug();
        $GLOBALS['eZDebugEnabled'] = false;
        return $saved;
    }

    public static function restoreDebug( array $saved )
    {
        foreach ( $saved as $key => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $value[0];
        }
    }

    /**
     * The layers to test: the ones PHP has, narrowed by EXP_TEST_ODBC_LAYER.
     *
     * @return string[]
     */
    public static function layers()
    {
        $have = array();
        if ( class_exists( 'PDO' ) && in_array( 'odbc', PDO::getAvailableDrivers(), true ) )
            $have[] = 'pdo_odbc';
        if ( function_exists( 'odbc_connect' ) )
            $have[] = 'odbc';
        $wanted = (string)getenv( 'EXP_TEST_ODBC_LAYER' );
        if ( $wanted !== '' && $wanted !== 'both' )
            $have = array_values( array_intersect( $have, array( $wanted ) ) );
        return $have;
    }

    /**
     * The configured live targets: "dialect/layer" => array( dialect, layer, connection string, user, password, driver ).
     *
     * @return array
     */
    public static function targets()
    {
        $targets = array();
        foreach ( self::LIVE_DIALECTS as $dialect )
        {
            $key = 'EXP_TEST_ODBC_' . strtoupper( $dialect );
            $keywords = (string)getenv( $key );
            if ( $keywords === '' )
                continue;
            if ( $dialect === 'sqlite' && $keywords === '1' )
                $keywords = 'Database=' . self::sqliteFile();
            $driver = (string)getenv( $key . '_DRIVER' );
            foreach ( self::layers() as $layer )
                $targets["$dialect/$layer"] = array( $dialect, $layer, $keywords, (string)getenv( $key . '_USER' ), (string)getenv( $key . '_PASSWORD' ),
                                                     $driver !== '' ? $driver : expODBCDB::defaultDriver( $dialect ) );
        }
        return $targets;
    }

    /** @var string|null the SQLite file of this process's live tests */
    private static $sqliteFile = null;

    /**
     * One SQLite file for the live tests of this process, removed when it ends.
     *
     * @return string
     */
    public static function sqliteFile()
    {
        if ( self::$sqliteFile === null )
        {
            self::$sqliteFile = self::tempFile( 'odbc-live', 'db' );
            $file = self::$sqliteFile;
            register_shutdown_function( function () use ( $file ) {
                foreach ( array( '', '-journal', '-wal', '-shm' ) as $suffix )
                    if ( is_file( $file . $suffix ) )
                        @unlink( $file . $suffix );
            } );
        }
        return self::$sqliteFile;
    }

    /**
     * A connection to a target.
     *
     * @param array $target
     * @return expODBCDB
     */
    public static function connect( array $target )
    {
        list( $dialect, $layer, $keywords, $user, $password, $driver ) = $target;
        $db = new expODBCDB( array( 'user' => $user, 'password' => $password, 'connect_retries' => 0,
                                    'odbc' => array( 'Dialect' => $dialect, 'Driver' => $driver, 'ConnectionString' => $keywords, 'Layer' => $layer ) ) );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        return $db;
    }

    /**
     * A file name in var/tmp (not created).
     *
     * @param string $name
     * @param string $extension
     * @return string
     */
    public static function tempFile( $name, $extension )
    {
        $dir = dirname( __DIR__, 5 ) . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        return $dir . '/' . $name . '-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) ) . '.' . $extension;
    }

    /**
     * An expODBCDB (or subclass) with a dialect and no connection, for the pure methods.
     *
     * @param string $dialect
     * @param string $class
     * @param string $connectionString
     * @return expODBCDB
     */
    public static function unconnected( $dialect, $class = 'expODBCDB', $connectionString = '' )
    {
        $reflection = new ReflectionClass( $class );
        $db = $reflection->newInstanceWithoutConstructor();
        foreach ( array( 'Dialect' => $dialect, 'ConnectionString' => $connectionString, 'IsConnected' => false ) as $name => $value )
        {
            $property = new ReflectionProperty( 'expODBCDB', $name );
            if ( PHP_VERSION_ID < 80100 )
                $property->setAccessible( true );
            $property->setValue( $db, $value );
        }
        return $db;
    }
}
?>
