<?php
/**
 * File containing the expLibSQLConnection class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

// The SQLite driver reads its rows with SQLite3Result::fetchArray( SQLITE3_ASSOC ). A PHP without the sqlite3
// extension reaches a libSQL server all the same, so the constants it would define are given their values here.
if ( !defined( 'SQLITE3_ASSOC' ) )
    define( 'SQLITE3_ASSOC', 1 );
if ( !defined( 'SQLITE3_NUM' ) )
    define( 'SQLITE3_NUM', 2 );
if ( !defined( 'SQLITE3_BOTH' ) )
    define( 'SQLITE3_BOTH', 3 );

/**
 * A connection to a libSQL server (sqld, libsql-server, Turso) over its HTTP API, the Hrana protocol
 * (https://github.com/tursodatabase/libsql/blob/main/docs/HTTP_V2_SPEC.md and HRANA_3_SPEC.md), with the part of
 * PHP's SQLite3 class the SQLite driver uses: exec(), query(), querySingle(), lastErrorCode(), lastErrorMsg(),
 * lastInsertRowID(), changes(), escapeString(), busyTimeout(), close(). expLibSQLDB hands it to eZSQLite3DB in
 * place of a SQLite3 object, so the SQL, the transactions and the error handling of the SQLite driver stay as they
 * are, and only the transport changes.
 *
 * Every statement is one HTTP request (POST /v3/pipeline, /v2/pipeline on a server without Hrana 3) on one server
 * stream (the server's baton), which stays open from statement to statement: a new stream is a new connection of the
 * server to the database, most of a statement's time. A transaction therefore runs on one server connection, as on
 * a local file. With keepStreamOpen( false ) a stream is opened only for a transaction. With Hrana 3 each request
 * also asks whether the stream is in a transaction (get_autocommit), so a transaction the server ended by itself
 * (an error, its transaction timeout) is known at once.
 *
 * No library: PHP's curl extension, or its HTTP stream wrapper (allow_url_fopen) without curl. Keep-alive is used
 * where curl is there. A request that fails on the way (the connection lost, a stream the server has let expire)
 * is sent again once on a new stream, outside a transaction only: inside one the transaction is lost, and the
 * statement fails with the reason.
 *
 * @since 6.0.15
 */
class expLibSQLConnection
{
    /** Hrana's error codes and the result codes of SQLite they stand for (lastErrorCode()) */
    const SQLITE_CODES = array( 'ERROR' => 1, 'INTERNAL' => 2, 'PERM' => 3, 'ABORT' => 4, 'BUSY' => 5, 'LOCKED' => 6,
                                'NOMEM' => 7, 'READONLY' => 8, 'INTERRUPT' => 9, 'IOERR' => 10, 'CORRUPT' => 11,
                                'NOTFOUND' => 12, 'FULL' => 13, 'CANTOPEN' => 14, 'PROTOCOL' => 15, 'EMPTY' => 16,
                                'SCHEMA' => 17, 'TOOBIG' => 18, 'CONSTRAINT' => 19, 'MISMATCH' => 20, 'MISUSE' => 21,
                                'NOLFS' => 22, 'AUTH' => 23, 'FORMAT' => 24, 'RANGE' => 25, 'NOTADB' => 26 );

    /** lastErrorCode() of a request that did not reach the server or got no answer it could read */
    const TRANSPORT_ERROR = 10;

    /** @var string the server's base URL, http(s)://host:port, no trailing slash */
    protected $Url;

    /** @var string the auth token (a JWT: Turso's database token, or sqld's --auth-jwt-key-file), '' for none */
    protected $Token;

    /** @var float seconds a request may take, connect included */
    protected $Timeout;

    /** @var int 3 (Hrana 3, /v3/pipeline) or 2 (/v2/pipeline) */
    protected $Version = 3;

    /** @var string|null the open stream's baton; null: no stream open */
    protected $Baton = null;

