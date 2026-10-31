<?php
/**
 * The speed measurement of the GraphQL API against decision 18 (doc/eng-us/features/6.0/graphql.md, "Speed"; the
 * command exp:graphql:bench): sends one kind of request after the other to an end point, N times each, and gives the
 * median, p95 and max of each kind with the verdict.
 *
 * The kinds of request (rows):
 *
 *   get       a cached persisted query, GET ?id=...      (the early answer of index_rest.php)
 *   304       the same with If-None-Match                (the early answer, no body)
 *   post      a cached persisted query, POST JSON {id}   (the early answer)
 *   kernel    a cached persisted query that starts the kernel: a form POST, which the early answer does not read;
 *             it costs what the first request of new credentials costs (the kernel, the user, the cache)
 *   uncached  the text of the query as an ad-hoc POST    (never cached: the query runs)
 *
 * Decision 18: every cached answer within 33 ms (the hard limit, on every request: the max), and the targets 20 ms,
 * then 13 ms, for the median and the p95. Uncached answers have no limit; they are measured.
 *
 * A time is from the request being sent to the last byte of the answer: one connection is kept for all requests, so
 * connecting and the TLS handshake are not in it. The first requests of a row warm it up and are not counted.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLBench
{
    /** Decision 18: no cached answer may take longer, in ms. */
    const HARD_LIMIT_MS = 33.0;

    /** Decision 18: the targets for the median and the p95 of cached answers, in ms (the first, then the second). */
    const TARGETS_MS = array( 20.0, 13.0 );

    /** Every row, in the order they run. */
    const ROWS = array( 'get', '304', 'post', 'kernel', 'uncached' );

    /** The rows of cached answers (the hard limit applies). */
    const CACHED_ROWS = array( 'get', '304', 'post', 'kernel' );

    /** @var string */
    protected $url;

    /** @var string the name or SHA-256 of the persisted query */
    protected $id;

    /** @var array|null */
    protected $variables;

    /** @var string|null the text of the query (the uncached row) */
    protected $query;

    /** @var string[] extra request headers ("Name: value") */
    protected $headers;

    /** @var callable function( array $request ): array status, cache, etag, ms (null ms: no answer) */
    protected $transport;

    /** @var resource|CurlHandle|null */
    protected $curl = null;

    /** @var bool */
    protected $insecure;

    /**
     * @param string $url the end point (https://example.com/api/graphql)
     * @param string $id the name or SHA-256 of a persisted query
     * @param array $options variables (array), query (the text, for the uncached row), headers (string[]), insecure
     *                       (do not check the TLS certificate), transport (a callable instead of HTTP: tests)
     */
    public function __construct( $url, $id, array $options = array() )
    {
        $this->url = (string)$url;
        $this->id = (string)$id;
        $this->variables = isset( $options['variables'] ) && is_array( $options['variables'] ) ? $options['variables'] : null;
        $this->query = isset( $options['query'] ) && is_string( $options['query'] ) && trim( $options['query'] ) !== '' ? $options['query'] : null;
        $this->headers = isset( $options['headers'] ) ? array_values( (array)$options['headers'] ) : array();
        $this->insecure = !empty( $options['insecure'] );
        $this->transport = isset( $options['transport'] ) && is_callable( $options['transport'] ) ? $options['transport'] : array( $this, 'httpSend' );
    }

    /**
     * @param string $row
     * @return bool the row can run (the uncached row needs the text of the query)
     */
    public function canRun( $row )
    {
        return in_array( $row, self::ROWS, true ) && ( $row !== 'uncached' || $this->query !== null );
    }

    /**
     * The request of a row.
     *
     * @param string $row
     * @param string|null $etag If-None-Match (the 304 row)
     * @return array method, url, headers (string[]), body (string|null)
     */
    public function request( $row, $etag = null )
    {
        $variables = $this->variables !== null ? json_encode( $this->variables, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : null;
        $headers = $this->headers;
        switch ( $row )
        {
            case 'get':
            case '304':
                $query = array( 'id' => $this->id );
                if ( $variables !== null )
                    $query['variables'] = $variables;
                if ( $row === '304' && $etag !== null )
                    $headers[] = 'If-None-Match: ' . $etag;
                return array( 'method' => 'GET', 'url' => $this->withQuery( $query ), 'headers' => $headers, 'body' => null );
            case 'post':
                $body = array( 'id' => $this->id );
                if ( $this->variables !== null )
                    $body['variables'] = $this->variables;
                $headers[] = 'Content-Type: application/json';
                return array( 'method' => 'POST', 'url' => $this->url, 'headers' => $headers, 'body' => json_encode( $body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            case 'kernel':
                $form = array( 'id' => $this->id );
                if ( $variables !== null )
                    $form['variables'] = $variables;
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                return array( 'method' => 'POST', 'url' => $this->url, 'headers' => $headers, 'body' => http_build_query( $form, '', '&', PHP_QUERY_RFC3986 ) );
            case 'uncached':
                $body = array( 'query' => (string)$this->query );
                if ( $this->variables !== null )
                    $body['variables'] = $this->variables;
                $headers[] = 'Content-Type: application/json';
                return array( 'method' => 'POST', 'url' => $this->url, 'headers' => $headers, 'body' => json_encode( $body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        }
        throw new InvalidArgumentException( "Unknown row: $row" );
    }

    /**
     * @param array $query
     * @return string the end point with this query string
     */
    protected function withQuery( array $query )
    {
        return $this->url . ( strpos( $this->url, '?' ) === false ? '?' : '&' ) . http_build_query( $query, '', '&', PHP_QUERY_RFC3986 );
    }

    /**
     * Runs a row: $warmUp requests that are not counted (the answer is cached by the first), then $n one after the
     * other.
     *
     * @param string $row
     * @param int $n
     * @param int $warmUp
     * @return array row, n, times (ms), statuses (status => count), cache (X-GraphQL-Cache => count), failed (requests
     *               without an answer), and the stats() of the times
     */
    public function runRow( $row, $n, $warmUp = 2 )
    {
        $etag = null;
        for ( $i = 0; $i < max( 1, (int)$warmUp ); $i++ )
        {
            $answer = call_user_func( $this->transport, $this->request( $row === '304' ? 'get' : $row ) );
            if ( isset( $answer['etag'] ) && $answer['etag'] !== '' )
                $etag = $answer['etag'];
        }
        $times = array();
        $statuses = array();
        $cache = array();
        $failed = 0;
        for ( $i = 0; $i < (int)$n; $i++ )
        {
            $answer = call_user_func( $this->transport, $this->request( $row, $etag ) );
            if ( !isset( $answer['ms'] ) || $answer['ms'] === null )
            {
                $failed++;
                continue;
            }
            $times[] = (float)$answer['ms'];
            $status = (int)$answer['status'];
            $statuses[$status] = isset( $statuses[$status] ) ? $statuses[$status] + 1 : 1;
            $state = isset( $answer['cache'] ) && $answer['cache'] !== '' ? (string)$answer['cache'] : '-';
            $cache[$state] = isset( $cache[$state] ) ? $cache[$state] + 1 : 1;
        }
        ksort( $statuses );
        ksort( $cache );
        return array( 'row' => $row, 'n' => (int)$n, 'times' => $times, 'statuses' => $statuses, 'cache' => $cache, 'failed' => $failed )
               + self::stats( $times );
    }

    /**
     * @param float[] $times ms
     * @return array median, p95 (nearest rank), max, min, mean; null each without times
     */
    public static function stats( array $times )
    {
        if ( !$times )
            return array( 'median' => null, 'p95' => null, 'max' => null, 'min' => null, 'mean' => null );
        sort( $times );
        $count = count( $times );
        $median = $count % 2 ? $times[intdiv( $count, 2 )] : ( $times[$count / 2 - 1] + $times[$count / 2] ) / 2;
        return array( 'median' => round( $median, 2 ), 'p95' => round( $times[(int)ceil( 0.95 * $count ) - 1], 2 ),
                      'max' => round( $times[$count - 1], 2 ), 'min' => round( $times[0], 2 ), 'mean' => round( array_sum( $times ) / $count, 2 ) );
    }

    /**
     * The verdict of a row against decision 18.
     *
     * @param array $result of runRow()
     * @return array verdict (PASS, FAIL or MEASURED), hardLimit (bool|null), targets (ms => bool), reason
     */
    public static function verdict( array $result )
    {
        $row = $result['row'];
        $targets = array();
        foreach ( self::TARGETS_MS as $target )
            $targets[(string)$target] = $result['median'] !== null && $result['median'] <= $target && $result['p95'] <= $target;
        if ( !in_array( $row, self::CACHED_ROWS, true ) )
            return array( 'verdict' => 'MEASURED', 'hardLimit' => null, 'targets' => array(), 'reason' => 'no limit (decision 18: uncached answers are measured)' );
        $good = 0;
        foreach ( $result['statuses'] as $status => $count )
        {
            if ( $row === '304' ? $status === 304 : $status === 200 )
                $good += $count;
        }
        $hits = $row === '304' ? $good : ( isset( $result['cache']['HIT'] ) ? $result['cache']['HIT'] : 0 );
        $reason = '';
        if ( $result['failed'] > 0 || !$result['times'] )
            $reason = 'requests without an answer';
        else if ( $good < count( $result['times'] ) )
            $reason = 'answers with another status: ' . json_encode( $result['statuses'] );
        else if ( $hits < count( $result['times'] ) )
            $reason = 'answers not from the cache (X-GraphQL-Cache): ' . json_encode( $result['cache'] );
        else if ( $result['max'] > self::HARD_LIMIT_MS )
            $reason = sprintf( 'max %.1f ms is over the hard limit of %d ms', $result['max'], self::HARD_LIMIT_MS );
        return array( 'verdict' => $reason === '' ? 'PASS' : 'FAIL', 'hardLimit' => $reason === '', 'targets' => $targets, 'reason' => $reason );
    }

    /**
     * Sends a request over HTTP with one curl handle for all (keep-alive).
     *
     * @param array $request of request()
     * @return array status, cache (X-GraphQL-Cache), etag, ms (null: no answer)
     */
    public function httpSend( array $request )
    {
        if ( $this->curl === null )
            $this->curl = curl_init();
        $headers = array();
        $options = array( CURLOPT_URL => $request['url'], CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30,
                          CURLOPT_HTTPHEADER => array_merge( $request['headers'], array( 'Expect:' ) ),
                          CURLOPT_SSL_VERIFYPEER => !$this->insecure, CURLOPT_SSL_VERIFYHOST => $this->insecure ? 0 : 2,
                          CURLOPT_HEADERFUNCTION => function ( $handle, $line ) use ( &$headers ) {
                              $parts = explode( ':', $line, 2 );
                              if ( count( $parts ) === 2 )
                                  $headers[strtolower( trim( $parts[0] ) )] = trim( $parts[1] );
                              return strlen( $line );
                          } );
        if ( $request['method'] === 'POST' )
        {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = (string)$request['body'];
        }
        else
        {
            $options[CURLOPT_HTTPGET] = true;
        }
        curl_setopt_array( $this->curl, $options );
        if ( curl_exec( $this->curl ) === false )
            return array( 'status' => 0, 'cache' => '', 'etag' => '', 'ms' => null, 'error' => curl_error( $this->curl ) );
        $total = curl_getinfo( $this->curl, CURLINFO_TOTAL_TIME_T );
        $pretransfer = curl_getinfo( $this->curl, CURLINFO_PRETRANSFER_TIME_T );
        return array( 'status' => (int)curl_getinfo( $this->curl, CURLINFO_RESPONSE_CODE ),
                      'cache' => isset( $headers['x-graphql-cache'] ) ? $headers['x-graphql-cache'] : '',
                      'etag' => isset( $headers['etag'] ) ? $headers['etag'] : '',
                      'ms' => max( 0, $total - $pretransfer ) / 1000 );
    }
}

?>
