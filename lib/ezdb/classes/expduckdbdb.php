<?php
/**
 * File containing the expDuckDBDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The DuckDB database driver (DatabaseImplementation=duckdb): a whole Exponential site, or a data table connection,
 * in one DuckDB file. DuckDB is an embedded, columnar SQL database; PHP reaches its C library (libduckdb) through
 * the FFI extension (expDuckDBFFI).
 *
 * site.ini:
 *
 *   [DatabaseSettings]
 *   DatabaseImplementation=duckdb
 *   Database=site.duckdb          a name: var/storage/duckdb/<name>; an absolute path as it is
 *
 *   [DuckDBSettings]              see settings/site.ini (Library, AccessMode, LockWait ...)
 *
 * One process writes at a time. DuckDB locks the file: one process may open it to write, or any number to read, never
 * both. So the driver opens the file read-only first (AccessMode=auto) and only for the first statement that writes,
 * or the first transaction, opens it again to write, waiting (LockWait seconds) while another process has it. Every
 * request closes the file when it ends, so the next one can have it. A site with many concurrent writers therefore
 * queues its requests one behind the other: DuckDB suits small and read-mostly sites, single-editor installations,
 * build and demo sites, and analytics; it is not a database for a busy site with many editors.
 *
 * The SQL is PostgreSQL's (sequences, ||, ON CONFLICT ...): see expDuckDBDialect for what is changed on the way.
 * databaseName() is 'duckdb'; error numbers are the SQLSTATE PostgreSQL gives the same error ('23505' duplicate key).
 *
 * @since 6.0.15
 * @package lib
 */
class expDuckDBDB extends eZDBInterface
{
    /** The directory a database named without a path lives in, relative to the installation root */
    const STORAGE_DIRECTORY = 'var/storage/duckdb';

    /** The defaults of [DuckDBSettings] */
    const DEFAULT_SETTINGS = array(
        'Library' => '',
        'AccessMode' => 'auto',
        'LockWait' => '',
        'LockWaitWeb' => 10,
        'LockWaitCommandLine' => 120,
        'Threads' => 0,
        'MemoryLimit' => '',
        'TempDirectory' => '',
        'IntegerDivision' => 'enabled',
        'Config' => array(),
    );

    /** @var array the effective [DuckDBSettings] */
    protected $Settings = array();

    /** @var expDuckDBFFI|object the library binding (or a stand-in with the same methods, for tests) */
    protected $Engine = null;

    /** @var string the database file */
    protected $Path = '';

    /** @var string|null READ_ONLY or READ_WRITE: how the file is open now; null when closed */
    protected $OpenMode = null;

    /** @var string[] SET statements of this connection, replayed when the file is opened again */
    protected $SessionStatements = array();

    /** @var bool whether a DuckDB transaction is open */
    protected $InSQLTransaction = false;

    /** @var Closure|null the handler that lets the file go at the end of a request (made once per instance) */
    protected $ReleaseHandler = null;

    /** @var bool whether release() closed the file (a later statement opens it again) */
    protected $Released = false;

    /** @var int statements run, for the debug output */
    public $NumQueries = 0;

