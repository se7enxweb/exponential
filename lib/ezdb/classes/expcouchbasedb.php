<?php
/**
 * File containing the expCouchbaseDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Couchbase driver (DatabaseImplementation=couchbase): a data table connection, or the database of a site, in a
 * scope of a Couchbase bucket, through the Query service's SQL++ (expCouchbaseQueryService, PHP's curl extension).
 *
 *   [DatabaseSettings]
 *   DatabaseImplementation=couchbase
 *   Server=cb1.example.com,cb2.example.com      the Query service's nodes
 *   Port=8093                                    18093 with TLS
 *   Database=exponential.site                    bucket.scope (bucket alone: its _default scope)
 *   User=..., Password=...
 *
 *   [CouchbaseSettings]                          see settings/site.ini
 *
 * A table is a collection of the scope, a row a JSON document, a column a field. The driver keeps each table's
 * definition (.dba form: the columns' types, defaults, the primary key, the counted-up column) in the collection
 * exp_couchbase_schema; from it a document's key is the row's primary key (one column: its value; several: a JSON
 * array), a statement's literals take their column's type, and an INSERT fills the columns it leaves out. New ids
 * come from counter documents in exp_couchbase_sequence. expCouchbaseDialect turns the SQL into SQL++.
 *
 * Consistency: an index of Couchbase is updated after the write, so a read through an index waits for the writes
 * before it (scan_consistency=request_plus, the default ScanConsistency) to be exact. A read by the whole primary
 * key needs no index and no wait (USE KEYS). Transactions are the Query service's (BEGIN WORK; every statement of
 * the transaction carries its txid; reads in it see its writes).
 *
 * databaseName() is 'couchbase'; error 1062 is a duplicate key, 1146 a missing collection, others the service's code.
 *
 * @since 6.0.15
 * @package lib
 */
class expCouchbaseDB extends eZDBInterface
{
    /** The collection of the tables' definitions */
    const SCHEMA_COLLECTION = 'exp_couchbase_schema';

    /** The collection of the counters of new ids */
    const SEQUENCE_COLLECTION = 'exp_couchbase_sequence';

    /** The error number of a server that does not answer (MySQL's "can't connect") */
    const ERROR_CONNECTION_FAILED = 2003;

    /** The defaults of [CouchbaseSettings] */
    const DEFAULT_SETTINGS = array(
        'Scope' => '',
        'ScanConsistency' => 'request_plus',
        'TransactionTimeout' => '120s',
        'DurabilityLevel' => '',
        'TLS' => 'disabled',
        'TLSCAFile' => '',
        'TLSVerifyPeer' => 'enabled',
        'ConnectTimeout' => 5,
        'RequestTimeout' => 75,
        'AutoCreateScope' => 'disabled',
        'PrimaryIndex' => 'enabled',
        'IndexWait' => 120,
        'TranslationCache' => 500,
    );

    /** @var array the effective [CouchbaseSettings] */
    protected $Settings = array();

    /** @var callable the Query service (expCouchbaseQueryService or a stand-in) */
    protected $Service;

    /** @var string */
    protected $Bucket = '';

    /** @var string */
    protected $ScopeName = '_default';

    /** @var string|null the open transaction's id */
    protected $TxId = null;

    /** @var array|null the tables' definitions: table => .dba table */
    protected $Definitions = null;

    /** @var expCouchbaseDialect|null */
    protected $Dialect = null;

    /** @var array table => the id last given or inserted */
    protected $LastIds = array();

    /** @var string[] temporary tables of this connection, dropped when it closes */
    protected $TemporaryTables = array();

    /** @var array SQL => translation (SELECTs) */
    protected $Translations = array();

    /** @var bool whether this connection wrote since its last read through an index (ScanConsistency=auto) */
    protected $Wrote = true;

    /** @var array|null the last statement's metrics */
    public $LastMetrics = null;

    /** @var int statements sent */
    public $NumQueries = 0;

    /**
     * @param array $parameters the eZDB parameters; 'couchbase' => array( <[CouchbaseSettings] name> => value ) over
     *                          site.ini; 'couchbase_service' => a callable( array $request ) answering for the service (tests)
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
        $this->UseSlaveServer = false;
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
        $this->Settings = self::settings( isset( $parameters['couchbase'] ) && is_array( $parameters['couchbase'] ) ? $parameters['couchbase'] : array() );
        list( $this->Bucket, $this->ScopeName ) = self::bucketAndScope( $this->DB, $this->Settings['Scope'] );
        if ( class_exists( 'eZDebug' ) )
            eZDebug::createAccumulatorGroup( 'couchbase_total', 'Couchbase Total' );

        if ( isset( $parameters['couchbase_service'] ) && is_callable( $parameters['couchbase_service'] ) )
            $this->Service = $parameters['couchbase_service'];
        else
        {
            if ( !expCouchbaseQueryService::available() )
                $this->fail( 'PHP has no curl extension: the Couchbase driver reaches the Query service through it', eZDBInterface::ERROR_MISSING_EXTENSION );
            $this->Service = new expCouchbaseQueryService( $this->Server, $this->Port, $this->User, $this->Password, $this->Settings );
        }
        if ( $this->Bucket === '' )
        {
            // no database named yet (the setup wizard asks the driver before it has one)
            $this->IsConnected = false;
            return;
        }
        $this->IsConnected = true;
        $version = $this->send( 'SELECT RAW ds_version()', array( 'context' => false ) );
        if ( $version === false )
        {
            $this->IsConnected = false;
            $this->fail( 'Couchbase does not answer: ' . $this->ErrorMessage, self::ERROR_CONNECTION_FAILED );
        }
        $this->ServerVersion = isset( $version[0] ) ? (string)$version[0] : '';
        if ( $this->ScopeName !== '_default' && !$this->scopeExists( $this->ScopeName ) )
        {
            if ( !self::flag( $this->Settings['AutoCreateScope'] ) || !$this->createDatabase( $this->ScopeName ) )
            {
                $this->IsConnected = false;
                $this->fail( "The bucket {$this->Bucket} has no scope {$this->ScopeName}", self::ERROR_CONNECTION_FAILED );
            }
        }
    }

    /** @var string the server's version */
    protected $ServerVersion = '';

    /**
     * Records a connection error and throws.
     *
     * @throws eZDBNoConnectionException
     */
    protected function fail( $message, $number )
    {
        $this->ErrorMessage = $message;
        $this->ErrorNumber = $number;
        if ( class_exists( 'eZDebug' ) )
            eZDebug::writeError( $message, __CLASS__ );
        throw new eZDBNoConnectionException( (string)$this->Server, $message, $number );
    }

