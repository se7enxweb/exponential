<?php
/**
 * The statistics, the regression check, the output formats and the parsers of the external tools of
 * exp:benchmark (expBenchmark, expBenchmarkHttp). No network, no database, no kernel.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/benchmark/ExpBenchmarkTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group benchmark
 */

require_once __DIR__ . '/../../../../../kernel/classes/expbenchmark.php';
require_once __DIR__ . '/../../../../../kernel/classes/expbenchmarkhttp.php';

class ExpBenchmarkTest extends \PHPUnit\Framework\TestCase
{
    public function testPercentileInterpolatesBetweenRanks()
    {
        $sorted = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 );
        $this->assertSame( 1.0, expBenchmark::percentile( $sorted, 0 ) );
        $this->assertSame( 10.0, expBenchmark::percentile( $sorted, 100 ) );
        $this->assertEqualsWithDelta( 5.5, expBenchmark::percentile( $sorted, 50 ), 1e-9 );
        // rank 0.95 * 9 = 8.55: 9 + 0.55 * (10 - 9)
        $this->assertEqualsWithDelta( 9.55, expBenchmark::percentile( $sorted, 95 ), 1e-9 );
        $this->assertEqualsWithDelta( 9.91, expBenchmark::percentile( $sorted, 99 ), 1e-9 );
    }

    public function testPercentileOfOneAndOfNoSample()
    {
        $this->assertNull( expBenchmark::percentile( array(), 50 ) );
        $this->assertSame( 42.0, expBenchmark::percentile( array( 42 ), 99 ) );
    }

    public function testSummarizeSortsAndComputesEveryField()
    {
        $summary = expBenchmark::summarize( array( 30, 10, 20, 40 ) );
        $this->assertSame( 4, $summary['n'] );
        $this->assertSame( 10.0, $summary['min'] );
        $this->assertSame( 40.0, $summary['max'] );
        $this->assertEqualsWithDelta( 25.0, $summary['mean'], 1e-9 );
        $this->assertEqualsWithDelta( 25.0, $summary['median'], 1e-9 );
        $this->assertEqualsWithDelta( 38.5, $summary['p95'], 1e-9 );
        $this->assertEqualsWithDelta( 12.9099, $summary['stddev'], 1e-4 );
    }

    public function testSummarizeOfNothing()
    {
        $summary = expBenchmark::summarize( array() );
        $this->assertSame( 0, $summary['n'] );
        $this->assertNull( $summary['median'] );
    }

    public function testHttpRowCountsStatusesCacheAnswersAndErrors()
    {
        $requests = array(
            array( 'ms' => 10, 'ttfb' => 5, 'status' => 200, 'bytes' => 1000, 'cache' => 'HIT', 'error' => '' ),
            array( 'ms' => 20, 'ttfb' => 6, 'status' => 200, 'bytes' => 1000, 'cache' => 'BYPASS (query string)', 'error' => '' ),
            array( 'ms' => 30, 'ttfb' => 7, 'status' => 502, 'bytes' => 100, 'cache' => '', 'error' => '' ),
            array( 'ms' => 0, 'ttfb' => 0, 'status' => 0, 'bytes' => 0, 'cache' => '', 'error' => 'Connection refused' ),
        );
        $row = expBenchmark::httpRow( 'https://example.com/', '/news', $requests, 2.0 );

        $this->assertSame( 'https://example.com /news', $row['key'] );
        $this->assertSame( 'http', $row['kind'] );
        $this->assertSame( 4, $row['requests'] );
        $this->assertSame( 2, $row['errors'] );
        // The refused connection has no time and is left out of the timings; the 502 is in them.
        $this->assertSame( 3, $row['n'] );
        $this->assertSame( 10.0, $row['min'] );
        $this->assertSame( 30.0, $row['max'] );
        $this->assertEqualsWithDelta( 1.0, $row['rps'], 1e-9 );   // two successful requests in two seconds
        $this->assertSame( array( '200' => 2, '502' => 1, 'failed' => 1 ), $row['status'] );
        $this->assertSame( array( 'HIT' => 1, 'BYPASS' => 1 ), $row['cache'] );
        $this->assertSame( 700, $row['bytes'] );
        $this->assertSame( array( 'HTTP 502' => 1, 'Connection refused' => 1 ), $row['error_messages'] );
    }

    public function testKernelRowUsesTheMeanForOperationsPerSecond()
    {
        $row = expBenchmark::kernelRow( 'db_query', array( 2, 2, 2, 2 ), 1, 1024, 'note' );
        $this->assertSame( 'kernel db_query', $row['key'] );
        $this->assertSame( 'kernel', $row['kind'] );
        $this->assertEqualsWithDelta( 500.0, $row['rps'], 1e-9 );
        $this->assertSame( 5, $row['requests'] );
        $this->assertSame( 1024, $row['mem_peak'] );
    }

    /**
     * A row like httpRow() makes, with only the fields compare() reads.
     */
    protected function row( $key, $median, $p95, $rps, $errors = 0, $requests = 100, $kind = 'http' )
    {
        return array( 'key' => $key, 'kind' => $kind, 'median' => $median, 'p95' => $p95, 'rps' => $rps,
                      'errors' => $errors, 'requests' => $requests, 'n' => $requests );
    }

    public function testCompareFindsNoRegressionWithinTheThreshold()
    {
        $base = array( $this->row( 'a /', 100, 150, 50 ) );
        $now = array( $this->row( 'a /', 110, 160, 46 ) );   // +10 %, +6.7 %, -8 %
        $result = expBenchmark::compare( $base, $now, 15 );
        $this->assertSame( 0, $result['regressions'] );
        $this->assertCount( 4, $result['checks'] );   // median, p95, rps, error rate
    }

    public function testCompareFlagsSlowerLatencyAndFewerRequestsPerSecond()
    {
        $base = array( $this->row( 'a /', 100, 150, 50 ) );
        $now = array( $this->row( 'a /', 130, 150, 40 ) );   // median +30 %, rps -20 %
        $result = expBenchmark::compare( $base, $now, 15 );
        $this->assertSame( 2, $result['regressions'] );
        $regressed = array();
        foreach ( $result['checks'] as $check )
        {
            if ( $check['regressed'] )
                $regressed[] = $check['metric'];
        }
        $this->assertSame( array( 'median', 'rps' ), $regressed );
    }

    public function testCompareIgnoresChangesBelowTheNoiseFloor()
    {
        // 0.2 ms to 0.5 ms is +150 %, but less than 1 ms: noise, not a regression.
        $base = array( $this->row( 'kernel db_query', 0.2, 0.3, null, 0, 20, 'kernel' ) );
        $now = array( $this->row( 'kernel db_query', 0.5, 0.6, null, 0, 20, 'kernel' ) );
        $this->assertSame( 0, expBenchmark::compare( $base, $now, 15, 1.0 )['regressions'] );
        $this->assertSame( 2, expBenchmark::compare( $base, $now, 15, 0.0 )['regressions'] );
    }

    public function testFewerRequestsPerSecondWithoutSlowerRequestsIsNoise()
    {
        // A cache hit of 1.3 ms against 1.5 ms: 30 % fewer requests per second, but 0.2 ms is the machine.
        $base = array( $this->row( 'a /', 1.3, 2.0, 2000 ) );
        $now = array( $this->row( 'a /', 1.5, 2.2, 1400 ) );
        $this->assertSame( 0, expBenchmark::compare( $base, $now, 15, 1.0 )['regressions'] );
    }

    public function testCompareDoesNotCheckRequestsPerSecondOfKernelRows()
    {
        $base = array( $this->row( 'kernel render_warm', 10, 12, 100, 0, 20, 'kernel' ) );
        $now = array( $this->row( 'kernel render_warm', 10, 12, 10, 0, 20, 'kernel' ) );
        $result = expBenchmark::compare( $base, $now, 15 );
        $this->assertSame( 0, $result['regressions'] );
        foreach ( $result['checks'] as $check )
            $this->assertNotSame( 'rps', $check['metric'] );
    }

    public function testCompareFlagsARisingErrorRate()
    {
        $base = array( $this->row( 'a /', 100, 150, 50, 0 ) );
        $now = array( $this->row( 'a /', 100, 150, 50, 5 ) );   // 5 % errors
        $result = expBenchmark::compare( $base, $now, 15 );
        $this->assertSame( 1, $result['regressions'] );
    }

    public function testCompareReportsMissingAndNewRowsWithoutFailing()
    {
        $base = array( $this->row( 'a /', 100, 150, 50 ), $this->row( 'a /gone', 100, 150, 50 ) );
        $now = array( $this->row( 'a /', 100, 150, 50 ), $this->row( 'a /new', 100, 150, 50 ) );
        $result = expBenchmark::compare( $base, $now );
        $this->assertSame( 0, $result['regressions'] );
        $this->assertSame( array( 'a /gone' ), $result['missing'] );
        $this->assertSame( array( 'a /new' ), $result['new'] );
    }

    public function testPairsComparesEveryLaterTargetWithTheFirst()
    {
        $rows = array(
            expBenchmark::httpRow( 'https://a', '/', array( array( 'ms' => 100, 'status' => 200 ) ), 1.0 ),
            expBenchmark::httpRow( 'https://b', '/', array( array( 'ms' => 50, 'status' => 200 ), array( 'ms' => 50, 'status' => 200 ) ), 1.0 ),
        );
        $pairs = expBenchmark::pairs( $rows, array( 'https://a', 'https://b' ) );
        $this->assertCount( 1, $pairs );
        $this->assertEqualsWithDelta( 0.5, $pairs[0]['median_ratio'], 1e-9 );
        $this->assertEqualsWithDelta( 2.0, $pairs[0]['rps_ratio'], 1e-9 );
    }

    public function testSaveAndLoadRoundTrip()
    {
        $dir = __DIR__ . '/../../../../../var/tmp/benchmark-test';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $file = $dir . '/roundtrip-' . getmypid() . '.json';

        $rows = array( expBenchmark::kernelRow( 'db_query', array( 1.5, 2.5 ) ) );
        $document = expBenchmark::document( 'kernel', $rows, array( 'repeat' => 2 ) );
        $this->assertTrue( expBenchmark::save( $file, $document ) );
        $loaded = expBenchmark::load( $file );
        unlink( $file );

        $this->assertSame( expBenchmark::FORMAT, $loaded['format'] );
        $this->assertSame( 'kernel', $loaded['mode'] );
        $this->assertSame( 'kernel db_query', $loaded['rows'][0]['key'] );
        $this->assertEqualsWithDelta( 2.0, $loaded['rows'][0]['median'], 1e-9 );
    }

    public function testLoadRefusesSomethingElse()
    {
        $dir = __DIR__ . '/../../../../../var/tmp/benchmark-test';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $file = $dir . '/other-' . getmypid() . '.json';
        file_put_contents( $file, '{"rows": [], "format": 999}' );
        try
        {
            expBenchmark::load( $file );
            $this->fail( 'A run of another format was accepted' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'format', $e->getMessage() );
        }
        finally
        {
            unlink( $file );
        }
    }

    public function testCsvHasAHeaderAndOneLinePerRow()
    {
        $rows = array(
            expBenchmark::httpRow( 'https://a', '/x,y', array( array( 'ms' => 10, 'status' => 200, 'cache' => 'HIT' ) ), 1.0 ),
            expBenchmark::httpRow( 'https://a', '/z', array( array( 'ms' => 20, 'status' => 404 ) ), 1.0 ),
        );
        $lines = array_values( array_filter( explode( "\n", expBenchmark::csv( $rows ) ) ) );
        $this->assertCount( 3, $lines );
        $header = str_getcsv( $lines[0], ',', '"', '' );
        $first = array_combine( $header, str_getcsv( $lines[1], ',', '"', '' ) );
        $this->assertSame( '/x,y', $first['name'] );   // quoted, the comma survives
        $this->assertSame( '200:1', $first['status'] );
        $this->assertSame( 'HIT:1', $first['cache'] );
        $second = array_combine( $header, str_getcsv( $lines[2], ',', '"', '' ) );
        $this->assertSame( '1', $second['errors'] );
    }

    public function testJsonIsValidAndKeepsFloats()
    {
        $document = expBenchmark::document( 'kernel', array( expBenchmark::kernelRow( 'x', array( 1.0 ) ) ) );
        $decoded = json_decode( expBenchmark::json( $document ), true );
        $this->assertSame( 'exp:benchmark', $decoded['tool'] );
        $this->assertSame( 1.0, $decoded['rows'][0]['median'] );
    }

    public function testTableAlignsColumnsAndShowsEveryRow()
    {
        $rows = array(
            expBenchmark::httpRow( 'https://example.com', '/', array( array( 'ms' => 12.345, 'status' => 200, 'bytes' => 2048 ) ), 1.0 ),
            expBenchmark::httpRow( 'https://example.com:8080', '/a-longer-path', array( array( 'ms' => 150, 'status' => 200 ) ), 1.0 ),
        );
        $lines = explode( "\n", rtrim( expBenchmark::table( $rows ) ) );
        $this->assertCount( 4, $lines );   // header, rule, two rows
        $this->assertStringContainsString( 'median', $lines[0] );
        $this->assertStringContainsString( 'example.com:8080', $lines[3] );
        $this->assertStringContainsString( '12.3', $lines[2] );
        $this->assertStringContainsString( '2.0k', $lines[2] );
        $this->assertSame( strlen( $lines[1] ), strlen( rtrim( $lines[1] ) ) );
    }

    public function testComparisonTableShowsOnlyRegressionsUnlessAskedForAll()
    {
        $comparison = expBenchmark::compare( array( $this->row( 'a /', 100, 150, 50 ) ), array( $this->row( 'a /', 200, 150, 50 ) ) );
        $short = expBenchmark::comparisonTable( $comparison );
        $this->assertStringContainsString( 'REGRESSED', $short );
        $this->assertStringNotContainsString( ' ok', $short );
        $this->assertStringContainsString( ' ok', expBenchmark::comparisonTable( $comparison, true ) );
    }

    public function testUrlHelpers()
    {
        $this->assertSame( 'https://example.com:8080', expBenchmarkHttp::origin( 'HTTPS://Example.com:8080/a/b?c=1' ) );
        $this->assertSame( '', expBenchmarkHttp::origin( '/relative' ) );
        $this->assertSame( '', expBenchmarkHttp::origin( 'ftp://example.com/' ) );
        $this->assertSame( '/a/b?c=1', expBenchmarkHttp::pathOf( 'https://example.com/a/b?c=1' ) );
        $this->assertSame( '/', expBenchmarkHttp::pathOf( 'https://example.com' ) );
        $this->assertSame( 'https://example.com/news', expBenchmarkHttp::join( 'https://example.com/', '/news' ) );
        $this->assertSame( 'https://other.example/x', expBenchmarkHttp::join( 'https://example.com', 'https://other.example/x' ) );
    }

    public function testBustAddsAUniqueParameter()
    {
        $a = expBenchmarkHttp::bust( 'https://example.com/' );
        $b = expBenchmarkHttp::bust( 'https://example.com/' );
        $this->assertNotSame( $a, $b );
        $this->assertStringContainsString( '/?_bench=', $a );
        $this->assertStringContainsString( '?page=2&_bench=', expBenchmarkHttp::bust( 'https://example.com/?page=2' ) );
    }

    public function testLoopbackDetection()
    {
        foreach ( array( 'localhost', '127.0.0.1', '127.1.2.3', '::1', '[::1]', 'app.localhost' ) as $host )
            $this->assertTrue( expBenchmarkHttp::isLoopback( $host ), $host );
        foreach ( array( 'example.com', '10.0.0.1', '128.0.0.1', '127.0.0.1.example.com', 'localhost.example.com' ) as $host )
            $this->assertFalse( expBenchmarkHttp::isLoopback( $host ), $host );
    }

    public function testParseAb()
    {
        $text = <<<'AB'
Server Software:        Apache
Document Path:          /
Document Length:        Variable

Concurrency Level:      2
Time taken for tests:   0.106 seconds
Complete requests:      10
Failed requests:        0
Non-2xx responses:      1
Total transferred:      180000 bytes
HTML transferred:       170000 bytes
Requests per second:    93.99 [#/sec] (mean)
Time per request:       21.278 [ms] (mean)
Time per request:       10.639 [ms] (mean, across all concurrent requests)

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        3    6   2.1      6      10
Processing:     7   15   5.3     13      22
Waiting:        6   13   5.0     12      20
Total:         10   21   5.0     18      28

Percentage of the requests served within a certain time (ms)
  50%     18
  66%     21
  75%     25
  80%     27
  90%     28
  95%     28
  98%     28
  99%     28
 100%     28 (longest request)
AB;
        $parsed = expBenchmarkHttp::parseAb( $text );
        $this->assertSame( 10, $parsed['n'] );
        $this->assertSame( 93.99, $parsed['rps'] );
        $this->assertSame( 21.278, $parsed['mean'] );
        $this->assertSame( 10.0, $parsed['min'] );
        $this->assertSame( 18.0, $parsed['median'] );
        $this->assertSame( 28.0, $parsed['p95'] );
        $this->assertSame( 28.0, $parsed['max'] );
        $this->assertSame( 17000, $parsed['bytes'] );
        $this->assertSame( 1, $parsed['errors'] );
        $this->assertSame( array( '2xx' => 9, 'other' => 1 ), $parsed['status'] );
    }

    public function testParseWrk()
    {
        $text = <<<'WRK'
Running 10s test @ https://example.com/
  2 threads and 4 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency    12.50ms    3.20ms   1.02s    90.00%
    Req/Sec   160.00     20.00   200.00     70.00%
  Latency Distribution
     50%   11.80ms
     75%   13.00ms
     90%   15.00ms
     99%  850.00us
  3200 requests in 10.01s, 50.00MB read
  Socket errors: connect 0, read 1, write 0, timeout 2
  Non-2xx or 3xx responses: 4
Requests/sec:    319.68
Transfer/sec:      5.00MB
WRK;
        $parsed = expBenchmarkHttp::parseWrk( $text );
        $this->assertSame( 3200, $parsed['n'] );
        $this->assertSame( 319.68, $parsed['rps'] );
        $this->assertEqualsWithDelta( 12.5, $parsed['mean'], 1e-9 );
        $this->assertEqualsWithDelta( 1020.0, $parsed['max'], 1e-9 );
        $this->assertEqualsWithDelta( 11.8, $parsed['median'], 1e-9 );
        $this->assertEqualsWithDelta( 0.85, $parsed['p99'], 1e-9 );
        $this->assertSame( 7, $parsed['errors'] );
    }

    public function testParseOha()
    {
        $json = json_encode( array(
            'summary' => array( 'successRate' => 1.0, 'total' => 1.0, 'slowest' => 0.05, 'fastest' => 0.01,
                                'average' => 0.02, 'requestsPerSec' => 199.5, 'sizePerRequest' => 1000 ),
            'latencyPercentiles' => array( 'p50' => 0.018, 'p95' => 0.04, 'p99' => 0.049 ),
            'statusCodeDistribution' => array( '200' => 198, '503' => 2 ),
            'errorDistribution' => array( 'connection reset' => 1 ),
        ) );
        $parsed = expBenchmarkHttp::parseOha( $json );
        $this->assertSame( 199.5, $parsed['rps'] );
        $this->assertEqualsWithDelta( 10.0, $parsed['min'], 1e-9 );
        $this->assertEqualsWithDelta( 18.0, $parsed['median'], 1e-9 );
        $this->assertEqualsWithDelta( 49.0, $parsed['p99'], 1e-9 );
        $this->assertSame( 3, $parsed['errors'] );
        $this->assertSame( 201, $parsed['n'] );
        $this->assertSame( array( '200' => 198, '503' => 2 ), $parsed['status'] );
    }

    public function testToolRowKeepsWhatTheToolDidNotReportEmpty()
    {
        $row = expBenchmark::toolRow( 'https://a', '/', array( 'n' => 10, 'rps' => 5.0, 'median' => 20.0 ), 'ab' );
        $this->assertSame( 'ab', $row['tool'] );
        $this->assertSame( 20.0, $row['median'] );
        $this->assertNull( $row['p99'] );
        $this->assertSame( 0, $row['errors'] );
    }
}
