<?php
/**
 * Collecting a request's statistics without debug output (site.ini [DebugSettings] CollectStatsPolicyList[]):
 *   - eZDebug::isCollecting() and expRenderStats::collecting() follow debug output or the collect flag
 *   - with the flag alone the render statistics and the accumulators are noted, nothing else
 *   - resetRequest() and eZDebug::updateSettings() take the flag back for the next request
 *   - policyMatches(): module/function, module/*, malformed entries skipped
 *
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expRenderStatsCollectTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();

    protected function setUp(): void
    {
        foreach ( array( 'eZDebugEnabled', 'eZDebugCollect' ) as $name )
            $this->saved[$name] = array_key_exists( $name, $GLOBALS ) ? $GLOBALS[$name] : null;
        expRenderStats::resetRequest();
    }

    protected function tearDown(): void
    {
        expRenderStats::resetRequest();
        foreach ( $this->saved as $name => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $value;
        }
    }

    public function testCollectingFollowsDebugOrTheFlag()
    {
        $GLOBALS['eZDebugEnabled'] = false;
        $GLOBALS['eZDebugCollect'] = false;
        $this->assertFalse( eZDebug::isCollecting() );
        $this->assertFalse( expRenderStats::collecting() );
        $GLOBALS['eZDebugCollect'] = true;
        $this->assertTrue( eZDebug::isCollecting() );
        $this->assertTrue( expRenderStats::collecting() );
        $this->assertFalse( eZDebug::isDebugEnabled(), 'collecting never switches debug output on' );
        $GLOBALS['eZDebugCollect'] = false;
        $GLOBALS['eZDebugEnabled'] = true;
        $this->assertTrue( eZDebug::isCollecting() );
    }

    public function testTheFlagAloneNotesTheRenderStatisticsAndAccumulators()
    {
        $GLOBALS['eZDebugEnabled'] = false;
        $GLOBALS['eZDebugCollect'] = true;
        expRenderStats::noteViewCacheHit( 'var/cache/x.cache', time() - 5 );
        $view = expRenderStats::viewCache();
        $this->assertSame( 'hit', $view['state'] );
        eZDebug::accumulatorStart( 'expwt_test_query', 'expwt_test_total', 'Test queries' );
        eZDebug::accumulatorStop( 'expwt_test_query' );
        $list = eZDebug::instance()->TimeAccumulatorList;
        $this->assertSame( 1, $list['expwt_test_query']['count'] );
        unset( eZDebug::instance()->TimeAccumulatorList['expwt_test_query'], eZDebug::instance()->TimeAccumulatorList['expwt_test_total'] );
    }

    public function testResetRequestTakesTheFlagBack()
    {
        $GLOBALS['eZDebugCollect'] = true;
        expRenderStats::resetRequest();
        $this->assertFalse( $GLOBALS['eZDebugCollect'] );
    }

    public function testCheckCollectByPolicyKeepsDebugOutputAsItIs()
    {
        $GLOBALS['eZDebugEnabled'] = true;
        $this->assertTrue( expRenderStats::checkCollectByPolicy() );
    }

    public function testPolicyMatches()
    {
        $held = array( 'expwt/performance' );
        $holds = function ( $module, $function ) use ( &$held ) {
            return in_array( $module . '/' . $function, $held, true ) || in_array( $module . '/*', $held, true );
        };
        $this->assertTrue( expRenderStats::policyMatches( array( 'expwt/performance' ), $holds ) );
        $this->assertFalse( expRenderStats::policyMatches( array( 'setup/system_info' ), $holds ) );
        $this->assertTrue( expRenderStats::policyMatches( array( 'setup/system_info', ' expwt/performance ' ), $holds ) );
        $this->assertFalse( expRenderStats::policyMatches( array( '', 'expwt', 'expwt/perf ormance', '../x/y' ), $holds ) );
        $this->assertFalse( expRenderStats::policyMatches( array(), $holds ) );
        $held = array( 'expwt/*' );
        $this->assertTrue( expRenderStats::policyMatches( array( 'expwt/performance' ), $holds ) );
    }
}
