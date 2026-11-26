<?php
/**
 * File containing the expODBCNativeConnection and expODBCNativeStatement classes.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * PHP's own odbc extension (odbc_connect() ...) behind the few PDO methods expODBCDB uses: query(), exec(), prepare()
 * with execute(), the transaction calls, errorInfo(). It is the second layer of expODBCDB (Layer=odbc), for a PHP
 * without PDO_ODBC, and it is the one with the catalogue functions PDO has no word for: tables() (SQLTables) and
 * columns() (SQLColumns), which MS Access needs because its system table MSysObjects is not readable through ODBC.
 * Errors are thrown as PDOException with errorInfo = array( SQLSTATE, native code, message ), as PDO does.
 *
 * @since 6.0.15
 * @package lib
 */
class expODBCNativeConnection
{
    /** @var resource|object|null */
    protected $link = null;

    /** @var bool */
    protected $inTransaction = false;

    /**
     * @param string $connectionString the ODBC connection string (Driver={...};... or DSN=...), without "odbc:"
     * @param string|null $user
     * @param string|null $password
     * @throws PDOException
     */
    public function __construct( $connectionString, $user = null, $password = null )
    {
        if ( !function_exists( 'odbc_connect' ) )
            throw self::exception( 'IM001', 0, "This PHP has no odbc extension" );
        $link = @odbc_connect( (string)$connectionString, (string)$user, (string)$password );
        if ( !$link )
            throw self::exception( (string)odbc_error(), 0, (string)odbc_errormsg() );
        $this->link = $link;
    }

    /** @return bool whether PHP has the odbc extension */
    public static function available()
    {
        return function_exists( 'odbc_connect' );
    }

    /**
     * @param string $state
     * @param int $code
     * @param string $message
     * @return PDOException
     */
    public static function exception( $state, $code, $message )
    {
        $code = (int)$code;
        if ( $code === 0 && preg_match( '/\((-?\d+)\)\s*$/', (string)$message, $m ) )
            $code = (int)$m[1];
        $e = new PDOException( "SQLSTATE[$state]: $message" );
        $e->errorInfo = array( (string)$state, $code, (string)$message );
        return $e;
    }

    /** @return PDOException the error of the connection */
    protected function lastError()
    {
        return self::exception( (string)odbc_error( $this->link ), 0, (string)odbc_errormsg( $this->link ) );
    }

    /**
     * Runs a statement and gives its rows.
     *
     * @param string $sql
     * @return expODBCNativeStatement
     * @throws PDOException
     */
    public function query( $sql )
    {
        $result = @odbc_exec( $this->link, (string)$sql );
        if ( $result === false )
            throw $this->lastError();
        return new expODBCNativeStatement( $this, $result );
    }

    /**
     * Runs a statement without rows.
     *
     * @param string $sql
     * @return int the rows it changed (-1 when the driver does not say)
     * @throws PDOException
     */
    public function exec( $sql )
    {
        $result = @odbc_exec( $this->link, (string)$sql );
        if ( $result === false )
            throw $this->lastError();
        $count = @odbc_num_rows( $result );
        @odbc_free_result( $result );
        return is_int( $count ) ? $count : -1;
    }

    /**
     * @param string $sql with ? for the values
     * @return expODBCNativeStatement
     * @throws PDOException
     */
    public function prepare( $sql )
    {
        $result = @odbc_prepare( $this->link, (string)$sql );
        if ( $result === false )
            throw $this->lastError();
        return new expODBCNativeStatement( $this, $result, true );
    }

    public function beginTransaction()
    {
        if ( !@odbc_autocommit( $this->link, false ) )
            throw $this->lastError();
        $this->inTransaction = true;
        return true;
    }

    public function commit()
    {
        $ok = @odbc_commit( $this->link );
        $error = $ok ? null : $this->lastError();
        @odbc_autocommit( $this->link, true );
        $this->inTransaction = false;
        if ( $error )
            throw $error;
        return true;
    }

