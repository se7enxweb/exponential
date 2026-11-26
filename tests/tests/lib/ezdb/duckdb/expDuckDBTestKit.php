<?php
/**
 * Shared pieces of the DuckDB driver tests: the driver's files, quiet debug output, a stand-in for the library binding
 * (expDuckDBFakeEngine) and the live target from the environment:
 *
 *   EXP_TEST_DUCKDB_LIBRARY   libduckdb (libduckdb.so ...) for the live tests; they skip without it or without FFI
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'lib/ezdb/classes/expdbsqlhelpers.php', 'lib/ezdb/classes/expduckdbffi.php', 'lib/ezdb/classes/expduckdbdialect.php',
                 'lib/ezdb/classes/expduckdbdb.php', 'lib/ezdbschema/classes/ezdbschemainterface.php', 'lib/ezdbschema/classes/ezpgsqlschema.php',
                 'lib/ezdbschema/classes/expduckdbschema.php' ) as $file )
    require_once dirname( __DIR__, 5 ) . '/' . $file;

class expDuckDBTestKit
{
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
     * The library for the live tests, or null (no FFI, no EXP_TEST_DUCKDB_LIBRARY).
     *
     * @return string|null
     */
    public static function liveLibrary()
    {
        $library = (string)getenv( 'EXP_TEST_DUCKDB_LIBRARY' );
        if ( $library === '' || !expDuckDBFFI::ffiUsable() )
            return null;
        return $library;
    }

    /**
     * A driver on the stand-in engine.
     *
     * @param expDuckDBFakeEngine $engine
     * @param array $settings [DuckDBSettings] overrides
     * @param string $database
     * @return expDuckDBDB
     */
    public static function driver( expDuckDBFakeEngine $engine, array $settings = array(), $database = '/tmp/exp-duckdb-test-none/site.duckdb' )
    {
        return new expDuckDBDB( array( 'database' => $database, 'duckdb_engine' => $engine,
                                       'duckdb' => $settings + array( 'LockWait' => 0 ) ) );
    }

    /**
     * A file name in the system's temporary directory for one live test.
     *
     * @param string $name
     * @return string
     */
    public static function tempFile( $name )
    {
        $dir = sys_get_temp_dir() . '/exp-duckdb-tests-' . getmypid();
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0700, true );
        return $dir . '/' . $name . '-' . bin2hex( random_bytes( 4 ) ) . '.duckdb';
    }

    /**
     * Removes the files tempFile() made.
     */
    public static function removeTempFiles()
    {
        $dir = sys_get_temp_dir() . '/exp-duckdb-tests-' . getmypid();
        if ( !is_dir( $dir ) )
            return;
        foreach ( (array)glob( $dir . '/*' ) as $file )
            @unlink( $file );
        @rmdir( $dir );
    }
}

/**
 * The library binding without a library: records what is sent and answers from a script.
 */
class expDuckDBFakeEngine
{
    public $Error = '';
    public $ErrorType = 0;

    /** @var array[] every open: array( path, config ) */
    public $Opens = array();

    /** @var string[] every statement sent */
    public $Sent = array();

    /** @var int closes */
    public $Closes = 0;

    /** @var array statement pattern (regex) => result array or array( 'error' => message, 'type' => int ) */
    public $Answers = array();

    /** @var array errors for the next opens (message), consumed one by one */
    public $OpenErrors = array();

    protected $Open = false;

    public function open( $path, array $config = array() )
    {
        $this->Opens[] = array( $path, $config );
        if ( $this->OpenErrors )
        {
            $this->Error = array_shift( $this->OpenErrors );
            $this->ErrorType = expDuckDBFFI::ERROR_IO;
            return false;
        }
        $this->Error = '';
        $this->Open = true;
        return true;
    }

    public function isOpen()
    {
        return $this->Open;
    }

    public function close()
    {
        if ( $this->Open )
            $this->Closes++;
        $this->Open = false;
    }

    public function libraryVersion()
    {
        return 'v2.0.1';
    }

    public function query( $sql, $wantRows = true )
    {
        $this->Sent[] = $sql;
        $this->Error = '';
        $this->ErrorType = 0;
        foreach ( $this->Answers as $pattern => $answer )
        {
            if ( !preg_match( $pattern, $sql ) )
                continue;
            if ( isset( $answer['error'] ) )
            {
                $this->Error = $answer['error'];
                $this->ErrorType = isset( $answer['type'] ) ? $answer['type'] : 0;
                return false;
            }
            return $answer + array( 'columns' => array(), 'rows' => array(), 'changed' => 0 );
        }
        return array( 'columns' => array(), 'rows' => array(), 'changed' => 0 );
    }

    /**
     * The last open's access mode.
     *
     * @return string|null
     */
    public function lastMode()
    {
        $last = end( $this->Opens );
        return $last && isset( $last[1]['access_mode'] ) ? $last[1]['access_mode'] : null;
    }
}
