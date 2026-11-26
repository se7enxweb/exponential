<?php
/**
 * File containing the expPostgreSQLWireDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The PostgreSQL driver for databases that speak the PostgreSQL wire protocol without being PostgreSQL, or that are
 * PostgreSQL behind a service with rules of its own: CockroachDB, YugabyteDB, Google Cloud Spanner (PostgreSQL
 * dialect, through PGAdapter), Neon, Supabase and Amazon Aurora PostgreSQL. One subclass per database names its
 * profile (expPostgreSQLWireProfile); site.ini:
 *
 * <code>
 * [DatabaseSettings]
 * DatabaseImplementation=cockroachdb
 * Server=crdb1.example.com
 * Port=26257
 *
 * [PostgreSQLWireSettings]
 * SSLMode=verify-full
 * </code>
 *
 * databaseName() stays "postgresql": the kernel, the schema handler and the SQL helpers treat the database as
 * PostgreSQL, and this class changes what has to differ:
 *
 *  - Connecting: TLS (sslmode) and connect_timeout from the profile; attempts with a growing wait, for a Neon compute
 *    that wakes up or an Aurora writer in failover; statements sent after every connect (CockroachDB: READ COMMITTED
 *    and temporary tables); none of them behind a pooler in transaction mode, where they would reach one server
 *    connection of many.
 *  - Retries: a single statement outside a transaction that fails with a serialization failure (40001, CockroachDB's
 *    and YugabyteDB's restart error) or a deadlock (40P01) is sent again; one whose connection was lost (an Aurora
 *    failover, a Neon compute suspended) is sent again on a new connection. Inside a transaction the transaction is
 *    lost, and only the caller can run it again: retryTransaction().
 *  - Locks: where LOCK TABLE is missing (CockroachDB, YugabyteDB), lock() locks a row per table in the table
 *    exp_wire_lock with SELECT ... FOR UPDATE, so lockers of one table wait for each other, as LOCK TABLE makes them.
 *    Advisory locks are never used: CockroachDB's pg_try_advisory_lock() answers true without locking.
 *  - New ids: behind a pooler in transaction mode, or where currval() does not exist (Spanner), an INSERT into a table
 *    with a serial column gets RETURNING <column>, and lastSerialID() answers from it.
 *  - md5() is PostgreSQL's built-in md5(), not pgcrypto's digest(), which YugabyteDB and Neon do not have by default.
 *  - Replicas: Aurora's readers are read when they are in recovery (they have no WAL receiver to ask).
 *
 * @since 6.0.15
 */
class expPostgreSQLWireDB extends eZPostgreSQLDB
{
    /** The profile of this class (expPostgreSQLWireProfile::all()); a subclass sets its own */
    const PROFILE = 'postgresql';

    /** @var array the applied profile (expPostgreSQLWireProfile::apply()) */
    public $Profile = array();

    /** @var string the pooler of this connection (expPostgreSQLWireProfile::POOLER_*) */
    public $Pooler = expPostgreSQLWireProfile::POOLER_NONE;

    /** @var int statements sent again (retries and reconnects) since the connection was made */
    public $Retries = 0;

    /** @var int connects that needed more than one attempt */
    public $ColdStarts = 0;

    /** @var array|null lower-case table => serial column, read once per connection (RETURNING) */
    protected $SerialColumns = null;

    /** @var array lower-case table => the id its last INSERT made (RETURNING) */
    protected $LastInsertIds = array();

    /** @var bool|null whether the lock table exists (LOCK_ROW); null: not asked yet */
    protected $LockTableReady = null;

    /** @var bool a warning about a lock that cannot be taken was written in this request */
    protected $LockWarned = false;

    /** @var string|null the server's version() */
    protected $VersionText = null;