    public function rollBack()
    {
        $ok = @odbc_rollback( $this->link );
        $error = $ok ? null : $this->lastError();
        @odbc_autocommit( $this->link, true );
        $this->inTransaction = false;
        if ( $error )
            throw $error;
        return true;
    }

    /** @return bool */
    public function inTransaction()
    {
        return $this->inTransaction;
    }

    /** ODBC has no "last id" call: the dialect asks the database (expODBCDB::lastSerialID()). */
    public function lastInsertId( $name = null )
    {
        return false;
    }

    /** @return array SQLSTATE, native code, message */
    public function errorInfo()
    {
        $state = (string)odbc_error( $this->link );
        return array( $state === '' ? '00000' : $state, 0, (string)odbc_errormsg( $this->link ) );
    }

    /**
     * The few attributes ODBC can tell.
     *
     * @param int $attribute
     * @return mixed
     */
    public function getAttribute( $attribute )
    {
        if ( $attribute === PDO::ATTR_DRIVER_NAME )
            return 'odbc';
        return null;
    }

    /**
     * The tables (and with $types the views ...) of the data source, through SQLTables.
     *
     * @param string $types TABLE, VIEW, SYSTEM TABLE ... comma separated
     * @return array[] rows with TABLE_NAME, TABLE_TYPE ...
     * @throws PDOException
     */
    public function tables( $types = 'TABLE' )
    {
        $result = @odbc_tables( $this->link, null, null, '%', (string)$types );
        if ( $result === false )
            throw $this->lastError();
        $statement = new expODBCNativeStatement( $this, $result );
        return $statement->fetchAll( PDO::FETCH_ASSOC );
    }

    /**
     * The columns of a table, through SQLColumns: COLUMN_NAME, TYPE_NAME, DATA_TYPE, COLUMN_SIZE, DECIMAL_DIGITS,
     * NULLABLE, ORDINAL_POSITION ...
     *
     * @param string $table
     * @return array[]
     * @throws PDOException
     */
    public function columns( $table )
    {
        $result = @odbc_columns( $this->link, null, null, (string)$table, '%' );
        if ( $result === false )
            throw $this->lastError();
        $statement = new expODBCNativeStatement( $this, $result );
        return $statement->fetchAll( PDO::FETCH_ASSOC );
    }

    /**
     * The unique indexes of a table, through SQLStatistics: INDEX_NAME, COLUMN_NAME, NON_UNIQUE, ORDINAL_POSITION.
     *
     * @param string $table
     * @return array[]
     */
    public function statistics( $table )
    {
        if ( !function_exists( 'odbc_statistics' ) )
            return array();
        $result = @odbc_statistics( $this->link, null, null, (string)$table, SQL_INDEX_ALL, SQL_QUICK );
        if ( $result === false )
            return array();
        $statement = new expODBCNativeStatement( $this, $result );
        return $statement->fetchAll( PDO::FETCH_ASSOC );
    }

    /** @return resource|object the odbc link */
    public function link()
    {
        return $this->link;
    }

    public function close()
    {
        if ( $this->link )
        {
            if ( $this->inTransaction )
                @odbc_rollback( $this->link );
            @odbc_close( $this->link );
        }
        $this->link = null;
        $this->inTransaction = false;
    }

    public function __destruct()
    {
        $this->close();
    }
}

/**
 * A result of expODBCNativeConnection, with PDOStatement's fetchAll(), execute(), closeCursor() and rowCount().
 *
 * @since 6.0.15
 * @package lib
 */
class expODBCNativeStatement
{
    /** The most bytes of one long text or binary value read (the odbc extension allocates this much per value) */
    const LONG_READ_LENGTH = 16777216;

    /** @var expODBCNativeConnection */
    protected $connection;

    /** @var resource|object */
    protected $result;

    /** @var bool a prepared statement not run yet */
    protected $prepared;

    public function __construct( expODBCNativeConnection $connection, $result, $prepared = false )
    {
        $this->connection = $connection;
        $this->result = $result;
        $this->prepared = $prepared;
    }

