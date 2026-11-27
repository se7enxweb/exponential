<?php
/**
 * File containing the expHANAODBCTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * expHANADB over SAP's HANA ODBC driver (HDBODBC, part of the SAP HANA client), through PHP's odbc extension
 * (expODBCNativeConnection, the second layer of the generic ODBC driver) or PDO_ODBC. The connection runs in
 * autocommit mode; begin() opens a transaction until commit() or rollback(). Text comes as UTF-8 (CHAR_AS_UTF8).
 *
 * PDO reads :name in a statement as a parameter and, as PDO_ODBC has no named parameters, sends it as "?". A trigger's
 * body reads its row as :new_row.id, so on PDO_ODBC a statement with a ":" outside its literals runs over a second
 * connection of the odbc extension (DDL, which HANA commits by itself); without that extension it is refused.
 */
class expHANAODBCTransport extends expHANATransport
{
    /** @var PDO|expODBCNativeConnection|null */
    protected $connection = null;

    /** @var string pdo_odbc or odbc */
    protected $layer;

    /** @var string the connection string, as opened (without the password) */
    public $ConnectionString = '';

    /** @var expODBCNativeConnection|null the odbc extension's connection for statements with ":" (PDO_ODBC only) */
    protected $native = null;

    /** @var array the settings of the connection, for the second one */
    protected $config = array();

    /**
     * @param string $layer pdo_odbc or odbc
     */
    public function __construct( $layer = 'pdo_odbc' )
    {
        $this->layer = $layer === 'odbc' ? 'odbc' : 'pdo_odbc';
    }

    public function name()
    {
        return $this->layer;
    }

    /**
     * The odbcinst.ini files unixODBC reads its drivers from, the first one first: <ODBCSYSINI>/<ODBCINSTINI> when the
     * environment names them (as `odbcinst -j` reports), then the places distributions and SAP's client installer use,
     * then the user's ~/.odbcinst.ini.
     *
     * @return string[]
     */
    public static function odbcInstFiles()
    {
        $directory = (string)getenv( 'ODBCSYSINI' );
        $name = (string)getenv( 'ODBCINSTINI' );
        $files = array();
        if ( $directory !== '' || $name !== '' )
            $files[] = ( $directory !== '' ? rtrim( $directory, '/' ) : '/etc' ) . '/' . ( $name !== '' ? $name : 'odbcinst.ini' );
        foreach ( array( '/etc/odbcinst.ini', '/etc/unixODBC/odbcinst.ini', '/usr/local/etc/odbcinst.ini' ) as $file )
            $files[] = $file;
        $home = (string)getenv( 'HOME' );
        if ( $home !== '' )
            $files[] = rtrim( $home, '/' ) . '/.odbcinst.ini';
        return array_values( array_unique( $files ) );
    }

    /**
     * Whether the ODBC driver the connection string names is there: a driver given as the path of its library (DRIVER=
     * /usr/sap/hdbclient/libodbcHDB.so) when that file exists, a driver given by name (HDBODBC, the default) when an
     * odbcinst.ini of unixODBC has a section of that name. PHP has no call that lists the ODBC drivers, so on Windows,
     * where the drivers are in the registry, only a library path is recognised.
     *
     * @param string $driver the name or the library of the driver ([HANASettings] Driver)
     * @param string[]|null $files the odbcinst.ini files to read (tests); null: odbcInstFiles()
     * @return bool
     */
    public static function driverRegistered( $driver, $files = null )
    {
        $driver = trim( (string)$driver, " \t{}" );
        if ( $driver === '' )
            $driver = expHANADialect::DEFAULT_DRIVER;
        if ( strpbrk( $driver, '/\\' ) !== false )
            return is_file( $driver );
        foreach ( $files === null ? self::odbcInstFiles() : (array)$files as $file )
        {
            if ( !is_file( $file ) || !is_readable( $file ) )
                continue;
            $text = @file_get_contents( $file );
            if ( !is_string( $text ) )
                continue;
            foreach ( preg_split( '/\r\n|\r|\n/', $text ) as $line )
            {
                if ( preg_match( '/^\s*\[\s*(.+?)\s*\]\s*$/', $line, $match ) && strcasecmp( $match[1], $driver ) === 0 )
                    return true;
            }
        }
        return false;
    }

