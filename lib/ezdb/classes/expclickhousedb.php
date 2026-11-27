<?php
/**
 * File containing the expClickHouseDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The ClickHouse database driver (DatabaseImplementation=clickhouse): a data table connection, or a source of rows to
 * import, on a ClickHouse server. ClickHouse is a columnar database built for analytics over many rows; PHP reaches
 * it through its HTTP interface (expClickHouseHttp), with curl or PHP's own HTTP stream.
 *
 * It is not a database for a whole site, and the installer does not offer it: ClickHouse has no transactions over
 * statements, does not enforce a primary or unique key (two rows with one key are both kept), has no sequences or
 * auto-increment ids, and changes rows by rewriting parts of a table (an UPDATE takes tens of milliseconds and a
 * DELETE leaves a mask until the parts merge). The kernel relies on all four. What it is for: analytics and archive
 * tables of data table classes, written in chunks and read with aggregates, beside a site on another database.
 *
 * site.ini [ClickHouseSettings] (see settings/site.ini), or a data table connection:
 *
 *   [Connection_events]
 *   Engine=clickhouse
 *   Server=127.0.0.1
 *   Port=8123
 *   Database=analytics
 *   User=exponential
 *   Secret=clickhouse_password
 *
 * What the driver does: begin(), commit() and rollback() send nothing (a rollback after a write logs a warning, as
 * nothing can be undone); UPDATE is a lightweight update, else a mutation waited for; DELETE without WHERE is TRUNCATE;
 * new ids for a serial column are the column's maximum plus one, taken by the driver (nextSerialID(); one writer of new
 * rows at a time); error numbers are the SQLSTATE PostgreSQL gives the same error where there is one, else 'CH' and
 * ClickHouse's number. databaseName() is 'clickhouse'.
 *
 * @since 6.0.15
 * @package lib
 */
class expClickHouseDB extends eZDBInterface
{
    /** The defaults of [ClickHouseSettings] */
    const DEFAULT_SETTINGS = array(
        'Secure' => 'disabled',
        'VerifyTLS' => 'enabled',
        'ConnectTimeout' => 10,
        'Timeout' => 300,
        'MutationsSync' => 2,
        'Settings' => array(),
    );

    /** @var array the effective [ClickHouseSettings] */
    protected $Settings = array();

    /** @var expClickHouseHttp|object the transport (or a stand-in with the same methods, for tests) */
    protected $Http = null;

    /** @var int[] the id nextSerialID() gave last, by table */
    protected $LastIDs = array();

    /** @var bool whether begin() was called and something was written since */
    protected $WroteInTransaction = false;

    /** @var array the summary (rows written, read ...) of the last statement */
    public $LastSummary = array();

    /** @var int statements run, for the debug output */
    public $NumQueries = 0;

    /**
     * @param array $parameters the eZDB parameters; 'clickhouse' => array( <[ClickHouseSettings] name> => value ) over
     *                          site.ini, 'clickhouse_http' => a stand-in for expClickHouseHttp (tests)
     * @throws eZDBNoConnectionException when the server does not answer
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
        $this->Settings = self::settings( isset( $parameters['clickhouse'] ) && is_array( $parameters['clickhouse'] ) ? $parameters['clickhouse'] : array() );
        eZDebug::createAccumulatorGroup( 'clickhouse_total', 'ClickHouse Total' );
        if ( isset( $parameters['clickhouse_http'] ) && is_object( $parameters['clickhouse_http'] ) )
            $this->Http = $parameters['clickhouse_http'];
        else
            $this->Http = new expClickHouseHttp( $this->transportOptions() );

        // a first statement tells whether the server answers and the login holds
        eZDebug::accumulatorStart( 'clickhouse_connection', 'clickhouse_total', 'Database connection' );
        $answer = $this->Http->query( 'SELECT 1' );
        eZDebug::accumulatorStop( 'clickhouse_connection' );
        if ( $answer === false )
        {
            $this->IsConnected = false;
            $this->ErrorMessage = (string)$this->Http->Error;
            $this->ErrorNumber = expClickHouseDialect::errorCode( $this->Http->ErrorCode );
            eZDebug::writeError( 'Connection to ClickHouse failed: ' . $this->ErrorMessage, __CLASS__ );
            if ( function_exists( 'eZAppendWarningItem' ) )
                eZAppendWarningItem( array( 'error' => array( 'type' => 'ezdb', 'number' => $this->ErrorNumber ),
                                            'text' => 'Connection to ClickHouse failed: ' . $this->ErrorMessage ) );
            throw new eZDBNoConnectionException( $this->Server, $this->ErrorMessage, $this->ErrorNumber );
        }
        $this->IsConnected = true;
    }

    /**
     * The effective [ClickHouseSettings]: the defaults, site.ini, then $overrides.
     *
     * @param array $overrides
     * @return array
     */
    public static function settings( array $overrides = array() )
    {
        $settings = self::DEFAULT_SETTINGS;
        try
        {
            $ini = eZINI::instance( 'site.ini' );
            if ( $ini->hasGroup( 'ClickHouseSettings' ) )
                foreach ( array_keys( self::DEFAULT_SETTINGS ) as $name )
                    if ( $ini->hasVariable( 'ClickHouseSettings', $name ) )
                        $settings[$name] = $ini->variable( 'ClickHouseSettings', $name );
        }
        catch ( Throwable $e )
        {
        }
        foreach ( $overrides as $name => $value )
            if ( array_key_exists( $name, self::DEFAULT_SETTINGS ) )
                $settings[$name] = $value;
        return $settings;
    }

