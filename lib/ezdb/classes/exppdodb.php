<?php
/**
 * File containing the expPDODB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * A generic database driver over PDO, for engines Exponential has no native driver for: Microsoft SQL Server
 * (pdo_sqlsrv, pdo_dblib, or pdo_odbc with a SQL Server ODBC driver), Oracle without the ezoracle extension
 * (pdo_oci), and anything else PHP has a PDO driver for. It speaks the eZDBInterface every persistent object and
 * template fetch uses, so a named connection of the data table layer is an eZDB instance whatever its engine.
 *
 *   $db = new expPDODB( array( 'pdo_dsn' => 'sqlsrv:Server=db.example.com,1433;Database=crm', 'pdo_driver' => 'sqlsrv',
 *                              'user' => 'reader', 'password' => $secret ) );
 *   $rows = $db->arrayQuery( 'SELECT id, name FROM customer ORDER BY name', array( 'limit' => 20, 'offset' => 40 ) );
 *
 * Parameters, besides the usual eZDB ones (user, password, charset ...):
 *
 *   pdo_dsn       the PDO data source name (required)
 *   pdo_driver    sqlsrv, dblib, odbc, oci, mysql, pgsql, sqlite ...; read from the DSN prefix when left out
 *   pdo_dialect   the SQL dialect when the driver does not tell it (odbc: sqlserver, oracle ...); dialect() otherwise
 *   pdo_options   PDO attributes for the connection
 *
 * The dialect decides the SQL this driver writes itself: the LIMIT/OFFSET of arrayQuery(), string escaping, the
 * bit, concatenation, MD5 and substring expressions, the table catalogue and the serial ids. A failed connection
 * throws eZDBNoConnectionException; its message never holds the password, also when the DSN carries one.
 * Results are not taken from the kernel's query cache: that cache knows the site's database only.
 *
 * @since 6.0.15
 * @package lib
 */
class expPDODB extends eZDBInterface
{
    /** The dialects this driver writes SQL for */
    const DIALECTS = array( 'sqlserver', 'oracle', 'mysql', 'postgresql', 'sqlite', 'generic' );

    /** @var PDO|null */
    protected $PDO = null;

    /** @var string the PDO driver name (sqlsrv, oci ...) */
    protected $PDODriver = '';

    /** @var string one of DIALECTS */
    protected $Dialect = 'generic';

    /** @var string the data source name, as given */
    protected $DSN = '';

    /** @var bool whether PDO has a transaction open on this connection */
    protected $InSQLTransaction = false;

    /**
     * @param array $parameters the eZDB parameters plus pdo_dsn, pdo_driver, pdo_dialect, pdo_options
     * @throws eZDBNoConnectionException
     */
    public function __construct( $parameters )
    {
        $parameters = array_merge( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => '',
                                          'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false,
                                          'slave_user' => '', 'slave_password' => '', 'slave_database' => '', 'socket' => false,
                                          'charset' => 'utf-8', 'is_internal_charset' => false, 'builtin_encoding' => false,
                                          'connect_retries' => 1, 'use_persistent_connection' => false, 'show_errors' => false ),
                                   (array)$parameters );
        parent::__construct( $parameters );

        $this->DSN = isset( $parameters['pdo_dsn'] ) ? trim( (string)$parameters['pdo_dsn'] ) : '';
        $driver = isset( $parameters['pdo_driver'] ) ? strtolower( trim( (string)$parameters['pdo_driver'] ) ) : '';
        if ( $driver === '' && preg_match( '/^([a-z0-9_]+):/i', $this->DSN, $match ) )
            $driver = strtolower( $match[1] );
        $this->PDODriver = $driver;
        $dialect = isset( $parameters['pdo_dialect'] ) ? strtolower( trim( (string)$parameters['pdo_dialect'] ) ) : '';
        $this->Dialect = in_array( $dialect, self::DIALECTS, true ) ? $dialect : self::dialect( $driver );

