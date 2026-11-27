<?php
/**
 * File containing the expMonetDBDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The MonetDB database driver (DatabaseImplementation=monetdb): a whole Exponential site, or a data table connection,
 * on a MonetDB server. MonetDB is a columnar SQL database server with transactions, sequences, primary keys and unique
 * constraints; PHP reaches it through MonetDB's own protocol, MAPI, spoken in plain PHP by expMonetDBMapi (no extension).
 *
 * site.ini:
 *
 *   [DatabaseSettings]
 *   DatabaseImplementation=monetdb
 *   Server=127.0.0.1
 *   Port=50000
 *   User=monetdb
 *   Password=...
 *   Database=exponential          the database monetdbd serves
 *
 *   [MonetDBSettings]             see settings/site.ini (Schema, timeouts)
 *
 * Transactions are optimistic: a transaction reads a snapshot and writes without locks. MonetDB tells conflicts apart
 * coarsely: two open transactions that change the same table conflict even on different rows (the second UPDATE fails
 * at once, or the second COMMIT of two INSERTs into a table with a key). So the driver lets one writer write at a time,
 * as DuckDB's file lock does (WriteLock=enabled, the default): a transaction starts by changing the row 'write' of the
 * table monetdb_write_lock, and a writer that finds it changed by another open transaction rolls back, waits (10 ms,
 * doubling up to 250 ms) and tries again for LockWait seconds, then fails with 55P03. A write outside a transaction is
 * sent in such a transaction of its own. Reads never wait. Row locks (SELECT ... FOR UPDATE) and LOCK TABLE are left out
 * (lock() is the write lock); see expMonetDBDialect for what else is changed on the way. databaseName() is 'monetdb';
 * error numbers are the SQLSTATE PostgreSQL gives the same error ('23505' duplicate key, '40001' a conflict).
 *
 * @since 6.0.15
 * @package lib
 */
class expMonetDBDB extends eZDBInterface
{
    /** The defaults of [MonetDBSettings] */
    const DEFAULT_SETTINGS = array(
        'Schema' => '',
        'ConnectTimeout' => 10,
        'ReadTimeout' => 300,
        'WriteLock' => 'enabled',
        'LockWait' => '',
        'LockWaitWeb' => 10,
        'LockWaitCommandLine' => 120,
        'ConflictRetries' => 3,
    );

    /** The port of monetdbd */
    const DEFAULT_PORT = 50000;

    /** The table of the write lock (no ez/exp prefix: not one of the kernel's tables) */
    const LOCK_TABLE = 'monetdb_write_lock';

    /** @var bool whether the lock table is known to exist on this connection */
    protected $LockTableReady = false;

    /** @var string|null the connection's schema (CURRENT_SCHEMA), asked once */
    protected $CurrentSchema = null;

    /** @var int write lock waits of this connection (for the tests and the debug output) */
    public $LockWaits = 0;

    /** @var array the effective [MonetDBSettings] */
    protected $Settings = array();

    /** @var expMonetDBMapi|object the protocol client (or a stand-in with the same methods, for tests) */
    protected $Mapi = null;

    /** @var int[] the id the last INSERT into a table gave its serial column, by table */
    protected $LastIDs = array();

    /** @var int the id the last INSERT gave, of any table */
    protected $LastID = -1;

    /** @var bool whether a transaction is open on the server */
    protected $InSQLTransaction = false;

    /** @var int statements run, for the debug output */
    public $NumQueries = 0;