    /**
     * Creates the driver and connects.
     *
     * @param array $parameters as eZDB::instance() gives them; 'postgresql_wire' => array of [PostgreSQLWireSettings]
     *                          values replaces the settings of site.ini, 'profile' another profile's name
     * @throws eZDBNoConnectionException
     */
    public function __construct( $parameters )
    {
        // eZPostgreSQLDB's constructor connects at once, in its own way: only the base class's is wanted here
        eZDBInterface::__construct( $parameters );

        $name = isset( $parameters['profile'] ) && is_string( $parameters['profile'] ) ? $parameters['profile'] : static::PROFILE;
        $profile = expPostgreSQLWireProfile::get( $name );
        if ( $profile === null )
            $profile = expPostgreSQLWireProfile::get( static::PROFILE );
        $this->Profile = expPostgreSQLWireProfile::apply( $profile, self::settingsFrom( $parameters ) );
        $this->Pooler = expPostgreSQLWireProfile::pooler( $this->Profile, $this->Server, $this->Port );

        if ( !extension_loaded( 'pgsql' ) )
        {
            if ( function_exists( 'eZAppendWarningItem' ) )
            {
                eZAppendWarningItem( array( 'error' => array( 'type' => 'ezdb',
                                                              'number' => eZDBInterface::ERROR_MISSING_EXTENSION ),
                                            'text' => 'PostgreSQL extension was not found, the DB handler will not be initialized.' ) );
            }
            $this->IsConnected = false;
            eZDebug::writeWarning( 'The pgsql extension of PHP was not found: ' . $this->Profile['label'] . ' needs it', __CLASS__ );
            return;
        }

        eZDebug::createAccumulatorGroup( 'postgresql_total', 'Postgresql Total' );
        $this->DBConnection = $this->connect();
        if ( !$this->DBConnection )
        {
            $this->IsConnected = false;
            throw new eZDBNoConnectionException( $this->Server, $this->ErrorMessage, $this->ErrorNumber );
        }
        $this->IsConnected = true;
        $this->setErrorVerbosity();
        $this->startSession();
    }

    /**
     * The [PostgreSQLWireSettings] of site.ini, replaced by the 'postgresql_wire' parameters where given.
     *
     * @param array $parameters
     * @return array
     */
    protected static function settingsFrom( array $parameters )
    {
        $settings = array();
        try
        {
            $ini = eZINI::instance();
            if ( $ini->hasGroup( 'PostgreSQLWireSettings' ) )
                $settings = (array)$ini->group( 'PostgreSQLWireSettings' );
        }
        catch ( Throwable $e )
        {
        }
        if ( isset( $parameters['postgresql_wire'] ) && is_array( $parameters['postgresql_wire'] ) )
            $settings = array_merge( $settings, $parameters['postgresql_wire'] );
        return $settings;
    }

    /**
     * Opens the connection: ConnectAttempts attempts (at least the site's ConnectRetries + 1), waiting ConnectWait ms,
     * twice as long after each failure. ErrorMessage holds libpq's reason (without the password) when all failed.
     *
     * @return resource|object|false
     */
    protected function connect()
    {
        $connectString = expPostgreSQLWireProfile::connectString( $this->Profile, $this->Server, $this->Port, $this->DB, $this->User, $this->Password );
        $attempts = max( (int)$this->Profile['settings']['ConnectAttempts'], (int)$this->connectRetryCount() + 1 );
        $wait = (int)$this->Profile['settings']['ConnectWait'];
        $persistent = $this->UsePersistentConnection && function_exists( 'pg_pconnect' );
        $connection = false;
        for ( $n = 1; $n <= $attempts; $n++ )
        {
            $oldHandling = eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
            eZDebug::accumulatorStart( 'postgresql_connection', 'postgresql_total', 'Database connection' );
            $reason = '';
            try
            {
                $connection = $persistent ? pg_pconnect( $connectString ) : pg_connect( $connectString, PGSQL_CONNECT_FORCE_NEW );
            }
            catch ( Throwable $e )
            {
                $connection = false;
                $reason = $e->getMessage();
            }
            eZDebug::accumulatorStop( 'postgresql_connection' );
            eZDebug::setHandleType( $oldHandling );
            if ( $connection )
            {
                if ( $n > 1 )
                    $this->ColdStarts++;
                return $connection;
            }
            $this->ErrorNumber = 1;
            $this->ErrorMessage = (string)$this->Password !== '' ? str_replace( (string)$this->Password, '***', $reason ) : $reason;
            if ( $n < $attempts && $wait > 0 )
                usleep( min( 30000, $wait * ( 1 << ( $n - 1 ) ) ) * 1000 );
        }
        return false;
    }

