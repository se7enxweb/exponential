<?php
/**
 * File containing the expHANADB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The SAP HANA driver: SAP HANA 2.0 SPS 05 and later (the express edition included) and SAP HANA Cloud, over SAP's
 * HANA ODBC driver (HDBODBC of the SAP HANA client) through PDO_ODBC or PHP's odbc extension. site.ini:
 *
 * <code>
 * [DatabaseSettings]
 * DatabaseImplementation=hana
 * Server=hana.example.com
 * Port=39041                  (the SQL port of the tenant database: 3<instance>41, 30041 for instance 00)
 * Database=EXPONENTIAL        (the schema the site's tables are in)
 * </code>
 *
 * and the [HANASettings] of site.ini (ODBC driver, layer, tenant, encryption, retries). What the driver does that the
 * other drivers do not have to:
 *
 *  - Statements: the kernel's MySQL words become HANA's (expHANADialect::rewrite()): CONCAT() with more than two
 *    arguments, MD5(), UNIX_TIMESTAMP() and the bit operators of the language masks rewritten, a backslash in a LIKE
 *    pattern given ESCAPE '\', LIMIT m, n as LIMIT n OFFSET m, a SELECT without FROM given FROM DUMMY, an INSERT of
 *    several rows sent row by row.
 *  - Database and schema: Database is the schema (CURRENTSCHEMA of the connection). createDatabase() makes a schema.
 *  - New ids: an auto_increment column is a column with a sequence (S_<table>) and a BEFORE INSERT trigger
 *    (T_<table>_BI) made by the schema handler. The trigger numbers a row that has no id, NULL or 0 (MySQL's ways of
 *    asking for the next number) and moves the sequence past an id given by the statement. lastSerialID() reads the
 *    sequence's CURRVAL, which is this session's last number, never another session's.
 *  - Temporary tables: CREATE TEMPORARY TABLE becomes a global temporary table: its definition is permanent and
 *    shared, its rows belong to the session. The definition is made once, over a second connection so it is
 *    committed at once; the rows are deleted when the kernel drops the table.
 *  - Errors: deadlocks (133), lock wait timeouts (131), unique keys (301) and lost connections are told apart
 *    (expHANADialect::errorCategory()). A single statement outside a transaction that loses a deadlock is sent again;
 *    a lost connection outside a transaction is opened again.
 *
 * Not here: read replicas (HANA's active/active read-enabled secondaries are a client setting of the connection
 * string, ConnectionString).
 */
class expHANADB extends eZDBInterface
{
    /** databaseName() of this driver */
    const NAME = 'hana';

    /** @var array the defaults of [HANASettings] */
    const DEFAULT_SETTINGS = array(
        'Driver' => 'HDBODBC',
        'Layer' => 'auto',
        'TenantDatabase' => '',
        'Encrypt' => 'disabled',
        'ValidateCertificate' => 'enabled',
        'ConnectionString' => '',
        'DeadlockRetries' => 3,
        'DeadlockRetryWait' => 50,
        'ReconnectOnLoss' => 'enabled',
        'InListChunkSize' => 5000,
        'CreateSchema' => 'enabled',
        'BindLiterals' => 'enabled',
        'TableType' => 'column',
    );

    /** @var array the NCLOB column names per schema, shared by the connections of a process: key => string[] */
    protected static $LobColumnCache = array();

    /** @var expHANATransport|null */
    public $Transport = null;

    /** @var array the effective [HANASettings] */
    public $Settings = array();

    /** @var array|null what the server said about itself at connect */
    public $ServerInfo = null;

    /** @var array global temporary tables this connection has written rows to: lower-case name => name */
    public $TempTables = array();

    /** @var array|null lower-case names of the tables, read once per connection for the temporary tables */
    public $KnownTables = null;

    /** @var string[]|null the names of the columns that are an NCLOB in every table of the schema that has them */
    public $LobColumns = null;

