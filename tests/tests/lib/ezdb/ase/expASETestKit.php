<?php
/**
 * Shared pieces of the SAP ASE driver tests: the driver's files and a recording transport that answers from rules
 * instead of a server (no ASE server was available for live tests, see expASEDialect).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'expsqlserverlexer', 'expdbsqlhelpers', 'expdbunsupportedexception', 'expsqlserverdialect', 'expsqlservererrormap', 'expsqlservertransport',
                 'expsqlserverpdotransport', 'expsqlserverdb', 'expasedialect', 'expasepdotransport', 'expasedb' ) as $file )
    require_once dirname( __DIR__, 5 ) . '/lib/ezdb/classes/' . $file . '.php';
require_once dirname( __DIR__, 5 ) . '/lib/ezdbschema/classes/expaseschema.php';

/**
 * A transport that records every batch and answers from rules (the result sets of the SQL Server transports).
 */
class expASEFakeTransport extends expSQLServerTransport
{
    /** @var array[] every batch: array( sql, params ) */
    public $log = array();

    /** @var array[] rules: array( pattern, answer ) */
    public $rules = array();

    /** @var array[] errors for the next batches: array( number, state, message ) */
    public $queue = array();

    /** @var bool */
    public $open = false;

    /** @var int */
    public $opens = 0;

    /** @var bool */
    public $refuseOpen = false;

    /** @var array */
    public $config = array();

    /** @var string what @@version says */
    public $version = 'Adaptive Server Enterprise/16.0 SP04 PL04/EBF 30399 SMP/P/x86_64/SLES 12.4/ase160sp04pl04x/0/64-bit/FBO/Mon Nov 21 2022';

    /** @var array lower-case table => identity column */
    public $identity = array( 'ezx' => 'id' );

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
            $this->setError( 4002, '28000', 'Login failed with password ' . ( isset( $config['password'] ) ? $config['password'] : '' ) );
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
            $this->setError( $next[0], $next[1], $next[2] );
            if ( expSQLServerErrorMap::isConnectionLost( expSQLServerErrorMap::category( $next[0], $next[1] ) ) )
                $this->open = false;
            return false;
        }
        foreach ( $this->rules as $rule )
        {
            if ( !preg_match( $rule[0], $sql ) )
                continue;
            $answer = $rule[1];
            if ( is_callable( $answer ) && !is_array( $answer ) )
                $answer = $answer( $sql, $params, $this );
            if ( is_array( $answer ) && isset( $answer['error'] ) )
            {
                $this->setError( $answer['error'][0], $answer['error'][1], $answer['error'][2] );
                return false;
            }
            if ( is_array( $answer ) && array_key_exists( 'sets', $answer ) )
                return $answer + array( 'affected' => 0 );
            return array( 'sets' => array( $answer ), 'affected' => 0 );
        }
        if ( strpos( $sql, '@@version' ) !== false )
            return array( 'sets' => array( array( array( 'version' => $this->version, 'spid' => '17' ) ) ), 'affected' => 0 );
        if ( strpos( $sql, '(c.status & 128) = 128' ) !== false )
        {
            $rows = array();
            foreach ( $this->identity as $table => $column )
                $rows[] = array( 'table_name' => $table, 'column_name' => $column );
            return array( 'sets' => array( $rows ), 'affected' => 0 );
        }
        if ( preg_match( '/SELECT @@identity AS exp_identity\s*$/', $sql ) )
            return array( 'sets' => array( array( array( 'exp_identity' => '41' ) ) ), 'affected' => 1 );
        return array( 'sets' => array(), 'affected' => 1 );
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
        return 'fake FreeTDS 1.5.20';
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

class expASETestKit
{
    public static function parameters( array $overrides = array() )
    {
        return $overrides + array(
            'server' => '127.0.0.1', 'port' => 5000, 'user' => 'sa', 'password' => 'Secret-Pass-123', 'database' => 'exptest',
            'use_slave_server' => false, 'slave_server' => null, 'slave_port' => null, 'slave_user' => null, 'slave_password' => null,
            'slave_database' => null, 'charset' => 'utf-8', 'is_internal_charset' => true, 'socket' => false,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => false, 'show_errors' => false,
        );
    }

    /**
     * @param expASEFakeTransport|null $transport
     * @param array $settings [ASESettings] overrides
     * @param array $parameters
     * @return expASEDB
     */
    public static function driver( $transport = null, array $settings = array(), array $parameters = array() )
    {
        if ( $transport === null )
            $transport = new expASEFakeTransport();
        $settings += array( 'DeadlockRetryWait' => 0 );
        return new expASEDB( self::parameters( $parameters + array( 'ase_transport' => $transport, 'ase' => $settings ) ) );
    }

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
