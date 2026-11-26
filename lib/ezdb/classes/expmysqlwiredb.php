<?php
/**
 * File containing the expMySQLWireDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The MySQL driver for a database that speaks the MySQL protocol but is not MySQL: TiDB (expTiDBDB), Vitess and
 * PlanetScale (expVitessDB), SingleStore (expSingleStoreDB), MariaDB Xpand (expXpandDB) and Amazon Aurora MySQL
 * (expAuroraMySQLDB). Each of those is this class with its profile (expMySQLWireProfile).
 *
 * databaseName() stays 'mysql': the kernel's MySQL statements, the MySQL schema handler (eZMysqlSchema) and the
 * MySQL cluster backend run as they are, and this driver sends each statement the way the server takes it
 * (expMySQLWireProfile::rewriteStatement()): BEGIN instead of BEGIN WORK, foreign keys and FULLTEXT keys left out
 * where they are refused, a case-insensitive collation where the server's default is binary, TiDB's AUTO_ID_CACHE,
 * SingleStore's reference tables. Table locks become GET_LOCK() locks where LOCK TABLES does nothing (TiDB, Xpand).
 *
 * Settings: [MySQLWireSettings] of site.ini, or the parameter 'mysqlwire' => array( <name> => value ) of
 * eZDB::instance(), which wins.
 */
class expMySQLWireDB extends eZMySQLiDB
{
    /** The profile of the class; a subclass names its own */
    const PROFILE = expMySQLWireProfile::MYSQL;

    /** @var array the defaults of [MySQLWireSettings] */
    const DEFAULT_SETTINGS = array(
        // empty: the profile of the driver class
        'Profile' => '',
        // disabled, warning (one more statement per connection: the server is asked what it is)
        'VerifyServer' => 'disabled',
        // empty: the server's default
        'IsolationLevel' => '',
        // auto (the profile's), native (LOCK TABLES), named (GET_LOCK), none
        'TableLocks' => 'auto',
        'NamedLockTimeout' => '30',
        // TiDB: AUTO_ID_CACHE of a new table with an auto_increment column; 1 gives MySQL's order of ids, 0: TiDB's default
        'TiDBAutoIdCache' => '1',
        // disabled, enabled (TLS, the server's certificate is not checked), verify (checked against SSLCA)
        'SSL' => 'disabled',
        'SSLCA' => '',
        'SSLCert' => '',
        'SSLKey' => '',
        // Aurora: after connecting, a writer that turned out read-only (a failover still in DNS) is connected to again
        'FailoverCheck' => 'auto',
        'FailoverRetries' => '5',
        'FailoverWait' => '1',
    );

    /** @var string the profile name */
    protected $Profile;

    /** @var array the effective [MySQLWireSettings] */
    protected $WireSettings = array();

    /** @var string[] the GET_LOCK() names lock() took */
    protected $NamedLocks = array();

    /**
     * @param array $parameters as eZMySQLiDB, plus 'mysqlwire' => array( <[MySQLWireSettings] name> => value )
     */
    public function __construct( $parameters )
    {
        $this->WireSettings = self::settings( isset( $parameters['mysqlwire'] ) && is_array( $parameters['mysqlwire'] ) ? $parameters['mysqlwire'] : array() );
        $this->Profile = expMySQLWireProfile::normalizeName( $this->WireSettings['Profile'] !== '' ? $this->WireSettings['Profile'] : static::PROFILE );
        // an unknown profile is an error of the settings: said here, before a connection is opened
        expMySQLWireProfile::get( $this->Profile );

        parent::__construct( $parameters );

        if ( $this->IsConnected && $this->DBWriteConnection )
            $this->setUpSession( $this->DBWriteConnection );
    }

