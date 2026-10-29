<?php
/**
 * Bookmarks of system pages, the rules that need no database: the address that is stored (expBookmarkView), the
 * name a new bookmark gets, and how the bookmark page shows such a bookmark next to node bookmarks (expBookmarkPage).
 *
 *  BV-01 normalizeURI(): one form per address; another site, "..", backslashes, control characters, empty and
 *        overlong addresses are refused; a query string and a fragment are dropped
 *  BV-02 withRequestParameters(): the view parameters of the request are put back on the request's own address only
 *  BV-03 defaultName(): the window title (the path, the last step first), tags and control characters removed; the
 *        address when the page has no path
 *  BV-04 parts(): the module and the view of an address
 *  BV-05 expBookmarkPage::items(): a system page row (node_id 0) is an item of kind view with its address; it is ok,
 *        denied (cannot be opened) or gone (no address); node rows are unchanged and of kind node
 *  BV-06 search finds a system page by its address; the type order sorts it by its label; the overview counts them
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group bookmarks
 */

class expBookmarkViewTest extends PHPUnit\Framework\TestCase
{
    /** BV-01 */
    public function testNormalizeURI()
    {
        $this->assertSame( 'setup/info', expBookmarkView::normalizeURI( 'setup/info' ) );
        $this->assertSame( 'setup/info', expBookmarkView::normalizeURI( '/setup//info/' ) );
        $this->assertSame( 'setup/info', expBookmarkView::normalizeURI( '  /setup/info?x=1#top ' ) );
        $this->assertSame( 'content/bookmark/(folder)/top', expBookmarkView::normalizeURI( 'content/bookmark/(folder)/top' ) );
        $this->assertSame( 'audit/console/(user)/14/(offset)/20', expBookmarkView::normalizeURI( '/audit/console/(user)/14/(offset)/20/' ) );
        foreach ( array( '', '/', 'https://example.org/setup/info', 'javascript:alert(1)', '//example.org/x', 'setup/../user',
                         'setup/./info', "setup\\info", "setup/info\n", "setup/\x00info", str_repeat( 'a/', 1001 ) . 'b' ) as $bad )
            $this->assertFalse( expBookmarkView::normalizeURI( $bad ), var_export( $bad, true ) );
        $this->assertFalse( expBookmarkView::normalizeURI( null ) );
        $this->assertFalse( expBookmarkView::normalizeURI( array( 'setup/info' ) ) );
    }

    /** BV-02 */
    public function testWithRequestParameters()
    {
        $request = array( 'uri' => 'content/bookmark', 'parameters' => array( 'folder' => 'top', 'sort' => 'name' ) );
        $this->assertSame( 'content/bookmark/(folder)/top/(sort)/name', expBookmarkView::withRequestParameters( '/content/bookmark', $request ) );
        $this->assertSame( 'setup/info', expBookmarkView::withRequestParameters( 'setup/info', $request ), 'another address is left as it is' );
        $this->assertSame( 'content/bookmark', expBookmarkView::withRequestParameters( 'content/bookmark', array( 'uri' => 'content/bookmark', 'parameters' => array() ) ) );
        $this->assertSame( 'audit/console/(user)/14', expBookmarkView::withRequestParameters( 'audit/console',
            array( 'uri' => 'audit/console', 'parameters' => array( 'user' => '14', 'bad name' => 'x', 'x)/(y' => 'z' ) ) ), 'only parameter names of letters, digits and _' );
    }

    /** BV-03 */
    public function testDefaultName()
    {
        $this->assertSame( 'Upgrade check / Setup', expBookmarkView::defaultName( array( array( 'text' => 'Setup', 'url' => 'setup/menu' ),
                                                                                          array( 'text' => 'Upgrade check', 'url' => false ) ), 'setup/systemupgrade' ) );
        $this->assertSame( 'System information', expBookmarkView::defaultName( array( array( 'text' => ' System information ' ) ), 'setup/info' ) );
        $this->assertSame( 'Help', expBookmarkView::defaultName( array( array( 'text' => '' ), array( 'text' => '<b>Help</b>' ) ), 'exphelp/dashboard' ) );
        $this->assertSame( '/setup/info', expBookmarkView::defaultName( array(), 'setup/info' ) );
        $this->assertSame( 255, mb_strlen( expBookmarkView::defaultName( array( array( 'text' => str_repeat( 'x', 400 ) ) ), 'a/b' ) ) );
    }

