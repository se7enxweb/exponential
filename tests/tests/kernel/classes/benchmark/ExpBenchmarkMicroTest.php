<?php
/**
 * The repeatable-run additions of exp:benchmark: p90 and the standard deviation, the micro rows, the
 * normalisation against the calibration loop and the comparison made with it (what the CI performance check
 * runs), the environment block of the JSON output and the commit read from .git. No network, no database,
 * no kernel.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/benchmark/ExpBenchmarkMicroTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group benchmark
 */

require_once __DIR__ . '/../../../../../kernel/classes/expbenchmark.php';
require_once __DIR__ . '/../../../../../kernel/classes/expbenchmarkmicro.php';

class ExpBenchmarkMicroTest extends \PHPUnit\Framework\TestCase
{
    /** @var string a scratch directory under var/tmp, removed after each test */
    protected $dir = '';

    protected function tearDown(): void
    {
        if ( $this->dir !== '' and is_dir( $this->dir ) )
            $this->removeTree( $this->dir );
        $this->dir = '';
    }

    public function testSummarizeHasP90AndTheSampleStandardDeviation()
    {
        $summary = expBenchmark::summarize( array( 10, 1, 9, 2, 8, 3, 7, 4, 6, 5 ) );
        // rank 0.9 * 9 = 8.1: 9 + 0.1 * (10 - 9)
        $this->assertEqualsWithDelta( 9.1, $summary['p90'], 1e-9 );
        $this->assertEqualsWithDelta( 5.5, $summary['median'], 1e-9 );
        // sample standard deviation of 1..10: sqrt(82.5 / 9)
        $this->assertEqualsWithDelta( sqrt( 82.5 / 9 ), $summary['stddev'], 1e-9 );
        $this->assertSame( 1.0, $summary['min'] );
        $this->assertSame( 10.0, $summary['max'] );

        $this->assertNull( expBenchmark::summarize( array() )['p90'] );
        $this->assertSame( 0.0, expBenchmark::summarize( array( 3 ) )['stddev'] );
    }

    public function testMicroRowReportsOpsPerSecondAtTheMedianAndTheNormalisedMedian()
    {
        $row = expBenchmark::microRow( 'uri_parse', array( 4.0, 2.0, 3.0 ), 500, 1.5, 0, 'ten URLs' );
        $this->assertSame( 'micro uri_parse', $row['key'] );
        $this->assertSame( 'micro', $row['kind'] );
        $this->assertSame( 500, $row['ops'] );
        $this->assertEqualsWithDelta( 3.0, $row['median'], 1e-9 );
        $this->assertEqualsWithDelta( 500 * 1000 / 3.0, $row['rps'], 1e-6 );
        $this->assertEqualsWithDelta( 2.0, $row['normalized'], 1e-9 );
        $this->assertSame( 3, $row['requests'] );
        $this->assertSame( 'ten URLs', $row['note'] );

        $failed = expBenchmark::microRow( 'x', array(), 10, 1.5, 4, 'failed: boom' );
        $this->assertNull( $failed['median'] );
        $this->assertNull( $failed['rps'] );
        $this->assertNull( $failed['normalized'] );
        $this->assertSame( 4, $failed['errors'] );
    }

    public function testNormalizeDividesByTheCalibrationAndRefusesNothing()
    {
        $this->assertEqualsWithDelta( 4.0, expBenchmark::normalize( 10.0, 2.5 ), 1e-9 );
        $this->assertNull( expBenchmark::normalize( null, 2.5 ) );
        $this->assertNull( expBenchmark::normalize( 10.0, null ) );
        $this->assertNull( expBenchmark::normalize( 10.0, 0.0 ) );
    }