    /**
     * @param array $parameters the eZDB parameters; 'monetdb' => array( <[MonetDBSettings] name> => value ) over
     *                          site.ini, 'monetdb_mapi' => a stand-in for expMonetDBMapi (tests)
     * @throws eZDBNoConnectionException when the server cannot be reached
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
        // MonetDB replicates through its own tools, not through a second address in site.ini
        $this->UseSlaveServer = false;
        $this->Settings = self::settings( isset( $parameters['monetdb'] ) && is_array( $parameters['monetdb'] ) ? $parameters['monetdb'] : array() );
        eZDebug::createAccumulatorGroup( 'monetdb_total', 'MonetDB Total' );
        $this->Mapi = isset( $parameters['monetdb_mapi'] ) && is_object( $parameters['monetdb_mapi'] )
                    ? $parameters['monetdb_mapi'] : new expMonetDBMapi( (float)$this->Settings['ReadTimeout'] );

        if ( $this->DB === null || $this->DB === '' || $this->DB === false )
        {
            // No database named yet (the setup wizard asks the driver before it has one)
            $this->IsConnected = false;
            return;
        }
        if ( !$this->connect() )
            throw new eZDBNoConnectionException( $this->Server, $this->ErrorMessage, $this->ErrorNumber );
    }

    /**
     * The effective [MonetDBSettings]: the defaults, site.ini, then $overrides.
     *
     * @param array $overrides
     * @return array
     */
    public static function settings( array $overrides = array() )
    {
        $settings = self::DEFAULT_SETTINGS;
        if ( class_exists( 'eZINI', false ) || class_exists( 'eZINI' ) )
        {
            try
            {
                $ini = eZINI::instance( 'site.ini' );
                if ( $ini->hasGroup( 'MonetDBSettings' ) )
                    foreach ( array_keys( self::DEFAULT_SETTINGS ) as $name )
                        if ( $ini->hasVariable( 'MonetDBSettings', $name ) )
                            $settings[$name] = $ini->variable( 'MonetDBSettings', $name );
            }
            catch ( Throwable $e )
            {
            }
        }
        foreach ( $overrides as $name => $value )
            if ( array_key_exists( $name, self::DEFAULT_SETTINGS ) )
                $settings[$name] = $value;
        return $settings;
    }

    /**
     * Connects, logs in and chooses the schema.
     *
     * @return bool
     */
    protected function connect()
    {
        $host = $this->SocketPath ? (string)$this->SocketPath : ( $this->Server === '' || $this->Server === false ? '127.0.0.1' : (string)$this->Server );
        $port = $this->Port ? (int)$this->Port : self::DEFAULT_PORT;
        $tries = max( 1, (int)$this->ConnectRetries );
        for ( $try = 0; $try < $tries; $try++ )
        {
            if ( $try > 0 )
                usleep( 200000 );
            eZDebug::accumulatorStart( 'monetdb_connection', 'monetdb_total', 'Database connection' );
            $ok = $this->Mapi->connect( $host, $port, (string)$this->User, (string)$this->Password, (string)$this->DB, (float)$this->Settings['ConnectTimeout'] );
            eZDebug::accumulatorStop( 'monetdb_connection' );
            if ( $ok )
                break;
        }
        if ( !$ok )
        {
            $this->IsConnected = false;
            $this->ErrorMessage = (string)$this->Mapi->Error;
            $this->ErrorNumber = expMonetDBDialect::errorCode( (string)$this->Mapi->SQLState, $this->ErrorMessage );
            eZDebug::writeError( "Connection to MonetDB at $host:$port failed: " . $this->ErrorMessage, __CLASS__ );
            if ( function_exists( 'eZAppendWarningItem' ) )
                eZAppendWarningItem( array( 'error' => array( 'type' => 'ezdb', 'number' => $this->ErrorNumber ),
                                            'text' => 'Connection to MonetDB failed: ' . $this->ErrorMessage ) );
            return false;
        }
        $this->IsConnected = true;
        $schema = trim( (string)$this->Settings['Schema'] );
        if ( $schema !== '' && !$this->query( 'SET SCHEMA ' . self::quoteName( $schema ) ) )
            return false;
        return true;
    }

    /**
     * A name in double quotes.
     */
    public static function quoteName( $name )
    {
        return '"' . str_replace( '"', '""', (string)$name ) . '"';
    }

    function databaseName()
    {
        return 'monetdb';
    }

    public function sqlHelperDialect()
    {
        return 'monetdb';
    }

    function bindingType()
    {
        return eZDBInterface::BINDING_NO;
    }

    function bindVariable( $value, $fieldDef = false )
    {
        return $value;
    }