    /**
     * Sends the profile's session statements (expPostgreSQLWireProfile::sessionStatements()). A statement the server
     * refuses (an older CockroachDB without READ COMMITTED) is logged and left out; the connection stays usable.
     */
    protected function startSession()
    {
        foreach ( expPostgreSQLWireProfile::sessionStatements( $this->Profile, $this->Pooler ) as $statement )
        {
            $oldHandling = eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
            try
            {
                $result = @pg_query( $this->DBConnection, $statement );
            }
            catch ( Throwable $e )
            {
                $result = false;
            }
            eZDebug::setHandleType( $oldHandling );
            if ( !$result )
                eZDebug::writeWarning( $this->Profile['label'] . ' refused a session setting, the connection goes on without it: '
                                     . $statement . ': ' . trim( (string)@pg_last_error( $this->DBConnection ) ), __METHOD__ );
        }
        $this->SerialColumns = null;
        $this->LastInsertIds = array();
        $this->LockTableReady = null;
    }

    /**
     * The profile of this connection, with its effective settings.
     *
     * @return array
     */
    public function wireProfile()
    {
        return $this->Profile;
    }

    /**
     * The name of the database this driver talks to (cockroachdb, yugabytedb, spanner, neon, supabase, aurora).
     * databaseName() stays "postgresql", which the kernel branches on.
     *
     * @return string
     */
    public function databaseVariant()
    {
        return $this->Profile['name'];
    }

    // ------------------------------------------------------------------ statements

    function query( $sql, $server = false )
    {
        $table = '';
        if ( $this->isConnected() && $this->needsReturning() )
        {
            $table = expPostgreSQLWireProfile::insertTable( $sql );
            $column = $table !== '' ? $this->serialColumn( $table ) : '';
            if ( $column !== '' && !preg_match( '/\bRETURNING\b/i', $sql ) )
                $sql = rtrim( $sql, " \t\r\n;" ) . ' RETURNING ' . $column;
            else
                $table = '';
        }
        $result = parent::query( $sql, $server );
        if ( $table !== '' && $result && !is_bool( $result ) )
        {
            $rows = pg_num_rows( $result );
            if ( $rows > 0 )
            {
                $row = pg_fetch_row( $result, $rows - 1 );
                if ( $row && $row[0] !== null )
                    $this->LastInsertIds[$table] = (int)$row[0];
            }
        }
        return $result;
    }

    /**
     * Whether an INSERT must name its new id itself (RETURNING): behind a pooler in transaction mode the next
     * statement, currval(), may run on another server connection; Spanner has no currval().
     *
     * @return bool
     */
    protected function needsReturning()
    {
        return $this->Pooler === expPostgreSQLWireProfile::POOLER_TRANSACTION || !$this->Profile['currval'];
    }

    /**
     * The serial column of a table ('' for none): a column whose default is nextval(), read once per connection.
     *
     * @param string $table lower case
     * @return string
     */
    protected function serialColumn( $table )
    {
        if ( $this->SerialColumns === null )
        {
            $this->SerialColumns = array();
            $result = @pg_query( $this->DBConnection, "SELECT table_name, column_name FROM information_schema.columns"
                                                    . " WHERE column_default LIKE 'nextval(%'"
                                                    . " AND table_schema NOT IN ( 'pg_catalog', 'information_schema' )" );
            if ( $result )
            {
                while ( $row = pg_fetch_row( $result ) )
                    if ( !isset( $this->SerialColumns[strtolower( $row[0] )] ) )
                        $this->SerialColumns[strtolower( $row[0] )] = (string)$row[1];
                pg_free_result( $result );
            }
        }
        return isset( $this->SerialColumns[$table] ) ? $this->SerialColumns[$table] : '';
    }

