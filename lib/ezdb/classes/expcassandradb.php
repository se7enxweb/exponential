<?php
/**
 * File containing the expCassandraDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The driver of Apache Cassandra (4.0 to 5.0) and ScyllaDB (expScyllaDB), wide-column databases that speak CQL. It is
 * a data table connection and an import source, not a site database (doc/eng-us/features/6.0/cassandra-scylladb-driver.md
 * says why): datatable.ini, engine cassandra or scylladb, or site.ini of a tool that wants it:
 *
 * <code>
 * [DatabaseSettings]
 * DatabaseImplementation=cassandra
 * Server=cass1.example.com,cass2.example.com
 * Port=9042
 * Database=archive              (the keyspace)
 * </code>
 *
 * and [CassandraSettings] of site.ini. The kernel's persistent classes and the data table layer write SQL; the driver
 * answers it on CQL (expCassandraDialect parses it):
 *
 *  - SELECT: a WHERE that names the whole partition key (= or IN) becomes a key lookup, one with = on an indexed
 *    column an index lookup, comparisons of other columns a scan with ALLOW FILTERING ([CassandraSettings]
 *    AllowFiltering); what CQL cannot say (OR, LIKE, IN on other columns, functions, ORDER BY, OFFSET, DISTINCT,
 *    aggregates over a filter) is done in PHP on the rows the server sent, page by page, refusing a statement that
 *    would read more than ScanLimit rows. Joins, sub-selects, GROUP BY and UNION are refused
 *    (expDBUnsupportedException), never answered wrongly.
 *  - INSERT: a row whose key exists already is refused as a duplicate, as SQL does ([CassandraSettings] InsertCheck:
 *    read, a lookup before the write; lwt, IF NOT EXISTS, exact under concurrency; none, Cassandra's upsert).
 *    REPLACE and INSERT ... ON DUPLICATE KEY UPDATE (sqlUpsert()) write over it.
 *  - UPDATE and DELETE: the rows the WHERE finds are read and written by their primary key (a changed key: the old
 *    row deleted, the new one written).
 *  - New ids: an auto_increment column (the schema handler records it) takes the next number of a counter row in
 *    exp_cassandra_sequence, moved on with a lightweight transaction (compare and set), so two writers never get the
 *    same id; lastSerialID() gives this connection's last one per table.
 *  - Transactions: Cassandra has none across rows. Writes go to the server at once; rollback() undoes this
 *    connection's writes of the transaction with compensating writes (an inserted row deleted, an updated or deleted
 *    row written back as it was). Other connections may see the writes in between, and a process that dies in a
 *    transaction leaves them. lock() does nothing.
 *  - Anything that is not SELECT, INSERT, UPDATE, DELETE is CQL already (CREATE TABLE, CREATE INDEX, DROP, TRUNCATE ...)
 *    and goes to the server as it is.
 *
 * Not here: temporary tables, GROUP BY, joins, sub-selects (the kernel's search, tree and permission code needs
 * them: no site runs on Cassandra), and read-your-writes across data centres below the chosen consistency.
 */
class expCassandraDB extends eZDBInterface
{
    /** databaseName() of this driver */
    const NAME = 'cassandra';

    /** The table of the counters of auto_increment columns */
    const SEQUENCE_TABLE = 'exp_cassandra_sequence';

    /** The table of the .dba definitions the schema handler made tables from */
    const SCHEMA_TABLE = 'exp_cassandra_schema';

    /** @var array the defaults of [CassandraSettings] */
    const DEFAULT_SETTINGS = array(
        'Consistency' => 'LOCAL_QUORUM',
        'SerialConsistency' => 'LOCAL_SERIAL',
        'ReplicationStrategy' => 'SimpleStrategy',
        'ReplicationFactor' => 1,
        'DataCenters' => '',
        'Tablets' => 'disabled',
        'AutoCreateKeyspace' => 'disabled',
        'TLS' => 'disabled',
        'TLSCAFile' => '',
        'TLSVerifyPeer' => 'enabled',
        'ConnectTimeout' => 5,
        'RequestTimeout' => 30,
        'Compression' => 'disabled',
        'ProtocolVersion' => 4,
        'PageSize' => 5000,
        'ScanLimit' => 100000,
        'AllowFiltering' => 'enabled',
        'InsertCheck' => 'read',
        'InListChunkSize' => 100,
        'JournalLimit' => 10000,
        'ReconnectOnLoss' => 'enabled',
    );

    /** @var expCassandraTransport|null */
    public $Transport = null;

    /** @var array the effective [CassandraSettings] */
    public $Settings = array();

    /** @var array|null product (cassandra, scylladb), version */
    public $ServerInfo = null;

    /** @var array table => description (columns, partition, clustering, indexes, definition) */
    public $Tables = array();

    /** @var array table => the id the last insert of this connection gave it */
    public $LastIDs = array();

    /** @var array sequence name => the value this connection last saw */
    public $Sequences = array();

    /** @var array sequence name => the highest id given explicitly, not yet carried into the counter */
    public $PendingBumps = array();

    /** @var array[] the compensations of the open transaction: array( action, table, row ) */
    public $Journal = array();

    /** @var bool whether the journal of the open transaction lost entries (JournalLimit) */
    public $JournalOverflow = false;

    /** @var int statements sent to the server (each page counts) */
    public $StatementCount = 0;

    /** @var array how SELECTs were answered: key, index, filter, scan => count; rows_read */
    public $Plans = array( 'key' => 0, 'index' => 0, 'filter' => 0, 'scan' => 0, 'rows_read' => 0 );

    /** @var string the last CQL statement sent */
    public $LastStatement = '';

    /** @var string|null the category of the last error */
    public $ErrorCategory = null;

    /** @var int rows the last write changed */
    public $AffectedRows = 0;

    /**
     * Connects: the [DatabaseSettings] parameters of eZDB::instance(), and optionally 'cassandra' => array(
     * <[CassandraSettings] names> => value ) over site.ini's [CassandraSettings], and 'cassandra_transport' => an
     * expCassandraTransport to use instead of the library.
     *
     * @param array $parameters
     * @throws eZDBNoConnectionException when no connection can be opened
     */
    public function __construct( $parameters )
    {
        parent::__construct( $parameters );
        $this->Settings = self::settings( isset( $parameters['cassandra'] ) && is_array( $parameters['cassandra'] ) ? $parameters['cassandra'] : array() );
        if ( isset( $parameters['cassandra_transport'] ) && $parameters['cassandra_transport'] instanceof expCassandraTransport )
            $this->Transport = $parameters['cassandra_transport'];
        else
        {
            if ( expCassandraTransport::availableLayer() === null )
            {
                $text = expCassandraTransport::missingLayerText() . ', the DB handler will not be initialized.';
                if ( function_exists( 'eZAppendWarningItem' ) )
                    eZAppendWarningItem( array( 'error' => array( 'type' => 'ezdb', 'number' => eZDBInterface::ERROR_MISSING_EXTENSION ), 'text' => $text ) );
                eZDebug::writeWarning( $text, __CLASS__ );
                $this->ErrorMessage = $text;
                $this->IsConnected = false;
                return;
            }
            $this->Transport = new expCassandraPHPTransport();
        }
        eZDebug::createAccumulatorGroup( 'cassandra_total', 'Cassandra Total' );

        $attempts = max( 0, (int)$this->connectRetryCount() );
        $wait = max( 0, (int)$this->connectRetryWaitTime() );
        $connected = $this->openConnection();
        for ( $n = 1; !$connected && $n <= $attempts; $n++ )
        {
            if ( $wait )
                sleep( $wait );
            $connected = $this->openConnection();
        }
        if ( !$connected )
        {
            $this->setError();
            throw new eZDBNoConnectionException( (string)$this->Server, $this->ErrorMessage, $this->ErrorNumber );
        }
    }

    /**
     * The effective [CassandraSettings]: the defaults, site.ini, then $overrides.
     *
     * @param array $overrides
     * @return array
     */
    public static function settings( array $overrides = array() )
    {
        $settings = self::DEFAULT_SETTINGS;
        if ( class_exists( 'eZINI' ) )
        {
            try
            {
                $ini = eZINI::instance();
                if ( $ini->hasGroup( 'CassandraSettings' ) )
                    foreach ( $ini->group( 'CassandraSettings' ) as $name => $value )
                        if ( array_key_exists( $name, $settings ) && $value !== '' && $value !== null )
                            $settings[$name] = $value;
            }
            catch ( Throwable $e )
            {
            }
        }
        foreach ( $overrides as $name => $value )
            $settings[$name] = $value;
        return $settings;
    }

    /**
     * Whether a setting is on (enabled, true, 1, yes).
     *
     * @param mixed $value
     * @return bool
     */
    public static function flag( $value )
    {
        return in_array( strtolower( trim( (string)$value ) ), array( 'enabled', 'true', '1', 'yes', 'on' ), true );
    }

    /**
     * The hosts of Server: a comma-separated list (host, host:port, tls://host).
     *
     * @param string $server
     * @return string[]
     */
    public static function hosts( $server )
    {
        $hosts = array_values( array_filter( array_map( 'trim', explode( ',', (string)$server ) ), 'strlen' ) );
        return $hosts ? $hosts : array( '127.0.0.1' );
    }