    /**
     * The options of the transport (pure apart from the settings): host, port, TLS, login, database, timeouts.
     *
     * @return array
     */
    public function transportOptions()
    {
        $settings = array();
        foreach ( (array)$this->Settings['Settings'] as $name => $value )
            if ( is_string( $name ) && preg_match( '/^[a-z_][a-z0-9_]*$/', $name ) )
                $settings[$name] = $value;
        return array( 'host' => $this->Server === '' || $this->Server === false ? '127.0.0.1' : (string)$this->Server,
                      'port' => (int)$this->Port, 'secure' => self::flag( $this->Settings['Secure'] ),
                      'user' => (string)$this->User === '' ? 'default' : (string)$this->User, 'password' => (string)$this->Password,
                      'database' => (string)$this->DB, 'timeout' => (float)$this->Settings['Timeout'],
                      'connect_timeout' => (float)$this->Settings['ConnectTimeout'], 'verify_tls' => self::flag( $this->Settings['VerifyTLS'] ),
                      'settings' => $settings );
    }

    function databaseName()
    {
        return 'clickhouse';
    }

    public function sqlHelperDialect()
    {
        return 'clickhouse';
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
     * Sends one statement; the result of the transport, true when nothing was sent, false on an error.
     *
     * @param string $sql
     * @return array|bool
     */
    protected function run( $sql )
    {
        if ( !$this->IsConnected )
            return false;
        $sent = expClickHouseDialect::rewrite( $sql );
        if ( $sent === null )
            return true;
        $settings = array();
        $word = expClickHouseDialect::firstWord( $sent );
        if ( $word === 'UPDATE' )
            $settings['allow_experimental_lightweight_update'] = 1;
        if ( $word === 'ALTER' )
            $settings['mutations_sync'] = (int)$this->Settings['MutationsSync'];

        eZDebug::accumulatorStart( 'clickhouse_query', 'clickhouse_total', 'ClickHouse queries' );
        if ( $this->OutputSQL )
            $this->startTimer();
        $result = $this->Http->query( $sent, $settings );
        // a table without the block number columns takes no lightweight update: the mutation instead
        if ( $result === false && $word === 'UPDATE' && expClickHouseDialect::isLightweightUpdateRefused( $this->Http->ErrorCode, $this->Http->Error ) )
        {
            $mutation = expClickHouseDialect::updateAsMutation( $sent );
            if ( $mutation !== null )
            {
                $sent = $mutation;
                $result = $this->Http->query( $sent, array( 'mutations_sync' => (int)$this->Settings['MutationsSync'] ) );
            }
        }
        $this->NumQueries++;
        if ( $this->OutputSQL )
        {
            $this->endTimer();
            if ( $this->timeTaken() > $this->SlowSQLTimeout )
                $this->reportQuery( __CLASS__, $sent, is_array( $result ) ? count( $result['rows'] ) : false, $this->timeTaken() );
        }
        eZDebug::accumulatorStop( 'clickhouse_query' );

        if ( $result === false )
        {
            $this->ErrorMessage = (string)$this->Http->Error;
            $this->ErrorNumber = expClickHouseDialect::errorCode( $this->Http->ErrorCode );
            eZDebug::writeError( self::failedQueryMessage( $this->ErrorMessage, $sent ), __CLASS__ );
            if ( $this->errorHandling == eZDB::ERROR_HANDLING_EXCEPTIONS )
                throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
            $this->reportError();
            return false;
        }
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
        $this->LastSummary = isset( $result['summary'] ) ? $result['summary'] : array();
        if ( $this->TransactionCounter > 0 && expClickHouseDialect::isWrite( $sent ) )
            $this->WroteInTransaction = true;
        eZDBQueryCache::noteWrite( $this, $sent );
        return $result;
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
            $limited = expDBSQLHelpers::limit( 'clickhouse', $sql, $limit !== false && $limit >= 0 ? $limit : false, $offset );
            if ( $limited === null )
                return array();
            $sql = $limited;
        }
        $result = $this->run( $sql );
        if ( !is_array( $result ) )
            return $result === false ? false : array();
        $names = $result['columns'];
        $types = $result['types'];
        $rows = array();
        foreach ( $result['rows'] as $i => $values )
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
     * A value as the kernel reads values from MySQL and PostgreSQL (pure): text; a boolean 1 or 0; a DateTime64
     * without its fraction.
     *
     * @param string|null $value
     * @param string $type ClickHouse's type
     * @return string|null
     */
    public static function value( $value, $type )
    {
        if ( $value === null )
            return null;
        $base = preg_replace( '/^(Nullable|LowCardinality)\((.*)\)$/', '$2', (string)$type );
        $base = preg_replace( '/^(Nullable|LowCardinality)\((.*)\)$/', '$2', $base );
        if ( $base === 'Bool' )
            return $value === 'true' ? '1' : ( $value === 'false' ? '0' : $value );
        if ( strpos( $base, 'DateTime64' ) === 0 )
            return substr( $value, 0, 19 );
        return $value;
    }

    /**
     * Nothing is sent: ClickHouse has no transactions over statements.
     */
    function beginQuery()
    {
        $this->WroteInTransaction = false;
        return $this->IsConnected;
    }

    function commitQuery()
    {
        $this->WroteInTransaction = false;
        return true;
    }

    /**
     * Nothing can be undone: what was written since begin() stays. Logged as a warning when something was.
     */
    function rollbackQuery()
    {
        if ( $this->WroteInTransaction )
            eZDebug::writeWarning( 'A rollback on ClickHouse: what was written since begin() stays (ClickHouse has no transactions over statements)', __METHOD__ );
        $this->WroteInTransaction = false;
        return true;
    }

    function lock( $table )
    {
        $this->begin();
    }

    function unlock()
    {
        $this->commit();
    }

    /**
     * Whether an insert must give the serial column its value: always, ClickHouse makes no ids.
     */
    public function serialIDNeedsValue( $table, $column )
    {
        return true;
    }

    /**
     * The next id of a serial column: its maximum plus one (one writer of new rows at a time; ClickHouse has no
     * sequences). lastSerialID() returns it afterwards.
     *
     * @param string $table
     * @param string $column
     * @return int
     */
    public function nextSerialID( $table, $column = 'id' )
    {
        if ( !preg_match( '/^[A-Za-z0-9_]+$/', $table . $column ) )
            return false;
        $rows = $this->arrayQuery( "SELECT max(`$column`) AS m FROM `$table`" );
        $id = ( isset( $rows[0]['m'] ) ? (int)$rows[0]['m'] : 0 ) + 1;
        if ( isset( $this->LastIDs[$table] ) && $this->LastIDs[$table] >= $id )
            $id = $this->LastIDs[$table] + 1;
        $this->LastIDs[$table] = $id;
        return $id;
    }

    function lastSerialID( $table = false, $column = false )
    {
        return $table && isset( $this->LastIDs[$table] ) ? $this->LastIDs[$table] : false;
    }

    function escapeString( $str )
    {
        return expClickHouseDialect::escape( $str );
    }

    function subString( $string, $from, $len = null )
    {
        if ( $len === null || $len === false )
            return " substringUTF8( $string, $from ) ";
        return " substringUTF8( $string, $from, $len ) ";
    }

    function concatString( $strings = array() )
    {
        return ' concat( ' . implode( ', ', $strings ) . ' ) ';
    }

    function md5( $str )
    {
        return " lower( hex( MD5( $str ) ) ) ";
    }

    function bitAnd( $arg1, $arg2 )
    {
        return ' bitAnd( ' . $arg1 . ', ' . $arg2 . ' ) ';
    }

    function bitOr( $arg1, $arg2 )
    {
        return ' bitOr( ' . $arg1 . ', ' . $arg2 . ' ) ';
    }

    function supportedRelationTypeMask()
    {
        return eZDBInterface::RELATION_TABLE_BIT | eZDBInterface::RELATION_VIEW_BIT;
    }

    function supportedRelationTypes()
    {
        return array( eZDBInterface::RELATION_TABLE, eZDBInterface::RELATION_VIEW );
    }

    function relationList( $relationType = eZDBInterface::RELATION_TABLE )
    {
        if ( $relationType === eZDBInterface::RELATION_TABLE )
            $sql = "SELECT name FROM system.tables WHERE database = currentDatabase() AND NOT is_temporary AND engine NOT LIKE '%View' ORDER BY name";
        elseif ( $relationType === eZDBInterface::RELATION_VIEW )
            $sql = "SELECT name FROM system.tables WHERE database = currentDatabase() AND engine LIKE '%View' ORDER BY name";
        else
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
        foreach ( (array)$this->relationList( eZDBInterface::RELATION_TABLE ) as $name )
            $tables[$name] = eZDBInterface::RELATION_TABLE;
        return $tables;
    }

    function relationMatchRegexp( $relationType )
    {
        return '#^(ez|exp)#';
    }

    function removeRelation( $relationName, $relationType )
    {
        $typeName = $this->relationName( $relationType );
        if ( !$typeName || !preg_match( '/^[A-Za-z0-9_]+$/', (string)$relationName ) )
        {
            eZDebug::writeError( "Unsupported relation type '$relationType' or name '$relationName'", __METHOD__ );
            return false;
        }
        return $this->query( "DROP $typeName IF EXISTS `$relationName`" );
    }

    /**
     * The columns of a table: name => array( 'type' => ClickHouse type, 'nullable' => bool, 'default' => text|null ).
     *
     * @param string $table
     * @return array
     */
    public function tableColumns( $table )
    {
        $columns = array();
        $rows = $this->arrayQuery( "SELECT name, type, default_expression FROM system.columns WHERE database = currentDatabase()
                                    AND table = '" . $this->escapeString( $table ) . "' ORDER BY position" );
        foreach ( (array)$rows as $row )
            $columns[$row['name']] = array( 'type' => $row['type'], 'nullable' => strpos( $row['type'], 'Nullable(' ) === 0,
                                            'default' => $row['default_expression'] === '' ? null : $row['default_expression'] );
        return $columns;
    }

    function databaseServerVersion()
    {
        if ( !$this->IsConnected )
            return false;
        $rows = $this->arrayQuery( 'SELECT version() AS v' );
        $text = isset( $rows[0]['v'] ) ? (string)$rows[0]['v'] : '';
        return array( 'string' => $text, 'values' => explode( '.', $text ) );
    }

    function databaseClientVersion()
    {
        return array( 'string' => 'HTTP', 'values' => array() );
    }

    function isCharsetSupported( $charset )
    {
        return in_array( strtolower( (string)$charset ), array( 'utf-8', 'utf8' ), true );
    }

    function availableDatabases()
    {
        if ( !$this->IsConnected )
            return array();
        return array_values( (array)$this->arrayQuery( "SELECT name FROM system.databases WHERE name NOT IN ( 'system', 'INFORMATION_SCHEMA', 'information_schema' ) ORDER BY name",
                                                        array( 'column' => 'name' ) ) );
    }

    function createDatabase( $dbName )
    {
        if ( preg_match( '/^[A-Za-z0-9_]+$/', (string)$dbName ) )
            $this->query( "CREATE DATABASE IF NOT EXISTS `$dbName`" );
    }

    /**
     * Empties the open database (its tables and views are dropped), or drops another one.
     */
    function removeDatabase( $dbName )
    {
        if ( !$this->IsConnected || !preg_match( '/^[A-Za-z0-9_]+$/', (string)$dbName ) )
            return false;
        if ( $dbName !== $this->DB )
            return $this->query( "DROP DATABASE IF EXISTS `$dbName`" );
        foreach ( array( eZDBInterface::RELATION_VIEW, eZDBInterface::RELATION_TABLE ) as $type )
            foreach ( (array)$this->relationList( $type ) as $name )
                $this->removeRelation( $name, $type );
        return true;
    }

    function supportsDefaultValuesInsertion()
    {
        return false;
    }

    function close()
    {
        $this->IsConnected = false;
    }

    function setError()
    {
        if ( $this->Http && $this->Http->Error !== '' )
        {
            $this->ErrorMessage = (string)$this->Http->Error;
            $this->ErrorNumber = expClickHouseDialect::errorCode( $this->Http->ErrorCode );
        }
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