    /**
     * Sends a statement that failed again, outside a transaction: after a serialization failure or a deadlock up
     * to SerializationRetries times (waiting RetryWait ms, longer each time), after a lost connection once on a new
     * connection (ReconnectOnLoss). Inside a transaction nothing is repeated: the transaction is lost as a whole.
     *
     * @param string $sql
     * @param int|bool $server
     * @return resource|object|false
     */
    protected function retryFailedQuery( $sql, $server )
    {
        if ( (int)$this->TransactionCounter > 0 )
            return false;
        $settings = $this->Profile['settings'];
        $state = (string)$this->ErrorNumber;
        $lost = expPostgreSQLWireProfile::isConnectionLost( $state, (string)$this->ErrorMessage ) || !$this->connectionAlive();
        if ( $lost )
        {
            if ( !$settings['ReconnectOnLoss'] )
                return false;
            eZDebug::writeWarning( $this->Profile['label'] . ': the connection was lost (' . $state . '), connecting again', __METHOD__ );
            if ( $this->DBConnection )
                @pg_close( $this->DBConnection );
            $this->DBConnection = $this->connect();
            if ( !$this->DBConnection )
            {
                $this->IsConnected = false;
                return false;
            }
            $this->IsConnected = true;
            $this->setErrorVerbosity();
            $this->startSession();
            $this->Retries++;
            return $this->sendAgain( $sql );
        }
        if ( !expPostgreSQLWireProfile::isRetryable( $this->Profile, $state ) )
            return false;
        for ( $n = 1; $n <= (int)$settings['SerializationRetries']; $n++ )
        {
            usleep( (int)$settings['RetryWait'] * 1000 * $n );
            $this->Retries++;
            $result = $this->sendAgain( $sql );
            if ( $result )
                return $result;
            if ( !expPostgreSQLWireProfile::isRetryable( $this->Profile, (string)$this->ErrorNumber ) )
                return false;
        }
        return false;
    }

    /**
     * Whether the connection still answers (libpq's view of it; no statement is sent).
     *
     * @return bool
     */
    protected function connectionAlive()
    {
        return $this->DBConnection && @pg_connection_status( $this->DBConnection ) === PGSQL_CONNECTION_OK;
    }

    /**
     * One more attempt at a statement on the primary; setError() on failure.
     *
     * @param string $sql
     * @return resource|object|false
     */
    protected function sendAgain( $sql )
    {
        $oldHandling = eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
        try
        {
            $result = @pg_query( $this->DBConnection, $sql );
        }
        catch ( Throwable $e )
        {
            $result = false;
        }
        eZDebug::setHandleType( $oldHandling );
        if ( !$result )
            $this->setError();
        return $result;
    }