    /** @var string|null the URL the server asked the stream's next request to go to (base_url) */
    protected $StreamUrl = null;

    /** @var bool whether the open stream is in a transaction */
    protected $InTransaction = false;

    /** @var bool whether the stream stays open between statements outside a transaction (keepStreamOpen()) */
    protected $KeepStreamOpen = true;

    /** @var int|string the rowid of the last INSERT on this connection, as SQLite3::lastInsertRowID() */
    protected $LastInsertRowId = 0;

    /** @var int rows the last statement changed */
    protected $Changes = 0;

    /** @var int SQLite result code of the last statement, 0 when it worked */
    protected $ErrorCode = 0;

    /** @var string */
    protected $ErrorMessage = 'not an error';

    /** @var string Hrana's own code of the last error (SQLITE_CONSTRAINT, STREAM_EXPIRED ...) */
    protected $HranaCode = '';

    /** @var bool curl when PHP has it (the default), else PHP's HTTP stream wrapper; false: the stream wrapper always */
    public $UseCurl = true;

    /** @var resource|object|null the curl handle, per process */
    protected $Curl = null;

    /** @var int the process the curl handle and the stream belong to (a fork must not share them) */
    protected $Pid = 0;

    /** @var int HTTP requests sent, statements run, requests sent again */
    public $Requests = 0;
    public $Statements = 0;
    public $Retries = 0;

    /** @var float seconds spent waiting for the server */
    public $NetworkTime = 0.0;

    /**
     * @param string $url http://host:port or https://host[:port]; libsql://host is https://host
     * @param string $token the auth token, '' for none
     * @param float $timeout seconds per request
     */
    public function __construct( $url, $token = '', $timeout = 30.0 )
    {
        $this->Url = self::normalizeUrl( $url );
        $this->Token = (string)$token;
        $this->Timeout = max( 1.0, (float)$timeout );
        $this->Pid = getmypid();
    }

    /**
     * The URL requests go to: libsql:// and wss:// become https://, ws:// becomes http://, a URL without a scheme
     * gets http://; no trailing slash, no path (pure).
     *
     * @param string $url
     * @return string
     */
    public static function normalizeUrl( $url )
    {
        $url = trim( (string)$url );
        if ( preg_match( '#^(libsql|wss|https)://#i', $url ) )
            $url = 'https://' . preg_replace( '#^[a-z]+://#i', '', $url );
        elseif ( preg_match( '#^(ws|http)://#i', $url ) )
            $url = 'http://' . preg_replace( '#^[a-z]+://#i', '', $url );
        elseif ( $url !== '' )
            $url = 'http://' . $url;
        $parts = parse_url( $url );
        if ( !$parts || !isset( $parts['host'] ) )
            return rtrim( $url, '/' );
        return $parts['scheme'] . '://' . $parts['host'] . ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' );
    }

    /** @return string the base URL */
    public function url()
    {
        return $this->Url;
    }

    /** @return int the Hrana version in use, 3 or 2 */
    public function hranaVersion()
    {
        return $this->Version;
    }

    /**
     * Whether the stream stays open between statements outside a transaction (the default), or each statement opens
     * and closes one (for a server or proxy that limits open streams).
     *
     * @param bool $keep
     */
    public function keepStreamOpen( $keep )
    {
        $this->KeepStreamOpen = (bool)$keep;
    }

    /** @return bool whether a transaction is open on the server */
    public function inTransaction()
    {
        return $this->InTransaction;
    }

    /** @return string Hrana's code of the last error ('' when it worked) */
    public function hranaCode()
    {
        return $this->HranaCode;
    }

    /**
     * Runs one statement, or several separated by ";" (a Hrana sequence), without rows.
     *
     * @param string $sql
     * @return bool
     */
    public function exec( $sql )
    {
        $result = $this->execute( $sql );
        if ( $result === false && $this->HranaCode === 'SQL_MANY_STATEMENTS' )
            return $this->sequence( $sql );
        return $result !== false;
    }