    public function open( array $config )
    {
        $this->close();
        $this->error = null;
        $this->config = $config;
        try
        {
            $this->ConnectionString = expHANADialect::connectionString( $config );
            $user = isset( $config['user'] ) && $config['user'] !== '' ? (string)$config['user'] : null;
            $password = isset( $config['password'] ) && $config['password'] !== '' ? (string)$config['password'] : null;
            if ( $this->layer === 'odbc' )
                $this->connection = new expODBCNativeConnection( self::ownLink( $this->ConnectionString ), $user, $password );
            else
            {
                // the driver's own cursors: unixODBC's cursor library is often not installed, and HANA needs none
                // (PHP 8.4 names the constants in Pdo\Odbc; PHP 8.5 deprecates the old PDO::ODBC_* ones)
                $odbc = defined( 'Pdo\\Odbc::ATTR_USE_CURSOR_LIBRARY' ) ? 'Pdo\\Odbc::' : 'PDO::ODBC_';
                $options = array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_CASE => PDO::CASE_LOWER,
                                  PDO::ATTR_STRINGIFY_FETCHES => true,
                                  constant( $odbc . 'ATTR_USE_CURSOR_LIBRARY' ) => constant( $odbc . 'SQL_USE_DRIVER' ) );
                if ( !empty( $config['persistent'] ) )
                    $options[PDO::ATTR_PERSISTENT] = true;
                $this->connection = new PDO( 'odbc:' . $this->ConnectionString, $user, $password, $options );
            }
            return true;
        }
        catch ( Throwable $e )
        {
            $this->connection = null;
            $this->takeException( $e );
            return false;
        }
    }

    public function run( $sql, array $params = array() )
    {
        $this->error = null;
        if ( !$this->connection )
        {
            $this->setError( -10807, '08003', 'The connection is closed' );
            return false;
        }
        try
        {
            $connection = $this->connection;
            if ( $this->layer === 'pdo_odbc' && self::hasColon( $sql ) )
                $connection = $this->nativeConnection();
            if ( $params )
            {
                $statement = $connection->prepare( (string)$sql );
                $statement->execute( array_map( function ( $value ) { return $value === null ? null : (string)$value; }, array_values( $params ) ) );
            }
            else
                $statement = $connection->query( (string)$sql );
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

    /**
     * Whether a statement has a ":" outside its literals, identifiers and comments (PDO would take :name for a
     * parameter).
     *
     * @param string $sql
     * @return bool
     */
    public static function hasColon( $sql )
    {
        if ( strpos( (string)$sql, ':' ) === false )
            return false;
        foreach ( expSQLServerLexer::tokenize( (string)$sql ) as $token )
            if ( $token['type'] === expSQLServerLexer::PUNCT && $token['text'] === ':' )
                return true;
        return false;
    }

    /**
     * The odbc extension's connection for statements PDO cannot send, opened when first needed.
     *
     * @return expODBCNativeConnection
     * @throws PDOException when PHP has no odbc extension
     */
    protected function nativeConnection()
    {
        if ( $this->native === null )
        {
            if ( !expODBCNativeConnection::available() )
                throw expODBCNativeConnection::exception( 'IM001', 0, 'A statement with ":" (a trigger) needs the odbc extension of PHP next to PDO_ODBC' );
            $this->native = new expODBCNativeConnection( self::ownLink( $this->ConnectionString ), isset( $this->config['user'] ) && $this->config['user'] !== '' ? (string)$this->config['user'] : null,
                                                         isset( $this->config['password'] ) && $this->config['password'] !== '' ? (string)$this->config['password'] : null );
        }
        return $this->native;
    }

    /**
     * The connection string made unique by a session variable of HANA's client (SESSIONVARIABLE:EXP_LINK=...): the
     * odbc extension hands the link it has to every odbc_connect() with the same connection string and user, so the
     * second session the driver needs (for a global temporary table) would be the first one again.
     *
     * @param string $connectionString
     * @return string
     */
    public static function ownLink( $connectionString )
    {
        static $n = 0;
        return $connectionString . ';SESSIONVARIABLE:EXP_LINK=' . getmypid() . '.' . ( ++$n );
    }

    public function begin()
    {
        return $this->call( 'beginTransaction' );
    }

    public function commit()
    {
        return $this->connection && $this->connection->inTransaction() ? $this->call( 'commit' ) : true;
    }

    public function rollback()
    {
        return $this->connection && $this->connection->inTransaction() ? $this->call( 'rollBack' ) : true;
    }

    protected function call( $method )
    {
        $this->error = null;
        if ( !$this->connection )
        {
            $this->setError( -10807, '08003', 'The connection is closed' );
            return false;
        }
        try
        {
            return (bool)$this->connection->$method();
        }
        catch ( Throwable $e )
        {
            $this->takeException( $e );
            return false;
        }
    }

    public function close()
    {
        if ( $this->connection instanceof expODBCNativeConnection )
            $this->connection->close();
        if ( $this->native !== null )
            $this->native->close();
        $this->connection = null;
        $this->native = null;
    }

    public function sibling()
    {
        return new expHANAODBCTransport( $this->layer );
    }

    public function isOpen()
    {
        return $this->connection !== null;
    }

    public function clientVersion()
    {
        $client = '';
        if ( $this->connection instanceof PDO )
        {
            try
            {
                $client = (string)$this->connection->getAttribute( PDO::ATTR_CLIENT_VERSION );
            }
            catch ( Throwable $e )
            {
            }
        }
        return $this->layer . ' ' . PHP_VERSION . ( $client !== '' ? ', ' . $client : '' );
    }

    /**
     * The error of an exception: SQLSTATE, the native error (HANA's code) and the message; the code is read from the
     * message when the layer gives none.
     */
    protected function takeException( Throwable $e )
    {
        $info = $e instanceof PDOException && is_array( $e->errorInfo ) ? $e->errorInfo : array();
        $state = isset( $info[0] ) && $info[0] !== '' ? (string)$info[0] : ( $e instanceof PDOException ? (string)$e->getCode() : null );
        $message = isset( $info[2] ) && $info[2] !== '' ? (string)$info[2] : $e->getMessage();
        $code = isset( $info[1] ) && is_numeric( $info[1] ) && (int)$info[1] !== 0 ? (int)$info[1] : self::codeInMessage( $message );
        if ( $code === null )
            $code = self::codeInMessage( $e->getMessage() );
        $this->setError( $code, $state, $message );
    }
}
