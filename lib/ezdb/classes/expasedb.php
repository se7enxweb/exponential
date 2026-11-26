<?php
/**
 * File containing the expASEDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The SAP Adaptive Server Enterprise driver (ASE, formerly Sybase ASE): ASE 15.7 and 16.0, over pdo_dblib (FreeTDS at
 * TDS 5.0) or PDO_ODBC with FreeTDS's or SAP's ODBC driver. site.ini:
 *
 * <code>
 * [DatabaseSettings]
 * DatabaseImplementation=ase
 * Server=ase.example.com
 * Port=5000
 * Database=exponential
 * </code>
 *
 * and the [ASESettings] of site.ini. ASE and SQL Server share their origin and the TDS protocol, so this driver is
 * the SQL Server driver (expSQLServerDB: the retries, the reconnect, the #temporary tables, IDENTITY_INSERT for an
 * insert with its own id) with ASE's SQL (expASEDialect):
 *
 *  - New ids: the INSERT and SELECT @@identity go in one batch; an INSERT that gives the identity column NULL or 0
 *    leaves the column out, one with a real number runs with identity_insert on.
 *  - Paging: SELECT TOP (offset + limit), the first offset rows dropped here.
 *  - Text: an empty string is stored by ASE as one space in a variable length column; a value of exactly one space
 *    is read back as ''.
 *  - No read replicas, no application locks (sp_getapplock is SQL Server's).
 *
 * Untested against a server: no SAP ASE could be had for the tests of this driver (see expASEDialect).
 */
class expASEDB extends expSQLServerDB
{
    /** databaseName() of this driver */
    const NAME = 'ase';

    /** @var array the defaults of [ASESettings] */
    const DEFAULT_SETTINGS = array(
        'Layer' => 'auto',
        'ODBCDriver' => 'FreeTDS',
        'ApplicationName' => 'Exponential',
        'LoginTimeout' => 15,
        'LockTimeout' => -1,
        'DeadlockRetries' => 3,
        'DeadlockRetryWait' => 50,
        'ReconnectOnLoss' => 'enabled',
        'InListChunkSize' => 1000,
        'EmptyStringSpace' => 'enabled',
    );