    /**
     * Runs $work in a transaction, and again (up to SerializationRetries times) when the transaction is lost to a
     * serialization failure, a deadlock or a lost connection: on CockroachDB and YugabyteDB any transaction may have
     * to be run again, and only the code that made it can do so. Returns what $work returns; any other error is
     * thrown (eZDBException).
     *
     * @param callable $work function( expPostgreSQLWireDB $db )
     * @return mixed
     * @throws eZDBException
     */
    public function retryTransaction( callable $work )
    {
        $attempts = max( 0, (int)$this->Profile['settings']['SerializationRetries'] ) + 1;
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
                    $state = (string)$this->ErrorNumber;
                    while ( (int)$this->TransactionCounter > 0 )
                        $this->rollback();
                    $retry = expPostgreSQLWireProfile::isRetryable( $this->Profile, $state )
                          || expPostgreSQLWireProfile::isConnectionLost( $state, (string)$this->ErrorMessage );
                    if ( $n >= $attempts || !$retry )
                        throw $e;
                    $this->Retries++;
                    usleep( (int)$this->Profile['settings']['RetryWait'] * 1000 * $n );
                }
            }
        }
        finally
        {
            $this->setErrorHandling( $old );
        }
    }

    /**
     * BEGIN, COMMIT and ROLLBACK without PostgreSQL's optional WORK, which CockroachDB and Spanner do not take.
     */
    function beginQuery()
    {
        return $this->query( 'BEGIN' );
    }

    function commitQuery()
    {
        return $this->query( 'COMMIT' );
    }

    function rollbackQuery()
    {
        if ( !$this->DBConnection )
            return false;
        return @pg_query( $this->DBConnection, 'ROLLBACK' );
    }

    function lastSerialID( $table = false, $column = 'id' )
    {
        $key = strtolower( (string)$table );
        if ( isset( $this->LastInsertIds[$key] ) )
            return $this->LastInsertIds[$key];
        if ( !$this->Profile['currval'] )
            return false;
        return parent::lastSerialID( $table, $column );
    }

    // ------------------------------------------------------------------ locks

    /**
     * MySQL's LOCK TABLES as this database allows it: LOCK TABLE where it exists; else a transaction that holds a row
     * per table of exp_wire_lock FOR UPDATE, so a second lock() of the same table waits until unlock(); else (Spanner)
     * a transaction alone, with a warning.
     *
     * @param string|array $table a name, or array( array( 'table' => name ), ... )
     */
    function lock( $table )
    {
        $method = expPostgreSQLWireProfile::lockMethod( $this->Profile );
        if ( $method === expPostgreSQLWireProfile::LOCK_TABLE )
        {
            parent::lock( $table );
            return;
        }
        $tables = array();
        foreach ( is_array( $table ) ? $table : array( $table ) as $entry )
            $tables[] = is_array( $entry ) ? $entry['table'] : $entry;
        $ready = $method === expPostgreSQLWireProfile::LOCK_ROW && $this->ensureLockTable();
        $literal = function ( $value ) { return "'" . $this->escapeString( $value ) . "'"; };
        sort( $tables );
        $statements = array();
        foreach ( $tables as $name )
            $statements[] = expPostgreSQLWireProfile::rowLockStatements( $name, $literal );
        // The rows are made before the transaction where it can be: a row another transaction has not committed yet
        // is not there for a second locker's SELECT ... FOR UPDATE, which would then not wait
        if ( $ready && (int)$this->TransactionCounter === 0 )
        {
            foreach ( $statements as $i => $pair )
            {
                $this->query( $pair[0] );
                $statements[$i] = array( $pair[1] );
            }
        }
        $this->begin();
        if ( !$this->isConnected() )
            return;
        if ( !$ready )
        {
            if ( !$this->LockWarned )
                eZDebug::writeWarning( $this->Profile['label'] . ' cannot lock the tables ' . implode( ', ', $tables )
                                     . ': the work runs in a transaction without the lock', __METHOD__ );
            $this->LockWarned = true;
            return;
        }
        foreach ( $statements as $pair )
            foreach ( $pair as $statement )
                $this->query( $statement );
    }

    /**
     * Whether the table of the row locks exists; made when it does not and no transaction is open (DDL inside a
     * transaction commits it on CockroachDB, and is refused on Spanner).
     *
     * @return bool
     */
    public function ensureLockTable()
    {
        if ( $this->LockTableReady !== null )
            return $this->LockTableReady;
        if ( !$this->isConnected() )
            return false;
        $result = @pg_query( $this->DBConnection, "SELECT 1 FROM information_schema.tables WHERE table_name = '"
                                                . expPostgreSQLWireProfile::LOCK_TABLE_NAME . "'" );
        $exists = $result && pg_num_rows( $result ) > 0;
        if ( !$exists && (int)$this->TransactionCounter === 0 )
        {
            $oldHandling = eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
            try
            {
                $exists = (bool)@pg_query( $this->DBConnection, expPostgreSQLWireProfile::lockTableDDL() );
            }
            catch ( Throwable $e )
            {
                $exists = false;
            }
            eZDebug::setHandleType( $oldHandling );
        }
        // asked again in the next transaction-free lock() when it could not be made inside a transaction
        if ( $exists || (int)$this->TransactionCounter === 0 )
            $this->LockTableReady = $exists;
        return $exists;
    }

    // ------------------------------------------------------------------ SQL of this database

    /**
     * PostgreSQL's built-in md5() (every version since 7.4): pgcrypto's digest(), which eZPostgreSQLDB used, is not
     * there by default on YugabyteDB, Neon or Aurora. Spanner has neither: the expression is sent and refused.
     *
     * @param string $str
     * @return string
     */
    function md5( $str )
    {
        return " md5( $str ) ";
    }

    public function sqlLimit( $sql, $limit, $offset = 0 )
    {
        $sql = parent::sqlLimit( $sql, $limit, $offset );
        // Spanner has no LIMIT ALL: an offset without a limit takes the largest number
        if ( $sql !== null && !$this->Profile['limit_all'] )
            $sql = preg_replace( '/\nLIMIT ALL( OFFSET \d+)$/', "\nLIMIT 9223372036854775807\$1", $sql );
        return $sql;
    }

    public function sqlForUpdate( $sql )
    {
        // Spanner reads in a read-write transaction lock what they read; FOR UPDATE is refused outside one
        if ( !$this->Profile['for_update'] )
            return rtrim( (string)$sql, " \t\r\n;" );
        return parent::sqlForUpdate( $sql );
    }

    function createTempTable( $createTableQuery = '', $server = self::SERVER_SLAVE )
    {
        if ( $this->Profile['temp_tables'] === 'none' )
        {
            $this->ErrorNumber = '0A000';
            $this->ErrorMessage = $this->Profile['label'] . ' has no temporary tables';
            eZDebug::writeError( $this->ErrorMessage . ': ' . $createTableQuery, __METHOD__ );
            if ( $this->errorHandling == eZDB::ERROR_HANDLING_EXCEPTIONS )
                throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
            return false;
        }
        // A temporary table belongs to its session: behind a transaction pooler, or on a replica, it is gone with it
        if ( $this->UseSlaveServer )
            $this->ReplicaPinned = true;
        return $this->query( $createTableQuery, $server );
    }

    function databaseServerVersion()
    {
        if ( !$this->isConnected() )
            return false;
        if ( $this->VersionText === null )
        {
            $result = @pg_query( $this->DBConnection, 'SELECT version()' );
            $row = $result ? pg_fetch_row( $result, 0 ) : false;
            $this->VersionText = $row ? (string)$row[0] : '';
        }
        return expPostgreSQLWireProfile::parseVersion( $this->Profile, $this->VersionText );
    }

    function correctSequenceValues()
    {
        // a bit-reversed sequence hands out numbers in no order: there is nothing to move past the rows
        if ( $this->Profile['sequences'] !== 'sql' )
            return true;
        return parent::correctSequenceValues();
    }

    function removeDatabase( $dbName )
    {
        if ( $this->DBConnection != false && $this->Profile['name'] === 'cockroachdb' )
        {
            // CockroachDB drops a database with tables in it only with CASCADE
            $this->query( "DROP DATABASE IF EXISTS $dbName CASCADE" );
            $this->setError();
            return;
        }
        parent::removeDatabase( $dbName );
    }

    protected function replicaStatusQuery()
    {
        if ( $this->Profile['replica_check'] === 'in_recovery' )
            return "SELECT CASE WHEN pg_is_in_recovery() THEN 'streaming' ELSE 'primary' END";
        return parent::replicaStatusQuery();
    }

    protected function connectReplica()
    {
        $connectString = expPostgreSQLWireProfile::connectString( $this->Profile, $this->SlaveServer, $this->SlavePort, $this->SlaveDB,
                                                                 $this->SlaveUser, $this->SlavePassword );
        $connectString = preg_replace( "/ connect_timeout='\d+'/", '', $connectString ) . " connect_timeout='2'";
        $oldHandling = eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
        try
        {
            $connection = @pg_connect( $connectString, PGSQL_CONNECT_FORCE_NEW );
        }
        catch ( Throwable $e )
        {
            $connection = false;
        }
        eZDebug::setHandleType( $oldHandling );
        return $connection ?: false;
    }

    function close()
    {
        parent::close();
        $this->SerialColumns = null;
        $this->LastInsertIds = array();
        $this->LockTableReady = null;
    }
}
