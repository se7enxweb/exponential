<?php
/**
 * Shared pieces of the SQL Server driver tests: the driver's files, a recording transport that answers from a
 * script instead of a server, the eZDB parameters of a test connection, and the live server's settings
 * (EXP_TEST_SQLSERVER_HOST, _PORT, _USER, _PASSWORD or _PASSWORD_FILE, _DATABASE, _LAYER, _ODBC_DRIVER).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'expsqlserverlexer', 'expsqlserverdialect', 'expsqlservererrormap', 'expsqlservertransport',
                 'expsqlserversqlsrvtransport', 'expsqlserverpdotransport', 'expsqlserverdb' ) as $file )
    require_once dirname( __DIR__, 5 ) . '/lib/ezdb/classes/' . $file . '.php';

/**
 * A transport that records every batch and answers from rules: the first rule whose pattern matches the batch
 * gives the answer (rows, an error, or a callable). Errors can also be queued for the next batches.
 */
class expSQLServerFakeTransport extends expSQLServerTransport
{
    /** @var array[] every batch: array( sql, params ) */
    public $log = array();

    /** @var array[] rules: array( pattern, answer ) */
    public $rules = array();

    /** @var array[] errors for the next batches, in order: array( number, state, message ) or null for success */
    public $queue = array();

    /** @var bool */
    public $open = false;

    /** @var int */
    public $opens = 0;

    /** @var bool whether open() fails */
    public $refuseOpen = false;

    /** @var string */
    public $layerName = 'fake';

    /** @var array the config of the last open() */
    public $config = array();

    /** @var string product version the server reports */
    public $version = '16.0.4325.4';

    /** @var int engine edition the server reports */
    public $edition = 3;

    /** @var int is_read_committed_snapshot_on */
    public $rcsi = 1;

    public function name()
    {
        return $this->layerName;
    }

    public function open( array $config )
    {
        $this->config = $config;
        $this->opens++;
        if ( $this->refuseOpen )
        {
            $this->setError( 18456, '28000', "Login failed for user '" . ( isset( $config['user'] ) ? $config['user'] : '' ) . "' with password " . ( isset( $config['password'] ) ? $config['password'] : '' ) );
            return false;
        }
        $this->open = true;
        return true;
    }

    /**
     * @param string $pattern a regular expression the batch must match
     * @param mixed $answer rows (array of row arrays), an array( 'error' => array( number, state, message ) ),
     *                      an array( 'sets' => ..., 'affected' => n ), or a callable( $sql, $params, $transport )
     */
    public function on( $pattern, $answer )
    {
        array_unshift( $this->rules, array( $pattern, $answer ) );
        return $this;
    }

    public function failNext( $number, $state = null, $message = 'Simulated error' )
    {
        $this->queue[] = array( $number, $state, $message );
        return $this;
    }

    public function run( $sql, array $params = array() )
    {
        $this->error = null;
        $this->log[] = array( $sql, $params );
        if ( !$this->open )
        {
            $this->setError( null, '08003', 'The connection is closed' );
            return false;
        }
        if ( $this->queue )
        {
            $next = array_shift( $this->queue );
            if ( $next !== null )
            {
                $this->setError( $next[0], $next[1], $next[2] );
                if ( expSQLServerErrorMap::isConnectionLost( expSQLServerErrorMap::category( $next[0], $next[1] ) ) )
                    $this->open = false;
                return false;
            }
        }
        foreach ( $this->rules as $rule )
        {
            if ( !preg_match( $rule[0], $sql ) )
                continue;
            $answer = $rule[1];
            if ( is_callable( $answer ) && !is_array( $answer ) )
                $answer = $answer( $sql, $params, $this );
            if ( $answer === false )
                return false;
            if ( is_array( $answer ) && isset( $answer['error'] ) )
            {
                $this->setError( $answer['error'][0], $answer['error'][1], $answer['error'][2] );
                return false;
            }
            if ( is_array( $answer ) && array_key_exists( 'sets', $answer ) )
                return $answer + array( 'affected' => 0 );
            return array( 'sets' => array( $answer ), 'affected' => 0 );
        }
        if ( strpos( $sql, "SERVERPROPERTY('ProductVersion')" ) !== false )
            return array( 'sets' => array( array( array( 'version' => $this->version, 'edition' => (string)$this->edition, 'edition_name' => 'Developer Edition (64-bit)',
                                                         'rcsi' => (string)$this->rcsi, 'collation' => 'SQL_Latin1_General_CP1_CI_AS', 'spid' => '57' ) ) ), 'affected' => 0 );
        if ( strpos( $sql, 'sys.identity_columns' ) !== false )
            return array( 'sets' => array( array() ), 'affected' => 0 );
        if ( preg_match( '/SELECT CAST\(SCOPE_IDENTITY\(\) AS BIGINT\) AS exp_identity\s*$/', $sql ) )
            return array( 'sets' => array( array( array( 'exp_identity' => '41' ) ) ), 'affected' => 1 );
        return array( 'sets' => array(), 'affected' => 0 );
    }

    public function close()
    {
        $this->open = false;
    }

    public function isOpen()
    {
        return $this->open;
    }

    public function clientVersion()
    {
        return 'fake 1.2.3';
    }

    /**
     * The batches sent so far that match $pattern.
     *
     * @param string $pattern
     * @return string[]
     */
    public function sent( $pattern = '/./' )
    {
        $list = array();
        foreach ( $this->log as $entry )
            if ( preg_match( $pattern, $entry[0] ) )
                $list[] = $entry[0];
        return $list;
    }
}