    /**
     * @param array $parameters the eZDB parameters; 'duckdb' => array( <[DuckDBSettings] name> => value ) over
     *                          site.ini, 'duckdb_engine' => a stand-in for expDuckDBFFI (tests)
     * @throws eZDBNoConnectionException when the file cannot be opened
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
        // One file on one server: a read replica does not apply (as for SQLite)
        $this->UseSlaveServer = false;
        $this->Settings = self::settings( isset( $parameters['duckdb'] ) && is_array( $parameters['duckdb'] ) ? $parameters['duckdb'] : array() );
        $this->Path = self::filePath( $this->DB );
        eZDebug::createAccumulatorGroup( 'duckdb_total', 'DuckDB Total' );

        if ( isset( $parameters['duckdb_engine'] ) && is_object( $parameters['duckdb_engine'] ) )
            $this->Engine = $parameters['duckdb_engine'];
        else
        {
            try
            {
                $this->Engine = new expDuckDBFFI( self::library( $this->Settings ) );
            }
            catch ( RuntimeException $e )
            {
                $this->ErrorMessage = $e->getMessage();
                $this->ErrorNumber = eZDBInterface::ERROR_MISSING_EXTENSION;
                if ( function_exists( 'eZAppendWarningItem' ) )
                    eZAppendWarningItem( array( 'error' => array( 'type' => 'ezdb', 'number' => eZDBInterface::ERROR_MISSING_EXTENSION ),
                                                'text' => $this->ErrorMessage ) );
                eZDebug::writeError( $this->ErrorMessage, __CLASS__ );
                $this->IsConnected = false;
                throw new eZDBNoConnectionException( $this->Path, $this->ErrorMessage, $this->ErrorNumber );
            }
        }

        if ( $this->DB === null || $this->DB === '' || $this->DB === false )
        {
            // No database named yet (the setup wizard asks the driver before it has one)
            $this->IsConnected = false;
            return;
        }
        $mode = $this->accessMode() === 'read_only' || ( $this->accessMode() === 'auto' && $this->Path !== ':memory:' && is_file( $this->Path ) )
              ? 'READ_ONLY' : 'READ_WRITE';
        if ( !$this->openFile( $mode ) )
            throw new eZDBNoConnectionException( $this->Path, $this->ErrorMessage, $this->ErrorNumber );
    }

    /**
     * The effective [DuckDBSettings]: the defaults, site.ini, then $overrides.
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
            if ( $ini->hasGroup( 'DuckDBSettings' ) )
                foreach ( $ini->group( 'DuckDBSettings' ) as $name => $value )
                    if ( array_key_exists( $name, $settings ) && $value !== '' && $value !== null )
                        $settings[$name] = $value;
        }
        foreach ( $overrides as $name => $value )
            $settings[$name] = $value;
        return $settings;
    }

    /**
     * The library file: Library of the settings, else the environment's DUCKDB_LIBRARY, else the system's name.
     *
     * @param array $settings
     * @return string
     */
    public static function library( array $settings )
    {
        if ( isset( $settings['Library'] ) && trim( (string)$settings['Library'] ) !== '' )
            return trim( (string)$settings['Library'] );
        $env = getenv( 'DUCKDB_LIBRARY' );
        if ( is_string( $env ) && $env !== '' )
            return $env;
        return expDuckDBFFI::defaultLibrary();
    }

    /**
     * The file a DatabaseSettings Database value names: an absolute path as it is, ":memory:", or a name in
     * STORAGE_DIRECTORY (".duckdb" added to a name without an extension). var/storage is sent to index.php by the
     * shipped .htaccess and left out by Velocity's static files, so the file is never served.
     *
     * @param string $name
     * @return string
     */
    public static function filePath( $name )
    {
        $name = trim( (string)$name );
        if ( $name === '' || $name === ':memory:' )
            return $name;
        if ( $name[0] === '/' || preg_match( '#^[A-Za-z]:[\\\\/]#', $name ) )
            return $name;
        if ( pathinfo( $name, PATHINFO_EXTENSION ) === '' )
            $name .= '.duckdb';
        return self::STORAGE_DIRECTORY . '/' . $name;
    }

    /**
     * auto, read_write or read_only.
     *
     * @return string
     */
    public function accessMode()
    {
        $mode = strtolower( trim( (string)$this->Settings['AccessMode'] ) );
        return in_array( $mode, array( 'auto', 'read_write', 'read_only' ), true ) ? $mode : 'auto';
    }

