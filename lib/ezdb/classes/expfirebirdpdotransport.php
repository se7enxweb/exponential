<?php
/**
 * File containing the expFirebirdPDOTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * expFirebirdDB over pdo_firebird (PHP 8.4 and later). The connection runs in autocommit mode (each statement is
 * its own transaction, read committed); begin() opens a transaction of its own until commit() or rollback().
 * Column names come in lower case; a BLOB comes as its text.
 */
class expFirebirdPDOTransport extends expFirebirdTransport
{
    /** @var PDO|null */
    protected $pdo = null;

    public function name()
    {
        return 'pdo_firebird';
    }

    public function open( array $config )
    {
        $this->close();
        $this->error = null;
        try
        {
            $dsn = expFirebirdDialect::pdoDSN( $config );
            $options = array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_CASE => PDO::CASE_LOWER,
                              PDO::ATTR_AUTOCOMMIT => true, PDO::ATTR_STRINGIFY_FETCHES => false );
            if ( !empty( $config['persistent'] ) )
                $options[PDO::ATTR_PERSISTENT] = true;
            $this->pdo = new PDO( $dsn, isset( $config['user'] ) ? (string)$config['user'] : '',
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
            $this->setError( -902, null, '08003', 'The connection is closed' );
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
            $affected = $statement->rowCount();
            $statement->closeCursor();
            return array( 'rows' => $rows, 'affected' => (int)$affected );
        }
        catch ( Throwable $e )
        {
            $this->takeException( $e );
            return false;
        }
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
            $this->setError( -902, null, '08003', 'The connection is closed' );
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
        return new expFirebirdPDOTransport();
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
        return 'pdo_firebird ' . PHP_VERSION . ( $client !== '' ? ', Firebird client ' . $client : '' );
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
     * The error of an exception: pdo_firebird gives the SQLSTATE, the SQLCODE (errorInfo[1]) and a message that
     * starts with the GDS code in brackets at connect time.
     */
    protected function takeException( Throwable $e )
    {
        $info = $e instanceof PDOException && is_array( $e->errorInfo ) ? $e->errorInfo : array();
        $state = isset( $info[0] ) ? $info[0] : ( $e instanceof PDOException ? (string)$e->getCode() : null );
        $sqlCode = isset( $info[1] ) && is_numeric( $info[1] ) ? (int)$info[1] : null;
        $message = isset( $info[2] ) && $info[2] !== '' ? (string)$info[2] : $e->getMessage();
        $gds = null;
        if ( preg_match( '/\[(335\d{6})\]/', $e->getMessage(), $m ) )
            $gds = (int)$m[1];
        if ( $sqlCode === null && preg_match( '/:\s*(-\d+)\s/', $e->getMessage(), $m ) )
            $sqlCode = (int)$m[1];
        $this->setError( $sqlCode, $gds, $state, $message );
    }
}
