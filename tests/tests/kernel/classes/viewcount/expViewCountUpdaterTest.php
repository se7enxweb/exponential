<?php
/**
 * expViewCountUpdater from log line to node count, without a database: the alias lookup, the siteaccess matching,
 * the node check and the writer are stand-ins. A dry run writes nothing; a run counts each line once; a shared log
 * gives each line to its siteaccess; a log of one siteaccess per siteaccess keeps them apart; a failed write keeps
 * the state, so the lines are counted by the next run; the earlier cronjob's updateview.log is taken over; the
 * maintenance actions (--skip-to-end, --reset-state) and --from-start behave.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expViewCountTestFiles.php';

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expViewCountUpdaterTest extends PHPUnit\Framework\TestCase
{
    use expViewCountTestFiles;

    /** @var array[] the counts the writer was given, one list per call */
    private $written = array();

    /** @var bool what the writer answers */
    private $writerResult = true;

    /** @var array alias => node */
    private $aliases = array( 'news' => 10, 'news/story' => 11, 'about' => 12, 'fit-healthy/recipes' => 61, 'gone' => 99 );

    protected function tearDown(): void
    {
        $this->removeViewCountDir();
    }

    private function config( array $sites, array $settings = array() )
    {
        return new expViewCountConfiguration( $settings + array( 'state_dir' => $this->viewCountDir() . '/state', 'lock_timeout' => 0 ), $sites );
    }

    private function site( $name, $log, array $properties = array() )
    {
        return new expViewCountSite( $properties + array( 'name' => $name, 'logFile' => $log, 'indexPage' => '/content/view/full/2' ) );
    }

    private function updater( expViewCountConfiguration $config, array $options = array() )
    {
        $resolver = new expViewCountPathResolver( function ( $path )
        {
            $key = mb_strtolower( $path );
            return isset( $this->aliases[$key] ) ? $this->aliases[$key] : false;
        }, array(), function () {} );
        return new expViewCountUpdater( $config, $options + array( 'legacy_dir' => $this->viewCountDir() . '/legacy' ), array(
            'resolver' => $resolver,
            // /bold/... is bold, everything else site: like MatchOrder=uri;host with a default siteaccess
            'matcher' => function ( $host, $port, array $elements )
            {
                if ( $elements && in_array( $elements[0], array( 'bold', 'admin' ), true ) )
                    return array( $elements[0], 1 );
                return array( $host === 'other.example.com' ? 'other' : 'site', 0 );
            },
            'writer' => function ( array $counts )
            {
                $this->written[] = $counts;
                return $this->writerResult;
            },
            'node_exists' => function ( array $ids )
            {
                return array_values( array_diff( $ids, array( 99 ) ) );
            },
            'node_name' => function ( $id ) { return "Node $id"; },
        ) );
    }

    private function log( $name, array $lines )
    {
        $file = $this->viewCountDir() . '/logs/' . $name;
        if ( !is_dir( dirname( $file ) ) )
            mkdir( dirname( $file ), 0775, true );
        file_put_contents( $file, implode( '', $lines ), FILE_APPEND );
        return $file;
    }

    public function testASingleSiteRunCountsEachViewOnce()
    {
        $log = $this->log( 'access_log', array(
            self::combined( '/' ),
            self::combined( '/News/Story?utm=x' ),
            self::combined( '/news/story/(offset)/10' ),
            self::combined( '/content/view/full/42' ),
            self::combined( '/News', 404 ),
            self::combined( '/News', 200, 'Googlebot/2.1' ),
            self::combined( '/News', 200, 'Firefox', 'POST' ),
            self::combined( '/design/a/stylesheets/x.css' ),
            self::combined( '/user/login' ),
            self::combined( '/no-such-page' ),
            self::combined( '/gone' ),
            "not a log line\n",
        ) );
        $report = $this->updater( $this->config( array( $this->site( 'site', $log ) ) ), array( 'top' => 2 ) )->run();
        $this->assertSame( 0, $report['exit_code'], implode( "\n", expViewCountUpdater::lines( $report ) ) );
        $this->assertSame( array( array( 2 => 1, 11 => 2, 42 => 1 ) ), $this->sorted( $this->written ) );
        $this->assertSame( 4, $report['views'] );
        $this->assertSame( array( 'site' => array( 'views' => 4, 'nodes' => 3 ) ), $report['sites'] );
        $skipped = $report['logs'][0]['skipped'];
        $this->assertSame( 1, $skipped['status'] );
        $this->assertSame( 1, $skipped['bot'] );
        $this->assertSame( 1, $skipped['method'] );
        $this->assertSame( 1, $skipped['asset'] );
        $this->assertSame( 1, $skipped['excluded'] );
        $this->assertSame( 1, $skipped['not-content'] );
        $this->assertSame( 1, $skipped['no-node'] );
        $this->assertSame( 1, $skipped['malformed'] );
        $this->assertSame( 12, $report['logs'][0]['lines'] );
        $this->assertSame( 'saved', $report['logs'][0]['state'] );
        $this->assertSame( array( 'node_id' => 11, 'views' => 2, 'name' => 'Node 11' ), $report['top'][0] );

        // the second run finds nothing new; a new line is counted once
        $this->written = array();
        $again = $this->updater( $this->config( array( $this->site( 'site', $log ) ) ) )->run();
        $this->assertSame( 0, $again['views'] );
        $this->assertSame( array(), $this->written );
        $this->log( 'access_log', array( self::combined( '/about' ) ) );
        $third = $this->updater( $this->config( array( $this->site( 'site', $log ) ) ) )->run();
        $this->assertSame( array( array( 12 => 1 ) ), $this->written );
        $this->assertSame( 1, $third['logs'][0]['lines'] );
    }

    public function testADryRunWritesNeitherCountsNorState()
    {
        $log = $this->log( 'access_log', array( self::combined( '/news' ) ) );
        $config = $this->config( array( $this->site( 'site', $log ) ) );
        $report = $this->updater( $config, array( 'dry_run' => true ) )->run();
        $this->assertSame( 1, $report['views'] );
        $this->assertSame( array(), $this->written );
        $this->assertSame( 'dry-run', $report['logs'][0]['state'] );
        $this->assertFileDoesNotExist( $report['logs'][0]['state_file'] );
        $this->assertFalse( $report['written'] );
        // so the real run still counts it
        $this->assertSame( 1, $this->updater( $config )->run()['views'] );
    }

    public function testASharedLogGivesEachLineToItsSiteaccess()
    {
        $log = $this->log( 'access_ssl_log', array(
            self::combined( '/' ),
            self::combined( '/bold' ),
            self::combined( '/bold/news' ),
            self::combined( '/bold/user/login' ),
            self::combined( '/admin/content/view/full/2' ),
            self::combined( '/news' ),
        ) );
        $config = $this->config( array( $this->site( 'site', $log, array( 'indexPage' => '/content/view/full/2' ) ),
                                        $this->site( 'bold', $log, array( 'indexPage' => '/content/view/full/73' ) ) ) );
        $this->assertTrue( $config->jobs()[0]['match'] );
        $report = $this->updater( $config )->run();
        $this->assertSame( array( array( 2 => 1, 10 => 2, 73 => 1 ) ), $this->sorted( $this->written ) );
        $this->assertSame( array( 'bold' => array( 'views' => 2, 'nodes' => 2 ), 'site' => array( 'views' => 2, 'nodes' => 2 ) ), $report['sites'] );
        $this->assertSame( 1, $report['logs'][0]['skipped']['other-siteaccess'] );
        $this->assertSame( 1, $report['logs'][0]['skipped']['excluded'] );
        $this->assertCount( 1, $report['logs'], 'the shared log is read once' );
    }

    public function testRootHitsOfAPathPrefixSiteGoToItsIndexPage()
    {
        $log = $this->log( 'access_log', array( self::combined( '/' ), self::combined( '/Recipes' ), self::combined( '/about' ) ) );
        $site = $this->site( 'korg', $log, array( 'pathPrefixes' => array( 'Fit-Healthy' ), 'indexPage' => '/content/view/full/60' ) );
        $this->updater( $this->config( array( $site ) ) )->run();
        $this->assertSame( array( array( 12 => 1, 60 => 1, 61 => 1 ) ), $this->sorted( $this->written ) );
    }

    public function testSiteaccessesWithLogsOfTheirOwnStayApart()
    {
        $a = $this->log( 'a/access_log', array( self::combined( '/news' ) ) );
        $b = $this->log( 'b/access_log', array( self::combined( '/about' ), self::combined( '/about' ) ) );
        $config = $this->config( array( $this->site( 'site', $a ), $this->site( 'korg', $b ) ) );
        $this->assertCount( 2, $config->jobs() );
        $this->assertFalse( $config->jobs()[0]['match'] );
        $report = $this->updater( $config )->run();
        $this->assertSame( array( 'korg' => array( 'views' => 2, 'nodes' => 1 ), 'site' => array( 'views' => 1, 'nodes' => 1 ) ), $report['sites'] );
        $this->assertNotSame( $report['logs'][0]['state_file'], $report['logs'][1]['state_file'] );
        // a new line in one log is counted for its siteaccess only
        $this->written = array();
        $this->log( 'b/access_log', array( self::combined( '/news' ) ) );
        $next = $this->updater( $config )->run();
        $this->assertSame( array( 'korg' => array( 'views' => 1, 'nodes' => 1 ) ), $next['sites'] );
    }

    public function testHostsLimitTheLinesOfASiteaccess()
    {
        $log = $this->log( 'access_log', array(
            'www.example.com:443 192.0.2.1 - - [03/Nov/2026:10:00:00 +0000] "GET /news HTTP/1.1" 200 1 "-" "Firefox"' . "\n",
            'other.example.com:443 192.0.2.1 - - [03/Nov/2026:10:00:00 +0000] "GET /news HTTP/1.1" 200 1 "-" "Firefox"' . "\n",
        ) );
        $report = $this->updater( $this->config( array( $this->site( 'site', $log, array( 'hosts' => array( 'WWW.example.com' ) ) ) ) ) )->run();
        $this->assertSame( 1, $report['views'] );
        $this->assertSame( 1, $report['logs'][0]['skipped']['other-host'] );
    }

    public function testAFailedWriteKeepsTheStateSoTheNextRunCountsTheLines()
    {
        $log = $this->log( 'access_log', array( self::combined( '/news' ) ) );
        $config = $this->config( array( $this->site( 'site', $log ) ) );
        $this->writerResult = false;
        $failed = $this->updater( $config )->run();
        $this->assertSame( expViewCountUpdater::EXIT_ERROR, $failed['exit_code'] );
        $this->assertSame( 'unchanged', $failed['logs'][0]['state'] );
        $this->assertNotEmpty( $failed['errors'] );
        $this->writerResult = true;
        $this->written = array();
        $this->updater( $config )->run();
        $this->assertSame( array( array( 10 => 1 ) ), $this->written );
    }

    public function testTheEarlierCronjobsStateIsTakenOver()
    {
        $old = self::combined( '/news', 200, 'Firefox', 'GET', '03/Nov/2026:09:00:00 +0000' );
        $log = $this->log( 'access_log', array( $old, self::combined( '/about', 200, 'Firefox', 'GET', '03/Nov/2026:09:30:00 +0000' ) ) );
        mkdir( $this->viewCountDir() . '/legacy' );
        file_put_contents( $this->viewCountDir() . '/legacy/updateview.log', "# Finished at 03/11/2026 09:10\n# Last updated entry:\n" . $old . "\n" );
        $report = $this->updater( $this->config( array( $this->site( 'site', $log ) ) ) )->run();
        $this->assertSame( array( array( 12 => 1 ) ), $this->written, 'only the line after the last one the old cronjob counted' );
        $this->assertSame( array( 'site' => 'migrated' ), $report['logs'][0]['events'] );
        $state = new expViewCountState( $this->viewCountDir() . '/state', expViewCountConfiguration::logKey( $log ) );
        $this->assertSame( 'updateview.log', $state->entry( 'site' )['migrated_from'] );
    }

    public function testTheEarlierStateOfALogNoLongerInItCountsByTime()
    {
        $marker = self::combined( '/news', 200, 'Firefox', 'GET', '03/Nov/2026:09:00:00 +0000' );
        $log = $this->log( 'access_log', array( self::combined( '/news', 200, 'Firefox', 'GET', '03/Nov/2026:08:00:00 +0000' ),
                                                self::combined( '/about', 200, 'Firefox', 'GET', '03/Nov/2026:10:00:00 +0000' ) ) );
        mkdir( $this->viewCountDir() . '/legacy' );
        // the per-log name of the prototype this is built on
        file_put_contents( $this->viewCountDir() . '/legacy/updateview-access_log.log', "# Last updated entry:\n" . $marker );
        $report = $this->updater( $this->config( array( $this->site( 'site', $log ) ) ) )->run();
        $this->assertSame( array( array( 12 => 1 ) ), $this->written );
        $this->assertSame( 1, $report['logs'][0]['skipped']['before-resume'] );
    }

    public function testSkipToEndAndResetState()
    {
        $log = $this->log( 'access_log', array( self::combined( '/news' ), 'half a line' ) );
        $config = $this->config( array( $this->site( 'site', $log ) ) );
        $skip = $this->updater( $config, array( 'skip_to_end' => true ) )->run();
        $this->assertSame( 'skipped-to-end', $skip['logs'][0]['state'] );
        $this->assertSame( strlen( self::combined( '/news' ) ), $skip['logs'][0]['bytes'] );
        $this->assertSame( 0, $this->updater( $config )->run()['views'] );
        $this->assertSame( array(), $this->written );

        $reset = $this->updater( $config, array( 'reset_state' => true ) )->run();
        $this->assertSame( 'reset', $reset['logs'][0]['state'] );
        $this->assertSame( 1, $this->updater( $config )->run()['views'] );
    }

    public function testFromStartIsADryRunOnly()
    {
        $log = $this->log( 'access_log', array( self::combined( '/news' ) ) );
        $config = $this->config( array( $this->site( 'site', $log ) ) );
        $this->updater( $config )->run();
        $refused = $this->updater( $config, array( 'from_start' => true ) )->run();
        $this->assertSame( expViewCountUpdater::EXIT_ERROR, $refused['exit_code'] );
        $preview = $this->updater( $config, array( 'from_start' => true, 'dry_run' => true ) )->run();
        $this->assertSame( 1, $preview['views'] );
        $this->assertCount( 1, $this->written );
    }

    public function testAMissingLogAndALockedLog()
    {
        $missing = $this->updater( $this->config( array( $this->site( 'site', $this->viewCountDir() . '/logs/none' ) ) ) )->run();
        $this->assertSame( expViewCountUpdater::EXIT_ERROR, $missing['exit_code'] );
        $this->assertSame( 'missing', $missing['logs'][0]['state'] );

        $log = $this->log( 'access_log', array( self::combined( '/news' ) ) );
        $holder = new expViewCountState( $this->viewCountDir() . '/state', expViewCountConfiguration::logKey( $log ) );
        $this->assertTrue( $holder->lock( 0 ) );
        $locked = $this->updater( $this->config( array( $this->site( 'site', $log ) ) ) )->run();
        $holder->unlock();
        $this->assertSame( expViewCountUpdater::EXIT_LOCKED, $locked['exit_code'] );
        $this->assertSame( 'locked', $locked['logs'][0]['state'] );
        $this->assertSame( array(), $this->written );
    }

    public function testTheReportReadsAsText()
    {
        $log = $this->log( 'access_log', array( self::combined( '/news' ) ) );
        $lines = implode( "\n", expViewCountUpdater::lines( $this->updater( $this->config( array( $this->site( 'site', $log ) ) ), array( 'dry_run' => true ) )->run() ) );
        $this->assertStringContainsString( 'View count (dry run, nothing written): 1 view(s) of 1 node(s)', $lines );
        $this->assertStringContainsString( 'Siteaccesses: site', $lines );
        $this->assertStringNotContainsString( '192.0.2.10', $lines, 'no client address in the report' );
    }

    /** @return array the written counts, each list sorted by node */
    private function sorted( array $written )
    {
        foreach ( $written as $i => $counts )
            ksort( $written[$i] );
        return $written;
    }
}
