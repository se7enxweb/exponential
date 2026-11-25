<?php
/**
 * File containing the expSQLServerDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Microsoft SQL Server driver: SQL Server 2017, 2019, 2022, 2025, Azure SQL Database and Azure SQL Managed
 * Instance, over Microsoft's sqlsrv or pdo_sqlsrv extensions, PDO_ODBC with an ODBC driver for SQL Server, or
 * pdo_dblib (FreeTDS). site.ini:
 *
 * <code>
 * [DatabaseSettings]
 * DatabaseImplementation=sqlserver
 * Server=sql1.example.com
 * Port=1433
 * </code>
 *
 * and the [SQLServerSettings] of site.ini (layer, encryption, failover, lock timeout, literals). What the driver does
 * that the other drivers do not have to:
 *
 *  - Paging: arrayQuery()'s limit and offset become ORDER BY ... OFFSET n ROWS FETCH NEXT m ROWS ONLY on the server
 *    (expSQLServerDialect::applyLimit()); nothing is skipped or cut in PHP.
 *  - New ids: the INSERT and SCOPE_IDENTITY() go in one batch, so the id is the one of this statement (not of a
 *    trigger, as @@IDENTITY could be, and not lost in a parameterized call's own scope). An INSERT that gives the
 *    identity column NULL or 0 (MySQL's way of asking for the next number) leaves the column out; one that gives a
 *    real number runs with IDENTITY_INSERT on for that one statement.
 *  - Text: string literals are sent as bound NVARCHAR parameters where the statement allows it, and as N'...'
 *    literals elsewhere (expSQLServerDialect::parameterize(), unicodeLiterals()); both found by a lexer, never by a
 *    regular expression over the statement.
 *  - Errors: deadlock victims (1205), lock timeouts (1222), duplicate keys (2627/2601) and lost connections are told
 *    apart (expSQLServerErrorMap). A single statement outside a transaction that loses a deadlock is sent again;
 *    a lost connection outside a transaction is opened again (a persistent Velocity worker survives a server
 *    restart). Inside a transaction the transaction is invalid and the kernel rolls it back.
 *  - Temporary tables: CREATE TEMPORARY TABLE becomes a #table of the session; the driver drops every one it made
 *    at the end of a request, so a persistent connection does not carry them into the next.
 *  - Read replicas: plain SELECTs of an anonymous GET request may go to an Always On readable secondary
 *    ([DatabaseSettings] UseSlaveServer, SlaveServerArray: the listener, with ApplicationIntent=ReadOnly).
 */
class expSQLServerDB extends eZDBInterface
{
    /** databaseName() of this driver */
    const NAME = 'SQLServer';

    /** @var expSQLServerTransport|null */
    public $Transport = null;

    /** @var expSQLServerTransport|null the read replica's connection (ReplicaConnection holds the same) */
    public $ReplicaTransport = null;

    /** @var array the effective [SQLServerSettings] */
    public $Settings = array();

    /** @var array|null what the server said about itself at connect: version, edition, rcsi, collation, spid */
    public $ServerInfo = null;

    /** @var array|null lower-case table name => identity column, read once per connection */
    public $IdentityColumns = null;

    /** @var array|null array( table, id ) of the last INSERT that made an id */
    public $LastInsert = null;

    /** @var array #temporary tables this connection made and has not dropped: name => true */
    public $TempTables = array();

    /** @var int statements sent to the server (each attempt counts) */
    public $StatementCount = 0;

    /** @var int connections opened again after one was lost */
    public $Reconnects = 0;

    /** @var int statements sent again after a deadlock or a transient error */
    public $Retries = 0;

    /** @var string the last statement as it was sent (after the driver's rewrites), for tests and the debug output */
    public $LastStatement = '';

    /** @var array the parameters of LastStatement */
    public $LastParameters = array();

    /** @var string|null the category of the last error (expSQLServerErrorMap) */
    public $ErrorCategory = null;

    /** @var array the defaults of [SQLServerSettings] */
    const DEFAULT_SETTINGS = array(
        'Layer' => 'auto',
        'ODBCDriver' => 'ODBC Driver 18 for SQL Server',
        'Encrypt' => 'yes',
        'TrustServerCertificate' => 'disabled',
        'MultiSubnetFailover' => 'disabled',
        'ApplicationIntent' => 'ReadWrite',
        'ReplicaApplicationIntent' => 'ReadOnly',
        'LoginTimeout' => 15,
        'ConnectRetryCount' => 0,
        'LockTimeout' => -1,
        'LiteralMode' => 'bind',
        'DeadlockRetries' => 3,
        'DeadlockRetryWait' => 50,
        'ReconnectOnLoss' => 'enabled',
        'ApplicationName' => 'Exponential',
        'InListChunkSize' => 1000,
        'DatabaseCollation' => '',
    );

    /**
     * Connects to the server: the [DatabaseSettings] parameters of eZDB::instance(), and optionally
     * 'sqlserver' => array( <[SQLServerSettings] names> => value ) over site.ini's [SQLServerSettings], and
     * 'sqlserver_transport' => an expSQLServerTransport to use instead of one of PHP's layers.
     *
     * @param array $parameters
     * @throws eZDBNoConnectionException when no connection can be opened
     */
    public function __construct( $parameters )
    {
        parent::__construct( $parameters );
        $this->Settings = self::settings( isset( $parameters['sqlserver'] ) && is_array( $parameters['sqlserver'] ) ? $parameters['sqlserver'] : array() );
        if ( isset( $parameters['sqlserver_transport'] ) && $parameters['sqlserver_transport'] instanceof expSQLServerTransport )
            $this->Transport = $parameters['sqlserver_transport'];
        else
        {
            $layer = expSQLServerTransport::chooseLayer( $this->Settings['Layer'] );
            if ( $layer === null )
            {
                $text = 'No PHP extension for SQL Server was found (sqlsrv, pdo_sqlsrv, PDO_ODBC or pdo_dblib), the DB handler will not be initialized.';
                if ( function_exists( 'eZAppendWarningItem' ) )
                    eZAppendWarningItem( array( 'error' => array( 'type' => 'ezdb', 'number' => eZDBInterface::ERROR_MISSING_EXTENSION ), 'text' => $text ) );
                eZDebug::writeWarning( $text, __CLASS__ );
                $this->IsConnected = false;
                return;
            }
            $this->Transport = expSQLServerTransport::create( $layer );
        }
        eZDebug::createAccumulatorGroup( 'sqlserver_total', 'SQL Server Total' );

        $attempts = max( 0, (int)$this->connectRetryCount() );
        $wait = max( 0, (int)$this->connectRetryWaitTime() );
        $connected = $this->openPrimary();
        for ( $n = 1; !$connected && $n <= $attempts; $n++ )
        {
            if ( $wait )
                sleep( $wait );
            $connected = $this->openPrimary();
        }
        if ( !$connected )
        {
            $this->setError();
            throw new eZDBNoConnectionException( (string)$this->Server, $this->ErrorMessage, $this->ErrorNumber );
        }
    }