    /**
     * Seconds to wait for another process's lock on the file before an open fails.
     *
     * @return float
     */
    public function lockWait()
    {
        if ( is_numeric( $this->Settings['LockWait'] ) )
            return max( 0.0, (float)$this->Settings['LockWait'] );
        return max( 0.0, (float)( PHP_SAPI === 'cli' ? $this->Settings['LockWaitCommandLine'] : $this->Settings['LockWaitWeb'] ) );
    }

    /**
     * DuckDB's configuration for an open.
     *
     * @param string $mode READ_ONLY or READ_WRITE
     * @return array
     */
    public function openConfig( $mode )
    {
        $config = array( 'access_mode' => $mode );
        if ( (int)$this->Settings['Threads'] > 0 )
            $config['threads'] = (string)(int)$this->Settings['Threads'];
        if ( trim( (string)$this->Settings['MemoryLimit'] ) !== '' )
            $config['memory_limit'] = trim( (string)$this->Settings['MemoryLimit'] );
        if ( trim( (string)$this->Settings['TempDirectory'] ) !== '' )
            $config['temp_directory'] = trim( (string)$this->Settings['TempDirectory'] );
        foreach ( (array)$this->Settings['Config'] as $name => $value )
            if ( preg_match( '/^[a-z_]+$/', (string)$name ) )
                $config[$name] = (string)$value;
        if ( $this->Path === ':memory:' )
            unset( $config['access_mode'] );
        return $config;
    }

    /**
     * Opens the file in a mode, waiting for another process's lock up to lockWait(). Applies the connection settings
     * and replays this connection's SET statements.
     *
     * @param string $mode READ_ONLY or READ_WRITE
     * @return bool
     */
    protected function openFile( $mode )
    {
        if ( $this->Path !== ':memory:' )
        {
            $dir = dirname( $this->Path );
            if ( !is_dir( $dir ) )
                @mkdir( $dir, class_exists( 'eZDir' ) ? eZDir::dirMode( 0775 ) : 0775, true );
            if ( $mode === 'READ_ONLY' && !is_file( $this->Path ) )
                $mode = 'READ_WRITE';   // a file that is not there yet can only be made
        }
        $deadline = microtime( true ) + $this->lockWait();
        $sleep = 0.01;
        while ( true )
        {
            if ( $this->Engine->open( $this->Path, $this->openConfig( $mode ) ) )
                break;
            $message = (string)$this->Engine->Error;
            if ( !expDuckDBDialect::isLockError( $message ) || microtime( true ) + $sleep > $deadline )
            {
                $this->ErrorMessage = $message;
                $this->ErrorNumber = expDuckDBDialect::errorCode( expDuckDBFFI::ERROR_IO, $message );
                $this->IsConnected = false;
                $this->OpenMode = null;
                eZDebug::writeError( "DuckDB could not open {$this->Path}: $message", __CLASS__ );
                return false;
            }
            usleep( (int)( $sleep * 1000000 ) );
            $sleep = min( 0.25, $sleep * 2 );
        }
        $this->OpenMode = $mode;
        $this->IsConnected = true;
        $this->InSQLTransaction = false;
        $statements = array();
        if ( self::flag( $this->Settings['IntegerDivision'] ) )
            $statements[] = 'SET integer_division = true';
        foreach ( array_merge( $statements, $this->SessionStatements ) as $sql )
            $this->Engine->query( $sql, false );
        $this->Released = false;
        if ( self::isWebProcess() )
            $this->registerRelease();
        return true;
    }

    /**
     * Whether this process serves web requests (PHP-FPM, Apache's module, Velocity's workers), where the file must be
     * let go at the end of each request; a command line script has it until it ends.
     *
     * @return bool
     */
    public static function isWebProcess()
    {
        return PHP_SAPI !== 'cli' || defined( 'QBIX_WEBSERVER' ) || isset( $_SERVER['QBIX_WORKER'] )
               || isset( $_SERVER['VELOCITY'] ) || class_exists( 'Q_WebServer', false );
    }