    /**
     * Sends one statement; its result (see expMonetDBMapi::parse()), true when nothing was sent, false on an error.
     *
     * @param string $sql
     * @return array|bool
     */
    protected function run( $sql )
    {
        if ( !$this->IsConnected )
            return false;
        $sent = expMonetDBDialect::rewrite( $sql );
        if ( $sent === null )
            return true;

        eZDebug::accumulatorStart( 'monetdb_query', 'monetdb_total', 'MonetDB queries' );
        if ( $this->OutputSQL )
            $this->startTimer();
        $word = expMonetDBDialect::firstWord( $sent );
        $isWrite = expMonetDBDialect::isWrite( $sent );
        if ( $isWrite && !$this->InSQLTransaction && $this->writeLockEnabled() )
        {
            // a write outside a transaction: in a transaction of its own that holds the write lock
            list( $results, $error, $state ) = $this->acquireWriteLock();
            if ( $error === null )
            {
                list( $results, $error, $state ) = $this->send( $sent );
                if ( $error === null )
                {
                    list( $done, $error, $state ) = $this->send( 'COMMIT' );
                    $this->InSQLTransaction = false;
                }
                else
                    $this->abort();
            }
        }
        else
        {
            list( $results, $error, $state ) = $this->send( $sent );
            // a conflict of a single statement (no write lock, or another program writing): sent again
            for ( $try = 1; $error !== null && !$this->InSQLTransaction && $isWrite && $try <= (int)$this->Settings['ConflictRetries']
                            && expMonetDBDialect::errorCode( $state, $error ) === '40001'; $try++ )
            {
                usleep( 10000 * $try );
                list( $results, $error, $state ) = $this->send( $sent );
            }
        }
        if ( $this->OutputSQL )
        {
            $this->endTimer();
            if ( $this->timeTaken() > $this->SlowSQLTimeout )
                $this->reportQuery( __CLASS__, $sent, $error === null ? self::rowCount( $results ) : false, $this->timeTaken() );
        }
        eZDebug::accumulatorStop( 'monetdb_query' );
        if ( $word === 'COMMIT' || $word === 'ROLLBACK' )
            $this->InSQLTransaction = false;

        if ( $error !== null )
        {
            $this->ErrorMessage = $error;
            $this->ErrorNumber = expMonetDBDialect::errorCode( $state, $error );
            eZDebug::writeError( self::failedQueryMessage( $this->ErrorMessage, $sent ), __CLASS__ );
            if ( $this->errorHandling == eZDB::ERROR_HANDLING_EXCEPTIONS )
                throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
            $this->reportError();
            return false;
        }
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
        foreach ( $results as $result )
        {
            if ( $result['type'] === 'update' && $result['lastid'] > 0 )
            {
                $this->LastID = $result['lastid'];
                if ( preg_match( '/^\s*INSERT\s+INTO\s+"?([A-Za-z0-9_]+)"?/i', $sent, $m ) )
                    $this->LastIDs[strtolower( $m[1] )] = $result['lastid'];
            }
            if ( $result['type'] === 'trans' )
                $this->InSQLTransaction = !$result['autocommit'];
        }
        eZDBQueryCache::noteWrite( $this, $sent );
        return $results;
    }

    /**
     * Sends a statement as it is: array( results, error message or null, SQLSTATE ).
     *
     * @param string $sql
     * @return array
     */
    protected function send( $sql )
    {
        $results = $this->Mapi->query( $sql );
        $this->NumQueries++;
        if ( $results === false )
        {
            // the connection is gone: the transaction with it
            $this->IsConnected = $this->Mapi->isConnected();
            $this->InSQLTransaction = false;
            return array( false, (string)$this->Mapi->Error, (string)$this->Mapi->SQLState );
        }
        foreach ( $results as $result )
        {
            if ( $result['type'] === 'error' )
                return array( $results, $result['message'], $result['sqlstate'] );
            if ( $result['type'] === 'trans' )
                $this->InSQLTransaction = !$result['autocommit'];
        }
        return array( $results, null, '' );
    }

    /**
     * Ends an open transaction on the server without a message (a failed or aborted one).
     */
    protected function abort()
    {
        if ( $this->InSQLTransaction && $this->IsConnected )
            $this->Mapi->query( 'ROLLBACK' );
        $this->InSQLTransaction = false;
    }

    /**
     * Whether writers take the write lock (WriteLock=enabled).
     *
     * @return bool
     */
    public function writeLockEnabled()
    {
        return in_array( strtolower( trim( (string)$this->Settings['WriteLock'] ) ), array( '1', 'yes', 'true', 'enabled', 'on' ), true );
    }