    /**
     * The effective [MySQLWireSettings]: the defaults, site.ini, then $overrides.
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
            if ( $ini->hasGroup( 'MySQLWireSettings' ) )
                foreach ( $ini->group( 'MySQLWireSettings' ) as $name => $value )
                    if ( array_key_exists( $name, $settings ) && is_scalar( $value ) && $value !== '' )
                        $settings[$name] = (string)$value;
        }
        foreach ( $overrides as $name => $value )
            if ( is_scalar( $value ) )
                $settings[$name] = (string)$value;
        return $settings;
    }

    /**
     * The profile name of this connection.
     *
     * @return string
     */
    public function mysqlWireProfile()
    {
        return $this->Profile;
    }

    /**
     * The facts of the profile (expMySQLWireProfile::definitions()).
     *
     * @return array
     */
    public function mysqlWireFacts()
    {
        return expMySQLWireProfile::get( $this->Profile );
    }

    /**
     * The effective settings.
     *
     * @return array
     */
    public function mysqlWireSettings()
    {
        return $this->WireSettings;
    }

    /**
     * The table lock method: native, named or none.
     *
     * @return string
     */
    public function tableLockMethod()
    {
        $method = strtolower( $this->WireSettings['TableLocks'] );
        if ( !in_array( $method, array( 'native', 'named', 'none' ), true ) )
        {
            $facts = $this->mysqlWireFacts();
            $method = $facts['table_locks'];
            if ( $method === 'named' && !$facts['named_locks'] )
                $method = 'native';
        }
        return $method;
    }

    /**
     * Opens the connection, with TLS when [MySQLWireSettings] SSL says so (PlanetScale and most managed services
     * accept nothing else).
     */
    protected function openConnection( $server, $user, $password, $port, $socketPath )
    {
        $ssl = strtolower( $this->WireSettings['SSL'] );
        if ( $ssl !== 'enabled' && $ssl !== 'verify' )
            return parent::openConnection( $server, $user, $password, $port, $socketPath );

        $link = mysqli_init();
        if ( !$link )
            return false;
        $ca = $this->WireSettings['SSLCA'];
        if ( $ssl === 'verify' && $ca === '' )
        {
            // the CA bundle of the system, where PHP knows it
            $defaults = function_exists( 'openssl_get_cert_locations' ) ? openssl_get_cert_locations() : array();
            if ( !empty( $defaults['default_cert_file'] ) && is_readable( $defaults['default_cert_file'] ) )
                $ca = $defaults['default_cert_file'];
        }
        mysqli_ssl_set( $link,
                        $this->WireSettings['SSLKey'] !== '' ? $this->WireSettings['SSLKey'] : null,
                        $this->WireSettings['SSLCert'] !== '' ? $this->WireSettings['SSLCert'] : null,
                        $ca !== '' ? $ca : null, null, null );
        $flags = MYSQLI_CLIENT_SSL;
        if ( $ssl !== 'verify' )
            $flags |= MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
        if ( !mysqli_real_connect( $link, $server, $user, $password, null, (int)$port, $socketPath ? $socketPath : null, $flags ) )
            return false;
        return $link;
    }

    /**
     * What a new connection needs: the isolation level, the server check, Aurora's writer check.
     *
     * @param mysqli $connection
     */
    protected function setUpSession( $connection )
    {
        $level = expMySQLWireProfile::isolationLevel( $this->Profile, $this->WireSettings['IsolationLevel'] );
        if ( $level !== null )
            $this->rawQuery( $connection, "SET SESSION TRANSACTION ISOLATION LEVEL $level" );
        else if ( trim( $this->WireSettings['IsolationLevel'] ) !== '' )
            eZDebug::writeWarning( "[MySQLWireSettings] IsolationLevel={$this->WireSettings['IsolationLevel']} is not one the "
                . $this->Profile . ' profile keeps; the server default is used', __METHOD__ );

        if ( strtolower( $this->WireSettings['VerifyServer'] ) === 'warning' )
        {
            $detected = $this->detectServerProfile();
            if ( $detected !== null && $detected !== $this->Profile )
                eZDebug::writeWarning( "The database server is $detected, the driver's profile is {$this->Profile}", __METHOD__ );
        }

        if ( $this->failoverCheckEnabled() )
            $this->ensureWritablePrimary();
    }

