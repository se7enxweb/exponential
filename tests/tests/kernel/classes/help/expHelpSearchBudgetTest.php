<?php
/**
 * The time budget of the help search (doc/features/6.0/help-system.md, "Finding a page"). No database, no files.
 *
 *  SB-01  over an index of 7 000 generated pages (more than the help has; 5 752 on 19 October 2026), a query, with
 *         and without an audience filter, takes less than BUDGET ms of CPU time at the 95th percentile of 10 runs
 *         (CPU time, not wall time, so a busy machine does not fail it). The budget is
 *         the help's own target for the search itself (300 ms server-side), not the whole page; measured on alpha
 *         under load 44: p95 below 75 ms per query.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpSearchBudgetTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

if ( !class_exists( 'expHelpSearch' ) )
    require_once dirname( __DIR__, 5 ) . '/kernel/classes/help/exphelpsearch.php';

class expHelpSearchBudgetTest extends PHPUnit\Framework\TestCase
{
    const PAGES = 7000;
    const BUDGET = 300.0;

    /** @var expHelpSearch */
    private static $search;

    public static function setUpBeforeClass(): void
    {
        // a deterministic vocabulary with the shape of the help: common words on many pages, rare ones on few
        mt_srand( 612 );
        $common = array( 'content', 'publish', 'article', 'image', 'cache', 'template', 'operator', 'setting', 'user',
                         'role', 'policy', 'layout', 'zone', 'block', 'extension', 'design', 'search', 'language',
                         'version', 'draft', 'upload', 'hosting', 'server', 'database', 'velocity', 'password' );
        $rare = array();
        for ( $i = 0; $i < 4000; ++$i )
            $rare[] = 'term' . base_convert( (string)( $i * 7919 ), 10, 36 );
        $audiences = array( 'editor', 'admin', 'developer', 'user', 'designer', 'hosting', 'trainer', 'press' );
        $docs = array();
        for ( $p = 0; $p < self::PAGES; ++$p )
        {
            $words = array();
            for ( $w = 0; $w < 300; ++$w )
                $words[] = mt_rand( 0, 3 ) === 0 ? $common[mt_rand( 0, count( $common ) - 1 )] : $rare[mt_rand( 0, count( $rare ) - 1 )];
            $title = ucfirst( $common[$p % count( $common )] ) . ' ' . $rare[$p % count( $rare )];
            $docs['help/generated/page-' . $p . '.md'] = array(
                'title' => $title, 'audience' => array( $audiences[$p % count( $audiences )] ), 'category' => 'reference',
                'type' => 'help', 'text' => '# ' . $title . "\n\n## Section\n\n" . implode( ' ', $words ) . "\n",
            );
        }
        self::$search = expHelpSearch::fromDocuments( $docs );
        self::$search->search( 'warm up' );
    }

    /**
     * The CPU time of this process in ms (user + system): the work of a query, not the time it waited for a CPU, so
     * a busy test machine does not fail the budget
     */
    private static function cpu()
    {
        $u = getrusage();
        return ( $u['ru_utime.tv_sec'] + $u['ru_stime.tv_sec'] ) * 1000 + ( $u['ru_utime.tv_usec'] + $u['ru_stime.tv_usec'] ) / 1000;
    }

    /** SB-01 */
    public function testQueriesStayWithinTheBudget()
    {
        $over = array();
        foreach ( array( 'publish article', 'cache', 'template operator', 'upload image', 'layout zone block', 'velocity',
                         'hosting server database', 'password policy', 'zzqxv' ) as $query )
        {
            foreach ( array( array(), array( 'audience' => 'editor' ) ) as $filters )
            {
                $times = array();
                for ( $i = 0; $i < 10; ++$i )
                {
                    $start = self::cpu();
                    $result = self::$search->search( $query, $filters );
                    $times[] = self::cpu() - $start;
                }
                sort( $times );
                $p95 = $times[(int)round( 0.95 * ( count( $times ) - 1 ) )];
                if ( $p95 > self::BUDGET )
                    $over[] = sprintf( '%s%s: p95 %.0f ms (%s)', $query, $filters ? ' (editor)' : '', $p95, implode( ' ', array_map( 'round', $times ) ) );
                if ( $query !== 'zzqxv' )
                    $this->assertGreaterThan( 0, $result['total'], $query );
            }
        }
        $this->assertSame( array(), $over, 'queries over ' . self::BUDGET . ' ms' );
    }
}