    /**
     * Seconds a writer waits for the write lock: LockWait, else LockWaitWeb in a web request, LockWaitCommandLine on
     * the command line.
     *
     * @return float
     */
    public function lockWait()
    {
        if ( is_numeric( $this->Settings['LockWait'] ) )
            return max( 0.0, (float)$this->Settings['LockWait'] );
        $web = PHP_SAPI !== 'cli' || defined( 'QBIX_WEBSERVER' ) || isset( $_SERVER['QBIX_WORKER'] );
        return max( 0.0, (float)( $web ? $this->Settings['LockWaitWeb'] : $this->Settings['LockWaitCommandLine'] ) );
    }

    /**
     * Starts a transaction that holds the write lock: START TRANSACTION and a change of the lock row; while another
     * open transaction has changed it, rolled back and tried again (10 ms, doubling up to 250 ms) for lockWait()
     * seconds. Returns array( results, error or null, SQLSTATE ); 55P03 when the wait ran out.
     *
     * @return array
     */
    protected function acquireWriteLock()
    {
        if ( !$this->LockTableReady )
        {
            $error = $this->prepareLockTable();
            if ( $error !== null )
                return $error;
        }
        $deadline = microtime( true ) + $this->lockWait();
        $pause = 10000;
        while ( true )
        {
            list( $results, $error, $state ) = $this->send( 'START TRANSACTION' );
            if ( $error === null )
            {
                list( $results, $error, $state ) = $this->send( 'UPDATE ' . $this->lockTableName() . " SET n = n + 1 WHERE name = 'write'" );
                if ( $error === null )
                    return array( $results, null, '' );
            }
            $this->abort();
            if ( !$this->IsConnected || expMonetDBDialect::errorCode( $state, $error ) !== '40001' )
                return array( false, $error, $state );
            if ( microtime( true ) >= $deadline )
                return array( false, 'Another process holds the write lock of the MonetDB database (waited ' . $this->lockWait() . ' s)', '55P03' );
            $this->LockWaits++;
            usleep( $pause );
            $pause = min( 250000, $pause * 2 );
        }
    }

    /**
     * The lock table in the connection's schema, quoted ("<schema>"."monetdb_write_lock"): a name alone would also
     * find a lock table of the schema sys.
     *
     * @return string
     */
    public function lockTableName()
    {
        if ( $this->CurrentSchema === null )
        {
            list( $rows, $error, $state ) = $this->send( 'SELECT CURRENT_SCHEMA AS s' );
            $this->CurrentSchema = $error === null && isset( $rows[0]['rows'][0][0] ) ? (string)$rows[0]['rows'][0][0] : 'sys';
        }
        return self::quoteName( $this->CurrentSchema ) . '.' . self::LOCK_TABLE;
    }

    /**
     * Makes the lock table and its row when they are missing (several processes may try at once: a conflict or a
     * duplicate of another's is no error). Null when it is ready, else array( false, error, SQLSTATE ).
     *
     * @return array|null
     */
    protected function prepareLockTable()
    {
        $this->CurrentSchema = null;
        for ( $try = 0; $try < 20; $try++ )
        {
            list( $rows, $error, $state ) = $this->send( "SELECT n FROM " . $this->lockTableName() . " WHERE name = 'write'" );
            if ( $error === null && !empty( $rows[0]['rows'] ) )
            {
                $this->LockTableReady = true;
                return null;
            }
            if ( $error === null )
                $this->send( 'INSERT INTO ' . $this->lockTableName() . " ( name, n ) VALUES ( 'write', 0 )" );
            else
                $this->send( 'CREATE TABLE IF NOT EXISTS ' . $this->lockTableName() . ' ( name VARCHAR(64) NOT NULL PRIMARY KEY, n BIGINT DEFAULT 0 NOT NULL )' );
            if ( !$this->IsConnected )
                return array( false, (string)$this->Mapi->Error, (string)$this->Mapi->SQLState );
            usleep( 5000 * ( $try + 1 ) );
        }
        return array( false, 'The write lock table ' . self::LOCK_TABLE . ' of the MonetDB database could not be made', '55000' );
    }

    protected static function rowCount( $results )
    {
        foreach ( (array)$results as $result )
            if ( $result['type'] === 'table' )
                return count( $result['rows'] );
        return 0;
    }

