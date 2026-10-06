<?php
/**
 * File containing the expBenchmarkHttp class.
 *
 * The HTTP side of exp:benchmark: requests a list of URLs, several at a time, with PHP's own curl
 * (curl_multi), so no external program is needed. When ab, wrk or oha is installed it can be used instead
 * (--tool=ab|wrk|oha); their summaries are parsed into the same rows and the table says which tool measured.
 *
 * Every request is timed by curl itself (total time and time to the first byte), not by the loop around it,
 * so a slow event loop does not show up as a slow server. Requests per second is the number of successful
 * requests divided by the wall time of the batch.
 *
 * Read-only: GET only, no cookies are sent unless asked for (--cookie), nothing is ever posted.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expBenchmarkHttp
{
    const USER_AGENT = 'Exponential-Benchmark/1.0 (exp:benchmark)';

    /** The query parameter --cold adds to every request so no response cache can answer it. */
    const COLD_PARAMETER = '_bench';

    /** @var int a counter for the cache-busting parameter, unique within the process */
    protected static $counter = 0;

    /**
     * The options and their defaults.
     *
     * @return array
     */
    public static function defaults()
    {
        return array(
            'requests'    => 100,     // per URL and target
            'concurrency' => 4,
            'warmup'      => 5,       // per URL and target, not measured
            'rounds'      => 2,       // the requests are split into rounds; targets alternate their order
            'cold'        => false,   // add a unique query string to every request
            'insecure'    => false,   // do not verify TLS certificates
            'timeout'     => 30,      // seconds per request
            'keepalive'   => true,
            'http'        => '',      // '', '1.1' or '2'
            'encoding'    => 'gzip',  // the Accept-Encoding to send; '' for none
            'headers'     => array(),
            'auth'        => '',      // "user:password" for HTTP basic auth
            'resolve'     => array(), // curl --resolve entries, "host:port:address"
            'tool'        => 'curl',
            'duration'    => 10,      // seconds, for wrk, which runs for a time and not a number of requests
        );
    }

    /**
     * Which external load generators are installed.
     *
     * @return array name => path
     */
    public static function availableTools()
    {
        $found = array();
        foreach ( array( 'ab', 'wrk', 'oha' ) as $tool )
        {
            $path = self::which( $tool );
            if ( $path !== '' )
                $found[$tool] = $path;
        }
        return $found;
    }

    /**
     * Measures every URL against every target.
     *
     * The requests of a URL are split into rounds. Within a round every target gets its share back to back,
     * and the order of the targets is reversed every other round, so a change in the machine's load during
     * the run lands on all targets alike instead of on whichever ran last. That is the A/B method: the same
     * request, the same minute, both servers.
     *
     * @param array $targets list of base URLs ("https://example.com", "https://example.com:8080")
     * @param array $paths list of paths with their query ("/", "/news?page=2")
     * @param array $options see defaults()
     * @param callable|null $progress called with a line of text after every batch
     * @return array list of rows (expBenchmark::httpRow())
     */
    public static function run( $targets, $paths, $options = array(), $progress = null )
    {
        $options = array_merge( self::defaults(), (array)$options );
        $targets = array_values( (array)$targets );
        $rounds = max( 1, min( (int)$options['rounds'], (int)$options['requests'] ) );

        // One pool of connections per server for the whole run (see batch()).
        $connections = array();
        if ( $options['tool'] === 'curl' )
        {
            foreach ( $targets as $target )
                $connections[$target] = self::connections( $options );
        }

        $collected = array();
        foreach ( $paths as $path )
        {
            foreach ( $targets as $target )
            {
                $url = self::join( $target, $path );
                $collected[$url] = array( 'target' => $target, 'path' => $path, 'requests' => array(),
                                          'wall' => 0.0, 'parsed' => null );
                if ( (int)$options['warmup'] > 0 and $options['tool'] === 'curl' )
                {
                    // At least one request per connection, so that no measured request opens one.
                    self::batch( $url, max( (int)$options['warmup'], (int)$options['concurrency'] ), $options, $connections[$target] );
                }
            }

            if ( $options['tool'] !== 'curl' )
            {
                // An external tool gets the whole count in one go: its summary cannot be merged across rounds.
                foreach ( $targets as $target )
                {
                    $url = self::join( $target, $path );
                    $collected[$url]['parsed'] = self::runTool( $options['tool'], $url, $options );
                    if ( $progress )
                        call_user_func( $progress, sprintf( '  %s  %s', $options['tool'], $url ) );
                }
                continue;
            }

            for ( $round = 0; $round < $rounds; $round++ )
            {
                $count = self::share( (int)$options['requests'], $rounds, $round );
                $order = $round % 2 === 0 ? $targets : array_reverse( $targets );
                foreach ( $order as $target )
                {
                    $url = self::join( $target, $path );
                    $result = self::batch( $url, $count, $options, $connections[$target] );
                    $collected[$url]['requests'] = array_merge( $collected[$url]['requests'], $result['requests'] );
                    $collected[$url]['wall'] += $result['wall'];
                    if ( $progress )
                        call_user_func( $progress, sprintf( '  round %d/%d  %4d requests  %6.2fs  %s',
                                                            $round + 1, $rounds, $count, $result['wall'], $url ) );
                }
            }
        }

        $rows = array();
        foreach ( $collected as $item )
        {
            if ( $item['parsed'] !== null )
                $rows[] = expBenchmark::toolRow( $item['target'], $item['path'], $item['parsed'], $options['tool'] );
            else
                $rows[] = expBenchmark::httpRow( $item['target'], $item['path'], $item['requests'], $item['wall'] );
        }
        return $rows;
    }

    /**
     * Makes $count GET requests to $url, at most $options['concurrency'] at a time.
     *
     * A fixed pool of curl handles is reused, so a server that keeps connections alive is measured with
     * kept-alive connections, as a browser uses it (--no-keepalive opens a new one for every request).
     * Pass the same $connections to every batch of one server and the pool, and with it the open
     * connections, carries over from one batch to the next: only the warm-up pays the TLS handshakes.
     *
     * @param string $url
     * @param int $count
     * @param array $options
     * @param array|null $connections from connections(); null for a pool of this batch only
     * @return array( 'requests' => list of array( ms, ttfb, status, bytes, cache, server, error ), 'wall' => seconds )
     */
    public static function batch( $url, $count, $options = array(), $connections = null )
    {
        $options = array_merge( self::defaults(), (array)$options );
        $count = max( 0, (int)$count );
        if ( $connections === null )
            $connections = self::connections( $options );
        $multi = $connections['multi'];
        $pool = array_slice( $connections['pool'], 0, max( 1, min( count( $connections['pool'] ), $count ) ) );

        $headers = array();
        $results = array();
        $queued = $count;
        $inFlight = 0;

        $start = function ( $handle ) use ( &$headers, &$queued, &$inFlight, $multi, $url, $options )
        {
            $id = spl_object_id( $handle );
            $headers[$id] = array( 'cache' => '', 'server' => '' );
            curl_setopt( $handle, CURLOPT_URL, $options['cold'] ? self::bust( $url ) : $url );
            curl_setopt( $handle, CURLOPT_HEADERFUNCTION, function ( $ch, $line ) use ( &$headers, $id )
            {
                if ( stripos( $line, 'x-exp-cache:' ) === 0 or stripos( $line, 'x-cache:' ) === 0 )
                    $headers[$id]['cache'] = trim( substr( $line, strpos( $line, ':' ) + 1 ) );
                else if ( stripos( $line, 'server:' ) === 0 )
                    $headers[$id]['server'] = trim( substr( $line, 7 ) );
                return strlen( $line );
            } );
            curl_multi_add_handle( $multi, $handle );
            $queued--;
            $inFlight++;
        };

        $began = hrtime( true );
        foreach ( $pool as $handle )
        {
            if ( $queued <= 0 )
                break;
            $start( $handle );
        }

        while ( $inFlight > 0 )
        {
            do
            {
                $status = curl_multi_exec( $multi, $running );
            }
            while ( $status === CURLM_CALL_MULTI_PERFORM );

            while ( $info = curl_multi_info_read( $multi ) )
            {
                $handle = $info['handle'];
                $seen = $headers[spl_object_id( $handle )];
                $results[] = self::record( $handle, $info['result'], $seen['cache'], $seen['server'] );
                curl_multi_remove_handle( $multi, $handle );
                $inFlight--;
                if ( $queued > 0 )
                    $start( $handle );
            }

            if ( $inFlight > 0 and curl_multi_select( $multi, 0.25 ) === -1 )
                usleep( 1000 );
        }
        $wall = ( hrtime( true ) - $began ) / 1e9;

        return array( 'requests' => $results, 'wall' => $wall );
    }

    /**
     * A curl multi handle and a pool of $options['concurrency'] handles for batch(). They are freed with the
     * last reference to them (curl_close() does nothing since PHP 8.0 and is deprecated since 8.5).
     *
     * @param array $options
     * @return array( 'multi' => CurlMultiHandle, 'pool' => list of CurlHandle )
     */
    public static function connections( $options )
    {
        $options = array_merge( self::defaults(), (array)$options );
        $concurrency = max( 1, (int)$options['concurrency'] );
        $multi = curl_multi_init();
        if ( defined( 'CURLMOPT_MAX_HOST_CONNECTIONS' ) )
            curl_multi_setopt( $multi, CURLMOPT_MAX_HOST_CONNECTIONS, $concurrency );
        $pool = array();
        for ( $i = 0; $i < $concurrency; $i++ )
            $pool[] = self::handle( $options );
        return array( 'multi' => $multi, 'pool' => $pool );
    }

    /**
     * $url with a query parameter no response cache has seen before.
     *
     * @param string $url
     * @return string
     */
    public static function bust( $url )
    {
        self::$counter++;
        $value = dechex( (int)( microtime( true ) * 1000 ) ) . '-' . getmypid() . '-' . self::$counter;
        return $url . ( strpos( $url, '?' ) === false ? '?' : '&' ) . self::COLD_PARAMETER . '=' . $value;
    }

    /**
     * A base URL and a path as one URL; an absolute "path" is returned unchanged.
     *
     * @param string $base
     * @param string $path
     * @return string
     */
    public static function join( $base, $path )
    {
        if ( preg_match( '#^https?://#i', (string)$path ) )
            return (string)$path;
        return rtrim( (string)$base, '/' ) . '/' . ltrim( (string)$path, '/' );
    }

    /**
     * The scheme, host and port of a URL ("https://example.com:8080"), the form a target is named by.
     *
     * @param string $url
     * @return string empty when it is not an http(s) URL
     */
    public static function origin( $url )
    {
        $parts = parse_url( (string)$url );
        if ( !is_array( $parts ) or empty( $parts['host'] ) or empty( $parts['scheme'] )
             or !in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) )
            return '';
        $origin = strtolower( $parts['scheme'] ) . '://' . strtolower( $parts['host'] );
        if ( !empty( $parts['port'] ) )
            $origin .= ':' . (int)$parts['port'];
        return $origin;
    }

    /**
     * The path and query of a URL ("/news?page=2"), "/" when it has none.
     *
     * @param string $url
     * @return string
     */
    public static function pathOf( $url )
    {
        $parts = parse_url( (string)$url );
        $path = isset( $parts['path'] ) && $parts['path'] !== '' ? $parts['path'] : '/';
        if ( isset( $parts['query'] ) and $parts['query'] !== '' )
            $path .= '?' . $parts['query'];
        return $path;
    }

    /**
     * Whether a host is this machine's loopback: "localhost", 127.0.0.0/8 or ::1.
     *
     * @param string $host
     * @return bool
     */
    public static function isLoopback( $host )
    {
        $host = strtolower( trim( (string)$host, '[]' ) );
        if ( $host === 'localhost' or substr( $host, -10 ) === '.localhost' or $host === '::1' )
            return true;
        return (bool)preg_match( '/^127\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $host );
    }

    /**
     * Parses the output of ApacheBench (ab).
     *
     * @param string $text
     * @return array n, min, mean, median, p95, p99, max, stddev, rps, errors, bytes, status
     */
    public static function parseAb( $text )
    {
        $parsed = array();
        if ( preg_match( '/^Complete requests:\s+(\d+)/m', $text, $m ) )
            $parsed['n'] = (int)$m[1];
        if ( preg_match( '/^Requests per second:\s+([\d.]+)/m', $text, $m ) )
            $parsed['rps'] = (float)$m[1];
        if ( preg_match( '/^Time per request:\s+([\d.]+) \[ms\] \(mean\)$/m', $text, $m ) )
            $parsed['mean'] = (float)$m[1];
        if ( preg_match( '/^HTML transferred:\s+(\d+) bytes/m', $text, $m ) and !empty( $parsed['n'] ) )
            $parsed['bytes'] = (int)round( (int)$m[1] / $parsed['n'] );
        else if ( preg_match( '/^Document Length:\s+(\d+)/m', $text, $m ) )
            $parsed['bytes'] = (int)$m[1];
        if ( preg_match( '/^Total:\s+(\d+)\s+(\d+)\s+([\d.]+)\s+(\d+)\s+(\d+)/m', $text, $m ) )
        {
            $parsed['min'] = (float)$m[1];
            $parsed['stddev'] = (float)$m[3];
            $parsed['median'] = (float)$m[4];
            $parsed['max'] = (float)$m[5];
        }
        foreach ( array( 50 => 'median', 95 => 'p95', 99 => 'p99', 100 => 'max' ) as $percent => $field )
        {
            if ( preg_match( '/^\s+' . $percent . '%\s+(\d+)/m', $text, $m ) )
                $parsed[$field] = (float)$m[1];
        }
        $failed = preg_match( '/^Failed requests:\s+(\d+)/m', $text, $m ) ? (int)$m[1] : 0;
        $non2xx = preg_match( '/^Non-2xx responses:\s+(\d+)/m', $text, $m ) ? (int)$m[1] : 0;
        $parsed['errors'] = $non2xx + $failed;
        if ( isset( $parsed['n'] ) )
            $parsed['status'] = array( '2xx' => max( 0, $parsed['n'] - $non2xx ) ) + ( $non2xx ? array( 'other' => $non2xx ) : array() );
        return $parsed;
    }

    /**
     * Parses the output of wrk run with --latency.
     *
     * @param string $text
     * @return array
     */
    public static function parseWrk( $text )
    {
        $parsed = array();
        if ( preg_match( '/^\s+(\d+) requests in ([\d.]+)(\w+)/m', $text, $m ) )
            $parsed['n'] = (int)$m[1];
        if ( preg_match( '/^Requests\/sec:\s+([\d.]+)/m', $text, $m ) )
            $parsed['rps'] = (float)$m[1];
        if ( preg_match( '/^\s+Latency\s+([\d.]+\w+)\s+([\d.]+\w+)\s+([\d.]+\w+)/m', $text, $m ) )
        {
            $parsed['mean'] = self::wrkMs( $m[1] );
            $parsed['stddev'] = self::wrkMs( $m[2] );
            $parsed['max'] = self::wrkMs( $m[3] );
        }
        foreach ( array( 50 => 'median', 99 => 'p99' ) as $percent => $field )
        {
            if ( preg_match( '/^\s+' . $percent . '(?:\.\d+)?%\s+([\d.]+\w+)/m', $text, $m ) )
                $parsed[$field] = self::wrkMs( $m[1] );
        }
        $errors = 0;
        if ( preg_match( '/Socket errors: connect (\d+), read (\d+), write (\d+), timeout (\d+)/', $text, $m ) )
            $errors += (int)$m[1] + (int)$m[2] + (int)$m[3] + (int)$m[4];
        if ( preg_match( '/Non-2xx or 3xx responses:\s+(\d+)/', $text, $m ) )
            $errors += (int)$m[1];
        $parsed['errors'] = $errors;
        return $parsed;
    }

    /**
     * Parses the JSON summary of oha (--output-format json, or -j in older releases).
     *
     * @param string $text
     * @return array
     */
    public static function parseOha( $text )
    {
        $data = json_decode( (string)$text, true );
        if ( !is_array( $data ) )
            return array();
        $parsed = array();
        $summary = isset( $data['summary'] ) ? $data['summary'] : array();
        if ( isset( $summary['requestsPerSec'] ) )
            $parsed['rps'] = (float)$summary['requestsPerSec'];
        foreach ( array( 'fastest' => 'min', 'slowest' => 'max', 'average' => 'mean' ) as $from => $to )
        {
            if ( isset( $summary[$from] ) )
                $parsed[$to] = (float)$summary[$from] * 1000.0;
        }
        if ( isset( $summary['sizePerRequest'] ) )
            $parsed['bytes'] = (int)$summary['sizePerRequest'];
        $percentiles = isset( $data['latencyPercentiles'] ) ? $data['latencyPercentiles'] : array();
        foreach ( array( 'p50' => 'median', 'p95' => 'p95', 'p99' => 'p99' ) as $from => $to )
        {
            if ( isset( $percentiles[$from] ) )
                $parsed[$to] = (float)$percentiles[$from] * 1000.0;
        }
        $status = array();
        $ok = 0;
        $errors = 0;
        foreach ( ( isset( $data['statusCodeDistribution'] ) ? (array)$data['statusCodeDistribution'] : array() ) as $code => $count )
        {
            $status[(string)$code] = (int)$count;
            if ( (int)$code >= 400 )
                $errors += (int)$count;
            else
                $ok += (int)$count;
        }
        foreach ( ( isset( $data['errorDistribution'] ) ? (array)$data['errorDistribution'] : array() ) as $count )
            $errors += (int)$count;
        $parsed['status'] = $status;
        $parsed['errors'] = $errors;
        $parsed['n'] = $ok + $errors;
        return $parsed;
    }

    /**
     * Runs an external tool against one URL and parses its summary.
     *
     * @param string $tool ab, wrk or oha
     * @param string $url
     * @param array $options
     * @return array parsed summary (see parseAb())
     * @throws RuntimeException when the tool is not installed or printed nothing usable
     */
    public static function runTool( $tool, $url, $options )
    {
        $path = self::which( $tool );
        if ( $path === '' )
            throw new RuntimeException( "$tool is not installed" );

        $requests = max( 1, (int)$options['requests'] );
        $concurrency = max( 1, (int)$options['concurrency'] );
        $headers = self::headerLines( $options );
        $target = $options['cold'] ? self::bust( $url ) : $url;   // one query string for the whole run

        $command = array( $path );
        if ( $tool === 'ab' )
        {
            // -l: pages differ in length from one request to the next, which is not a failure
            $command = array_merge( $command, array( '-q', '-l', '-n', (string)$requests, '-c', (string)$concurrency, '-s', (string)(int)$options['timeout'] ) );
            if ( $options['keepalive'] )
                $command[] = '-k';
            if ( $options['auth'] !== '' )
                $command = array_merge( $command, array( '-A', $options['auth'] ) );
            foreach ( $headers as $line )
                $command = array_merge( $command, array( '-H', $line ) );
            $command[] = $target;
        }
        else if ( $tool === 'wrk' )
        {
            $threads = max( 1, min( 4, $concurrency ) );
            $command = array_merge( $command, array( '--latency', '-t', (string)$threads, '-c', (string)$concurrency,
                                                     '-d', max( 1, (int)$options['duration'] ) . 's', '--timeout', (int)$options['timeout'] . 's' ) );
            if ( $options['auth'] !== '' )
                $headers[] = 'Authorization: Basic ' . base64_encode( $options['auth'] );
            foreach ( $headers as $line )
                $command = array_merge( $command, array( '-H', $line ) );
            $command[] = $target;
        }
        else if ( $tool === 'oha' )
        {
            $command = array_merge( $command, array( '--no-tui', '--output-format', 'json', '-n', (string)$requests, '-c', (string)$concurrency ) );
            if ( $options['insecure'] )
                $command[] = '--insecure';
            if ( !$options['keepalive'] )
                $command[] = '--disable-keepalive';
            if ( $options['auth'] !== '' )
                $command = array_merge( $command, array( '-a', $options['auth'] ) );
            foreach ( $headers as $line )
                $command = array_merge( $command, array( '-H', $line ) );
            $command[] = $target;
        }
        else
        {
            throw new RuntimeException( "Unknown tool: $tool (curl, ab, wrk or oha)" );
        }

        $output = self::execute( $command );
        $parsed = $tool === 'ab' ? self::parseAb( $output ) : ( $tool === 'wrk' ? self::parseWrk( $output ) : self::parseOha( $output ) );
        if ( !isset( $parsed['rps'] ) )
            throw new RuntimeException( "$tool printed no summary for $url:\n" . trim( substr( $output, 0, 2000 ) ) );
        return $parsed;
    }

    /**
     * The extra request header lines, in "Name: value" form.
     */
    protected static function headerLines( $options )
    {
        $lines = array();
        foreach ( (array)$options['headers'] as $line )
        {
            if ( strpos( (string)$line, ':' ) !== false )
                $lines[] = (string)$line;
        }
        if ( $options['encoding'] !== '' )
            $lines[] = 'Accept-Encoding: ' . $options['encoding'];
        $lines[] = 'User-Agent: ' . self::USER_AGENT;
        return $lines;
    }

    /**
     * A curl handle with every option of the run except the URL.
     */
    protected static function handle( $options )
    {
        $handle = curl_init();
        curl_setopt_array( $handle, array(
            CURLOPT_HTTPGET        => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT        => max( 1, (int)$options['timeout'] ),
            CURLOPT_CONNECTTIMEOUT => min( 10, max( 1, (int)$options['timeout'] ) ),
            CURLOPT_SSL_VERIFYPEER => !$options['insecure'],
            CURLOPT_SSL_VERIFYHOST => $options['insecure'] ? 0 : 2,
            CURLOPT_FORBID_REUSE   => !$options['keepalive'],
            CURLOPT_FRESH_CONNECT  => !$options['keepalive'],
            CURLOPT_HTTPHEADER     => array_values( array_filter( self::headerLines( $options ), function ( $line )
            {
                return stripos( $line, 'Accept-Encoding:' ) !== 0;
            } ) ),
            // The body is counted, not kept: a run of thousands of pages would otherwise hold them all.
            CURLOPT_WRITEFUNCTION  => function ( $ch, $data ) { return strlen( $data ); },
        ) );
        if ( $options['encoding'] !== '' )
            curl_setopt( $handle, CURLOPT_ENCODING, $options['encoding'] );
        if ( $options['auth'] !== '' )
        {
            curl_setopt( $handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
            curl_setopt( $handle, CURLOPT_USERPWD, $options['auth'] );
        }
        if ( !empty( $options['resolve'] ) )
            curl_setopt( $handle, CURLOPT_RESOLVE, array_values( (array)$options['resolve'] ) );
        if ( $options['http'] === '1.1' )
            curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        else if ( $options['http'] === '2' and defined( 'CURL_HTTP_VERSION_2TLS' ) )
            curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2TLS );
        return $handle;
    }

    /**
     * One finished request as a sample.
     */
    protected static function record( $handle, $result, $cacheHeader, $serverHeader = '' )
    {
        $error = '';
        if ( $result !== CURLE_OK )
        {
            $error = curl_strerror( $result );
            $message = curl_error( $handle );
            if ( $message !== '' )
                $error = $message;
        }
        return array(
            'ms'     => (float)curl_getinfo( $handle, CURLINFO_TOTAL_TIME ) * 1000.0,
            'ttfb'   => (float)curl_getinfo( $handle, CURLINFO_STARTTRANSFER_TIME ) * 1000.0,
            'status' => (int)curl_getinfo( $handle, CURLINFO_RESPONSE_CODE ),
            'bytes'  => (int)curl_getinfo( $handle, CURLINFO_SIZE_DOWNLOAD ),
            'cache'  => (string)$cacheHeader,
            'server' => (string)$serverHeader,
            'error'  => $error,
        );
    }

    /**
     * How many of $total requests round $round of $rounds gets; the remainder goes to the first rounds.
     */
    protected static function share( $total, $rounds, $round )
    {
        $base = intdiv( $total, $rounds );
        return $base + ( $round < $total % $rounds ? 1 : 0 );
    }

    /**
     * wrk's "1.23ms", "850.00us", "2.01s" in milliseconds.
     */
    protected static function wrkMs( $value )
    {
        if ( !preg_match( '/^([\d.]+)(us|ms|s|m)$/', (string)$value, $m ) )
            return null;
        $number = (float)$m[1];
        switch ( $m[2] )
        {
            case 'us': return $number / 1000.0;
            case 's':  return $number * 1000.0;
            case 'm':  return $number * 60000.0;
        }
        return $number;
    }

    /**
     * The full path of a program on $PATH, empty when there is none.
     */
    protected static function which( $program )
    {
        foreach ( explode( PATH_SEPARATOR, (string)getenv( 'PATH' ) ) as $directory )
        {
            if ( $directory === '' )
                continue;
            $path = rtrim( $directory, '/' ) . '/' . $program;
            if ( is_file( $path ) and is_executable( $path ) )
                return $path;
        }
        return '';
    }

    /**
     * Runs a command without a shell (the arguments are never interpreted) and returns what it printed.
     */
    protected static function execute( $command )
    {
        $process = proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        if ( !is_resource( $process ) )
            throw new RuntimeException( 'Could not start ' . $command[0] );
        $output = stream_get_contents( $pipes[1] );
        $errors = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        proc_close( $process );
        return (string)$output . ( trim( (string)$errors ) !== '' ? "\n" . $errors : '' );
    }
}