    /**
     * Makes the end of the request let the file go, so the next request (another PHP-FPM worker, another Velocity
     * worker) can open it: the kernel's cleanup at the end of every request (eZExecution, also in a persistent
     * worker), the start of the next request in a persistent worker (expKernelServiceScope), and PHP's shutdown.
     */
    protected function registerRelease()
    {
        if ( $this->ReleaseHandler === null )
        {
            $self = $this;
            $this->ReleaseHandler = function () use ( $self ) { $self->release(); };
            register_shutdown_function( $this->ReleaseHandler );
        }
        if ( class_exists( 'eZExecution' ) )
            eZExecution::addCleanupHandler( $this->ReleaseHandler );
        if ( class_exists( 'expKernelServiceScope' ) )
            expKernelServiceScope::onRequestReset( 'duckdb-release-' . spl_object_id( $this ), $this->ReleaseHandler );
    }

    /**
     * Lets the file go at the end of a request: like close(), but a statement after it (code that still writes in
     * a later cleanup handler) opens the file again, as at the start of a request.
     */
    public function release()
    {
        if ( $this->OpenMode === null )
            return;
        $this->close();
        $this->Released = true;
    }

    /**
     * Opens the file again after release().
     *
     * @return bool
     */
    protected function reopenAfterRelease()
    {
        if ( !$this->Released || $this->Engine === null )
            return false;
        $this->Released = false;
        $mode = $this->accessMode() === 'read_write' ? 'READ_WRITE' : 'READ_ONLY';
        return $this->openFile( $mode );
    }

    /**
     * Makes sure the file is open to write: opens it again read-write when it is open read-only. Only between
     * transactions (a transaction always starts read-write).
     *
     * @return bool
     */
    public function ensureWritable()
    {
        if ( $this->OpenMode === 'READ_WRITE' )
            return true;
        if ( $this->accessMode() === 'read_only' )
        {
            $this->ErrorMessage = 'The DuckDB database is open read only ([DuckDBSettings] AccessMode=read_only)';
            $this->ErrorNumber = '25006';
            return false;
        }
        $this->Engine->close();
        $this->OpenMode = null;
        if ( $this->openFile( 'READ_WRITE' ) )
            return true;
        // keep reading at least
        $error = array( $this->ErrorMessage, $this->ErrorNumber );
        $this->openFile( 'READ_ONLY' );
        list( $this->ErrorMessage, $this->ErrorNumber ) = $error;
        return false;
    }

    /**
     * How the file is open: READ_ONLY, READ_WRITE, or null when closed.
     *
     * @return string|null
     */
    public function openMode()
    {
        return $this->OpenMode;
    }

    /**
     * The database file.
     *
     * @return string
     */
    public function path()
    {
        return $this->Path;
    }

    function databaseName()
    {
        return 'duckdb';
    }