    function query( $sql, $server = false )
    {
        $result = $this->run( $sql );
        return $result === false ? false : true;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        $cacheTicket = eZDBQueryCache::lookup( $this, $sql, $params, $cached );
        if ( $cached !== null )
            return $cached;
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
        if ( $limit !== false && $limit >= 0 || $offset > 0 )
        {
            $limited = expDBSQLHelpers::limit( 'monetdb', $sql, $limit !== false && $limit >= 0 ? $limit : false, $offset );
            if ( $limited === null )
                return array();
            $sql = $limited;
        }
        $results = $this->run( $sql );
        if ( !is_array( $results ) )
            return $results === false ? false : array();
        $table = null;
        foreach ( $results as $result )
            if ( $result['type'] === 'table' )
                $table = $result;
        if ( $table === null )
            return array();
        $names = $table['columns'];
        $types = $table['types'];
        $rows = array();
        foreach ( $table['rows'] as $i => $values )
        {
            $row = array();
            foreach ( $names as $c => $name )
                $row[$name] = self::value( isset( $values[$c] ) ? $values[$c] : null, isset( $types[$c] ) ? $types[$c] : '' );
            if ( $column === false )
                $rows[$i + $offset] = $row;
            elseif ( is_int( $column ) || ctype_digit( (string)$column ) )
            {
                $list = array_values( $row );
                $rows[$i + $offset] = isset( $list[(int)$column] ) ? $list[(int)$column] : null;
            }
            else
                $rows[$i + $offset] = array_key_exists( $column, $row ) ? $row[$column] : null;
        }
        eZDBQueryCache::store( $cacheTicket, $rows );
        return $rows;
    }

    /**
     * A value as the kernel reads values from MySQL and PostgreSQL (pure): text; a boolean 1 or 0; a timestamp
     * Y-m-d H:i:s.
     *
     * @param string|null $value
     * @param string $type MonetDB's type name
     * @return string|null
     */
    public static function value( $value, $type )
    {
        if ( $value === null )
            return null;
        switch ( $type )
        {
            case 'boolean':
                return $value === 'true' ? '1' : ( $value === 'false' ? '0' : $value );
            case 'timestamp':
            case 'timestamptz':
                return substr( $value, 0, 19 );
            case 'time':
            case 'timetz':
                return substr( $value, 0, 8 );
        }
        return $value;
    }

    /**
     * START TRANSACTION; with WriteLock=enabled the transaction takes the write lock first (see the class).
     */
    function beginQuery()
    {
        if ( !$this->IsConnected )
            return false;
        if ( !$this->writeLockEnabled() )
            return $this->query( 'START TRANSACTION' );
        list( $results, $error, $state ) = $this->acquireWriteLock();
        if ( $error === null )
            return true;
        $this->ErrorMessage = $error;
        $this->ErrorNumber = expMonetDBDialect::errorCode( $state, $error );
        eZDebug::writeError( $this->ErrorMessage, __METHOD__ );
        if ( $this->errorHandling == eZDB::ERROR_HANDLING_EXCEPTIONS )
            throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
        return false;
    }

    /**
     * COMMIT. A commit MonetDB refuses (a conflict with another program's writes) rolls the transaction back: false,
     * with the error number 40001.
     */
    function commitQuery()
    {
        if ( !$this->InSQLTransaction )
            return true;
        list( $results, $error, $state ) = $this->send( 'COMMIT' );
        $this->InSQLTransaction = false;
        if ( $error === null )
        {
            $this->ErrorMessage = '';
            $this->ErrorNumber = 0;
            return true;
        }
        $this->ErrorMessage = $error;
        $this->ErrorNumber = expMonetDBDialect::errorCode( $state, $error );
        eZDebug::writeError( self::failedQueryMessage( $this->ErrorMessage, 'COMMIT' ), __CLASS__ );
        return false;
    }

    function rollbackQuery()
    {
        if ( !$this->IsConnected )
            return false;
        $this->abort();
        return true;
    }