        if ( $this->DSN === '' )
        {
            $this->ErrorMessage = 'No PDO data source name (pdo_dsn) was given';
            $this->ErrorNumber = -1;
            throw new eZDBNoConnectionException( '(no data source name)', $this->ErrorMessage, $this->ErrorNumber );
        }
        if ( !class_exists( 'PDO' ) || ( $driver !== '' && !self::available( $driver ) ) )
        {
            $this->ErrorMessage = "This PHP has no PDO driver '$driver'";
            $this->ErrorNumber = eZDBInterface::ERROR_MISSING_EXTENSION;
            throw new eZDBNoConnectionException( $this->maskedDSN(), $this->ErrorMessage, $this->ErrorNumber );
        }

        $options = isset( $parameters['pdo_options'] ) && is_array( $parameters['pdo_options'] ) ? $parameters['pdo_options'] : array();
        $options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        if ( !isset( $options[PDO::ATTR_DEFAULT_FETCH_MODE] ) )
            $options[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_ASSOC;
        if ( $this->UsePersistentConnection )
            $options[PDO::ATTR_PERSISTENT] = true;

        $attempts = max( 1, (int)$this->connectRetryCount() + 1 );
        for ( $attempt = 1; $attempt <= $attempts && $this->PDO === null; ++$attempt )
        {
            try
            {
                $this->PDO = new PDO( $this->DSN, $this->User !== '' ? (string)$this->User : null,
                                      $this->Password !== '' ? (string)$this->Password : null, $options );
            }
            catch ( Throwable $e )
            {
                $this->PDO = null;
                $this->ErrorNumber = self::errorNumberOf( $e );
                $this->ErrorMessage = $this->mask( $e->getMessage() );
                if ( $attempt < $attempts )
                    usleep( 100000 );
            }
        }
        if ( $this->PDO === null )
        {
            $this->IsConnected = false;
            eZDebug::writeError( 'Connection to ' . $this->maskedDSN() . ' failed: ' . $this->ErrorMessage, __CLASS__ );
            throw new eZDBNoConnectionException( $this->maskedDSN(), $this->ErrorMessage, $this->ErrorNumber );
        }
        $this->DBConnection = $this->PDO;
        $this->DBWriteConnection = $this->PDO;
        $this->IsConnected = true;
        $this->ErrorMessage = false;
        $this->ErrorNumber = false;
    }

    /**
     * The dialect of a PDO driver.
     *
     * @param string $driver
     * @return string sqlserver, oracle, mysql, postgresql, sqlite or generic
     */
    public static function dialect( $driver )
    {
        switch ( strtolower( trim( (string)$driver ) ) )
        {
            case 'sqlsrv':
            case 'dblib':
            case 'mssql':
            case 'sybase':
                return 'sqlserver';
            case 'oci':
            case 'oracle':
                return 'oracle';
            case 'mysql':
            case 'mysqli':
                return 'mysql';
            case 'pgsql':
            case 'postgresql':
                return 'postgresql';
            case 'sqlite':
            case 'sqlite3':
                return 'sqlite';
            default:
                return 'generic';
        }
    }

    /**
     * Whether this PHP has a PDO driver.
     *
     * @param string $driver
     * @return bool
     */
    public static function available( $driver )
    {
        return class_exists( 'PDO' ) && in_array( strtolower( (string)$driver ), PDO::getAvailableDrivers(), true );
    }

    /** @return string pdo_<driver> (pdo_sqlsrv, pdo_oci ...) */
    function databaseName()
    {
        return 'pdo_' . ( $this->PDODriver !== '' ? $this->PDODriver : 'generic' );
    }

    /** @return string the PDO driver name */
    public function pdoDriver()
    {
        return $this->PDODriver;
    }

    /** @return string the dialect of the SQL this driver writes */
    public function sqlDialect()
    {
        return $this->Dialect;
    }

    /** @return PDO|null the PDO handle (null when not connected) */
    public function pdo()
    {
        return $this->IsConnected ? $this->PDO : null;
    }

