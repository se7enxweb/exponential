<?php
/**
 * File containing the expLibSQLDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The libSQL driver: DatabaseImplementation=libsql (also sqld) in site.ini, a site's database on a libSQL server
 * (sqld, the libsql-server container) reached over its HTTP API; expTursoDB (turso) is the same for Turso's cloud.
 *
 * <code>
 * [DatabaseSettings]
 * DatabaseImplementation=libsql
 * Server=10.0.0.5
 * Port=8080
 * Database=exponential        a name for the database; one libSQL server holds one (without namespaces)
 * Password=                   the auth token, when the server needs one ([LibSQLSettings] AuthToken as well)
 *
 * [LibSQLSettings]
 * URL=                        http(s)://host:port or libsql://host; replaces Server and Port
 * </code>
 *
 * libSQL is SQLite's fork: the same SQL, the same file format, the same answers. So this class is the SQLite driver
 * (eZSQLite3DB) with another transport, expLibSQLConnection, in place of PHP's SQLite3 object, and databaseName()
 * stays "sqlite": the kernel's SQLite schema (kernel/sql/sqlite, eZSQLiteSchema), the SQL helpers and every place
 * that treats SQLite in its own way treat a libSQL server the same. What differs:
 *
 *  - Every statement is an HTTP request to the server; a transaction keeps one server stream open from BEGIN to its
 *    end. The server queues writers itself: the writer gate of the SQLite driver (a lock file next to the database
 *    file) is not used, there is no file here.
 *  - Functions in PHP cannot run on a server: md5() of a literal is worked out in PHP (the URL aliases' text_md5), of
 *    a column it is the server's md5() as lower-case hex; BIT_OR() and BIT_AND(), registered in PHP on a local file,
 *    are written as MAX() and MIN() of each bit.
 *  - Temporary tables: the server has none (CREATE TEMPORARY TABLE is refused), so createTempTable() makes an
 *    ordinary table of the unique name the caller chose, and dropTempTable() or the end of the connection drops it.
 *  - The connection's PRAGMAs (cache, mmap, WAL) are the server's business: none are sent.
 *  - A libSQL server ends a write transaction that holds the lock longer than its transaction timeout (5 s in sqld)
 *    while another write waits; the statement then fails with "the libSQL server ended the transaction", and the
 *    caller's transaction is rolled back, as any failed transaction is.
 *
 * The embedded file mode ([LibSQLSettings] Mode=file) needs no server: the database is a local file in
 * var/storage/sqlite3, and every method is then the SQLite driver's own over PHP's sqlite3.
 *
 * @since 6.0.15
 */
class expLibSQLDB extends eZSQLite3DB
{
    /** The profile of this class (expLibSQLProfile::all()); expTursoDB sets its own */
    const PROFILE = 'libsql';

    /** @var array the applied profile (expLibSQLProfile::apply()) */
    public $Profile = array();

    /** @var string the server's URL */
    public $Url = '';

    /** @var string sqlite_version() of the server */
    protected $ServerSQLiteVersion = '';

    /** @var array lower-case name => name of the tables createTempTable() made on this connection */
    protected $TempTables = array();

    /** @var bool the embedded file mode: the database is a local file, every method is the SQLite driver's */
    public $Embedded = false;

    /** @var expLibSQLConnection|null the transport the parameters gave ('libsql_connection') */
    protected $GivenConnection = null;

