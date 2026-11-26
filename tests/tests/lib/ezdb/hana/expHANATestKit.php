<?php
/**
 * Shared pieces of the SAP HANA driver tests: the driver's files, a recording transport that answers from rules
 * instead of a server, the eZDB parameters of a test connection, and the live server's settings
 * (EXP_TEST_HANA_HOST, _PORT, _USER, _PASSWORD or _PASSWORD_FILE, _SCHEMA, _DRIVER, _LAYER).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'expsqlserverlexer', 'expdbsqlhelpers', 'expodbcnativeconnection', 'exphanadialect', 'exphanatransport', 'exphanaodbctransport', 'exphanadb' ) as $file )
    require_once dirname( __DIR__, 5 ) . '/lib/ezdb/classes/' . $file . '.php';
require_once dirname( __DIR__, 5 ) . '/lib/ezdbschema/classes/exphanaschema.php';

/**
 * A transport that records every statement and answers from rules: the first rule whose pattern matches gives the
 * answer (rows, an error, or a callable). Errors can also be queued for the next statements.
 */
class expHANAFakeTransport extends expHANATransport
{
    /** @var array[] every statement: array( sql, params ) */
    public $log = array();

    /** @var array[] rules: array( pattern, answer ) */
    public $rules = array();

    /** @var array[] errors for the next statements: array( code, message ) or null for success */
    public $queue = array();

    /** @var bool */
    public $open = false;

    /** @var int */
    public $opens = 0;

    /** @var bool */
    public $refuseOpen = false;

    /** @var int|null the error code of a refused open */
    public $refuseCode = 10;

    /** @var string[] begin, commit, rollback as called */
    public $transactions = array();

    /** @var array */
    public $config = array();

    /** @var array[] every config opened */
    public $configs = array();

    /** @var string */
    public $version = '2.00.088.00.1760424921';

    /** @var expHANAFakeTransport[] the siblings handed out */
    public $siblings = array();

    public function __construct()
    {
        $this->on( '/FROM SYS\.M_DATABASE/', function () { return array( array( 'version' => $this->version ) ); } );
    }

    public function name()
    {
        return 'fake';
    }

    public function open( array $config )
    {
        $this->config = $config;
        $this->configs[] = $config;
        $this->opens++;
        if ( $this->refuseOpen )
        {
            $this->setError( $this->refuseCode, '28000', 'authentication failed for ' . ( isset( $config['password'] ) ? $config['password'] : '' ) );
            return false;
        }
        $this->open = true;
        return true;
    }

    public function on( $pattern, $answer )
    {
        array_unshift( $this->rules, array( $pattern, $answer ) );
        return $this;
    }

    public function failNext( $code, $message = 'Simulated error', $state = 'HY000' )
    {
        $this->queue[] = array( $code, $message, $state );
        return $this;
    }

    public function run( $sql, array $params = array() )
    {
        $this->error = null;
        $this->log[] = array( $sql, $params );
        if ( !$this->open )
        {
            $this->setError( -10807, '08003', 'The connection is closed' );
            return false;
        }
        if ( $this->queue )
        {
            $next = array_shift( $this->queue );
            if ( $next !== null )
            {
                $this->setError( $next[0], $next[2], $next[1] );
                if ( expHANADialect::errorCategory( $next[0], $next[1], $next[2] ) === 'connection' )
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
                $this->setError( $answer['error'][0], 'HY000', $answer['error'][1] );
                return false;
            }
            if ( is_array( $answer ) && array_key_exists( 'rows', $answer ) )
                return $answer + array( 'affected' => 0 );
            return array( 'rows' => $answer, 'affected' => 0 );
        }
        return array( 'rows' => array(), 'affected' => 1 );
    }

    public function begin()
    {
        $this->transactions[] = 'begin';
        return true;
    }

    public function commit()
    {
        $this->transactions[] = 'commit';
        return true;
    }

    public function rollback()
    {
        $this->transactions[] = 'rollback';
        return true;
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
        return 'fake 2.25.22';
    }

    public function sibling()
    {
        $sibling = new expHANAFakeTransport();
        $sibling->rules = $this->rules;
        $this->siblings[] = $sibling;
        return $sibling;
    }