    /**
     * True when connecting checks that the primary takes writes ([MySQLWireSettings] FailoverCheck; auto: the
     * profiles with failover, Aurora).
     *
     * @return bool
     */
    protected function failoverCheckEnabled()
    {
        $value = strtolower( $this->WireSettings['FailoverCheck'] );
        if ( $value === 'auto' )
        {
            $facts = $this->mysqlWireFacts();
            return $facts['failover_read_only'];
        }
        return $value === 'enabled';
    }

    /**
     * After a failover the cluster address can still lead to the old writer for some seconds, and it is read-only
     * now: every write would fail. Connects again (FailoverRetries times, FailoverWait seconds apart) until the
     * primary connection takes writes.
     *
     * @return bool true when the primary takes writes
     */
    protected function ensureWritablePrimary()
    {
        $retries = max( 0, (int)$this->WireSettings['FailoverRetries'] );
        for ( $attempt = 0; ; ++$attempt )
        {
            if ( !$this->primaryIsReadOnly() )
                return true;
            if ( $attempt >= $retries )
                break;
            eZDebug::writeNotice( 'The primary is read-only (a failover?): connecting again', __METHOD__ );
            $this->reopenPrimary( max( 0, (int)$this->WireSettings['FailoverWait'] ) );
        }
        eZDebug::writeError( 'The primary database server stays read-only: writes will fail', __METHOD__ );
        return false;
    }

    /**
     * @return bool true when the server of the primary connection is read-only
     */
    protected function primaryIsReadOnly()
    {
        $row = $this->rawRow( $this->DBWriteConnection, 'SELECT @@innodb_read_only' );
        return $row !== null && (string)reset( $row ) === '1';
    }

    /**
     * Closes the primary connection and opens it again.
     *
     * @param int $waitSeconds
     */
    protected function reopenPrimary( $waitSeconds )
    {
        if ( $this->DBWriteConnection instanceof mysqli )
        {
            try { @mysqli_close( $this->DBWriteConnection ); } catch ( Throwable $e ) {}
        }
        if ( $waitSeconds > 0 )
            sleep( $waitSeconds );
        $connection = $this->connect( $this->Server, $this->DB, $this->User, $this->Password, $this->SocketPath, $this->Charset, $this->Port );
        $this->DBWriteConnection = $connection;
        $this->DBConnection = $connection;
    }

    /**
     * The profile of the server the primary connection reached, from what it says about itself; null when it cannot
     * be asked.
     *
     * @return string|null
     */
    public function detectServerProfile()
    {
        if ( !$this->IsConnected || !$this->DBWriteConnection )
            return null;
        $row = $this->rawRow( $this->DBWriteConnection, 'SELECT VERSION(), @@version_comment' );
        if ( $row === null )
            return null;
        $row = array_values( $row );
        $variables = array();
        foreach ( array( 'aurora_version', 'memsql_version' ) as $name )
        {
            $value = $this->rawRow( $this->DBWriteConnection, "SELECT @@$name" );
            $variables[$name] = $value !== null ? reset( $value ) : null;
        }
        return expMySQLWireProfile::detect( (string)$row[0], (string)$row[1], $variables );
    }

    /**
     * Runs a statement of the driver itself, outside the error handling of query(): a refusal is not an error here.
     *
     * @param mysqli $connection
     * @param string $sql
     * @return bool
     */
    protected function rawQuery( $connection, $sql )
    {
        try
        {
            $result = @mysqli_query( $connection, $sql );
        }
        catch ( mysqli_sql_exception $e )
        {
            return false;
        }
        if ( $result instanceof mysqli_result )
            mysqli_free_result( $result );
        return $result !== false;
    }

    /**
     * The first row of a statement of the driver itself; null when it fails or has none.
     *
     * @param mysqli $connection
     * @param string $sql
     * @return array|null
     */
    protected function rawRow( $connection, $sql )
    {
        try
        {
            $result = @mysqli_query( $connection, $sql );
        }
        catch ( mysqli_sql_exception $e )
        {
            return null;
        }
        if ( !$result instanceof mysqli_result )
            return null;
        $row = mysqli_fetch_row( $result );
        mysqli_free_result( $result );
        return is_array( $row ) ? $row : null;
    }