    /**
     * Connects to the server.
     *
     * @param array $parameters as eZDB::instance() gives them; 'libsql' => array of [LibSQLSettings] values replaces
     *                          the settings of site.ini, 'profile' another profile's name
     * @throws eZDBNoConnectionException
     */
    public function __construct( $parameters )
    {
        // eZSQLite3DB's constructor opens a file: only the base class's is wanted here
        eZDBInterface::__construct( $parameters );

        // Read replicas of a libSQL database are the service's (Turso's replicas answer at the same URL)
        $this->UseSlaveServer = false;

        $name = isset( $parameters['profile'] ) && is_string( $parameters['profile'] ) ? $parameters['profile'] : static::PROFILE;
        $profile = expLibSQLProfile::get( $name );
        if ( $profile === null )
            $profile = expLibSQLProfile::get( static::PROFILE );
        $this->Profile = expLibSQLProfile::apply( $profile, self::settingsFrom( $parameters ) );
        $this->TempTableList = array();
        $this->StatementWaitMs = self::configuredStatementWaitMs();

        // The embedded file mode ([LibSQLSettings] Mode=file): a local file in var/storage/sqlite3, opened by PHP's
        // sqlite3 exactly as an SQLite site's (libSQL's file format is SQLite's); every method below then is the
        // SQLite driver's own
        if ( expLibSQLProfile::embedded( $this->Profile ) )
        {
            $this->Embedded = true;
            $this->Url = '';
            parent::__construct( $parameters );
            return;
        }
        $this->Url = expLibSQLProfile::url( $this->Profile, $this->Server, $this->Port );

        if ( !function_exists( 'curl_init' ) && !filter_var( ini_get( 'allow_url_fopen' ), FILTER_VALIDATE_BOOLEAN ) )
        {
            if ( function_exists( 'eZAppendWarningItem' ) )
            {
                eZAppendWarningItem( array( 'error' => array( 'type' => 'ezdb', 'number' => eZDBInterface::ERROR_MISSING_EXTENSION ),
                                            'text' => 'libSQL needs PHP\'s curl extension or allow_url_fopen, the DB handler will not be initialized.' ) );
            }
            $this->IsConnected = false;
            eZDebug::writeError( 'libSQL needs PHP\'s curl extension or allow_url_fopen', __CLASS__ );
            return;
        }

        eZDebug::createAccumulatorGroup( 'sqlite3_total', 'libSQL Total' );
        // a test hands in its own transport (an expLibSQLConnection whose send() answers without a server)
        if ( isset( $parameters['libsql_connection'] ) && $parameters['libsql_connection'] instanceof expLibSQLConnection )
            $this->GivenConnection = $parameters['libsql_connection'];
        $this->DBConnection = $this->openConnection();
        if ( !$this->DBConnection )
        {
            $this->IsConnected = false;
            throw new eZDBNoConnectionException( $this->Url, $this->ErrorMessage, $this->ErrorNumber );
        }
        $this->IsConnected = true;
    }

    /**
     * The [LibSQLSettings] of site.ini, replaced by the 'libsql' parameters where given.
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
            if ( $ini->hasGroup( 'LibSQLSettings' ) )
                $settings = (array)$ini->group( 'LibSQLSettings' );
        }
        catch ( Throwable $e )
        {
        }
        if ( isset( $parameters['libsql'] ) && is_array( $parameters['libsql'] ) )
            $settings = array_merge( $settings, $parameters['libsql'] );
        return $settings;
    }

    /**
     * The connection: ConnectAttempts attempts (at least the site's ConnectRetries + 1) of SELECT sqlite_version(),
     * waiting ConnectWait ms, twice as long after each failure. ErrorMessage holds the reason (never the token).
     *
     * @return expLibSQLConnection|false
     */
    protected function openConnection()
    {
        $settings = $this->Profile['settings'];
        $token = expLibSQLProfile::token( $this->Profile, $this->Password );
        if ( $token === '' && $this->Profile['token_required'] )
        {
            $this->ErrorNumber = 23;
            $this->ErrorMessage = $this->Profile['label'] . ' needs an auth token: [DatabaseSettings] Password or [LibSQLSettings] AuthToken';
            return false;
        }
        // a request waits at least as long as a statement waits for a lock on the server
        $timeout = max( (float)$settings['Timeout'], $this->StatementWaitMs / 1000 + 5 );
        $connection = $this->newConnection( $this->Url, $token, $timeout );
        $connection->keepStreamOpen( !in_array( strtolower( $settings['KeepStream'] ), array( 'disabled', 'false', '0' ), true ) );
        if ( $settings['CAFile'] !== '' )
            $connection->setCAFile( $settings['CAFile'] );
        $attempts = max( (int)$settings['ConnectAttempts'], (int)$this->connectRetryCount() + 1, 1 );
        $wait = (int)$settings['ConnectWait'];
        for ( $n = 1; $n <= $attempts; $n++ )
        {
            $version = $connection->querySingle( 'SELECT sqlite_version()' );
            if ( is_string( $version ) && $version !== '' )
            {
                $this->ServerSQLiteVersion = $version;
                if ( $this->OutputSQL )
                    eZDebug::writeDebug( "Connected to the libSQL server at {$this->Url} (SQLite $version)", __METHOD__ );
                return $connection;
            }
            $this->ErrorNumber = $connection->lastErrorCode();
            $this->ErrorMessage = $token !== '' ? str_replace( $token, '***', $connection->lastErrorMsg() ) : $connection->lastErrorMsg();
            if ( $this->ErrorNumber === 23 )
                break;   // a token the server refuses stays refused
            if ( $n < $attempts && $wait > 0 )
                usleep( min( 30000, $wait * ( 1 << ( $n - 1 ) ) ) * 1000 );
        }
        eZDebug::writeError( "Connection error: Couldn't connect to the libSQL server at {$this->Url}: " . $this->ErrorMessage, __CLASS__ );
        return false;
    }