    /**
     * The effective [SQLServerSettings]: the defaults, site.ini, then $overrides.
     *
     * @param array $overrides
     * @return array
     */
    public static function settings( array $overrides = array() )
    {
        $settings = self::DEFAULT_SETTINGS;
        if ( class_exists( 'eZINI', false ) || class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance();
            if ( $ini->hasGroup( 'SQLServerSettings' ) )
                foreach ( $ini->group( 'SQLServerSettings' ) as $name => $value )
                    if ( array_key_exists( $name, $settings ) && $value !== '' && $value !== null )
                        $settings[$name] = $value;
        }
        foreach ( $overrides as $name => $value )
            $settings[$name] = $value;
        return $settings;
    }

    /**
     * The connection settings of the primary (or, with $replica, of the read replica) for the transport.
     *
     * @param bool $replica
     * @return array
     */
    public function connectionConfig( $replica = false )
    {
        $s = $this->Settings;
        return array(
            'host' => $replica ? $this->SlaveServer : $this->Server,
            'port' => $replica ? $this->SlavePort : $this->Port,
            'database' => $replica ? ( $this->SlaveDB ? $this->SlaveDB : $this->DB ) : $this->DB,
            'user' => $replica ? ( $this->SlaveUser ? $this->SlaveUser : $this->User ) : $this->User,
            'password' => $replica ? ( $this->SlaveUser ? $this->SlavePassword : $this->Password ) : $this->Password,
            'encrypt' => $s['Encrypt'],
            'trust_server_certificate' => expSQLServerDialect::flag( $s['TrustServerCertificate'] ),
            'multi_subnet_failover' => expSQLServerDialect::flag( $s['MultiSubnetFailover'] ),
            'application_intent' => $replica ? $s['ReplicaApplicationIntent'] : $s['ApplicationIntent'],
            'login_timeout' => (int)$s['LoginTimeout'],
            'connect_retry_count' => (int)$s['ConnectRetryCount'],
            'app_name' => $s['ApplicationName'],
            'odbc_driver' => $s['ODBCDriver'],
        );
    }

    /**
     * Opens the primary connection and prepares the session.
     *
     * @return bool
     */
    protected function openPrimary()
    {
        eZDebug::accumulatorStart( 'sqlserver_connection', 'sqlserver_total', 'Database connection' );
        $ok = $this->Transport->open( $this->connectionConfig() );
        eZDebug::accumulatorStop( 'sqlserver_connection' );
        if ( !$ok )
        {
            $this->IsConnected = false;
            return false;
        }
        $this->DBConnection = $this->Transport;
        $this->DBWriteConnection = $this->Transport;
        $this->IsConnected = true;
        $this->IdentityColumns = null;
        $this->TempTables = array();
        if ( !$this->prepareSession( $this->Transport ) )
        {
            $this->Transport->close();
            $this->IsConnected = false;
            return false;
        }
        return true;
    }

    /**
     * Runs the session setup on a new connection and reads what the server is (the first connection only).
     *
     * @param expSQLServerTransport $transport
     * @return bool
     */
    protected function prepareSession( expSQLServerTransport $transport )
    {
        if ( $transport->run( expSQLServerDialect::sessionSetup( (int)$this->Settings['LockTimeout'] ) ) === false )
            return false;
        if ( $this->ServerInfo === null && $transport === $this->Transport )
        {
            $result = $transport->run( "SELECT CAST(SERVERPROPERTY('ProductVersion') AS NVARCHAR(64)) AS version,"
                                     . " CAST(SERVERPROPERTY('EngineEdition') AS INT) AS edition,"
                                     . " CAST(SERVERPROPERTY('Edition') AS NVARCHAR(128)) AS edition_name,"
                                     . " (SELECT is_read_committed_snapshot_on FROM sys.databases WHERE database_id = DB_ID()) AS rcsi,"
                                     . " CAST(DATABASEPROPERTYEX(DB_NAME(), 'Collation') AS NVARCHAR(128)) AS collation,"
                                     . " @@SPID AS spid" );
            if ( $result === false || !isset( $result['sets'][0][0] ) )
                return false;
            $row = $result['sets'][0][0];
            $this->ServerInfo = expSQLServerDialect::describeVersion( $row['version'], $row['edition'] ) + array(
                'edition' => (int)$row['edition'], 'edition_name' => (string)$row['edition_name'],
                'rcsi' => (string)$row['rcsi'] === '1', 'collation' => (string)$row['collation'], 'spid' => (int)$row['spid'] );
            if ( !$this->ServerInfo['supported'] )
                eZDebug::writeWarning( $this->ServerInfo['name'] . ' (' . $this->ServerInfo['string'] . ') is not supported: ' . $this->ServerInfo['note'], __CLASS__ );
            if ( !$this->ServerInfo['rcsi'] )
                eZDebugSetting::writeDebug( 'kernel-db-sqlserver', 'READ_COMMITTED_SNAPSHOT is off for this database: readers wait for writers. '
                    . 'ALTER DATABASE [' . $this->DB . '] SET READ_COMMITTED_SNAPSHOT ON is recommended.', __CLASS__ );
        }
        return true;
    }