    /** @var string|null the table of the last INSERT (lastSerialID() without a table) */
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
     * Connects: the [DatabaseSettings] parameters of eZDB::instance(), and optionally 'hana' => array( <[HANASettings]
     * names> => value ) over site.ini's [HANASettings], and 'hana_transport' => an expHANATransport to use instead of
     * ODBC.
     *
     * @param array $parameters
     * @throws eZDBNoConnectionException when no connection can be opened
     */
    public function __construct( $parameters )
    {
        parent::__construct( $parameters );
        $this->Settings = self::settings( isset( $parameters['hana'] ) && is_array( $parameters['hana'] ) ? $parameters['hana'] : array() );
        if ( isset( $parameters['hana_transport'] ) && $parameters['hana_transport'] instanceof expHANATransport )
            $this->Transport = $parameters['hana_transport'];
        else
        {
            $layer = expHANATransport::availableLayer( (string)$this->Settings['Layer'] );
            if ( $layer === null )
            {
                $text = 'SAP HANA needs PDO_ODBC or the odbc extension of PHP and the SAP HANA ODBC driver, the DB handler will not be initialized.';
                if ( function_exists( 'eZAppendWarningItem' ) )
                    eZAppendWarningItem( array( 'error' => array( 'type' => 'ezdb', 'number' => eZDBInterface::ERROR_MISSING_EXTENSION ), 'text' => $text ) );
                eZDebug::writeWarning( $text, __CLASS__ );
                $this->IsConnected = false;
                return;
            }
            $this->Transport = new expHANAODBCTransport( $layer );
        }
        eZDebug::createAccumulatorGroup( 'hana_total', 'SAP HANA Total' );

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
     * The effective [HANASettings]: the defaults, site.ini, then $overrides.
     *
     * @param array $overrides
     * @return array
     */
    public static function settings( array $overrides = array() )
    {
        $settings = self::DEFAULT_SETTINGS;
        if ( class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance();
            if ( $ini->hasGroup( 'HANASettings' ) )
                foreach ( $ini->group( 'HANASettings' ) as $name => $value )
                    if ( array_key_exists( $name, $settings ) && $value !== '' && $value !== null )
                        $settings[$name] = $value;
        }
        foreach ( $overrides as $name => $value )
            $settings[$name] = $value;
        return $settings;
    }

    /**
     * The connection settings for the transport; $withSchema false connects without a current schema (to make it).
     *
     * @param bool $withSchema
     * @return array
     */
    public function connectionConfig( $withSchema = true )
    {
        return array( 'host' => (string)$this->Server, 'port' => $this->Port ? (int)$this->Port : 0,
                      'schema' => $withSchema ? (string)$this->DB : '', 'user' => (string)$this->User, 'password' => (string)$this->Password,
                      'driver' => (string)$this->Settings['Driver'], 'tenant' => (string)$this->Settings['TenantDatabase'],
                      'encrypt' => expHANADialect::flag( $this->Settings['Encrypt'] ),
                      'validate_certificate' => expHANADialect::flag( $this->Settings['ValidateCertificate'] ),
                      'extra' => (string)$this->Settings['ConnectionString'], 'persistent' => (bool)$this->UsePersistentConnection );
    }

    /**
     * Opens the connection and reads what the server is.
     *
     * @return bool
     */
    protected function openConnection()
    {
        eZDebug::accumulatorStart( 'hana_connection', 'hana_total', 'Database connection' );
        $ok = $this->Transport->open( $this->connectionConfig() );
        // a schema that does not exist yet (error 362): made, when CreateSchema allows it, and the connection opened again
        $error = $ok ? null : $this->Transport->error();
        if ( !$ok && (string)$this->DB !== '' && is_array( $error ) && (int)$error['code'] === 362 && expHANADialect::flag( $this->Settings['CreateSchema'] ) )
        {
            if ( $this->Transport->open( $this->connectionConfig( false ) ) )
            {
                $made = $this->Transport->run( 'CREATE SCHEMA ' . expHANADialect::quoteIdentifier( $this->DB ) ) !== false;
                $this->Transport->close();
                if ( $made )
                    $ok = $this->Transport->open( $this->connectionConfig() );
            }
        }
        eZDebug::accumulatorStop( 'hana_connection' );
        $this->IsConnected = $ok;
        if ( !$ok )
            return false;
        $this->DBConnection = $this->Transport;
        $this->DBWriteConnection = $this->Transport;
        $this->TempTables = array();
        $this->KnownTables = null;
        if ( $this->ServerInfo === null )
        {
            $result = $this->Transport->run( 'SELECT VERSION AS version FROM SYS.M_DATABASE' );
            $this->ServerInfo = expHANADialect::describeVersion( $result !== false && isset( $result['rows'][0]['version'] ) ? $result['rows'][0]['version'] : '' );
            if ( !$this->ServerInfo['supported'] )
                eZDebug::writeWarning( $this->ServerInfo['name'] . ' (' . $this->ServerInfo['string'] . ') is not supported: SAP HANA 2.0 SPS 05 or later is needed', __CLASS__ );
        }
        return true;
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
        return $this->Transport && $this->Transport->isOpen() && $this->Transport->run( 'SELECT 1 AS ok FROM DUMMY' ) !== false;
    }

    function databaseName()
    {
        return self::NAME;
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
     * A statement as it goes to the server (expHANADialect::rewrite()).
     *
     * @param string $sql
     * @return string
     */
    public function prepareStatement( $sql )
    {
        // the NCLOB columns matter where a statement compares or sorts: not in the schema's statements, nor in a
        // plain INSERT ... VALUES
        $kind = expHANADialect::statementKind( $sql );
        $reads = in_array( $kind, array( 'SELECT', 'WITH', 'UPDATE', 'DELETE', 'MERGE' ), true )
              || ( in_array( $kind, array( 'INSERT', 'UPSERT' ), true ) && stripos( (string)$sql, 'select' ) !== false );
        return expHANADialect::rewrite( $sql, $reads ? $this->lobColumns() : array() );
    }

    /**
     * The names of the columns that are an NCLOB in every table of the schema that has them (lower case), read once
     * per process and schema, and again after a change of the schema.
     *
     * @return string[]
     */
    public function lobColumns()
    {
        if ( $this->LobColumns !== null )
            return $this->LobColumns;
        $key = $this->lobCacheKey();
        if ( isset( self::$LobColumnCache[$key] ) )
            return $this->LobColumns = self::$LobColumnCache[$key];
        if ( !$this->IsConnected || !$this->Transport )
            return array();
        $result = $this->Transport->run( "SELECT LOWER(COLUMN_NAME) AS name, MIN(CASE WHEN DATA_TYPE_NAME IN ('NCLOB', 'CLOB', 'BLOB', 'TEXT') THEN 1 ELSE 0 END) AS lob"
                                       . ' FROM SYS.TABLE_COLUMNS WHERE SCHEMA_NAME = CURRENT_SCHEMA GROUP BY LOWER(COLUMN_NAME)' );
        if ( $result === false )
            return array();
        $names = array();
        foreach ( $result['rows'] as $row )
            if ( (string)$row['lob'] === '1' )
                $names[] = $row['name'];
        self::$LobColumnCache[$key] = $names;
        return $this->LobColumns = $names;
    }

    /** @return string */
    protected function lobCacheKey()
    {
        return strtolower( (string)$this->Server . ':' . (int)$this->Port . '/' . (string)$this->DB );
    }

    /**
     * Forgets the NCLOB columns (after a change of the schema).
     */
    public function forgetLobColumns()
    {
        $this->LobColumns = null;
        unset( self::$LobColumnCache[$this->lobCacheKey()] );
    }

    /**
     * Runs a statement; sends it again once after a lost connection and up to DeadlockRetries times after a deadlock,
     * both only outside a transaction. An INSERT of several rows is sent row by row.
     *
     * @param string $sql
     * @param string $caller for the messages
     * @param bool $rewritten whether $sql is in HANA's words already (arrayQuery() pages the rewritten statement)
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
        $prepared = $rewritten ? (string)$sql : $this->prepareStatement( $sql );
        // DROP ... IF EXISTS, CREATE ... IF NOT EXISTS: the condition asked of the catalogue
        $conditional = $rewritten ? null : expHANADialect::conditionalDDL( $prepared );
        if ( $conditional !== null )
        {
            $found = $this->Transport->run( expHANADialect::existsQuery( $conditional['type'], $conditional['name'] ) );
            if ( $found !== false && ( count( $found['rows'] ) > 0 ) === $conditional['exists'] )
                return array( 'rows' => array(), 'affected' => 0 );
            $prepared = $conditional['sql'];
        }
        $statements = $rewritten ? null : expHANADialect::splitMultiRowInsert( $prepared );
        if ( $statements === null )
            $statements = array( $prepared );
        eZDebug::accumulatorStart( 'hana_query', 'hana_total', 'SAP HANA queries' );
        if ( $this->OutputSQL )
            $this->startTimer();

        $error = null;
        $result = false;
        $affected = 0;
        foreach ( $statements as $statement )
        {
            $result = $this->send( $statement, $error );
            if ( $result === false )
                break;
            $affected += (int)$result['affected'];
        }
        if ( $result !== false && count( $statements ) > 1 )
            $result = array( 'rows' => array(), 'affected' => $affected );

        if ( $this->OutputSQL )
        {
            $this->endTimer();
            if ( $this->timeTaken() > $this->SlowSQLTimeout )
                $this->reportQuery( __CLASS__, $sql, false, $this->timeTaken() );
        }
        eZDebug::accumulatorStop( 'hana_query' );

        if ( $result === false )
        {
            $this->takeError( $error );
            eZDebug::writeError( self::failedQueryMessage( $this->ErrorMessage, $this->LastStatement ), __CLASS__ . '::' . $caller );
            if ( (int)$this->TransactionCounter > 0 && in_array( $this->ErrorCategory, array( 'deadlock', 'connection' ), true ) )
                $this->invalidateTransaction();
            if ( $this->errorHandling == eZDB::ERROR_HANDLING_EXCEPTIONS )
                throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
            $this->reportError();
            return false;
        }
        $insertTable = expHANADialect::insertTable( $prepared );
        if ( $insertTable !== null )
            $this->LastInsertTable = $insertTable;
        if ( expHANADialect::isSchemaChange( $prepared ) )
        {
            $this->KnownTables = null;
            $this->forgetLobColumns();
            // a dropped table takes the sequence of its auto_increment column along (its trigger goes with the
            // table), so the table can be made again (installers drop and make a package's tables)
            $dropped = expHANADialect::droppedTable( $prepared );
            if ( $dropped !== null && class_exists( 'expHANASchema' ) )
            {
                $sequence = expHANASchema::objectName( 'S_', $dropped );
                $found = $this->Transport->run( expHANADialect::existsQuery( 'SEQUENCE', $sequence ) );
                if ( $found !== false && $found['rows'] )
                    $this->Transport->run( 'DROP SEQUENCE ' . expHANADialect::quoteIdentifierIfNeeded( $sequence ) );
            }
        }
        eZDBQueryCache::noteWrite( $this, $sql );
        return $result;
    }

    /**
     * One statement to the server, with the retries; $error takes the transport's error of a failure.
     *
     * @param string $prepared
     * @param array|null $error
     * @return array|false
     */
    protected function send( $prepared, &$error )
    {
        $reconnected = false;
        $truncated = false;
        $retries = 0;
        $maxRetries = max( 0, (int)$this->Settings['DeadlockRetries'] );
        while ( true )
        {
            $this->LastStatement = $prepared;
            $this->StatementCount++;
            $bound = $this->bind( $prepared );
            $result = $bound === null ? $this->Transport->run( $prepared ) : $this->Transport->run( $bound[0], $bound[1] );
            if ( $result !== false )
                return $result;
            $error = $this->Transport->error();
            // a global temporary table is dropped only when no session has rows in it: this session's go first
            $dropped = $truncated ? null : expHANADialect::droppedTable( $prepared );
            if ( $dropped !== null && stripos( (string)$error['message'], 'must be truncated' ) !== false )
            {
                $truncated = true;
                $this->Transport->run( 'TRUNCATE TABLE ' . expHANADialect::quoteIdentifierIfNeeded( $dropped ) );
                continue;
            }
            $category = expHANADialect::errorCategory( $error['code'], $error['message'], $error['state'] );
            $outsideTransaction = (int)$this->TransactionCounter === 0;
            if ( $category === 'connection' && $outsideTransaction && !$reconnected && !$this->TempTables
                 && expHANADialect::flag( $this->Settings['ReconnectOnLoss'] ) )
            {
                $reconnected = true;
                if ( $this->reconnect() )
                    continue;
                $error = $this->Transport->error() ?: $error;
                return false;
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
            return false;
        }
    }

    /**
     * A statement with its literal values as parameters (expHANADialect::parameterize(): HANA then compiles it once
     * for all values), else with its long literals as parameters, and the literals left in its text that hold more
     * than ASCII as bytes (expHANADialect::unicodeLiterals()); null to send it as it is. The odbc extension reads a
     * value in single quotes as the name of a file to send: such a statement goes with its literals.
     *
     * @param string $prepared
     * @return array|null array( sql, values )
     */
    protected function bind( $prepared )
    {
        $bound = expHANADialect::flag( $this->Settings['BindLiterals'] ) ? expHANADialect::parameterize( $prepared ) : null;
        if ( $bound !== null && $this->Transport->name() === 'odbc' )
            foreach ( $bound[1] as $value )
                if ( strlen( $value ) > 1 && $value[0] === "'" && substr( $value, -1 ) === "'" )
                {
                    $bound = null;
                    break;
                }
        if ( $bound === null )
            $bound = expHANADialect::parameterizeLongLiterals( $prepared );
        // the literals left in the text: Unicode as bytes (the driver reads a statement's text in a single-byte code page)
        $sql = expHANADialect::unicodeLiterals( $bound !== null ? $bound[0] : $prepared );
        if ( $bound !== null )
            return array( $sql, $bound[1] );
        return $sql !== $prepared ? array( $sql, array() ) : null;
    }

    /**
     * Takes the transport's error into ErrorNumber (HANA's error code, or -1), ErrorMessage (without any password)
     * and ErrorCategory.
     *
     * @param array|null $error
     */
    protected function takeError( $error )
    {
        if ( !is_array( $error ) )
            $error = array( 'code' => null, 'state' => null, 'message' => 'Unknown SAP HANA error' );
        $this->ErrorCategory = expHANADialect::errorCategory( $error['code'], $error['message'], $error['state'] );
        $this->ErrorNumber = $error['code'] !== null ? (int)$error['code'] : -1;
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
        $prepared = $this->prepareStatement( $sql );
        // SELECT DISTINCT over an NCLOB, which HANA refuses: sent without DISTINCT, the duplicates dropped here
        $distinct = expHANADialect::statementKind( $prepared ) === 'SELECT' ? expHANADialect::withoutLobDistinct( $prepared, $this->lobColumns() ) : null;
        if ( $distinct !== null && $limit === 0 )
            return array();
        $paged = $distinct !== null ? $distinct : expHANADialect::applyLimit( $prepared, $limit, $offset );
        if ( $paged === null )
            return array();
        $result = $this->execute( $paged, 'arrayQuery', true );
        if ( $result === false )
            return false;
        if ( $distinct !== null )
        {
            $seen = array();
            $rows = array();
            foreach ( $result['rows'] as $row )
            {
                $key = serialize( $row );
                if ( !isset( $seen[$key] ) )
                {
                    $seen[$key] = true;
                    $rows[] = $row;
                }
            }
            $result['rows'] = array_slice( $rows, $offset, $limit === false ? null : $limit );
        }
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
     * The id the last INSERT of this session into $table made (the last INSERT's table without $table): the CURRVAL
     * of the table's sequence, which the trigger moved; false when there is none.
     *
     * @param string|false $table
     * @param string|false $column
     * @return int|string|false
     */
    function lastSerialID( $table = false, $column = false )
    {
        if ( !$this->IsConnected )
            return false;
        $table = ( $table !== false && $table !== null && $table !== '' ) ? trim( (string)$table, '`" ' ) : $this->LastInsertTable;
        if ( $table === null || $table === '' )
            return false;
        $sequence = expHANADialect::quoteIdentifierIfNeeded( expHANASchema::objectName( 'S_', strtolower( $table ) ) );
        $result = $this->Transport->run( "SELECT $sequence.CURRVAL AS id FROM DUMMY" );
        if ( $result === false || !isset( $result['rows'][0]['id'] ) || $result['rows'][0]['id'] === '' )
            return false;
        $id = $result['rows'][0]['id'];
        return ( is_numeric( $id ) && abs( (float)$id ) < PHP_INT_MAX ) ? (int)$id : $id;
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
        eZDebug::writeError( "SAP HANA $method failed: " . $this->ErrorMessage, __CLASS__ );
        return false;
    }

    /**
     * MySQL's LOCK TABLES: a transaction that takes an exclusive lock of each table (LOCK TABLE ... IN EXCLUSIVE
     * MODE); unlock() commits it.
     *
     * @param string|array $table
     */
    function lock( $table )
    {
        $this->begin();
        foreach ( is_array( $table ) ? $table : array( $table ) as $entry )
        {
            $name = is_array( $entry ) ? ( isset( $entry['table'] ) ? $entry['table'] : reset( $entry ) ) : $entry;
            $name = trim( preg_replace( '/\s+(READ|WRITE)$/i', '', trim( (string)$name ) ) );
            if ( $name !== '' )
                $this->query( 'LOCK TABLE ' . expHANADialect::quoteIdentifierIfNeeded( $name ) . ' IN EXCLUSIVE MODE' );
        }
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
     * CREATE TEMPORARY TABLE: the global temporary table is made when the schema has none of that name (over a second
     * connection, so it is committed at once), and emptied of this session's rows when it has.
     */
    function createTempTable( $createTableQuery = '', $server = self::SERVER_SLAVE )
    {
        $temporary = expHANADialect::temporaryTable( $createTableQuery );
        if ( $temporary === null )
            return $this->query( $createTableQuery );
        $name = $temporary['name'];
        if ( !$this->tableExists( $name ) )
        {
            $made = false;
            $helper = $this->Transport->sibling();
            if ( $helper->open( $this->connectionConfig() ) )
            {
                $made = $helper->run( $temporary['sql'] ) !== false || $this->tableExists( $name, true );
                $helper->close();
            }
            if ( !$made && !$this->query( $temporary['sql'] ) )
                return false;
            $this->KnownTables = null;
        }
        $ok = $this->query( 'DELETE FROM ' . expHANADialect::quoteIdentifierIfNeeded( $name ) );
        if ( $ok )
            $this->TempTables[strtolower( $name )] = $name;
        return $ok;
    }

    /**
     * DROP TABLE of a temporary table: its rows of this session are deleted, the shared definition stays.
     */
    function dropTempTable( $dropTableQuery = '', $server = self::SERVER_SLAVE )
    {
        if ( preg_match( '/^\s*DROP\s+(?:TEMPORARY\s+)?TABLE\s+(?:IF\s+EXISTS\s+)?([`"]?[\w$#]+[`"]?)\s*;?\s*$/i', (string)$dropTableQuery, $m ) )
        {
            $name = trim( $m[1], '`"' );
            if ( isset( $this->TempTables[strtolower( $name )] ) )
            {
                unset( $this->TempTables[strtolower( $name )] );
                return $this->query( 'DELETE FROM ' . expHANADialect::quoteIdentifierIfNeeded( $name ) );
            }
        }
        return $this->query( $dropTableQuery );
    }

    function dropTempTableList( $tableList, $server = self::SERVER_SLAVE )
    {
        foreach ( $tableList as $name )
            $this->dropTempTable( 'DROP TABLE ' . $name, $server );
    }

    /**
     * Whether a table of that name exists, a global temporary one included (the list is read once per connection).
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
            $result = $this->IsConnected ? $this->Transport->run( 'SELECT LOWER(TABLE_NAME) AS name FROM SYS.TABLES WHERE SCHEMA_NAME = CURRENT_SCHEMA' ) : false;
            foreach ( $result !== false ? $result['rows'] : array() as $row )
                $this->KnownTables[$row['name']] = true;
        }
        return isset( $this->KnownTables[strtolower( (string)$name )] );
    }

    // ------------------------------------------------------------------ strings and expressions

    function escapeString( $str )
    {
        return expHANADialect::escapeString( $str );
    }

    function subString( $string, $from, $len = null )
    {
        return expHANADialect::subString( $string, $from, $len );
    }

    function concatString( $strings = array() )
    {
        return expHANADialect::concatString( (array)$strings );
    }

    function md5( $str )
    {
        return expHANADialect::md5( $str );
    }

    function bitAnd( $arg1, $arg2 )
    {
        return expHANADialect::bitAnd( $arg1, $arg2 );
    }

    function bitOr( $arg1, $arg2 )
    {
        return expHANADialect::bitOr( $arg1, $arg2 );
    }

    function generateSQLINStatement( $elements, $columnName = '', $not = false, $unique = true, $type = false )
    {
        return expHANADialect::inList( is_array( $elements ) ? $elements : array( $elements ), $columnName, $not === true, $type,
                                       (int)$this->Settings['InListChunkSize'], (bool)$unique );
    }

    public function sqlHelperDialect()
    {
        return 'hana';
    }

    public function sqlLiteral( $value )
    {
        return expHANADialect::literal( $value );
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
     * The catalogue query of a relation type in the current schema: names in lower case, the way the kernel writes
     * them (HANA stores an unquoted name in upper case). Global temporary tables are not listed as tables.
     *
     * @param int $relationType
     * @return string|null
     */
    public static function relationListQuery( $relationType )
    {
        switch ( $relationType )
        {
            case eZDBInterface::RELATION_TABLE:
                return "SELECT LOWER(TABLE_NAME) AS name FROM SYS.TABLES WHERE SCHEMA_NAME = CURRENT_SCHEMA AND IS_TEMPORARY = 'FALSE' ORDER BY 1";
            case eZDBInterface::RELATION_SEQUENCE:
                return 'SELECT LOWER(SEQUENCE_NAME) AS name FROM SYS.SEQUENCES WHERE SCHEMA_NAME = CURRENT_SCHEMA ORDER BY 1';
            case eZDBInterface::RELATION_TRIGGER:
                return 'SELECT LOWER(TRIGGER_NAME) AS name FROM SYS.TRIGGERS WHERE SCHEMA_NAME = CURRENT_SCHEMA ORDER BY 1';
            case eZDBInterface::RELATION_VIEW:
                return 'SELECT LOWER(VIEW_NAME) AS name FROM SYS.VIEWS WHERE SCHEMA_NAME = CURRENT_SCHEMA ORDER BY 1';
            case eZDBInterface::RELATION_INDEX:
                return 'SELECT LOWER(INDEX_NAME) AS name FROM SYS.INDEXES WHERE SCHEMA_NAME = CURRENT_SCHEMA AND CONSTRAINT IS NULL ORDER BY 1';
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
        $ok = $this->query( 'DROP ' . $words[$relationType] . ' ' . expHANADialect::quoteIdentifier( $relationName ) );
        if ( $ok && $relationType === eZDBInterface::RELATION_TABLE )
            $this->KnownTables = null;
        return $ok;
    }

    /**
     * The columns of a table from the catalogue: name, type, length, scale, nullable, default, comment.
     *
     * @param string $table
     * @return array|false
     */
    public function tableColumns( $table )
    {
        return $this->arrayQuery( 'SELECT LOWER(COLUMN_NAME) AS name, DATA_TYPE_NAME AS type, LENGTH AS length, SCALE AS scale,'
                                . " IS_NULLABLE AS nullable, DEFAULT_VALUE AS default_value, COMMENTS AS comments FROM SYS.TABLE_COLUMNS"
                                . ' WHERE SCHEMA_NAME = CURRENT_SCHEMA AND TABLE_NAME = ' . expHANADialect::stringLiteral( expHANADialect::upper( $table ) )
                                . ' ORDER BY POSITION' );
    }

    // ------------------------------------------------------------------ server

    function databaseServerVersion()
    {
        if ( !$this->ServerInfo )
            return false;
        $values = $this->ServerInfo['string'] !== '' ? array_map( 'intval', array_slice( explode( '.', $this->ServerInfo['string'] ), 0, 3 ) ) : array();
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

    /**
     * The schemas the user may use (a site's "database" is a schema of the tenant database).
     */
    function availableDatabases()
    {
        $result = $this->IsConnected ? $this->Transport->run( "SELECT SCHEMA_NAME AS name FROM SYS.SCHEMAS WHERE HAS_PRIVILEGES = 'TRUE' ORDER BY 1" ) : false;
        if ( $result === false )
            return array( (string)$this->DB );
        $names = array();
        foreach ( $result['rows'] as $row )
            $names[] = $row['name'];
        return $names;
    }

    /**
     * A schema of that name (a site's "database"), when there is none.
     */
    function createDatabase( $dbName )
    {
        $name = expHANADialect::upper( (string)$dbName );
        $found = $this->Transport->run( 'SELECT 1 AS found FROM SYS.SCHEMAS WHERE SCHEMA_NAME = ' . expHANADialect::stringLiteral( $name ) );
        if ( $found !== false && $found['rows'] )
            return true;
        return $this->query( 'CREATE SCHEMA ' . expHANADialect::quoteIdentifier( $dbName ) );
    }

    function removeDatabase( $dbName )
    {
        return $this->query( 'DROP SCHEMA ' . expHANADialect::quoteIdentifier( $dbName ) . ' CASCADE' );
    }

    function supportsDefaultValuesInsertion()
    {
        return false;
    }

    function close()
    {
        if ( $this->Transport && $this->IsConnected && $this->TempTables && (int)$this->TransactionCounter === 0 )
            foreach ( $this->TempTables as $name )
                $this->Transport->run( 'DELETE FROM ' . expHANADialect::quoteIdentifierIfNeeded( $name ) );
        if ( $this->Transport )
            $this->Transport->close();
        $this->TempTables = array();
        $this->IsConnected = false;
    }
}
