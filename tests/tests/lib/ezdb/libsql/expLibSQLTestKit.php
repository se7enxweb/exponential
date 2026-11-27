<?php
/**
 * What the libSQL tests share: a stand-in for a libSQL server (expLibSQLFakeConnection: the Hrana answers of sqld,
 * worked out on an in-memory SQLite database), the driver on it, the live server's address from the environment.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'lib/ezdb/classes/explibsqlconnection.php', 'lib/ezdb/classes/explibsqlresult.php', 'lib/ezdb/classes/explibsqlprofile.php',
                 'lib/ezdb/classes/ezsqlite3db.php', 'lib/ezdb/classes/explibsqldb.php', 'lib/ezdb/classes/exptursodb.php' ) as $file )
    require_once dirname( __DIR__, 5 ) . '/' . $file;

class expLibSQLTestKit
{
    /**
     * eZDebug quiet for a test, and what it was before.
     *
     * @return array
     */
    public static function quietDebug()
    {
        $saved = array();
        foreach ( array( 'eZDebugGlobalInstance', 'eZDebugEnabled' ) as $key )
            $saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $GLOBALS['eZDebugGlobalInstance'] = new eZDebug();
        $GLOBALS['eZDebugEnabled'] = false;
        return $saved;
    }

    public static function restoreDebug( array $saved )
    {
        foreach ( $saved as $key => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $value[0];
        }
    }

    /**
     * The driver's parameters as eZDB::instance() gives them.
     *
     * @param array $more
     * @return array
     */
    public static function parameters( array $more = array() )
    {
        return $more + array( 'server' => '127.0.0.1', 'port' => '8080', 'user' => '', 'password' => '', 'database' => 'exponential',
                              'use_slave_server' => false, 'slave_server' => '', 'slave_port' => '', 'slave_user' => '',
                              'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                              'is_internal_charset' => true, 'builtin_encoding' => false, 'connect_retries' => 0,
                              'use_persistent_connection' => 'disabled',
                              'libsql' => array( 'ConnectAttempts' => 1, 'ConnectWait' => 0 ) );
    }

    /**
     * The driver on a stand-in server.
     *
     * @param expLibSQLFakeConnection|null $server
     * @param array $more parameters
     * @return expLibSQLDB
     */
    public static function driver( ?expLibSQLFakeConnection $server = null, array $more = array() )
    {
        return new expLibSQLDB( self::parameters( $more + array( 'libsql_connection' => $server ?: new expLibSQLFakeConnection() ) ) );
    }

    /**
     * The live server's URL (EXP_TEST_LIBSQL_URL, e.g. http://127.0.0.1:8080), null without one.
     *
     * @return string|null
     */
    public static function liveUrl()
    {
        $url = (string)getenv( 'EXP_TEST_LIBSQL_URL' );
        return $url !== '' ? $url : null;
    }

    /**
     * The driver on the live server.
     *
     * @param array $libsql [LibSQLSettings] values
     * @return expLibSQLDB
     */
    public static function liveDriver( array $libsql = array() )
    {
        return new expLibSQLDB( self::parameters( array( 'password' => (string)getenv( 'EXP_TEST_LIBSQL_TOKEN' ),
                                                         'libsql' => $libsql + array( 'URL' => self::liveUrl(), 'ConnectAttempts' => 2 ) ) ) );
    }
}

/**
 * A libSQL server in PHP: the HTTP requests of expLibSQLConnection answered as sqld 0.24 answers them (checked against
 * it), on an in-memory SQLite database. Streams are batons; a stream is a connection, so a transaction is the stream's.
 * One database for all streams: a second stream's BEGIN while another has a transaction answers TRANSACTION_TIMEOUT
 * for the first, as sqld does after its timeout. It records every request; switches make it fail as a server does.
 */
class expLibSQLFakeConnection extends expLibSQLConnection
{
    /** @var SQLite3 */
    public $Database;

    /** @var array every request: array( method, url, decoded body ) */
    public $Sent = array();

    /** @var array baton => true for the open streams */
    public $Streams = array();

    /** @var string|null the stream that holds a transaction */
    public $TransactionStream = null;

