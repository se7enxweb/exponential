<?php
/**
 * Shared pieces of the Cassandra and ScyllaDB driver tests: the driver's files, a recording transport that answers
 * from rules instead of a server, the eZDB parameters of a test connection, and the live servers' settings
 * (EXP_TEST_CASSANDRA_HOST, _PORT, _USER, _PASSWORD, _KEYSPACE; the same for EXP_TEST_SCYLLADB_).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'expdbunsupportedexception', 'expdbsqlhelpers', 'expcassandradialect', 'expcassandratransport', 'expcassandraphptransport', 'expcassandradb', 'expscylladb' ) as $file )
    require_once dirname( __DIR__, 5 ) . '/lib/ezdb/classes/' . $file . '.php';
require_once dirname( __DIR__, 5 ) . '/lib/ezdbschema/classes/expcassandraschema.php';

/**
 * A transport that records every statement and answers from rules: the first rule whose pattern matches gives the
 * answer (rows, an error, or a callable). describe() answers system_schema for a table.
 */
class expCassandraFakeTransport extends expCassandraTransport
{
    /** @var array[] every statement: array( cql, values, options ) */
    public $log = array();

    /** @var array[] rules: array( pattern, answer ) */
    public $rules = array();

    /** @var bool */
    public $open = false;

    /** @var array */
    public $config = array();

    /** @var bool */
    public $refuseOpen = false;

    public function name()
    {
        return 'fake';
    }

    public function open( array $config )
    {
        $this->config = $config;
        if ( $this->refuseOpen )
        {
            $this->setError( 0x0100, 'Provided username cassandra and/or password ' . $config['password'] . ' are incorrect' );
            return false;
        }
        $this->open = true;
        return true;
    }

    /**
     * Adds a rule (newest first).
     *
     * @param string $pattern a regular expression on the CQL
     * @param mixed $answer rows (array), a callable( cql, values, options ), or array( 'error' => array( code, message ) )
     * @return $this
     */
    public function on( $pattern, $answer )
    {
        array_unshift( $this->rules, array( $pattern, $answer ) );
        return $this;
    }

    /**
     * Answers system_schema for a table: columns name => array( type, kind, position ), indexes column => name.
     *
     * @param string $table
     * @param array $columns
     * @param array $indexes
     * @param array|null $definition the .dba definition of exp_cassandra_schema
     * @return $this
     */
    public function describe( $table, array $columns, array $indexes = array(), $definition = null )
    {
        $rows = array();
        foreach ( $columns as $name => $c )
            $rows[] = array( 'column_name' => $name, 'type' => $c[0], 'kind' => $c[1], 'position' => isset( $c[2] ) ? $c[2] : ( $c[1] === 'regular' ? -1 : 0 ),
                             'clustering_order' => isset( $c[3] ) ? $c[3] : 'none' );
        $indexRows = array();
        foreach ( $indexes as $column => $name )
            $indexRows[] = array( 'index_name' => $name, 'options' => array( 'target' => $column ) );
        $this->on( '/FROM system_schema\.columns/', function ( $cql, $values ) use ( $table, $rows ) {
            return $values[1] === $table ? $rows : null;
        } );
        $this->on( '/FROM system_schema\.indexes WHERE keyspace_name = \? AND table_name/', function ( $cql, $values ) use ( $table, $indexRows ) {
            return $values[1] === $table ? $indexRows : null;
        } );
        $this->on( '/SELECT definition FROM "exp_cassandra_schema"/', function ( $cql, $values ) use ( $table, $definition ) {
            if ( $values[0] !== $table )
                return null;
            return $definition === null ? array() : array( array( 'definition' => json_encode( $definition ) ) );
        } );
        return $this;
    }

    public function run( $cql, array $values = array(), array $options = array() )
    {
        $this->log[] = array( $cql, $values, $options );
        $this->error = null;
        foreach ( $this->rules as $rule )
        {
            if ( !preg_match( $rule[0], $cql ) )
                continue;
            $answer = $rule[1];
            if ( is_callable( $answer ) )
            {
                $answer = $answer( $cql, $values, $options );
                if ( $answer === null )
                    continue;
            }
            if ( isset( $answer['error'] ) )
            {
                $this->setError( $answer['error'][0], $answer['error'][1] );
                return false;
            }
            if ( isset( $answer['rows'] ) )
                return $answer + array( 'columns' => array(), 'paging_state' => null );
            return array( 'rows' => $answer, 'columns' => array(), 'paging_state' => null );
        }
        // system tables, keyspaces and unknown tables: nothing
        return array( 'rows' => array(), 'columns' => array(), 'paging_state' => null );
    }