    /**
     * A statement with its LIMIT and OFFSET in the dialect's words.
     *
     * @param string $dialect
     * @param string $sql
     * @param int|false $limit false for all rows
     * @param int $offset
     * @return string
     */
    public static function limitClause( $dialect, $sql, $limit, $offset )
    {
        $sql = rtrim( (string)$sql, " \t\r\n;" );
        $limit = ( $limit === false || $limit === null || $limit === '' ) ? false : max( 0, (int)$limit );
        $offset = max( 0, (int)$offset );
        if ( $limit === false && $offset === 0 )
            return $sql;
        switch ( $dialect )
        {
            case 'sqlserver':
                if ( !preg_match( '/\border\s+by\b[^)]*$/i', $sql ) )
                    $sql .= "\nORDER BY (SELECT NULL)";
                $sql .= "\nOFFSET $offset ROWS";
                if ( $limit !== false )
                    $sql .= " FETCH NEXT $limit ROWS ONLY";
                return $sql;
            case 'oracle':
                $sql .= "\nOFFSET $offset ROWS";
                if ( $limit !== false )
                    $sql .= " FETCH NEXT $limit ROWS ONLY";
                return $sql;
            case 'mysql':
                return $sql . "\nLIMIT " . ( $limit === false ? '18446744073709551615' : $limit ) . " OFFSET $offset";
            case 'sqlite':
                return $sql . "\nLIMIT " . ( $limit === false ? '-1' : $limit ) . " OFFSET $offset";
            default:
                return $sql . ( $limit !== false ? "\nLIMIT $limit" : "\n" ) . " OFFSET $offset";
        }
    }

    /**
     * Runs a statement that returns no rows.
     *
     * @param string $sql
     * @param int|false $server
     * @return bool
     * @throws eZDBException with the error handling eZDB::ERROR_HANDLING_EXCEPTIONS
     */
    function query( $sql, $server = false )
    {
        if ( !$this->IsConnected )
        {
            eZDebug::writeError( 'Trying to do a query without being connected to a database!', __CLASS__ );
            return false;
        }
        // MySQL session switches the installers write around bulk loads mean nothing elsewhere
        if ( $this->Dialect !== 'mysql'
             && preg_match( '/^\s*SET\s+(?:SESSION\s+|GLOBAL\s+)?(?:FOREIGN_KEY_CHECKS|NAMES|CHARACTER\s+SET|AUTOCOMMIT|SQL_MODE|UNIQUE_CHECKS)\b/i', $sql ) )
            return true;

        eZDebug::accumulatorStart( 'pdo_query', 'pdo_total', 'pdo_queries' );
        if ( $this->OutputSQL )
            $this->startTimer();
        $profileStart = eZDBInterface::$SQLProfileOn === false ? 0 : microtime( true );
        $failure = null;
        try
        {
            $this->PDO->exec( $sql );
        }
        catch ( Throwable $e )
        {
            $failure = $e;
        }
        if ( eZDBInterface::$SQLProfileOn !== false )
            eZDBInterface::profileSQL( $sql, microtime( true ) - $profileStart );
        eZDebug::accumulatorStop( 'pdo_query' );
        if ( $this->OutputSQL )
        {
            $this->endTimer();
            if ( $this->timeTaken() > $this->SlowSQLTimeout )
                $this->reportQuery( __CLASS__ . '[' . $this->databaseName() . ']', $sql, false, $this->timeTaken() );
        }
        if ( $failure === null )
            return true;
        return $this->failed( $failure, $sql );
    }