    /**
     * A runner that is twice as slow at everything is not a regression: the calibration loop is twice as slow
     * too, and the normalised medians are unchanged.
     */
    public function testASlowerMachineIsNotARegression()
    {
        $baseline = $this->microRun( 2.0, array( 'a' => 10.0, 'b' => 1.0 ) );
        $slow = $this->microRun( 4.0, array( 'a' => 20.0, 'b' => 2.0 ) );
        $result = expBenchmark::compareNormalized( $baseline, $slow, 40 );
        $this->assertSame( 0, $result['regressions'] );
        $this->assertSame( 'normalized', $result['method'] );
        $this->assertCount( 2, $result['checks'] );
        $this->assertEqualsWithDelta( 0.5, $result['calibration']['speed'], 1e-9 );
        foreach ( $result['checks'] as $check )
            $this->assertEqualsWithDelta( 0.0, $check['change_pct'], 1e-9 );
    }

    public function testAProbeSlowerRelativeToTheCalibrationRegressesAboveTheThreshold()
    {
        $baseline = $this->microRun( 2.0, array( 'a' => 10.0, 'b' => 1.0, 'c' => 5.0 ) );
        // same machine; a is 60 % slower, b 30 % slower, c faster
        $now = $this->microRun( 2.0, array( 'a' => 16.0, 'b' => 1.3, 'c' => 4.0 ) );
        $result = expBenchmark::compareNormalized( $baseline, $now, 40 );
        $this->assertSame( 1, $result['regressions'] );
        $regressed = array();
        foreach ( $result['checks'] as $check )
        {
            if ( $check['regressed'] )
                $regressed[] = $check['key'];
        }
        $this->assertSame( array( 'micro a' ), $regressed );

        // at a lower threshold b counts too
        $this->assertSame( 2, expBenchmark::compareNormalized( $baseline, $now, 25 )['regressions'] );
    }

    public function testNormalisationTakesTheCalibrationFromEachRunNotFromTheRows()
    {
        // The rows of the baseline carry a stale 'normalized'; the comparison recomputes it from the medians.
        $baseline = $this->microRun( 2.0, array( 'a' => 10.0 ) );
        $baseline['rows'][0]['normalized'] = 99.0;
        $now = $this->microRun( 1.0, array( 'a' => 5.0 ) );   // twice as fast machine, same code
        $result = expBenchmark::compareNormalized( $baseline, $now, 40 );
        $this->assertSame( 0, $result['regressions'] );
        $this->assertEqualsWithDelta( 5.0, $result['checks'][0]['baseline'], 1e-9 );
        $this->assertEqualsWithDelta( 5.0, $result['checks'][0]['current'], 1e-9 );
        $this->assertEqualsWithDelta( 2.0, $result['calibration']['speed'], 1e-9 );
    }

    public function testMissingNewAndFailingProbes()
    {
        $baseline = $this->microRun( 2.0, array( 'a' => 10.0, 'gone' => 1.0 ) );
        $now = $this->microRun( 2.0, array( 'a' => 10.0, 'added' => 1.0 ) );
        $now['rows'][0]['errors'] = 3;
        $now['rows'][0]['requests'] = 13;
        $result = expBenchmark::compareNormalized( $baseline, $now, 40 );
        $this->assertSame( array( 'micro gone' ), $result['missing'] );
        $this->assertSame( array( 'micro added' ), $result['new'] );
        $this->assertSame( 1, $result['regressions'] );   // the error rate of a, not its time
        $metrics = array_column( $result['checks'], 'metric' );
        $this->assertContains( 'error_pct', $metrics );
    }

    public function testCompareNormalizedRefusesARunWithoutCalibration()
    {
        $baseline = $this->microRun( 2.0, array( 'a' => 10.0 ) );
        $now = $this->microRun( 2.0, array( 'a' => 10.0 ) );
        unset( $now['calibration'] );
        $this->expectException( RuntimeException::class );
        expBenchmark::compareNormalized( $baseline, $now );
    }