    /**
     * Opens the connection again (after it was lost, or by request); temporary tables and the identity cache of the
     * old session are gone with it.
     *
     * @return bool
     */
    public function reconnect()
    {
        if ( !$this->Transport )
            return false;
        $this->Transport->close();
        $ok = $this->openPrimary();
        if ( $ok )
            $this->Reconnects++;
        return $ok;
    }

    /**
     * Whether the connection still answers (SELECT 1).
     *
     * @return bool
     */
    public function ping()
    {
        return $this->Transport && $this->Transport->isOpen() && $this->Transport->run( 'SELECT 1 AS ok' ) !== false;
    }

    function databaseName()
    {
        return self::NAME;
    }

    /**
     * The PHP layer in use: sqlsrv, pdo_sqlsrv, pdo_odbc, pdo_dblib (or a test transport's name).
     *
     * @return string
     */
    public function layer()
    {
        return $this->Transport ? $this->Transport->name() : '';
    }

    function bindingType()
    {
        return eZDBInterface::BINDING_NO;
    }

    function bindVariable( $value, $fieldDef = false )
    {
        return $value;
    }

    // ------------------------------------------------------------------ statements

    /**
     * A statement as it goes to the server: MySQL backticks as brackets, the identity column handled, and its
     * literals bound or written as N'...'.
     *
     * @param string $sql
     * @return array keys: sql, params, identity_table (string|null: capture the new id), identity_insert (string|null:
     *               table to switch IDENTITY_INSERT on for)
     */
    public function prepareStatement( $sql )
    {
        $sql = expSQLServerDialect::backticksToBrackets( trim( (string)$sql ) );
        $identityTable = null;
        $identityInsert = null;
        $kind = expSQLServerDialect::statementKind( $sql );
        if ( $kind === 'INSERT' )
        {
            $parsed = expSQLServerDialect::parseInsert( $sql );
            if ( $parsed !== null )
            {
                $column = $this->identityColumn( $parsed['table'] );
                if ( $column !== null )
                {
                    $rewrite = expSQLServerDialect::rewriteIdentityInsert( $sql, $column );
                    $sql = $rewrite['sql'];
                    if ( $rewrite['identity_insert'] )
                        $identityInsert = $parsed['tableText'];
                    $identityTable = $parsed['table'];
                }
            }
        }
        $params = array();
        $mode = strtolower( (string)$this->Settings['LiteralMode'] );
        $bound = null;
        if ( $mode !== 'prefix' )
            $bound = expSQLServerDialect::parameterize( $sql );
        if ( $bound !== null )
            list( $sql, $params ) = $bound;
        else
            $sql = expSQLServerDialect::unicodeLiterals( $sql );
        if ( $identityTable !== null )
        {
            $sql = rtrim( $sql );
            while ( substr( $sql, -1 ) === ';' )
                $sql = rtrim( substr( $sql, 0, -1 ) );
            $sql .= ";\nSELECT CAST(SCOPE_IDENTITY() AS BIGINT) AS exp_identity";
        }
        if ( $identityInsert !== null )
            $sql = 'SET IDENTITY_INSERT ' . $identityInsert . " ON;\n" . $sql . ";\nSET IDENTITY_INSERT " . $identityInsert . ' OFF';
        return array( 'sql' => $sql, 'params' => $params, 'identity_table' => $identityTable, 'identity_insert' => $identityInsert );
    }

    /**
     * Runs a statement: on a read replica when it may, else on the primary; sends it again once after a lost
     * connection and up to DeadlockRetries times after a deadlock or a transient error, both only outside a
     * transaction. The result of the transport, or false.
     *
     * @param string $sql
     * @param int|false $server
     * @param string $caller for the messages
     * @return array|false
     * @throws eZDBException with the exceptions error handling
     */
    protected function execute( $sql, $server = false, $caller = 'query' )
    {
        if ( !$this->IsConnected || !$this->Transport )
        {
            eZDebug::writeError( 'Trying to do a query without being connected to a database!', __CLASS__ );
            return false;
        }
        $prepared = $this->prepareStatement( $sql );
        eZDebug::accumulatorStart( 'sqlserver_query', 'sqlserver_total', 'SQL Server queries' );
        if ( $this->OutputSQL )
            $this->startTimer();

        $result = false;
        $replica = $this->replicaConnectionFor( $sql, $server );
        if ( $replica )
            $result = $this->replicaRun( $replica, $prepared );
        else
            $this->notePrimaryStatement( $sql );

        $error = null;
        if ( $result === false )
        {
            $reconnected = false;
            $retries = 0;
            $maxRetries = max( 0, (int)$this->Settings['DeadlockRetries'] );
            while ( true )
            {
                $this->LastStatement = $prepared['sql'];
                $this->LastParameters = $prepared['params'];
                $this->StatementCount++;
                $result = $this->Transport->run( $prepared['sql'], $prepared['params'] );
                if ( $result !== false )
                    break;
                $error = $this->Transport->error();
                $category = expSQLServerErrorMap::category( $error['number'], $error['state'], $error['message'] );
                if ( $prepared['identity_insert'] !== null && !expSQLServerErrorMap::isConnectionLost( $category ) )
                {
                    // The batch stopped before its own SET IDENTITY_INSERT ... OFF: one table per session may have
                    // it on, and a later explicit insert into another table would fail (8107).
                    $this->Transport->run( 'SET IDENTITY_INSERT ' . $prepared['identity_insert'] . ' OFF' );
                }
                $outsideTransaction = (int)$this->TransactionCounter === 0;
                if ( expSQLServerErrorMap::isConnectionLost( $category ) && $outsideTransaction && !$reconnected
                     && !$this->TempTables && expSQLServerDialect::flag( $this->Settings['ReconnectOnLoss'] ) )
                {
                    $reconnected = true;
                    if ( $this->reconnect() )
                    {
                        $prepared = $this->prepareStatement( $sql );
                        continue;
                    }
                    $error = $this->Transport->error() ?: $error;
                    break;
                }
                if ( expSQLServerErrorMap::isRetryable( $category ) && $outsideTransaction && $retries < $maxRetries )
                {
                    $retries++;
                    $this->Retries++;
                    $wait = (int)$this->Settings['DeadlockRetryWait'];
                    if ( $wait > 0 )
                        usleep( $wait * 1000 * $retries );
                    continue;
                }
                break;
            }
        }

        if ( $this->OutputSQL )
        {
            $this->endTimer();
            if ( $this->timeTaken() > $this->SlowSQLTimeout )
                $this->reportQuery( __CLASS__, $sql, false, $this->timeTaken() );
        }
        eZDebug::accumulatorStop( 'sqlserver_query' );

        if ( $result === false )
        {
            $this->takeError( $error !== null ? $error : $this->Transport->error() );
            eZDebug::writeError( self::failedQueryMessage( $this->ErrorMessage, $sql ), __CLASS__ . '::' . $caller );
            if ( (int)$this->TransactionCounter > 0 && expSQLServerErrorMap::endsTransaction( $this->ErrorCategory ) )
                $this->invalidateTransaction();
            if ( $this->errorHandling == eZDB::ERROR_HANDLING_EXCEPTIONS )
                throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
            $this->reportError();
            return false;
        }

        if ( $prepared['identity_table'] !== null )
        {
            $id = null;
            foreach ( $result['sets'] as $k => $set )
                if ( isset( $set[0] ) && array_key_exists( 'exp_identity', $set[0] ) )
                {
                    $id = $set[0]['exp_identity'];
                    unset( $result['sets'][$k] );
                }
            $result['sets'] = array_values( $result['sets'] );
            if ( $id !== null && $id !== '' )
                $this->LastInsert = array( $prepared['identity_table'], $id );
        }
        if ( expSQLServerDialect::isSchemaChange( $sql ) )
            $this->IdentityColumns = null;
        eZDBQueryCache::noteWrite( $this, $sql );
        return $result;
    }