    /**
     * The options of expMySQLWireProfile::rewriteStatement() from the settings.
     *
     * @return array
     */
    protected function rewriteOptions()
    {
        return array( 'auto_id_cache' => (int)$this->WireSettings['TiDBAutoIdCache'] );
    }

    /**
     * Runs the statement as the server takes it: none, one or more statements (expMySQLWireProfile::rewriteStatement()).
     */
    function query( $sql, $server = false )
    {
        $statements = expMySQLWireProfile::rewriteStatement( $this->Profile, $sql, $this->rewriteOptions() );
        if ( !$statements )
            return true;
        $result = true;
        foreach ( $statements as $statement )
        {
            $result = $this->runStatement( $statement, $server );
            if ( $result === false )
                break;
        }
        return $result;
    }

    /**
     * Runs one statement as the MySQL driver does.
     *
     * @param string $sql
     * @param int|false $server
     * @return mixed
     */
    protected function runStatement( $sql, $server )
    {
        return parent::query( $sql, $server );
    }

    /**
     * The character set of the database from information_schema: SingleStore refuses SHOW CREATE DATABASE, which the
     * MySQL driver reads. The answer is the MySQL driver's: true, the matching name of a list, or false.
     */
    function checkCharsetPriv( $charset, &$currentCharset )
    {
        $row = $this->rawRow( $this->DBConnection, 'SELECT DEFAULT_CHARACTER_SET_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '
                                                   . $this->sqlLiteral( (string)$this->DB ) );
        if ( $row === null || (string)reset( $row ) === '' )
            return true;
        return self::charsetVerdict( $charset, (string)reset( $row ), $currentCharset );
    }

    /**
     * Whether the database's character set is the one asked for (pure): true, the matching entry of a list, false.
     *
     * @param string|string[] $charset the real charset code(s) asked for
     * @param string $databaseCharset as the server names it (utf8mb4, latin1 ...)
     * @param string|null $currentCharset set to the database's charset code
     * @return bool|string
     */
    public static function charsetVerdict( $charset, $databaseCharset, &$currentCharset = null )
    {
        $currentCharset = eZCharsetInfo::realCharsetCode( $databaseCharset );
        $unmapped = eZMySQLCharset::mapFrom( $currentCharset );
        if ( is_array( $charset ) )
            return in_array( $unmapped, $charset ) ? $unmapped : false;
        return $unmapped == $charset;
    }

    function beginQuery()
    {
        // BEGIN WORK is refused by TiDB and Vitess; BEGIN means the same everywhere
        return $this->query( 'BEGIN' );
    }

    function lock( $table )
    {
        $method = $this->tableLockMethod();
        if ( $method === 'native' )
        {
            parent::lock( $table );
            return;
        }
        if ( $method === 'none' || !$this->IsConnected )
            return;

        // named: one GET_LOCK() per table, always in the same order, so two sessions cannot wait for each other
        $tables = array();
        foreach ( is_array( $table ) ? $table : array( array( 'table' => $table ) ) as $entry )
            $tables[] = is_array( $entry ) ? $entry['table'] : $entry;
        $tables = array_unique( $tables );
        sort( $tables );
        $timeout = max( 0, (int)$this->WireSettings['NamedLockTimeout'] );
        foreach ( $tables as $name )
        {
            $lockName = expMySQLWireProfile::namedLockName( (string)$this->DB, $name );
            $rows = $this->arrayQuery( 'SELECT GET_LOCK(' . $this->sqlLiteral( $lockName ) . ", $timeout) AS l" );
            if ( isset( $rows[0]['l'] ) && (string)$rows[0]['l'] === '1' )
                $this->NamedLocks[] = $lockName;
            else
                eZDebug::writeError( "Could not take the lock of table $name in $timeout seconds", __METHOD__ );
        }
    }

