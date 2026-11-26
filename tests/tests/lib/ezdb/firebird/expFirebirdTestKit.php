<?php
/**
 * Shared pieces of the Firebird driver tests: the driver's files, a recording transport that answers from rules
 * instead of a server, the eZDB parameters of a test connection, and the live server's settings
 * (EXP_TEST_FIREBIRD_HOST, _PORT, _USER, _PASSWORD or _PASSWORD_FILE, _DATABASE).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'expsqlserverlexer', 'expdbsqlhelpers', 'expfirebirddialect', 'expfirebirdtransport', 'expfirebirdpdotransport', 'expfirebirddb' ) as $file )
    require_once dirname( __DIR__, 5 ) . '/lib/ezdb/classes/' . $file . '.php';
require_once dirname( __DIR__, 5 ) . '/lib/ezdbschema/classes/expfirebirdschema.php';

/**
 * A transport that records every statement and answers from rules: the first rule whose pattern matches gives the
 * answer (rows, an error, or a callable). Errors can also be queued for the next statements.
 */
class expFirebirdFakeTransport extends expFirebirdTransport
{
    /** @var array[] every statement: array( sql, params ) */
    public $log = array();

    /** @var array[] rules: array( pattern, answer ) */
    public $rules = array();

    /** @var array[] errors for the next statements: array( sqlcode, gds, message ) or null for success */
    public $queue = array();

    /** @var bool */
    public $open = false;

    /** @var int */
    public $opens = 0;

    /** @var bool */
    public $refuseOpen = false;

    /** @var string[] begin, commit, rollback as called */
    public $transactions = array();

    /** @var array */
    public $config = array();

    /** @var string */
    public $version = 'LI-V5.0.4.1812 Firebird 5.0';

    /** @var expFirebirdFakeTransport[] the siblings handed out */
    public $siblings = array();

    public function name()
    {
        return 'fake';
    }

    public function open( array $config )
    {
        $this->config = $config;
        $this->opens++;
        if ( $this->refuseOpen )
        {
            $this->setError( -902, 335544472, '08004', 'Your user name and password are not defined. Password ' . ( isset( $config['password'] ) ? $config['password'] : '' ) );
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

    public function failNext( $sqlCode, $gds = null, $message = 'Simulated error' )
    {
        $this->queue[] = array( $sqlCode, $gds, $message );
        return $this;
    }

    public function run( $sql, array $params = array() )
    {
        $this->error = null;
        $this->log[] = array( $sql, $params );
        if ( !$this->open )
        {
            $this->setError( -902, null, '08003', 'The connection is closed' );
            return false;
        }
        if ( $this->queue )
        {
            $next = array_shift( $this->queue );
            if ( $next !== null )
            {
                $this->setError( $next[0], $next[1], 'HY000', $next[2] );
                if ( expFirebirdDialect::errorCategory( $next[0], $next[2], $next[1] ) === 'connection' )
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
                $this->setError( $answer['error'][0], $answer['error'][1], 'HY000', $answer['error'][2] );
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
        return 'fake 5.0.1';
    }

    public function serverVersion()
    {
        return $this->version;
    }

    public function sibling()
    {
        $sibling = new expFirebirdFakeTransport();
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

class expFirebirdTestKit
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
            'server' => '127.0.0.1', 'port' => 3050, 'user' => 'SYSDBA', 'password' => 'Secret-Pass-123', 'database' => 'exptest',
            'use_slave_server' => false, 'slave_server' => null, 'slave_port' => null, 'slave_user' => null, 'slave_password' => null,
            'slave_database' => null, 'charset' => 'utf-8', 'is_internal_charset' => true, 'socket' => false,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => false, 'show_errors' => false,
        );
    }

    /**
     * A driver over a fake transport.
     *
     * @param expFirebirdFakeTransport|null $transport
     * @param array $settings [FirebirdSettings] overrides
     * @param array $parameters
     * @return expFirebirdDB
     */
    public static function driver( $transport = null, array $settings = array(), array $parameters = array() )
    {
        if ( $transport === null )
            $transport = new expFirebirdFakeTransport();
        $settings += array( 'DeadlockRetryWait' => 0 );
        return new expFirebirdDB( self::parameters( $parameters + array( 'firebird_transport' => $transport, 'firebird' => $settings ) ) );
    }

    /**
     * The live server's settings, or null when EXP_TEST_FIREBIRD_HOST is not set.
     *
     * @return array|null
     */
    public static function live()
    {
        $host = getenv( 'EXP_TEST_FIREBIRD_HOST' );
        if ( $host === false || $host === '' )
            return null;
        $password = getenv( 'EXP_TEST_FIREBIRD_PASSWORD' );
        if ( $password === false || $password === '' )
        {
            $file = getenv( 'EXP_TEST_FIREBIRD_PASSWORD_FILE' );
            $password = $file && is_readable( $file ) ? trim( (string)file_get_contents( $file ) ) : '';
        }
        return array(
            'host' => $host,
            'port' => getenv( 'EXP_TEST_FIREBIRD_PORT' ) ?: 3050,
            'user' => getenv( 'EXP_TEST_FIREBIRD_USER' ) ?: 'SYSDBA',
            'password' => $password,
            'database' => getenv( 'EXP_TEST_FIREBIRD_DATABASE' ) ?: '/var/lib/firebird/data/exp_test.fdb',
        );
    }

    /**
     * A driver on the live server, or the reason it cannot be had.
     *
     * @param array $settings
     * @param array $parameters
     * @return expFirebirdDB|string
     */
    public static function liveDriver( array $settings = array(), array $parameters = array() )
    {
        $live = self::live();
        if ( $live === null )
            return 'No Firebird for the live tests: set EXP_TEST_FIREBIRD_HOST (and _PORT, _USER, _PASSWORD or _PASSWORD_FILE, _DATABASE)';
        if ( expFirebirdTransport::availableLayer() === null )
            return 'PHP has no pdo_firebird of PHP 8.4 or later';
        $settings += array( 'DeadlockRetryWait' => 10 );
        try
        {
            return new expFirebirdDB( self::parameters( $parameters + array( 'server' => $live['host'], 'port' => $live['port'], 'user' => $live['user'],
                                                                             'password' => $live['password'], 'database' => $live['database'],
                                                                             'firebird' => $settings ) ) );
        }
        catch ( Exception $e )
        {
            return 'The Firebird server for the live tests does not answer: ' . $e->getMessage();
        }
    }

    /**
     * Drops every table, view and sequence of the test database (a test database only: the live tests start empty).
     *
     * @param expFirebirdDB $db
     */
    public static function emptyDatabase( expFirebirdDB $db )
    {
        foreach ( array( eZDBInterface::RELATION_VIEW, eZDBInterface::RELATION_TABLE, eZDBInterface::RELATION_SEQUENCE ) as $type )
            foreach ( (array)$db->relationList( $type ) as $name )
                $db->removeRelation( $name, $type );
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