    /**
     * The transport (a test gives its own).
     *
     * @param string $url
     * @param string $token
     * @param float $timeout
     * @return expLibSQLConnection
     */
    protected function newConnection( $url, $token, $timeout )
    {
        if ( $this->GivenConnection !== null )
            return $this->GivenConnection;
        return new expLibSQLConnection( $url, $token, $timeout );
    }

    /// The server's settings are its own: no PRAGMAs per connection
    protected function applyPragmas()
    {
        if ( $this->Embedded )
            return parent::applyPragmas(  );
    }

    /// WAL is the server's journal mode (sqld runs WAL always)
    protected function useWAL()
    {
        if ( $this->Embedded )
            return parent::useWAL(  );
    }

    /// No lock file: the server queues the writers
    protected function writerGate()
    {
        if ( $this->Embedded )
            return parent::writerGate(  );
        $this->WriterGate = false;
        $this->HoldsWriterGate = false;
        return false;
    }

    /// No queue file, see writerGate()
    protected function writerQueue()
    {
        if ( $this->Embedded )
            return parent::writerQueue(  );
        $this->WriterQueue = false;
        return false;
    }

    function query( $sql, $server = false )
    {
        if ( $this->Embedded )
            return parent::query( $sql, $server );
        return parent::query( self::rewriteFunctions( $sql ), $server );
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        if ( $this->Embedded )
            return parent::arrayQuery( $sql, $params, $server );
        return parent::arrayQuery( self::rewriteFunctions( $sql ), $params, $server );
    }

    /**
     * MySQL's BIT_OR() and BIT_AND(), which the SQLite driver registers in PHP, written in SQL the server runs: the
     * bits 0 to 62 one by one, MAX() of each for BIT_OR, MIN() for BIT_AND, 0 for no rows as on a local file (pure).
     * Other SQL is returned as it is.
     *
     * @param string $sql
     * @return string
     */
    public static function rewriteFunctions( $sql )
    {
        $sql = (string)$sql;
        if ( stripos( $sql, 'BIT_' ) === false || !preg_match( '/\bBIT_(?:OR|AND)\s*\(/i', $sql ) )
            return $sql;
        $out = '';
        $offset = 0;
        while ( preg_match( '/\bBIT_(OR|AND)\s*\(/i', $sql, $m, PREG_OFFSET_CAPTURE, $offset ) )
        {
            $start = $m[0][1];
            $open = $start + strlen( $m[0][0] );
            $close = self::closingParenthesis( $sql, $open );
            if ( $close === null )
                break;
            $argument = self::rewriteFunctions( substr( $sql, $open, $close - $open ) );
            $aggregate = strtoupper( $m[1][0] ) === 'OR' ? 'MAX' : 'MIN';
            $terms = array();
            for ( $bit = 0; $bit < 63; $bit++ )
                $terms[] = "$aggregate(($argument)&" . ( 1 << $bit ) . ')';
            $out .= substr( $sql, $offset, $start - $offset ) . 'COALESCE((' . implode( '|', $terms ) . '),0)';
            $offset = $close + 1;
        }
        return $out . substr( $sql, $offset );
    }

    /**
     * The position of the parenthesis that closes the one opened before $position, outside quotes; null when none.
     *
     * @param string $sql
     * @param int $position
     * @return int|null
     */
    protected static function closingParenthesis( $sql, $position )
    {
        $depth = 1;
        $quote = null;
        for ( $i = $position, $n = strlen( $sql ); $i < $n; $i++ )
        {
            $c = $sql[$i];
            if ( $quote !== null )
            {
                if ( $c === $quote )
                    $quote = null;
                continue;
            }
            if ( $c === "'" || $c === '"' )
                $quote = $c;
            elseif ( $c === '(' )
                $depth++;
            elseif ( $c === ')' && --$depth === 0 )
                return $i;
        }
        return null;
    }

    /**
     * MD5 of $str: a quoted literal is hashed in PHP (the URL aliases' text_md5 = MD5('...')), anything else by the
     * server's md5(), as the lower-case hex MySQL's MD5() returns.
     *
     * @param string $str
     * @return string
     */
    function md5( $str )
    {
        if ( $this->Embedded )
            return parent::md5( $str );
        $trimmed = trim( (string)$str );
        if ( preg_match( "/^'((?:[^']|'')*)'$/s", $trimmed, $m ) )
            return " '" . md5( str_replace( "''", "'", $m[1] ) ) . "' ";
        return " lower( hex( md5( $str ) ) ) ";
    }