    public function sqlHelperDialect()
    {
        return 'duckdb';
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
     * Sends one statement; the raw result of the binding, or false.
     *
     * @param string $sql
     * @param bool $wantRows
     * @return array|bool true for a statement that was not sent (LOCK TABLE)
     */
    protected function run( $sql, $wantRows )
    {
        if ( !$this->IsConnected && !$this->reopenAfterRelease() )
            return false;
        $sent = expDuckDBDialect::rewrite( $sql );
        if ( $sent === null )
            return true;
        if ( !$this->InSQLTransaction && $this->OpenMode === 'READ_ONLY' && expDuckDBDialect::isWrite( $sent ) )
        {
            if ( !$this->ensureWritable() )
                return false;
        }
        if ( expDuckDBDialect::isSessionStatement( $sent ) )
            $this->SessionStatements[] = $sent;

        eZDebug::accumulatorStart( 'duckdb_query', 'duckdb_total', 'DuckDB queries' );
        if ( $this->OutputSQL )
            $this->startTimer();
        $result = $this->Engine->query( $sent, $wantRows );
        // a write the classification missed, on a read-only file: open it to write and send it again
        if ( $result === false && !$this->InSQLTransaction && $this->OpenMode === 'READ_ONLY'
             && expDuckDBDialect::isReadOnlyError( $this->Engine->Error ) && $this->ensureWritable() )
            $result = $this->Engine->query( $sent, $wantRows );
        $this->NumQueries++;
        if ( $this->OutputSQL )
        {
            $this->endTimer();
            if ( $this->timeTaken() > $this->SlowSQLTimeout )
                $this->reportQuery( __CLASS__, $sent, $result ? count( $result['rows'] ) : false, $this->timeTaken() );
        }
        eZDebug::accumulatorStop( 'duckdb_query' );

        if ( $result === false )
        {
            $this->ErrorMessage = (string)$this->Engine->Error;
            $this->ErrorNumber = expDuckDBDialect::errorCode( (int)$this->Engine->ErrorType, $this->ErrorMessage );
            // DuckDB ends a transaction a statement failed in: it is rolled back at the next statement
            eZDebug::writeError( self::failedQueryMessage( $this->ErrorMessage, $sent ), __CLASS__ );
            if ( $this->errorHandling == eZDB::ERROR_HANDLING_EXCEPTIONS )
                throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
            $this->reportError();
            return false;
        }
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
        eZDBQueryCache::noteWrite( $this, $sent );
        return $result;
    }

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
        if ( !$this->IsConnected && !$this->reopenAfterRelease() )
            return array();
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
            $limited = expDBSQLHelpers::limit( 'duckdb', $sql, $limit !== false && $limit >= 0 ? $limit : false, $offset );
            if ( $limited === null )
                return array();
            $sql = $limited;
        }
        $result = $this->run( $sql, true );
        if ( !is_array( $result ) )
            return $result === false ? false : array();
        $rows = array();
        foreach ( $result['rows'] as $i => $row )
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

    function beginQuery()
    {
        if ( !$this->IsConnected && !$this->reopenAfterRelease() )
            return false;
        if ( $this->OpenMode !== 'READ_WRITE' && !$this->ensureWritable() )
            return false;
        $ok = $this->query( 'BEGIN TRANSACTION' );
        $this->InSQLTransaction = $ok;
        return $ok;
    }

    function commitQuery()
    {
        $this->InSQLTransaction = false;
        return $this->query( 'COMMIT' );
    }

    function rollbackQuery()
    {
        $this->InSQLTransaction = false;
        if ( !$this->IsConnected )
            return false;
        // a failed statement has ended DuckDB's transaction already: no error for that
        $this->Engine->query( 'ROLLBACK', false );
        return true;
    }

    /**
     * No table locks in DuckDB: one process writes at a time. A transaction keeps the writes together.
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
     * The value nextval() gave last for the column's sequence <table>_<column>_seq in this connection.
     */
    function lastSerialID( $table = false, $column = false )
    {
        if ( !$this->IsConnected || !$table )
            return false;
        $column = $column ? $column : 'id';
        $sequence = $this->escapeString( $table . '_' . $column . '_seq' );
        $rows = $this->arrayQuery( "SELECT currval( '$sequence' ) AS id" );
        return isset( $rows[0]['id'] ) ? (int)$rows[0]['id'] : false;
    }

