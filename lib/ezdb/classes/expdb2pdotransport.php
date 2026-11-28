<?php
/**
 * File containing the expDb2PDOTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * expDb2DB and expInformixDB over PDO: pdo_ibm (the PECL extension built against IBM's Data Server Driver for ODBC
 * and CLI, which reaches Db2 and, over DRDA, Informix), or PDO_ODBC with an ODBC driver of 64-bit lengths. The
 * connection runs in autocommit mode (each statement its own unit of work); begin() opens a transaction of its own
 * until commit() or rollback(). Column names come in lower case; a CLOB comes as its text.
 *
 * pdo_ibm and PHP's odbc or PDO_ODBC extension cannot be loaded into the same PHP: both export the ODBC function
 * names, and the one loaded first answers the calls of the other.
 */
class expDb2PDOTransport extends expDb2Transport
{
    /** @var PDO|null */
    protected $pdo = null;

    /** @var string the layer: pdo_ibm or pdo_odbc */
    protected $layer;

    /**
     * @param string $layer pdo_ibm or pdo_odbc
     */
    final public function __construct( $layer = 'pdo_ibm' )
    {
        $this->layer = $layer === 'pdo_odbc' ? 'pdo_odbc' : 'pdo_ibm';
    }

    public function name()
    {
        return $this->layer;
    }

    /**
     * The DSN of the connection settings.
     *
     * @param array $config
     * @return string
     */
    protected function dsn( array $config )
    {
        return expDb2Dialect::pdoDSN( array( 'layer' => $this->layer ) + $config );
    }

    public function open( array $config )
    {
        $this->close();
        $this->error = null;
        try
        {
            $options = array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_CASE => PDO::CASE_LOWER,
                              PDO::ATTR_AUTOCOMMIT => true );
            if ( !empty( $config['persistent'] ) )
                $options[PDO::ATTR_PERSISTENT] = true;
            $this->pdo = new PDO( $this->dsn( $config ), isset( $config['user'] ) ? (string)$config['user'] : '',
                                  isset( $config['password'] ) ? (string)$config['password'] : '', $options );
            return true;
        }
        catch ( Throwable $e )
        {
            $this->pdo = null;
            $this->takeException( $e );
            return false;
        }
    }

    public function run( $sql, array $params = array() )
    {
        $this->error = null;
        if ( !$this->pdo )
        {
            $this->setError( -900, '08003', 'The connection is closed' );
            return false;
        }
        try
        {
            if ( $params )
            {
                $statement = $this->pdo->prepare( (string)$sql );
                foreach ( array_values( $params ) as $n => $value )
                    $statement->bindValue( $n + 1, $value === null ? null : (string)$value, $value === null ? PDO::PARAM_NULL : PDO::PARAM_STR );
                $statement->execute();
            }
            else
                $statement = $this->pdo->query( (string)$sql );
            $rows = array();
            if ( $statement->columnCount() > 0 )
            {
                while ( ( $row = $statement->fetch( PDO::FETCH_ASSOC ) ) !== false )
                    $rows[] = self::normalizeRow( $row );
            }
            $affected = self::rowCount( $statement->rowCount() );
            // the statement is dropped, not closeCursor()'d: pdo_ibm's closeCursor() keeps the column descriptors of
            // the result (about 140 bytes a column) for the rest of the process, which took a site install past a
            // memory_limit of 1G; dropping the statement frees its cursor and all of it, on every PDO driver
            $statement = null;
            return array( 'rows' => $rows, 'affected' => $affected );
        }
        catch ( Throwable $e )
        {
            $this->takeException( $e );
            return false;
        }
    }

    /**
     * A row count as a number: an ODBC driver of 32-bit lengths under a 64-bit driver manager leaves the upper half
     * of the count unset (-4294967295 for 1).
     *
     * @param int $count
     * @return int
     */
    public static function rowCount( $count )
    {
        $count = (int)$count;
        if ( $count < -1 && PHP_INT_SIZE === 8 )
            $count = $count & 0xFFFFFFFF;
        return $count;
    }

    public function begin()
    {
        return $this->call( 'beginTransaction' );
    }

    public function commit()
    {
        return $this->pdo && $this->pdo->inTransaction() ? $this->call( 'commit' ) : true;
    }

    public function rollback()
    {
        return $this->pdo && $this->pdo->inTransaction() ? $this->call( 'rollBack' ) : true;
    }

    protected function call( $method )
    {
        $this->error = null;
        if ( !$this->pdo )
        {
            $this->setError( -900, '08003', 'The connection is closed' );
            return false;
        }
        try
        {
            return (bool)$this->pdo->$method();
        }
        catch ( Throwable $e )
        {
            $this->takeException( $e );
            return false;
        }
    }

    public function close()
    {
        $this->pdo = null;
    }

    public function sibling()
    {
        return new static( $this->layer );
    }

    public function isOpen()
    {
        return $this->pdo !== null;
    }

    public function clientVersion()
    {
        $client = '';
        if ( $this->pdo )
        {
            try
            {
                $client = (string)$this->pdo->getAttribute( PDO::ATTR_CLIENT_VERSION );
            }
            catch ( Throwable $e )
            {
            }
        }
        if ( $client === '' && function_exists( 'phpversion' ) )
            $client = (string)phpversion( $this->layer === 'pdo_ibm' ? 'pdo_ibm' : 'pdo_odbc' );
        return $this->layer . ( $client !== '' ? ' ' . $client : '' );
    }

    public function serverVersion()
    {
        if ( !$this->pdo )
            return '';
        try
        {
            return (string)$this->pdo->getAttribute( PDO::ATTR_SERVER_VERSION );
        }
        catch ( Throwable $e )
        {
            return '';
        }
    }

    /**
     * The error of an exception: the SQLSTATE, the SQLCODE (errorInfo[1], or SQLnnnnN in the message) and the
     * message.
     */
    protected function takeException( Throwable $e )
    {
        $info = $e instanceof PDOException && is_array( $e->errorInfo ) ? $e->errorInfo : array();
        $state = isset( $info[0] ) && $info[0] !== '' ? (string)$info[0] : ( $e instanceof PDOException ? (string)$e->getCode() : null );
        $sqlCode = isset( $info[1] ) && is_numeric( $info[1] ) && (int)$info[1] !== 0 ? (int)$info[1] : null;
        $message = isset( $info[2] ) && $info[2] !== '' ? (string)$info[2] : $e->getMessage();
        if ( $sqlCode === null && preg_match( '/\bSQL(\d{4,5})N\b/', $e->getMessage(), $m ) )
            $sqlCode = -(int)$m[1];
        if ( preg_match( '/SQLSTATE=(\w{5})/', $e->getMessage(), $m ) )
            $state = $m[1];
        $this->setError( $sqlCode, $state, $message );
    }
}
