<?php
/**
 * The place of a page in the help's page tree from the settings (exphelp.ini [TreeSettings] PageOrder[],
 * doc/eng-us/features/6.0/help-system.md, "Order of the pages"), without a database.
 *
 *  HO-01  PageOrder[<page id>] wins over a page's front matter order and places a page without front matter
 *  HO-02  a page without an entry keeps its front matter order, 0 for a folder index, else 1000; the tree sorts by it
 *  HO-03  only entries with a page id and an integer are kept
 *  HO-04  the shipped exphelp.ini lists exactly the built-in default, for pages that exist
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpPageOrderTest extends PHPUnit\Framework\TestCase
{
    private static function index( array $order )
    {
        $allowlist = new expHelpAllowlist( array( 'root' => sys_get_temp_dir(), 'roots' => array( 'doc' ) ) );
        return new expHelpDocumentIndex( $allowlist, array( 'cache_dir' => false, 'git_history' => false,
                                                            'audience' => new expHelpAudience( array( 'pageOrder' => $order ) ) ) );
    }

    private static function root()
    {
        return __DIR__ . '/../../../../..';
    }

    /** HO-01 */
    public function testSettingWins()
    {
        $index = self::index( array( 'branch-overview.md' => 30, 'release-overview' => 40 ) );
        $page = $index->describe( 'doc/eng-us/branch-overview.md', "---\ntitle: Branch history\norder: 90\n---\n# Branch history\n", array( 'format' => 'markdown' ) );
        $this->assertSame( 'branch-overview.md', $page['id'] );
        $this->assertSame( 30, $page['order'] );
        $page = $index->describe( 'doc/eng-us/release-overview', "Release history\n\n- 6.0.0 one\n", array( 'format' => 'text' ) );
        $this->assertSame( 40, $page['order'] );
    }

    /** HO-02 */
    public function testWithoutEntry()
    {
        $index = self::index( array( 'other.md' => 5 ) );
        $page = $index->describe( 'doc/eng-us/x.md', "---\norder: 7\n---\n# X\n", array( 'format' => 'markdown' ) );
        $this->assertSame( 7, $page['order'] );
        $this->assertSame( 1000, $index->describe( 'doc/eng-us/y.md', "# Y\n", array( 'format' => 'markdown' ) )['order'] );
        $this->assertSame( 0, $index->describe( 'doc/eng-us/guides/README.md', "# Guides\n", array( 'format' => 'markdown' ) )['order'] );

        $a = new expHelpAudience( array( 'pageOrder' => array( 'b.md' => 10 ) ) );
        $this->assertSame( 10, $a->pageOrder( 'b.md', 1000 ) );
        $this->assertSame( 1000, $a->pageOrder( 'a.md', 1000 ) );
        $pages = array( array( 'title' => 'A', 'order' => $a->pageOrder( 'a.md', 1000 ) ),
                        array( 'title' => 'B', 'order' => $a->pageOrder( 'b.md', 1000 ) ) );
        usort( $pages, array( 'expHelpDocumentIndex', 'compare' ) );
        $this->assertSame( 'B', $pages[0]['title'] );
    }

    /** HO-03 */
    public function testOrderMap()
    {
        $this->assertSame( array( 'a.md' => 10, 'project/README.md' => 0, 'c.md' => -5 ),
                           expHelpAudience::orderMap( array( 'a.md' => '10', 'project/README.md' => '0', 'c.md' => ' -5 ',
                                                             'd.md' => 'first', 'e.md' => '1.5', '' => '3', '/f.md' => '1',
                                                             'g/' => '1', '../h.md' => '1' ) ) );
    }

    /** HO-04 */
    public function testShippedSettings()
    {
        $text = (string)file_get_contents( self::root() . '/settings/exphelp.ini' );
        $this->assertSame( 1, preg_match( '/^\[TreeSettings\]\n(.*?)(?=^\[)/ms', $text, $section ) );
        preg_match_all( '/^PageOrder\[([^\]]+)\]=(.*)$/m', $section[1], $m, PREG_SET_ORDER );
        $shipped = array();
        foreach ( $m as $entry )
            $shipped[$entry[1]] = $entry[2];
        $this->assertSame( expHelpAudience::PAGE_ORDER, expHelpAudience::orderMap( $shipped ) );
        $this->assertSame( expHelpAudience::PAGE_ORDER, expHelpAudience::defaults()['pageOrder'] );
        foreach ( array_keys( expHelpAudience::PAGE_ORDER ) as $id )
        {
            $file = strpos( $id, 'project/' ) === 0 ? substr( $id, strlen( 'project/' ) ) : 'doc/eng-us/' . $id;
            $this->assertFileExists( self::root() . '/' . $file, 'an order for a page that does not exist' );
        }
        // the top level has one source: a front matter order there agrees with the settings
        foreach ( array( 'branch-overview.md', 'README-LICENSE.md', 'glossary.md' ) as $id )
            if ( preg_match( '/^order:\s*(-?\d+)\s*$/m', (string)file_get_contents( self::root() . '/doc/eng-us/' . $id ), $o ) )
                $this->assertSame( expHelpAudience::PAGE_ORDER[$id], (int)$o[1], $id );
    }
}