    /**
     * Runs a statement and returns its rows.
     *
     * @param string $sql
     * @param array $params limit, offset, column (a column name or number: a list of that column's values)
     * @param int|false $server
     * @return array|false
     * @throws eZDBException with the error handling eZDB::ERROR_HANDLING_EXCEPTIONS
     */
    function arrayQuery( $sql, $params = array(), $server = false )
    {
        if ( !$this->IsConnected )
        {
            eZDebug::writeError( 'Trying to do a query without being connected to a database!', __CLASS__ );
            return array();
        }
        $limit = false;
        $offset = 0;
        $column = false;
        if ( is_array( $params ) )
        {
            if ( isset( $params['limit'] ) && is_numeric( $params['limit'] ) )
                $limit = (int)$params['limit'];
            if ( isset( $params['offset'] ) && is_numeric( $params['offset'] ) )
                $offset = (int)$params['offset'];
            if ( isset( $params['column'] ) && ( is_numeric( $params['column'] ) || is_string( $params['column'] ) ) )
                $column = $params['column'];
        }
        $sql = self::limitClause( $this->Dialect, $sql, $limit, $offset );

        eZDebug::accumulatorStart( 'pdo_query', 'pdo_total', 'pdo_queries' );
        if ( $this->OutputSQL )
            $this->startTimer();
        $profileStart = eZDBInterface::$SQLProfileOn === false ? 0 : microtime( true );
        $failure = null;
        $rows = array();
        try
        {
            $statement = $this->PDO->query( $sql );
            $rows = $statement ? $statement->fetchAll( PDO::FETCH_ASSOC ) : array();
            if ( $statement )
                $statement->closeCursor();
        }
        catch ( Throwable $e )
        {
            $failure = $e;
        }
        if ( eZDBInterface::$SQLProfileOn !== false )
            eZDBInterface::profileSQL( $sql, microtime( true ) - $profileStart );
        eZDebug::accumulatorStop( 'pdo_query' );
        if ( $this->OutputSQL )
        {
            $this->endTimer();
            if ( $this->timeTaken() > $this->SlowSQLTimeout )
                $this->reportQuery( __CLASS__ . '[' . $this->databaseName() . ']', $sql, count( $rows ), $this->timeTaken() );
        }
        if ( $failure !== null )
            return $this->failed( $failure, $sql );

        $result = array();
        $i = 0;
        foreach ( $rows as $row )
        {
            $clean = array();
            foreach ( $row as $name => $value )
            {
                $name = (string)$name;
                if ( strpos( $name, '.' ) !== false )
                {
                    $parts = explode( '.', $name );
                    $name = array_pop( $parts );
                }
                if ( $this->Dialect === 'oracle' )
                    $name = strtolower( $name );
                $clean[$name] = is_resource( $value ) ? stream_get_contents( $value ) : $value;
            }
            if ( $column !== false )
            {
                if ( is_string( $column ) )
                    $clean = array_key_exists( $column, $clean ) ? $clean[$column] : ( array_key_exists( strtolower( $column ), $clean ) ? $clean[strtolower( $column )] : null );
                else
                {
                    $values = array_values( $clean );
                    $clean = isset( $values[$column] ) ? $values[$column] : null;
                }
            }
            $result[$i + $offset] = $clean;
            ++$i;
        }
        return $result;
    }

    /**
     * Records a failed statement and reports it the way the error handling asks.
     *
     * @param Throwable $e
     * @param string $sql
     * @return false
     * @throws eZDBException
     */
    protected function failed( Throwable $e, $sql )
    {
        $this->ErrorNumber = self::errorNumberOf( $e );
        $this->ErrorMessage = $this->mask( $e->getMessage() );
        eZDebug::writeError( self::failedQueryMessage( '(' . $this->ErrorNumber . ') ' . $this->ErrorMessage, $sql ), __CLASS__ );
        // in a transaction: rolled back, then the error page or eZDBException
        $this->reportError();
        if ( $this->errorHandling == eZDB::ERROR_HANDLING_EXCEPTIONS )
            throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
        return false;
    }

    /**
     * The driver's error number of an exception (the SQLSTATE's driver code when PDO gives one).
     *
     * @param Throwable $e
     * @return int
     */
    protected static function errorNumberOf( Throwable $e )
    {
        if ( $e instanceof PDOException && is_array( $e->errorInfo ) && isset( $e->errorInfo[1] ) && is_numeric( $e->errorInfo[1] ) )
            return (int)$e->errorInfo[1];
        return is_numeric( $e->getCode() ) ? (int)$e->getCode() : -1;
    }

    /**
     * A text with the password of this connection, and any password=/pwd= of a DSN, replaced.
     *
     * @param string $text
     * @return string
     */
    public function mask( $text )
    {
        return self::maskSecret( $text, (string)$this->Password );
    }

    /**
     * @param string $text
     * @param string $password
     * @return string
     */
    public static function maskSecret( $text, $password )
    {
        $text = (string)$text;
        if ( $password !== '' )
            $text = str_replace( $password, '***', $text );
        return preg_replace( '/\b(password|pwd|passwd)\s*=\s*("[^"]*"|\'[^\']*\'|\{[^}]*\}|[^;\s]*)/i', '$1=***', $text );
    }

    /** @return string the data source name, without a password */
    public function maskedDSN()
    {
        return $this->mask( $this->DSN );
    }

