<?php
/**
 * File containing the expClickHouseHttp class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * ClickHouse's HTTP interface (port 8123, 8443 with TLS), the transport of expClickHouseDB: one POST per statement,
 * through PHP's curl extension, else PHP's own HTTP stream.
 *
 * The statement is the body; the user and password travel in the headers X-ClickHouse-User and X-ClickHouse-Key
 * (never in the address), the database in X-ClickHouse-Database. Settings are parameters of the address (the
 * connection's, then the statement's). A result comes back in the format TabSeparatedWithNamesAndTypes
 * (default_format), so a statement is sent as it is: the names, the types, then one line per row, \N for NULL, and
 * backslash escapes. An error is an HTTP status other than 200 (or a status 200 whose body ends in an exception, when
 * the server failed while it streamed), its number in the header X-ClickHouse-Exception-Code.
 *
 * Why HTTP: it is ClickHouse's own client protocol for every language but C++, needs no PHP extension beyond curl
 * (not even that), carries settings per statement, and gives typed results and error numbers. ClickHouse also speaks
 * the MySQL and the PostgreSQL protocols, but only a part of them (no transactions, no LAST_INSERT_ID(), no bound
 * parameters over the PostgreSQL one), and no settings per statement.
 *
 * @since 6.0.15
 * @package lib
 */
class expClickHouseHttp
{
    /** @var string the base address, http(s)://host:port/ */
    protected $Url;

    /** @var array header lines of every request */
    protected $Headers = array();

    /** @var array settings sent with every request (name => value) */
    protected $Settings = array();

    /** @var float seconds a statement may take */
    protected $Timeout;

    /** @var float seconds the connection may take */
    protected $ConnectTimeout;

    /** @var bool whether the server's certificate is checked */
    protected $VerifyTls;

    /** @var string */
    public $Error = '';

    /** @var int ClickHouse's error number (0: none; -1: the request failed before ClickHouse answered) */
    public $ErrorCode = 0;

    /** @var array header lines of the last answer, lower case name => value */
    public $LastHeaders = array();

    /** @var callable|null sends a request instead of curl (tests): function( string $url, array $headers, string $body ) returning array( int status, array headers, string body ) or false */
    public $Sender = null;

    /**
     * @param array $options host, port, secure (bool), user, password, database, timeout, connect_timeout,
     *                       verify_tls (bool), settings (array)
     */
    public function __construct( array $options )
    {
        $options = array_merge( array( 'host' => '127.0.0.1', 'port' => 0, 'secure' => false, 'user' => 'default', 'password' => '',
                                       'database' => '', 'timeout' => 300, 'connect_timeout' => 10, 'verify_tls' => true,
                                       'settings' => array() ), $options );
        $secure = (bool)$options['secure'];
        $port = (int)$options['port'] ? (int)$options['port'] : ( $secure ? 8443 : 8123 );
        $host = (string)$options['host'] === '' ? '127.0.0.1' : (string)$options['host'];
        if ( strpos( $host, ':' ) !== false && $host[0] !== '[' )
            $host = '[' . $host . ']';
        $this->Url = ( $secure ? 'https' : 'http' ) . '://' . $host . ':' . $port . '/';
        $this->Headers = array( 'X-ClickHouse-User: ' . self::headerValue( $options['user'] === '' ? 'default' : $options['user'] ),
                                'X-ClickHouse-Key: ' . self::headerValue( $options['password'] ) );
        if ( (string)$options['database'] !== '' )
            $this->Headers[] = 'X-ClickHouse-Database: ' . self::headerValue( $options['database'] );
        $this->Settings = (array)$options['settings'];
        $this->Timeout = (float)$options['timeout'];
        $this->ConnectTimeout = (float)$options['connect_timeout'];
        $this->VerifyTls = (bool)$options['verify_tls'];
    }

    /**
     * The address a statement is sent to (without the password), for the messages and the tests.
     *
     * @param array $settings the statement's settings over the connection's
     * @return string
     */
    public function url( array $settings = array() )
    {
        $params = array_merge( array( 'default_format' => 'TabSeparatedWithNamesAndTypes' ), $this->Settings, $settings );
        return $this->Url . '?' . http_build_query( $params, '', '&', PHP_QUERY_RFC3986 );
    }