    /**
     * Runs $work in a transaction, and again (up to ConflictRetries times) when MonetDB refuses it for a conflict
     * (40001) or the write lock wait ran out (55P03). Returns what $work returns; any other error is thrown.
     *
     * @param callable $work function( expMonetDBDB $db )
     * @return mixed
     * @throws eZDBException
     */
    public function retryTransaction( callable $work )
    {
        $attempts = max( 0, (int)$this->Settings['ConflictRetries'] ) + 1;
        $old = $this->errorHandling;
        $this->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            for ( $n = 1; ; $n++ )
            {
                try
                {
                    $this->begin();
                    $value = $work( $this );
                    $this->commit();
                    if ( $this->ErrorNumber === '40001' )
                        throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
                    return $value;
                }
                catch ( eZDBException $e )
                {
                    $state = (string)$this->ErrorNumber;
                    while ( (int)$this->TransactionCounter > 0 )
                        $this->rollback();
                    if ( $n >= $attempts || !in_array( $state, array( '40001', '55P03' ), true ) )
                        throw $e;
                    usleep( 20000 * $n );
                }
            }
        }
        finally
        {
            $this->setErrorHandling( $old );
        }
    }

    /**
     * No table locks in MonetDB: the write lock (a transaction that holds it), whatever the tables.
     */
    function lock( $table )
    {
        $this->begin();
    }

    function unlock()
    {
        $this->commit();
    }

    /**
     * The id the last INSERT into $table gave its serial column, in this connection (MonetDB reports it with the
     * insert).
     */
    function lastSerialID( $table = false, $column = false )
    {
        if ( !$this->IsConnected )
            return false;
        if ( $table && isset( $this->LastIDs[strtolower( $table )] ) )
            return $this->LastIDs[strtolower( $table )];
        return $this->LastID > 0 ? $this->LastID : false;
    }

    function escapeString( $str )
    {
        return expMonetDBDialect::escape( $str );
    }

    function subString( $string, $from, $len = null )
    {
        if ( $len === null || $len === false )
            return " SUBSTRING( $string FROM $from ) ";
        return " SUBSTRING( $string FROM $from FOR $len ) ";
    }

    function concatString( $strings = array() )
    {
        return ' ' . implode( ' || ', $strings ) . ' ';
    }

    function md5( $str )
    {
        return " MD5( $str ) ";
    }

    /**
     * GROUP_CONCAT; the order inside it only where the server takes it (Dec2023 does not): else the order is left out,
     * as on SQLite before 3.44 (order the rows in a subquery when it matters).
     */
    public function sqlGroupConcat( $expression, $separator = ',', $orderBy = null )
    {
        if ( $orderBy !== null && trim( (string)$orderBy ) !== '' && !$this->groupConcatOrders() )
            $orderBy = null;
        return parent::sqlGroupConcat( $expression, $separator, $orderBy );
    }

    /** @var bool|null whether GROUP_CONCAT takes ORDER BY on this server, asked once */
    protected $GroupConcatOrders = null;

    /**
     * Whether the server takes GROUP_CONCAT( x, sep ORDER BY y ): asked once per connection.
     *
     * @return bool
     */
    public function groupConcatOrders()
    {
        if ( $this->GroupConcatOrders === null )
        {
            // a failed statement ends an open transaction in MonetDB: never asked inside one
            if ( !$this->IsConnected || $this->InSQLTransaction )
                return false;
            list( $results, $error, $state ) = $this->send( "SELECT GROUP_CONCAT( x, ',' ORDER BY x ) AS g FROM ( SELECT 'a' AS x ) AS t" );
            $this->GroupConcatOrders = $error === null;
        }
        return $this->GroupConcatOrders;
    }

    function supportedRelationTypeMask()
    {
        return eZDBInterface::RELATION_TABLE_BIT | eZDBInterface::RELATION_SEQUENCE_BIT
             | eZDBInterface::RELATION_VIEW_BIT | eZDBInterface::RELATION_INDEX_BIT;
    }

    function supportedRelationTypes()
    {
        return array( eZDBInterface::RELATION_TABLE, eZDBInterface::RELATION_SEQUENCE,
                      eZDBInterface::RELATION_VIEW, eZDBInterface::RELATION_INDEX );
    }

    /**
     * The catalog query listing the names of one kind of relation of the current schema.
     *
     * @param int $relationType
     * @return string|null
     */
    public static function relationListQuery( $relationType )
    {
        $schema = "(SELECT id FROM sys.schemas WHERE name = CURRENT_SCHEMA)";
        switch ( $relationType )
        {
            case eZDBInterface::RELATION_TABLE:
                return "SELECT name FROM sys.tables WHERE schema_id = $schema AND NOT system AND type = 0 ORDER BY name";
            case eZDBInterface::RELATION_SEQUENCE:
                return "SELECT name FROM sys.sequences WHERE schema_id = $schema ORDER BY name";
            case eZDBInterface::RELATION_VIEW:
                return "SELECT name FROM sys.tables WHERE schema_id = $schema AND NOT system AND type = 1 ORDER BY name";
            case eZDBInterface::RELATION_INDEX:
                return "SELECT i.name FROM sys.idxs i JOIN sys.tables t ON t.id = i.table_id
                        WHERE t.schema_id = $schema AND NOT t.system AND i.name NOT IN (SELECT name FROM sys.keys) ORDER BY i.name";
        }
        return null;
    }

    function relationList( $relationType = eZDBInterface::RELATION_TABLE )
    {
        $sql = self::relationListQuery( $relationType );
        if ( $sql === null )
        {
            eZDebug::writeError( "Unsupported relation type '$relationType'", __METHOD__ );
            return false;
        }
        return $this->IsConnected ? array_values( (array)$this->arrayQuery( $sql, array( 'column' => 'name' ) ) ) : array();
    }

    function relationCount( $relationType = eZDBInterface::RELATION_TABLE )
    {
        $list = $this->relationList( $relationType );
        return is_array( $list ) ? count( $list ) : false;
    }

    function relationCounts( $relationMask )
    {
        $count = 0;
        foreach ( $this->supportedRelationTypes() as $type )
            if ( $relationMask & ( 1 << $type ) )
                $count += (int)$this->relationCount( $type );
        return $count;
    }

    function eZTableList( $server = eZDBInterface::SERVER_MASTER )
    {
        $tables = array();
        foreach ( array( eZDBInterface::RELATION_TABLE, eZDBInterface::RELATION_SEQUENCE ) as $type )
            foreach ( (array)$this->relationList( $type ) as $name )
                if ( preg_match( '/^(ez|exp)/', $name ) )
                    $tables[$name] = $type;
        return $tables;
    }

    function relationMatchRegexp( $relationType )
    {
        return '#^(ez|exp|tmp_notification_rule_s)#';
    }

    function removeRelation( $relationName, $relationType )
    {
        $typeName = $this->relationName( $relationType );
        if ( !$typeName || !preg_match( '/^[A-Za-z0-9_]+$/', (string)$relationName ) )
        {
            eZDebug::writeError( "Unsupported relation type '$relationType' or name '$relationName'", __METHOD__ );
            return false;
        }
        return $this->query( "DROP $typeName IF EXISTS $relationName" . ( $relationType === eZDBInterface::RELATION_TABLE ? ' CASCADE' : '' ) );
    }

    /**
     * Restarts every sequence a column default reads (NEXT VALUE FOR <seq>) after the column's highest value, after
     * rows were inserted with their ids (an installation's data, an import).
     *
     * @return bool
     */
    function correctSequenceValues()
    {
        if ( !$this->IsConnected )
            return false;
        $rows = $this->arrayQuery( "SELECT t.name AS table_name, c.name AS column_name, c.\"default\" AS column_default
                                    FROM sys.columns c JOIN sys.tables t ON t.id = c.table_id
                                    WHERE t.schema_id = (SELECT id FROM sys.schemas WHERE name = CURRENT_SCHEMA) AND NOT t.system
                                    AND c.\"default\" LIKE 'next value for %'" );
        if ( !is_array( $rows ) )
            return false;
        foreach ( $rows as $row )
        {
            if ( !preg_match( '/next value for (?:"[^"]+"\.)?"?([A-Za-z0-9_]+)"?/i', (string)$row['column_default'], $m ) )
                continue;
            $sequence = $m[1];
            $table = $row['table_name'];
            $column = $row['column_name'];
            if ( !preg_match( '/^[A-Za-z0-9_]+$/', $table . $column ) )
                continue;
            $max = $this->arrayQuery( 'SELECT MAX(' . self::quoteName( $column ) . ') AS m FROM ' . self::quoteName( $table ) );
            $max = isset( $max[0]['m'] ) ? (int)$max[0]['m'] : 0;
            if ( $max < 1 )
                continue;
            $this->query( 'ALTER SEQUENCE ' . self::quoteName( $sequence ) . ' RESTART WITH ' . ( $max + 1 ) );
        }
        return true;
    }

    /**
     * The columns of a table: name => array( 'type' => MonetDB type, 'nullable' => bool, 'default' => text|null ).
     *
     * @param string $table
     * @return array
     */
    public function tableColumns( $table )
    {
        $columns = array();
        $rows = $this->arrayQuery( "SELECT c.name, c.type, c.type_digits, c.\"null\" AS nullable, c.\"default\" AS def
                                    FROM sys.columns c JOIN sys.tables t ON t.id = c.table_id
                                    WHERE t.schema_id = (SELECT id FROM sys.schemas WHERE name = CURRENT_SCHEMA)
                                    AND t.name = '" . $this->escapeString( $table ) . "' ORDER BY c.number" );
        foreach ( (array)$rows as $row )
            $columns[$row['name']] = array( 'type' => $row['type'], 'nullable' => $row['nullable'] === '1', 'default' => $row['def'] );
        return $columns;
    }

    function databaseServerVersion()
    {
        if ( !$this->IsConnected )
            return false;
        $rows = $this->arrayQuery( "SELECT value FROM sys.env() WHERE name = 'monet_version'" );
        $text = isset( $rows[0]['value'] ) ? (string)$rows[0]['value'] : '';
        return array( 'string' => $text, 'values' => explode( '.', $text ) );
    }

    function databaseClientVersion()
    {
        return array( 'string' => 'MAPI 9', 'values' => array( 9 ) );
    }

    function isCharsetSupported( $charset )
    {
        // MonetDB keeps text as UTF-8
        return in_array( strtolower( (string)$charset ), array( 'utf-8', 'utf8' ), true );
    }

    /**
     * The database this connection is on: monetdbd serves the others, each with its own login.
     */
    function availableDatabases()
    {
        return $this->DB ? array( $this->DB ) : array();
    }

    /**
     * MonetDB makes databases with its administration tool (monetdb create), not with SQL: the call fails with a
     * message that says so.
     */
    function createDatabase( $dbName )
    {
        $this->ErrorMessage = "MonetDB databases are made with 'monetdb create $dbName' on the server, not through SQL";
        $this->ErrorNumber = '0A000';
        eZDebug::writeError( $this->ErrorMessage, __METHOD__ );
    }

    /**
     * Empties the schema of the open database (its views, tables and sequences are dropped: the setup's "remove the
     * existing data"). Another database cannot be removed through SQL.
     *
     * @param string $dbName
     * @return bool
     */
    function removeDatabase( $dbName )
    {
        if ( !$this->IsConnected || ( $dbName !== '' && $dbName !== $this->DB ) )
            return false;
        $removed = 0;
        foreach ( array( eZDBInterface::RELATION_VIEW, eZDBInterface::RELATION_TABLE, eZDBInterface::RELATION_SEQUENCE ) as $type )
            foreach ( (array)$this->relationList( $type ) as $name )
                if ( $this->Mapi->query( 'DROP ' . $this->relationName( $type ) . ' ' . self::quoteName( $name )
                                         . ( $type === eZDBInterface::RELATION_SEQUENCE ? '' : ' CASCADE' ) ) !== false )
                    $removed++;
        $this->LockTableReady = false;
        eZDebug::writeNotice( "Emptied MonetDB database '$dbName': dropped $removed object(s)", __METHOD__ );
        return true;
    }

    function supportsDefaultValuesInsertion()
    {
        return true;
    }

    function close()
    {
        if ( $this->Mapi && $this->IsConnected )
        {
            if ( $this->InSQLTransaction )
                $this->Mapi->query( 'ROLLBACK' );
            $this->Mapi->close();
        }
        $this->InSQLTransaction = false;
        $this->IsConnected = false;
    }

    function setError()
    {
        if ( $this->Mapi && $this->Mapi->Error !== '' )
        {
            $this->ErrorMessage = (string)$this->Mapi->Error;
            $this->ErrorNumber = expMonetDBDialect::errorCode( (string)$this->Mapi->SQLState, $this->ErrorMessage );
        }
    }
}
