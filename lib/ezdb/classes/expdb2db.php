<?php
/**
 * File containing the expDb2DB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Db2 driver: IBM Db2 for Linux, UNIX and Windows 11.5 and 12.1 over pdo_ibm or ibm_db2 (or PDO_ODBC with an
 * ODBC driver of 64-bit lengths). site.ini:
 *
 * <code>
 * [DatabaseSettings]
 * DatabaseImplementation=db2
 * Server=db.example.com
 * Port=50000
 * Database=EXP                 (the database name; without Server a database alias catalogued in the client)
 * </code>
 *
 * and the [Db2Settings] of site.ini (layer, schema, retries). What the driver does that the other drivers do not
 * have to:
 *
 *  - Statements: the kernel's MySQL words become Db2's (expDb2Dialect::rewrite()): backticks as double quotes,
 *    LENGTH() and SUBSTRING() counted in characters, CONCAT() of more than two, MD5(), the bit operators of the
 *    language masks, ESCAPE for LIKE patterns with a backslash, FOR UPDATE as an isolation clause that keeps update
 *    locks, a SELECT without FROM given FROM SYSIBM.SYSDUMMY1, TRUNCATE as DELETE.
 *  - Paging: arrayQuery()'s limit and offset become OFFSET n ROWS FETCH FIRST m ROWS ONLY on the server.
 *  - New ids: an auto_increment column has a sequence (S_<table>) and a BEFORE INSERT trigger (T_<table>_BI) made
 *    by the schema handler: the trigger numbers a row that has no id, NULL or 0 (MySQL's ways of asking for the next
 *    number), and lastSerialID() reads PREVIOUS VALUE FOR the sequence, which Db2 keeps per connection: the id of
 *    this connection's last insert into that table, never another connection's. After a data file is loaded with
 *    its own ids, correctSequenceValues() moves every sequence past the highest id of its table.
 *  - Temporary tables: CREATE TEMPORARY TABLE becomes a created global temporary table (ON COMMIT PRESERVE ROWS
 *    NOT LOGGED): its definition is permanent and shared, its rows belong to the connection. The definition is
 *    made once, over a second connection so it is committed at once; the rows are deleted when the kernel drops the
 *    table. The database needs a USER TEMPORARY tablespace for them.
 *  - Statements: the kernel writes its values as literals, so every statement would be compiled anew; the
 *    connection asks for Db2's statement concentrator (StmtConcentrator=WITHLITERALS), which compiles statements
 *    that differ only in their literals once (measured: 400 statements in 413 ms instead of 1,822 ms).
 *  - Errors: deadlocks and rollbacks after a lock timeout, lock timeouts, duplicate keys and lost connections are
 *    told apart (expDb2Dialect::errorCategory()). A single statement outside a transaction that loses a deadlock
 *    is sent again; a lost connection outside a transaction is opened again.
 *
 * Not here: read replicas, LOCK TABLES (lock() opens a transaction), creating a database (createDatabase(): Db2
 * makes a database with the CREATE DATABASE command of the instance, not with SQL).
 *
 * @since 6.0.15
 * @package lib
 */
class expDb2DB extends eZDBInterface
{
    /** databaseName() of this driver */
    const NAME = 'db2';

    /** The dialect class of the driver's SQL */
    const DIALECT = 'expDb2Dialect';

    /** The settings group of site.ini */
    const SETTINGS_GROUP = 'Db2Settings';

    /** The PHP layers Layer=auto tries, in this order */
    const AUTO_LAYERS = array( 'pdo_ibm', 'ibm_db2' );

    /** @var array the defaults of [Db2Settings] */
    const DEFAULT_SETTINGS = array(
        'Layer' => 'auto',
        'Driver' => '',
        'Schema' => '',
        'Security' => '',
        'ConnectionOptions' => '',
        'StatementConcentrator' => 'enabled',
        'DeadlockRetries' => 3,
        'DeadlockRetryWait' => 50,
        'ReconnectOnLoss' => 'enabled',
        'InListChunkSize' => 1000,
    );

    /** @var expDb2Transport|null */
    public $Transport = null;

    /** @var array the effective settings */
    public $Settings = array();

    /** @var array|null what the server said about itself at connect */
    public $ServerInfo = null;

    /** @var array global temporary tables this connection has written rows to: lower-case name => name */
    public $TempTables = array();

