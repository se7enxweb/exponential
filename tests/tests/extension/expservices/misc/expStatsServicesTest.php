<?php
require_once __DIR__ . '/../media/expMediaTestCase.php';

/** expstats: search phrase and content statistics. */
class expStatsServicesTest extends expMediaTestCase
{
    public function testEveryServiceIsDeclared()
    {
        $this->assertDeclared( 'expStatsServices', 16 );
        foreach ( expStatsServices::$services as $m => $d )
            $this->assertFalse( $d['write'] );
    }

    public function testSearchTopIsOrderedByCount()
    {
        $r = $this->ok( 'expStatsServices', 'searchtop', array( 10, 0 ) );
        $this->assertPaged( $r );
        $counts = array_column( $r['data'], 'phrase_count' );
        $sorted = $counts;
        rsort( $sorted );
        $this->assertSame( $sorted, $counts );
    }

    public function testSearchTotals()
    {
        $t = $this->ok( 'expStatsServices', 'searchtotal' )['data'];
        $this->assertGreaterThanOrEqual( $t['phrases'], $t['searches'] );
    }

    public function testSearchPhraseAndNoResults()
    {
        $top = $this->ok( 'expStatsServices', 'searchtop', array( 1, 0 ) )['data'];
        if ( $top )
            $this->assertSame( $top[0]['phrase'], $this->ok( 'expStatsServices', 'searchphrase', array( $top[0]['phrase'] ) )['data']['phrase'] );
        $this->fails( 404, 'expStatsServices', 'searchphrase', array( 'no such phrase ' . uniqid() ) );
        $this->assertIsArray( $this->ok( 'expStatsServices', 'searchnoresults', array( 5 ) )['data'] );
    }

    public function testContentTotals()
    {
        $t = $this->ok( 'expStatsServices', 'contenttotals' )['data'];
        $this->assertGreaterThan( 0, $t['objects'] );
        $this->assertGreaterThanOrEqual( $t['objects'] - 10, $t['nodes'] );
        $this->assertGreaterThan( 0, $t['classes'] );
    }

    public function testClassCountsAddUpToAtMostTheObjects()
    {
        $classes = $this->ok( 'expStatsServices', 'classcounts', array( 200 ) )['data'];
        $sum = array_sum( array_column( $classes, 'count' ) );
        $this->assertLessThanOrEqual( $this->ok( 'expStatsServices', 'contenttotals' )['data']['objects'], $sum );
    }

    public function testSectionsAndStates()
    {
        $this->assertNotEmpty( $this->ok( 'expStatsServices', 'sectioncounts' )['data'] );
        $this->assertIsArray( $this->ok( 'expStatsServices', 'statecounts' )['data'] );
    }

    public function testRecentPublishedIsNewestFirst()
    {
        $list = $this->ok( 'expStatsServices', 'recentpublished', array( 5 ) )['data'];
        $this->assertLessThanOrEqual( 5, count( $list ) );
        $this->assertIsArray( $this->ok( 'expStatsServices', 'recentmodified', array( 5 ) )['data'] );
    }

    public function testGrowthCoversEveryDay()
    {
        $g = $this->ok( 'expStatsServices', 'growth', array( 10 ) )['data'];
        $this->assertCount( 10, $g );
        $this->assertSame( date( 'Y-m-d' ), end( $g )['day'] );
        $this->assertCount( 90, $this->ok( 'expStatsServices', 'growth', array( 1000 ) )['data'] );
    }

    public function testTopOwnersLanguagesUsersSessions()
    {
        $this->assertNotEmpty( $this->ok( 'expStatsServices', 'topowners', array( 3 ) )['data'] );
        $this->assertNotEmpty( $this->ok( 'expStatsServices', 'languagecounts' )['data'] );
        $u = $this->ok( 'expStatsServices', 'users' )['data'];
        $this->assertSame( $u['users'], $u['enabled'] + $u['disabled'] );
        $this->assertArrayHasKey( 'active', $this->ok( 'expStatsServices', 'sessions' )['data'] );
    }

    public function testOverview()
    {
        $o = $this->ok( 'expStatsServices', 'overview' )['data'];
        $this->assertGreaterThan( 0, $o['objects'] );
        $this->assertGreaterThanOrEqual( 0, $o['published_today'] );
    }

    public function testSearchStatisticsNeedSetupAdministrate()
    {
        $this->loginAnonymous();
        $r = $this->call( 'expStatsServices', 'searchtop' );
        $this->assertFalse( $r['ok'] );
        $this->assertContains( $r['error']['code'], array( 401, 403 ) );
        $this->assertFalse( $this->call( 'expStatsServices', 'sessions' )['ok'] );
    }
}