    /** @var bool answer 404 to /v3/pipeline (a server of Hrana 2 only) */
    public $Hrana2Only = false;

    /** @var int the next requests that get no answer (the connection lost) */
    public $DropNext = 0;

    /** @var bool the next request with a baton is answered "invalid baton" (the stream expired) */
    public $ExpireNext = false;

    /** @var string|null the token the server wants; null: none */
    public $RequiredToken = null;

    /** @var int streams opened, closed */
    public $Opened = 0;
    public $Closed = 0;

    private $next = 1;

    public function __construct( $url = 'http://127.0.0.1:8080', $token = '', $timeout = 30.0 )
    {
        parent::__construct( $url, $token, $timeout );
        $this->Database = new SQLite3( ':memory:' );
    }

    protected function send( $method, $url, $body )
    {
        $this->Requests++;
        $decoded = $body !== null ? json_decode( $body, true ) : null;
        $this->Sent[] = array( $method, $url, $decoded );
        if ( $this->DropNext > 0 )
        {
            $this->DropNext--;
            return array( 'status' => 0, 'body' => '', 'error' => 'Connection refused' );
        }
        if ( $this->RequiredToken !== null && $this->Token !== $this->RequiredToken )
            return array( 'status' => 401, 'body' => 'Unauthorized: invalid token', 'error' => '' );
        $path = (string)parse_url( $url, PHP_URL_PATH );
        if ( $method === 'GET' && $path === '/version' )
            return array( 'status' => 200, 'body' => 'sqld 0.24.33 (fake)', 'error' => '' );
        if ( $method === 'GET' && $path === '/health' )
            return array( 'status' => 200, 'body' => '', 'error' => '' );
        if ( $path === '/v3/pipeline' && $this->Hrana2Only )
            return array( 'status' => 404, 'body' => '', 'error' => '' );
        if ( $path !== '/v3/pipeline' && $path !== '/v2/pipeline' )
            return array( 'status' => 404, 'body' => '', 'error' => '' );
        $baton = isset( $decoded['baton'] ) ? $decoded['baton'] : null;
        if ( $baton !== null && ( $this->ExpireNext || !isset( $this->Streams[$baton] ) ) )
        {
            $this->ExpireNext = false;
            unset( $this->Streams[$baton] );
            return array( 'status' => 400, 'body' => 'Received an invalid baton', 'error' => '' );
        }
        if ( $baton === null )
        {
            $baton = 'b' . $this->next++;
            $this->Streams[$baton] = true;
            $this->Opened++;
        }
        $results = array();
        $closed = false;
        foreach ( (array)$decoded['requests'] as $request )
        {
            switch ( $request['type'] )
            {
                case 'execute':
                    $results[] = $this->execute1( $baton, $request['stmt']['sql'] );
                    break;
                case 'sequence':
                    $results[] = $this->sequence1( $baton, $request['sql'] );
                    break;
                case 'get_autocommit':
                    if ( $path === '/v2/pipeline' )
                        return array( 'status' => 400, 'body' => 'The `get_autocommit` request is only supported in protocol version hrana3 and higher', 'error' => '' );
                    $results[] = array( 'type' => 'ok', 'response' => array( 'type' => 'get_autocommit', 'is_autocommit' => $this->TransactionStream !== $baton ) );
                    break;
                case 'close':
                    if ( $this->TransactionStream === $baton )
                    {
                        $this->Database->exec( 'ROLLBACK' );
                        $this->TransactionStream = null;
                    }
                    unset( $this->Streams[$baton] );
                    $this->Closed++;
                    $closed = true;
                    $results[] = array( 'type' => 'ok', 'response' => array( 'type' => 'close' ) );
                    break;
            }
        }
        return array( 'status' => 200, 'error' => '',
                      'body' => json_encode( array( 'baton' => $closed ? null : $baton, 'base_url' => null, 'results' => $results ) ) );
    }