    /**
     * Runs one statement and returns its rows.
     *
     * @param string $sql
     * @return expLibSQLResult|false
     */
    public function query( $sql )
    {
        $result = $this->execute( $sql );
        return $result === false ? false : new expLibSQLResult( $result['columns'], $result['rows'] );
    }

    /**
     * The first column of the first row ($entireRow: the whole first row, by column name), as SQLite3::querySingle().
     *
     * @param string $sql
     * @param bool $entireRow
     * @return mixed null without a row, false on an error
     */
    public function querySingle( $sql, $entireRow = false )
    {
        $result = $this->execute( $sql );
        if ( $result === false )
            return false;
        if ( !$result['rows'] )
            return $entireRow ? array() : null;
        $row = $result['rows'][0];
        return $entireRow ? array_combine( $result['columns'], $row ) : $row[0];
    }

    /** @return int|string */
    public function lastInsertRowID()
    {
        return $this->LastInsertRowId;
    }

    /** @return int */
    public function changes()
    {
        return $this->Changes;
    }

    /** @return int */
    public function lastErrorCode()
    {
        return $this->ErrorCode;
    }

    /** @return string */
    public function lastErrorMsg()
    {
        return $this->ErrorMessage;
    }

    /**
     * A string for a quoted SQL literal: SQLite doubles the single quote, nothing else (pure).
     *
     * @param string $value
     * @return string
     */
    public static function escapeString( $value )
    {
        return str_replace( "'", "''", (string)$value );
    }

    /**
     * The server waits for its own locks (and ends a transaction that holds the write lock longer than its
     * transaction timeout while another waits); nothing to set per connection.
     *
     * @param int $ms
     * @return bool
     */
    public function busyTimeout( $ms )
    {
        return true;
    }

    /**
     * Functions in PHP cannot run on the server: refused, as SQLite3 refuses a function it cannot register.
     *
     * @return bool false
     */
    public function createFunction()
    {
        return false;
    }

    /** @return bool false, see createFunction() */
    public function createAggregate()
    {
        return false;
    }

    /**
     * Closes the open stream; the server rolls back a transaction left open in it.
     *
     * @return bool
     */
    public function close()
    {
        if ( $this->Baton !== null && $this->Pid === getmypid() )
            $this->pipeline( array( array( 'type' => 'close' ) ), true );
        $this->Baton = null;
        $this->StreamUrl = null;
        $this->InTransaction = false;
        if ( $this->Curl !== null && $this->Pid === getmypid() && function_exists( 'curl_close' ) && PHP_VERSION_ID < 80000 )
            curl_close( $this->Curl );
        $this->Curl = null;
        return true;
    }

    /**
     * The server's version (GET /version, "sqld 0.24.33 (...)"), '' when it does not say.
     *
     * @return string
     */
    public function serverVersion()
    {
        $answer = $this->send( 'GET', $this->Url . '/version', null );
        return $answer['status'] === 200 ? trim( (string)$answer['body'] ) : '';
    }

    /**
     * Whether the server answers (GET /health), and the error when not.
     *
     * @return bool
     */
    public function ping()
    {
        $answer = $this->send( 'GET', $this->Url . '/health', null );
        if ( $answer['status'] === 200 )
            return true;
        $this->setError( self::TRANSPORT_ERROR, '', $answer['error'] !== '' ? $answer['error'] : 'the server answered HTTP ' . $answer['status'] );
        return false;
    }