    public function testTheDefaultMicroThresholdIsLenient()
    {
        $this->assertSame( 40.0, expBenchmark::DEFAULT_MICRO_THRESHOLD );
        $baseline = $this->microRun( 2.0, array( 'a' => 10.0 ) );
        $this->assertSame( 0, expBenchmark::compareNormalized( $baseline, $this->microRun( 2.0, array( 'a' => 13.9 ) ) )['regressions'] );
        $this->assertSame( 1, expBenchmark::compareNormalized( $baseline, $this->microRun( 2.0, array( 'a' => 14.1 ) ) )['regressions'] );
    }

    public function testComparisonTableShowsTheNormalisedMedians()
    {
        $result = expBenchmark::compareNormalized( $this->microRun( 2.0, array( 'a' => 10.0 ) ), $this->microRun( 2.0, array( 'a' => 16.0 ) ) );
        $text = expBenchmark::comparisonTable( $result );
        $this->assertStringContainsString( 'median/calib', $text );
        $this->assertStringContainsString( '5.000', $text );
        $this->assertStringContainsString( '8.000', $text );
        $this->assertStringContainsString( '+60.0%', $text );
        $this->assertStringContainsString( 'REGRESSED', $text );
    }

    public function testMicroTableHasEveryColumn()
    {
        $rows = array( expBenchmark::microRow( 'uri_parse', array( 2.0, 2.5 ), 500, 1.0, 0, 'note text' ) );
        $lines = explode( "\n", rtrim( expBenchmark::table( $rows ) ) );
        $this->assertCount( 3, $lines );
        foreach ( array( 'probe', 'ops', 'median', 'p90', 'p99', 'sd', 'ops/s', 'x calib' ) as $column )
            $this->assertStringContainsString( $column, $lines[0] );
        $this->assertStringContainsString( '2.250', $lines[2] );   // normalised: 2.25 / 1.0
        $this->assertStringContainsString( 'note text', $lines[2] );
    }

    public function testHttpAndKernelTablesShowP90AndStandardDeviation()
    {
        $http = expBenchmark::table( array( expBenchmark::httpRow( 'https://a', '/', array( array( 'ms' => 10, 'status' => 200 ), array( 'ms' => 20, 'status' => 200 ) ), 1.0 ) ) );
        $kernel = expBenchmark::table( array( expBenchmark::kernelRow( 'db_query', array( 1.0, 2.0 ) ) ) );
        foreach ( array( $http, $kernel ) as $table )
        {
            $header = strtok( $table, "\n" );
            $this->assertStringContainsString( 'p90', $header );
            $this->assertStringContainsString( ' sd', $header );
        }
        // a row saved before p90 existed still prints
        $old = expBenchmark::kernelRow( 'x', array( 1.0 ) );
        unset( $old['p90'], $old['stddev'] );
        $this->assertStringContainsString( ' x ', expBenchmark::table( array( $old ) ) . ' ' );
    }

    public function testHttpRowRecordsTheServerHeader()
    {
        $requests = array(
            array( 'ms' => 1, 'status' => 200, 'server' => 'nginx' ),
            array( 'ms' => 1, 'status' => 200, 'server' => 'nginx' ),
            array( 'ms' => 1, 'status' => 200, 'server' => 'Apache' ),
        );
        $this->assertSame( 'nginx', expBenchmark::httpRow( 'https://a', '/', $requests, 1.0 )['server'] );
        $this->assertSame( '', expBenchmark::httpRow( 'https://a', '/', array( array( 'ms' => 1, 'status' => 200 ) ), 1.0 )['server'] );
    }