    /** BV-04 */
    public function testParts()
    {
        $this->assertSame( array( 'module' => 'setup', 'view' => 'info' ), expBookmarkView::parts( 'setup/info' ) );
        $this->assertSame( array( 'module' => 'audit', 'view' => 'console' ), expBookmarkView::parts( '/audit/console/(user)/14' ) );
        $this->assertSame( array( 'module' => 'search', 'view' => '' ), expBookmarkView::parts( 'search' ) );
    }

    private function rows()
    {
        $node = array( 'node_id' => 43 );
        return array(
            array( 'type' => 'folder', 'id' => 1, 'name' => 'Admin', 'parent_id' => 0, 'depth' => 0, 'path' => array(), 'count' => 2 ),
            array( 'type' => 'bookmark', 'id' => 10, 'name' => 'Upgrade check', 'folder_id' => 1, 'bookmark' => array( 'node_id' => 0 ),
                   'kind' => 'view', 'uri' => 'setup/systemupgrade', 'can_open' => true ),
            array( 'type' => 'bookmark', 'id' => 11, 'name' => 'Audit of Anna', 'folder_id' => 1, 'bookmark' => array( 'node_id' => 0 ),
                   'kind' => 'view', 'uri' => 'audit/console/(user)/14', 'can_open' => false ),
            array( 'type' => 'bookmark', 'id' => 12, 'name' => 'Media', 'folder_id' => 0, 'bookmark' => $node, 'kind' => 'node' ),
            array( 'type' => 'bookmark', 'id' => 13, 'name' => '', 'folder_id' => 0, 'bookmark' => array( 'node_id' => 0 ) ),
        );
    }

    private function nodes()
    {
        return array( 43 => array( 'name' => 'Media', 'class_identifier' => 'folder', 'class_name' => 'Folder', 'is_hidden' => false,
                                   'is_invisible' => false, 'modified' => 100, 'path' => array(), 'is_container' => true,
                                   'contentobject_id' => 41, 'can_read' => true ) );
    }

    /** BV-05 */
    public function testItems()
    {
        $items = expBookmarkPage::items( $this->rows(), $this->nodes(), 'System page' );
        $this->assertCount( 4, $items );
        $byId = array_column( $items, null, 'id' );
        $this->assertSame( 'view', $byId[10]['kind'] );
        $this->assertSame( 'setup/systemupgrade', $byId[10]['uri'] );
        $this->assertSame( 'setup', $byId[10]['module'] );
        $this->assertSame( 'systemupgrade', $byId[10]['view'] );
        $this->assertSame( 'ok', $byId[10]['state'] );
        $this->assertSame( 'Upgrade check', $byId[10]['name'] );
        $this->assertSame( 'System page', $byId[10]['class_name'] );
        $this->assertSame( 0, $byId[10]['node_id'] );
        $this->assertSame( 0, $byId[10]['contentobject_id'], 'nothing to edit' );
        $this->assertSame( 'denied', $byId[11]['state'], 'a page the user may no longer open' );
        $this->assertSame( 'Audit of Anna', $byId[11]['name'], 'its own name is still shown' );
        $this->assertSame( 'node', $byId[12]['kind'] );
        $this->assertSame( 'Media', $byId[12]['name'] );
        $this->assertSame( 'Folder', $byId[12]['class_name'] );
        $this->assertSame( 'gone', $byId[13]['state'], 'a system page bookmark without an address' );
        $this->assertSame( '/', $byId[13]['name'] );
    }

    /** BV-06 */
    public function testSearchOrderAndSummary()
    {
        $folders = expBookmarkPage::folders( $this->rows() );
        $items = expBookmarkPage::items( $this->rows(), $this->nodes(), 'System page' );
        $found = expBookmarkPage::select( $items, $folders, expBookmarkPage::SCOPE_ALL, 'systemupgrade', 'own' );
        $this->assertSame( array( 10 ), array_column( $found, 'id' ), 'found by its address' );
        $found = expBookmarkPage::select( $items, $folders, expBookmarkPage::SCOPE_ALL, 'system page', 'own' );
        $this->assertSame( array( 13, 10, 11 ), array_column( $found, 'id' ), 'found by its type' );
        $typed = expBookmarkPage::select( $items, $folders, expBookmarkPage::SCOPE_ALL, '', 'type' );
        $this->assertSame( array( 12, 13, 11, 10 ), array_column( $typed, 'id' ), 'Folder before System page, by name within a type, grouped by folder' );
        $summary = expBookmarkPage::summary( $items, $folders );
        $this->assertSame( 3, $summary['views'] );
        $this->assertSame( 2, $summary['gone'], 'denied and gone count as no longer available' );
        $this->assertSame( 4, $summary['bookmarks'] );
    }
}