    function unlock()
    {
        if ( $this->tableLockMethod() === 'native' )
        {
            parent::unlock();
            return;
        }
        if ( !$this->IsConnected )
            return;
        foreach ( array_reverse( $this->NamedLocks ) as $lockName )
            $this->query( 'SELECT RELEASE_LOCK(' . $this->sqlLiteral( $lockName ) . ')' );
        $this->NamedLocks = array();
    }

    function createDatabase( $dbName )
    {
        $facts = $this->mysqlWireFacts();
        if ( !$facts['create_database'] )
        {
            // a keyspace or a managed database is made outside: there is nothing to do when it is there
            $databases = $this->availableDatabases();
            if ( is_array( $databases ) && in_array( $dbName, $databases ) )
                return;
            eZDebug::writeError( "The database $dbName does not exist, and {$facts['label']} does not create one with CREATE DATABASE", __METHOD__ );
            $this->ErrorMessage = "{$facts['label']}: create the database $dbName on the server's own console first";
            $this->ErrorNumber = 1049;
            return;
        }
        if ( $this->IsConnected )
        {
            $collation = $facts['binary_default_collation'] ? ' CHARACTER SET utf8mb4 COLLATE ' . expMySQLWireProfile::CASE_INSENSITIVE_COLLATION : '';
            $this->query( "CREATE DATABASE $dbName$collation" );
            $this->setError();
        }
    }

    function removeDatabase( $dbName )
    {
        $facts = $this->mysqlWireFacts();
        if ( $facts['create_database'] )
        {
            parent::removeDatabase( $dbName );
            return;
        }
        // a keyspace stays: its tables are dropped
        if ( !$this->IsConnected )
            return;
        $rows = $this->arrayQuery( 'SHOW FULL TABLES FROM ' . $this->sqlQuoteIdentifier( $dbName ) );
        foreach ( $rows as $row )
        {
            $row = array_values( $row );
            $kind = isset( $row[1] ) && stripos( (string)$row[1], 'VIEW' ) !== false ? 'VIEW' : 'TABLE';
            $this->query( "DROP $kind IF EXISTS " . $this->sqlQuoteIdentifier( $dbName ) . '.' . $this->sqlQuoteIdentifier( (string)$row[0] ) );
        }
        $this->setError();
    }

    function availableDatabases()
    {
        $databases = parent::availableDatabases();
        return is_array( $databases ) ? self::withoutSystemDatabases( $databases ) : $databases;
    }

    /**
     * The databases of a list that are not the server's own: SingleStore's cluster and memsql, TiDB's metrics and
     * performance schemas, Vitess's _vt, MySQL's.
     *
     * @param string[] $databases
     * @return string[]
     */
    public static function withoutSystemDatabases( array $databases )
    {
        $system = array( 'performance_schema', 'sys', 'metrics_schema', 'cluster', 'memsql', 'information_schema', 'mysql', '_vt' );
        return array_values( array_filter( $databases, function ( $name ) use ( $system ) {
            return !in_array( strtolower( (string)$name ), $system, true );
        } ) );
    }

    function supportedRelationTypeMask()
    {
        $facts = $this->mysqlWireFacts();
        return $facts['foreign_keys'] === 'enforced' ? parent::supportedRelationTypeMask() : eZDBInterface::RELATION_TABLE_BIT;
    }

    function supportedRelationTypes()
    {
        $facts = $this->mysqlWireFacts();
        return $facts['foreign_keys'] === 'enforced' ? parent::supportedRelationTypes() : array( eZDBInterface::RELATION_TABLE );
    }

    public function sqlUnixTimestamp( $expression = null )
    {
        $sql = expMySQLWireProfile::unixTimestamp( $this->Profile, $expression );
        return $sql !== null ? $sql : parent::sqlUnixTimestamp( $expression );
    }

    public function sqlFromUnixTimestamp( $expression )
    {
        $sql = expMySQLWireProfile::fromUnixTimestamp( $this->Profile, $expression );
        return $sql !== null ? $sql : parent::sqlFromUnixTimestamp( $expression );
    }
}
