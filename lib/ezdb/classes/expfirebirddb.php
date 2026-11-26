<?php
/**
 * File containing the expFirebirdDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Firebird driver: Firebird 4.0 and 5.0 over pdo_firebird of PHP 8.4 or later. site.ini:
 *
 * <code>
 * [DatabaseSettings]
 * DatabaseImplementation=firebird
 * Server=db.example.com
 * Port=3050
 * Database=exponential        (an alias of the server's databases.conf, or the path of the .fdb file on the server)
 * </code>
 *
 * and the [FirebirdSettings] of site.ini (role, charset, retries). What the driver does that the other drivers do
 * not have to:
 *
 *  - Statements: the kernel's MySQL words become Firebird's (expFirebirdDialect::rewrite()): names that are
 *    reserved words quoted, LENGTH(), CONCAT(), NOW() ... rewritten, a backslash in a LIKE pattern given ESCAPE '\',
 *    FOR UPDATE given WITH LOCK, a SELECT without FROM given FROM RDB$DATABASE.
 *  - Paging: arrayQuery()'s limit and offset become OFFSET n ROWS FETCH NEXT m ROWS ONLY on the server.
 *  - New ids: an auto_increment column is a column with a sequence (S_<table>) and a BEFORE INSERT trigger
 *    (T_<table>_BI) made by the schema handler. The trigger numbers a row that has no id, NULL or 0 (MySQL's ways of
 *    asking for the next number), moves the sequence past an id given by the statement, and keeps the id in a
 *    context variable of the connection, where lastSerialID() reads it: the id of this connection's last insert
 *    into that table, never another connection's.
 *  - Temporary tables: CREATE TEMPORARY TABLE becomes a global temporary table (ON COMMIT PRESERVE ROWS): its
 *    definition is permanent and shared, its rows belong to the connection. The definition is made once, over a
 *    second connection so it is committed at once; the rows are deleted when the kernel drops the table.
 *  - Errors: update conflicts and deadlocks, lock conflicts, duplicate keys and lost connections are told apart
 *    (expFirebirdDialect::errorCategory()). A single statement outside a transaction that loses a deadlock is sent
 *    again; a lost connection outside a transaction is opened again.
 *
 * Not here: read replicas, LOCK TABLES (lock() opens a transaction), creating a database (createDatabase(): Firebird
 * makes a database with CREATE DATABASE outside any connection, e.g. isql, or the container's FIREBIRD_DATABASE).
 */
class expFirebirdDB extends eZDBInterface
{
    /** databaseName() of this driver */
    const NAME = 'firebird';

    /** The context variable namespace and prefix of the ids the triggers keep */
    const ID_CONTEXT = 'EXP_ID';

    /** @var array the defaults of [FirebirdSettings] */
    const DEFAULT_SETTINGS = array(
        'Role' => '',
        'Charset' => 'UTF8',
        'DeadlockRetries' => 3,
        'DeadlockRetryWait' => 50,
        'ReconnectOnLoss' => 'enabled',
        'InListChunkSize' => 1500,
    );

    /** @var expFirebirdTransport|null */
    public $Transport = null;

    /** @var array the effective [FirebirdSettings] */
    public $Settings = array();

    /** @var array|null what the server said about itself at connect */
    public $ServerInfo = null;

    /** @var array global temporary tables this connection has written rows to: lower-case name => name */
    public $TempTables = array();