    /**
     * Runs one statement: its columns and rows (values decoded), or false with the error set.
     *
     * @param string $sql
     * @return array|false array( 'columns' => string[], 'rows' => list of lists, 'affected' => int )
     */
    protected function execute( $sql )
    {
        $this->Statements++;
        $response = $this->statement( array( 'type' => 'execute', 'stmt' => array( 'sql' => (string)$sql, 'want_rows' => true ) ), $sql );
        if ( $response === false )
            return false;
        $result = isset( $response['result'] ) && is_array( $response['result'] ) ? $response['result'] : array();
        $columns = array();
        foreach ( isset( $result['cols'] ) ? (array)$result['cols'] : array() as $n => $column )
            $columns[] = isset( $column['name'] ) && $column['name'] !== null ? (string)$column['name'] : (string)$n;
        $rows = array();
        foreach ( isset( $result['rows'] ) ? (array)$result['rows'] : array() as $row )
        {
            $values = array();
            foreach ( (array)$row as $value )
                $values[] = self::decodeValue( $value );
            $rows[] = $values;
        }
        $this->Changes = isset( $result['affected_row_count'] ) ? (int)$result['affected_row_count'] : 0;
        if ( isset( $result['last_insert_rowid'] ) && $result['last_insert_rowid'] !== null )
            $this->LastInsertRowId = self::decodeInteger( $result['last_insert_rowid'] );
        return array( 'columns' => $columns, 'rows' => $rows, 'affected' => $this->Changes );
    }

    /**
     * Runs several statements separated by ";" (a Hrana sequence: no rows, no ids).
     *
     * @param string $sql
     * @return bool
     */
    protected function sequence( $sql )
    {
        return $this->statement( array( 'type' => 'sequence', 'sql' => (string)$sql ), $sql ) !== false;
    }

    /**
     * Sends one statement request on the stream: the stream stays open when a transaction is (or may be about to be)
     * open, and is closed by the same request otherwise. Sent again once on a new stream when the stream had expired
     * or the request did not reach the server, outside a transaction.
     *
     * @param array $request
     * @param string $sql
     * @return array|false the response of the request, or false with the error set
     */
    protected function statement( array $request, $sql )
    {
        if ( $this->Pid !== getmypid() )
        {
            // a forked process: the parent's stream and handle are the parent's
            $this->Pid = getmypid();
            $this->Curl = null;
            $this->Baton = null;
            $this->StreamUrl = null;
            $this->InTransaction = false;
        }
        for ( $try = 0; ; $try++ )
        {
            $wasInTransaction = $this->InTransaction;
            // The stream stays open for the next statement: a new stream is a new connection of the server to the
            // database, which cost sqld 18 ms of a 20 ms read and 70 of a 76 ms write (measured, sqld 0.24 on
            // loopback). close() closes it; one the server let expire while idle is replaced (below).
            $keepOpen = $this->KeepStreamOpen || $this->InTransaction || self::opensTransaction( $sql );
            $requests = array( $request );
            if ( $this->Version >= 3 )
                $requests[] = array( 'type' => 'get_autocommit' );
            if ( !$keepOpen )
                $requests[] = array( 'type' => 'close' );
            $results = $this->pipeline( $requests, !$keepOpen );
            if ( $results === false )
            {
                $retry = !$wasInTransaction && $try === 0
                         && ( $this->ErrorCode === self::TRANSPORT_ERROR || in_array( $this->HranaCode, array( 'STREAM_EXPIRED', 'INVALID_BATON' ), true ) );
                if ( $retry )
                {
                    $this->Retries++;
                    continue;
                }
                if ( $wasInTransaction )
                {
                    $this->InTransaction = false;
                    $this->ErrorMessage .= '; the transaction on the libSQL server is lost';
                }
                return false;
            }
            if ( $this->Version >= 3 && isset( $results[1]['type'] ) && $results[1]['type'] === 'ok' )
                $this->InTransaction = empty( $results[1]['response']['is_autocommit'] );
            elseif ( $this->Version < 3 )
                $this->InTransaction = self::transactionAfter( $sql, $wasInTransaction );
            $first = isset( $results[0] ) ? $results[0] : null;
            if ( !is_array( $first ) || !isset( $first['type'] ) )
            {
                $this->setError( self::TRANSPORT_ERROR, '', 'the libSQL server sent no result for the statement' );
                return false;
            }
            if ( $first['type'] === 'error' )
            {
                $error = isset( $first['error'] ) ? (array)$first['error'] : array();
                $code = isset( $error['code'] ) ? (string)$error['code'] : '';
                $this->setError( self::sqliteCode( $code ), $code, self::message( $error, $code ) );
                return false;
            }
            $this->setError( 0, '', 'not an error' );
            return isset( $first['response'] ) ? (array)$first['response'] : array();
        }
    }