    public function close()
    {
        $this->open = false;
    }

    public function isOpen()
    {
        return $this->open;
    }

    public function protocolVersion()
    {
        return 5;
    }

    public function clientVersion()
    {
        return 'fake 1.0';
    }

    /**
     * The statements sent that match a pattern.
     *
     * @param string $pattern
     * @return array[] array( cql, values )
     */
    public function sent( $pattern = '/./' )
    {
        $out = array();
        foreach ( $this->log as $entry )
            if ( preg_match( $pattern, $entry[0] ) )
                $out[] = array( $entry[0], $entry[1] );
        return $out;
    }
}

class expCassandraTestKit
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
            'server' => '127.0.0.1', 'port' => 9042, 'user' => 'cassandra', 'password' => 'Secret-Pass-123', 'database' => '',
            'use_slave_server' => false, 'slave_server' => null, 'slave_port' => null, 'slave_user' => null, 'slave_password' => null,
            'slave_database' => null, 'charset' => 'utf-8', 'is_internal_charset' => true, 'socket' => false,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => false, 'show_errors' => false,
        );
    }

    /**
     * A driver over a fake transport.
     *
     * @param expCassandraFakeTransport|null $transport
     * @param array $settings [CassandraSettings] overrides
     * @param array $parameters
     * @return expCassandraDB
     */
    public static function driver( $transport = null, array $settings = array(), array $parameters = array() )
    {
        if ( $transport === null )
            $transport = new expCassandraFakeTransport();
        return new expCassandraDB( self::parameters( $parameters + array( 'cassandra_transport' => $transport, 'cassandra' => $settings ) ) );
    }

    /**
     * The live server's settings of a product, or null when EXP_TEST_<PRODUCT>_HOST is not set.
     *
     * @param string $product cassandra, scylladb
     * @return array|null
     */
    public static function live( $product )
    {
        $prefix = 'EXP_TEST_' . strtoupper( $product ) . '_';
        $host = getenv( $prefix . 'HOST' );
        if ( $host === false || $host === '' )
            return null;
        return array(
            'host' => $host,
            'port' => getenv( $prefix . 'PORT' ) ?: 9042,
            'user' => getenv( $prefix . 'USER' ) ?: '',
            'password' => getenv( $prefix . 'PASSWORD' ) ?: '',
            'keyspace' => getenv( $prefix . 'KEYSPACE' ) ?: 'exp_test',
        );
    }

    /**
     * A driver on a live server (the keyspace made when missing), or the reason it cannot be had.
     *
     * @param string $product cassandra, scylladb
     * @param array $settings
     * @param array $parameters
     * @return expCassandraDB|string
     */
    public static function liveDriver( $product, array $settings = array(), array $parameters = array() )
    {
        $live = self::live( $product );
        if ( $live === null )
            return "No $product for the live tests: set EXP_TEST_" . strtoupper( $product ) . '_HOST (and _PORT, _USER, _PASSWORD, _KEYSPACE)';
        if ( expCassandraTransport::availableLayer() === null )
            return expCassandraTransport::missingLayerText();
        $settings += array( 'AutoCreateKeyspace' => 'enabled', 'Consistency' => 'ONE', 'SerialConsistency' => 'SERIAL' );
        $class = $product === 'scylladb' ? 'expScyllaDB' : 'expCassandraDB';
        try
        {
            return new $class( self::parameters( $parameters + array( 'server' => $live['host'], 'port' => $live['port'], 'user' => $live['user'],
                                                                      'password' => $live['password'], 'database' => $live['keyspace'],
                                                                      'cassandra' => $settings ) ) );
        }
        catch ( Exception $e )
        {
            return "The $product server for the live tests does not answer: " . $e->getMessage();
        }
    }

    /**
     * The products with a live server configured.
     *
     * @return string[]
     */
    public static function liveProducts()
    {
        $out = array();
        foreach ( array( 'cassandra', 'scylladb' ) as $product )
            if ( self::live( $product ) !== null )
                $out[] = $product;
        return $out;
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
        return $saved;
    }

    /**
     * @param array $saved
     */
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
}
?>