class expSQLServerTestKit
{
    /**
     * The eZDB parameters of a connection.
     *
     * @param array $overrides
     * @return array
     */
    public static function parameters( array $overrides = array() )
    {
        return $overrides + array(
            'server' => '127.0.0.1', 'port' => 14330, 'user' => 'sa', 'password' => 'Secret-Pass-123', 'database' => 'exptest',
            'use_slave_server' => false, 'slave_server' => null, 'slave_port' => null, 'slave_user' => null, 'slave_password' => null,
            'slave_database' => null, 'charset' => 'utf-8', 'is_internal_charset' => true, 'socket' => false,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => false, 'show_errors' => false,
        );
    }

    /**
     * A driver over a fake transport.
     *
     * @param expSQLServerFakeTransport|null $transport
     * @param array $settings [SQLServerSettings] overrides
     * @param array $parameters eZDB parameter overrides
     * @return expSQLServerDB
     */
    public static function driver( $transport = null, array $settings = array(), array $parameters = array() )
    {
        if ( $transport === null )
            $transport = new expSQLServerFakeTransport();
        $settings += array( 'DeadlockRetryWait' => 0 );
        return new expSQLServerDB( self::parameters( $parameters + array( 'sqlserver_transport' => $transport, 'sqlserver' => $settings ) ) );
    }

    /**
     * The live server's settings, or null when EXP_TEST_SQLSERVER_HOST is not set.
     *
     * @return array|null
     */
    public static function live()
    {
        $host = getenv( 'EXP_TEST_SQLSERVER_HOST' );
        if ( $host === false || $host === '' )
            return null;
        $password = getenv( 'EXP_TEST_SQLSERVER_PASSWORD' );
        if ( $password === false || $password === '' )
        {
            $file = getenv( 'EXP_TEST_SQLSERVER_PASSWORD_FILE' );
            $password = $file && is_readable( $file ) ? trim( (string)file_get_contents( $file ) ) : '';
        }
        return array(
            'host' => $host,
            'port' => getenv( 'EXP_TEST_SQLSERVER_PORT' ) ?: 1433,
            // '-': Windows authentication (LocalDB), no user and password
            'user' => getenv( 'EXP_TEST_SQLSERVER_USER' ) === '-' ? '' : ( getenv( 'EXP_TEST_SQLSERVER_USER' ) ?: 'sa' ),
            'password' => $password,
            'database' => getenv( 'EXP_TEST_SQLSERVER_DATABASE' ) ?: 'exp_sqlserver_test',
            'layer' => getenv( 'EXP_TEST_SQLSERVER_LAYER' ) ?: 'auto',
            'odbc_driver' => getenv( 'EXP_TEST_SQLSERVER_ODBC_DRIVER' ) ?: 'ODBC Driver 18 for SQL Server',
        );
    }

    /** @var bool whether the test database exists (made once per process) */
    private static $databaseReady = false;

    /**
     * A driver on the live server (the test database is made on first use), or the reason it cannot be had.
     *
     * @param array $settings [SQLServerSettings] overrides
     * @param array $parameters eZDB parameter overrides
     * @return expSQLServerDB|string
     */
    public static function liveDriver( array $settings = array(), array $parameters = array() )
    {
        $live = self::live();
        if ( $live === null )
            return 'No SQL Server for the live tests: set EXP_TEST_SQLSERVER_HOST (and _PORT, _USER, _PASSWORD or _PASSWORD_FILE)';
        $layer = expSQLServerTransport::chooseLayer( $live['layer'] );
        if ( $layer === null )
            return "PHP has no SQL Server layer '{$live['layer']}' (sqlsrv, pdo_sqlsrv, PDO_ODBC, pdo_dblib)";
        $settings += array( 'Layer' => $layer, 'TrustServerCertificate' => 'enabled', 'ODBCDriver' => $live['odbc_driver'],
                            'LoginTimeout' => 10, 'DeadlockRetryWait' => 10 );
        $base = array( 'server' => $live['host'], 'port' => $live['port'], 'user' => $live['user'], 'password' => $live['password'],
                       'sqlserver' => $settings );
        if ( !self::$databaseReady )
        {
            try
            {
                $master = new expSQLServerDB( self::parameters( array( 'database' => 'master' ) + $base ) );
            }
            catch ( Exception $e )
            {
                return 'The SQL Server for the live tests does not answer: ' . $e->getMessage();
            }
            $name = expSQLServerDialect::quoteIdentifier( $live['database'] );
            $master->query( "IF DB_ID(N'" . expSQLServerDialect::escapeString( $live['database'] ) . "') IS NULL CREATE DATABASE $name" );
            $master->query( "ALTER DATABASE $name SET READ_COMMITTED_SNAPSHOT ON WITH ROLLBACK IMMEDIATE" );
            $master->close();
            self::$databaseReady = true;
        }
        try
        {
            return new expSQLServerDB( self::parameters( $parameters + array( 'database' => $live['database'] ) + $base ) );
        }
        catch ( Exception $e )
        {
            return 'The SQL Server for the live tests does not answer: ' . $e->getMessage();
        }
    }

    /**
     * Saves and replaces the debug globals; restore() puts them back.
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
        // the site's query cache would answer a statement a test sent before
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