    /**
     * The effective [CouchbaseSettings]: the defaults, site.ini, then $overrides.
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
            if ( $ini->hasGroup( 'CouchbaseSettings' ) )
                foreach ( $ini->group( 'CouchbaseSettings' ) as $name => $value )
                    if ( array_key_exists( $name, $settings ) && $value !== '' && $value !== null )
                        $settings[$name] = $value;
        }
        foreach ( $overrides as $name => $value )
            $settings[$name] = $value;
        return $settings;
    }

    /**
     * The bucket and scope a Database value names: bucket.scope, bucket/scope or bucket (its scope from the setting,
     * else _default) (pure).
     *
     * @param string $database
     * @param string $scopeSetting
     * @return string[] array( bucket, scope )
     */
    public static function bucketAndScope( $database, $scopeSetting = '' )
    {
        $database = trim( (string)$database );
        $parts = preg_split( '#[./]#', $database, 2 );
        $bucket = $parts[0];
        $scope = isset( $parts[1] ) && $parts[1] !== '' ? $parts[1] : ( trim( (string)$scopeSetting ) !== '' ? trim( (string)$scopeSetting ) : '_default' );
        return array( $bucket, $scope );
    }

    /** @return string */
    public function bucket()
    {
        return $this->Bucket;
    }

    /** @return string */
    public function scope()
    {
        return $this->ScopeName;
    }

    /** @return string default:`bucket`.`scope` */
    public function queryContext()
    {
        return 'default:' . expCouchbaseDialect::quoteName( $this->Bucket ) . '.' . expCouchbaseDialect::quoteName( $this->ScopeName );
    }

    function databaseName()
    {
        return 'couchbase';
    }

    public function sqlHelperDialect()
    {
        return 'couchbase';
    }

    function bindingType()
    {
        return eZDBInterface::BINDING_NO;
    }

    function bindVariable( $value, $fieldDef = false )
    {
        return $value;
    }

    // ------------------------------------------------------------------ the service

    /**
     * Sends a SQL++ statement as it is.
     *
     * @param string $statement
     * @param array $options context (false: none), consistency, tx (false: outside the open transaction), args,
     *                       report (false: an error is not reported)
     * @return array|false the results
     */
    public function send( $statement, array $options = array() )
    {
        $request = array( 'statement' => $statement );
        if ( !isset( $options['context'] ) || $options['context'] !== false )
            $request['query_context'] = $this->queryContext();
        if ( isset( $options['args'] ) )
            $request['args'] = array_values( $options['args'] );
        $inTx = $this->TxId !== null && ( !isset( $options['tx'] ) || $options['tx'] !== false );
        if ( $inTx )
            $request['txid'] = $this->TxId;
        else
        {
            $consistency = isset( $options['consistency'] ) ? $options['consistency'] : $this->Settings['ScanConsistency'];
            if ( $consistency === 'auto' )
                $consistency = $this->Wrote ? 'request_plus' : 'not_bounded';
            $request['scan_consistency'] = $consistency === 'not_bounded' ? 'not_bounded' : 'request_plus';
        }
        if ( isset( $options['extra'] ) )
            $request = $options['extra'] + $request;

        if ( class_exists( 'eZDebug' ) )
            eZDebug::accumulatorStart( 'couchbase_query', 'couchbase_total', 'Couchbase queries' );
        if ( $this->OutputSQL )
            $this->startTimer();
        $started = microtime( true );
        $response = call_user_func( $this->Service, $request );
        $this->NumQueries++;
        if ( class_exists( 'eZDBInterface' ) && method_exists( 'eZDBInterface', 'profileSQL' ) )
            eZDBInterface::profileSQL( $statement, microtime( true ) - $started );
        if ( $this->OutputSQL )
        {
            $this->endTimer();
            if ( $this->timeTaken() > $this->SlowSQLTimeout )
                $this->reportQuery( __CLASS__, $statement, isset( $response['results'] ) ? count( (array)$response['results'] ) : false, $this->timeTaken() );
        }
        if ( class_exists( 'eZDebug' ) )
            eZDebug::accumulatorStop( 'couchbase_query' );
        $this->LastMetrics = isset( $response['metrics'] ) ? $response['metrics'] : null;

        if ( !is_array( $response ) || !isset( $response['status'] ) || $response['status'] !== 'success' )
        {
            $error = isset( $response['errors'][0] ) ? $response['errors'][0] : array( 'code' => 0, 'msg' => 'no answer' );
            $this->ErrorMessage = (string)( isset( $error['msg'] ) ? $error['msg'] : json_encode( $error ) );
            $cause = isset( $error['reason']['code'] ) ? (int)$error['reason']['code'] : 0;
            $this->ErrorNumber = expCouchbaseDialect::errorNumber( $cause === 17012 ? 17012 : ( isset( $error['code'] ) ? (int)$error['code'] : 0 ), $this->ErrorMessage );
            if ( !isset( $options['report'] ) || $options['report'] !== false )
                $this->reportFailure( $statement );
            return false;
        }
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
        return isset( $response['results'] ) && is_array( $response['results'] ) ? $response['results'] : array();
    }

    /**
     * Reports a failed statement: a debug error, and per the error handling an exception or the kernel's report.
     *
     * @throws eZDBException
     */
    protected function reportFailure( $statement )
    {
        if ( class_exists( 'eZDebug' ) )
            eZDebug::writeError( self::failedQueryMessage( $this->ErrorMessage, $statement ), __CLASS__ );
        if ( $this->errorHandling == eZDB::ERROR_HANDLING_EXCEPTIONS )
            throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
        $this->reportError();
    }

    /**
     * An unsupported statement: false (or the exception of the error handling) with its name.
     */
    protected function unsupported( expDBUnsupportedException $e, $sql )
    {
        $this->ErrorMessage = $e->getMessage();
        $this->ErrorNumber = 1235;
        if ( class_exists( 'eZDebug' ) )
            eZDebug::writeError( self::failedQueryMessage( $this->ErrorMessage, $sql ), __CLASS__ );
        if ( $this->errorHandling == eZDB::ERROR_HANDLING_EXCEPTIONS )
            throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
        $this->reportError();
        return false;
    }

    // ------------------------------------------------------------------ catalogue

    /**
     * The tables' definitions (.dba form), read once per connection.
     *
     * @param bool $reload
     * @return array table => definition
     */
    public function definitions( $reload = false )
    {
        if ( $this->Definitions !== null && !$reload )
            return $this->Definitions;
        if ( !$this->IsConnected )
            return $this->Definitions = array();
        $rows = $this->send( 'SELECT META(d).id AS name, d.definition FROM ' . expCouchbaseDialect::quoteName( self::SCHEMA_COLLECTION ) . ' AS d WHERE META(d).id IS NOT MISSING',
                             array( 'report' => false, 'tx' => false, 'consistency' => 'request_plus' ) );
        if ( $rows === false && $this->ErrorNumber !== 1146 )
        {
            // the service did not answer: the catalogue read before is kept (an empty one would turn every
            // literal into the wrong type and every table into an unknown one)
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeWarning( 'The table definitions could not be read: ' . $this->ErrorMessage, __METHOD__ );
            if ( $this->Definitions === null )
                $this->Definitions = array();
            return $this->Definitions;
        }
        $this->Definitions = array();
        $this->Dialect = null;
        if ( is_array( $rows ) )
            foreach ( $rows as $row )
                if ( isset( $row['name'], $row['definition'] ) && is_array( $row['definition'] ) )
                    $this->Definitions[(string)$row['name']] = $row['definition'];
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
        return $this->Definitions;
    }