    /**
     * @param array $values
     * @return bool
     * @throws PDOException
     */
    public function execute( array $values = array() )
    {
        // odbc_execute() reads a value 'x' in single quotes as the name of a file to send: such a value is refused
        $params = array();
        foreach ( array_values( $values ) as $value )
        {
            if ( is_string( $value ) && strlen( $value ) > 1 && $value[0] === "'" && substr( $value, -1 ) === "'" )
                throw expODBCNativeConnection::exception( 'HY000', 0, 'A value in single quotes cannot be bound through the odbc extension (it would be read as a file name)' );
            $params[] = $value === null ? null : ( is_bool( $value ) ? (int)$value : $value );
        }
        if ( !@odbc_execute( $this->result, $params ) )
        {
            $link = $this->connection->link();
            throw expODBCNativeConnection::exception( (string)odbc_error( $link ), 0, (string)odbc_errormsg( $link ) );
        }
        $this->prepared = false;
        return true;
    }

    /** @var string[]|null the column names of the result */
    protected $names = null;

    /**
     * The next row, as name => value (FETCH_ASSOC) or as a list (FETCH_NUM); false after the last.
     *
     * @param int $mode
     * @return array|false
     */
    public function fetch( $mode = PDO::FETCH_ASSOC )
    {
        if ( !$this->result || @odbc_num_fields( $this->result ) < 1 )
            return false;
        if ( $this->names === null )
        {
            // long text and binary up to 16 MB, not cut at odbc.defaultlrl (4 KB)
            @odbc_longreadlen( $this->result, self::LONG_READ_LENGTH );
            @odbc_binmode( $this->result, ODBC_BINMODE_RETURN );
            $this->names = array();
            $count = odbc_num_fields( $this->result );
            for ( $i = 1; $i <= $count; $i++ )
                $this->names[] = odbc_field_name( $this->result, $i );
        }
        if ( !@odbc_fetch_row( $this->result ) )
            return false;
        $row = array();
        foreach ( $this->names as $i => $name )
        {
            $value = odbc_result( $this->result, $i + 1 );
            $value = $value === false ? null : $value;
            if ( $mode === PDO::FETCH_NUM )
                $row[] = $value;
            else
                $row[$name] = $value;
        }
        return $row;
    }

    /**
     * Every row, as name => value (FETCH_ASSOC) or as a list (FETCH_NUM).
     *
     * @param int $mode
     * @return array[]
     */
    public function fetchAll( $mode = PDO::FETCH_ASSOC )
    {
        $rows = array();
        while ( ( $row = $this->fetch( $mode ) ) !== false )
            $rows[] = $row;
        return $rows;
    }

    /** @return int the rows the statement changed */
    public function rowCount()
    {
        $count = @odbc_num_rows( $this->result );
        return is_int( $count ) ? $count : -1;
    }

    /** @return bool whether there is another result (SQLMoreResults) */
    public function nextRowset()
    {
        return function_exists( 'odbc_next_result' ) && @odbc_next_result( $this->result );
    }

    public function closeCursor()
    {
        if ( $this->result )
            @odbc_free_result( $this->result );
        $this->result = null;
        return true;
    }

    /**
     * Column names and ODBC types of the result, as PDOStatement::getColumnMeta() would give them.
     *
     * @param int $column 0-based
     * @return array|false name, native_type, len, precision, scale
     */
    public function getColumnMeta( $column )
    {
        $i = (int)$column + 1;
        if ( !$this->result || $i > @odbc_num_fields( $this->result ) )
            return false;
        return array( 'name' => odbc_field_name( $this->result, $i ), 'native_type' => odbc_field_type( $this->result, $i ),
                      'len' => odbc_field_len( $this->result, $i ), 'precision' => odbc_field_precision( $this->result, $i ),
                      'scale' => odbc_field_scale( $this->result, $i ) );
    }

    /** @return int */
    public function columnCount()
    {
        return $this->result ? (int)@odbc_num_fields( $this->result ) : 0;
    }
}
?>