    /**
     * Sends one statement.
     *
     * @param string $sql
     * @param array $settings settings of this statement
     * @return array|false array( 'columns' => names, 'types' => ClickHouse types, 'rows' => lists of strings or null,
     *                     'summary' => the X-ClickHouse-Summary values ), false with $Error set
     */
    public function query( $sql, array $settings = array() )
    {
        $this->Error = '';
        $this->ErrorCode = 0;
        $answer = $this->send( $this->url( $settings ), $this->Headers, (string)$sql );
        if ( $answer === false )
            return false;
        list( $status, $headers, $body ) = $answer;
        $this->LastHeaders = $headers;
        if ( $status !== 200 || isset( $headers['x-clickhouse-exception-code'] ) || self::endsInException( $body ) )
        {
            $this->ErrorCode = isset( $headers['x-clickhouse-exception-code'] ) ? (int)$headers['x-clickhouse-exception-code'] : self::codeOf( $body );
            $this->Error = self::messageOf( $body, $status );
            return false;
        }
        $result = self::parse( $body );
        $result['summary'] = isset( $headers['x-clickhouse-summary'] ) ? (array)json_decode( $headers['x-clickhouse-summary'], true ) : array();
        return $result;
    }

    /**
     * The rows of a TabSeparatedWithNamesAndTypes body (pure).
     *
     * @param string $body
     * @return array( 'columns' => string[], 'types' => string[], 'rows' => array[] )
     */
    public static function parse( $body )
    {
        $result = array( 'columns' => array(), 'types' => array(), 'rows' => array() );
        if ( $body === '' )
            return $result;
        // every line ends in \n; a row of one empty text is an empty line, so only the last \n is taken off
        if ( substr( $body, -1 ) === "\n" )
            $body = substr( $body, 0, -1 );
        $lines = explode( "\n", $body );
        if ( count( $lines ) < 2 )
            return $result;
        $result['columns'] = array_map( array( __CLASS__, 'unescape' ), explode( "\t", array_shift( $lines ) ) );
        $result['types'] = array_map( array( __CLASS__, 'unescape' ), explode( "\t", array_shift( $lines ) ) );
        foreach ( $lines as $line )
        {
            $row = array();
            foreach ( explode( "\t", $line ) as $field )
                $row[] = $field === '\\N' ? null : self::unescape( $field );
            $result['rows'][] = $row;
        }
        return $result;
    }

    /**
     * A TabSeparated field as text (pure): \t \n \r \0 \\ \' and the other backslash escapes.
     *
     * @param string $field
     * @return string
     */
    public static function unescape( $field )
    {
        if ( strpos( $field, '\\' ) === false )
            return $field;
        return preg_replace_callback( '/\\\\(.)/s', function ( $m ) {
            switch ( $m[1] )
            {
                case 't': return "\t";
                case 'n': return "\n";
                case 'r': return "\r";
                case '0': return "\0";
                case 'b': return "\x08";
                case 'f': return "\f";
                case 'a': return "\x07";
                case 'v': return "\x0B";
                case 'e': return "\x1B";
            }
            return $m[1];
        }, $field );
    }

    /**
     * Whether a body of status 200 ends in an exception (the server failed after it began to send).
     */
    public static function endsInException( $body )
    {
        $tail = substr( (string)$body, -2000 );
        return (bool)preg_match( '/(^|\n)Code: \d+\. DB::Exception: [^\n]*\n?$/', $tail ) && strpos( $tail, '(version ' ) !== false;
    }

    /**
     * ClickHouse's error number in an error text (pure), 0 when there is none.
     */
    public static function codeOf( $body )
    {
        return preg_match( '/Code: (\d+)\./', (string)$body, $m ) ? (int)$m[1] : 0;
    }

    /**
     * The message of an error answer (pure): "Code: 60. DB::Exception: ..." without the code and the version.
     */
    public static function messageOf( $body, $status )
    {
        $text = trim( (string)$body );
        if ( preg_match( '/Code: \d+\. (?:DB::Exception: )?(.*?)(?:\s*\(version [^()]*(?:\([^()]*\))?[^()]*\))?\s*$/s', $text, $m ) )
            $text = trim( $m[1] );
        if ( $text === '' )
            $text = "HTTP status $status";
        return strlen( $text ) > 2000 ? substr( $text, 0, 2000 ) . ' ...' : $text;
    }