    /** One statement of a stream, as sqld answers it */
    private function execute1( $baton, $sql )
    {
        if ( preg_match( '/^\s*CREATE\s+TEMP(ORARY)?\s+TABLE/i', $sql ) )
            return $this->error( 'SQL string could not be parsed: unsupported statement: ' . $sql, 'SQL_PARSE_ERROR' );
        if ( count( array_filter( array_map( 'trim', explode( ';', $sql ) ), 'strlen' ) ) > 1 )
            return $this->error( 'SQL string contains more than one statement', 'SQL_MANY_STATEMENTS' );
        if ( $this->TransactionStream !== null && $this->TransactionStream !== $baton )
        {
            // another stream's transaction: sqld takes the lock away from it after its timeout
            $this->Database->exec( 'ROLLBACK' );
            $this->Victim = $this->TransactionStream;
            $this->TransactionStream = null;
        }
        if ( $this->Victim !== null && $this->Victim === $baton )
        {
            $this->Victim = null;
            return $this->error( 'Transaction timed out', 'TRANSACTION_TIMEOUT' );
        }
        $before = $this->Database->lastInsertRowID();
        // rows from query(); a statement without rows through exec(), which runs it once (query() and execute() of
        // PHP's SQLite3 can run a BEGIN or ROLLBACK a second time when the result is freed)
        $reads = preg_match( '/^\s*(SELECT|PRAGMA|WITH|VALUES|EXPLAIN)\b/i', $sql ) === 1;
        $result = $reads ? @$this->Database->query( $sql ) : ( @$this->Database->exec( $sql ) ? null : false );
        if ( $result === false )
        {
            $message = $this->Database->lastErrorMsg();
            $code = stripos( $message, 'constraint' ) !== false ? 'SQLITE_CONSTRAINT' : 'SQLITE_UNKNOWN';
            return $this->error( 'SQLite error: ' . $message, $code );
        }
        if ( preg_match( '/^\s*BEGIN\b/i', $sql ) )
            $this->TransactionStream = $baton;
        if ( preg_match( '/^\s*(COMMIT|END|ROLLBACK)\b(?!\s+TO)/i', $sql ) )
            $this->TransactionStream = null;
        $cols = array();
        for ( $i = 0; $result !== null && $i < $result->numColumns(); $i++ )
            $cols[] = array( 'name' => $result->columnName( $i ), 'decltype' => null );
        $rows = array();
        while ( $result !== null && $result->numColumns() && ( $row = $result->fetchArray( SQLITE3_NUM ) ) !== false )
        {
            $values = array();
            foreach ( $row as $value )
                $values[] = self::encode( $value );
            $rows[] = $values;
        }
        $after = $this->Database->lastInsertRowID();
        return array( 'type' => 'ok', 'response' => array( 'type' => 'execute', 'result' => array(
            'cols' => $cols, 'rows' => $rows, 'affected_row_count' => $this->Database->changes(),
            'last_insert_rowid' => $after !== $before ? (string)$after : null ) ) );
    }

    private function sequence1( $baton, $sql )
    {
        if ( @$this->Database->exec( $sql ) )
            return array( 'type' => 'ok', 'response' => array( 'type' => 'sequence' ) );
        return $this->error( 'SQLite error: ' . $this->Database->lastErrorMsg(), 'SQLITE_UNKNOWN' );
    }

    private function error( $message, $code )
    {
        return array( 'type' => 'error', 'error' => array( 'message' => $message, 'code' => $code ) );
    }

    /** A PHP value as a Hrana value */
    public static function encode( $value )
    {
        if ( $value === null )
            return array( 'type' => 'null' );
        if ( is_int( $value ) )
            return array( 'type' => 'integer', 'value' => (string)$value );
        if ( is_float( $value ) )
            return array( 'type' => 'float', 'value' => $value );
        if ( !mb_check_encoding( (string)$value, 'UTF-8' ) )
            return array( 'type' => 'blob', 'base64' => rtrim( base64_encode( $value ), '=' ) );
        return array( 'type' => 'text', 'value' => (string)$value );
    }

    /** @var string|null the stream whose transaction another took (TRANSACTION_TIMEOUT at its next statement) */
    public $Victim = null;

    /**
     * A statement of another client, on a stream of its own (one for all its statements).
     *
     * @param string $sql
     * @return array the Hrana result
     */
    public function otherClient( $sql )
    {
        $baton = 'other';
        $this->Streams[$baton] = true;
        $result = $this->execute1( $baton, $sql );
        unset( $this->Streams[$baton] );
        return $result;
    }
}