    /**
     * Takes the error of the transport into ErrorNumber (the native number, or the SQLSTATE when there is none),
     * ErrorMessage (without any password) and ErrorCategory.
     *
     * @param array|null $error
     */
    protected function takeError( $error )
    {
        if ( !is_array( $error ) )
            $error = array( 'number' => null, 'state' => null, 'message' => 'Unknown SQL Server error' );
        $this->ErrorCategory = expSQLServerErrorMap::category( $error['number'], $error['state'], $error['message'] );
        $this->ErrorNumber = $error['number'] !== null ? (int)$error['number'] : ( $error['state'] !== null ? $error['state'] : -1 );
        $this->ErrorMessage = $this->mask( $error['message'] );
    }

    function setError()
    {
        if ( $this->Transport )
            $this->takeError( $this->Transport->error() );
    }

    /**
     * A text without the passwords of this connection.
     *
     * @param string $text
     * @return string
     */
    public function mask( $text )
    {
        $text = (string)$text;
        foreach ( array( $this->Password, $this->SlavePassword ) as $secret )
            if ( is_string( $secret ) && $secret !== '' )
                $text = str_replace( $secret, '***', $text );
        return $text;
    }

    function query( $sql, $server = false )
    {
        return $this->execute( $sql, $server, 'query' ) !== false;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        $cacheTicket = eZDBQueryCache::lookup( $this, $sql, $params, $cached );
        if ( $cached !== null )
            return $cached;
        if ( !$this->IsConnected )
            return array();
        $limit = false;
        $offset = 0;
        $column = false;
        if ( is_array( $params ) )
        {
            if ( isset( $params['limit'] ) && is_numeric( $params['limit'] ) )
                $limit = (int)$params['limit'];
            if ( isset( $params['offset'] ) && is_numeric( $params['offset'] ) )
                $offset = max( 0, (int)$params['offset'] );
            if ( isset( $params['column'] ) && ( is_numeric( $params['column'] ) || is_string( $params['column'] ) ) )
                $column = $params['column'];
        }
        $paged = expSQLServerDialect::applyLimit( $sql, $limit, $offset );
        if ( $paged === null )
            return array();
        $result = $this->execute( $paged, $server, 'arrayQuery' );
        if ( $result === false )
            return false;
        $rows = isset( $result['sets'][0] ) ? $result['sets'][0] : array();
        $retArray = array();
        foreach ( $rows as $i => $row )
        {
            if ( $column === false )
                $retArray[$i + $offset] = $row;
            else if ( is_int( $column ) || ctype_digit( (string)$column ) )
            {
                $values = array_values( $row );
                $retArray[$i + $offset] = isset( $values[(int)$column] ) ? $values[(int)$column] : null;
            }
            else
                $retArray[$i + $offset] = array_key_exists( $column, $row ) ? $row[$column] : null;
        }
        eZDBQueryCache::store( $cacheTicket, $retArray );
        return $retArray;
    }

    /**
     * The identity column of a table, or null; read once per connection for all tables (sys.identity_columns), for a
     * #temporary table from tempdb.
     *
     * @param string $table without brackets
     * @return string|null
     */
    public function identityColumn( $table )
    {
        $table = (string)$table;
        if ( $table === '' )
            return null;
        $key = strtolower( $table );
        if ( $table[0] === '#' )
        {
            if ( $this->IdentityColumns !== null && array_key_exists( $key, $this->IdentityColumns ) )
                return $this->IdentityColumns[$key];
            $result = $this->Transport->run( 'SELECT name FROM tempdb.sys.identity_columns WHERE object_id = OBJECT_ID(?)', array( 'tempdb..' . $table ) );
            $column = $result !== false && isset( $result['sets'][0][0]['name'] ) ? $result['sets'][0][0]['name'] : null;
            if ( $this->IdentityColumns === null )
                $this->loadIdentityColumns();
            $this->IdentityColumns[$key] = $column;
            return $column;
        }
        if ( $this->IdentityColumns === null )
            $this->loadIdentityColumns();
        return isset( $this->IdentityColumns[$key] ) ? $this->IdentityColumns[$key] : null;
    }