    /**
     * The connection settings for the transport.
     *
     * @return array
     */
    public function connectionConfig()
    {
        $ssl = array();
        if ( self::flag( $this->Settings['TLS'] ) )
        {
            $verify = self::flag( $this->Settings['TLSVerifyPeer'] );
            $ssl = array( 'verify_peer' => $verify, 'verify_peer_name' => $verify, 'allow_self_signed' => !$verify );
            if ( (string)$this->Settings['TLSCAFile'] !== '' )
                $ssl['cafile'] = (string)$this->Settings['TLSCAFile'];
        }
        return array( 'hosts' => self::hosts( $this->Server ), 'port' => $this->Port ? (int)$this->Port : 9042, 'user' => (string)$this->User,
                      'password' => (string)$this->Password, 'keyspace' => '', 'tls' => self::flag( $this->Settings['TLS'] ), 'ssl' => $ssl,
                      'connect_timeout' => (float)$this->Settings['ConnectTimeout'], 'request_timeout' => (float)$this->Settings['RequestTimeout'],
                      'consistency' => (string)$this->Settings['Consistency'], 'serial_consistency' => (string)$this->Settings['SerialConsistency'],
                      'compression' => self::flag( $this->Settings['Compression'] ), 'protocol' => (int)$this->Settings['ProtocolVersion'] );
    }

    /**
     * Opens the connection, reads what the server is and changes to the keyspace (Database).
     *
     * @return bool
     */
    protected function openConnection()
    {
        eZDebug::accumulatorStart( 'cassandra_connection', 'cassandra_total', 'Database connection' );
        $ok = $this->Transport->open( $this->connectionConfig() );
        eZDebug::accumulatorStop( 'cassandra_connection' );
        $this->IsConnected = $ok;
        if ( !$ok )
            return false;
        $this->DBConnection = $this->Transport;
        $this->DBWriteConnection = $this->Transport;
        $this->Tables = array();
        if ( $this->ServerInfo === null )
            $this->ServerInfo = $this->readServerInfo();
        if ( (string)$this->DB !== '' )
        {
            if ( !$this->keyspaceExists( $this->DB ) )
            {
                if ( !self::flag( $this->Settings['AutoCreateKeyspace'] ) || !$this->createDatabase( $this->DB ) )
                {
                    $this->Transport->close();
                    $this->IsConnected = false;
                    $this->ErrorMessage = "The keyspace '{$this->DB}' does not exist (CREATE KEYSPACE, or [CassandraSettings] AutoCreateKeyspace=enabled)";
                    $this->ErrorNumber = -1;
                    return false;
                }
            }
            if ( $this->Transport->run( 'USE ' . expCassandraDialect::quoteName( $this->DB ) ) === false )
            {
                $this->IsConnected = false;
                return false;
            }
        }
        return true;
    }

    /**
     * What the server is: product (cassandra, scylladb), version, protocol.
     *
     * @return array
     */
    protected function readServerInfo()
    {
        $info = array( 'product' => 'cassandra', 'version' => '', 'cql_version' => '', 'protocol' => $this->Transport->protocolVersion(), 'name' => 'Apache Cassandra' );
        $local = $this->Transport->run( "SELECT release_version, cql_version FROM system.local WHERE key = 'local'" );
        if ( $local !== false && isset( $local['rows'][0] ) )
        {
            $info['version'] = (string)$local['rows'][0]['release_version'];
            $info['cql_version'] = (string)$local['rows'][0]['cql_version'];
        }
        // ScyllaDB answers 3.0.8 as its Cassandra compatibility level and keeps its own version in system.versions
        $scylla = $this->Transport->run( "SELECT version FROM system.versions WHERE key = 'local'" );
        if ( $scylla !== false && isset( $scylla['rows'][0]['version'] ) )
        {
            $info['product'] = 'scylladb';
            $info['name'] = 'ScyllaDB';
            $info['compatible'] = $info['version'];
            $info['version'] = preg_replace( '/-.*$/', '', (string)$scylla['rows'][0]['version'] );
        }
        $this->clearError();
        return $info;
    }

    /** Clears the last error after a probe that may fail */
    protected function clearError()
    {
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
    }

    /**
     * Opens the connection again.
     *
     * @return bool
     */
    public function reconnect()
    {
        if ( !$this->Transport )
            return false;
        $this->Transport->close();
        return $this->openConnection();
    }

    function databaseName()
    {
        return static::NAME;
    }

    /**
     * The product the server is (cassandra or scylladb), whatever the driver was named.
     *
     * @return string
     */
    public function product()
    {
        return $this->ServerInfo ? $this->ServerInfo['product'] : '';
    }

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

    // ------------------------------------------------------------------ CQL

    /**
     * Runs a CQL statement with bound values and returns its rows (every page): for code that speaks CQL itself (the
     * import, the tests). Errors are handled as in query().
     *
     * @param string $cql
     * @param array $values
     * @param array $options page_size, consistency, serial, max_rows
     * @return array[]|false
     */
    public function cql( $cql, array $values = array(), array $options = array() )
    {
        $rows = array();
        $paging = null;
        $max = isset( $options['max_rows'] ) ? (int)$options['max_rows'] : 0;
        do
        {
            $page = $options + array( 'page_size' => (int)$this->Settings['PageSize'] );
            if ( $paging !== null )
                $page['paging_state'] = $paging;
            $result = $this->send( $cql, $values, $page );
            if ( $result === false )
                return false;
            foreach ( $result['rows'] as $row )
                $rows[] = $row;
            $paging = $result['paging_state'];
            if ( $max > 0 && count( $rows ) >= $max )
                break;
        }
        while ( $paging !== null && $paging !== '' );
        return $rows;
    }

    /**
     * Calls $each( $row ) for every row of a CQL statement, page by page (nothing kept in memory).
     *
     * @param string $cql
     * @param array $values
     * @param callable $each return false to stop
     * @return int|false the rows read
     */
    public function cqlEach( $cql, array $values, callable $each )
    {
        $count = 0;
        $paging = null;
        do
        {
            $page = array( 'page_size' => (int)$this->Settings['PageSize'] );
            if ( $paging !== null )
                $page['paging_state'] = $paging;
            $result = $this->send( $cql, $values, $page );
            if ( $result === false )
                return false;
            foreach ( $result['rows'] as $row )
            {
                $count++;
                if ( $each( $row ) === false )
                    return $count;
            }
            $paging = $result['paging_state'];
        }
        while ( $paging !== null && $paging !== '' );
        return $count;
    }

    /**
     * Sends one statement; sends it again once after a lost connection outside a transaction.
     *
     * @param string $cql
     * @param array $values
     * @param array $options
     * @return array|false
     */
    protected function send( $cql, array $values = array(), array $options = array() )
    {
        if ( !$this->IsConnected || !$this->Transport )
        {
            $this->ErrorMessage = 'Trying to do a query without being connected to a database!';
            $this->ErrorNumber = -1;
            return false;
        }
        eZDebug::accumulatorStart( 'cassandra_query', 'cassandra_total', 'Cassandra queries' );
        $reconnected = false;
        while ( true )
        {
            $this->LastStatement = $cql;
            $this->StatementCount++;
            $result = $this->Transport->run( $cql, $values, $options );
            if ( $result !== false )
                break;
            $error = $this->Transport->error();
            if ( is_array( $error ) && $error['category'] === 'connection' && !$reconnected && (int)$this->TransactionCounter === 0
                 && self::flag( $this->Settings['ReconnectOnLoss'] ) )
            {
                $reconnected = true;
                if ( $this->reconnect() )
                    continue;
            }
            $this->takeError( $error );
            break;
        }
        eZDebug::accumulatorStop( 'cassandra_query' );
        return $result;
    }

    /**
     * Takes the transport's error into ErrorNumber (the protocol's error code, or -1), ErrorMessage and ErrorCategory.
     *
     * @param array|null $error
     */
    protected function takeError( $error )
    {
        if ( !is_array( $error ) )
            $error = array( 'code' => null, 'message' => 'Unknown Cassandra error', 'category' => 'other' );
        $this->ErrorCategory = $error['category'];
        $this->ErrorNumber = $error['code'] !== null ? (int)$error['code'] : -1;
        $this->ErrorMessage = $this->mask( $error['message'] );
    }

    /**
     * Notes an error of the driver itself (not the server's).
     *
     * @param string $message
     * @param string $category
     * @param int $number
     */
    protected function fail( $message, $category = 'invalid', $number = -1 )
    {
        $this->ErrorMessage = $this->mask( $message );
        $this->ErrorNumber = $number;
        $this->ErrorCategory = $category;
    }

    function setError()
    {
        if ( $this->Transport && $this->Transport->error() )
            $this->takeError( $this->Transport->error() );
    }

    /**
     * A text without the password of this connection.
     *
     * @param string $text
     * @return string
     */
    public function mask( $text )
    {
        $text = (string)$text;
        if ( is_string( $this->Password ) && $this->Password !== '' )
            $text = str_replace( $this->Password, '***', $text );
        return $text;
    }

    // ------------------------------------------------------------------ SQL