    function beginQuery()
    {
        if ( !$this->IsConnected )
            return false;
        try
        {
            $this->PDO->beginTransaction();
            $this->InSQLTransaction = true;
            return true;
        }
        catch ( Throwable $e )
        {
            $this->ErrorNumber = self::errorNumberOf( $e );
            $this->ErrorMessage = $this->mask( $e->getMessage() );
            eZDebug::writeError( 'The transaction could not start: ' . $this->ErrorMessage, __CLASS__ );
            return false;
        }
    }

    function commitQuery()
    {
        if ( !$this->IsConnected || !$this->InSQLTransaction )
            return true;
        try
        {
            $this->PDO->commit();
            $this->InSQLTransaction = false;
            return true;
        }
        catch ( Throwable $e )
        {
            $this->ErrorNumber = self::errorNumberOf( $e );
            $this->ErrorMessage = $this->mask( $e->getMessage() );
            eZDebug::writeError( 'The transaction could not be committed: ' . $this->ErrorMessage, __CLASS__ );
            return false;
        }
    }

    function rollbackQuery()
    {
        if ( !$this->IsConnected || !$this->InSQLTransaction )
            return true;
        $this->InSQLTransaction = false;
        try
        {
            if ( $this->PDO->inTransaction() )
                $this->PDO->rollBack();
            return true;
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'The transaction could not be rolled back: ' . $this->mask( $e->getMessage() ), __CLASS__ );
            return false;
        }
    }

    /**
     * The sequence of a table as ezoracle names it: s_<name> for an ez table, se_<table> (30 characters at most)
     * for another.
     *
     * @param string $table
     * @return string
     */
    public static function oracleSequence( $table )
    {
        $sequence = preg_replace( '/^ez/i', 's_', (string)$table );
        if ( $sequence === (string)$table )
            $sequence = substr( 'se_' . $sequence, 0, 30 );
        return $sequence;
    }

    /**
     * The id the last INSERT gave.
     *
     * @param string|false $table
     * @param string|false $column
     * @return int|string|false
     */
    function lastSerialID( $table = false, $column = false )
    {
        if ( !$this->IsConnected )
            return false;
        try
        {
            switch ( $this->Dialect )
            {
                case 'oracle':
                    if ( !is_string( $table ) || $table === '' )
                        return false;
                    $rows = $this->PDO->query( 'SELECT ' . self::oracleSequence( $table ) . '.CURRVAL AS id FROM DUAL' )->fetchAll( PDO::FETCH_NUM );
                    return isset( $rows[0][0] ) ? (int)$rows[0][0] : false;
                case 'sqlserver':
                    $rows = $this->PDO->query( 'SELECT CAST( SCOPE_IDENTITY() AS BIGINT ) AS id' )->fetchAll( PDO::FETCH_NUM );
                    if ( isset( $rows[0][0] ) && $rows[0][0] !== null )
                        return (int)$rows[0][0];
                    $id = $this->PDO->lastInsertId();
                    return $id === false || $id === '' ? false : (int)$id;
                case 'postgresql':
                    $name = is_string( $table ) && $table !== '' && is_string( $column ) && $column !== '' ? $table . '_' . $column . '_seq' : null;
                    if ( $name !== null )
                    {
                        $rows = $this->PDO->query( "SELECT pg_get_serial_sequence( " . $this->PDO->quote( $table ) . ", " . $this->PDO->quote( $column ) . " ) AS s" )->fetchAll( PDO::FETCH_NUM );
                        if ( isset( $rows[0][0] ) && $rows[0][0] )
                            $name = $rows[0][0];
                    }
                    $id = $this->PDO->lastInsertId( $name );
                    return $id === false || $id === '' ? false : (int)$id;
                default:
                    $id = $this->PDO->lastInsertId();
                    return $id === false || $id === '' ? false : (int)$id;
            }
        }
        catch ( Throwable $e )
        {
            $this->ErrorNumber = self::errorNumberOf( $e );
            $this->ErrorMessage = $this->mask( $e->getMessage() );
            eZDebug::writeError( 'The last id could not be read: ' . $this->ErrorMessage, __CLASS__ );
            return false;
        }
    }

    /**
     * A string ready to go between single quotes in a statement of this dialect.
     *
     * @param string|null $str
     * @return string
     */
    function escapeString( $str )
    {
        if ( $str === null )
            return '';
        return self::escapeFor( $this->Dialect, (string)$str, $this->IsConnected ? $this->PDO : null );
    }

    /**
     * Escapes a string for a dialect, through the connection when it can quote and the dialect leaves it to it.
     *
     * @param string $dialect
     * @param string $str
     * @param PDO|null $pdo
     * @return string
     */
    public static function escapeFor( $dialect, $str, $pdo = null )
    {
        $str = str_replace( "\0", '', (string)$str );
        if ( $dialect === 'sqlserver' || $dialect === 'oracle' || $dialect === 'sqlite' || $dialect === 'generic' )
            return str_replace( "'", "''", $str );
        if ( $pdo instanceof PDO )
        {
            try
            {
                $quoted = $pdo->quote( $str );
                if ( is_string( $quoted ) && strlen( $quoted ) >= 2 )
                {
                    if ( $quoted[0] === 'E' || $quoted[0] === 'e' )
                        $quoted = substr( $quoted, 1 );
                    return substr( $quoted, 1, -1 );
                }
            }
            catch ( Throwable $e )
            {
                // the driver cannot quote: the dialect's rule below
            }
        }
        if ( $dialect === 'mysql' )
            return strtr( $str, array( '\\' => '\\\\', "'" => "\\'", '"' => '\\"', "\n" => '\\n', "\r" => '\\r', "\x1a" => '\\Z' ) );
        return str_replace( "'", "''", $str );
    }

    function close()
    {
        if ( $this->IsConnected && $this->InSQLTransaction && $this->PDO && $this->PDO->inTransaction() )
        {
            try
            {
                $this->PDO->rollBack();
            }
            catch ( Throwable $e )
            {
            }
        }
        $this->InSQLTransaction = false;
        $this->PDO = null;
        $this->DBConnection = false;
        $this->DBWriteConnection = false;
        $this->IsConnected = false;
    }

    function setError()
    {
        if ( $this->IsConnected && $this->PDO )
        {
            $info = $this->PDO->errorInfo();
            $this->ErrorNumber = isset( $info[1] ) && is_numeric( $info[1] ) ? (int)$info[1] : 0;
            $this->ErrorMessage = isset( $info[2] ) ? $this->mask( (string)$info[2] ) : '';
        }
    }

    function databaseServerVersion()
    {
        $version = '';
        if ( $this->IsConnected )
        {
            try
            {
                $version = (string)$this->PDO->getAttribute( PDO::ATTR_SERVER_VERSION );
            }
            catch ( Throwable $e )
            {
                $version = '';
            }
        }
        preg_match( '/\d+(?:\.\d+)*/', $version, $match );
        return array( 'string' => $version, 'values' => isset( $match[0] ) ? explode( '.', $match[0] ) : array() );
    }

    function databaseClientVersion()
    {
        $version = '';
        if ( $this->IsConnected )
        {
            try
            {
                $version = (string)$this->PDO->getAttribute( PDO::ATTR_CLIENT_VERSION );
            }
            catch ( Throwable $e )
            {
                $version = '';
            }
        }
        preg_match( '/\d+(?:\.\d+)*/', $version, $match );
        return array( 'string' => $version, 'values' => isset( $match[0] ) ? explode( '.', $match[0] ) : array() );
    }

    function isCharsetSupported( $charset )
    {
        return true;
    }

    function bindingType()
    {
        return eZDBInterface::BINDING_NO;
    }

    function bindVariable( $value, $fieldDef = false )
    {
        return $value;
    }

    function subString( $string, $from, $len = null )
    {
        $function = $this->Dialect === 'oracle' || $this->Dialect === 'sqlite' ? 'SUBSTR' : 'SUBSTRING';
        if ( $len === null )
        {
            if ( $this->Dialect === 'sqlserver' )
                return " SUBSTRING( $string, $from, LEN( $string ) ) ";
            return " $function( $string, $from ) ";
        }
        return " $function( $string, $from, $len ) ";
    }

    function concatString( $strings = array() )
    {
        if ( $this->Dialect === 'sqlserver' )
            return ' ( ' . implode( ' + ', $strings ) . ' ) ';
        if ( $this->Dialect === 'mysql' )
            return ' CONCAT( ' . implode( ', ', $strings ) . ' ) ';
        return ' ( ' . implode( ' || ', $strings ) . ' ) ';
    }

    function md5( $str )
    {
        switch ( $this->Dialect )
        {
            case 'sqlserver':
                return " LOWER( CONVERT( VARCHAR(32), HASHBYTES( 'MD5', $str ), 2 ) ) ";
            case 'oracle':
                return " LOWER( STANDARD_HASH( $str, 'MD5' ) ) ";
            default:
                return " MD5( $str ) ";
        }
    }

    function bitAnd( $arg1, $arg2 )
    {
        if ( $this->Dialect === 'oracle' )
            return " BITAND( $arg1, $arg2 ) ";
        return " ( $arg1 & $arg2 ) ";
    }

    function bitOr( $arg1, $arg2 )
    {
        if ( $this->Dialect === 'oracle' )
            return " ( $arg1 + $arg2 - BITAND( $arg1, $arg2 ) ) ";
        return " ( $arg1 | $arg2 ) ";
    }

    /**
     * The tables of the database (all of them, not only the kernel's: a connection of the data table layer is a
     * database of its own).
     *
     * @param int $server
     * @return array name => eZDBInterface::RELATION_TABLE
     */
    function eZTableList( $server = eZDBInterface::SERVER_MASTER )
    {
        $tables = array();
        if ( !$this->IsConnected )
            return $tables;
        switch ( $this->Dialect )
        {
            case 'oracle':
                $sql = 'SELECT table_name AS name FROM user_tables ORDER BY table_name';
                break;
            case 'sqlite':
                $sql = "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name";
                break;
            case 'mysql':
                $sql = "SELECT table_name AS name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_type = 'BASE TABLE' ORDER BY table_name";
                break;
            case 'postgresql':
                $sql = "SELECT table_name AS name FROM information_schema.tables WHERE table_schema = current_schema() AND table_type = 'BASE TABLE' ORDER BY table_name";
                break;
            default:
                $sql = "SELECT table_name AS name FROM information_schema.tables WHERE table_type = 'BASE TABLE' ORDER BY table_name";
        }
        $before = $this->errorHandling;
        $this->errorHandling = eZDB::ERROR_HANDLING_EXCEPTIONS;
        try
        {
            $rows = $this->arrayQuery( $sql );
        }
        catch ( Throwable $e )
        {
            $rows = array();
        }
        $this->errorHandling = $before;
        foreach ( (array)$rows as $row )
        {
            $name = isset( $row['name'] ) ? (string)$row['name'] : '';
            if ( $name === '' )
                continue;
            if ( $this->Dialect === 'oracle' )
                $name = strtolower( $name );
            $tables[$name] = eZDBInterface::RELATION_TABLE;
        }
        return $tables;
    }

    function supportedRelationTypes()
    {
        return array( eZDBInterface::RELATION_TABLE );
    }

    function relationList( $relationType = eZDBInterface::RELATION_TABLE )
    {
        if ( $relationType != eZDBInterface::RELATION_TABLE )
        {
            eZDebug::writeError( "Unsupported relation type '$relationType'", __METHOD__ );
            return false;
        }
        return array_keys( $this->eZTableList() );
    }

    function removeRelation( $relationName, $relationType )
    {
        $relationTypeName = $this->relationName( $relationType );
        if ( !$relationTypeName )
        {
            eZDebug::writeError( "Unknown relation type '$relationType'", __METHOD__ );
            return false;
        }
        if ( !preg_match( '/^[A-Za-z_][A-Za-z0-9_.$]*$/', (string)$relationName ) )
        {
            eZDebug::writeError( "Not a relation name: '$relationName'", __METHOD__ );
            return false;
        }
        return $this->IsConnected ? $this->query( "DROP $relationTypeName $relationName" ) : false;
    }

    /** A database of the server is not created through a data source name. */
    function createDatabase( $dbName )
    {
        return false;
    }

    /** A database of the server is not removed through a data source name. */
    function removeDatabase( $dbName )
    {
        return false;
    }

    /** @return array the databases of a data source name are not listed */
    function availableDatabases()
    {
        return array();
    }

    function __destruct()
    {
        $this->close();
    }
}
?>