    /**
     * Reads every table's identity column at once.
     */
    protected function loadIdentityColumns()
    {
        $this->IdentityColumns = array();
        $result = $this->Transport->run( 'SELECT t.name AS table_name, c.name AS column_name FROM sys.identity_columns c'
                                       . ' JOIN sys.tables t ON t.object_id = c.object_id' );
        if ( $result === false || !isset( $result['sets'][0] ) )
            return;
        foreach ( $result['sets'][0] as $row )
            $this->IdentityColumns[strtolower( $row['table_name'] )] = $row['column_name'];
    }

    /**
     * The id the last INSERT into $table (any table without $table) made, from SCOPE_IDENTITY() in the INSERT's own
     * batch; false when there is none.
     *
     * @param string|false $table
     * @param string|false $column
     * @return int|string|false
     */
    function lastSerialID( $table = false, $column = false )
    {
        if ( $this->LastInsert === null )
            return false;
        if ( $table !== false && $table !== null && $table !== '' )
        {
            $name = expSQLServerDialect::insertTable( 'INSERT INTO ' . $table . ' DEFAULT VALUES' );
            if ( $name === null || strcasecmp( $name, $this->LastInsert[0] ) !== 0 )
                return false;
        }
        $id = $this->LastInsert[1];
        return ( is_numeric( $id ) && abs( (float)$id ) < PHP_INT_MAX ) ? (int)$id : $id;
    }

    // ------------------------------------------------------------------ transactions and locks

    function beginQuery()
    {
        return $this->query( 'BEGIN TRANSACTION' );
    }

    function commitQuery()
    {
        return $this->query( 'IF @@TRANCOUNT > 0 COMMIT TRANSACTION' );
    }

    function rollbackQuery()
    {
        return $this->query( 'IF @@TRANCOUNT > 0 ROLLBACK TRANSACTION' );
    }

    /**
     * MySQL's LOCK TABLES: a transaction that holds an exclusive lock on each table until unlock().
     *
     * @param string|array $table a name, or array( array( 'table' => name ), ... ) as the other drivers take it
     */
    function lock( $table )
    {
        $this->begin();
        if ( !$this->IsConnected )
            return;
        $tables = array();
        if ( is_array( $table ) )
        {
            foreach ( $table as $entry )
                $tables[] = is_array( $entry ) ? $entry['table'] : $entry;
        }
        else
            $tables[] = $table;
        foreach ( $tables as $name )
            $this->query( 'SELECT TOP (0) 1 AS locked FROM ' . expSQLServerDialect::quoteName( $name ) . ' WITH (TABLOCKX, HOLDLOCK)' );
    }

    function unlock()
    {
        $this->commit();
    }

    /**
     * An application lock of this session (sp_getapplock): a named mutex between connections, for work that is
     * not tied to a table (cluster maintenance, a job that must run once). True when it was granted.
     *
     * @param string $resource
     * @param int $timeoutMs 0: do not wait; -1: wait without end
     * @param string $mode Exclusive, Shared, Update
     * @return bool
     */
    public function getAppLock( $resource, $timeoutMs = 0, $mode = 'Exclusive' )
    {
        $mode = in_array( $mode, array( 'Exclusive', 'Shared', 'Update', 'IntentShared', 'IntentExclusive' ), true ) ? $mode : 'Exclusive';
        $result = $this->execute( 'DECLARE @r INT; EXEC @r = sp_getapplock @Resource = ' . expSQLServerDialect::stringLiteral( substr( (string)$resource, 0, 255 ) )
                                . ", @LockMode = '$mode', @LockOwner = 'Session', @LockTimeout = " . (int)$timeoutMs . '; SELECT @r AS result', false, 'getAppLock' );
        return $result !== false && isset( $result['sets'][0][0]['result'] ) && (int)$result['sets'][0][0]['result'] >= 0;
    }

    /**
     * Releases an application lock of this session. True when it was held.
     *
     * @param string $resource
     * @return bool
     */
    public function releaseAppLock( $resource )
    {
        $result = $this->execute( 'DECLARE @r INT; EXEC @r = sp_releaseapplock @Resource = ' . expSQLServerDialect::stringLiteral( substr( (string)$resource, 0, 255 ) )
                                . ", @LockOwner = 'Session'; SELECT @r AS result", false, 'releaseAppLock' );
        return $result !== false && isset( $result['sets'][0][0]['result'] ) && (int)$result['sets'][0][0]['result'] === 0;
    }