    /**
     * Connects: the [DatabaseSettings] parameters of eZDB::instance(), and optionally 'ase' => array( <[ASESettings]
     * names> => value ) and 'ase_transport' => an expSQLServerTransport to use instead of PDO.
     *
     * @param array $parameters
     * @throws eZDBNoConnectionException
     */
    public function __construct( $parameters )
    {
        eZDBInterface::__construct( $parameters );
        // ASE has no readable secondaries of the SQL Server kind
        $this->UseSlaveServer = false;
        $this->Settings = self::aseSettings( isset( $parameters['ase'] ) && is_array( $parameters['ase'] ) ? $parameters['ase'] : array() );
        if ( isset( $parameters['ase_transport'] ) && $parameters['ase_transport'] instanceof expSQLServerTransport )
            $this->Transport = $parameters['ase_transport'];
        else
        {
            $layer = expASEPDOTransport::chooseASELayer( $this->Settings['Layer'] );
            if ( $layer === null )
            {
                $text = 'SAP ASE needs pdo_dblib (FreeTDS) or PDO_ODBC in PHP, the DB handler will not be initialized.';
                if ( function_exists( 'eZAppendWarningItem' ) )
                    eZAppendWarningItem( array( 'error' => array( 'type' => 'ezdb', 'number' => eZDBInterface::ERROR_MISSING_EXTENSION ), 'text' => $text ) );
                eZDebug::writeWarning( $text, __CLASS__ );
                $this->IsConnected = false;
                return;
            }
            $this->Transport = new expASEPDOTransport( $layer );
        }
        eZDebug::createAccumulatorGroup( 'sqlserver_total', 'SAP ASE Total' );

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
     * The effective [ASESettings]: the defaults, site.ini, then $overrides.
     *
     * @param array $overrides
     * @return array
     */
    public static function aseSettings( array $overrides = array() )
    {
        $settings = self::DEFAULT_SETTINGS;
        if ( class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance();
            if ( $ini->hasGroup( 'ASESettings' ) )
                foreach ( $ini->group( 'ASESettings' ) as $name => $value )
                    if ( array_key_exists( $name, $settings ) && $value !== '' && $value !== null )
                        $settings[$name] = $value;
        }
        foreach ( $overrides as $name => $value )
            $settings[$name] = $value;
        return $settings;
    }

    public function connectionConfig( $replica = false )
    {
        $s = $this->Settings;
        return array( 'host' => $this->Server, 'port' => $this->Port, 'database' => $this->DB, 'user' => $this->User, 'password' => $this->Password,
                      'login_timeout' => (int)$s['LoginTimeout'], 'app_name' => $s['ApplicationName'], 'odbc_driver' => $s['ODBCDriver'] );
    }

    /**
     * The session setup on a new connection, and what the server is (@@version, @@spid) on the first.
     *
     * @param expSQLServerTransport $transport
     * @return bool
     */
    protected function prepareSession( expSQLServerTransport $transport )
    {
        if ( $transport->run( expASEDialect::sessionSetup( (int)$this->Settings['LockTimeout'] ) ) === false )
            return false;
        if ( $this->ServerInfo === null && $transport === $this->Transport )
        {
            $result = $transport->run( 'SELECT @@version AS version, @@spid AS spid' );
            if ( $result === false || !isset( $result['sets'][0][0] ) )
                return false;
            $row = $result['sets'][0][0];
            $this->ServerInfo = expASEDialect::describeVersion( $row['version'] ) + array( 'spid' => (int)$row['spid'], 'rcsi' => false );
            if ( !$this->ServerInfo['supported'] )
                eZDebug::writeWarning( $this->ServerInfo['name'] . ' is not supported: ' . $this->ServerInfo['note'], __CLASS__ );
        }
        return true;
    }

    function databaseName()
    {
        return self::NAME;
    }

    // ------------------------------------------------------------------ statements

    /**
     * A statement as it goes to the server: in ASE's words (expASEDialect::rewrite()), an INSERT of several rows as
     * one batch of single-row INSERTs, the identity column handled, and SELECT @@identity after an INSERT that made
     * an id.
     *
     * @param string $sql
     * @return array keys: sql, params, identity_table, identity_insert
     */
    public function prepareStatement( $sql )
    {
        $sql = expASEDialect::rewrite( $sql );
        $kind = expASEDialect::statementKind( $sql );
        $identityTable = null;
        $identityInsert = null;
        if ( $kind === 'SELECT' && expSQLServerDialect::hasTrailingForUpdate( $sql ) )
            $sql = expASEDialect::forUpdate( expSQLServerDialect::withoutTrailingForUpdate( $sql ) );
        if ( $kind === 'INSERT' )
        {
            $parsed = expASEDialect::parseInsert( $sql );
            if ( $parsed !== null )
            {
                $column = $this->identityColumn( $parsed['table'] );
                if ( $column !== null )
                {
                    $rewrite = expASEDialect::rewriteIdentityInsert( $sql, $column );
                    $sql = $rewrite['sql'];
                    if ( $rewrite['identity_insert'] )
                        $identityInsert = $parsed['tableText'];
                    $identityTable = $parsed['table'];
                }
            }
            $rows = expASEDialect::splitMultiRowInsert( $sql );
            if ( $rows !== null )
                $sql = implode( "\n", $rows );
        }
        if ( $identityTable !== null )
            $sql .= "\nSELECT @@identity AS exp_identity";
        if ( $identityInsert !== null )
            $sql = 'SET IDENTITY_INSERT ' . $identityInsert . " ON\n" . $sql . "\nSET IDENTITY_INSERT " . $identityInsert . ' OFF';
        return array( 'sql' => $sql, 'params' => array(), 'identity_table' => $identityTable, 'identity_insert' => $identityInsert );
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
        // the kernel's own LIMIT at the end of the statement, when no limit is given
        $trailing = $limit === false && $offset === 0 ? expASEDialect::trailingLimit( $sql ) : null;
        if ( $trailing !== null )
            list( $sql, $limit, $offset ) = $trailing;
        $paged = expASEDialect::applyLimit( $sql, $limit, $offset );
        if ( $paged === null )
            return array();
        list( $pagedSql, $skip, $cut ) = $paged;
        $result = $this->execute( $pagedSql, $server, 'arrayQuery' );
        if ( $result === false )
            return false;
        $rows = isset( $result['sets'][0] ) ? $result['sets'][0] : array();
        $rows = array_slice( $rows, $skip, $cut || $limit === false ? null : $limit );
        $space = expASEDialect::flag( $this->Settings['EmptyStringSpace'] );
        $retArray = array();
        foreach ( $rows as $i => $row )
        {
            if ( $space )
                foreach ( $row as $key => $value )
                    if ( $value === ' ' )
                        $row[$key] = '';
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
     * The identity column of a table, or null; read once per connection for all tables (syscolumns, status 0x80).
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
        if ( $this->IdentityColumns === null )
            $this->loadIdentityColumns();
        if ( $table[0] === '#' && !array_key_exists( $key, $this->IdentityColumns ) )
        {
            $result = $this->Transport->run( 'SELECT c.name FROM tempdb..syscolumns c WHERE c.id = OBJECT_ID(' . expASEDialect::stringLiteral( 'tempdb..' . $table )
                                           . ') AND (c.status & 128) = 128' );
            $this->IdentityColumns[$key] = $result !== false && isset( $result['sets'][0][0]['name'] ) ? $result['sets'][0][0]['name'] : null;
        }
        return isset( $this->IdentityColumns[$key] ) ? $this->IdentityColumns[$key] : null;
    }

    protected function loadIdentityColumns()
    {
        $this->IdentityColumns = array();
        $result = $this->Transport->run( "SELECT o.name AS table_name, c.name AS column_name FROM syscolumns c JOIN sysobjects o ON o.id = c.id"
                                       . " WHERE o.type = 'U' AND (c.status & 128) = 128" );
        if ( $result === false || !isset( $result['sets'][0] ) )
            return;
        foreach ( $result['sets'][0] as $row )
            $this->IdentityColumns[strtolower( $row['table_name'] )] = $row['column_name'];
    }

    function lastSerialID( $table = false, $column = false )
    {
        if ( $this->LastInsert === null )
            return false;
        if ( $table !== false && $table !== null && $table !== '' && strcasecmp( trim( (string)$table, '[]`" ' ), $this->LastInsert[0] ) !== 0 )
            return false;
        $id = $this->LastInsert[1];
        return ( is_numeric( $id ) && abs( (float)$id ) < PHP_INT_MAX ) ? (int)$id : $id;
    }

    // ------------------------------------------------------------------ transactions and locks

    /**
     * MySQL's LOCK TABLES: a transaction that holds an exclusive lock of each table (LOCK TABLE ... IN EXCLUSIVE MODE).
     *
     * @param string|array $table
     */
    function lock( $table )
    {
        $this->begin();
        if ( !$this->IsConnected )
            return;
        foreach ( is_array( $table ) ? $table : array( $table ) as $entry )
        {
            $name = is_array( $entry ) ? $entry['table'] : $entry;
            $name = trim( preg_replace( '/\s+(READ|WRITE)$/i', '', trim( (string)$name ) ) );
            if ( $name !== '' )
                $this->query( 'LOCK TABLE ' . expASEDialect::quoteName( $name ) . ' IN EXCLUSIVE MODE' );
        }
    }

    /**
     * ASE has no application locks of the SQL Server kind.
     */
    public function getAppLock( $resource, $timeoutMs = 0, $mode = 'Exclusive' )
    {
        return false;
    }

    public function releaseAppLock( $resource )
    {
        return false;
    }

    // ------------------------------------------------------------------ temporary tables

    function createTempTable( $createTableQuery = '', $server = self::SERVER_SLAVE )
    {
        $temporary = expASEDialect::temporaryTable( $createTableQuery );
        if ( $temporary === null )
            return $this->query( $createTableQuery );
        list( $sql, $name ) = $temporary;
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
            $ok = $this->query( expASEDialect::dropTemporaryTable( $name ) );
            unset( $this->TempTables[strtolower( $name )] );
            return $ok;
        }
        return $this->query( $dropTableQuery );
    }

    // ------------------------------------------------------------------ SQL expressions

    function escapeString( $str )
    {
        return expASEDialect::escapeString( $str );
    }

    function subString( $string, $from, $len = null )
    {
        return expASEDialect::subString( $string, $from, $len );
    }

    function concatString( $strings = array() )
    {
        return expASEDialect::concatString( (array)$strings );
    }

    function md5( $str )
    {
        return expASEDialect::md5( $str );
    }

    function generateSQLINStatement( $elements, $columnName = '', $not = false, $unique = true, $type = false )
    {
        if ( !is_array( $elements ) )
            $elements = array( $elements );
        if ( $unique )
            $elements = array_values( array_unique( $elements ) );
        return expASEDialect::inList( $elements, $columnName, $not === true, $type, (int)$this->Settings['InListChunkSize'] );
    }

    public function sqlHelperDialect()
    {
        return 'ase';
    }

    public function sqlLiteral( $value )
    {
        return expASEDialect::literal( $value );
    }

    public function sqlLimit( $sql, $limit, $offset = 0 )
    {
        return expASEDialect::limitStatement( $sql, $limit, $offset );
    }

    public function sqlUpsert( $table, array $row, array $keyColumns, $updateColumns = null )
    {
        return expASEDialect::upsert( $table, $row, $keyColumns, $updateColumns );
    }

    public function sqlGroupConcat( $expression, $separator = ',', $orderBy = null )
    {
        return expASEDialect::groupConcat( $expression, $separator, $orderBy );
    }

    public function sqlUnixTimestamp( $expression = null )
    {
        return expASEDialect::unixTimestamp( $expression );
    }

    public function sqlFromUnixTimestamp( $expression )
    {
        return expASEDialect::fromUnixTimestamp( $expression );
    }

    public function sqlQuoteIdentifier( $name )
    {
        return expASEDialect::quoteName( $name );
    }

    public function sqlForUpdate( $sql )
    {
        return expASEDialect::forUpdate( $sql );
    }

    // ------------------------------------------------------------------ relations

    /**
     * The catalogue query of a relation type (the user objects of the current database). ASE has no sequences.
     *
     * @param int $relationType
     * @return string|null
     */
    public static function relationListQuery( $relationType )
    {
        switch ( $relationType )
        {
            case eZDBInterface::RELATION_TABLE:
                return "SELECT name FROM sysobjects WHERE type = 'U' ORDER BY name";
            case eZDBInterface::RELATION_TRIGGER:
                return "SELECT name FROM sysobjects WHERE type = 'TR' ORDER BY name";
            case eZDBInterface::RELATION_VIEW:
                return "SELECT name FROM sysobjects WHERE type = 'V' AND uid = user_id() ORDER BY name";
            case eZDBInterface::RELATION_INDEX:
                return "SELECT i.name FROM sysindexes i JOIN sysobjects o ON o.id = i.id WHERE o.type = 'U' AND i.indid > 0 AND i.indid < 255"
                     . ' AND (i.status & 2048) = 0 ORDER BY i.name';
        }
        return null;
    }

    function supportedRelationTypeMask()
    {
        return eZDBInterface::RELATION_TABLE_BIT | eZDBInterface::RELATION_TRIGGER_BIT | eZDBInterface::RELATION_VIEW_BIT | eZDBInterface::RELATION_INDEX_BIT;
    }

    function supportedRelationTypes()
    {
        return array( eZDBInterface::RELATION_TABLE, eZDBInterface::RELATION_TRIGGER, eZDBInterface::RELATION_VIEW, eZDBInterface::RELATION_INDEX );
    }

    function relationList( $relationType = eZDBInterface::RELATION_TABLE )
    {
        $sql = self::relationListQuery( $relationType );
        if ( $sql === null )
            return $relationType === eZDBInterface::RELATION_SEQUENCE ? array() : false;
        $rows = $this->arrayQuery( $sql, array( 'column' => 'name' ), eZDBInterface::SERVER_MASTER );
        return is_array( $rows ) ? array_values( $rows ) : false;
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
        if ( $relationType === eZDBInterface::RELATION_INDEX )
        {
            $rows = $this->arrayQuery( 'SELECT o.name AS table_name FROM sysindexes i JOIN sysobjects o ON o.id = i.id WHERE i.name = '
                                     . expASEDialect::stringLiteral( $relationName ), array( 'column' => 'table_name' ) );
            if ( !$rows )
                return false;
            return $this->query( 'DROP INDEX ' . expASEDialect::quoteIdentifier( reset( $rows ) ) . '.' . expASEDialect::quoteIdentifier( $relationName ) );
        }
        $words = array( eZDBInterface::RELATION_TABLE => 'TABLE', eZDBInterface::RELATION_TRIGGER => 'TRIGGER', eZDBInterface::RELATION_VIEW => 'VIEW' );
        if ( !isset( $words[$relationType] ) )
        {
            eZDebug::writeError( "Unknown relation type '$relationType'", __METHOD__ );
            return false;
        }
        return $this->query( 'DROP ' . $words[$relationType] . ' ' . expASEDialect::quoteName( $relationName ) );
    }

    /**
     * The columns of a table from the catalogue: name, type, length (bytes), precision, scale, nullable, identity,
     * default (its text).
     *
     * @param string $table
     * @return array[]|false
     */
    public function tableColumns( $table )
    {
        return $this->arrayQuery( 'SELECT c.name, t.name AS type, c.length AS max_length, c.prec AS [precision], c.scale,'
                                . ' CASE WHEN (c.status & 8) = 8 THEN 1 ELSE 0 END AS nullable, CASE WHEN (c.status & 128) = 128 THEN 1 ELSE 0 END AS [identity],'
                                . ' (SELECT cm.text FROM syscomments cm WHERE cm.id = c.cdefault AND cm.colid2 = 0 AND cm.colid = 1) AS [default]'
                                . ' FROM syscolumns c JOIN systypes t ON t.usertype = c.usertype'
                                . ' WHERE c.id = OBJECT_ID(' . expASEDialect::stringLiteral( $table ) . ') ORDER BY c.colid', array(), eZDBInterface::SERVER_MASTER );
    }

    // ------------------------------------------------------------------ server

    function isCharsetSupported( $charset )
    {
        return in_array( strtolower( (string)$charset ), array( 'utf-8', 'utf8' ), true );
    }

    function availableDatabases()
    {
        $rows = $this->arrayQuery( "SELECT name FROM master..sysdatabases WHERE name NOT IN ('master', 'model', 'tempdb', 'sybsystemprocs', 'sybsystemdb', 'sybsecurity')"
                                 . ' ORDER BY name', array( 'column' => 'name' ) );
        return is_array( $rows ) ? array_values( $rows ) : array();
    }

    function createDatabase( $dbName )
    {
        return $this->query( 'CREATE DATABASE ' . expASEDialect::quoteIdentifier( $dbName ) );
    }

    function removeDatabase( $dbName )
    {
        return $this->query( 'DROP DATABASE ' . expASEDialect::quoteIdentifier( $dbName ) );
    }

    function supportsDefaultValuesInsertion()
    {
        return false;
    }
}
