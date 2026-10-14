<?php
/**
 * The SQL query cache in the Exp Debug bar: the level of the summary chip (expDebugBarSummary::queryCacheLevel()),
 * the chip itself (expDebugBarReport), and the strings the Cache tab's query cache section uses.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDebugBarQueryCacheTest extends PHPUnit\Framework\TestCase
{
    private function snapshot( array $values = array() )
    {
        return $values + array( 'mode' => 'shared', 'apcu' => true, 'storage' => 'apcu', 'generation' => 3, 'hits' => 8, 'misses' => 2, 'stores' => 2,
                                'not_cached' => 1, 'writes' => 0, 'hit_rate' => 80.0, 'saved_ms' => 186.4, 'hits_unmeasured' => 0,
                                'skipped' => array(), 'stale' => array(), 'stale_more' => 0 );
    }

    /** DQ-01 */
    public function testLevel()
    {
        $limits = array( 50, 10 );
        $this->assertSame( 'ok', expDebugBarSummary::queryCacheLevel( $this->snapshot(), $limits ) );
        $this->assertSame( 'ok', expDebugBarSummary::queryCacheLevel( $this->snapshot( array( 'hit_rate' => 50.0 ) ), $limits ) );
        $this->assertSame( 'warn', expDebugBarSummary::queryCacheLevel( $this->snapshot( array( 'hit_rate' => 49.9 ) ), $limits ) );
        $this->assertSame( 'high', expDebugBarSummary::queryCacheLevel( $this->snapshot( array( 'hit_rate' => 9.9 ) ), $limits ) );
        $this->assertSame( 'ok', expDebugBarSummary::queryCacheLevel( $this->snapshot( array( 'hit_rate' => null ) ), $limits ), 'no lookup' );
        $this->assertSame( 'warn', expDebugBarSummary::queryCacheLevel( $this->snapshot( array( 'stale' => array( 'CALL x()' ) ) ), $limits ) );
        $this->assertSame( 'warn', expDebugBarSummary::queryCacheLevel( array( 'stale_count' => 2 ) + $this->snapshot( array( 'stale' => array() ) ), $limits ), 'the summary carries a count' );
        $this->assertSame( 'high', expDebugBarSummary::queryCacheLevel( $this->snapshot( array( 'apcu' => false, 'storage' => null ) ), $limits ), 'Storage=apcu without APCu' );
        $this->assertSame( 'ok', expDebugBarSummary::queryCacheLevel( $this->snapshot( array( 'apcu' => false, 'storage' => 'file' ) ), $limits ), 'without APCu in files' );
        $this->assertSame( 'ok', expDebugBarSummary::queryCacheLevel( $this->snapshot( array( 'mode' => 'request', 'apcu' => false, 'storage' => null ) ), $limits ) );
    }

    /** DQ-02 */
    public function testChip()
    {
        $html = $this->summaryHtml( array( 'measured' => true ) + $this->snapshot( array( 'hit_rate' => 83.3 ) ) + array( 'level' => 'warn' ) );
        $this->assertMatchesRegularExpression( '/<button [^>]*data-tab="sql" data-key="querycache" data-level="warn"[^>]*>QC 83 % · -186 ms \(186 %\)<\/button>/u', $html, 'the page took 100 ms' );
        $this->assertStringContainsString( 'The saved time is 186 % of this page&#039;s time: without the cache about 286 ms instead of 100 ms', $html );
        $this->assertStringContainsString( '8 hits, 2 misses, 2 stored, 1 not cached, 0 writes, mode shared (apcu)', $html );

        $html = $this->summaryHtml( array( 'measured' => true ) + $this->snapshot( array( 'storage' => 'file' ) ) + array( 'level' => 'ok' ) );
        $this->assertStringContainsString( 'mode shared (file)', $html );

        $html = $this->summaryHtml( array( 'measured' => true ) + $this->snapshot( array( 'hit_rate' => null, 'apcu' => false, 'storage' => null ) ) + array( 'level' => 'high' ) );
        $this->assertMatchesRegularExpression( '/data-key="querycache" data-level="high"[^>]*>QC - · -186 ms \(186 %\)</u', $html );
        $this->assertStringContainsString( 'mode shared (no APCu)', $html );

        $html = $this->summaryHtml( array( 'measured' => true ) + $this->snapshot( array( 'saved_ms' => 2761.0 ) ) + array( 'level' => 'ok' ) );
        $this->assertStringContainsString( '(2761 %)', $html, 'no thousands separator: "2,761 %" reads as a decimal in German' );
        $this->assertStringContainsString( 'about 2861 ms instead of 100 ms', $html );

        $html = $this->summaryHtml( array( 'measured' => true ) + $this->snapshot( array( 'saved_ms' => 0.2 ) ) + array( 'level' => 'ok' ) );
        $this->assertMatchesRegularExpression( '/>QC 80 % · 0 ms</u', $html, 'nothing saved: no minus sign, no share' );
        $this->assertStringNotContainsString( 'The saved time is', $html );

        $this->assertStringNotContainsString( 'querycache', $this->summaryHtml( null ), 'cache off: no chip' );
        $this->assertStringNotContainsString( 'querycache', $this->summaryHtml( array( 'measured' => false ) ), 'nothing measured: no chip' );
    }

    /** DQ-05 */
    public function testSavedShare()
    {
        $this->assertSame( 200, eZDBQueryCache::savedShare( 200, 100 ), 'saved twice the request: three times as long without the cache' );
        $this->assertSame( 50, eZDBQueryCache::savedShare( 50.2, 100.0 ) );
        $this->assertNull( eZDBQueryCache::savedShare( 0.3, 100 ), 'nothing saved' );
        $this->assertNull( eZDBQueryCache::savedShare( 100, null ), 'the request time unknown' );
        $this->assertNull( eZDBQueryCache::savedShare( 100, 0 ) );
    }

    /** DQ-03 */
    public function testDefaultThreshold()
    {
        $ini = eZINI::instance( 'debugbar.ini' );
        $this->assertSame( '50;10', $ini->variable( 'DebugBarSettings', 'Thresholds' )['querycache_hit_rate'] );
    }

    /** DQ-04 */
    public function testSummaryCarriesTheNumberOfStaleWritesNotTheirStatements()
    {
        $keys = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly' );
        $saved = array();
        foreach ( $keys as $key )
            $saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $dir = sys_get_temp_dir() . '/querycache-dq04-' . getmypid() . '-' . mt_rand();
        mkdir( $dir, 0770, true );
        $GLOBALS['eZDebugGlobalInstance'] = $debug = new eZDebug();
        foreach ( $debug->LogFiles as $level => $file )
            $debug->LogFiles[$level] = array( $dir . '/', $file[1] );
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;
        try
        {
            eZDBQueryCache::setStateDir( $dir );
            eZDBQueryCache::resetRequest();
            eZDBQueryCache::setSettings( array( 'mode' => 'request', 'maxAge' => 300, 'maxRows' => 5000, 'exclude' => array() ) );
            $db = new stdClass();
            $db->TransactionCounter = 0;
            eZDBQueryCache::noteWrite( $db, "CALL some_procedure( 'secret' )" );
            $q = expDebugBarSummary::queryCache( array( 50, 10 ) );
            $this->assertTrue( $q['measured'] );
            $this->assertSame( 1, $q['stale_count'] );
            $this->assertArrayNotHasKey( 'stale', $q );
            $this->assertSame( 'warn', $q['level'] );
            $this->assertStringNotContainsString( 'some_procedure', json_encode( $q ) );
        }
        finally
        {
            eZDBQueryCache::resetRequest();
            eZDBQueryCache::setStateDir( null );
            eZDBQueryCache::setSettings( null );
            foreach ( $saved as $key => $value )
            {
                if ( $value === null )
                    unset( $GLOBALS[$key] );
                else
                    $GLOBALS[$key] = $value[0];
            }
            foreach ( glob( $dir . '/*' ) as $f )
                unlink( $f );
            rmdir( $dir );
        }
    }

    private function summaryHtml( $queryCache )
    {
        $summary = array( 'time' => 0.1, 'sql_count' => 3, 'sql_ms' => 2.0, 'memory' => 1048576, 'memory_limit' => 0, 'templates' => null,
                          'warnings' => 0, 'errors' => 0, 'notices' => 0, 'deprecations' => 0, 'levels' => array(), 'querycache' => $queryCache );
        $m = new ReflectionMethod( 'expDebugBarReport', 'summaryHtml' );
        $m->setAccessible( true );
        return $m->invoke( null, $summary );
    }
}