    function escapeString( $str )
    {
        return expDuckDBDialect::escape( $str );
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
     * The catalog query listing the names of one kind of relation of the main schema.
     *
     * @param int $relationType
     * @return string|null
     */
    public static function relationListQuery( $relationType )
    {
        switch ( $relationType )
        {
            case eZDBInterface::RELATION_TABLE:
                return "SELECT table_name AS name FROM duckdb_tables() WHERE schema_name = 'main' AND NOT temporary AND database_name = current_database() ORDER BY table_name";
            case eZDBInterface::RELATION_SEQUENCE:
                return "SELECT sequence_name AS name FROM duckdb_sequences() WHERE schema_name = 'main' AND NOT temporary AND database_name = current_database() ORDER BY sequence_name";
            case eZDBInterface::RELATION_VIEW:
                return "SELECT view_name AS name FROM duckdb_views() WHERE schema_name = 'main' AND NOT internal AND NOT temporary AND database_name = current_database() ORDER BY view_name";
            case eZDBInterface::RELATION_INDEX:
                return "SELECT index_name AS name FROM duckdb_indexes() WHERE schema_name = 'main' AND database_name = current_database() ORDER BY index_name";
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
     * Sets every sequence a column default reads (nextval('<seq>')) to the column's highest value, after rows were
     * inserted with their ids (an installation's data, an import). setval() where DuckDB has it (2.0), else the
     * sequence is advanced with nextval() as far as needed (DuckDB 1.x has no setval()).
     *
     * @return bool
     */
    function correctSequenceValues()
    {
        if ( !$this->IsConnected )
            return false;
        $rows = $this->arrayQuery( "SELECT table_name, column_name, column_default FROM duckdb_columns()
                                    WHERE schema_name = 'main' AND NOT internal AND column_default LIKE 'nextval(%'" );
        if ( !is_array( $rows ) )
            return false;
        $hasSetval = (bool)$this->arrayQuery( "SELECT 1 AS x FROM duckdb_functions() WHERE function_name = 'setval' LIMIT 1" );
        foreach ( $rows as $row )
        {
            if ( !preg_match( "/nextval\\(\\s*(?:CAST\\(\\s*)?'([^']+)'/i", (string)$row['column_default'], $m ) )
                continue;
            $sequence = $m[1];
            $table = $row['table_name'];
            $column = $row['column_name'];
            if ( !preg_match( '/^[A-Za-z0-9_]+$/', $table . $column . $sequence ) )
                continue;
            $max = $this->arrayQuery( "SELECT MAX($column) AS m FROM $table" );
            $max = isset( $max[0]['m'] ) ? (int)$max[0]['m'] : 0;
            if ( $max < 1 )
                continue;
            if ( $hasSetval )
            {
                $this->query( "SELECT setval( '$sequence', $max )" );
                continue;
            }
            $last = $this->arrayQuery( "SELECT last_value FROM duckdb_sequences() WHERE sequence_name = '$sequence' AND schema_name = 'main'" );
            $last = isset( $last[0]['last_value'] ) && $last[0]['last_value'] !== null ? (int)$last[0]['last_value'] : 0;
            if ( $last < $max )
                $this->query( "SELECT MAX( nextval( '$sequence' ) ) FROM range( " . ( $max - $last ) . " )" );
        }
        return true;
    }

    /**
     * The columns of a table: name => array( 'type' => DuckDB type, 'nullable' => bool, 'default' => text|null ).
     *
     * @param string $table
     * @return array
     */
    public function tableColumns( $table )
    {
        $columns = array();
        $rows = $this->arrayQuery( "SELECT column_name, data_type, is_nullable, column_default FROM duckdb_columns()
                                    WHERE schema_name = 'main' AND table_name = '" . $this->escapeString( $table ) . "' ORDER BY column_index" );
        foreach ( (array)$rows as $row )
            $columns[$row['column_name']] = array( 'type' => $row['data_type'], 'nullable' => $row['is_nullable'] === '1' || $row['is_nullable'] === 'true',
                                                   'default' => $row['column_default'] );
        return $columns;
    }

    function databaseServerVersion()
    {
        if ( !$this->IsConnected )
            return false;
        $rows = $this->arrayQuery( 'SELECT version() AS v' );
        $text = isset( $rows[0]['v'] ) ? ltrim( (string)$rows[0]['v'], 'v' ) : '';
        return array( 'string' => $text, 'values' => explode( '.', $text ) );
    }

    function databaseClientVersion()
    {
        $text = $this->Engine && method_exists( $this->Engine, 'libraryVersion' ) ? ltrim( (string)$this->Engine->libraryVersion(), 'v' ) : '';
        return array( 'string' => $text, 'values' => explode( '.', $text ) );
    }

    function isCharsetSupported( $charset )
    {
        // DuckDB keeps text as UTF-8
        return in_array( strtolower( (string)$charset ), array( 'utf-8', 'utf8' ), true );
    }

    /**
     * The DuckDB files in the storage directory.
     */
    function availableDatabases()
    {
        $names = array();
        foreach ( (array)glob( self::STORAGE_DIRECTORY . '/*.duckdb' ) as $file )
            $names[] = basename( $file );
        return $names;
    }

    /**
     * A database is a file: it is made by opening it.
     */
    function createDatabase( $dbName )
    {
        $path = self::filePath( $dbName );
        if ( $path === '' || $path === ':memory:' || is_file( $path ) )
            return;
        $engine = new expDuckDBFFI( self::library( $this->Settings ) );
        if ( !is_dir( dirname( $path ) ) )
            @mkdir( dirname( $path ), 0775, true );
        if ( $engine->open( $path, array( 'access_mode' => 'READ_WRITE' ) ) )
            $engine->close();
        else
        {
            $this->ErrorMessage = $engine->Error;
            $this->ErrorNumber = '58030';
        }
    }

    /**
     * Empties the open database (its views, indexes, tables and sequences are dropped: the setup's "remove the
     * existing data"), or removes the file of another one (and its write-ahead log).
     *
     * @param string $dbName
     * @return bool
     */
    function removeDatabase( $dbName )
    {
        $path = self::filePath( $dbName );
        if ( $path === '' )
            return false;
        if ( $path === ':memory:' || ( $this->IsConnected && ( $path === $this->Path || realpath( $path ) === realpath( $this->Path ) ) ) )
        {
            if ( !$this->IsConnected || !$this->ensureWritable() )
                return false;
            $removed = 0;
            foreach ( array( eZDBInterface::RELATION_VIEW, eZDBInterface::RELATION_INDEX, eZDBInterface::RELATION_TABLE,
                             eZDBInterface::RELATION_SEQUENCE ) as $type )
                foreach ( (array)$this->relationList( $type ) as $name )
                    if ( $this->Engine->query( 'DROP ' . $this->relationName( $type ) . ' IF EXISTS "' . str_replace( '"', '""', $name ) . '"'
                                               . ( $type === eZDBInterface::RELATION_TABLE || $type === eZDBInterface::RELATION_SEQUENCE ? ' CASCADE' : '' ), false ) )
                        $removed++;
            $this->Engine->query( 'CHECKPOINT', false );
            eZDebug::writeNotice( "Emptied DuckDB database '$dbName': dropped $removed object(s)", __METHOD__ );
            return true;
        }
        foreach ( array( $path, $path . '.wal' ) as $file )
            if ( is_file( $file ) )
                @unlink( $file );
        return true;
    }

    function supportsDefaultValuesInsertion()
    {
        return true;
    }

    /**
     * Closes the file, which lets other processes open it to write. A transaction left open is rolled back.
     */
    function close()
    {
        if ( $this->Engine && $this->OpenMode !== null )
        {
            if ( $this->InSQLTransaction )
                $this->Engine->query( 'ROLLBACK', false );
            $this->Engine->close();
        }
        $this->InSQLTransaction = false;
        $this->OpenMode = null;
        $this->IsConnected = false;
    }

    function setError()
    {
        if ( $this->Engine && $this->Engine->Error !== '' )
        {
            $this->ErrorMessage = (string)$this->Engine->Error;
            $this->ErrorNumber = expDuckDBDialect::errorCode( (int)$this->Engine->ErrorType, $this->ErrorMessage );
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
