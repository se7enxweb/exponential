<?php
/**
 * What the tests of the MySQL-wire drivers share: a driver that records what it would send instead of sending it,
 * and the connection of the live tests (EXP_TEST_<PROFILE>_HOST, _PORT, _USER, _PASSWORD or _PASSWORD_FILE,
 * _DATABASE for each of TIDB, VITESS, SINGLESTORE, XPAND, AURORA).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

/**
 * expMySQLWireDB without a server: every statement the driver would run is recorded in $sent, the driver's own
 * statements in $raw; arrayQuery() and rawRow() answer from $answers (a regular expression of the statement =>
 * rows, the first that matches).
 */
class expMySQLWireRecordingDB extends expMySQLWireDB
{
    /** @var string[] */
    public $sent = array();
    /** @var string[] */
    public $raw = array();
    /** @var array pattern => rows (arrayQuery) or a row or a list of rows to hand out one by one (rawRow) */
    public $answers = array();
    /** @var int */
    public $reopened = 0;

    public function __construct( array $settings = array(), $connected = true )
    {
        $this->WireSettings = self::settings( $settings );
        $this->Profile = expMySQLWireProfile::normalizeName( $this->WireSettings['Profile'] !== '' ? $this->WireSettings['Profile'] : static::PROFILE );
        expMySQLWireProfile::get( $this->Profile );
        $this->IsConnected = $connected;
        $this->DBConnection = false;
        $this->DBWriteConnection = false;
        $this->DB = 'site';
        $this->TransactionCounter = 0;
    }

    public function setUp()
    {
        $this->setUpSession( false );
    }

    protected function runStatement( $sql, $server )
    {
        $this->sent[] = $sql;
        return true;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        $this->sent[] = $sql;
        foreach ( $this->answers as $pattern => $rows )
            if ( preg_match( $pattern, $sql ) )
                return $rows;
        return array();
    }

    function escapeString( $str )
    {
        return eZMySQLiDB::escapeStringWithoutConnection( (string)$str );
    }

    protected function rawQuery( $connection, $sql )
    {
        $this->raw[] = $sql;
        return true;
    }

    protected function rawRow( $connection, $sql )
    {
        $this->raw[] = $sql;
        foreach ( $this->answers as $pattern => $row )
        {
            if ( !preg_match( $pattern, $sql ) )
                continue;
            if ( isset( $row['sequence'] ) )
                return array_shift( $this->answers[$pattern]['sequence'] );
            return $row;
        }
        return null;
    }

    protected function reopenPrimary( $waitSeconds )
    {
        ++$this->reopened;
    }

    public function sentWithout( $pattern )
    {
        return array_values( array_filter( $this->sent, function ( $sql ) use ( $pattern ) { return !preg_match( $pattern, $sql ); } ) );
    }
}

class expMySQLWireTestKit
{
    /** The live profiles and the driver class of each */
    const LIVE = array( 'tidb' => 'expTiDBDB', 'vitess' => 'expVitessDB', 'singlestore' => 'expSingleStoreDB',
                        'xpand' => 'expXpandDB', 'aurora' => 'expAuroraMySQLDB' );

    /**
     * The live server of a profile from the environment, null when none is set.
     *
     * @param string $profile
     * @return array|null host, port, user, password, database
     */
    public static function live( $profile )
    {
        $prefix = 'EXP_TEST_' . strtoupper( $profile ) . '_';
        $host = getenv( $prefix . 'HOST' );
        if ( $host === false || $host === '' )
            return null;
        $password = getenv( $prefix . 'PASSWORD' );
        $file = getenv( $prefix . 'PASSWORD_FILE' );
        if ( ( $password === false || $password === '' ) && $file !== false && $file !== '' && is_readable( $file ) )
            $password = trim( (string)file_get_contents( $file ) );
        $facts = expMySQLWireProfile::get( $profile );
        $database = getenv( $prefix . 'DATABASE' );
        return array(
            'host' => $host,
            'port' => getenv( $prefix . 'PORT' ) ?: (string)$facts['port'],
            'user' => getenv( $prefix . 'USER' ) ?: 'root',
            'password' => $password === false ? '' : (string)$password,
            'database' => $database !== false && $database !== '' ? $database : 'exptest_mysqlwire',
        );
    }

    /**
     * eZDB parameters.
     *
     * @param array $overrides
     * @return array
     */
    public static function parameters( array $overrides = array() )
    {
        return $overrides + array(
            'server' => '127.0.0.1', 'port' => 3306, 'user' => 'root', 'password' => '', 'database' => 'exptest_mysqlwire',
            'use_slave_server' => false, 'slave_server' => null, 'slave_port' => null, 'slave_user' => null, 'slave_password' => null,
            'slave_database' => null, 'charset' => 'utf-8', 'is_internal_charset' => true, 'socket' => false,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => false, 'show_errors' => false,
        );
    }

    /**
     * A driver connected to the live server of the profile, its database there and empty of the test's tables; a
     * reason to skip as a string when there is none.
     *
     * @param string $profile
     * @return expMySQLWireDB|string
     */
    public static function liveDriver( $profile )
    {
        if ( !extension_loaded( 'mysqli' ) )
            return 'The mysqli extension is not loaded';
        $live = self::live( $profile );
        if ( $live === null )
            return 'No ' . $profile . ' server for the live tests: set EXP_TEST_' . strtoupper( $profile ) . '_HOST (and _PORT, _USER, _PASSWORD)';
        $class = self::LIVE[$profile];
        $base = array( 'server' => $live['host'], 'port' => $live['port'], 'user' => $live['user'], 'password' => $live['password'] );
        try
        {
            $server = new $class( self::parameters( array( 'database' => null ) + $base ) );
            if ( !$server->isConnected() )
                return "The $profile server does not answer";
            $databases = $server->availableDatabases();
            if ( !is_array( $databases ) || !in_array( $live['database'], $databases ) )
                $server->createDatabase( $live['database'] );
            $server->close();
            $db = new $class( self::parameters( array( 'database' => $live['database'] ) + $base ) );
            return $db->isConnected() ? $db : "The $profile server does not answer for the database {$live['database']}";
        }
        catch ( Exception $e )
        {
            return "The $profile server does not answer: " . $e->getMessage();
        }
    }

    /**
     * The statements of a .sql file of the kernel, split at the semicolons that end a line.
     *
     * @param string $file
     * @return string[]
     */
    public static function statements( $file )
    {
        $text = (string)file_get_contents( $file );
        $text = preg_replace( '/^\s*--.*$/m', '', $text );
        $statements = array();
        foreach ( preg_split( '/;\s*$/m', $text ) as $statement )
            if ( trim( $statement ) !== '' )
                $statements[] = trim( $statement );
        return $statements;
    }

    /**
     * Saves and replaces the debug globals; restoreDebug() puts them back.
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
        eZDBQueryCache::setSettings( array( 'mode' => 'off' ) );
        return $saved;
    }

    /**
     * @param array $saved
     */
    public static function restoreDebug( array $saved )
    {
        eZDBQueryCache::setSettings( null );
        foreach ( $saved as $key => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $value[0];
        }
    }
}