    public function testDocumentCarriesTheEnvironmentAndSurvivesJson()
    {
        $document = expBenchmark::document( 'micro', array( expBenchmark::microRow( 'a', array( 1.0, 2.0 ), 10, 1.0 ) ),
                                            array( 'repeat' => 2, 'warmup' => 0 ),
                                            array( 'calibration' => expBenchmarkMicro::calibrationSummary( array( 1.0 ), array( 1.2 ) ) ) );
        $decoded = json_decode( expBenchmark::json( $document ), true );
        $this->assertIsArray( $decoded );
        foreach ( array( 'date', 'php', 'sapi', 'engine', 'opcache', 'jit', 'xdebug', 'os', 'arch', 'cpus', 'load', 'git_commit', 'ci' ) as $key )
            $this->assertArrayHasKey( $key, $decoded['environment'], "environment.$key" );
        $this->assertSame( PHP_VERSION, $decoded['environment']['php'] );
        $this->assertIsBool( $decoded['environment']['opcache'] );
        $this->assertMatchesRegularExpression( '/^\d{4}-\d\d-\d\dT\d\d:\d\d:\d\dZ$/', $decoded['environment']['date'] );
        $this->assertSame( 'micro', $decoded['mode'] );
        $this->assertSame( 1.5, $decoded['rows'][0]['median'] );
        $this->assertEqualsWithDelta( 1.9, $decoded['rows'][0]['p90'], 1e-9 );
        $this->assertEqualsWithDelta( 20.0, $decoded['calibration']['drift_pct'], 1e-9 );

        // an environment passed in is kept as it is
        $fixed = expBenchmark::document( 'micro', array(), array(), array( 'environment' => array( 'php' => 'x' ) ) );
        $this->assertSame( array( 'php' => 'x' ), $fixed['environment'] );
    }

    public function testEnvironmentDifferencesNameWhatMovesTheNumbers()
    {
        $base = array( 'php' => '8.5.1', 'opcache' => false, 'jit' => false, 'xdebug' => false, 'load' => 1.0 );
        $this->assertSame( array(), expBenchmark::environmentDifferences( $base, array( 'php' => '8.5.11', 'load' => 9.0 ) + $base ) );
        $this->assertSame( array( 'PHP: 8.5.1 -> 8.4.3', 'OPcache: off -> on', 'Xdebug: off -> debug,coverage' ),
                           expBenchmark::environmentDifferences( $base, array( 'php' => '8.4.3', 'opcache' => true, 'xdebug' => 'debug,coverage' ) + $base ) );
        $this->assertSame( array(), expBenchmark::environmentDifferences( null, $base ) );
    }

    public function testSavedMicroRunComparesAfterLoading()
    {
        $this->dir = $this->scratch();
        $file = $this->dir . '/baseline.json';
        $this->assertTrue( expBenchmark::save( $file, $this->microRun( 2.0, array( 'a' => 10.0 ) ) ) );
        $loaded = expBenchmark::load( $file );
        $this->assertSame( 0, expBenchmark::compareNormalized( $loaded, $this->microRun( 3.0, array( 'a' => 15.0 ) ) )['regressions'] );
    }

    public function testCalibrationLoopDoesTheSameWorkEveryTime()
    {
        $first = expBenchmarkMicro::calibrationLoop();
        $this->assertIsInt( $first );
        $this->assertSame( $first, expBenchmarkMicro::calibrationLoop() );
        $samples = expBenchmarkMicro::calibrate( 3, 1 );
        $this->assertCount( 3, $samples );
        foreach ( $samples as $ms )
            $this->assertGreaterThan( 0.0, $ms );
    }

    public function testCalibrationSummaryReportsTheDrift()
    {
        $summary = expBenchmarkMicro::calibrationSummary( array( 2.0, 2.0, 2.0 ), array( 2.5, 2.5, 2.5 ) );
        $this->assertEqualsWithDelta( 2.0, $summary['before_median'], 1e-9 );
        $this->assertEqualsWithDelta( 2.5, $summary['after_median'], 1e-9 );
        $this->assertEqualsWithDelta( 25.0, $summary['drift_pct'], 1e-9 );
        $this->assertSame( 6, $summary['n'] );
        $this->assertSame( expBenchmarkMicro::calibrationLoop(), $summary['checksum'] );
    }

