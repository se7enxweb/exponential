<?php
/**
 * Shared pieces of the Db2 and Informix driver tests: the drivers' files, a recording transport that answers from
 * rules instead of a server, the eZDB parameters of a test connection, and the live servers' settings
 * (EXP_TEST_DB2_HOST, _PORT, _USER, _PASSWORD or _PASSWORD_FILE, _DATABASE; EXP_TEST_INFORMIX_... the same).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'expsqlserverlexer', 'expdbsqlhelpers', 'expdb2dialect', 'expinformixdialect', 'expdb2transport', 'expdb2pdotransport',
                 'expinformixpdotransport', 'expdb2db', 'expinformixdb' ) as $file )
    require_once dirname( __DIR__, 5 ) . '/lib/ezdb/classes/' . $file . '.php';
require_once dirname( __DIR__, 5 ) . '/lib/ezdbschema/classes/expdb2schema.php';
require_once dirname( __DIR__, 5 ) . '/lib/ezdbschema/classes/expinformixschema.php';

/**
 * A transport that records every statement and answers from rules: the first rule whose pattern matches gives the
 * answer (rows, an error, or a callable). Errors can also be queued for the next statements.
 */
class expDb2FakeTransport extends expDb2Transport
{
    /** @var array[] every statement: array( sql, params ) */
    public $log = array();

    /** @var array[] rules: array( pattern, answer ) */
    public $rules = array();

    /** @var array[] errors for the next statements: array( sqlcode, state, message ) or null for success */
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
    public $version = '12.01.0600';

    /** @var expDb2FakeTransport[] the siblings handed out */
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
            $this->setError( -30082, '08001', 'SQL30082N Security processing failed. Password ' . ( isset( $config['password'] ) ? $config['password'] : '' ) );
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

    public function failNext( $sqlCode, $state = null, $message = 'Simulated error' )
    {
        $this->queue[] = array( $sqlCode, $state, $message );
        return $this;
    }

    public function run( $sql, array $params = array() )
    {
        $this->error = null;
        $this->log[] = array( $sql, $params );
        if ( !$this->open )
        {
            $this->setError( -900, '08003', 'The connection is closed' );
            return false;
        }
        if ( $this->queue )
        {
            $next = array_shift( $this->queue );
            if ( $next !== null )
            {
                $this->setError( $next[0], $next[1], $next[2] );
                if ( expDb2Dialect::errorCategory( $next[0], $next[1], $next[2] ) === 'connection' )
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
        return 'fake 12.1.4';
    }

    public function serverVersion()
    {
        return $this->version;
    }

    public function sibling()
    {
        $sibling = new expDb2FakeTransport();
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

class expDb2TestKit
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
            'server' => '127.0.0.1', 'port' => 50000, 'user' => 'db2inst1', 'password' => 'Secret-Pass-123', 'database' => 'EXPTEST',
            'use_slave_server' => false, 'slave_server' => null, 'slave_port' => null, 'slave_user' => null, 'slave_password' => null,
            'slave_database' => null, 'charset' => 'utf-8', 'is_internal_charset' => true, 'socket' => false,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => false, 'show_errors' => false,
        );
    }

    /**
     * A driver over a fake transport.
     *
     * @param expDb2FakeTransport|null $transport
     * @param array $settings [Db2Settings] (or [InformixSettings]) overrides
     * @param array $parameters
     * @param string $class expDb2DB or expInformixDB
     * @return expDb2DB
     */
    public static function driver( $transport = null, array $settings = array(), array $parameters = array(), $class = 'expDb2DB' )
    {
        if ( $transport === null )
            $transport = new expDb2FakeTransport();
        $settings += array( 'DeadlockRetryWait' => 0 );
        $key = $class === 'expInformixDB' ? 'informix' : 'db2';
        return new $class( self::parameters( $parameters + array( 'db2_transport' => $transport, $key => $settings ) ) );
    }

    /**
     * The live server's settings, or null when EXP_TEST_<KIND>_HOST is not set.
     *
     * @param string $kind DB2 or INFORMIX
     * @return array|null
     */
    public static function live( $kind = 'DB2' )
    {
        $host = getenv( "EXP_TEST_{$kind}_HOST" );
        if ( $host === false || $host === '' )
            return null;
        $password = getenv( "EXP_TEST_{$kind}_PASSWORD" );
        if ( $password === false || $password === '' )
        {
            $file = getenv( "EXP_TEST_{$kind}_PASSWORD_FILE" );
            $password = $file && is_readable( $file ) ? trim( (string)file_get_contents( $file ) ) : '';
        }
        $db2 = $kind === 'DB2';
        return array(
            'host' => $host,
            'port' => getenv( "EXP_TEST_{$kind}_PORT" ) ?: ( $db2 ? 50000 : 9089 ),
            'user' => getenv( "EXP_TEST_{$kind}_USER" ) ?: ( $db2 ? 'db2inst1' : 'informix' ),
            'password' => $password,
            'database' => getenv( "EXP_TEST_{$kind}_DATABASE" ) ?: ( $db2 ? 'EXPTEST' : 'exptest' ),
        );
    }

    /**
     * A driver on the live server, or the reason it cannot be had.
     *
     * @param array $settings
     * @param array $parameters
     * @param string $class expDb2DB or expInformixDB
     * @return expDb2DB|string
     */
    public static function liveDriver( array $settings = array(), array $parameters = array(), $class = 'expDb2DB' )
    {
        $kind = $class === 'expInformixDB' ? 'INFORMIX' : 'DB2';
        $live = self::live( $kind );
        if ( $live === null )
            return "No server for the live tests: set EXP_TEST_{$kind}_HOST (and _PORT, _USER, _PASSWORD or _PASSWORD_FILE, _DATABASE)";
        if ( expDb2Transport::availableLayer() === null )
            return 'PHP has neither pdo_ibm nor ibm_db2';
        $settings += array( 'DeadlockRetryWait' => 10 );
        // EXP_TEST_DB2_LAYER, EXP_TEST_INFORMIX_LAYER: pdo_ibm or ibm_db2 (default: the driver's auto)
        $layer = getenv( "EXP_TEST_{$kind}_LAYER" );
        if ( $layer !== false && $layer !== '' && !isset( $settings['Layer'] ) )
            $settings['Layer'] = $layer;
        $key = $class === 'expInformixDB' ? 'informix' : 'db2';
        try
        {
            return new $class( self::parameters( $parameters + array( 'server' => $live['host'], 'port' => $live['port'], 'user' => $live['user'],
                                                                      'password' => $live['password'], 'database' => $live['database'],
                                                                      $key => $settings ) ) );
        }
        catch ( Exception $e )
        {
            return 'The server for the live tests does not answer: ' . $e->getMessage();
        }
    }

    /**
     * Drops every table, view and sequence of the test database (a test database only: the live tests start empty).
     *
     * @param expDb2DB $db
     */
    public static function emptyDatabase( expDb2DB $db )
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
