<?php
/**
 * File containing the expODBCDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * A database driver over ODBC, for any database that has an ODBC driver: the SQL it writes itself (paging, escaping,
 * the new id, the catalogue, the portable helpers) comes from the dialect named in the settings, so one driver serves
 * SQL Server, PostgreSQL, MariaDB/MySQL, SQLite, Oracle and MS Access through their ODBC drivers.
 *
 *   [DatabaseSettings]
 *   DatabaseImplementation=odbc
 *   Server=db.example.com
 *   Database=crm
 *   [ODBCSettings]
 *   Dialect=postgresql
 *   Driver=PostgreSQL Unicode
 *
 * Or as a named connection of the data table layer (engine odbc), or in code:
 *
 *   $db = new expODBCDB( array( 'server' => 'db', 'database' => 'crm', 'user' => 'u', 'password' => $secret,
 *                               'odbc' => array( 'Dialect' => 'mysql', 'Driver' => 'MariaDB Unicode' ) ) );
 *
 * [ODBCSettings] (each also as 'odbc' => array( name => value ) in the parameters, which win):
 *
 *   Dialect           sqlserver, postgresql, mysql, sqlite, oracle, msaccess or generic
 *   Driver            the ODBC driver's name in odbcinst.ini (Linux) or the ODBC administrator (Windows); empty: the
 *                     dialect's usual one (defaultDriver())
 *   DataSourceName    a DSN of odbc.ini instead of Driver and the server settings
 *   ConnectionString  more keyword=value pairs, ; separated (Encrypt=yes;TrustServerCertificate=yes ...)
 *   Layer             auto, pdo_odbc or odbc (PHP's odbc extension); auto takes pdo_odbc first, odbc first for
 *                     msaccess (whose tables only SQLTables lists)
 *
 * databaseName() is the kernel's name of the dialect (postgresql, mysql, sqlite, oracle, SQLServer, MSAccess; odbc for
 * generic), so the schema handlers and the data table layer treat the connection as that engine.
 *
 * @since 6.0.15
 * @package lib
 */
class expODBCDB extends expPDODB
{
    /** The dialects this driver writes SQL for */
    const ODBC_DIALECTS = array( 'sqlserver', 'postgresql', 'mysql', 'sqlite', 'oracle', 'msaccess', 'generic' );

    /** The [ODBCSettings] and their defaults */
    const DEFAULT_SETTINGS = array(
        'Dialect' => 'generic',
        'Driver' => '',
        'DataSourceName' => '',
        'ConnectionString' => '',
        'Layer' => 'auto',
    );

    /** @var array the effective settings */
    protected $Settings = array();

    /** @var string pdo_odbc or odbc */
    protected $Layer = '';

    /** @var int|false the id an INSERT of this connection gave (SQL Server: SCOPE_IDENTITY() in its own batch) */
    protected $InsertID = false;

    /** @var string the connection string, as opened */
    protected $ConnectionString = '';