    /**
     * The dialect's catalogue entry of a .dba table (pure).
     *
     * @param array $definition
     * @return array fields (column => kind), order, primary, auto, defaults, not_null
     */
    public static function catalogueEntry( array $definition )
    {
        $entry = array( 'fields' => array(), 'order' => array(), 'primary' => array(), 'auto' => null, 'defaults' => array(), 'not_null' => array() );
        foreach ( isset( $definition['fields'] ) ? (array)$definition['fields'] : array() as $name => $field )
        {
            $name = strtolower( $name );
            $field = (array)$field;
            $entry['fields'][$name] = expCouchbaseDialect::kindOf( $field );
            $entry['order'][] = $name;
            if ( isset( $field['type'] ) && $field['type'] === 'auto_increment' )
                $entry['auto'] = $name;
            $default = array_key_exists( 'default', $field ) ? $field['default'] : false;
            if ( $default !== false && $default !== null )
                $entry['defaults'][$name] = expCouchbaseDialect::coerce( $default, $entry['fields'][$name] );
            if ( !empty( $field['not_null'] ) )
                $entry['not_null'][$name] = true;
        }
        foreach ( isset( $definition['indexes'] ) ? (array)$definition['indexes'] : array() as $index )
            if ( isset( $index['type'] ) && $index['type'] === 'primary' )
            {
                foreach ( $index['fields'] as $f )
                    $entry['primary'][] = strtolower( is_array( $f ) ? $f['name'] : $f );
                break;
            }
        return $entry;
    }

    /**
     * The dialect over the current catalogue.
     *
     * @return expCouchbaseDialect
     */
    public function dialect()
    {
        if ( $this->Dialect === null )
        {
            $catalogue = array();
            foreach ( $this->definitions() as $table => $definition )
                $catalogue[$table] = self::catalogueEntry( $definition );
            $this->Dialect = new expCouchbaseDialect( $catalogue, $this->Bucket, $this->ScopeName );
            $this->Translations = array();
        }
        return $this->Dialect;
    }

    // ------------------------------------------------------------------ statements