    /**
     * A header value without line breaks.
     */
    protected static function headerValue( $value )
    {
        return str_replace( array( "\r", "\n" ), '', (string)$value );
    }

    /**
     * One POST: array( status, headers, body ), or false when no answer came.
     */
    protected function send( $url, array $headers, $body )
    {
        if ( $this->Sender !== null )
        {
            $answer = call_user_func( $this->Sender, $url, $headers, $body );
            if ( $answer === false )
            {
                $this->Error = 'The ClickHouse server did not answer';
                $this->ErrorCode = -1;
            }
            return $answer;
        }
        if ( function_exists( 'curl_init' ) )
            return $this->sendCurl( $url, $headers, $body );
        return $this->sendStream( $url, $headers, $body );
    }

    protected function sendCurl( $url, array $headers, $body )
    {
        $handle = curl_init( $url );
        $answerHeaders = array();
        curl_setopt_array( $handle, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array_merge( $headers, array( 'Content-Type: text/plain; charset=utf-8', 'Expect:' ) ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT_MS => (int)( $this->ConnectTimeout * 1000 ),
            CURLOPT_TIMEOUT_MS => (int)( $this->Timeout * 1000 ),
            CURLOPT_SSL_VERIFYPEER => $this->VerifyTls,
            CURLOPT_SSL_VERIFYHOST => $this->VerifyTls ? 2 : 0,
            CURLOPT_ENCODING => '',
            CURLOPT_HEADERFUNCTION => function ( $h, $line ) use ( &$answerHeaders ) {
                $colon = strpos( $line, ':' );
                if ( $colon !== false )
                    $answerHeaders[strtolower( trim( substr( $line, 0, $colon ) ) )] = trim( substr( $line, $colon + 1 ) );
                return strlen( $line );
            },
        ) );
        $answer = curl_exec( $handle );
        if ( $answer === false )
        {
            $this->Error = 'The ClickHouse server did not answer: ' . curl_error( $handle );
            $this->ErrorCode = -1;
            return false;
        }
        $status = (int)curl_getinfo( $handle, CURLINFO_RESPONSE_CODE );
        return array( $status, $answerHeaders, (string)$answer );
    }

    protected function sendStream( $url, array $headers, $body )
    {
        $context = stream_context_create( array(
            'http' => array( 'method' => 'POST', 'header' => implode( "\r\n", array_merge( $headers, array( 'Content-Type: text/plain; charset=utf-8' ) ) ),
                             'content' => $body, 'timeout' => $this->Timeout, 'ignore_errors' => true ),
            'ssl' => array( 'verify_peer' => $this->VerifyTls, 'verify_peer_name' => $this->VerifyTls ),
        ) );
        $answer = @file_get_contents( $url, false, $context );
        // PHP 8.4 and later: http_get_last_response_headers(); before: the local http_response_header (read through
        // get_defined_vars(), as PHP 8.5 deprecates the name in code)
        $local = get_defined_vars();
        $lines = function_exists( 'http_get_last_response_headers' ) ? http_get_last_response_headers()
               : ( isset( $local['http_response_header'] ) ? $local['http_response_header'] : null );
        if ( $answer === false || !is_array( $lines ) )
        {
            $this->Error = 'The ClickHouse server did not answer';
            $this->ErrorCode = -1;
            return false;
        }
        $status = 0;
        $answerHeaders = array();
        foreach ( $lines as $line )
        {
            if ( preg_match( '#^HTTP/\S+\s+(\d+)#', $line, $m ) )
            {
                $status = (int)$m[1];
                $answerHeaders = array();
                continue;
            }
            $colon = strpos( $line, ':' );
            if ( $colon !== false )
                $answerHeaders[strtolower( trim( substr( $line, 0, $colon ) ) )] = trim( substr( $line, $colon + 1 ) );
        }
        return array( $status, $answerHeaders, (string)$answer );
    }
}
