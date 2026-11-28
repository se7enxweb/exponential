<?php
/**
 * File containing the expDb2IbmDb2Transport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * expDb2DB and expInformixDB over ibm_db2 (the PECL extension db2_connect() ..., built against IBM's Data Server
 * Driver for ODBC and CLI, as pdo_ibm is). It is the layer of Informix: pdo_ibm 1.7 ends PHP with a segmentation fault
 * when it reads a CLOB or TEXT of Informix over DRDA (measured with Informix 15), while ibm_db2 reads them as text.
 * The connection runs in autocommit mode; begin() turns autocommit off until commit() or rollback(). Column names
 * come in lower case (lower-cased by normalizeRow(), not by the connection's DB2_ATTR_CASE, which keeps memory).
 *
 * @since 6.0.15
 * @package lib
 */
class expDb2IbmDb2Transport extends expDb2Transport
{
    /** @var resource|object|null the connection */
    protected $connection = null;

    /** @var string the dialect class whose pdoDSN() names the server (expDb2Dialect or expInformixDialect) */
    protected $dialect;

    /** @var bool */
    protected $inTransaction = false;

    /** @var bool */
    protected $persistent = false;

    /**
     * @param string $dialect expDb2Dialect or expInformixDialect
     */
    final public function __construct( $dialect = 'expDb2Dialect' )
    {
        $this->dialect = $dialect === 'expInformixDialect' ? 'expInformixDialect' : 'expDb2Dialect';
    }

    public function name()
    {
        return 'ibm_db2';
    }

    /**
     * The connection string of ibm_db2: the keywords of pdo_ibm's DSN with UID and PWD.
     *
     * @param array $config
     * @return string
     */
    public function connectionString( array $config )
    {
        $dialect = $this->dialect;
        $dsn = substr( $dialect::pdoDSN( array( 'layer' => 'pdo_ibm' ) + $config ), 4 );
        return $dsn . 'UID=' . $dialect::dsnValue( isset( $config['user'] ) ? (string)$config['user'] : '' ) . ';'
             . 'PWD=' . $dialect::dsnValue( isset( $config['password'] ) ? (string)$config['password'] : '' ) . ';';
    }

    public function open( array $config )
    {
        $this->close();
        $this->error = null;
        if ( !function_exists( 'db2_connect' ) )
        {
            $this->setError( -900, '08001', 'PHP has no ibm_db2 extension' );
            return false;
        }
        try
        {
            $this->persistent = !empty( $config['persistent'] );
            // no DB2_ATTR_CASE: ibm_db2 2.3 keeps the lower-cased name of every column of every fetched row for the
            // rest of the process (16 bytes a column); normalizeRow() lower-cases the names instead
            $options = array( 'autocommit' => DB2_AUTOCOMMIT_ON );
            $string = $this->connectionString( $config );
            $connection = $this->persistent ? @db2_pconnect( $string, '', '', $options ) : @db2_connect( $string, '', '', $options );
        }
        catch ( Throwable $e )
        {
            $this->setError( -900, '08001', $e->getMessage() );
            return false;
        }
        if ( !$connection )
        {
            $this->takeError( db2_conn_error(), db2_conn_errormsg() );
            return false;
        }
        $this->connection = $connection;
        $this->inTransaction = false;
        return true;
    }

    public function run( $sql, array $params = array() )
    {
        $this->error = null;
        if ( !$this->connection )
        {
            $this->setError( -900, '08003', 'The connection is closed' );
            return false;
        }
        if ( $params )
        {
            $statement = @db2_prepare( $this->connection, (string)$sql );
            if ( !$statement )
            {
                $this->takeError( db2_stmt_error(), db2_stmt_errormsg() );
                return false;
            }
            $values = array();
            foreach ( array_values( $params ) as $value )
                $values[] = $value === null ? null : (string)$value;
            if ( !@db2_execute( $statement, $values ) )
            {
                $this->takeError( db2_stmt_error( $statement ), db2_stmt_errormsg( $statement ) );
                return false;
            }
        }
        else
        {
            $statement = @db2_exec( $this->connection, (string)$sql );
            if ( !$statement )
            {
                $this->takeError( db2_stmt_error(), db2_stmt_errormsg() );
                return false;
            }
        }
        $rows = array();
        if ( db2_num_fields( $statement ) > 0 )
        {
            while ( ( $row = db2_fetch_assoc( $statement ) ) !== false )
                $rows[] = self::normalizeRow( $row );
        }
        $affected = expDb2PDOTransport::rowCount( (int)db2_num_rows( $statement ) );
        db2_free_stmt( $statement );
        return array( 'rows' => $rows, 'affected' => $affected );
    }

    public function begin()
    {
        if ( !$this->connection )
            return false;
        if ( db2_autocommit( $this->connection, DB2_AUTOCOMMIT_OFF ) === false )
        {
            $this->takeError( db2_conn_error( $this->connection ), db2_conn_errormsg( $this->connection ) );
            return false;
        }
        $this->inTransaction = true;
        return true;
    }

    public function commit()
    {
        return $this->finish( 'db2_commit' );
    }

    public function rollback()
    {
        return $this->finish( 'db2_rollback' );
    }

    /**
     * Ends the transaction and turns autocommit on again.
     *
     * @param string $function db2_commit or db2_rollback
     * @return bool
     */
    protected function finish( $function )
    {
        if ( !$this->connection || !$this->inTransaction )
            return true;
        $ok = $function( $this->connection );
        if ( !$ok )
            $this->takeError( db2_conn_error( $this->connection ), db2_conn_errormsg( $this->connection ) );
        db2_autocommit( $this->connection, DB2_AUTOCOMMIT_ON );
        $this->inTransaction = false;
        return (bool)$ok;
    }

    public function close()
    {
        if ( $this->connection && !$this->persistent )
            @db2_close( $this->connection );
        $this->connection = null;
        $this->inTransaction = false;
    }

    public function sibling()
    {
        return new static( $this->dialect );
    }

    public function isOpen()
    {
        return $this->connection !== null;
    }

    public function clientVersion()
    {
        $client = $this->connection ? @db2_client_info( $this->connection ) : false;
        return 'ibm_db2 ' . phpversion( 'ibm_db2' ) . ( is_object( $client ) && isset( $client->DRIVER_VER ) ? ', IBM Data Server Driver ' . $client->DRIVER_VER : '' );
    }

    public function serverVersion()
    {
        $server = $this->connection ? @db2_server_info( $this->connection ) : false;
        return is_object( $server ) && isset( $server->DBMS_VER ) ? (string)$server->DBMS_VER : '';
    }

    /**
     * The error of ibm_db2: the SQLSTATE and a message that ends in "SQLCODE=-617".
     *
     * @param string $state
     * @param string $message
     */
    protected function takeError( $state, $message )
    {
        $message = (string)$message;
        $code = null;
        if ( preg_match( '/SQLCODE=(-?\d+)/', $message, $m ) )
            $code = (int)$m[1];
        else if ( preg_match( '/\bSQL(\d{4,5})N\b/', $message, $m ) )
            $code = -(int)$m[1];
        $this->setError( $code, $state !== '' ? (string)$state : null, $message );
    }
}