    /**
     * @param array $parameters the eZDB parameters plus 'odbc' => array( [ODBCSettings] name => value )
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
        // the grandparent: expPDODB's constructor opens a PDO connection of its own
        eZDBInterface::__construct( $parameters );
        $this->Settings = static::settings( isset( $parameters['odbc'] ) && is_array( $parameters['odbc'] ) ? $parameters['odbc'] : array() );
        $dialect = strtolower( trim( (string)$this->Settings['Dialect'] ) );
        $this->Dialect = in_array( $dialect, self::ODBC_DIALECTS, true ) ? $dialect : 'generic';
        $this->PDODriver = 'odbc';
        $this->ConnectionString = static::connectionString( $this->Dialect, $this->Settings, (string)$this->Server, $this->Port, (string)$this->DB );
        $this->DSN = 'odbc:' . $this->ConnectionString;

        $this->Layer = static::chooseLayer( (string)$this->Settings['Layer'], $this->Dialect );
        if ( $this->Layer === '' )
        {
            $this->ErrorMessage = 'This PHP has neither PDO_ODBC nor the odbc extension';
            $this->ErrorNumber = eZDBInterface::ERROR_MISSING_EXTENSION;
            throw new eZDBNoConnectionException( $this->maskedDSN(), $this->ErrorMessage, $this->ErrorNumber );
        }

        $attempts = max( 1, (int)$this->connectRetryCount() + 1 );
        for ( $attempt = 1; $attempt <= $attempts && $this->PDO === null; ++$attempt )
        {
            try
            {
                $this->PDO = $this->openLayer();
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
        $this->sessionSetup();
    }

    /**
     * Opens the connection on the chosen layer.
     *
     * @return PDO|expODBCNativeConnection
     * @throws Throwable
     */
    protected function openLayer()
    {
        $user = $this->User !== '' && $this->User !== false ? (string)$this->User : null;
        $password = $this->Password !== '' && $this->Password !== false ? (string)$this->Password : null;
        if ( $this->Layer === 'odbc' )
            return new expODBCNativeConnection( $this->ConnectionString, $user, $password );
        $options = array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                          PDO::ATTR_STRINGIFY_FETCHES => true );
        if ( $this->UsePersistentConnection )
            $options[PDO::ATTR_PERSISTENT] = true;
        // the driver's own cursors: unixODBC's cursor library is often not installed, and MS Access needs none
        if ( $this->Dialect === 'msaccess' && defined( 'PDO::ODBC_ATTR_USE_CURSOR_LIBRARY' ) )
            $options[PDO::ODBC_ATTR_USE_CURSOR_LIBRARY] = PDO::ODBC_SQL_USE_DRIVER;
        return new PDO( $this->DSN, $user, $password, $options );
    }

    /**
     * What every connection of the dialect needs first: SQL Server's session options, MySQL's character set.
     */
    protected function sessionSetup()
    {
        $statements = array();
        switch ( $this->Dialect )
        {
            case 'sqlserver':
                // exec(): a prepared statement's SET ends with it
                $statements = array( 'SET NOCOUNT ON', 'SET XACT_ABORT ON', 'SET ANSI_NULLS ON', 'SET QUOTED_IDENTIFIER ON', 'SET DATEFORMAT ymd' );
                break;
            case 'mysql':
                $statements = array( 'SET NAMES utf8mb4' );
                break;
        }
        foreach ( $statements as $sql )
        {
            try
            {
                $this->PDO->exec( $sql );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeWarning( "$sql failed: " . $this->mask( $e->getMessage() ), __CLASS__ );
            }
        }
    }

    /**
     * The effective [ODBCSettings]: the defaults, site.ini, then $overrides.
     *
     * @param array $overrides
     * @return array
     */
    public static function settings( array $overrides = array() )
    {
        $settings = static::DEFAULT_SETTINGS;
        $group = static::settingsGroup();
        if ( class_exists( 'eZINI' ) )
        {
            try
            {
                $ini = eZINI::instance();
                if ( $ini->hasGroup( $group ) )
                    foreach ( $ini->group( $group ) as $name => $value )
                        if ( array_key_exists( $name, $settings ) && $value !== '' && $value !== null )
                            $settings[$name] = $value;
            }
            catch ( Throwable $e )
            {
            }
        }
        foreach ( $overrides as $name => $value )
            if ( $value !== null )
                $settings[$name] = $value;
        return $settings;
    }

    /** @return string the site.ini group of the settings */
    public static function settingsGroup()
    {
        return 'ODBCSettings';
    }

    /**
     * The layer to use: pdo_odbc or odbc, or '' when PHP has neither.
     *
     * @param string $wanted auto, pdo_odbc or odbc
     * @param string $dialect
     * @param array|null $have the layers PHP has (tests); null: asked of PHP
     * @return string
     */
    public static function chooseLayer( $wanted, $dialect = 'generic', $have = null )
    {
        if ( $have === null )
        {
            $have = array();
            if ( class_exists( 'PDO' ) && in_array( 'odbc', PDO::getAvailableDrivers(), true ) )
                $have[] = 'pdo_odbc';
            if ( expODBCNativeConnection::available() )
                $have[] = 'odbc';
        }
        $wanted = strtolower( trim( (string)$wanted ) );
        if ( $wanted === 'pdo_odbc' || $wanted === 'odbc' )
            return in_array( $wanted, $have, true ) ? $wanted : '';
        $order = $dialect === 'msaccess' ? array( 'odbc', 'pdo_odbc' ) : array( 'pdo_odbc', 'odbc' );
        foreach ( $order as $layer )
            if ( in_array( $layer, $have, true ) )
                return $layer;
        return '';
    }

    /**
     * The ODBC driver a dialect usually has, by the name its package registers (Debian and Ubuntu packages on Linux,
     * the vendors' installers on Windows).
     *
     * @param string $dialect
     * @param bool|null $windows null: this system
     * @return string
     */
    public static function defaultDriver( $dialect, $windows = null )
    {
        $windows = $windows === null ? DIRECTORY_SEPARATOR === '\\' : (bool)$windows;
        switch ( $dialect )
        {
            case 'sqlserver':
                return 'ODBC Driver 18 for SQL Server';
            case 'postgresql':
                return $windows ? 'PostgreSQL Unicode(x64)' : 'PostgreSQL Unicode';
            case 'mysql':
                return $windows ? 'MariaDB ODBC 3.2 Driver' : 'MariaDB Unicode';
            case 'sqlite':
                return $windows ? 'SQLite3 ODBC Driver' : 'SQLite3';
            case 'oracle':
                return $windows ? 'Oracle in instantclient_23' : 'Oracle 23 ODBC driver';
            case 'msaccess':
                return $windows ? 'Microsoft Access Driver (*.mdb, *.accdb)' : 'MDBTools';
        }
        return '';
    }

    /**
     * The ODBC connection string (pure): DSN=... or Driver={...} with the dialect's keywords for server, port and
     * database, then ConnectionString. User and password are not in it: they go to the connect call.
     *
     * @param string $dialect
     * @param array $settings [ODBCSettings]
     * @param string $server
     * @param int|false $port
     * @param string $database a database name, or the file of SQLite and MS Access
     * @return string
     */
    public static function connectionString( $dialect, array $settings, $server, $port, $database )
    {
        $value = array( 'expSQLServerDialect', 'connectionValue' );
        $parts = array();
        $dsn = isset( $settings['DataSourceName'] ) ? trim( (string)$settings['DataSourceName'] ) : '';
        if ( $dsn !== '' )
            $parts[] = 'DSN=' . call_user_func( $value, $dsn );
        else
        {
            $driver = isset( $settings['Driver'] ) && trim( (string)$settings['Driver'] ) !== '' ? trim( (string)$settings['Driver'] ) : static::defaultDriver( $dialect );
            if ( $driver !== '' )
                $parts[] = 'Driver={' . str_replace( '}', '}}', $driver ) . '}';
        }
        $server = trim( (string)$server );
        $port = (int)$port > 0 ? (int)$port : 0;
        $database = (string)$database;
        switch ( $dialect )
        {
            case 'sqlserver':
                if ( $server !== '' )
                    $parts[] = 'Server=' . call_user_func( $value, expSQLServerDialect::serverAddress( $server, $port ?: null ) );
                if ( $database !== '' )
                    $parts[] = 'Database=' . call_user_func( $value, $database );
                break;
            case 'sqlite':
            case 'msaccess':
                if ( $database !== '' )
                    $parts[] = ( $dialect === 'msaccess' ? 'DBQ=' : 'Database=' ) . call_user_func( $value, $database );
                break;
            case 'oracle':
                if ( $server !== '' )
                    $parts[] = 'DBQ=' . call_user_func( $value, '//' . $server . ( $port ? ':' . $port : '' ) . '/' . $database );
                break;
            default:
                if ( $server !== '' )
                    $parts[] = 'Server=' . call_user_func( $value, $server );
                if ( $port )
                    $parts[] = 'Port=' . $port;
                if ( $database !== '' )
                    $parts[] = 'Database=' . call_user_func( $value, $database );
                if ( $dialect === 'mysql' )
                    $parts[] = 'CHARSET=utf8mb4';
        }
        $extra = isset( $settings['ConnectionString'] ) ? trim( (string)$settings['ConnectionString'], " ;\t\r\n" ) : '';
        if ( $extra !== '' )
            $parts[] = $extra;
        return implode( ';', $parts );
    }

    /**
     * The kernel's name of a dialect's engine.
     *
     * @param string $dialect
     * @return string
     */
    public static function engineName( $dialect )
    {
        switch ( $dialect )
        {
            case 'sqlserver':
                return 'SQLServer';
            case 'msaccess':
                return 'MSAccess';
            case 'postgresql':
            case 'mysql':
            case 'sqlite':
            case 'oracle':
                return $dialect;
        }
        return 'odbc';
    }

    /** @return string the kernel's name of the dialect's engine (postgresql, mysql, sqlite, oracle, SQLServer, MSAccess, odbc) */
    function databaseName()
    {
        return static::engineName( $this->Dialect );
    }

    /** @return string the dialect of the portable helpers */
    public function sqlHelperDialect()
    {
        return in_array( $this->Dialect, expDBSQLHelpers::DIALECTS, true ) ? $this->Dialect : 'generic';
    }

    /** @return string pdo_odbc or odbc */
    public function layer()
    {
        return $this->Layer;
    }

    /** @return array the effective settings */
    public function odbcSettings()
    {
        return $this->Settings;
    }

    /** @return string the connection string as opened (without user and password) */
    public function connectionStringUsed()
    {
        return $this->mask( $this->ConnectionString );
    }

    /**
     * The statements to send for one call, in the dialect: MS Access takes one statement per call, in its own words;
     * SQL Server gets brackets for backticks.
     *
     * @param string $sql
     * @return string[]
     */
    public function prepareStatements( $sql )
    {
        switch ( $this->Dialect )
        {
            case 'msaccess':
                $statements = expMSAccessDialect::split( $sql );
                return array_map( array( 'expMSAccessDialect', 'translate' ), $statements ?: array( '' ) );
            case 'sqlserver':
                return array( expSQLServerDialect::backticksToBrackets( $sql ) );
        }
        return array( (string)$sql );
    }

    /**
     * Sends one statement and gives its rows (or an empty list). On SQL Server, both layers send the statement text
     * in the client's code page (UTF-8 text in N'' literals arrives mangled): its string literals are bound as
     * parameters instead (expSQLServerDialect::parameterize()), and sent as N'' literals only where they cannot be;
     * an INSERT reads SCOPE_IDENTITY() in its own batch.
     *
     * @param string $sql
     * @param bool $wantRows
     * @return array
     * @throws Throwable
     */
    protected function send( $sql, $wantRows )
    {
        $values = array();
        $insert = false;
        if ( $this->Dialect === 'sqlserver' )
        {
            $kind = expSQLServerDialect::statementKind( $sql );
            $insert = $kind === 'INSERT' && expSQLServerDialect::isSingleStatement( $sql );
            $bound = expSQLServerDialect::parameterize( $sql );
            // the odbc extension reads a bound value 'in single quotes' as a file name: such a statement keeps literals
            if ( $bound !== null && $this->Layer === 'odbc' )
                foreach ( $bound[1] as $value )
                    if ( is_string( $value ) && strlen( $value ) > 1 && $value[0] === "'" && substr( $value, -1 ) === "'" )
                        $bound = null;
            if ( $bound !== null )
                list( $sql, $values ) = $bound;
            else
                $sql = expSQLServerDialect::unicodeLiterals( $sql );
            if ( $insert )
                $sql = rtrim( $sql, " \t\r\n;" ) . ";\nSELECT CAST( SCOPE_IDENTITY() AS BIGINT ) AS exp_insert_id";
        }
        if ( !$wantRows && !$values && !$insert )
        {
            $this->PDO->exec( $sql );
            return array();
        }
        if ( $values )
        {
            $statement = $this->PDO->prepare( $sql );
            $statement->execute( $values );
        }
        else
            $statement = $this->PDO->query( $sql );
        $rows = array();
        if ( $insert )
        {
            // the INSERT's result (no columns), then the id
            do
            {
                if ( $statement->columnCount() > 0 )
                {
                    $found = $statement->fetchAll( PDO::FETCH_NUM );
                    $this->InsertID = isset( $found[0][0] ) && $found[0][0] !== null ? (int)$found[0][0] : false;
                    break;
                }
            }
            while ( $statement->nextRowset() );
        }
        elseif ( $wantRows && $statement->columnCount() > 0 )
            $rows = $statement->fetchAll( PDO::FETCH_ASSOC );
        $statement->closeCursor();
        return $rows;
    }

    /**
     * Runs a statement (MS Access: each statement of a script in turn) and times it.
     *
     * @param string $sql
     * @param bool $wantRows
     * @return array|false rows, or false after a failure (the error handling decides)
     */
    protected function run( $sql, $wantRows )
    {
        eZDebug::accumulatorStart( 'odbc_query', 'odbc_total', 'odbc_queries' );
        if ( $this->OutputSQL )
            $this->startTimer();
        $profileStart = eZDBInterface::$SQLProfileOn === false ? 0 : microtime( true );
        $failure = null;
        $rows = array();
        try
        {
            foreach ( $this->prepareStatements( $sql ) as $statement )
                $rows = $this->send( $statement, $wantRows );
        }
        catch ( Throwable $e )
        {
            $failure = $e;
        }
        if ( eZDBInterface::$SQLProfileOn !== false )
            eZDBInterface::profileSQL( $sql, microtime( true ) - $profileStart );
        eZDebug::accumulatorStop( 'odbc_query' );
        if ( $this->OutputSQL )
        {
            $this->endTimer();
            if ( $this->timeTaken() > $this->SlowSQLTimeout )
                $this->reportQuery( __CLASS__ . '[' . $this->databaseName() . ']', $sql, $wantRows ? count( $rows ) : false, $this->timeTaken() );
        }
        if ( $failure !== null )
            return $this->failed( $failure, $sql );
        return $rows;
    }

    function query( $sql, $server = false )
    {
        if ( !$this->IsConnected )
        {
            eZDebug::writeError( 'Trying to do a query without being connected to a database!', __CLASS__ );
            return false;
        }
        // MySQL's session switches the installers write around bulk loads mean nothing elsewhere
        if ( $this->Dialect !== 'mysql'
             && preg_match( '/^\s*SET\s+(?:SESSION\s+|GLOBAL\s+)?(?:FOREIGN_KEY_CHECKS|NAMES|CHARACTER\s+SET|AUTOCOMMIT|SQL_MODE|UNIQUE_CHECKS)\b/i', $sql ) )
            return true;
        return $this->run( $sql, false ) !== false;
    }

    /**
     * The SELECT of a page: the dialect's LIMIT/OFFSET, or for MS Access TOP offset+limit (whose first rows
     * arrayQuery() drops). null when no rows are asked for.
     *
     * @param string $sql
     * @param int|false $limit
     * @param int $offset
     * @return string|null
     */
    public function pageStatement( $sql, $limit, $offset )
    {
        if ( $this->Dialect === 'msaccess' )
            return expMSAccessDialect::top( $sql, $limit === false ? false : $limit + $offset );
        if ( $limit === false && $offset === 0 )
            return $sql;
        return expDBSQLHelpers::limit( $this->sqlHelperDialect(), $sql, $limit, $offset );
    }

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
                $limit = max( 0, (int)$params['limit'] );
            if ( isset( $params['offset'] ) && is_numeric( $params['offset'] ) )
                $offset = max( 0, (int)$params['offset'] );
            if ( isset( $params['column'] ) && ( is_numeric( $params['column'] ) || is_string( $params['column'] ) ) )
                $column = $params['column'];
        }
        $page = $this->pageStatement( $sql, $limit, $offset );
        if ( $page === null )
            return array();
        $rows = $this->run( $page, true );
        if ( $rows === false )
            return false;
        if ( $this->Dialect === 'msaccess' && ( $offset > 0 || $limit !== false ) )
            $rows = array_slice( $rows, $offset, $limit === false ? null : $limit );

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
     * One value of a statement that is the driver's own (the new id, the version ...), or null.
     *
     * @param string $sql
     * @return mixed
     */
    protected function scalar( $sql )
    {
        $statement = $this->PDO->query( $sql );
        $rows = $statement->fetchAll( PDO::FETCH_NUM );
        $statement->closeCursor();
        return isset( $rows[0][0] ) ? $rows[0][0] : null;
    }

    /**
     * The id the last INSERT gave: SCOPE_IDENTITY() read with the INSERT (SQL Server), lastval() or the column's
     * sequence (PostgreSQL), LAST_INSERT_ID() (MySQL), last_insert_rowid() (SQLite), the sequence (Oracle),
     * @@IDENTITY (MS Access).
     *
     * @param string|false $table
     * @param string|false $column
     * @return int|false
     */
    function lastSerialID( $table = false, $column = false )
    {
        if ( !$this->IsConnected )
            return false;
        try
        {
            switch ( $this->Dialect )
            {
                case 'sqlserver':
                    if ( $this->InsertID !== false )
                        return $this->InsertID;
                    $id = $this->scalar( 'SELECT CAST( @@IDENTITY AS BIGINT )' );
                    break;
                case 'postgresql':
                    // a SERIAL column's own sequence, or the kernel's <table>_<column>_seq (not owned by the column)
                    if ( is_string( $table ) && $table !== '' && is_string( $column ) && $column !== '' )
                        $id = $this->scalar( "SELECT currval( COALESCE( pg_get_serial_sequence( '" . str_replace( "'", "''", $table ) . "', '" . str_replace( "'", "''", $column ) . "' ), '"
                                             . str_replace( "'", "''", $table . '_' . $column . '_seq' ) . "' ) )" );
                    else
                        $id = $this->scalar( 'SELECT lastval()' );
                    break;
                case 'mysql':
                    $id = $this->scalar( 'SELECT LAST_INSERT_ID()' );
                    break;
                case 'sqlite':
                    $id = $this->scalar( 'SELECT last_insert_rowid()' );
                    break;
                case 'oracle':
                    if ( !is_string( $table ) || $table === '' )
                        return false;
                    $id = $this->scalar( 'SELECT ' . self::oracleSequence( $table ) . '.CURRVAL FROM DUAL' );
                    break;
                case 'msaccess':
                    $id = $this->scalar( 'SELECT @@IDENTITY' );
                    break;
                default:
                    $id = $this->PDO->lastInsertId();
            }
        }
        catch ( Throwable $e )
        {
            $this->ErrorNumber = self::errorNumberOf( $e );
            $this->ErrorMessage = $this->mask( $e->getMessage() );
            eZDebug::writeError( 'The last id could not be read: ' . $this->ErrorMessage, __CLASS__ );
            return false;
        }
        return $id === null || $id === false || $id === '' || (int)$id === 0 ? false : (int)$id;
    }

    /**
     * Text ready for single quotes. MySQL reads backslashes as escapes (unless NO_BACKSLASH_ESCAPES): they are
     * doubled there; every other dialect doubles the quote only. PDO_ODBC's quote() knows no dialect, so it is not used.
     *
     * @param string|null $str
     * @return string
     */
    function escapeString( $str )
    {
        if ( $str === null )
            return '';
        $str = str_replace( "\0", '', (string)$str );
        if ( $this->Dialect === 'mysql' )
            return strtr( $str, array( '\\' => '\\\\', "'" => "''", "\n" => '\\n', "\r" => '\\r', "\x1a" => '\\Z' ) );
        return str_replace( "'", "''", $str );
    }

    function databaseServerVersion()
    {
        $sql = null;
        switch ( $this->Dialect )
        {
            case 'sqlserver':
                $sql = "SELECT CAST( SERVERPROPERTY( 'ProductVersion' ) AS NVARCHAR(128) )";
                break;
            case 'postgresql':
                $sql = 'SHOW server_version';
                break;
            case 'mysql':
                $sql = 'SELECT VERSION()';
                break;
            case 'sqlite':
                $sql = 'SELECT sqlite_version()';
                break;
            case 'oracle':
                $sql = "SELECT version FROM product_component_version WHERE product LIKE 'Oracle%'";
                break;
        }
        $version = '';
        if ( $this->IsConnected && $sql !== null )
        {
            try
            {
                $version = (string)$this->scalar( $sql );
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
        return array( 'string' => $this->Layer, 'values' => array() );
    }

    function subString( $string, $from, $len = null )
    {
        if ( $this->Dialect === 'msaccess' )
            return expMSAccessDialect::subString( $string, $from, $len );
        if ( $this->Dialect === 'sqlserver' )
            return ' ' . expSQLServerDialect::subString( $string, $from, $len ) . ' ';
        return parent::subString( $string, $from, $len );
    }

    function concatString( $strings = array() )
    {
        if ( $this->Dialect === 'msaccess' )
            return expMSAccessDialect::concatString( (array)$strings );
        return parent::concatString( $strings );
    }

    /**
     * @throws expDBUnsupportedException on MS Access, which has no hash function
     */
    function md5( $str )
    {
        if ( $this->Dialect === 'msaccess' )
            throw new expDBUnsupportedException( 'msaccess', 'MD5', 'compute it in PHP' );
        return parent::md5( $str );
    }

    function bitAnd( $arg1, $arg2 )
    {
        if ( $this->Dialect === 'msaccess' )
            return expMSAccessDialect::bitAnd( $arg1, $arg2, $this->ansi92() );
        return parent::bitAnd( $arg1, $arg2 );
    }

    function bitOr( $arg1, $arg2 )
    {
        if ( $this->Dialect === 'msaccess' )
            return expMSAccessDialect::bitOr( $arg1, $arg2, $this->ansi92() );
        return parent::bitOr( $arg1, $arg2 );
    }

    /** @return bool whether MS Access runs in ANSI-92 mode (ExtendedAnsiSQL=1 in the connection string) */
    public function ansi92()
    {
        return (bool)preg_match( '/(^|;)\s*ExtendedAnsiSQL\s*=\s*1\b/i', $this->ConnectionString );
    }

    /**
     * The tables: SQLTables on the odbc layer (the only way on MS Access, whose MSysObjects ODBC may not read), the
     * dialect's catalogue otherwise.
     *
     * @param int $server
     * @return array name => eZDBInterface::RELATION_TABLE
     */
    function eZTableList( $server = eZDBInterface::SERVER_MASTER )
    {
        $tables = array();
        if ( !$this->IsConnected )
            return $tables;
        $names = null;
        if ( $this->PDO instanceof expODBCNativeConnection && in_array( $this->Dialect, array( 'msaccess', 'generic' ), true ) )
        {
            try
            {
                $names = array();
                foreach ( $this->PDO->tables( 'TABLE' ) as $row )
                    $names[] = isset( $row['TABLE_NAME'] ) ? (string)$row['TABLE_NAME'] : '';
            }
            catch ( Throwable $e )
            {
                $names = null;
            }
        }
        if ( $names === null )
        {
            switch ( $this->Dialect )
            {
                case 'msaccess':
                    $sql = 'SELECT [Name] AS name FROM MSysObjects WHERE [Type] IN (1, 4, 6) AND [Flags] = 0 ORDER BY [Name]';
                    break;
                case 'sqlserver':
                    $sql = 'SELECT name FROM sys.tables WHERE is_ms_shipped = 0 ORDER BY name';
                    break;
                default:
                    return parent::eZTableList( $server );
            }
            $names = array();
            try
            {
                $statement = $this->PDO->query( $sql );
                foreach ( $statement->fetchAll( PDO::FETCH_NUM ) as $row )
                    $names[] = (string)$row[0];
                $statement->closeCursor();
            }
            catch ( Throwable $e )
            {
                eZDebug::writeWarning( 'The tables could not be listed: ' . $this->mask( $e->getMessage() ), __CLASS__ );
            }
        }
        foreach ( $names as $name )
            if ( $name !== '' && strpos( $name, 'MSys' ) !== 0 )
                $tables[$name] = eZDBInterface::RELATION_TABLE;
        return $tables;
    }

    /**
     * PostgreSQL: every sequence of a column default set to the column's highest value, after rows were written with
     * their ids (as eZPostgreSQLDB::correctSequenceValues()). Nothing to do on the other dialects.
     *
     * @return bool false when not connected
     */
    function correctSequenceValues()
    {
        if ( !$this->IsConnected )
            return false;
        // other databases count their identity columns on from the highest value themselves
        if ( $this->Dialect !== 'postgresql' )
            return true;
        $rows = $this->arrayQuery( "SELECT pg_class.relname AS tbl, pg_attribute.attname AS col,
                                           pg_get_expr( pg_attrdef.adbin, pg_attrdef.adrelid ) AS def
                                    FROM pg_class, pg_attribute, pg_attrdef
                                    WHERE pg_get_expr( pg_attrdef.adbin, pg_attrdef.adrelid ) LIKE 'nextval(%'
                                      AND pg_attrdef.adrelid = pg_attribute.attrelid AND pg_attrdef.adnum = pg_attribute.attnum
                                      AND pg_attribute.attrelid = pg_class.oid AND pg_class.relnamespace = current_schema()::regnamespace" );
        foreach ( (array)$rows as $row )
        {
            if ( !preg_match( "/nextval\\(\\(?'([^']+)'/", (string)$row['def'], $m ) )
                continue;
            $this->query( "SELECT setval( '" . str_replace( "'", "''", $m[1] ) . "', COALESCE( MAX( \"" . str_replace( '"', '""', $row['col'] ) . "\" ), 1 ) ) FROM \""
                          . str_replace( '"', '""', $row['tbl'] ) . "\"" );
        }
        return true;
    }

    function removeRelation( $relationName, $relationType )
    {
        if ( $this->Dialect === 'msaccess' && $relationType == eZDBInterface::RELATION_TABLE )
            return $this->IsConnected ? $this->query( 'DROP TABLE ' . expMSAccessDialect::quoteName( $relationName ) ) : false;
        return parent::removeRelation( $relationName, $relationType );
    }

    function close()
    {
        if ( $this->PDO instanceof expODBCNativeConnection )
            $this->PDO->close();
        parent::close();
    }
}
?>