    /** @var array|null lower-case names of the tables, read once per connection for the temporary tables */
    public $KnownTables = null;

    /** @var array global temporary tables whose columns were checked on this connection: lower-case name => true */
    public $CheckedTempTables = array();

    /** @var string|null the table of this connection's last INSERT (lastSerialID() without a table) */
    public $LastInsertTable = null;

    /** @var int statements sent to the server (each attempt counts) */
    public $StatementCount = 0;

    /** @var int connections opened again after one was lost */
    public $Reconnects = 0;

    /** @var int statements sent again after a deadlock */
    public $Retries = 0;

    /** @var string the last statement as it was sent (after the rewrites) */
    public $LastStatement = '';

    /** @var string|null the category of the last error */
    public $ErrorCategory = null;

    /**
     * Connects: the [DatabaseSettings] parameters of eZDB::instance(), and optionally 'db2' => array( <settings
     * names> => value ) over site.ini's settings group, and 'db2_transport' => an expDb2Transport to use instead of
     * PDO.
     *
     * @param array $parameters
     * @throws eZDBNoConnectionException when no connection can be opened
     */
    public function __construct( $parameters )
    {
        parent::__construct( $parameters );
        $key = static::NAME;
        $this->Settings = static::settings( isset( $parameters[$key] ) && is_array( $parameters[$key] ) ? $parameters[$key] : array() );
        if ( isset( $parameters['db2_transport'] ) && $parameters['db2_transport'] instanceof expDb2Transport )
            $this->Transport = $parameters['db2_transport'];
        else
        {
            $layer = expDb2Transport::availableLayer( (string)$this->Settings['Layer'], null, static::AUTO_LAYERS );
            if ( $layer === null )
            {
                $text = static::missingLayerText();
                if ( function_exists( 'eZAppendWarningItem' ) )
                    eZAppendWarningItem( array( 'error' => array( 'type' => 'ezdb', 'number' => eZDBInterface::ERROR_MISSING_EXTENSION ), 'text' => $text ) );
                eZDebug::writeWarning( $text, __CLASS__ );
                $this->IsConnected = false;
                return;
            }
            $this->Transport = static::newTransport( $layer );
        }
        eZDebug::createAccumulatorGroup( static::NAME . '_total', static::productName() . ' Total' );

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

    /** @return string */
    public static function productName()
    {
        return 'Db2';
    }

    /** @return string the warning when PHP has no layer */
    protected static function missingLayerText()
    {
        return 'Db2 needs the pdo_ibm or ibm_db2 extension (or PDO_ODBC with an ODBC driver of 64-bit lengths), the DB handler will not be initialized.';
    }

    /**
     * @param string $layer
     * @return expDb2Transport
     */
    protected static function newTransport( $layer )
    {
        if ( $layer === 'ibm_db2' )
            return new expDb2IbmDb2Transport( static::DIALECT );
        return new expDb2PDOTransport( $layer );
    }

    /**
     * The effective settings: the defaults, site.ini, then $overrides.
     *
     * @param array $overrides
     * @return array
     */
    public static function settings( array $overrides = array() )
    {
        $settings = static::DEFAULT_SETTINGS;
        if ( class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance();
            if ( $ini->hasGroup( static::SETTINGS_GROUP ) )
                foreach ( $ini->group( static::SETTINGS_GROUP ) as $name => $value )
                    if ( array_key_exists( $name, $settings ) && $value !== '' && $value !== null )
                        $settings[$name] = $value;
        }
        foreach ( $overrides as $name => $value )
            $settings[$name] = $value;
        return $settings;
    }

    /**
     * The connection settings for the transport.
     *
     * @return array
     */
    public function connectionConfig()
    {
        return array( 'host' => (string)$this->Server, 'port' => $this->Port ? (int)$this->Port : 0, 'database' => (string)$this->DB,
                      'user' => (string)$this->User, 'password' => (string)$this->Password, 'driver' => (string)$this->Settings['Driver'],
                      'security' => (string)$this->Settings['Security'], 'extra' => (string)$this->Settings['ConnectionOptions'],
                      'concentrator' => expDb2Dialect::flag( isset( $this->Settings['StatementConcentrator'] ) ? $this->Settings['StatementConcentrator'] : false ),
                      'persistent' => (bool)$this->UsePersistentConnection );
    }

    /**
     * Opens the connection, sets the schema and reads what the server is.
     *
     * @return bool
     */
    protected function openConnection()
    {
        eZDebug::accumulatorStart( static::NAME . '_connection', static::NAME . '_total', 'Database connection' );
        $ok = $this->Transport->open( $this->connectionConfig() );
        eZDebug::accumulatorStop( static::NAME . '_connection' );
        $this->IsConnected = $ok;
        if ( !$ok )
            return false;
        $this->DBConnection = $this->Transport;
        $this->DBWriteConnection = $this->Transport;
        $this->TempTables = array();
        $this->KnownTables = null;
        $this->CheckedTempTables = array();
        $this->LastInsertTable = null;
        if ( !$this->sessionSetup() )
        {
            $this->Transport->close();
            $this->IsConnected = false;
            return false;
        }
        if ( $this->ServerInfo === null )
        {
            $dialect = static::DIALECT;
            $this->ServerInfo = $dialect::describeVersion( $this->serverVersionString() );
            if ( !$this->ServerInfo['supported'] )
                eZDebug::writeWarning( $this->ServerInfo['name'] . ' (' . $this->ServerInfo['string'] . ') is not supported: ' . static::productName() . ' ' . $dialect::MIN_VERSION . ' or later is needed', __CLASS__ );
        }
        return true;
    }

    /**
     * What every new connection needs first: the schema of [Db2Settings] Schema.
     *
     * @return bool
     */
    protected function sessionSetup()
    {
        $schema = trim( (string)$this->Settings['Schema'] );
        if ( $schema === '' )
            return true;
        return $this->Transport->run( 'SET SCHEMA ' . expDb2Dialect::quoteIdentifier( $schema ) ) !== false;
    }

    /**
     * The server's version as the server tells it.
     *
     * @return string
     */
    protected function serverVersionString()
    {
        $result = $this->Transport->run( 'SELECT SERVICE_LEVEL AS v FROM SYSIBMADM.ENV_INST_INFO' );
        if ( $result !== false && isset( $result['rows'][0]['v'] ) && $result['rows'][0]['v'] !== '' )
            return (string)$result['rows'][0]['v'];
        return $this->Transport->serverVersion();
    }

    /**
     * Opens the connection again; the rows of temporary tables of the old one are gone with it.
     *
     * @return bool
     */
    public function reconnect()
    {
        if ( !$this->Transport )
            return false;
        $this->Transport->close();
        $ok = $this->openConnection();
        if ( $ok )
            $this->Reconnects++;
        return $ok;
    }

    public function ping()
    {
        $dialect = static::DIALECT;
        return $this->Transport && $this->Transport->isOpen() && $this->Transport->run( $dialect::addOneRowTable( 'SELECT 1 AS ok' ) ) !== false;
    }

    function databaseName()
    {
        return static::NAME;
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

    // ------------------------------------------------------------------ statements

    /**
     * A statement as it goes to the server (expDb2Dialect::rewrite()).
     *
     * @param string $sql
     * @return string
     */
    public function prepareStatement( $sql )
    {
        $dialect = static::DIALECT;
        return $dialect::rewrite( $sql );
    }

    /**
     * Runs a statement; sends it again once after a lost connection and up to DeadlockRetries times after a
     * deadlock, both only outside a transaction.
     *
     * @param string $sql
     * @param string $caller for the messages
     * @param bool $rewritten whether $sql is in the server's words already (arrayQuery() pages the rewritten statement)
     * @return array|false
     * @throws eZDBException with the exceptions error handling
     */
    protected function execute( $sql, $caller = 'query', $rewritten = false )
    {
        if ( !$this->IsConnected || !$this->Transport )
        {
            eZDebug::writeError( 'Trying to do a query without being connected to a database!', __CLASS__ );
            return false;
        }
        $dialect = static::DIALECT;
        $prepared = $rewritten ? (string)$sql : $this->prepareStatement( $sql );
        eZDebug::accumulatorStart( static::NAME . '_query', static::NAME . '_total', static::productName() . ' queries' );
        if ( $this->OutputSQL )
            $this->startTimer();

        $error = null;
        $reconnected = false;
        $retries = 0;
        $maxRetries = max( 0, (int)$this->Settings['DeadlockRetries'] );
        while ( true )
        {
            $this->LastStatement = $prepared;
            $this->StatementCount++;
            $bound = $dialect::parameterizeLongLiterals( $prepared );
            $result = $bound === null ? $this->Transport->run( $prepared ) : $this->Transport->run( $bound[0], $bound[1] );
            if ( $result !== false )
                break;
            $error = $this->Transport->error();
            $category = $dialect::errorCategory( $error['sqlcode'], $error['state'], $error['message'] );
            $outsideTransaction = (int)$this->TransactionCounter === 0;
            if ( $category === 'connection' && $outsideTransaction && !$reconnected && !$this->TempTables
                 && $dialect::flag( $this->Settings['ReconnectOnLoss'] ) )
            {
                $reconnected = true;
                if ( $this->reconnect() )
                    continue;
                $error = $this->Transport->error() ?: $error;
                break;
            }
            if ( $category === 'deadlock' && $outsideTransaction && $retries < $maxRetries )
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

        if ( $this->OutputSQL )
        {
            $this->endTimer();
            if ( $this->timeTaken() > $this->SlowSQLTimeout )
                $this->reportQuery( __CLASS__, $sql, false, $this->timeTaken() );
        }
        eZDebug::accumulatorStop( static::NAME . '_query' );

        if ( $result === false )
        {
            $this->takeError( $error );
            eZDebug::writeError( self::failedQueryMessage( $this->ErrorMessage, $prepared ), __CLASS__ . '::' . $caller );
            if ( (int)$this->TransactionCounter > 0 && in_array( $this->ErrorCategory, array( 'deadlock', 'connection' ), true ) )
                $this->invalidateTransaction();
            if ( $this->errorHandling == eZDB::ERROR_HANDLING_EXCEPTIONS )
                throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
            $this->reportError();
            return false;
        }
        $this->afterStatement( $prepared );
        eZDBQueryCache::noteWrite( $this, $sql );
        return $result;
    }

    /**
     * What a statement that ran changes for the driver: the table of an INSERT, the catalogue after a schema change,
     * and the sequence of a dropped table (it goes with its table, so the table can be made again: installers drop
     * and make a package's tables).
     *
     * @param string $prepared
     */
    protected function afterStatement( $prepared )
    {
        $dialect = static::DIALECT;
        $kind = $dialect::statementKind( $prepared );
        if ( $kind === 'INSERT' )
        {
            $table = $dialect::insertTable( $prepared );
            if ( $table !== null )
                $this->LastInsertTable = $table;
            return;
        }
        if ( !in_array( $kind, array( 'CREATE', 'ALTER', 'DROP', 'RENAME' ), true ) )
            return;
        $this->KnownTables = null;
        $dropped = $dialect::droppedTable( $prepared );
        if ( $dropped !== null && class_exists( 'expDb2Schema' ) )
        {
            $sequence = expDb2Schema::objectName( 'S_', $dropped );
            $found = $this->Transport->run( $dialect::existsQuery( 'SEQUENCE', $sequence ) );
            if ( $found !== false && $found['rows'] )
                $this->Transport->run( 'DROP SEQUENCE ' . $sequence );
        }
    }

    /**
     * Takes the transport's error into ErrorNumber (the SQLCODE, or -1), ErrorMessage (without any password) and
     * ErrorCategory.
     *
     * @param array|null $error
     */
    protected function takeError( $error )
    {
        $dialect = static::DIALECT;
        if ( !is_array( $error ) )
            $error = array( 'sqlcode' => null, 'state' => null, 'message' => 'Unknown ' . static::productName() . ' error' );
        $this->ErrorCategory = $dialect::errorCategory( $error['sqlcode'], $error['state'], $error['message'] );
        $this->ErrorNumber = $error['sqlcode'] !== null ? (int)$error['sqlcode'] : -1;
        $this->ErrorMessage = $this->mask( $error['message'] );
    }

    function setError()
    {
        if ( $this->Transport )
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

    function query( $sql, $server = false )
    {
        return $this->execute( $sql, 'query' ) !== false;
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
                $column = is_string( $params['column'] ) ? strtolower( $params['column'] ) : $params['column'];
        }
        $dialect = static::DIALECT;
        $paged = $dialect::applyLimit( $dialect::rewrite( $sql ), $limit, $offset );
        if ( $paged === null )
            return array();
        $result = $this->execute( $paged, 'arrayQuery', true );
        if ( $result === false )
            return false;
        $retArray = array();
        foreach ( $result['rows'] as $i => $row )
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
     * The id the last INSERT of this connection into $table made (the last table inserted into without $table),
     * read from its sequence; false when there is none.
     *
     * @param string|false $table
     * @param string|false $column
     * @return int|string|false
     */
    function lastSerialID( $table = false, $column = false )
    {
        if ( !$this->IsConnected )
            return false;
        if ( $table === false || $table === null || $table === '' )
            $table = $this->LastInsertTable;
        if ( $table === null || $table === '' )
            return false;
        $sequence = expDb2Schema::objectName( 'S_', strtolower( trim( (string)$table, '`" ' ) ) );
        $result = $this->Transport->run( "VALUES PREVIOUS VALUE FOR $sequence" );
        if ( $result === false || !isset( $result['rows'][0] ) )
            return false;
        $id = reset( $result['rows'][0] );
        if ( $id === null || $id === '' )
            return false;
        return ( is_numeric( $id ) && abs( (float)$id ) < PHP_INT_MAX ) ? (int)$id : $id;
    }

    /**
     * Moves every sequence of an auto_increment column past the highest id of its table (after rows were written with
     * their own ids, e.g. the data of a package or of share/db_data.dba).
     *
     * @return bool
     */
    function correctSequenceValues()
    {
        if ( !$this->IsConnected )
            return false;
        $result = $this->Transport->run( 'SELECT LOWER(TRIM(s.SEQNAME)) AS seq, CAST(s.REMARKS AS VARCHAR(254)) AS col FROM SYSCAT.SEQUENCES s'
                                       . " WHERE s.SEQSCHEMA = CURRENT SCHEMA AND s.SEQNAME LIKE 'S\\_%' ESCAPE '\\' AND s.REMARKS IS NOT NULL" );
        if ( $result === false )
            return false;
        $ok = true;
        foreach ( $result['rows'] as $row )
        {
            $parts = explode( '.', (string)$row['col'] );
            if ( count( $parts ) !== 2 )
                continue;
            list( $table, $column ) = $parts;
            $max = $this->Transport->run( 'SELECT MAX(' . expDb2Dialect::quoteIdentifierIfNeeded( $column ) . ') AS m FROM ' . expDb2Dialect::quoteIdentifierIfNeeded( $table ) );
            if ( $max === false )
                continue;
            $next = (int)( isset( $max['rows'][0]['m'] ) ? $max['rows'][0]['m'] : 0 ) + 1;
            if ( $this->Transport->run( 'ALTER SEQUENCE ' . $row['seq'] . ' RESTART WITH ' . $next ) === false )
                $ok = false;
        }
        return $ok;
    }

    // ------------------------------------------------------------------ transactions

    function beginQuery()
    {
        return $this->transaction( 'begin' );
    }

    function commitQuery()
    {
        return $this->transaction( 'commit' );
    }

    function rollbackQuery()
    {
        return $this->transaction( 'rollback' );
    }

    protected function transaction( $method )
    {
        if ( !$this->IsConnected || !$this->Transport )
            return false;
        if ( $this->Transport->$method() )
            return true;
        $this->setError();
        eZDebug::writeError( static::productName() . " $method failed: " . $this->ErrorMessage, __CLASS__ );
        return false;
    }

    /**
     * MySQL's LOCK TABLES: lock() opens a transaction (the rows the kernel then writes are locked by it) and
     * unlock() commits it. Db2's LOCK TABLE holds a table only until the end of the transaction it is part of.
     *
     * @param string|array $table
     */
    function lock( $table )
    {
        $this->begin();
    }

    function unlock()
    {
        $this->commit();
    }

    // ------------------------------------------------------------------ temporary tables

    /**
     * A name for a temporary table that stays the same for the same pattern and index (the definition of a global
     * temporary table is permanent; a random name would leave one behind per request).
     */
    function generateUniqueTempTableName( $pattern, $randomizeIndex = false, $server = self::SERVER_SLAVE )
    {
        $index = $randomizeIndex === false ? 0 : (int)$randomizeIndex;
        do
        {
            $name = str_replace( '%', (string)$index, (string)$pattern );
            $index++;
        }
        while ( isset( $this->TempTables[strtolower( $name )] ) );
        return $name;
    }

    /**
     * CREATE TEMPORARY TABLE: the global temporary table is made when the database has none of that name (over a
     * second connection, so it is committed at once), and emptied of this connection's rows when it has.
     */
    function createTempTable( $createTableQuery = '', $server = self::SERVER_SLAVE )
    {
        $dialect = static::DIALECT;
        $temporary = $dialect::temporaryTable( $createTableQuery );
        if ( $temporary === null )
            return $this->query( $createTableQuery );
        $name = $temporary['name'];
        // a definition of other columns (an older kernel's, or another program's) is made again
        $stale = $this->tableExists( $name ) && !$this->temporaryColumnsMatch( $name, $temporary['columns'] );
        if ( $stale || !$this->tableExists( $name ) )
        {
            $made = false;
            $helper = $this->Transport->sibling();
            if ( $helper->open( $this->connectionConfig() ) )
            {
                $schema = trim( (string)$this->Settings['Schema'] );
                if ( $schema !== '' )
                    $helper->run( 'SET SCHEMA ' . expDb2Dialect::quoteIdentifier( $schema ) );
                if ( $stale )
                    $helper->run( 'DROP TABLE ' . $dialect::quoteIdentifierIfNeeded( $name ) );
                $made = $helper->run( $temporary['sql'] ) !== false || ( !$stale && $this->tableExists( $name, true ) );
                $helper->close();
            }
            if ( !$made && !$this->query( $temporary['sql'] ) )
                return false;
            $this->KnownTables = null;
        }
        $ok = $this->query( 'DELETE FROM ' . $dialect::quoteIdentifierIfNeeded( $name ) );
        if ( $ok )
            $this->TempTables[strtolower( $name )] = $name;
        return $ok;
    }

    /**
     * Whether the global temporary table $name has the columns $columns (checked once per connection and table).
     *
     * @param string $name
     * @param string[] $columns lower-case names
     * @return bool
     */
    protected function temporaryColumnsMatch( $name, array $columns )
    {
        $key = strtolower( $name );
        if ( isset( $this->CheckedTempTables[$key] ) )
            return true;
        $result = $this->Transport->run( 'SELECT LOWER(COLNAME) AS name FROM SYSCAT.COLUMNS WHERE TABSCHEMA = CURRENT SCHEMA AND TABNAME = '
                                       . expDb2Dialect::stringLiteral( expDb2Dialect::fold( $name ) ) . ' ORDER BY COLNO' );
        if ( $result === false )
            return true;
        $have = array();
        foreach ( $result['rows'] as $row )
            $have[] = (string)$row['name'];
        if ( $have !== array_values( $columns ) )
            return false;
        $this->CheckedTempTables[$key] = true;
        return true;
    }

    /**
     * DROP TABLE of a temporary table: its rows of this connection are deleted, the shared definition stays.
     */
    function dropTempTable( $dropTableQuery = '', $server = self::SERVER_SLAVE )
    {
        $dialect = static::DIALECT;
        if ( preg_match( '/^\s*DROP\s+(?:TEMPORARY\s+)?TABLE\s+(?:IF\s+EXISTS\s+)?([`"]?[\w$]+[`"]?)\s*;?\s*$/i', (string)$dropTableQuery, $m ) )
        {
            $name = trim( $m[1], '`"' );
            if ( isset( $this->TempTables[strtolower( $name )] ) )
            {
                unset( $this->TempTables[strtolower( $name )] );
                return $this->query( 'DELETE FROM ' . $dialect::quoteIdentifierIfNeeded( $name ) );
            }
            // a global temporary table this connection did not fill (its CREATE failed): the shared definition stays
            if ( $this->isGlobalTemporaryTable( $name ) )
                return true;
        }
        return $this->query( $dropTableQuery );
    }

    /**
     * Whether $name is a created global temporary table of the schema.
     *
     * @param string $name
     * @return bool
     */
    public function isGlobalTemporaryTable( $name )
    {
        $result = $this->IsConnected ? $this->Transport->run( "SELECT 1 AS found FROM SYSCAT.TABLES WHERE TABSCHEMA = CURRENT SCHEMA AND TYPE = 'G' AND TABNAME = "
                                                              . expDb2Dialect::stringLiteral( expDb2Dialect::fold( $name ) ) ) : false;
        return $result !== false && count( $result['rows'] ) > 0;
    }

    function dropTempTableList( $tableList, $server = self::SERVER_SLAVE )
    {
        foreach ( $tableList as $name )
            $this->dropTempTable( 'DROP TABLE ' . $name, $server );
    }

    /**
     * Whether a table (or global temporary table) of that name exists (the list is read once per connection).
     *
     * @param string $name
     * @param bool $reload
     * @return bool
     */
    public function tableExists( $name, $reload = false )
    {
        if ( $this->KnownTables === null || $reload )
        {
            $this->KnownTables = array();
            $result = $this->IsConnected ? $this->Transport->run( static::tableNamesQuery( true ) ) : false;
            if ( $result !== false )
                foreach ( $result['rows'] as $row )
                    $this->KnownTables[strtolower( (string)$row['name'] )] = true;
        }
        return isset( $this->KnownTables[strtolower( (string)$name )] );
    }

    // ------------------------------------------------------------------ strings and expressions

    function escapeString( $str )
    {
        $dialect = static::DIALECT;
        return $dialect::escapeString( $str );
    }

    function subString( $string, $from, $len = null )
    {
        $dialect = static::DIALECT;
        return $dialect::subString( $string, $from, $len );
    }

    function concatString( $strings = array() )
    {
        $dialect = static::DIALECT;
        return $dialect::concatString( (array)$strings );
    }

    function md5( $str )
    {
        $dialect = static::DIALECT;
        return $dialect::md5( $str );
    }

    function bitAnd( $arg1, $arg2 )
    {
        $dialect = static::DIALECT;
        return $dialect::bitAnd( $arg1, $arg2 );
    }

    function bitOr( $arg1, $arg2 )
    {
        $dialect = static::DIALECT;
        return $dialect::bitOr( $arg1, $arg2 );
    }

    function generateSQLINStatement( $elements, $columnName = '', $not = false, $unique = true, $type = false )
    {
        $dialect = static::DIALECT;
        return $dialect::inList( is_array( $elements ) ? $elements : array( $elements ), $columnName, $not === true, $type,
                                 (int)$this->Settings['InListChunkSize'], (bool)$unique );
    }

    public function sqlHelperDialect()
    {
        return static::NAME;
    }

    public function sqlLiteral( $value )
    {
        $dialect = static::DIALECT;
        return $dialect::literal( $value );
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
     * The names of the tables of the current schema, in lower case; with $temporary the created global temporary
     * tables too.
     *
     * @param bool $temporary
     * @return string
     */
    public static function tableNamesQuery( $temporary = false )
    {
        return "SELECT LOWER(TABNAME) AS name FROM SYSCAT.TABLES WHERE TABSCHEMA = CURRENT SCHEMA AND TYPE IN ('T'" . ( $temporary ? ", 'G'" : '' ) . ') ORDER BY 1';
    }

    /**
     * The catalogue query of a relation type: names in lower case, the way the kernel writes them (Db2 stores an
     * unquoted name in upper case).
     *
     * @param int $relationType
     * @return string|null
     */
    public static function relationListQuery( $relationType )
    {
        switch ( $relationType )
        {
            case eZDBInterface::RELATION_TABLE:
                return static::tableNamesQuery();
            case eZDBInterface::RELATION_SEQUENCE:
                return "SELECT LOWER(SEQNAME) AS name FROM SYSCAT.SEQUENCES WHERE SEQSCHEMA = CURRENT SCHEMA AND SEQTYPE = 'S' ORDER BY 1";
            case eZDBInterface::RELATION_TRIGGER:
                return 'SELECT LOWER(TRIGNAME) AS name FROM SYSCAT.TRIGGERS WHERE TRIGSCHEMA = CURRENT SCHEMA ORDER BY 1';
            case eZDBInterface::RELATION_VIEW:
                return "SELECT LOWER(TABNAME) AS name FROM SYSCAT.TABLES WHERE TABSCHEMA = CURRENT SCHEMA AND TYPE = 'V' ORDER BY 1";
            case eZDBInterface::RELATION_INDEX:
                return "SELECT LOWER(INDNAME) AS name FROM SYSCAT.INDEXES WHERE INDSCHEMA = CURRENT SCHEMA AND UNIQUERULE <> 'P' ORDER BY 1";
        }
        return null;
    }

    function relationList( $relationType = eZDBInterface::RELATION_TABLE )
    {
        $sql = static::relationListQuery( $relationType );
        if ( $sql === null )
        {
            eZDebug::writeError( "Unsupported relation type '$relationType'", __METHOD__ );
            return false;
        }
        $result = $this->IsConnected ? $this->Transport->run( $sql ) : false;
        if ( $result === false )
            return false;
        $names = array();
        foreach ( $result['rows'] as $row )
            $names[] = $row['name'];
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
        foreach ( (array)$this->relationList( eZDBInterface::RELATION_SEQUENCE ) as $name )
            $tables[$name] = eZDBInterface::RELATION_SEQUENCE;
        return $tables;
    }

    function removeRelation( $relationName, $relationType )
    {
        $words = array( eZDBInterface::RELATION_TABLE => 'TABLE', eZDBInterface::RELATION_SEQUENCE => 'SEQUENCE',
                        eZDBInterface::RELATION_TRIGGER => 'TRIGGER', eZDBInterface::RELATION_VIEW => 'VIEW',
                        eZDBInterface::RELATION_INDEX => 'INDEX' );
        if ( !isset( $words[$relationType] ) )
        {
            eZDebug::writeError( "Unknown relation type '$relationType'", __METHOD__ );
            return false;
        }
        $dialect = static::DIALECT;
        $ok = $this->query( 'DROP ' . $words[$relationType] . ' ' . $dialect::quoteIdentifier( $relationName ) );
        if ( $ok && $relationType === eZDBInterface::RELATION_TABLE )
            $this->KnownTables = null;
        return $ok;
    }

    /**
     * The columns of a table from the catalogue: name, type, length, scale, string units, nullable, default,
     * identity.
     *
     * @param string $table
     * @return array|false
     */
    public function tableColumns( $table )
    {
        return $this->arrayQuery( 'SELECT LOWER(COLNAME) AS name, TYPENAME AS type, LENGTH AS length, SCALE AS scale, STRINGUNITSLENGTH AS units_length,'
                                . " TYPESTRINGUNITS AS units, NULLS AS nullable, DEFAULT AS default_value, IDENTITY AS is_identity, CAST(REMARKS AS VARCHAR(254)) AS remarks"
                                . ' FROM SYSCAT.COLUMNS WHERE TABSCHEMA = CURRENT SCHEMA AND TABNAME = ' . expDb2Dialect::stringLiteral( expDb2Dialect::fold( $table ) )
                                . ' ORDER BY COLNO' );
    }

    // ------------------------------------------------------------------ server

    function databaseServerVersion()
    {
        if ( !$this->ServerInfo )
            return false;
        $values = $this->ServerInfo['string'] !== '' ? array_map( 'intval', explode( '.', $this->ServerInfo['string'] ) ) : array();
        return array( 'string' => $this->ServerInfo['string'], 'values' => $values, 'name' => $this->ServerInfo['name'] );
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
        return in_array( strtolower( (string)$charset ), array( 'utf-8', 'utf8' ), true );
    }

    function availableDatabases()
    {
        return array( (string)$this->DB );
    }

    /**
     * Db2 creates a database with the CREATE DATABASE command of its instance (db2 CLP), not with SQL: not from here.
     */
    function createDatabase( $dbName )
    {
        $this->ErrorMessage = static::productName() . ' creates a database outside of a connection (db2: CREATE DATABASE ... USING CODESET UTF-8 PAGESIZE 32 K)';
        $this->ErrorNumber = -1;
        eZDebug::writeError( $this->ErrorMessage, __METHOD__ );
        return false;
    }

    function removeDatabase( $dbName )
    {
        $this->ErrorMessage = static::productName() . ' drops a database outside of a connection (db2: DROP DATABASE)';
        $this->ErrorNumber = -1;
        eZDebug::writeError( $this->ErrorMessage, __METHOD__ );
        return false;
    }

    /**
     * Db2 has no INSERT ... DEFAULT VALUES: the kernel inserts (id) VALUES (NULL) instead, which the table's trigger
     * numbers.
     */
    function supportsDefaultValuesInsertion()
    {
        return false;
    }

    function close()
    {
        $dialect = static::DIALECT;
        if ( $this->Transport && $this->IsConnected && $this->TempTables && (int)$this->TransactionCounter === 0 )
            foreach ( $this->TempTables as $name )
                $this->Transport->run( 'DELETE FROM ' . $dialect::quoteIdentifierIfNeeded( $name ) );
        if ( $this->Transport )
            $this->Transport->close();
        $this->TempTables = array();
        $this->IsConnected = false;
    }
}