    /**
     * Sends a pipeline of requests on the open stream (or a new one) and returns the results, or false with the
     * error set when the request as a whole failed.
     *
     * @param array $requests
     * @param bool $closes whether the last request closes the stream
     * @return array|false
     */
    protected function pipeline( array $requests, $closes )
    {
        $body = array( 'baton' => $this->Baton, 'requests' => $requests );
        $url = ( $this->StreamUrl !== null ? $this->StreamUrl : $this->Url ) . '/v' . $this->Version . '/pipeline';
        $answer = $this->send( 'POST', $url, json_encode( $body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE ) );
        if ( $answer['status'] === 404 && $this->Version >= 3 && $this->Baton === null )
        {
            // a server of Hrana 2 only (sqld before 0.22)
            $this->Version = 2;
            $requests = array_values( array_filter( $requests, function ( $r ) { return $r['type'] !== 'get_autocommit'; } ) );
            $answer = $this->send( 'POST', $this->Url . '/v2/pipeline', json_encode( array( 'baton' => null, 'requests' => $requests ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE ) );
            if ( $answer['status'] === 200 )
            {
                $decoded = json_decode( (string)$answer['body'], true );
                if ( is_array( $decoded ) && isset( $decoded['results'] ) )
                {
                    // the result of get_autocommit is not there: put a gap where statement() looks for it
                    array_splice( $decoded['results'], 1, 0, array( null ) );
                    $answer['body'] = json_encode( $decoded );
                }
            }
        }
        if ( $answer['status'] !== 200 )
        {
            $this->Baton = null;
            $this->StreamUrl = null;
            if ( $answer['status'] === 0 )
            {
                $this->setError( self::TRANSPORT_ERROR, '', 'the libSQL server at ' . $this->Url . ' could not be reached: ' . $answer['error'] );
                return false;
            }
            $decoded = json_decode( (string)$answer['body'], true );
            $text = trim( is_array( $decoded ) ? (string)( isset( $decoded['message'] ) ? $decoded['message'] : ( isset( $decoded['error'] ) ? $decoded['error'] : '' ) ) : (string)$answer['body'] );
            $code = is_array( $decoded ) && isset( $decoded['code'] ) ? (string)$decoded['code'] : '';
            if ( $code === '' && stripos( $text, 'invalid baton' ) !== false )
                $code = 'INVALID_BATON';
            if ( $code === '' && ( $answer['status'] === 401 || $answer['status'] === 403 ) )
                $code = 'AUTH';
            $this->setError( $code === 'AUTH' ? 23 : self::TRANSPORT_ERROR, $code,
                             'the libSQL server answered HTTP ' . $answer['status'] . ( $text !== '' ? ': ' . self::shorten( $text ) : '' ) );
            return false;
        }
        $decoded = json_decode( (string)$answer['body'], true );
        if ( !is_array( $decoded ) || !isset( $decoded['results'] ) || !is_array( $decoded['results'] ) )
        {
            $this->Baton = null;
            $this->StreamUrl = null;
            $this->setError( self::TRANSPORT_ERROR, '', 'the libSQL server sent an answer that is not Hrana JSON: ' . self::shorten( (string)$answer['body'] ) );
            return false;
        }
        $this->Baton = $closes ? null : ( isset( $decoded['baton'] ) ? $decoded['baton'] : null );
        $this->StreamUrl = !$closes && !empty( $decoded['base_url'] ) ? self::normalizeUrl( $decoded['base_url'] ) : null;
        if ( $this->Baton === null )
            $this->StreamUrl = null;
        return $decoded['results'];
    }

    /**
     * Sends one HTTP request: curl with keep-alive when PHP has it, else PHP's HTTP stream wrapper.
     *
     * @param string $method GET or POST
     * @param string $url
     * @param string|null $body JSON
     * @return array array( 'status' => int (0: no answer), 'body' => string, 'error' => string )
     */
    protected function send( $method, $url, $body )
    {
        $this->Requests++;
        $headers = array( 'Accept: application/json' );
        if ( $body !== null )
            $headers[] = 'Content-Type: application/json';
        if ( $this->Token !== '' )
            $headers[] = 'Authorization: Bearer ' . $this->Token;
        $start = microtime( true );
        if ( $this->UseCurl && function_exists( 'curl_init' ) )
        {
            if ( $this->Curl === null )
                $this->Curl = curl_init();
            $curl = $this->Curl;
            curl_reset( $curl );
            curl_setopt_array( $curl, array( CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => $headers,
                                             CURLOPT_CONNECTTIMEOUT_MS => (int)( min( 10.0, $this->Timeout ) * 1000 ),
                                             CURLOPT_TIMEOUT_MS => (int)( $this->Timeout * 1000 ), CURLOPT_FOLLOWLOCATION => false,
                                             CURLOPT_NOSIGNAL => true, CURLOPT_ENCODING => '' ) );
            if ( $method === 'POST' )
            {
                curl_setopt( $curl, CURLOPT_POST, true );
                curl_setopt( $curl, CURLOPT_POSTFIELDS, (string)$body );
            }
            $response = curl_exec( $curl );
            $this->NetworkTime += microtime( true ) - $start;
            if ( $response === false )
                return array( 'status' => 0, 'body' => '', 'error' => curl_error( $curl ) );
            return array( 'status' => (int)curl_getinfo( $curl, CURLINFO_RESPONSE_CODE ), 'body' => (string)$response, 'error' => '' );
        }
        $context = stream_context_create( array( 'http' => array( 'method' => $method, 'header' => implode( "\r\n", $headers ),
                                                                   'content' => (string)$body, 'timeout' => $this->Timeout,
                                                                   'ignore_errors' => true, 'follow_location' => 0 ) ) );
        $error = '';
        set_error_handler( function ( $no, $text ) use ( &$error ) { $error = $text; return true; } );
        $stream = fopen( $url, 'rb', false, $context );
        $response = $stream ? stream_get_contents( $stream ) : false;
        // the response's header lines, from the stream (PHP 8.5 deprecates $http_response_header)
        $meta = $stream ? stream_get_meta_data( $stream ) : array();
        if ( $stream )
            fclose( $stream );
        restore_error_handler();
        $this->NetworkTime += microtime( true ) - $start;
        if ( $response === false )
            return array( 'status' => 0, 'body' => '', 'error' => $error !== '' ? $error : 'no answer' );
        $status = 0;
        foreach ( isset( $meta['wrapper_data'] ) ? (array)$meta['wrapper_data'] : array() as $line )
        {
            if ( preg_match( '#^HTTP/\S+\s+(\d{3})#', $line, $m ) )
                $status = (int)$m[1];
        }
        return array( 'status' => $status, 'body' => (string)$response, 'error' => '' );
    }

    /**
     * A Hrana value as PHP's SQLite3 gives it: integer as int (a string beyond PHP's range), float, text, blob as
     * its bytes, null (pure).
     *
     * @param mixed $value array( 'type' => ..., 'value' | 'base64' => ... )
     * @return mixed
     */
    public static function decodeValue( $value )
    {
        if ( !is_array( $value ) || !isset( $value['type'] ) )
            return $value;
        switch ( $value['type'] )
        {
            case 'integer':
                return self::decodeInteger( $value['value'] );
            case 'float':
                return (float)$value['value'];
            case 'text':
                return (string)$value['value'];
            case 'blob':
                $bytes = base64_decode( strtr( (string)$value['base64'], '-_', '+/' ) );
                return $bytes === false ? '' : $bytes;
            default:
                return null;
        }
    }

    /**
     * Hrana sends integers as strings (64 bits do not fit JSON's numbers): an int, or the string beyond PHP's range.
     *
     * @param mixed $value
     * @return int|string
     */
    public static function decodeInteger( $value )
    {
        $text = (string)$value;
        if ( preg_match( '/^-?\d+$/', $text ) && (string)(int)$text === ltrim( $text, '+' ) )
            return (int)$text;
        return $text;
    }

    /**
     * The SQLite result code of a Hrana error code: SQLITE_CONSTRAINT_UNIQUE is 19 (the primary code), SQL_PARSE_ERROR
     * and SQLITE_UNKNOWN are 1 (SQLITE_ERROR), TRANSACTION_TIMEOUT is 4 (SQLITE_ABORT), and STREAM_EXPIRED 10 (pure).
     *
     * @param string $code
     * @return int
     */
    public static function sqliteCode( $code )
    {
        $code = strtoupper( (string)$code );
        if ( $code === 'TRANSACTION_TIMEOUT' )
            return 4;
        if ( $code === 'TRANSACTION_BUSY' )
            return 5;
        if ( in_array( $code, array( 'STREAM_EXPIRED', 'INVALID_BATON' ), true ) )
            return self::TRANSPORT_ERROR;
        if ( strpos( $code, 'SQLITE_' ) === 0 )
        {
            $name = substr( $code, 7 );
            foreach ( array( $name, strstr( $name . '_', '_', true ) ) as $candidate )
            {
                if ( isset( self::SQLITE_CODES[$candidate] ) )
                    return self::SQLITE_CODES[$candidate];
            }
        }
        return 1;
    }

    /**
     * The message of a Hrana error as SQLite3::lastErrorMsg() gives it: without the server's "SQLite error: ", and a
     * transaction timeout said in full (pure).
     *
     * @param array $error
     * @param string $code
     * @return string
     */
    public static function message( array $error, $code )
    {
        $text = isset( $error['message'] ) ? (string)$error['message'] : '';
        $text = preg_replace( '/^SQLite error:\s*/i', '', $text );
        if ( $code === 'TRANSACTION_TIMEOUT' )
            return 'the libSQL server ended the transaction: it held the write lock longer than the server\'s transaction timeout while another write waited; nothing of it was written';
        if ( $text === '' )
            $text = $code !== '' ? $code : 'unknown error';
        return self::shorten( $text );
    }

    /**
     * Whether $sql opens a transaction (BEGIN, SAVEPOINT), so its stream must stay open (pure).
     *
     * @param string $sql
     * @return bool
     */
    public static function opensTransaction( $sql )
    {
        return preg_match( '#^(?:\s+|--[^\n]*\n|/\*.*?\*/)*(?:BEGIN|SAVEPOINT)\b#is', substr( (string)$sql, 0, 512 ) ) === 1;
    }

    /**
     * Hrana 2 has no get_autocommit: whether a transaction is open after $sql, from the statement (pure).
     *
     * @param string $sql
     * @param bool $before whether one was open before it
     * @return bool
     */
    public static function transactionAfter( $sql, $before )
    {
        if ( self::opensTransaction( $sql ) )
            return true;
        if ( preg_match( '#^\s*(?:COMMIT|END|ROLLBACK)\b(?!\s+TO\b)#i', (string)$sql ) )
            return false;
        return $before;
    }

    protected function setError( $code, $hranaCode, $message )
    {
        $this->ErrorCode = (int)$code;
        $this->HranaCode = (string)$hranaCode;
        $this->ErrorMessage = (string)$message;
    }

    /** A message cut to 2000 characters: a statement echoed by the server can be megabytes */
    protected static function shorten( $text )
    {
        $text = (string)$text;
        return strlen( $text ) > 2000 ? substr( $text, 0, 2000 ) . '...' : $text;
    }
}