    /**
     * CREATE TEMPORARY TABLE as an ordinary table of the caller's unique name (generateUniqueTempTableName()), which
     * dropTempTable() or close() drops: a libSQL server refuses temporary tables.
     *
     * @param string $createTableQuery
     * @param int $server
     * @return bool
     */
    function createTempTable( $createTableQuery = '', $server = self::SERVER_SLAVE )
    {
        if ( $this->Embedded )
            return parent::createTempTable( $createTableQuery, $server );
        if ( !preg_match( '/^\s*CREATE\s+(?:GLOBAL\s+|LOCAL\s+)?TEMP(?:ORARY)?\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?([A-Za-z0-9_]+)/i', (string)$createTableQuery, $m ) )
            return $this->query( $createTableQuery );
        $sql = preg_replace( '/^\s*CREATE\s+(?:GLOBAL\s+|LOCAL\s+)?TEMP(?:ORARY)?\s+TABLE\b/i', 'CREATE TABLE', (string)$createTableQuery );
        $ok = $this->query( $sql );
        if ( $ok )
            $this->TempTables[strtolower( $m[1] )] = $m[1];
        return $ok;
    }

    function dropTempTable( $dropTableQuery = '', $server = self::SERVER_SLAVE )
    {
        if ( $this->Embedded )
            return parent::dropTempTable( $dropTableQuery, $server );
        if ( preg_match( '/^\s*DROP\s+(?:TEMPORARY\s+)?TABLE\s+(?:IF\s+EXISTS\s+)?([A-Za-z0-9_]+)\s*;?\s*$/i', (string)$dropTableQuery, $m ) )
        {
            unset( $this->TempTables[strtolower( $m[1] )] );
            return $this->query( 'DROP TABLE IF EXISTS ' . $m[1] );
        }
        return $this->query( $dropTableQuery );
    }

    /**
     * @return string[] the tables createTempTable() made and nothing dropped yet
     */
    public function tempTables()
    {
        return array_values( $this->TempTables );
    }

    function escapeString( $str )
    {
        if ( $this->Embedded )
            return parent::escapeString( $str );
        return $str === null ? '' : expLibSQLConnection::escapeString( (string)$str );
    }

    function close()
    {
        if ( $this->IsConnected && $this->DBConnection instanceof expLibSQLConnection )
        {
            // the tables in place of temporary ones end with the connection, as temporary tables would
            if ( $this->TempTables && !$this->DBConnection->inTransaction() )
            {
                foreach ( $this->TempTables as $table )
                    $this->DBConnection->exec( 'DROP TABLE IF EXISTS ' . $table );
            }
            $this->TempTables = array();
        }
        parent::close();
    }

    /// The database is on the server: nothing to make here
    function createDatabase( $dbName )
    {
        if ( $this->Embedded )
            return parent::createDatabase( $dbName );
    }

    /// The database of the server, by the name of the settings
    function availableDatabases()
    {
        if ( $this->Embedded )
            return parent::availableDatabases(  );
        return (string)$this->DB !== '' ? array( (string)$this->DB ) : array();
    }

    function databaseServerVersion()
    {
        if ( $this->Embedded )
            return parent::databaseServerVersion(  );
        $version = $this->ServerSQLiteVersion;
        return array( 'string' => $version, 'values' => explode( '.', $version ) );
    }

    function databaseClientVersion()
    {
        if ( $this->Embedded )
            return parent::databaseClientVersion(  );
        $hrana = $this->DBConnection instanceof expLibSQLConnection ? $this->DBConnection->hranaVersion() : 3;
        return array( 'string' => 'Hrana ' . $hrana . ' over HTTP', 'values' => array( $hrana ) );
    }

    /**
     * The server's own version ("sqld 0.24.33 ..."), '' when it does not say.
     *
     * @return string
     */
    public function serverProduct()
    {
        if ( $this->Embedded )
            return 'SQLite ' . SQLite3::version()['versionString'] . ' (embedded file mode)';
        return $this->DBConnection instanceof expLibSQLConnection ? $this->DBConnection->serverVersion() : '';
    }

    /**
     * The connection's counters: HTTP requests, statements, requests sent again, seconds waited for the server.
     *
     * @return array
     */
    public function transportStatistics()
    {
        $c = $this->DBConnection;
        if ( !$c instanceof expLibSQLConnection )
            return array( 'requests' => 0, 'statements' => 0, 'retries' => 0, 'network_seconds' => 0.0 );
        return array( 'requests' => $c->Requests, 'statements' => $c->Statements, 'retries' => $c->Retries,
                      'network_seconds' => round( $c->NetworkTime, 3 ) );
    }
}