    /** @var array|null lower-case names of the tables, read once per connection for the temporary tables */
    public $KnownTables = null;

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
     * Connects: the [DatabaseSettings] parameters of eZDB::instance(), and optionally 'firebird' => array(
     * <[FirebirdSettings] names> => value ) over site.ini's [FirebirdSettings], and 'firebird_transport' => an
     * expFirebirdTransport to use instead of pdo_firebird.
     *
     * @param array $parameters
     * @throws eZDBNoConnectionException when no connection can be opened
     */
    public function __construct( $parameters )
    {
        parent::__construct( $parameters );
        $this->Settings = self::settings( isset( $parameters['firebird'] ) && is_array( $parameters['firebird'] ) ? $parameters['firebird'] : array() );
        if ( isset( $parameters['firebird_transport'] ) && $parameters['firebird_transport'] instanceof expFirebirdTransport )
            $this->Transport = $parameters['firebird_transport'];
        else
        {
            if ( expFirebirdTransport::availableLayer() === null )
            {
                $text = 'Firebird needs the pdo_firebird extension of PHP 8.4 or later, the DB handler will not be initialized.';
                if ( function_exists( 'eZAppendWarningItem' ) )
                    eZAppendWarningItem( array( 'error' => array( 'type' => 'ezdb', 'number' => eZDBInterface::ERROR_MISSING_EXTENSION ), 'text' => $text ) );
                eZDebug::writeWarning( $text, __CLASS__ );
                $this->IsConnected = false;
                return;
            }
            $this->Transport = new expFirebirdPDOTransport();
        }
        eZDebug::createAccumulatorGroup( 'firebird_total', 'Firebird Total' );

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
     * The effective [FirebirdSettings]: the defaults, site.ini, then $overrides.
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
            if ( $ini->hasGroup( 'FirebirdSettings' ) )
                foreach ( $ini->group( 'FirebirdSettings' ) as $name => $value )
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
                      'user' => (string)$this->User, 'password' => (string)$this->Password, 'charset' => (string)$this->Settings['Charset'],
                      'role' => (string)$this->Settings['Role'], 'persistent' => (bool)$this->UsePersistentConnection );
    }

    /**
     * Opens the connection and reads what the server is.
     *
     * @return bool
     */
    protected function openConnection()
    {
        eZDebug::accumulatorStart( 'firebird_connection', 'firebird_total', 'Database connection' );
        $ok = $this->Transport->open( $this->connectionConfig() );
        eZDebug::accumulatorStop( 'firebird_connection' );
        $this->IsConnected = $ok;
        if ( !$ok )
            return false;
        $this->DBConnection = $this->Transport;
        $this->DBWriteConnection = $this->Transport;
        $this->TempTables = array();
        $this->KnownTables = null;
        if ( $this->ServerInfo === null )
        {
            $this->ServerInfo = expFirebirdDialect::describeVersion( $this->Transport->serverVersion() );
            if ( !$this->ServerInfo['supported'] )
                eZDebug::writeWarning( $this->ServerInfo['name'] . ' (' . $this->ServerInfo['string'] . ') is not supported: Firebird 4.0 or later is needed', __CLASS__ );
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
        return $this->Transport && $this->Transport->isOpen() && $this->Transport->run( 'SELECT 1 AS ok FROM RDB$DATABASE' ) !== false;
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
     * A statement as it goes to the server (expFirebirdDialect::rewrite()).
     *
     * @param string $sql
     * @return string
     */
    public function prepareStatement( $sql )
    {
        return expFirebirdDialect::rewrite( $sql );
    }

    /**
     * Runs a statement; sends it again once after a lost connection and up to DeadlockRetries times after a
     * deadlock, both only outside a transaction.
     *
     * @param string $sql
     * @param string $caller for the messages
     * @param bool $rewritten whether $sql is in Firebird's words already (arrayQuery() pages the rewritten statement)
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
        $conditional = $rewritten ? null : expFirebirdDialect::conditionalDDL( $prepared );
        if ( $conditional !== null )
        {
            $found = $this->Transport->run( expFirebirdDialect::existsQuery( $conditional['type'], $conditional['name'] ) );
            if ( $found !== false && ( count( $found['rows'] ) > 0 ) === $conditional['exists'] )
                return array( 'rows' => array(), 'affected' => 0 );
            $prepared = $conditional['sql'];
        }
        eZDebug::accumulatorStart( 'firebird_query', 'firebird_total', 'Firebird queries' );
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
            $bound = expFirebirdDialect::parameterizeLongLiterals( $prepared );
            $result = $bound === null ? $this->Transport->run( $prepared ) : $this->Transport->run( $bound[0], $bound[1] );
            if ( $result !== false )
                break;
            $error = $this->Transport->error();
            $category = expFirebirdDialect::errorCategory( $error['sqlcode'], $error['message'], $error['gds'] );
            $outsideTransaction = (int)$this->TransactionCounter === 0;
            if ( $category === 'connection' && $outsideTransaction && !$reconnected && !$this->TempTables
                 && expFirebirdDialect::flag( $this->Settings['ReconnectOnLoss'] ) )
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
        eZDebug::accumulatorStop( 'firebird_query' );

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
        if ( expFirebirdDialect::isSchemaChange( $prepared ) )
        {
            $this->KnownTables = null;
            // a dropped table takes the sequence of its auto_increment column along (its trigger goes with it), so
            // the table can be made again (installers drop and make a package's tables)
            $dropped = expFirebirdDialect::droppedTable( $prepared );
            if ( $dropped !== null && class_exists( 'expFirebirdSchema' ) )
            {
                $sequence = expFirebirdSchema::objectName( 'S_', $dropped );
                $found = $this->Transport->run( expFirebirdDialect::existsQuery( 'SEQUENCE', $sequence ) );
                if ( $found !== false && $found['rows'] )
                    $this->Transport->run( 'DROP SEQUENCE ' . $sequence );
            }
        }
        eZDBQueryCache::noteWrite( $this, $sql );
        return $result;
    }

    /**
     * Takes the transport's error into ErrorNumber (the SQLCODE, or the GDS code, or -1), ErrorMessage (without any
     * password) and ErrorCategory.
     *
     * @param array|null $error
     */
    protected function takeError( $error )
    {
        if ( !is_array( $error ) )
            $error = array( 'sqlcode' => null, 'gds' => null, 'state' => null, 'message' => 'Unknown Firebird error' );
        $this->ErrorCategory = expFirebirdDialect::errorCategory( $error['sqlcode'], $error['message'], $error['gds'] );
        $this->ErrorNumber = $error['sqlcode'] !== null ? (int)$error['sqlcode'] : ( $error['gds'] !== null ? (int)$error['gds'] : -1 );
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
        $paged = expFirebirdDialect::applyLimit( expFirebirdDialect::rewrite( $sql ), $limit, $offset );
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
     * The id the last INSERT of this connection into $table made (any table without $table), as the table's
     * trigger kept it; false when there is none.
     *
     * @param string|false $table
     * @param string|false $column
     * @return int|string|false
     */
    function lastSerialID( $table = false, $column = false )
    {
        if ( !$this->IsConnected )
            return false;
        $name = self::ID_CONTEXT;
        if ( $table !== false && $table !== null && $table !== '' )
            $name .= '.' . expFirebirdDialect::upper( trim( (string)$table, '`" ' ) );
        $result = $this->Transport->run( "SELECT RDB\$GET_CONTEXT('USER_SESSION', " . expFirebirdDialect::stringLiteral( $name ) . ') AS id FROM RDB$DATABASE' );
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
        eZDebug::writeError( "Firebird $method failed: " . $this->ErrorMessage, __CLASS__ );
        return false;
    }

    /**
     * MySQL's LOCK TABLES: Firebird reserves tables only when a transaction starts (SET TRANSACTION ... RESERVING),
     * which PDO does not offer. lock() opens a transaction (the rows the kernel then writes are locked by it) and
     * unlock() commits it.
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
        $temporary = expFirebirdDialect::temporaryTable( $createTableQuery );
        if ( $temporary === null )
            return $this->query( $createTableQuery );
        $name = $temporary['name'];
        if ( !$this->tableExists( $name ) )
        {
            $made = false;
            $helper = $this->Transport->sibling();
            if ( $helper->open( $this->connectionConfig() + array() ) )
            {
                $made = $helper->run( $temporary['sql'] ) !== false || $this->tableExists( $name, true );
                $helper->close();
            }
            if ( !$made && !$this->query( $temporary['sql'] ) )
                return false;
            $this->KnownTables = null;
        }
        $ok = $this->query( 'DELETE FROM ' . expFirebirdDialect::quoteIdentifierIfNeeded( $name ) );
        if ( $ok )
            $this->TempTables[strtolower( $name )] = $name;
        return $ok;
    }

    /**
     * DROP TABLE of a temporary table: its rows of this connection are deleted, the shared definition stays.
     */
    function dropTempTable( $dropTableQuery = '', $server = self::SERVER_SLAVE )
    {
        if ( preg_match( '/^\s*DROP\s+(?:TEMPORARY\s+)?TABLE\s+(?:IF\s+EXISTS\s+)?([`"]?[\w$]+[`"]?)\s*;?\s*$/i', (string)$dropTableQuery, $m ) )
        {
            $name = trim( $m[1], '`"' );
            if ( isset( $this->TempTables[strtolower( $name )] ) )
            {
                unset( $this->TempTables[strtolower( $name )] );
                return $this->query( 'DELETE FROM ' . expFirebirdDialect::quoteIdentifierIfNeeded( $name ) );
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
     * Whether a table of that name exists (the list is read once per connection).
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
            foreach ( (array)$this->relationList( eZDBInterface::RELATION_TABLE ) as $table )
                $this->KnownTables[strtolower( $table )] = true;
        }
        return isset( $this->KnownTables[strtolower( (string)$name )] );
    }

    // ------------------------------------------------------------------ strings and expressions

    function escapeString( $str )
    {
        return expFirebirdDialect::escapeString( $str );
    }

    function subString( $string, $from, $len = null )
    {
        return expFirebirdDialect::subString( $string, $from, $len );
    }

    function concatString( $strings = array() )
    {
        return expFirebirdDialect::concatString( (array)$strings );
    }

    function md5( $str )
    {
        return expFirebirdDialect::md5( $str );
    }

    function bitAnd( $arg1, $arg2 )
    {
        return expFirebirdDialect::bitAnd( $arg1, $arg2 );
    }

    function bitOr( $arg1, $arg2 )
    {
        return expFirebirdDialect::bitOr( $arg1, $arg2 );
    }

    function generateSQLINStatement( $elements, $columnName = '', $not = false, $unique = true, $type = false )
    {
        return expFirebirdDialect::inList( is_array( $elements ) ? $elements : array( $elements ), $columnName, $not === true, $type,
                                           (int)$this->Settings['InListChunkSize'], (bool)$unique );
    }

    public function sqlHelperDialect()
    {
        return 'firebird';
    }

    public function sqlLiteral( $value )
    {
        return expFirebirdDialect::literal( $value );
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
     * The catalogue query of a relation type: names in lower case, the way the kernel writes them (Firebird stores an
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
                return 'SELECT LOWER(TRIM(RDB$RELATION_NAME)) AS name FROM RDB$RELATIONS'
                     . ' WHERE COALESCE(RDB$SYSTEM_FLAG, 0) = 0 AND RDB$VIEW_BLR IS NULL ORDER BY 1';
            case eZDBInterface::RELATION_SEQUENCE:
                return 'SELECT LOWER(TRIM(RDB$GENERATOR_NAME)) AS name FROM RDB$GENERATORS WHERE COALESCE(RDB$SYSTEM_FLAG, 0) = 0 ORDER BY 1';
            case eZDBInterface::RELATION_TRIGGER:
                return 'SELECT LOWER(TRIM(RDB$TRIGGER_NAME)) AS name FROM RDB$TRIGGERS WHERE COALESCE(RDB$SYSTEM_FLAG, 0) = 0 ORDER BY 1';
            case eZDBInterface::RELATION_VIEW:
                return 'SELECT LOWER(TRIM(RDB$RELATION_NAME)) AS name FROM RDB$RELATIONS'
                     . ' WHERE COALESCE(RDB$SYSTEM_FLAG, 0) = 0 AND RDB$VIEW_BLR IS NOT NULL ORDER BY 1';
            case eZDBInterface::RELATION_INDEX:
                return 'SELECT LOWER(TRIM(i.RDB$INDEX_NAME)) AS name FROM RDB$INDICES i'
                     . ' WHERE COALESCE(i.RDB$SYSTEM_FLAG, 0) = 0 AND NOT EXISTS (SELECT 1 FROM RDB$RELATION_CONSTRAINTS c'
                     . ' WHERE c.RDB$INDEX_NAME = i.RDB$INDEX_NAME) ORDER BY 1';
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
        $ok = $this->query( 'DROP ' . $words[$relationType] . ' ' . expFirebirdDialect::quoteIdentifier( $relationName ) );
        if ( $ok && $relationType === eZDBInterface::RELATION_TABLE )
            $this->KnownTables = null;
        return $ok;
    }

    /**
     * The columns of a table from the catalogue: name, type code, length, scale, nullable, default.
     *
     * @param string $table
     * @return array|false
     */
    public function tableColumns( $table )
    {
        return $this->arrayQuery( 'SELECT LOWER(TRIM(rf.RDB$FIELD_NAME)) AS name, f.RDB$FIELD_TYPE AS type, f.RDB$FIELD_SUB_TYPE AS sub_type,'
                                . ' f.RDB$CHARACTER_LENGTH AS char_len, f.RDB$FIELD_PRECISION AS numeric_precision, f.RDB$FIELD_SCALE AS scale,'
                                . ' COALESCE(rf.RDB$NULL_FLAG, f.RDB$NULL_FLAG, 0) AS not_null, CAST(COALESCE(rf.RDB$DEFAULT_SOURCE, f.RDB$DEFAULT_SOURCE) AS VARCHAR(1000)) AS default_source'
                                . ' FROM RDB$RELATION_FIELDS rf JOIN RDB$FIELDS f ON f.RDB$FIELD_NAME = rf.RDB$FIELD_SOURCE'
                                . ' WHERE rf.RDB$RELATION_NAME = ' . expFirebirdDialect::stringLiteral( expFirebirdDialect::upper( $table ) )
                                . ' ORDER BY rf.RDB$FIELD_POSITION' );
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
     * Firebird creates a database outside of any connection (CREATE DATABASE in isql, gbak, or the server's
     * container): not from here.
     */
    function createDatabase( $dbName )
    {
        $this->ErrorMessage = 'Firebird creates a database outside of a connection (isql: CREATE DATABASE ... PAGE_SIZE 16384 DEFAULT CHARACTER SET UTF8)';
        $this->ErrorNumber = -1;
        eZDebug::writeError( $this->ErrorMessage, __METHOD__ );
        return false;
    }

    function removeDatabase( $dbName )
    {
        $this->ErrorMessage = 'Firebird drops a database outside of a connection (isql: DROP DATABASE)';
        $this->ErrorNumber = -1;
        eZDebug::writeError( $this->ErrorMessage, __METHOD__ );
        return false;
    }

    function supportsDefaultValuesInsertion()
    {
        return true;
    }

    function close()
    {
        if ( $this->Transport && $this->IsConnected && $this->TempTables && (int)$this->TransactionCounter === 0 )
            foreach ( $this->TempTables as $name )
                $this->Transport->run( 'DELETE FROM ' . expFirebirdDialect::quoteIdentifierIfNeeded( $name ) );
        if ( $this->Transport )
            $this->Transport->close();
        $this->TempTables = array();
        $this->IsConnected = false;
    }
}