    function query( $sql, $server = false )
    {
        $result = $this->run( $sql, false );
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
            $limited = expDBSQLHelpers::limit( 'couchbase', $sql, $limit !== false && $limit >= 0 ? $limit : false, $offset );
            if ( $limited === null )
                return array();
            $sql = $limited;
        }
        $result = $this->run( $sql, true );
        if ( !is_array( $result ) )
            return $result === false ? false : array();
        $rows = array();
        foreach ( $result as $i => $row )
        {
            if ( $column === false )
                $rows[$i + $offset] = $row;
            elseif ( is_int( $column ) || ctype_digit( (string)$column ) )
            {
                $values = array_values( $row );
                $rows[$i + $offset] = isset( $values[(int)$column] ) ? $values[(int)$column] : null;
            }
            else
                $rows[$i + $offset] = array_key_exists( $column, $row ) ? $row[$column] : null;
        }
        eZDBQueryCache::store( $cacheTicket, $rows );
        return $rows;
    }

    /**
     * Runs one statement of the kernel's SQL.
     *
     * @param string $sql
     * @param bool $wantRows
     * @return array|bool rows for a SELECT, true for anything else, false on failure
     */
    protected function run( $sql, $wantRows )
    {
        if ( !$this->IsConnected )
            return false;
        $kind = expCouchbaseDialect::kind( $sql );
        try
        {
            switch ( $kind )
            {
                case 'select':
                    return $this->runSelect( $sql );
                case 'insert':
                    $ok = $this->runInsert( $sql );
                    break;
                case 'update':
                case 'delete':
                    $ok = $this->runWrite( $sql );
                    break;
                case 'ddl':
                    $ok = $this->runDDL( $sql );
                    break;
                case 'transaction':
                    $word = strtoupper( strtok( ltrim( $sql ), " \t\r\n;" ) );
                    if ( $word === 'COMMIT' )
                        $ok = $this->commitQuery();
                    elseif ( $word === 'ROLLBACK' )
                        $ok = $this->rollbackQuery();
                    else
                        $ok = $this->beginQuery();
                    break;
                case 'native':
                    $statement = preg_replace( '#^\s*/\*sqlpp\*/\s*#', '', $sql );
                    $rows = $this->send( $statement, array( 'tx' => preg_match( '/^\s*(CREATE|DROP|BUILD|ALTER)\b/i', $statement ) ? false : null ) );
                    if ( $rows !== false && preg_match( '/\b' . self::SCHEMA_COLLECTION . '\b/', $statement ) )
                    {
                        $this->Definitions = null;
                        $this->Dialect = null;
                    }
                    if ( $rows !== false && !preg_match( '/^\s*(SELECT|INFER|EXPLAIN|ADVISE)\b/i', $statement ) )
                        $this->Wrote = true;
                    return $rows === false ? false : ( $wantRows ? $rows : true );
                default:
                    return $wantRows ? array() : true;
            }
        }
        catch ( expDBUnsupportedException $e )
        {
            return $this->unsupported( $e, $sql );
        }
        if ( $ok !== false )
            eZDBQueryCache::noteWrite( $this, $sql );
        return $ok === false ? false : true;
    }

    /**
     * A SELECT: translated (cached per SQL), sent, the rows in the order and with the names of the select list.
     *
     * @param string $sql
     * @return array|false
     */
    protected function runSelect( $sql )
    {
        $dialect = $this->dialect();
        if ( isset( $this->Translations[$sql] ) )
            $translation = $this->Translations[$sql];
        else
        {
            $translation = $dialect->select( $sql );
            if ( count( $this->Translations ) >= (int)$this->Settings['TranslationCache'] )
                $this->Translations = array();
            $this->Translations[$sql] = $translation;
        }
        $rows = $this->send( $translation['sql'] );
        if ( $rows === false )
            return false;
        $this->Wrote = false;
        return self::rows( $rows, $translation['columns'] );
    }

    /**
     * The service's results as the kernel's rows (pure): the select list's order, a missing field as NULL, true and
     * false as 1 and 0, objects and arrays as JSON.
     *
     * @param array $results
     * @param string[]|null $columns
     * @return array[]
     */
    public static function rows( array $results, $columns )
    {
        $rows = array();
        foreach ( $results as $result )
        {
            if ( !is_array( $result ) )
            {
                $rows[] = array( 0 => self::value( $result ) );
                continue;
            }
            $row = array();
            if ( $columns !== null )
            {
                foreach ( $columns as $column )
                    $row[$column] = array_key_exists( $column, $result ) ? self::value( $result[$column] ) : null;
                foreach ( $result as $name => $value )
                    if ( !array_key_exists( $name, $row ) )
                        $row[$name] = self::value( $value );
            }
            else
                foreach ( $result as $name => $value )
                    $row[$name] = self::value( $value );
            $rows[] = $row;
        }
        return $rows;
    }

    /** A value of a result as the kernel takes it */
    public static function value( $value )
    {
        if ( is_bool( $value ) )
            return $value ? 1 : 0;
        if ( is_array( $value ) )
            return json_encode( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        return $value;
    }

    /**
     * An INSERT, REPLACE or INSERT ... SELECT: documents keyed by their primary key, the counted-up ids, the defaults.
     *
     * @param string $sql
     * @return bool
     */
    protected function runInsert( $sql )
    {
        $dialect = $this->dialect();
        $parsed = $dialect->insert( $sql );
        $table = $parsed['table'];
        $entry = $dialect->table( $table );
        $this->Wrote = true;
        if ( $parsed['select'] !== null )
            return $this->runInsertSelect( $parsed, $entry );

        // the documents
        $documents = array();
        $explicitMax = null;
        $needIds = 0;
        foreach ( $parsed['rows'] as $values )
        {
            $doc = array();
            $expressions = array();
            if ( $entry )
                foreach ( $entry['order'] as $column )
                    $doc[$column] = array_key_exists( $column, $entry['defaults'] ) ? $entry['defaults'][$column] : null;
            foreach ( $parsed['columns'] as $k => $column )
            {
                $item = $values[$k];
                if ( !empty( $item['default'] ) )
                    continue;
                if ( $item['literal'] )
                    $doc[$column] = expCouchbaseDialect::coerce( $item['value'], $entry && isset( $entry['fields'][$column] ) ? $entry['fields'][$column] : null );
                else
                {
                    $expressions[$column] = $item['value'];
                    $doc[$column] = null;
                }
            }
            if ( $entry && $entry['auto'] !== null && !isset( $expressions[$entry['auto']] ) )
            {
                $auto = $entry['auto'];
                if ( $doc[$auto] === null || $doc[$auto] === 0 || $doc[$auto] === '' )
                {
                    $doc[$auto] = null;
                    $needIds++;
                }
                else
                    $explicitMax = $explicitMax === null ? (int)$doc[$auto] : max( $explicitMax, (int)$doc[$auto] );
            }
            $documents[] = array( 'doc' => $doc, 'expressions' => $expressions );
        }
        if ( $needIds )
        {
            $first = $this->nextIds( $table, $entry['auto'], $needIds );
            if ( $first === false )
                return false;
            foreach ( $documents as $k => $d )
                if ( $d['doc'][$entry['auto']] === null )
                {
                    $documents[$k]['doc'][$entry['auto']] = $first++;
                    $this->LastIds[$table] = $first - 1;
                }
        }
        if ( $explicitMax !== null )
        {
            if ( $this->bumpCounter( $table, $entry['auto'], $explicitMax ) === false )
                return false;
            if ( !$needIds )
                $this->LastIds[$table] = $explicitMax;
        }

        $verb = $parsed['mode'] === 'replace' ? 'UPSERT' : 'INSERT';
        $pairs = array();
        $keys = array();
        foreach ( $documents as $d )
        {
            $key = $entry ? $dialect->documentKey( $table, $d['doc'] ) : null;
            if ( $key === null || isset( $d['expressions'] ) && array_intersect( array_keys( $d['expressions'] ), $entry ? $entry['primary'] : array() ) )
                $key = self::uuid();
            $keys[] = $key;
            $pairs[] = '(' . expCouchbaseDialect::stringLiteral( $key ) . ', ' . self::documentLiteral( $d['doc'], $d['expressions'] ) . ')';
        }
        if ( !$pairs )
            return true;
        if ( $parsed['update'] === null && $parsed['mode'] !== 'ignore' )
            return $this->send( $verb . ' INTO ' . expCouchbaseDialect::quoteName( $table ) . ' (KEY, VALUE) VALUES ' . implode( ', ', $pairs ) ) !== false;

        // INSERT IGNORE and ON DUPLICATE KEY UPDATE: one document at a time, a duplicate key handled
        foreach ( $pairs as $k => $pair )
        {
            $ok = $this->send( 'INSERT INTO ' . expCouchbaseDialect::quoteName( $table ) . ' (KEY, VALUE) VALUES ' . $pair, array( 'report' => false ) );
            if ( $ok !== false )
                continue;
            if ( $this->ErrorNumber !== 1062 )
            {
                $this->reportFailure( 'INSERT INTO ' . $table );
                return false;
            }
            if ( $parsed['mode'] === 'ignore' && $parsed['update'] === null )
                continue;
            $sets = array();
            foreach ( $parsed['update'] as $column => $what )
            {
                if ( isset( $what['values'] ) )
                {
                    $from = $what['values'];
                    $value = isset( $documents[$k]['expressions'][$from] ) ? $documents[$k]['expressions'][$from]
                           : expCouchbaseDialect::literal( isset( $documents[$k]['doc'][$from] ) ? $documents[$k]['doc'][$from] : null );
                }
                else
                    $value = $what['sql'];
                $sets[] = expCouchbaseDialect::quoteName( $table ) . '.' . expCouchbaseDialect::quoteName( $column ) . ' = ' . $value;
            }
            if ( $sets && $this->send( 'UPDATE ' . expCouchbaseDialect::quoteName( $table ) . ' USE KEYS ' . expCouchbaseDialect::stringLiteral( $keys[$k] )
                                       . ' SET ' . implode( ', ', $sets ) ) === false )
                return false;
        }
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
        return true;
    }

    /**
     * INSERT INTO t (columns) SELECT ...: one SQL++ statement, keys from the primary key (or new ones).
     */
    protected function runInsertSelect( array $parsed, $entry )
    {
        $table = $parsed['table'];
        $fields = array();
        $select = $parsed['select'];
        // the SELECT's columns by position: its result is read as x, its names are the select list's
        $names = self::selectNames( $select );
        if ( count( $names ) !== count( $parsed['columns'] ) )
            throw new expDBUnsupportedException( 'couchbase', 'INSERT ... SELECT whose columns cannot be matched' );
        $doc = array();
        if ( $entry )
            foreach ( $entry['order'] as $column )
                $doc[$column] = expCouchbaseDialect::literal( array_key_exists( $column, $entry['defaults'] ) ? $entry['defaults'][$column] : null );
        foreach ( $parsed['columns'] as $k => $column )
            $doc[$column] = 'x.' . expCouchbaseDialect::quoteName( $names[$k] );
        foreach ( $doc as $column => $value )
            $fields[] = expCouchbaseDialect::stringLiteral( $column ) . ': ' . $value;
        $key = 'UUID()';
        if ( $entry && $entry['primary'] && !array_diff( $entry['primary'], $parsed['columns'] ) )
        {
            $parts = array();
            foreach ( $entry['primary'] as $column )
                $parts[] = 'TOSTRING(' . $doc[$column] . ')';
            $key = count( $parts ) === 1 ? $parts[0] : 'ENCODE_JSON([' . implode( ', ', $parts ) . '])';
        }
        if ( $entry && $entry['auto'] !== null && !in_array( $entry['auto'], $parsed['columns'], true ) )
            throw new expDBUnsupportedException( 'couchbase', 'INSERT ... SELECT that leaves the counted-up column to the database' );
        $statement = 'INSERT INTO ' . expCouchbaseDialect::quoteName( $table ) . ' (KEY _k, VALUE _v) SELECT ' . $key . ' AS _k, {' . implode( ', ', $fields ) . '} AS _v FROM ('
                   . $select . ') AS x';
        $verb = $parsed['mode'] === 'replace' ? 'UPSERT' : 'INSERT';
        return $this->send( $verb . substr( $statement, 6 ) ) !== false;
    }

    /**
     * The names of a translated SELECT's list (... AS `name`, ...) (pure).
     *
     * @param string $sqlpp
     * @return string[]
     */
    public static function selectNames( $sqlpp )
    {
        preg_match_all( '/ AS `((?:[^`]|``)*)`(?=,| FROM )/', substr( $sqlpp, 0, ( $p = strpos( $sqlpp, ' FROM ' ) ) === false ? strlen( $sqlpp ) : $p + 6 ), $m );
        return array_map( function ( $name ) { return str_replace( '``', '`', $name ); }, $m[1] );
    }

    /**
     * A document as a SQL++ object: literals as JSON, expressions as they are.
     *
     * @param array $doc column => value
     * @param array $expressions column => SQL++
     * @return string
     */
    public static function documentLiteral( array $doc, array $expressions = array() )
    {
        if ( !$expressions )
            return json_encode( (object)$doc, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRESERVE_ZERO_FRACTION );
        $parts = array();
        foreach ( $doc as $column => $value )
            $parts[] = expCouchbaseDialect::stringLiteral( $column ) . ': ' . ( isset( $expressions[$column] ) ? $expressions[$column] : expCouchbaseDialect::literal( $value ) );
        return '{' . implode( ', ', $parts ) . '}';
    }

    /** A random key for a document of a table without a primary key */
    public static function uuid()
    {
        $bytes = random_bytes( 16 );
        $bytes[6] = chr( ord( $bytes[6] ) & 0x0f | 0x40 );
        $bytes[8] = chr( ord( $bytes[8] ) & 0x3f | 0x80 );
        return vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $bytes ), 4 ) );
    }

    /**
     * An UPDATE or DELETE. An UPDATE that sets a column of the primary key moves the documents to their new keys.
     *
     * @param string $sql
     * @return bool
     */
    protected function runWrite( $sql )
    {
        $dialect = $this->dialect();
        $write = $dialect->write( $sql );
        $this->Wrote = true;
        if ( $write['kind'] === 'delete' || !$write['keys_changed'] )
            return $this->send( $write['sql'] ) !== false;
        $q = expCouchbaseDialect::quoteName( $write['alias'] );
        $rows = $this->send( $write['sql'] . ' RETURNING META(' . $q . ').id AS _k, ' . $q . '.* ' );
        if ( $rows === false )
            return false;
        foreach ( $rows as $row )
        {
            $old = $row['_k'];
            unset( $row['_k'] );
            $key = $dialect->documentKey( $write['table'], $row );
            if ( $key === null || $key === $old )
                continue;
            if ( $this->send( 'INSERT INTO ' . expCouchbaseDialect::quoteName( $write['table'] ) . ' (KEY, VALUE) VALUES (' . expCouchbaseDialect::stringLiteral( $key ) . ', '
                              . self::documentLiteral( $row ) . ')' ) === false )
                return false;
            if ( $this->send( 'DELETE FROM ' . expCouchbaseDialect::quoteName( $write['table'] ) . ' USE KEYS ' . expCouchbaseDialect::stringLiteral( $old ) ) === false )
                return false;
        }
        return true;
    }

    // ------------------------------------------------------------------ ids

    /**
     * Takes $count new ids of a table's counted-up column; the first of them.
     *
     * @param string $table
     * @param string $column
     * @param int $count
     * @return int|false
     */
    public function nextIds( $table, $column, $count = 1 )
    {
        $q = expCouchbaseDialect::quoteName( self::SEQUENCE_COLLECTION );
        $key = expCouchbaseDialect::stringLiteral( $table );
        for ( $attempt = 0; $attempt < 3; $attempt++ )
        {
            // outside the transaction, as MySQL's auto_increment: an id is never given twice, a rollback leaves a gap
            $rows = $this->send( "UPDATE $q USE KEYS $key SET v = v + " . (int)$count . ' RETURNING RAW v', array( 'tx' => false, 'report' => false ) );
            if ( is_array( $rows ) && isset( $rows[0] ) )
                return (int)$rows[0] - $count + 1;
            if ( $rows === false && $this->ErrorNumber !== 1146 )
            {
                $this->reportFailure( 'UPDATE ' . self::SEQUENCE_COLLECTION );
                return false;
            }
            if ( !$this->initCounter( $table, $column ) )
                return false;
        }
        return false;
    }

    /**
     * Moves a counter past an id a statement gave itself.
     */
    protected function bumpCounter( $table, $column, $value )
    {
        $q = expCouchbaseDialect::quoteName( self::SEQUENCE_COLLECTION );
        $key = expCouchbaseDialect::stringLiteral( $table );
        $rows = $this->send( "UPDATE $q USE KEYS $key SET v = GREATEST(v, " . (int)$value . ') RETURNING RAW v', array( 'tx' => false, 'report' => false ) );
        if ( is_array( $rows ) && isset( $rows[0] ) )
            return true;
        if ( !$this->initCounter( $table, $column, (int)$value ) )
            return false;
        return true;
    }

    /**
     * Makes the counter of a table: the column's highest value (or $atLeast).
     */
    protected function initCounter( $table, $column, $atLeast = 0 )
    {
        if ( !$this->ensureCollection( self::SEQUENCE_COLLECTION ) )
            return false;
        $max = $this->send( 'SELECT RAW MAX(t.' . expCouchbaseDialect::quoteName( $column ) . ') FROM ' . expCouchbaseDialect::quoteName( $table ) . ' AS t WHERE META(t).id IS NOT MISSING',
                            array( 'report' => false, 'consistency' => 'request_plus' ) );
        $start = is_array( $max ) && isset( $max[0] ) && is_numeric( $max[0] ) ? (int)$max[0] : 0;
        $start = max( $start, (int)$atLeast );
        $ok = $this->send( 'INSERT INTO ' . expCouchbaseDialect::quoteName( self::SEQUENCE_COLLECTION ) . ' (KEY, VALUE) VALUES ('
                           . expCouchbaseDialect::stringLiteral( $table ) . ', {"v": ' . $start . '})', array( 'tx' => false, 'report' => false ) );
        if ( $ok === false && $this->ErrorNumber !== 1062 )
        {
            $this->reportFailure( 'INSERT INTO ' . self::SEQUENCE_COLLECTION );
            return false;
        }
        if ( $ok === false && $atLeast )
            $this->send( 'UPDATE ' . expCouchbaseDialect::quoteName( self::SEQUENCE_COLLECTION ) . ' USE KEYS ' . expCouchbaseDialect::stringLiteral( $table )
                         . ' SET v = GREATEST(v, ' . (int)$atLeast . ')', array( 'tx' => false ) );
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
        return true;
    }

    function lastSerialID( $table = false, $column = false )
    {
        if ( !$table )
            return false;
        $table = strtolower( $table );
        return isset( $this->LastIds[$table] ) ? $this->LastIds[$table] : false;
    }

    // ------------------------------------------------------------------ DDL

    /**
     * Whether a collection of the scope exists.
     */
    public function collectionExists( $name )
    {
        $rows = $this->send( 'SELECT RAW name FROM system:keyspaces WHERE `bucket` = $1 AND `scope` = $2 AND name = $3',
                             array( 'context' => false, 'tx' => false, 'args' => array( $this->Bucket, $this->ScopeName, $name ) ) );
        return is_array( $rows ) && count( $rows ) > 0;
    }

    /**
     * Makes a collection (and its primary index) when it is missing, and waits until the Query service sees it.
     *
     * @param string $name
     * @param bool $primaryIndex
     * @return bool
     */
    public function ensureCollection( $name, $primaryIndex = true )
    {
        $q = expCouchbaseDialect::quoteName( $name );
        if ( $this->send( "CREATE COLLECTION $q IF NOT EXISTS", array( 'tx' => false ) ) === false )
            return false;
        // a new collection reaches the Query service after a moment
        $deadline = microtime( true ) + max( 1, (int)$this->Settings['IndexWait'] );
        while ( !$this->collectionExists( $name ) && microtime( true ) < $deadline )
            usleep( 200000 );
        if ( $primaryIndex && self::flag( $this->Settings['PrimaryIndex'] ) )
        {
            if ( $this->send( "CREATE PRIMARY INDEX IF NOT EXISTS ON $q", array( 'tx' => false ) ) === false )
                return false;
        }
        return true;
    }

    /**
     * Makes a table from its .dba definition: the collection, its primary index, an index per index of the
     * definition (built together), and the definition kept in exp_couchbase_schema.
     *
     * @param string $table
     * @param array $definition .dba table
     * @return bool
     */
    public function createTable( $table, array $definition )
    {
        $table = strtolower( $table );
        if ( !$this->ensureCollection( self::SCHEMA_COLLECTION ) || !$this->ensureCollection( $table ) )
            return false;
        $q = expCouchbaseDialect::quoteName( $table );
        $names = array();
        foreach ( expCouchbaseSchema::indexStatements( $table, $definition ) as $name => $statement )
        {
            if ( $this->send( $statement, array( 'tx' => false ) ) === false )
                return false;
            $names[] = expCouchbaseDialect::quoteName( $name );
        }
        if ( $names && $this->send( "BUILD INDEX ON $q(" . implode( ', ', $names ) . ')', array( 'tx' => false, 'report' => false ) ) === false
             && stripos( $this->ErrorMessage, 'already' ) === false )
        {
            $this->reportFailure( "BUILD INDEX ON $q" );
            return false;
        }
        if ( !$this->waitForIndexes( $table ) )
            return false;
        return $this->storeDefinition( $table, $definition );
    }

    /**
     * Waits (IndexWait seconds) until every index of a collection is online.
     */
    public function waitForIndexes( $table )
    {
        $deadline = microtime( true ) + max( 1, (int)$this->Settings['IndexWait'] );
        do
        {
            $rows = $this->send( 'SELECT RAW state FROM system:indexes WHERE bucket_id = $1 AND scope_id = $2 AND keyspace_id = $3',
                                 array( 'context' => false, 'tx' => false, 'args' => array( $this->Bucket, $this->ScopeName, $table ) ) );
            if ( $rows === false )
                return false;
            if ( !array_diff( array_unique( $rows ), array( 'online' ) ) )
                return true;
            usleep( 250000 );
        }
        while ( microtime( true ) < $deadline );
        $this->ErrorMessage = "The indexes of $table are not online after " . (int)$this->Settings['IndexWait'] . ' s';
        $this->ErrorNumber = 1;
        $this->reportFailure( "BUILD INDEX ON $table" );
        return false;
    }

    /**
     * Keeps a table's definition.
     */
    public function storeDefinition( $table, array $definition )
    {
        $definition['name'] = $table;
        $ok = $this->send( 'UPSERT INTO ' . expCouchbaseDialect::quoteName( self::SCHEMA_COLLECTION ) . ' (KEY, VALUE) VALUES ('
                           . expCouchbaseDialect::stringLiteral( $table ) . ', ' . json_encode( array( 'definition' => $definition ),
                               JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE ) . ')', array( 'tx' => false ) ) !== false;
        if ( $ok )
        {
            if ( $this->Definitions !== null )
                $this->Definitions[$table] = $definition;
            $this->Dialect = null;
        }
        return $ok;
    }

    /**
     * CREATE TABLE, DROP TABLE, CREATE INDEX, DROP INDEX, ALTER TABLE ADD / DROP / MODIFY, TRUNCATE.
     *
     * @param string $sql
     * @return bool
     */
    protected function runDDL( $sql )
    {
        if ( preg_match( '/^\s*TRUNCATE\b/i', $sql ) )
            return $this->runWrite( $sql );
        $ddl = $this->dialect()->ddl( $sql );
        $definitions = $this->definitions();
        $this->Wrote = true;
        switch ( $ddl['action'] )
        {
            case 'create_table':
                $table = $ddl['table'];
                if ( $ddl['temporary'] )
                    $this->TemporaryTables[$table] = true;
                if ( isset( $definitions[$table] ) && $this->collectionExists( $table ) && preg_match( '/\bIF\s+NOT\s+EXISTS\b/i', $sql ) )
                    return true;
                return $this->createTable( $table, $ddl['definition'] );
            case 'drop_table':
                foreach ( $ddl['tables'] as $table )
                {
                    if ( $this->send( 'DROP COLLECTION ' . expCouchbaseDialect::quoteName( $table ) . ' IF EXISTS', array( 'tx' => false ) ) === false )
                        return false;
                    // Couchbase drops a collection after the answer: a CREATE right after would find it still there
                    // (IF NOT EXISTS does nothing) and the table would then be gone
                    $deadline = microtime( true ) + max( 1, (int)$this->Settings['IndexWait'] );
                    while ( $this->collectionExists( $table ) && microtime( true ) < $deadline )
                        usleep( 200000 );
                    $this->send( 'DELETE FROM ' . expCouchbaseDialect::quoteName( self::SCHEMA_COLLECTION ) . ' USE KEYS ' . expCouchbaseDialect::stringLiteral( $table ),
                                 array( 'tx' => false, 'report' => false ) );
                    unset( $this->TemporaryTables[$table], $this->LastIds[$table] );
                    $this->send( 'DELETE FROM ' . expCouchbaseDialect::quoteName( self::SEQUENCE_COLLECTION ) . ' USE KEYS ' . expCouchbaseDialect::stringLiteral( $table ),
                                 array( 'tx' => false, 'report' => false ) );
                }
                $this->Definitions = null;
                $this->Dialect = null;
                $this->ErrorMessage = '';
                $this->ErrorNumber = 0;
                return true;
            case 'create_index':
                $table = $ddl['table'];
                $definition = isset( $definitions[$table] ) ? $definitions[$table] : array( 'fields' => array(), 'indexes' => array() );
                $definition['indexes'][$ddl['name']] = array( 'type' => $ddl['unique'] ? 'unique' : 'non-unique', 'fields' => $ddl['fields'] );
                $statements = expCouchbaseSchema::indexStatements( $table, array( 'fields' => array(), 'indexes' => array( $ddl['name'] => $definition['indexes'][$ddl['name']] ) ), false, false );
                foreach ( $statements as $statement )
                    if ( $this->send( $statement, array( 'tx' => false ) ) === false )
                        return false;
                return $this->waitForIndexes( $table ) && $this->storeDefinition( $table, $definition );
            case 'drop_index':
                $table = $ddl['table'];
                if ( $table === null )
                    foreach ( $definitions as $t => $d )
                        if ( isset( $d['indexes'][$ddl['name']] ) )
                            $table = $t;
                if ( $table === null )
                    return true;
                $this->send( 'DROP INDEX ' . expCouchbaseDialect::quoteName( expCouchbaseSchema::indexName( $table, $ddl['name'] ) ) . ' IF EXISTS ON '
                             . expCouchbaseDialect::quoteName( $table ), array( 'tx' => false ) );
                if ( isset( $definitions[$table]['indexes'][$ddl['name']] ) )
                {
                    unset( $definitions[$table]['indexes'][$ddl['name']] );
                    return $this->storeDefinition( $table, $definitions[$table] );
                }
                return true;
            case 'add_column':
            case 'modify_column':
                $table = $ddl['table'];
                $definition = isset( $definitions[$table] ) ? $definitions[$table] : array( 'fields' => array(), 'indexes' => array() );
                $definition['fields'][$ddl['column']] = $ddl['field'];
                if ( !$this->storeDefinition( $table, $definition ) )
                    return false;
                $entry = self::catalogueEntry( $definition );
                $default = array_key_exists( $ddl['column'], $entry['defaults'] ) ? $entry['defaults'][$ddl['column']] : null;
                $c = expCouchbaseDialect::quoteName( $ddl['column'] );
                // the documents get the new field (a missing field is MISSING to SQL++, not NULL)
                return $this->send( 'UPDATE ' . expCouchbaseDialect::quoteName( $table ) . " AS t SET t.$c = " . expCouchbaseDialect::literal( $default )
                                    . " WHERE t.$c IS MISSING" ) !== false;
            case 'drop_column':
                $table = $ddl['table'];
                $c = expCouchbaseDialect::quoteName( $ddl['column'] );
                if ( $this->send( 'UPDATE ' . expCouchbaseDialect::quoteName( $table ) . " AS t UNSET t.$c WHERE t.$c IS NOT MISSING" ) === false )
                    return false;
                if ( isset( $definitions[$table]['fields'][$ddl['column']] ) )
                {
                    unset( $definitions[$table]['fields'][$ddl['column']] );
                    return $this->storeDefinition( $table, $definitions[$table] );
                }
                return true;
        }
        throw new expDBUnsupportedException( 'couchbase', 'DDL ' . $ddl['action'] );
    }

    // ------------------------------------------------------------------ transactions

    function beginQuery()
    {
        if ( !$this->IsConnected )
            return false;
        if ( $this->TxId !== null )
            return true;
        $extra = array( 'txtimeout' => (string)$this->Settings['TransactionTimeout'] );
        if ( trim( (string)$this->Settings['DurabilityLevel'] ) !== '' )
            $extra['durability_level'] = (string)$this->Settings['DurabilityLevel'];
        $rows = $this->send( 'BEGIN WORK', array( 'extra' => $extra ) );
        if ( !is_array( $rows ) || !isset( $rows[0]['txid'] ) )
            return false;
        $this->TxId = (string)$rows[0]['txid'];
        return true;
    }

    function commitQuery()
    {
        if ( $this->TxId === null )
            return true;
        $ok = $this->send( 'COMMIT WORK' ) !== false;
        $this->TxId = null;
        $this->Wrote = true;
        return $ok;
    }

    function rollbackQuery()
    {
        if ( $this->TxId === null )
            return true;
        // a failed statement may have ended the transaction already: no error for that
        $this->send( 'ROLLBACK WORK', array( 'report' => false ) );
        $this->TxId = null;
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
        return true;
    }

    /** @return string|null the open transaction's id */
    public function transactionId()
    {
        return $this->TxId;
    }

    /**
     * No table locks in Couchbase: a transaction keeps the writes together (a conflicting writer waits or fails).
     */
    function lock( $table )
    {
        $this->begin();
    }

    function unlock()
    {
        $this->commit();
    }

    // ------------------------------------------------------------------ helpers of the kernel

    function escapeString( $str )
    {
        return str_replace( array( '\\', "'", "\0" ), array( '\\\\', "\\'", '\\0' ), (string)$str );
    }

    function subString( $string, $from, $len = null )
    {
        if ( $len === null || $len === false )
            return " SUBSTRING( $string, $from ) ";
        return " SUBSTRING( $string, $from, $len ) ";
    }

    function concatString( $strings = array() )
    {
        return ' CONCAT( ' . implode( ', ', $strings ) . ' ) ';
    }

    function md5( $str )
    {
        return " MD5( $str ) ";
    }

    function bitAnd( $arg1, $arg2 )
    {
        return " BITAND( $arg1, $arg2 ) ";
    }

    function bitOr( $arg1, $arg2 )
    {
        return " BITOR( $arg1, $arg2 ) ";
    }

    function supportedRelationTypeMask()
    {
        return eZDBInterface::RELATION_TABLE_BIT | eZDBInterface::RELATION_INDEX_BIT;
    }

    function supportedRelationTypes()
    {
        return array( eZDBInterface::RELATION_TABLE, eZDBInterface::RELATION_INDEX );
    }

    /**
     * The collections of the scope (the driver's own left out), or the indexes.
     */
    function relationList( $relationType = eZDBInterface::RELATION_TABLE )
    {
        if ( !$this->IsConnected )
            return array();
        if ( $relationType === eZDBInterface::RELATION_INDEX )
            $rows = $this->send( 'SELECT RAW name FROM system:indexes WHERE bucket_id = $1 AND scope_id = $2 ORDER BY name',
                                 array( 'context' => false, 'tx' => false, 'args' => array( $this->Bucket, $this->ScopeName ) ) );
        elseif ( $relationType === eZDBInterface::RELATION_TABLE )
            $rows = $this->send( 'SELECT RAW name FROM system:keyspaces WHERE `bucket` = $1 AND `scope` = $2 ORDER BY name',
                                 array( 'context' => false, 'tx' => false, 'args' => array( $this->Bucket, $this->ScopeName ) ) );
        else
            return array();
        if ( !is_array( $rows ) )
            return false;
        return array_values( array_filter( $rows, function ( $name ) {
            return !in_array( $name, array( expCouchbaseDB::SCHEMA_COLLECTION, expCouchbaseDB::SEQUENCE_COLLECTION, '_default' ), true );
        } ) );
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
            if ( preg_match( '/^(ez|exp)/', $name ) )
                $tables[$name] = eZDBInterface::RELATION_TABLE;
        return $tables;
    }

    function relationMatchRegexp( $relationType )
    {
        return '#^(ez|exp|tmp_notification_rule_s)#';
    }

    function removeRelation( $relationName, $relationType )
    {
        if ( $relationType !== eZDBInterface::RELATION_TABLE || !preg_match( '/^[A-Za-z0-9_%-]+$/', (string)$relationName ) )
            return false;
        return $this->query( 'DROP TABLE ' . $relationName );
    }

    /**
     * A table's columns from its definition: name => array( type, kind, not_null, default ).
     *
     * @param string $table
     * @return array|null
     */
    public function tableColumns( $table )
    {
        $definitions = $this->definitions();
        $table = strtolower( $table );
        if ( !isset( $definitions[$table] ) )
            return null;
        $columns = array();
        foreach ( (array)$definitions[$table]['fields'] as $name => $field )
            $columns[strtolower( $name )] = array( 'type' => isset( $field['type'] ) ? $field['type'] : 'varchar', 'kind' => expCouchbaseDialect::kindOf( (array)$field ),
                                                   'not_null' => !empty( $field['not_null'] ), 'default' => isset( $field['default'] ) && $field['default'] !== false ? $field['default'] : null );
        return $columns;
    }

    /**
     * A table's definition as kept (or null).
     */
    public function tableDefinition( $table )
    {
        $definitions = $this->definitions();
        return isset( $definitions[strtolower( $table )] ) ? $definitions[strtolower( $table )] : null;
    }

    protected function scopeExists( $scope )
    {
        $rows = $this->send( 'SELECT RAW name FROM system:scopes WHERE `bucket` = $1 AND name = $2',
                             array( 'context' => false, 'tx' => false, 'args' => array( $this->Bucket, $scope ) ) );
        return is_array( $rows ) && count( $rows ) > 0;
    }

    function databaseServerVersion()
    {
        $version = $this->ServerVersion;
        $number = preg_match( '/^(\d+(?:\.\d+)*)/', $version, $m ) ? $m[1] : $version;
        return array( 'string' => $number, 'values' => array_map( 'intval', explode( '.', $number ) ),
                      'name' => 'Couchbase Server' . ( stripos( $version, 'community' ) !== false ? ' Community' : ( stripos( $version, 'enterprise' ) !== false ? ' Enterprise' : '' ) ) );
    }

    function databaseClientVersion()
    {
        $curl = function_exists( 'curl_version' ) ? curl_version() : array( 'version' => '' );
        return array( 'string' => 'REST (curl ' . $curl['version'] . ')', 'values' => array() );
    }

    function isCharsetSupported( $charset )
    {
        return in_array( strtolower( (string)$charset ), array( 'utf-8', 'utf8', 'utf8mb4' ), true );
    }

    /**
     * The scopes of the bucket: the "databases" a site can be in.
     */
    function availableDatabases()
    {
        if ( !$this->IsConnected )
            return false;
        $rows = $this->send( 'SELECT RAW name FROM system:scopes WHERE `bucket` = $1 ORDER BY name', array( 'context' => false, 'tx' => false, 'args' => array( $this->Bucket ) ) );
        return is_array( $rows ) ? $rows : false;
    }

    /**
     * Makes a scope of the bucket.
     *
     * @param string $dbName scope, or bucket.scope
     * @return bool
     */
    function createDatabase( $dbName )
    {
        list( $bucket, $scope ) = self::bucketAndScope( strpos( (string)$dbName, '.' ) === false && strpos( (string)$dbName, '/' ) === false ? $this->Bucket . '.' . $dbName : $dbName );
        $ok = $this->send( 'CREATE SCOPE ' . expCouchbaseDialect::quoteName( $bucket ) . '.' . expCouchbaseDialect::quoteName( $scope ) . ' IF NOT EXISTS',
                           array( 'context' => false, 'tx' => false ) ) !== false;
        $deadline = microtime( true ) + max( 1, (int)$this->Settings['IndexWait'] );
        while ( $ok && !$this->scopeExists( $scope ) && microtime( true ) < $deadline )
            usleep( 200000 );
        return $ok;
    }

    /**
     * Drops a scope of the bucket, with every collection in it.
     *
     * @param string $dbName scope, or bucket.scope
     * @return bool
     */
    function removeDatabase( $dbName )
    {
        list( $bucket, $scope ) = self::bucketAndScope( strpos( (string)$dbName, '.' ) === false && strpos( (string)$dbName, '/' ) === false ? $this->Bucket . '.' . $dbName : $dbName );
        if ( $scope === '_default' )
            return false;
        return $this->send( 'DROP SCOPE ' . expCouchbaseDialect::quoteName( $bucket ) . '.' . expCouchbaseDialect::quoteName( $scope ) . ' IF EXISTS',
                            array( 'context' => false, 'tx' => false ) ) !== false;
    }

    function supportsDefaultValuesInsertion()
    {
        return false;
    }

    /**
     * Rolls back an open transaction and drops this connection's temporary tables.
     */
    function close()
    {
        if ( $this->IsConnected )
        {
            if ( $this->TxId !== null )
                $this->rollbackQuery();
            foreach ( array_keys( $this->TemporaryTables ) as $table )
                $this->send( 'DROP COLLECTION ' . expCouchbaseDialect::quoteName( $table ) . ' IF EXISTS', array( 'tx' => false, 'report' => false ) );
        }
        $this->TemporaryTables = array();
        if ( $this->Service instanceof expCouchbaseQueryService )
            $this->Service->close();
        $this->IsConnected = false;
    }

    function setError()
    {
    }

    /**
     * Whether a setting is on (enabled, true, yes, 1, on).
     *
     * @param mixed $value
     * @return bool
     */
    public static function flag( $value )
    {
        return in_array( strtolower( trim( (string)$value ) ), array( '1', 'yes', 'true', 'enabled', 'on' ), true );
    }
}
?>