    public function testProbeNamesAreFixedAndOrdered()
    {
        $names = expBenchmarkMicro::probeNames();
        $this->assertSame( 'template_compile', $names[0] );
        $this->assertSame( count( $names ), count( array_unique( $names ) ) );
        foreach ( array( 'ini_parse', 'autoload_miss', 'uri_parse', 'i18n_lookup', 'datatype_validate' ) as $name )
            $this->assertContains( $name, $names );
    }

    public function testGitCommitFollowsRefsPackedRefsAndWorktrees()
    {
        $this->dir = $this->scratch();
        $sha1 = str_repeat( 'a1', 20 );
        $sha2 = str_repeat( 'b2', 20 );
        $sha3 = str_repeat( 'c3', 20 );

        // a loose ref
        mkdir( $this->dir . '/repo/.git/refs/heads', 0777, true );
        file_put_contents( $this->dir . '/repo/.git/HEAD', "ref: refs/heads/main\n" );
        file_put_contents( $this->dir . '/repo/.git/refs/heads/main', "$sha1\n" );
        $this->assertSame( $sha1, expBenchmark::gitCommit( $this->dir . '/repo' ) );

        // a packed ref
        file_put_contents( $this->dir . '/repo/.git/HEAD', "ref: refs/heads/packed\n" );
        file_put_contents( $this->dir . '/repo/.git/packed-refs', "# pack-refs with: peeled fully-peeled sorted\n$sha2 refs/heads/packed\n" );
        $this->assertSame( $sha2, expBenchmark::gitCommit( $this->dir . '/repo' ) );

        // a detached HEAD
        file_put_contents( $this->dir . '/repo/.git/HEAD', "$sha3\n" );
        $this->assertSame( $sha3, expBenchmark::gitCommit( $this->dir . '/repo' ) );

        // a linked worktree: .git is a file, its HEAD names a branch kept in the common directory
        mkdir( $this->dir . '/repo/.git/worktrees/wt', 0777, true );
        file_put_contents( $this->dir . '/repo/.git/worktrees/wt/HEAD', "ref: refs/heads/main\n" );
        file_put_contents( $this->dir . '/repo/.git/worktrees/wt/commondir', "../..\n" );
        mkdir( $this->dir . '/wt' );
        file_put_contents( $this->dir . '/wt/.git', 'gitdir: ' . $this->dir . "/repo/.git/worktrees/wt\n" );
        $this->assertSame( $sha1, expBenchmark::gitCommit( $this->dir . '/wt' ) );

        // nothing there
        mkdir( $this->dir . '/none' );
        $expected = preg_match( '/^[0-9a-f]{40,64}$/', (string)getenv( 'GITHUB_SHA' ) ) ? getenv( 'GITHUB_SHA' ) : '';
        $this->assertSame( $expected, expBenchmark::gitCommit( $this->dir . '/none' ) );
    }

    /**
     * A micro run as the command saves it.
     *
     * @param float $calibration median of the calibration loop, ms
     * @param array $medians probe => median ms
     */
    protected function microRun( $calibration, $medians )
    {
        $rows = array();
        foreach ( $medians as $name => $median )
            $rows[] = expBenchmark::microRow( $name, array( $median, $median, $median ), 100, $calibration );
        return array( 'tool' => 'exp:benchmark', 'format' => expBenchmark::FORMAT, 'mode' => 'micro',
                      'calibration' => expBenchmark::summarize( array( $calibration ) ), 'rows' => $rows );
    }

    protected function scratch()
    {
        $dir = __DIR__ . '/../../../../../var/tmp/benchmark-test/micro-' . getmypid() . '-' . mt_rand();
        mkdir( $dir, 0777, true );
        return $dir;
    }

    protected function removeTree( $dir )
    {
        foreach ( array_diff( (array)scandir( $dir ), array( '.', '..' ) ) as $entry )
        {
            $path = $dir . '/' . $entry;
            if ( is_dir( $path ) and !is_link( $path ) )
                $this->removeTree( $path );
            else
                unlink( $path );
        }
        rmdir( $dir );
    }
}