    /**
     * Runs $work in a transaction and runs it again, up to DeadlockRetries times, when the server chose it as a
     * deadlock victim (or failed over): the whole transaction is lost then, and only the caller can run it again.
     * Returns what $work returns; any other error is thrown (eZDBException).
     *
     * @param callable $work function( expSQLServerDB $db )
     * @return mixed
     * @throws eZDBException
     */
    public function retryOnDeadlock( callable $work )
    {
        $attempts = max( 0, (int)$this->Settings['DeadlockRetries'] ) + 1;
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
                    return $value;
                }
                catch ( eZDBException $e )
                {
                    while ( (int)$this->TransactionCounter > 0 )
                        $this->rollback();
                    if ( $n >= $attempts || !expSQLServerErrorMap::isRetryable( (string)$this->ErrorCategory ) )
                        throw $e;
                    $this->Retries++;
                    usleep( (int)$this->Settings['DeadlockRetryWait'] * 1000 * $n );
                }
            }
        }
        finally
        {
            $this->setErrorHandling( $old );
        }
    }

    // ------------------------------------------------------------------ temporary tables

    function generateUniqueTempTableName( $pattern, $randomizeIndex = false, $server = self::SERVER_SLAVE )
    {
        if ( $randomizeIndex === false )
            $randomizeIndex = mt_rand( 10, 1000 );
        do
        {
            $name = str_replace( '%', $randomizeIndex, (string)$pattern );
            if ( $name === '' || $name[0] !== '#' )
                $name = '#' . $name;
            $randomizeIndex++;
        }
        while ( isset( $this->TempTables[strtolower( $name )] ) );
        return $name;
    }

    function createTempTable( $createTableQuery = '', $server = self::SERVER_SLAVE )
    {
        $temporary = expSQLServerDialect::temporaryTable( $createTableQuery );
        if ( $temporary === null )
            return $this->query( $createTableQuery );
        list( $sql, $name ) = $temporary;
        // A #table belongs to the session that made it: always the primary, never the replica
        $this->ReplicaPinned = $this->UseSlaveServer ? true : $this->ReplicaPinned;
        $ok = $this->query( $sql );
        if ( $ok )
            $this->TempTables[strtolower( $name )] = $name;
        return $ok;
    }

    function dropTempTable( $dropTableQuery = '', $server = self::SERVER_SLAVE )
    {
        if ( preg_match( '/^\s*DROP\s+(?:TEMPORARY\s+)?TABLE\s+(?:IF\s+EXISTS\s+)?([#\w\[\]]+)\s*;?\s*$/i', (string)$dropTableQuery, $m ) )
        {
            $name = trim( $m[1], '[]' );
            if ( $name[0] !== '#' && !isset( $this->TempTables[strtolower( '#' . $name )] ) )
                return $this->query( $dropTableQuery );
            if ( $name[0] !== '#' )
                $name = '#' . $name;
            $ok = $this->query( expSQLServerDialect::dropTemporaryTable( $name ) );
            unset( $this->TempTables[strtolower( $name )] );
            return $ok;
        }
        return $this->query( $dropTableQuery );
    }

    function dropTempTableList( $tableList, $server = self::SERVER_SLAVE )
    {
        foreach ( $tableList as $name )
            $this->dropTempTable( 'DROP TABLE ' . $name, $server );
    }

    /**
     * Drops the #temporary tables this connection still has, so a persistent connection does not carry them into
     * the next request. Called at each request start (resetReplicaState()); nothing happens inside a transaction.
     *
     * @return int how many were dropped
     */
    public function dropLeftoverTempTables()
    {
        if ( !$this->TempTables || !$this->IsConnected || (int)$this->TransactionCounter > 0 )
            return 0;
        $sql = array();
        foreach ( $this->TempTables as $name )
            $sql[] = expSQLServerDialect::dropTemporaryTable( $name );
        $count = count( $sql );
        $this->Transport->run( implode( ";\n", $sql ) );
        $this->TempTables = array();
        return $count;
    }

    public function resetReplicaState()
    {
        parent::resetReplicaState();
        $this->dropLeftoverTempTables();
    }

    // ------------------------------------------------------------------ read replica

    /**
     * Whether a statement may run on a read replica (expSQLServerDialect::isReplicaReadable()).
     *
     * @param string $sql
     * @return bool
     */
    public static function isReplicaReadable( $sql )
    {
        return expSQLServerDialect::isReplicaReadable( $sql );
    }

    /**
     * The replica transport for this statement, or false for the primary. The first statement of a request that
     * may use the replica decides for the whole request, as in the other drivers: the request must be allowed to
     * (requestMayReadReplica()), the replica must answer, and must be readable (a secondary, or a primary the
     * listener sent us to) within SlaveServerMaxLag.
     *
     * @param string $sql
     * @param int|false $server
     * @return expSQLServerTransport|false
     */
    protected function replicaConnectionFor( $sql, $server )
    {
        if ( !$this->UseSlaveServer || $this->ReplicaPinned || $this->ReplicaState === 'off'
             || $server === eZDBInterface::SERVER_MASTER || (int)$this->TransactionCounter > 0
             || !self::isReplicaReadable( $sql ) )
            return false;
        if ( $this->ReplicaState === null )
        {
            $this->ReplicaState = 'off';
            if ( !$this->SlaveServer || !self::requestMayReadReplica( $_SERVER, $_COOKIE, self::isReplicaWebProcess() ) )
                return false;
            if ( !$this->ReplicaTransport || !$this->ReplicaTransport->isOpen() )
                $this->ReplicaTransport = $this->connectReplica();
            $this->ReplicaConnection = $this->ReplicaTransport ? $this->ReplicaTransport : false;
            if ( !$this->ReplicaTransport )
                return false;
            $state = $this->ReplicaTransport->run( "SELECT CAST(DATABASEPROPERTYEX(DB_NAME(), 'Updateability') AS NVARCHAR(16)) AS updateability" );
            $updateability = $state !== false && isset( $state['sets'][0][0]['updateability'] ) ? $state['sets'][0][0]['updateability'] : null;
            $maxLag = (int)$this->replicaSetting( 'SlaveServerMaxLag', 0 );
            $lag = null;
            if ( $maxLag > 0 )
            {
                $lagResult = $this->ReplicaTransport->run( 'SELECT MAX(secondary_lag_seconds) AS lag FROM sys.dm_hadr_database_replica_states'
                                                         . ' WHERE is_local = 1 AND database_id = DB_ID()' );
                if ( $lagResult !== false && isset( $lagResult['sets'][0][0]['lag'] ) )
                    $lag = $lagResult['sets'][0][0]['lag'];
            }
            if ( !expSQLServerDialect::replicaUsable( $updateability, $lag, $maxLag ) )
            {
                eZDebug::writeWarning( "The read replica {$this->SlaveServer} is not readable (" . var_export( $updateability, true )
                                     . ( $lag !== null ? ", {$lag}s behind" : '' ) . '), reading from the primary', __METHOD__ );
                return false;
            }
            $this->ReplicaState = 'on';
        }
        return $this->ReplicaTransport ? $this->ReplicaTransport : false;
    }

    /**
     * Opens the replica connection: once, no retries.
     *
     * @return expSQLServerTransport|false
     */
    protected function connectReplica()
    {
        $transport = isset( $this->Settings['ReplicaTransport'] ) && $this->Settings['ReplicaTransport'] instanceof expSQLServerTransport
            ? $this->Settings['ReplicaTransport'] : expSQLServerTransport::create( $this->Transport->name() );
        $config = $this->connectionConfig( true );
        $config['login_timeout'] = min( 5, max( 1, (int)$config['login_timeout'] ) );
        if ( !$transport->open( $config ) || !$this->prepareSession( $transport ) )
        {
            $transport->close();
            eZDebugSetting::writeDebug( 'kernel-db-sqlserver', "No read replica at {$this->SlaveServer}, reading from the primary", __METHOD__ );
            return false;
        }
        return $transport;
    }

    /**
     * A statement on the replica; on a failure the replica is not used again in this request (the statement then
     * runs on the primary).
     *
     * @param expSQLServerTransport $replica
     * @param array $prepared
     * @return array|false
     */
    protected function replicaRun( expSQLServerTransport $replica, array $prepared )
    {
        $this->StatementCount++;
        $result = $replica->run( $prepared['sql'], $prepared['params'] );
        if ( $result === false )
        {
            $this->ReplicaState = 'off';
            $error = $replica->error();
            eZDebug::writeWarning( 'A statement failed on the read replica and runs on the primary: ' . $this->mask( $error ? $error['message'] : '' ), __METHOD__ );
            if ( $error && expSQLServerErrorMap::isConnectionLost( expSQLServerErrorMap::category( $error['number'], $error['state'] ) ) )
            {
                $replica->close();
                $this->ReplicaTransport = null;
                $this->ReplicaConnection = false;
            }
        }
        return $result;
    }

    // ------------------------------------------------------------------ SQL expressions

    function escapeString( $str )
    {
        return expSQLServerDialect::escapeString( $str );
    }

    function subString( $string, $from, $len = null )
    {
        return expSQLServerDialect::subString( $string, $from, $len );
    }

    function concatString( $strings = array() )
    {
        return expSQLServerDialect::concatString( (array)$strings );
    }

    function md5( $str )
    {
        return expSQLServerDialect::md5( $str, $this->ServerInfo ? $this->ServerInfo['major'] : 15 );
    }

    function bitAnd( $arg1, $arg2 )
    {
        return expSQLServerDialect::bitAnd( $arg1, $arg2 );
    }

    function bitOr( $arg1, $arg2 )
    {
        return expSQLServerDialect::bitOr( $arg1, $arg2 );
    }

    function generateSQLINStatement( $elements, $columnName = '', $not = false, $unique = true, $type = false )
    {
        if ( !is_array( $elements ) )
            $elements = array( $elements );
        if ( $unique )
            $elements = array_values( array_unique( $elements ) );
        return expSQLServerDialect::inList( $elements, $columnName, $not === true, $type, (int)$this->Settings['InListChunkSize'] );
    }

    public function sqlHelperDialect()
    {
        return 'sqlserver';
    }

    /**
     * A PHP value as a literal: text as N'...'.
     *
     * @param mixed $value
     * @return string
     */
    public function sqlLiteral( $value )
    {
        return expSQLServerDialect::literal( $value );
    }

    /**
     * A SELECT with LIMIT and OFFSET in this database's words (the kernel's portable form of "LIMIT n OFFSET m");
     * null when $limit is 0.
     *
     * @param string $sql
     * @param int|false $limit
     * @param int $offset
     * @return string|null
     */
    public function sqlLimit( $sql, $limit, $offset = 0 )
    {
        return expSQLServerDialect::applyLimit( $sql, $limit, $offset );
    }

    /**
     * An insert-or-update of one row (MERGE ... WITH (HOLDLOCK)).
     *
     * @param string $table
     * @param array $row
     * @param string[] $keyColumns
     * @param string[]|null $updateColumns
     * @return string
     */
    public function sqlUpsert( $table, array $row, array $keyColumns, $updateColumns = null )
    {
        return expSQLServerDialect::upsert( $table, $row, $keyColumns, $updateColumns );
    }

    /**
     * GROUP_CONCAT (STRING_AGG).
     *
     * @param string $expression
     * @param string $separator
     * @param string|null $orderBy
     * @return string
     */
    public function sqlGroupConcat( $expression, $separator = ',', $orderBy = null )
    {
        return expSQLServerDialect::groupConcat( $expression, $separator, $orderBy );
    }

    /**
     * Seconds since 1970 (UTC) of now, or of a date expression.
     *
     * @param string|null $expression
     * @return string
     */
    public function sqlUnixTimestamp( $expression = null )
    {
        return expSQLServerDialect::unixTimestamp( $expression );
    }

    /**
     * A date (UTC) from seconds since 1970.
     *
     * @param string $expression
     * @return string
     */
    public function sqlFromUnixTimestamp( $expression )
    {
        return expSQLServerDialect::fromUnixTimestamp( $expression );
    }

    /**
     * A name in this database's quotes.
     *
     * @param string $name
     * @return string
     */
    public function sqlQuoteIdentifier( $name )
    {
        return expSQLServerDialect::quoteName( $name );
    }

    // ------------------------------------------------------------------ relations

    function supportedRelationTypeMask()
    {
        return eZDBInterface::RELATION_TABLE_BIT | eZDBInterface::RELATION_SEQUENCE_BIT | eZDBInterface::RELATION_TRIGGER_BIT
             | eZDBInterface::RELATION_VIEW_BIT | eZDBInterface::RELATION_INDEX_BIT;
    }

    function supportedRelationTypes()
    {
        return array( eZDBInterface::RELATION_TABLE, eZDBInterface::RELATION_SEQUENCE, eZDBInterface::RELATION_TRIGGER,
                      eZDBInterface::RELATION_VIEW, eZDBInterface::RELATION_INDEX );
    }

    /**
     * The catalog query that lists the names of one relation type (user objects of the current database only).
     *
     * @param int $relationType
     * @return string|null
     */
    public static function relationListQuery( $relationType )
    {
        switch ( $relationType )
        {
            case eZDBInterface::RELATION_TABLE:
                return 'SELECT name FROM sys.tables WHERE is_ms_shipped = 0 ORDER BY name';
            case eZDBInterface::RELATION_SEQUENCE:
                return 'SELECT name FROM sys.sequences ORDER BY name';
            case eZDBInterface::RELATION_TRIGGER:
                return 'SELECT name FROM sys.triggers WHERE parent_class = 1 AND is_ms_shipped = 0 ORDER BY name';
            case eZDBInterface::RELATION_VIEW:
                return 'SELECT name FROM sys.views WHERE is_ms_shipped = 0 ORDER BY name';
            case eZDBInterface::RELATION_INDEX:
                return 'SELECT i.name FROM sys.indexes i JOIN sys.tables t ON t.object_id = i.object_id'
                     . ' WHERE i.name IS NOT NULL AND i.is_primary_key = 0 AND i.is_unique_constraint = 0 AND t.is_ms_shipped = 0 ORDER BY i.name';
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
        $rows = $this->arrayQuery( $sql, array( 'column' => 'name' ), eZDBInterface::SERVER_MASTER );
        return is_array( $rows ) ? array_values( $rows ) : false;
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
        foreach ( (array)$this->relationList( eZDBInterface::RELATION_TABLE ) as $name )
            $tables[$name] = eZDBInterface::RELATION_TABLE;
        foreach ( (array)$this->relationList( eZDBInterface::RELATION_SEQUENCE ) as $name )
            $tables[$name] = eZDBInterface::RELATION_SEQUENCE;
        return $tables;
    }

    function removeRelation( $relationName, $relationType )
    {
        $words = array( eZDBInterface::RELATION_TABLE => 'TABLE', eZDBInterface::RELATION_SEQUENCE => 'SEQUENCE',
                        eZDBInterface::RELATION_TRIGGER => 'TRIGGER', eZDBInterface::RELATION_VIEW => 'VIEW' );
        if ( $relationType === eZDBInterface::RELATION_INDEX )
        {
            $rows = $this->arrayQuery( 'SELECT OBJECT_NAME(object_id) AS table_name FROM sys.indexes WHERE name = '
                                     . expSQLServerDialect::stringLiteral( $relationName ), array( 'column' => 'table_name' ) );
            if ( !$rows )
                return false;
            return $this->query( 'DROP INDEX ' . expSQLServerDialect::quoteIdentifier( $relationName ) . ' ON '
                               . expSQLServerDialect::quoteIdentifier( reset( $rows ) ) );
        }
        if ( !isset( $words[$relationType] ) )
        {
            eZDebug::writeError( "Unknown relation type '$relationType'", __METHOD__ );
            return false;
        }
        return $this->query( 'DROP ' . $words[$relationType] . ' ' . expSQLServerDialect::quoteName( $relationName ) );
    }

    /**
     * The columns of a table as the server describes them (for the schema read-back): name, type, max_length
     * (bytes; -1 for MAX), precision, scale, nullable, identity, default (its definition), collation.
     *
     * @param string $table
     * @return array[]|false
     */
    public function tableColumns( $table )
    {
        return $this->arrayQuery( 'SELECT c.name, t.name AS type, c.max_length, c.precision, c.scale, c.is_nullable AS nullable,'
                                . ' c.is_identity AS [identity], dc.definition AS [default], c.collation_name AS collation'
                                . ' FROM sys.columns c JOIN sys.types t ON t.user_type_id = c.user_type_id'
                                . ' LEFT JOIN sys.default_constraints dc ON dc.object_id = c.default_object_id'
                                . ' WHERE c.object_id = OBJECT_ID(' . expSQLServerDialect::stringLiteral( $table ) . ')'
                                . ' ORDER BY c.column_id', array(), eZDBInterface::SERVER_MASTER );
    }

    // ------------------------------------------------------------------ server

    function databaseServerVersion()
    {
        if ( !$this->ServerInfo )
            return false;
        return array( 'string' => $this->ServerInfo['string'], 'values' => $this->ServerInfo['values'], 'name' => $this->ServerInfo['name'] );
    }

    function databaseClientVersion()
    {
        if ( !$this->Transport )
            return false;
        $string = $this->Transport->clientVersion();
        preg_match( '/(\d+(?:\.\d+)+)/', $string, $m );
        return array( 'string' => $string, 'values' => isset( $m[1] ) ? array_map( 'intval', explode( '.', $m[1] ) ) : array() );
    }

    function version()
    {
        return $this->ServerInfo ? $this->ServerInfo['string'] : false;
    }

    function isCharsetSupported( $charset )
    {
        return in_array( strtolower( (string)$charset ), array( 'utf-8', 'utf8', 'utf8mb4' ), true );
    }

    function availableDatabases()
    {
        $rows = $this->arrayQuery( 'SELECT name FROM sys.databases WHERE database_id > 4 AND HAS_DBACCESS(name) = 1 AND state = 0 ORDER BY name',
                                   array( 'column' => 'name' ) );
        return is_array( $rows ) ? array_values( $rows ) : array();
    }

    function createDatabase( $dbName )
    {
        $collation = trim( (string)$this->Settings['DatabaseCollation'] );
        $sql = 'CREATE DATABASE ' . expSQLServerDialect::quoteIdentifier( $dbName );
        if ( $collation !== '' && preg_match( '/^[A-Za-z0-9_]+$/', $collation ) )
            $sql .= ' COLLATE ' . $collation;
        return $this->query( $sql );
    }

    function removeDatabase( $dbName )
    {
        return $this->query( 'DROP DATABASE ' . expSQLServerDialect::quoteIdentifier( $dbName ) );
    }

    function supportsDefaultValuesInsertion()
    {
        return true;
    }

    function close()
    {
        if ( $this->Transport )
            $this->Transport->close();
        if ( $this->ReplicaTransport )
            $this->ReplicaTransport->close();
        $this->ReplicaTransport = null;
        $this->ReplicaConnection = false;
        $this->ReplicaState = null;
        $this->ReplicaPinned = false;
        $this->TempTables = array();
        $this->IsConnected = false;
    }
}