    /**
     * The statements sent so far that match $pattern.
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

class expHANATestKit
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
            'server' => '127.0.0.1', 'port' => 39041, 'user' => 'EXPTEST', 'password' => 'Secret-Pass-123', 'database' => 'EXPTEST_SITE',
            'use_slave_server' => false, 'slave_server' => null, 'slave_port' => null, 'slave_user' => null, 'slave_password' => null,
            'slave_database' => null, 'charset' => 'utf-8', 'is_internal_charset' => true, 'socket' => false,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => false, 'show_errors' => false,
        );
    }

    /**
     * A driver over a fake transport.
     *
     * @param expHANAFakeTransport|null $transport
     * @param array $settings [HANASettings] overrides
     * @param array $parameters
     * @return expHANADB
     */
    public static function driver( $transport = null, array $settings = array(), array $parameters = array() )
    {
        if ( $transport === null )
            $transport = new expHANAFakeTransport();
        // the statements as written: the tests of the binding switch it on themselves
        $settings += array( 'DeadlockRetryWait' => 0, 'BindLiterals' => 'disabled' );
        return new expHANADB( self::parameters( $parameters + array( 'hana_transport' => $transport, 'hana' => $settings ) ) );
    }

    /**
     * The live server's settings, or null when EXP_TEST_HANA_HOST is not set.
     *
     * @return array|null
     */
    public static function live()
    {
        $host = getenv( 'EXP_TEST_HANA_HOST' );
        if ( $host === false || $host === '' )
            return null;
        $password = getenv( 'EXP_TEST_HANA_PASSWORD' );
        if ( $password === false || $password === '' )
        {
            $file = getenv( 'EXP_TEST_HANA_PASSWORD_FILE' );
            $password = $file && is_readable( $file ) ? trim( (string)file_get_contents( $file ) ) : '';
        }
        return array(
            'host' => $host,
            'port' => getenv( 'EXP_TEST_HANA_PORT' ) ?: 39041,
            'user' => getenv( 'EXP_TEST_HANA_USER' ) ?: 'SYSTEM',
            'password' => $password,
            'schema' => getenv( 'EXP_TEST_HANA_SCHEMA' ) ?: 'EXP_LIVE_TEST',
            'driver' => getenv( 'EXP_TEST_HANA_DRIVER' ) ?: 'HDBODBC',
            'layer' => getenv( 'EXP_TEST_HANA_LAYER' ) ?: 'auto',
        );
    }

    /**
     * A driver on the live server, or the reason it cannot be had.
     *
     * @param array $settings
     * @param array $parameters
     * @return expHANADB|string
     */
    public static function liveDriver( array $settings = array(), array $parameters = array() )
    {
        $live = self::live();
        if ( $live === null )
            return 'No SAP HANA for the live tests: set EXP_TEST_HANA_HOST (and _PORT, _USER, _PASSWORD or _PASSWORD_FILE, _SCHEMA, _DRIVER)';
        if ( expHANATransport::availableLayer( $live['layer'] ) === null )
            return 'PHP has neither the odbc extension nor PDO_ODBC';
        $settings += array( 'DeadlockRetryWait' => 10, 'Driver' => $live['driver'], 'Layer' => $live['layer'] );
        try
        {
            return new expHANADB( self::parameters( $parameters + array( 'server' => $live['host'], 'port' => $live['port'], 'user' => $live['user'],
                                                                         'password' => $live['password'], 'database' => $live['schema'],
                                                                         'hana' => $settings ) ) );
        }
        catch ( Exception $e )
        {
            return 'The SAP HANA server for the live tests does not answer: ' . $e->getMessage();
        }
    }

    /**
     * Drops every view, table and sequence of the test schema (a test schema only: the live tests start empty).
     *
     * @param expHANADB $db
     */
    public static function emptySchema( expHANADB $db )
    {
        foreach ( array( eZDBInterface::RELATION_VIEW, eZDBInterface::RELATION_TABLE, eZDBInterface::RELATION_SEQUENCE ) as $type )
            foreach ( (array)$db->relationList( $type ) as $name )
                $db->removeRelation( $name, $type );
        // the global temporary tables, which relationList() does not list as tables
        $rows = $db->arrayQuery( "SELECT LOWER(TABLE_NAME) AS name FROM SYS.TABLES WHERE SCHEMA_NAME = CURRENT_SCHEMA AND IS_TEMPORARY = 'TRUE'" );
        foreach ( (array)$rows as $row )
            $db->removeRelation( $row['name'], eZDBInterface::RELATION_TABLE );
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