    function query( $sql, $server = false )
    {
        return $this->execute( $sql, false, 0, 'query' ) !== false;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
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
                $column = is_string( $params['column'] ) ? strtolower( $params['column'] ) : $params['column'];
        }
        $rows = $this->execute( $sql, $limit, $offset, 'arrayQuery' );
        if ( $rows === false )
            return false;
        if ( !is_array( $rows ) )
            return array();
        $retArray = array();
        foreach ( array_values( $rows ) as $i => $row )
        {
            if ( $column === false )
                $retArray[$i + $offset] = $row;
            elseif ( is_int( $column ) || ctype_digit( (string)$column ) )
            {
                $values = array_values( $row );
                $retArray[$i + $offset] = isset( $values[(int)$column] ) ? $values[(int)$column] : null;
            }
            else
                $retArray[$i + $offset] = array_key_exists( $column, $row ) ? $row[$column] : null;
        }
        return $retArray;
    }

    /**
     * Runs a statement of SQL (the subset) or CQL.
     *
     * @param string $sql
     * @param int|false $limit
     * @param int $offset
     * @param string $caller
     * @return array|bool rows of a SELECT, true for anything else, false on an error
     * @throws eZDBException with the exceptions error handling
     */
    protected function execute( $sql, $limit, $offset, $caller )
    {
        if ( !$this->IsConnected || !$this->Transport )
        {
            eZDebug::writeError( 'Trying to do a query without being connected to a database!', __CLASS__ );
            return false;
        }
        if ( $this->OutputSQL )
            $this->startTimer();
        $this->ErrorCategory = null;
        $this->AffectedRows = 0;
        try
        {
            switch ( expCassandraDialect::kind( $sql ) )
            {
                case 'select':
                    $result = $this->runSelect( expCassandraDialect::parse( $sql ), $limit, $offset );
                    break;
                case 'insert':
                    $result = $this->runInsert( expCassandraDialect::parse( $sql ) );
                    break;
                case 'update':
                    $result = $this->runUpdate( expCassandraDialect::parse( $sql ) );
                    break;
                case 'delete':
                    $result = $this->runDelete( expCassandraDialect::parse( $sql ) );
                    break;
                default:
                    $result = $this->runCQL( $sql );
            }
        }
        catch ( expDBUnsupportedException $e )
        {
            $this->fail( $e->getMessage(), 'unsupported' );
            $result = false;
        }
        if ( $this->OutputSQL )
        {
            $this->endTimer();
            if ( $this->timeTaken() > $this->SlowSQLTimeout )
                $this->reportQuery( __CLASS__, $sql, false, $this->timeTaken() );
        }
        if ( $result === false )
        {
            eZDebug::writeError( self::failedQueryMessage( $this->ErrorMessage, $sql ), __CLASS__ . '::' . $caller );
            if ( $this->errorHandling == eZDB::ERROR_HANDLING_EXCEPTIONS )
                throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
            $this->reportError();
            return false;
        }
        return $result;
    }

    /**
     * A statement that is CQL already.
     *
     * @param string $sql
     * @return bool
     */
    protected function runCQL( $sql )
    {
        $cql = expCassandraDialect::passThrough( $sql );
        if ( $cql === '' )
            return true;
        // session statements of other databases mean nothing here
        if ( preg_match( '/^(SET\s+NAMES|SET\s+SESSION|SET\s+sql_mode|BEGIN|START\s+TRANSACTION|COMMIT|ROLLBACK|LOCK\s+TABLES|UNLOCK\s+TABLES)\b/i', $cql ) )
            return true;
        if ( preg_match( '/^TRUNCATE\s+TABLE\s+/i', $cql ) )
            $cql = preg_replace( '/^TRUNCATE\s+TABLE\s+/i', 'TRUNCATE ', $cql );
        $result = $this->send( $cql );
        if ( $result === false )
            return false;
        if ( preg_match( '/^(CREATE|DROP|ALTER)\s/i', $cql ) )
        {
            $this->Tables = array();
            if ( preg_match( '/^DROP\s+TABLE\s+(?:IF\s+EXISTS\s+)?("?)([\w.]+)\1/i', $cql, $m ) )
                $this->forgetTable( strtolower( preg_replace( '/^.*\./', '', $m[2] ) ) );
        }
        return true;
    }

    /**
     * The meta rows of a dropped table (its definition, its counters).
     *
     * @param string $table
     */
    protected function forgetTable( $table )
    {
        if ( in_array( $table, array( self::SEQUENCE_TABLE, self::SCHEMA_TABLE ), true ) )
            return;
        if ( $this->tableDescription( self::SCHEMA_TABLE ) )
            $this->send( 'DELETE FROM ' . expCassandraDialect::quoteName( self::SCHEMA_TABLE ) . ' WHERE table_name = ?', array( $table ) );
        if ( $this->tableDescription( self::SEQUENCE_TABLE ) )
        {
            $rows = $this->cql( 'SELECT name FROM ' . expCassandraDialect::quoteName( self::SEQUENCE_TABLE ) );
            foreach ( (array)$rows as $row )
                if ( strpos( (string)$row['name'], $table . '.' ) === 0 )
                    $this->send( 'DELETE FROM ' . expCassandraDialect::quoteName( self::SEQUENCE_TABLE ) . ' WHERE name = ?', array( $row['name'] ) );
        }
        foreach ( array_keys( $this->Sequences ) as $name )
            if ( strpos( $name, $table . '.' ) === 0 )
                unset( $this->Sequences[$name], $this->PendingBumps[$name] );
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
    }

    // ------------------------------------------------------------------ tables

    /**
     * A table as the server describes it: columns (name => CQL type, in order: partition key, clustering, the rest),
     * partition, clustering (column => asc/desc), indexes (column => index name), definition (the .dba table the
     * schema handler made it from, or null), auto (the auto_increment column or null). Null when there is no table.
     *
     * @param string $table
     * @return array|null
     */
    public function tableDescription( $table )
    {
        $table = strtolower( (string)$table );
        if ( array_key_exists( $table, $this->Tables ) )
            return $this->Tables[$table];
        $keyspace = $this->keyspace();
        $rows = $this->Transport->run( 'SELECT column_name, type, kind, position, clustering_order FROM system_schema.columns WHERE keyspace_name = ? AND table_name = ?',
                                       array( $keyspace, $table ) );
        if ( $rows === false || !$rows['rows'] )
            return $this->Tables[$table] = null;
        $partition = array();
        $clustering = array();
        $regular = array();
        $types = array();
        foreach ( $rows['rows'] as $row )
        {
            $types[$row['column_name']] = (string)$row['type'];
            if ( $row['kind'] === 'partition_key' )
                $partition[(int)$row['position']] = $row['column_name'];
            elseif ( $row['kind'] === 'clustering' )
                $clustering[(int)$row['position']] = array( $row['column_name'], strtolower( (string)$row['clustering_order'] ) );
            elseif ( $row['kind'] !== 'static' || true )
                $regular[] = $row['column_name'];
        }
        ksort( $partition );
        ksort( $clustering );
        sort( $regular );
        $columns = array();
        foreach ( $partition as $name )
            $columns[$name] = $types[$name];
        $order = array();
        foreach ( $clustering as $c )
        {
            $columns[$c[0]] = $types[$c[0]];
            $order[$c[0]] = $c[1] === 'desc' ? 'desc' : 'asc';
        }
        foreach ( $regular as $name )
            if ( !isset( $columns[$name] ) )
                $columns[$name] = $types[$name];
        $indexes = array();
        $index = $this->Transport->run( 'SELECT index_name, options FROM system_schema.indexes WHERE keyspace_name = ? AND table_name = ?', array( $keyspace, $table ) );
        if ( $index !== false )
        {
            foreach ( $index['rows'] as $row )
            {
                $target = is_array( $row['options'] ) && isset( $row['options']['target'] ) ? trim( (string)$row['options']['target'], '"' ) : '';
                if ( preg_match( '/^(?:values|keys|entries|full)\((.+)\)$/', $target, $m ) )
                    $target = '';
                if ( $target !== '' && isset( $columns[$target] ) )
                    $indexes[$target] = $row['index_name'];
            }
        }
        $definition = null;
        if ( $table !== self::SCHEMA_TABLE && $table !== self::SEQUENCE_TABLE )
        {
            $meta = $this->Transport->run( 'SELECT definition FROM ' . expCassandraDialect::quoteName( self::SCHEMA_TABLE ) . ' WHERE table_name = ?', array( $table ) );
            if ( $meta !== false && isset( $meta['rows'][0]['definition'] ) )
            {
                $decoded = json_decode( (string)$meta['rows'][0]['definition'], true );
                if ( is_array( $decoded ) )
                    $definition = $decoded;
            }
        }
        $auto = null;
        if ( $definition && isset( $definition['fields'] ) )
            foreach ( $definition['fields'] as $name => $field )
                if ( isset( $field['type'] ) && $field['type'] === 'auto_increment' && isset( $columns[strtolower( $name )] ) )
                    $auto = strtolower( $name );
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
        return $this->Tables[$table] = array( 'name' => $table, 'columns' => $columns, 'partition' => array_values( $partition ),
                                              'clustering' => $order, 'indexes' => $indexes, 'definition' => $definition, 'auto' => $auto );
    }

    /**
     * The table of a statement, or a failure (unknown table).
     *
     * @param string $table
     * @return array|false
     */
    protected function requireTable( $table )
    {
        $description = $this->tableDescription( $table );
        if ( $description === null )
        {
            $this->fail( "Table '" . $this->keyspace() . ".$table' doesn't exist", 'invalid', 1146 );
            return false;
        }
        return $description;
    }

    /**
     * Forgets what was read about the tables (after DDL from elsewhere).
     */
    public function forgetTables()
    {
        $this->Tables = array();
    }

    /**
     * The keyspace in use.
     *
     * @return string
     */
    public function keyspace()
    {
        return strtolower( (string)$this->DB );
    }

    /**
     * Whether a keyspace exists.
     *
     * @param string $keyspace
     * @return bool
     */
    public function keyspaceExists( $keyspace )
    {
        $rows = $this->Transport->run( 'SELECT keyspace_name FROM system_schema.keyspaces WHERE keyspace_name = ?', array( strtolower( (string)$keyspace ) ) );
        return $rows !== false && count( $rows['rows'] ) > 0;
    }

    // ------------------------------------------------------------------ SELECT

    /**
     * How a SELECT is answered (pure apart from the table's description): the CQL and its values, and what is left
     * for PHP.
     *
     * @param array $select a parsed SELECT
     * @param array $table tableDescription()
     * @param bool $allowFiltering
     * @return array kind (key, index, filter, scan), cql (WHERE part, without SELECT ... FROM), values, in (array( column,
     *               values ) of a chunked IN or null), rest (the WHERE left for PHP, or null), all (the WHERE left whole)
     */
    public static function plan( array $select, array $table, $allowFiltering = true )
    {
        $conjuncts = expCassandraDialect::conjuncts( $select['where'] );
        $plan = array( 'kind' => 'scan', 'where' => array(), 'values' => array(), 'in' => null, 'rest' => $select['where'] );
        $comparisons = array();
        foreach ( $conjuncts as $n => $condition )
        {
            $c = expCassandraDialect::simpleComparison( $condition );
            if ( $c !== null && isset( $table['columns'][$c['column']] ) )
                $comparisons[$n] = $c;
        }
        $bind = function ( $value, $column ) use ( $table ) {
            return expCassandraDialect::bindValue( $value, $table['columns'][$column] );
        };
        // the whole partition key with = (one IN at most): a key lookup, and = on the clustering columns in order
        $used = array();
        $where = array();
        $values = array();
        $in = null;
        $complete = (bool)$table['partition'];
        foreach ( $table['partition'] as $column )
        {
            $found = false;
            foreach ( $comparisons as $n => $c )
            {
                if ( $c['column'] !== $column || isset( $used[$n] ) )
                    continue;
                if ( $c['op'] === '=' && $bind( $c['value'], $column ) !== null )
                {
                    $where[] = expCassandraDialect::quoteName( $column ) . ' = ?';
                    $values[] = $bind( $c['value'], $column );
                    $used[$n] = true;
                    $found = true;
                    break;
                }
                if ( $c['op'] === 'in' && $in === null )
                {
                    $list = array();
                    foreach ( $c['values'] as $v )
                        if ( ( $b = $bind( $v, $column ) ) !== null )
                            $list[serialize( $b )] = $b;
                    $in = array( 'column' => $column, 'values' => array_values( $list ) );
                    $where[] = expCassandraDialect::quoteName( $column ) . ' IN ?';
                    $used[$n] = true;
                    $found = true;
                    break;
                }
            }
            if ( !$found )
            {
                $complete = false;
                break;
            }
        }
        if ( $complete )
        {
            foreach ( array_keys( $table['clustering'] ) as $column )
            {
                $found = false;
                foreach ( $comparisons as $n => $c )
                {
                    if ( $c['column'] === $column && !isset( $used[$n] ) && $c['op'] === '=' && $bind( $c['value'], $column ) !== null )
                    {
                        $where[] = expCassandraDialect::quoteName( $column ) . ' = ?';
                        $values[] = $bind( $c['value'], $column );
                        $used[$n] = true;
                        $found = true;
                        break;
                    }
                }
                if ( !$found )
                    break;
            }
            $plan['kind'] = 'key';
        }
        else
        {
            $where = array();
            $values = array();
            $used = array();
            $in = null;
            // = on an indexed column
            foreach ( $comparisons as $n => $c )
            {
                if ( $c['op'] === '=' && isset( $table['indexes'][$c['column']] ) && $bind( $c['value'], $c['column'] ) !== null )
                {
                    $where[] = expCassandraDialect::quoteName( $c['column'] ) . ' = ?';
                    $values[] = $bind( $c['value'], $c['column'] );
                    $used[$n] = true;
                    $plan['kind'] = 'index';
                    break;
                }
            }
            // comparisons of columns that are no key columns, filtered by the server
            if ( $allowFiltering )
            {
                $keys = array_merge( $table['partition'], array_keys( $table['clustering'] ) );
                foreach ( $comparisons as $n => $c )
                {
                    if ( isset( $used[$n] ) || in_array( $c['column'], $keys, true ) || !in_array( $c['op'], array( '=', '<', '>', '<=', '>=' ), true ) )
                        continue;
                    $type = strtolower( $table['columns'][$c['column']] );
                    if ( strpos( $type, '<' ) !== false || in_array( $type, array( 'blob', 'counter', 'duration' ), true ) )
                        continue;
                    $b = $bind( $c['value'], $c['column'] );
                    if ( $b === null )
                        continue;
                    $where[] = expCassandraDialect::quoteName( $c['column'] ) . ' ' . $c['op'] . ' ?';
                    $values[] = $b;
                    $used[$n] = true;
                }
                if ( $where && count( $used ) > ( $plan['kind'] === 'index' ? 1 : 0 ) )
                    $plan['kind'] = 'filter';
            }
        }
        $rest = array();
        foreach ( $conjuncts as $n => $condition )
            if ( !isset( $used[$n] ) )
                $rest[] = $condition;
        $plan['where'] = $where;
        $plan['values'] = $values;
        $plan['in'] = $in;
        $plan['rest'] = expCassandraDialect::andOf( $rest );
        return $plan;
    }

    /**
     * Answers a SELECT.
     *
     * @param array $select
     * @param int|false $limit
     * @param int $offset
     * @return array[]|false
     */
    protected function runSelect( array $select, $limit, $offset )
    {
        if ( $select['table'] === null )
            return expCassandraDialect::project( $select, array( array() ), array(), $limit, $offset );
        $table = $this->requireTable( $select['table'] );
        if ( $table === false )
            return false;
        foreach ( $select['columns'] as $item )
            if ( $item['expr']['t'] !== 'star' )
                foreach ( expCassandraDialect::columnsOf( $item['expr'] ) as $column )
                    if ( !isset( $table['columns'][$column] ) )
                        return $this->unknownColumn( $column, $select['table'] );
        $aliases = array();
        foreach ( $select['columns'] as $item )
            $aliases[] = $item['alias'];
        foreach ( array_merge( expCassandraDialect::columnsOf( $select['where'] ) ) as $column )
            if ( !isset( $table['columns'][$column] ) )
                return $this->unknownColumn( $column, $select['table'] );
        foreach ( $select['order'] as $o )
            foreach ( expCassandraDialect::columnsOf( $o['expr'] ) as $column )
                if ( !isset( $table['columns'][$column] ) && !in_array( $column, $aliases, true ) )
                    return $this->unknownColumn( $column, $select['table'] );
        if ( $limit === 0 )
            return array();

        $plan = self::plan( $select, $table, self::flag( $this->Settings['AllowFiltering'] ) );
        $aggregate = false;
        $countOnly = count( $select['columns'] ) === 1 && $select['columns'][0]['expr']['t'] === 'agg'
                     && $select['columns'][0]['expr']['fn'] === 'COUNT' && $select['columns'][0]['expr']['arg'] === null;
        foreach ( $select['columns'] as $item )
            $aggregate = $aggregate || $item['expr']['t'] === 'agg';

        // COUNT(*) the server can count: nothing left for PHP
        if ( $countOnly && $plan['rest'] === null && $plan['in'] === null )
        {
            $cql = 'SELECT COUNT(*) AS n FROM ' . expCassandraDialect::quoteName( $table['name'] ) . ( $plan['where'] ? ' WHERE ' . implode( ' AND ', $plan['where'] ) : '' )
                 . ( $plan['kind'] === 'filter' || ( $plan['kind'] === 'index' && count( $plan['where'] ) > 1 ) ? ' ALLOW FILTERING' : '' );
            $this->Plans[$plan['kind']]++;
            $rows = $this->cql( $cql, $plan['values'] );
            if ( $rows === false )
                return false;
            $n = isset( $rows[0]['n'] ) ? (string)$rows[0]['n'] : '0';
            $result = array( array( $select['columns'][0]['alias'] => $n ) );
            return $offset > 0 || ( $select['offset'] ?? 0 ) > 0 ? array() : $result;
        }

        // the columns to read
        $needed = array();
        $star = false;
        foreach ( $select['columns'] as $item )
        {
            if ( $item['expr']['t'] === 'star' )
                $star = true;
            else
                $needed = array_merge( $needed, expCassandraDialect::columnsOf( $item['expr'] ) );
        }
        $needed = array_merge( $needed, expCassandraDialect::columnsOf( $plan['rest'] ) );
        foreach ( $select['order'] as $o )
            foreach ( expCassandraDialect::columnsOf( $o['expr'] ) as $column )
                if ( isset( $table['columns'][$column] ) )
                    $needed[] = $column;
        $needed = array_values( array_unique( $needed ) );
        if ( $star )
            $list = '*';
        elseif ( $needed )
            $list = implode( ', ', array_map( array( 'expCassandraDialect', 'quoteName' ), $needed ) );
        else
        {
            // COUNT(*), constants: one column is enough to count the rows
            $first = array_keys( $table['columns'] );
            $list = expCassandraDialect::quoteName( $first[0] );
        }

        // a LIMIT the server can apply: nothing left for PHP but the slice
        $serverLimit = null;
        if ( $plan['rest'] === null && $plan['in'] === null && !$select['order'] && !$aggregate && !$select['distinct'] )
        {
            $limits = array();
            if ( $select['limit'] !== null )
                $limits[] = (int)$select['limit'];
            if ( $limit !== false )
                $limits[] = (int)$limit;
            if ( $limits )
                $serverLimit = min( $limits ) + (int)$offset + (int)$select['offset'];
        }
        $base = 'SELECT ' . $list . ' FROM ' . expCassandraDialect::quoteName( $table['name'] );
        $filtering = $plan['kind'] === 'filter' || ( $plan['kind'] === 'index' && count( $plan['where'] ) > 1 );
        $this->Plans[$plan['kind']]++;

        $statements = array();
        if ( $plan['in'] !== null )
        {
            if ( !$plan['in']['values'] )
                $statements = array();
            else
                foreach ( array_chunk( $plan['in']['values'], max( 1, (int)$this->Settings['InListChunkSize'] ) ) as $chunk )
                {
                    $where = $plan['where'];
                    $values = array();
                    $v = 0;
                    foreach ( $where as $w => $part )
                    {
                        if ( substr( $part, -5 ) === ' IN ?' )
                        {
                            $where[$w] = substr( $part, 0, -1 ) . '(' . implode( ', ', array_fill( 0, count( $chunk ), '?' ) ) . ')';
                            $values = array_merge( $values, $chunk );
                        }
                        else
                            $values[] = $plan['values'][$v++];
                    }
                    $statements[] = array( $base . ' WHERE ' . implode( ' AND ', $where ), $values );
                }
        }
        else
            $statements[] = array( $base . ( $plan['where'] ? ' WHERE ' . implode( ' AND ', $plan['where'] ) : '' )
                                   . ( $serverLimit !== null ? ' LIMIT ' . $serverLimit : '' ) . ( $filtering ? ' ALLOW FILTERING' : '' ), $plan['values'] );

        $rows = $this->readRows( $statements, $plan['rest'], $select, $limit, $offset, $aggregate );
        if ( $rows === false && $filtering && $this->ErrorCategory === 'invalid' )
        {
            // the server would not filter this (a type, an option of the table): read the table and filter here
            $this->Plans['scan']++;
            $columns = array_values( array_unique( array_merge( $star ? array() : $needed, expCassandraDialect::columnsOf( $select['where'] ) ) ) );
            $scanBase = 'SELECT ' . ( $star ? '*' : implode( ', ', array_map( array( 'expCassandraDialect', 'quoteName' ), $columns ? $columns : array_slice( array_keys( $table['columns'] ), 0, 1 ) ) ) )
                      . ' FROM ' . expCassandraDialect::quoteName( $table['name'] );
            $rows = $this->readRows( array( array( $scanBase, array() ) ), $select['where'], $select, $limit, $offset, $aggregate );
        }
        if ( $rows === false )
            return false;
        return expCassandraDialect::project( $select, $rows, array_keys( $table['columns'] ), $limit, $offset );
    }

    /**
     * Reads the rows of statements page by page and keeps those the rest of the WHERE passes; stops at the rows the
     * result needs, refuses to read more than ScanLimit.
     *
     * @return array[]|false
     */
    protected function readRows( array $statements, $rest, array $select, $limit, $offset, $aggregate )
    {
        $scanLimit = max( 1, (int)$this->Settings['ScanLimit'] );
        $needed = null;
        if ( !$select['order'] && !$aggregate && !$select['distinct'] )
        {
            $limits = array();
            if ( $select['limit'] !== null )
                $limits[] = (int)$select['limit'];
            if ( $limit !== false )
                $limits[] = (int)$limit;
            if ( $limits )
                $needed = min( $limits ) + (int)$offset + (int)$select['offset'];
        }
        $kept = array();
        $read = 0;
        $tooMany = false;
        foreach ( $statements as $statement )
        {
            $result = $this->cqlEach( $statement[0], $statement[1], function ( $row ) use ( &$kept, &$read, &$tooMany, $rest, $needed, $scanLimit ) {
                $read++;
                if ( $read > $scanLimit )
                {
                    $tooMany = true;
                    return false;
                }
                $row = self::rowValues( $row );
                if ( expCassandraDialect::matches( $rest, $row ) )
                    $kept[] = $row;
                if ( $needed !== null && count( $kept ) >= $needed )
                    return false;
                return true;
            } );
            $this->Plans['rows_read'] += min( $read, $scanLimit );
            if ( $result === false )
                return false;
            if ( $tooMany )
            {
                $this->fail( "The statement would read more than $scanLimit rows of " . $select['table'] . ' to answer what CQL cannot ([CassandraSettings] ScanLimit)', 'scan_limit' );
                return false;
            }
            if ( $needed !== null && count( $kept ) >= $needed )
                break;
        }
        return $kept;
    }

    /**
     * A row of the server with lower-case names and the library's values as they are (for the conditions).
     *
     * @param array $row
     * @return array
     */
    public static function rowValues( array $row )
    {
        $out = array();
        foreach ( $row as $name => $value )
            $out[strtolower( (string)$name )] = $value;
        return $out;
    }

    protected function unknownColumn( $column, $table )
    {
        $this->fail( "Unknown column '$column' in '$table'", 'invalid', 1054 );
        return false;
    }

    /**
     * The rows a WHERE finds, every column (for UPDATE and DELETE).
     *
     * @param array $table
     * @param array|null $where
     * @param string[]|null $columns the columns to read (all when null)
     * @return array[]|false
     */
    protected function matchingRows( array $table, $where, $columns = null )
    {
        $select = array( 'type' => 'select', 'table' => $table['name'], 'alias' => '', 'distinct' => false,
                         'columns' => array( array( 'expr' => array( 't' => 'star' ), 'alias' => '*' ) ),
                         'where' => $where, 'order' => array(), 'limit' => null, 'offset' => 0 );
        foreach ( expCassandraDialect::columnsOf( $where ) as $column )
            if ( !isset( $table['columns'][$column] ) )
                return $this->unknownColumn( $column, $table['name'] );
        $plan = self::plan( $select, $table, self::flag( $this->Settings['AllowFiltering'] ) );
        $list = $columns === null ? '*' : implode( ', ', array_map( array( 'expCassandraDialect', 'quoteName' ), array_values( array_unique( array_merge( $columns, expCassandraDialect::columnsOf( $plan['rest'] ) ) ) ) ) );
        $base = 'SELECT ' . $list . ' FROM ' . expCassandraDialect::quoteName( $table['name'] );
        $filtering = $plan['kind'] === 'filter' || ( $plan['kind'] === 'index' && count( $plan['where'] ) > 1 );
        $this->Plans[$plan['kind']]++;
        $statements = array();
        if ( $plan['in'] !== null )
        {
            foreach ( array_chunk( $plan['in']['values'], max( 1, (int)$this->Settings['InListChunkSize'] ) ) as $chunk )
            {
                $where = $plan['where'];
                $values = array();
                $v = 0;
                foreach ( $where as $w => $part )
                {
                    if ( substr( $part, -5 ) === ' IN ?' )
                    {
                        $where[$w] = substr( $part, 0, -1 ) . '(' . implode( ', ', array_fill( 0, count( $chunk ), '?' ) ) . ')';
                        $values = array_merge( $values, $chunk );
                    }
                    else
                        $values[] = $plan['values'][$v++];
                }
                $statements[] = array( $base . ' WHERE ' . implode( ' AND ', $where ), $values );
            }
        }
        else
            $statements[] = array( $base . ( $plan['where'] ? ' WHERE ' . implode( ' AND ', $plan['where'] ) : '' ) . ( $filtering ? ' ALLOW FILTERING' : '' ), $plan['values'] );
        $rows = $this->readRows( $statements, $plan['rest'], $select, false, 0, false );
        if ( $rows === false && $filtering && $this->ErrorCategory === 'invalid' )
            $rows = $this->readRows( array( array( 'SELECT ' . $list . ' FROM ' . expCassandraDialect::quoteName( $table['name'] ), array() ) ), $where, $select, false, 0, false );
        return $rows;
    }

    /**
     * The WHERE of a row's primary key, and its values.
     *
     * @param array $table
     * @param array $row
     * @return array( string, array )
     */
    protected static function keyWhere( array $table, array $row )
    {
        $where = array();
        $values = array();
        foreach ( array_merge( $table['partition'], array_keys( $table['clustering'] ) ) as $column )
        {
            $where[] = expCassandraDialect::quoteName( $column ) . ' = ?';
            $values[] = expCassandraDialect::bindValue( array_key_exists( $column, $row ) ? $row[$column] : null, $table['columns'][$column] );
        }
        return array( implode( ' AND ', $where ), $values );
    }

    /**
     * Whether a WHERE names the whole primary key with = and nothing else (a write needs no read).
     *
     * @param array $table
     * @param array|null $where
     * @return array|null column => value, or null
     */
    protected static function exactKey( array $table, $where )
    {
        $keys = array_merge( $table['partition'], array_keys( $table['clustering'] ) );
        $found = array();
        foreach ( expCassandraDialect::conjuncts( $where ) as $condition )
        {
            $c = expCassandraDialect::simpleComparison( $condition );
            if ( $c === null || $c['op'] !== '=' || !in_array( $c['column'], $keys, true ) || isset( $found[$c['column']] ) )
                return null;
            if ( expCassandraDialect::bindValue( $c['value'], $table['columns'][$c['column']] ) === null )
                return null;
            $found[$c['column']] = $c['value'];
        }
        return count( $found ) === count( $keys ) ? $found : null;
    }

    // ------------------------------------------------------------------ INSERT, UPDATE, DELETE

    /**
     * @param array $insert
     * @return bool
     */
    protected function runInsert( array $insert )
    {
        $table = $this->requireTable( $insert['table'] );
        if ( $table === false )
            return false;
        foreach ( $insert['columns'] as $column )
            if ( !isset( $table['columns'][$column] ) )
                return $this->unknownColumn( $column, $insert['table'] );
        $keys = array_merge( $table['partition'], array_keys( $table['clustering'] ) );
        foreach ( $insert['rows'] as $values )
        {
            $row = array();
            foreach ( $values as $column => $node )
                $row[$column] = $node['v'];
            // defaults of the definition for the columns the statement leaves out
            if ( $table['definition'] && isset( $table['definition']['fields'] ) )
                foreach ( $table['definition']['fields'] as $name => $field )
                {
                    $name = strtolower( $name );
                    if ( !array_key_exists( $name, $row ) && isset( $table['columns'][$name] ) && array_key_exists( 'default', $field )
                         && $field['default'] !== false && $field['default'] !== null && $name !== $table['auto'] )
                        $row[$name] = $field['default'];
                }
            // a table without a primary key of its own (expCassandraSchema::ROW_ID): a new row gets a new uuid
            if ( $table['partition'] === array( 'exp_row_id' ) && ( !isset( $row['exp_row_id'] ) || $row['exp_row_id'] === '' ) )
                $row['exp_row_id'] = self::uuid4();
            if ( $table['auto'] !== null )
            {
                $auto = $table['auto'];
                $given = array_key_exists( $auto, $row ) ? $row[$auto] : null;
                if ( $given === null || $given === '' || (string)$given === '0' )
                {
                    $id = $this->nextID( $table['name'], $auto );
                    if ( $id === false )
                        return false;
                    $row[$auto] = $id;
                }
                else
                    $this->noteExplicitID( $table['name'], $auto, (int)$given );
                $this->LastIDs[$table['name']] = (int)$row[$auto];
            }
            foreach ( $keys as $key )
                if ( !array_key_exists( $key, $row ) || expCassandraDialect::bindValue( $row[$key], $table['columns'][$key] ) === null )
                {
                    $this->fail( "Field '$key' doesn't have a default value (a key column of {$table['name']})", 'invalid', 1364 );
                    return false;
                }
            $mode = $insert['replace'] ? 'replace' : ( $insert['ignore'] ? 'ignore' : 'insert' );
            if ( isset( $insert['update_columns'] ) )
                $mode = 'upsert';
            if ( !$this->writeRow( $table, $row, $mode, isset( $insert['update_columns'] ) ? $insert['update_columns'] : array() ) )
                return false;
        }
        if ( (int)$this->TransactionCounter === 0 )
            $this->flushBumps();
        return true;
    }

    /**
     * Writes one row: insert (refused when its key exists, by InsertCheck), ignore (left alone when it exists),
     * replace (written over), upsert (the update columns written over when it exists, the whole row when not).
     *
     * @param array $table
     * @param array $row column => value
     * @param string $mode
     * @param string[] $updateColumns
     * @return bool
     */
    protected function writeRow( array $table, array $row, $mode, array $updateColumns = array() )
    {
        $check = strtolower( (string)$this->Settings['InsertCheck'] );
        $inTransaction = (int)$this->TransactionCounter > 0;
        list( $keyWhere, $keyValues ) = self::keyWhere( $table, $row );
        $quotedTable = expCassandraDialect::quoteName( $table['name'] );
        $old = null;
        // a lookup of the key: for the duplicate check, an upsert, and for the journal of a transaction
        if ( ( $mode === 'insert' && $check === 'read' ) || $mode === 'upsert' || ( $mode === 'ignore' && $check !== 'lwt' ) || ( $inTransaction && $mode === 'replace' ) )
        {
            $found = $this->cql( 'SELECT * FROM ' . $quotedTable . ' WHERE ' . $keyWhere, $keyValues );
            if ( $found === false )
                return false;
            $old = $found ? self::rowValues( $found[0] ) : null;
            if ( $old !== null && $mode === 'insert' )
            {
                $this->fail( "Duplicate entry for the primary key of '{$table['name']}'", 'duplicate', 1062 );
                return false;
            }
            if ( $old !== null && $mode === 'ignore' )
                return true;
            if ( $old !== null && $mode === 'upsert' )
            {
                $changes = array();
                foreach ( $updateColumns as $column )
                    if ( array_key_exists( $column, $row ) )
                        $changes[$column] = $row[$column];
                if ( !$changes )
                    return true;
                if ( !$this->updateRow( $table, $old, $changes ) )
                    return false;
                $this->AffectedRows++;
                return true;
            }
        }
        $columns = array();
        $marks = array();
        $values = array();
        foreach ( $row as $column => $value )
        {
            $columns[] = expCassandraDialect::quoteName( $column );
            $marks[] = '?';
            $values[] = expCassandraDialect::bindValue( $value, $table['columns'][$column] );
        }
        $lwt = ( $mode === 'insert' || $mode === 'ignore' ) && $check === 'lwt';
        $cql = 'INSERT INTO ' . $quotedTable . ' (' . implode( ', ', $columns ) . ') VALUES (' . implode( ', ', $marks ) . ')' . ( $lwt ? ' IF NOT EXISTS' : '' );
        $result = $this->send( $cql, $values );
        if ( $result === false )
            return false;
        if ( $lwt && isset( $result['rows'][0]['[applied]'] ) && !$result['rows'][0]['[applied]'] )
        {
            if ( $mode === 'ignore' )
                return true;
            $this->fail( "Duplicate entry for the primary key of '{$table['name']}'", 'duplicate', 1062 );
            return false;
        }
        $this->AffectedRows++;
        if ( $inTransaction )
            $this->journal( $old === null ? 'delete' : 'restore', $table['name'], $old === null ? $row : $old );
        return true;
    }

    /**
     * Updates the columns of one existing row (by its key; a changed key moves the row).
     *
     * @param array $table
     * @param array $old the row as it is
     * @param array $changes column => new value
     * @return bool
     */
    protected function updateRow( array $table, array $old, array $changes )
    {
        $keys = array_merge( $table['partition'], array_keys( $table['clustering'] ) );
        $quotedTable = expCassandraDialect::quoteName( $table['name'] );
        $keyChanged = false;
        foreach ( $changes as $column => $value )
            if ( in_array( $column, $keys, true ) && expCassandraDialect::compare( (string)$value, (string)$old[$column] ) !== 0 )
                $keyChanged = true;
        $inTransaction = (int)$this->TransactionCounter > 0;
        if ( $keyChanged )
        {
            $new = array_merge( $old, $changes );
            foreach ( $keys as $key )
                if ( expCassandraDialect::bindValue( $new[$key], $table['columns'][$key] ) === null )
                {
                    $this->fail( "Column '$key' cannot be null (a key column of {$table['name']})", 'invalid', 1048 );
                    return false;
                }
            list( $oldWhere, $oldValues ) = self::keyWhere( $table, $old );
            if ( $this->send( 'DELETE FROM ' . $quotedTable . ' WHERE ' . $oldWhere, $oldValues ) === false )
                return false;
            $columns = array();
            $values = array();
            foreach ( $new as $column => $value )
            {
                if ( $value === null || !isset( $table['columns'][$column] ) )
                    continue;
                $columns[] = expCassandraDialect::quoteName( $column );
                $values[] = expCassandraDialect::bindValue( $value, $table['columns'][$column] );
            }
            if ( $this->send( 'INSERT INTO ' . $quotedTable . ' (' . implode( ', ', $columns ) . ') VALUES (' . implode( ', ', array_fill( 0, count( $columns ), '?' ) ) . ')', $values ) === false )
                return false;
            if ( $inTransaction )
            {
                $this->journal( 'delete', $table['name'], $new );
                $this->journal( 'restore', $table['name'], $old );
            }
            return true;
        }
        $set = array();
        $values = array();
        foreach ( $changes as $column => $value )
        {
            if ( in_array( $column, $keys, true ) )
                continue;
            $set[] = expCassandraDialect::quoteName( $column ) . ' = ?';
            $values[] = expCassandraDialect::bindValue( $value, $table['columns'][$column] );
        }
        if ( !$set )
            return true;
        list( $keyWhere, $keyValues ) = self::keyWhere( $table, $old );
        if ( $this->send( 'UPDATE ' . $quotedTable . ' SET ' . implode( ', ', $set ) . ' WHERE ' . $keyWhere, array_merge( $values, $keyValues ) ) === false )
            return false;
        if ( $inTransaction )
            $this->journal( 'restore', $table['name'], $old );
        return true;
    }

    /**
     * @param array $update
     * @return bool
     */
    protected function runUpdate( array $update )
    {
        $table = $this->requireTable( $update['table'] );
        if ( $table === false )
            return false;
        foreach ( $update['set'] as $column => $expr )
        {
            if ( !isset( $table['columns'][$column] ) )
                return $this->unknownColumn( $column, $update['table'] );
            foreach ( expCassandraDialect::columnsOf( $expr ) as $read )
                if ( !isset( $table['columns'][$read] ) )
                    return $this->unknownColumn( $read, $update['table'] );
        }
        $rows = $this->matchingRows( $table, $update['where'] );
        if ( $rows === false )
            return false;
        foreach ( $rows as $old )
        {
            $changes = array();
            foreach ( $update['set'] as $column => $expr )
                $changes[$column] = expCassandraDialect::value( $expr, $old );
            if ( $table['auto'] !== null && isset( $changes[$table['auto']] ) && $changes[$table['auto']] !== null )
                $this->noteExplicitID( $table['name'], $table['auto'], (int)$changes[$table['auto']] );
            if ( !$this->updateRow( $table, $old, $changes ) )
                return false;
            $this->AffectedRows++;
        }
        if ( (int)$this->TransactionCounter === 0 )
            $this->flushBumps();
        return true;
    }

    /**
     * @param array $delete
     * @return bool
     */
    protected function runDelete( array $delete )
    {
        $table = $this->requireTable( $delete['table'] );
        if ( $table === false )
            return false;
        $quotedTable = expCassandraDialect::quoteName( $table['name'] );
        $inTransaction = (int)$this->TransactionCounter > 0;
        if ( $delete['where'] === null && !$inTransaction )
        {
            $ok = $this->send( 'TRUNCATE ' . $quotedTable ) !== false;
            return $ok;
        }
        $exact = self::exactKey( $table, $delete['where'] );
        if ( $exact !== null && !$inTransaction )
        {
            list( $keyWhere, $keyValues ) = self::keyWhere( $table, $exact );
            if ( $this->send( 'DELETE FROM ' . $quotedTable . ' WHERE ' . $keyWhere, $keyValues ) === false )
                return false;
            $this->AffectedRows = 1;
            return true;
        }
        $keys = array_merge( $table['partition'], array_keys( $table['clustering'] ) );
        $rows = $this->matchingRows( $table, $delete['where'], $inTransaction ? null : $keys );
        if ( $rows === false )
            return false;
        foreach ( $rows as $row )
        {
            list( $keyWhere, $keyValues ) = self::keyWhere( $table, $row );
            if ( $this->send( 'DELETE FROM ' . $quotedTable . ' WHERE ' . $keyWhere, $keyValues ) === false )
                return false;
            $this->AffectedRows++;
            if ( $inTransaction )
                $this->journal( 'restore', $table['name'], $row );
        }
        return true;
    }

    // ------------------------------------------------------------------ ids

    /**
     * A random UUID (version 4).
     *
     * @return string
     */
    public static function uuid4()
    {
        $bytes = random_bytes( 16 );
        $bytes[6] = chr( ( ord( $bytes[6] ) & 0x0f ) | 0x40 );
        $bytes[8] = chr( ( ord( $bytes[8] ) & 0x3f ) | 0x80 );
        $hex = bin2hex( $bytes );
        return substr( $hex, 0, 8 ) . '-' . substr( $hex, 8, 4 ) . '-' . substr( $hex, 12, 4 ) . '-' . substr( $hex, 16, 4 ) . '-' . substr( $hex, 20 );
    }

    /**
     * The tables of the driver's own (the counters, the definitions), made when missing.
     *
     * @return bool
     */
    public function ensureMetaTables()
    {
        foreach ( array( self::SEQUENCE_TABLE => '(name text PRIMARY KEY, value bigint)',
                         self::SCHEMA_TABLE => '(table_name text PRIMARY KEY, definition text, created timestamp)' ) as $name => $columns )
        {
            if ( $this->tableDescription( $name ) !== null )
                continue;
            if ( $this->send( 'CREATE TABLE IF NOT EXISTS ' . expCassandraDialect::quoteName( $name ) . ' ' . $columns ) === false )
                return false;
            unset( $this->Tables[$name] );
        }
        return true;
    }

    /**
     * The next id of an auto_increment column: the counter row moved on by compare and set.
     *
     * @param string $table
     * @param string $column
     * @return int|false
     */
    public function nextID( $table, $column )
    {
        $name = strtolower( $table ) . '.' . strtolower( $column );
        if ( !$this->ensureMetaTables() )
            return false;
        $this->flushBumps( $name );
        $quoted = expCassandraDialect::quoteName( self::SEQUENCE_TABLE );
        $current = $this->sequenceValue( $name );
        if ( $current === false )
            return false;
        for ( $attempt = 0; $attempt < 100; $attempt++ )
        {
            $next = $current + 1;
            $result = $this->send( "UPDATE $quoted SET value = ? WHERE name = ? IF value = ?", array( $next, $name, $current ) );
            if ( $result === false )
                return false;
            $row = isset( $result['rows'][0] ) ? $result['rows'][0] : array( '[applied]' => true );
            if ( !empty( $row['[applied]'] ) )
            {
                $this->Sequences[$name] = $next;
                return $next;
            }
            $current = isset( $row['value'] ) ? (int)$row['value'] : $this->sequenceValue( $name, true );
            if ( $current === false )
                return false;
        }
        $this->fail( "The counter of $name was changed by others 100 times in a row", 'unavailable' );
        return false;
    }

    /**
     * The value of a counter row, made at 0 (or the column's highest id) when missing.
     *
     * @param string $name table.column
     * @param bool $fresh
     * @return int|false
     */
    protected function sequenceValue( $name, $fresh = false )
    {
        if ( !$fresh && isset( $this->Sequences[$name] ) )
            return $this->Sequences[$name];
        $quoted = expCassandraDialect::quoteName( self::SEQUENCE_TABLE );
        $rows = $this->cql( "SELECT value FROM $quoted WHERE name = ?", array( $name ), array( 'consistency' => strtoupper( (string)$this->Settings['SerialConsistency'] ) ) );
        if ( $rows === false )
            return false;
        if ( $rows )
            return $this->Sequences[$name] = (int)$rows[0]['value'];
        // a table filled before its counter existed (an import of rows with their ids): start after its highest id
        list( $table, $column ) = explode( '.', $name, 2 );
        $start = 0;
        $description = $this->tableDescription( $table );
        if ( $description !== null && isset( $description['columns'][$column] ) )
        {
            $this->cqlEach( 'SELECT ' . expCassandraDialect::quoteName( $column ) . ' FROM ' . expCassandraDialect::quoteName( $table ), array(), function ( $row ) use ( &$start, $column ) {
                $v = (int)$row[$column];
                if ( $v > $start )
                    $start = $v;
            } );
        }
        $result = $this->send( "INSERT INTO $quoted (name, value) VALUES (?, ?) IF NOT EXISTS", array( $name, $start ) );
        if ( $result === false )
            return false;
        $row = isset( $result['rows'][0] ) ? $result['rows'][0] : array( '[applied]' => true );
        return $this->Sequences[$name] = !empty( $row['[applied]'] ) ? $start : (int)$row['value'];
    }

    /**
     * An id a statement gave itself: the counter must not hand it out later. Carried into the counter at the end of
     * the statement, or of the transaction.
     *
     * @param string $table
     * @param string $column
     * @param int $id
     */
    public function noteExplicitID( $table, $column, $id )
    {
        $name = strtolower( $table ) . '.' . strtolower( $column );
        if ( isset( $this->Sequences[$name] ) && $this->Sequences[$name] >= $id )
            return;
        if ( !isset( $this->PendingBumps[$name] ) || $this->PendingBumps[$name] < $id )
            $this->PendingBumps[$name] = $id;
    }

    /**
     * Moves the counters past the ids statements gave themselves.
     *
     * @param string|null $only one counter
     * @return bool
     */
    public function flushBumps( $only = null )
    {
        if ( !$this->PendingBumps || !$this->IsConnected )
            return true;
        if ( !$this->ensureMetaTables() )
            return false;
        $quoted = expCassandraDialect::quoteName( self::SEQUENCE_TABLE );
        foreach ( $this->PendingBumps as $name => $id )
        {
            if ( $only !== null && $name !== $only )
                continue;
            unset( $this->PendingBumps[$name] );
            $current = $this->sequenceValue( $name );
            for ( $attempt = 0; $current !== false && $current < $id && $attempt < 100; $attempt++ )
            {
                $result = $this->send( "UPDATE $quoted SET value = ? WHERE name = ? IF value = ?", array( $id, $name, $current ) );
                if ( $result === false )
                    return false;
                $row = isset( $result['rows'][0] ) ? $result['rows'][0] : array( '[applied]' => true );
                if ( !empty( $row['[applied]'] ) )
                {
                    $current = $id;
                    break;
                }
                $current = isset( $row['value'] ) ? (int)$row['value'] : false;
            }
            if ( $current !== false )
                $this->Sequences[$name] = max( $id, (int)$current );
        }
        return true;
    }

    /**
     * The id the last insert of this connection into $table gave (the last of any table without $table).
     *
     * @param string|false $table
     * @param string|false $column
     * @return int|false
     */
    function lastSerialID( $table = false, $column = false )
    {
        if ( $table === false || $table === null || $table === '' )
            return $this->LastIDs ? end( $this->LastIDs ) : false;
        $table = strtolower( trim( (string)$table, '`" ' ) );
        return isset( $this->LastIDs[$table] ) ? $this->LastIDs[$table] : false;
    }

    // ------------------------------------------------------------------ transactions

    /**
     * Notes how to undo a write of the open transaction.
     *
     * @param string $action delete (the row was new), restore (write the row back as it was)
     * @param string $table
     * @param array $row
     */
    protected function journal( $action, $table, array $row )
    {
        if ( count( $this->Journal ) >= max( 0, (int)$this->Settings['JournalLimit'] ) )
        {
            $this->JournalOverflow = true;
            return;
        }
        $this->Journal[] = array( $action, $table, $row );
    }

    function beginQuery()
    {
        $this->Journal = array();
        $this->JournalOverflow = false;
        return $this->IsConnected;
    }

    function commitQuery()
    {
        $this->Journal = array();
        $this->JournalOverflow = false;
        return $this->flushBumps();
    }

    /**
     * Undoes this connection's writes of the transaction, newest first (see the class).
     *
     * @return bool
     */
    function rollbackQuery()
    {
        $journal = array_reverse( $this->Journal );
        $this->Journal = array();
        $failed = 0;
        foreach ( $journal as $entry )
        {
            list( $action, $name, $row ) = $entry;
            $table = $this->tableDescription( $name );
            if ( $table === null )
            {
                $failed++;
                continue;
            }
            list( $keyWhere, $keyValues ) = self::keyWhere( $table, $row );
            $quoted = expCassandraDialect::quoteName( $name );
            if ( $action === 'delete' )
                $ok = $this->send( 'DELETE FROM ' . $quoted . ' WHERE ' . $keyWhere, $keyValues ) !== false;
            else
            {
                $columns = array();
                $values = array();
                foreach ( $row as $column => $value )
                {
                    if ( !isset( $table['columns'][$column] ) )
                        continue;
                    $columns[] = expCassandraDialect::quoteName( $column );
                    $values[] = $value === null ? null : expCassandraDialect::bindValue( $value, $table['columns'][$column] );
                }
                $ok = $this->send( 'INSERT INTO ' . $quoted . ' (' . implode( ', ', $columns ) . ') VALUES (' . implode( ', ', array_fill( 0, count( $columns ), '?' ) ) . ')', $values ) !== false;
            }
            if ( !$ok )
                $failed++;
        }
        $this->flushBumps();
        if ( $this->JournalOverflow || $failed )
        {
            eZDebug::writeWarning( 'Rollback on Cassandra: ' . ( $this->JournalOverflow ? 'more writes than JournalLimit, the first ones stay; ' : '' )
                                   . "$failed compensating writes failed", __CLASS__ );
            $this->JournalOverflow = false;
            return false;
        }
        return true;
    }

    /**
     * Cassandra locks no tables: the kernel's LOCK TABLES does nothing here.
     *
     * @param string|array $table
     */
    function lock( $table )
    {
    }

    function unlock()
    {
    }

    // ------------------------------------------------------------------ temporary tables

    /**
     * CQL has no temporary tables.
     */
    function createTempTable( $createTableQuery = '', $server = self::SERVER_SLAVE )
    {
        $this->fail( 'Cassandra has no temporary tables', 'unsupported' );
        eZDebug::writeError( $this->ErrorMessage, __METHOD__ );
        return false;
    }

    function dropTempTable( $dropTableQuery = '', $server = self::SERVER_SLAVE )
    {
        return true;
    }

    function dropTempTableList( $tableList, $server = self::SERVER_SLAVE )
    {
    }

    // ------------------------------------------------------------------ strings and expressions

    function escapeString( $str )
    {
        return str_replace( array( "'", "\0" ), array( "''", '' ), (string)$str );
    }

    function subString( $string, $from, $len = null )
    {
        return $len === null ? "SUBSTRING( $string, $from )" : "SUBSTRING( $string, $from, $len )";
    }

    function concatString( $strings = array() )
    {
        return 'CONCAT( ' . implode( ', ', (array)$strings ) . ' )';
    }

    function md5( $str )
    {
        return "MD5( $str )";
    }

    function bitAnd( $arg1, $arg2 )
    {
        return "( $arg1 & $arg2 )";
    }

    function bitOr( $arg1, $arg2 )
    {
        return "( $arg1 | $arg2 )";
    }

    public function sqlHelperDialect()
    {
        return expCassandraDialect::NAME;
    }

    public function sqlLiteral( $value )
    {
        return expCassandraDialect::literal( $value );
    }

    // ------------------------------------------------------------------ relations

    function supportedRelationTypeMask()
    {
        return eZDBInterface::RELATION_TABLE_BIT | eZDBInterface::RELATION_INDEX_BIT;
    }

    function supportedRelationTypes()
    {
        return array( eZDBInterface::RELATION_TABLE, eZDBInterface::RELATION_INDEX );
    }

    function relationList( $relationType = eZDBInterface::RELATION_TABLE )
    {
        if ( !$this->IsConnected )
            return false;
        if ( $relationType === eZDBInterface::RELATION_TABLE )
            $rows = $this->Transport->run( 'SELECT table_name AS name FROM system_schema.tables WHERE keyspace_name = ?', array( $this->keyspace() ) );
        elseif ( $relationType === eZDBInterface::RELATION_INDEX )
            $rows = $this->Transport->run( 'SELECT index_name AS name FROM system_schema.indexes WHERE keyspace_name = ?', array( $this->keyspace() ) );
        else
        {
            eZDebug::writeError( "Unsupported relation type '$relationType'", __METHOD__ );
            return false;
        }
        if ( $rows === false )
        {
            $this->setError();
            return false;
        }
        $names = array();
        foreach ( $rows['rows'] as $row )
            if ( !in_array( $row['name'], array( self::SEQUENCE_TABLE, self::SCHEMA_TABLE ), true ) )
                $names[] = (string)$row['name'];
        sort( $names );
        return $names;
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
        return $tables;
    }

    function removeRelation( $relationName, $relationType )
    {
        if ( $relationType === eZDBInterface::RELATION_TABLE )
            return $this->query( 'DROP TABLE IF EXISTS ' . expCassandraDialect::quoteName( $relationName ) );
        if ( $relationType === eZDBInterface::RELATION_INDEX )
            return $this->query( 'DROP INDEX IF EXISTS ' . expCassandraDialect::quoteName( $relationName ) );
        eZDebug::writeError( "Unknown relation type '$relationType'", __METHOD__ );
        return false;
    }

    // ------------------------------------------------------------------ server

    function databaseServerVersion()
    {
        if ( !$this->ServerInfo || $this->ServerInfo['version'] === '' )
            return false;
        preg_match( '/^(\d+(?:\.\d+)*)/', $this->ServerInfo['version'], $m );
        return array( 'string' => $this->ServerInfo['version'], 'values' => isset( $m[1] ) ? array_map( 'intval', explode( '.', $m[1] ) ) : array(),
                      'name' => $this->ServerInfo['name'] );
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
        return $this->ServerInfo ? $this->ServerInfo['version'] : false;
    }

    function isCharsetSupported( $charset )
    {
        return in_array( strtolower( (string)$charset ), array( 'utf-8', 'utf8', 'utf8mb4' ), true );
    }

    function availableDatabases()
    {
        if ( !$this->IsConnected )
            return false;
        $rows = $this->Transport->run( 'SELECT keyspace_name FROM system_schema.keyspaces' );
        if ( $rows === false )
            return false;
        $names = array();
        foreach ( $rows['rows'] as $row )
            if ( strpos( (string)$row['keyspace_name'], 'system' ) !== 0 )
                $names[] = (string)$row['keyspace_name'];
        sort( $names );
        return $names;
    }

    /**
     * The replication of a new keyspace from the settings (pure).
     *
     * @param array $settings
     * @return string
     */
    public static function replication( array $settings )
    {
        if ( strtolower( (string)$settings['ReplicationStrategy'] ) === 'networktopologystrategy' || trim( (string)$settings['DataCenters'] ) !== '' )
        {
            $parts = array( "'class': 'NetworkTopologyStrategy'" );
            foreach ( array_filter( array_map( 'trim', explode( ',', (string)$settings['DataCenters'] ) ), 'strlen' ) as $dc )
            {
                list( $name, $factor ) = array_pad( explode( ':', $dc, 2 ), 2, $settings['ReplicationFactor'] );
                $parts[] = expCassandraDialect::stringLiteral( trim( $name ) ) . ': ' . max( 1, (int)$factor );
            }
            if ( count( $parts ) === 1 )
                $parts[] = "'replication_factor': " . max( 1, (int)$settings['ReplicationFactor'] );
            return '{' . implode( ', ', $parts ) . '}';
        }
        return "{'class': 'SimpleStrategy', 'replication_factor': " . max( 1, (int)$settings['ReplicationFactor'] ) . '}';
    }

    /**
     * Creates a keyspace (the replication of [CassandraSettings]; on ScyllaDB without tablets unless Tablets=enabled:
     * lightweight transactions, which the ids use, need vnodes on ScyllaDB 2025.1).
     *
     * @param string $dbName
     * @return bool
     */
    function createDatabase( $dbName )
    {
        $cql = 'CREATE KEYSPACE IF NOT EXISTS ' . expCassandraDialect::quoteName( $dbName ) . ' WITH replication = ' . self::replication( $this->Settings );
        if ( $this->product() === 'scylladb' && !self::flag( $this->Settings['Tablets'] ) )
            $cql .= " AND tablets = {'enabled': false}";
        $ok = $this->send( $cql ) !== false;
        if ( !$ok && $this->product() === 'scylladb' && strpos( $cql, 'tablets' ) !== false && $this->ErrorCategory !== 'auth' )
            $ok = $this->send( preg_replace( '/ AND tablets = .*$/', '', $cql ) ) !== false;
        return $ok;
    }

    function removeDatabase( $dbName )
    {
        return $this->send( 'DROP KEYSPACE IF EXISTS ' . expCassandraDialect::quoteName( $dbName ) ) !== false;
    }

    function supportsDefaultValuesInsertion()
    {
        return false;
    }

    function close()
    {
        if ( $this->Transport && $this->IsConnected )
            $this->flushBumps();
        if ( $this->Transport )
            $this->Transport->close();
        $this->IsConnected = false;
    }
}
?>
