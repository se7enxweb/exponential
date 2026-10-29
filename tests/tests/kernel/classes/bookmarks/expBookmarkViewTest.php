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
 *  BV-07 queryOf(), pathOf(), canonicalQuery(): only the kept keys, one scalar value each, sorted and encoded
 *  BV-08 bookmarkable(): a listed view (module/view, module/*) is refused, every other one is taken
 *  BV-09 the kinds of bookmark (bookmark.ini): search before the system page kind, their icons and labels; a module
 *        icon from ModuleIcons, the default for a module that is not listed; parts() ignores the query string
 *  BV-10 reasonText(): a sentence for every reason a bookmark is greyed, and one for an unknown reason
 *  BV-11 expBookmarkPage::viewItem() carries the reason, the icon and the label of its kind
 *  BV-12 every icon bookmark.ini names has its class in both copies of components.css
 *  BV-13 an extension adds a kind of bookmark through bookmark.ini; a class that is not a kind is skipped
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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

    /** BV-07 */
    public function testQueryString()
    {
        $this->assertSame( 'SearchText=a%20b&x=1', expBookmarkView::queryOf( 'content/search?SearchText=a%20b&x=1#top' ) );
        $this->assertSame( '', expBookmarkView::queryOf( 'setup/info' ) );
        $this->assertSame( 'content/search', expBookmarkView::pathOf( 'content/search?SearchText=x' ) );
        $keys = array( 'SearchText', 'SectionID', 'SubTreeArray' );
        $this->assertSame( 'SearchText=Stra%C3%9Fe%20%26%20Weg&SectionID=3',
                           expBookmarkView::canonicalQuery( 'content', 'search', 'SectionID=3&SearchText=Stra%C3%9Fe+%26+Weg&Other=1', $keys ),
                           'sorted, encoded, other keys dropped' );
        $this->assertSame( 'SearchText=x', expBookmarkView::canonicalQuery( 'content', 'search', array( 'SearchText' => ' x ', 'SubTreeArray' => array( 2 ), 'SectionID' => '' ), $keys ),
                           'an array value and an empty value are dropped' );
        $this->assertSame( '', expBookmarkView::canonicalQuery( 'content', 'search', 'SearchText=' . str_repeat( 'y', 256 ), $keys ), 'too long' );
        $this->assertSame( '', expBookmarkView::canonicalQuery( 'content', 'search', "SearchText=a%0Ab", $keys ), 'a control character' );
        $this->assertSame( '', expBookmarkView::canonicalQuery( 'setup', 'info', 'SearchText=x', array() ), 'a view that keeps no keys' );
    }

    /** BV-08 */
    public function testBookmarkable()
    {
        $list = array( 'content/action', 'switchlanguage/*', ' /user/logout/ ' );
        $this->assertFalse( expBookmarkView::bookmarkable( 'content/action', $list )['result'] );
        $this->assertSame( 'not-bookmarkable', expBookmarkView::bookmarkable( 'content/action/12', $list )['reason'] );
        $this->assertFalse( expBookmarkView::bookmarkable( 'switchlanguage/to/ger/content', $list )['result'], 'module/*' );
        $this->assertFalse( expBookmarkView::bookmarkable( 'user/logout', $list )['result'], 'spaces and slashes of an entry are ignored' );
        // the setup module, not content: loading the content module definition reads the state groups from the database
        $this->assertTrue( expBookmarkView::bookmarkable( 'setup/action', $list )['result'], 'the same view name in another module' );
        $this->assertTrue( expBookmarkView::bookmarkable( 'setup/info', array() )['result'] );
    }

    /** BV-09 */
    public function testKindsAndIcons()
    {
        expBookmarkView::reset();
        $types = expBookmarkView::types();
        $this->assertGreaterThanOrEqual( 2, count( $types ) );
        $this->assertInstanceOf( 'expBookmarkTypeSystemPage', end( $types ), 'the system page kind comes last and takes every view' );
        $search = expBookmarkView::typeFor( 'content', 'search' );
        $this->assertInstanceOf( 'expBookmarkTypeSearch', $search );
        $this->assertSame( 'search', $search->icon( 'content/search?SearchText=x' ) );
        $this->assertSame( array( 'SearchText', 'SectionID', 'SubTreeArray' ), $search->queryParameters( 'content', 'search' ) );
        $page = expBookmarkView::typeFor( 'setup', 'info' );
        $this->assertSame( 'view', $page->identifier() );
        $this->assertSame( array(), $page->queryParameters( 'setup', 'info' ) );
        $this->assertSame( 'tools', $page->icon( 'setup/info' ) );
        $this->assertSame( 'shield-lock', expBookmarkView::moduleIcon( 'role' ) );
        $this->assertSame( 'gear', expBookmarkView::moduleIcon( 'bmvtest_unlisted_module' ) );
        $this->assertSame( array( 'module' => 'content', 'view' => 'search' ), expBookmarkView::parts( 'content/search?SearchText=a/b' ) );
    }

    /** BV-10 */
    public function testReasonText()
    {
        $texts = array();
        foreach ( array( 'siteaccess-rules', 'no-module', 'no-view', 'policy', 'login', 'node', 'not-bookmarkable', 'empty', 'bmvtest-other' ) as $reason )
        {
            $text = expBookmarkView::reasonText( $reason );
            $this->assertNotSame( '', $text, $reason );
            $texts[$reason] = $text;
        }
        $this->assertSame( $texts['node'], expBookmarkView::reasonText( 'object' ) );
        $this->assertCount( 9, array_unique( $texts ), 'each reason has its own sentence' );
    }

    /** BV-11 */
    public function testViewItemReasonIconAndKind()
    {
        $item = expBookmarkPage::viewItem( array( 'id' => 20, 'name' => 'Disk use', 'folder_id' => 0, 'uri' => 'setup/cache',
                                                  'can_open' => false, 'reason' => 'siteaccess-rules', 'reason_text' => 'Switched off here.',
                                                  'icon' => 'tools', 'type_label' => '' ), 1, 'System page' );
        $this->assertSame( 'denied', $item['state'] );
        $this->assertSame( 'siteaccess-rules', $item['reason'] );
        $this->assertSame( 'Switched off here.', $item['reason_text'] );
        $this->assertSame( 'tools', $item['icon'] );
        $this->assertSame( 'System page', $item['class_name'] );
        $item = expBookmarkPage::viewItem( array( 'id' => 21, 'name' => 'Drafts search', 'folder_id' => 0, 'uri' => 'content/search?SearchText=draft',
                                                  'can_open' => true, 'icon' => 'search', 'type_label' => 'Search' ), 2, 'System page' );
        $this->assertSame( 'ok', $item['state'] );
        $this->assertSame( 'search', $item['view'], 'the view without the query string' );
        $this->assertSame( 'Search', $item['class_name'], 'the label of its kind' );
        $this->assertSame( '', $item['reason_text'] );
    }

    /** BV-12 every icon bookmark.ini names (and the search kind's) has its class in both copies of components.css */
    public function testEveryBookmarkIconExists()
    {
        $root = dirname( __DIR__, 5 );
        $ini = eZINI::instance( 'bookmark.ini' );
        $icons = array_values( (array)$ini->variable( 'ViewBookmarkSettings', 'ModuleIcons' ) );
        $icons[] = $ini->variable( 'ViewBookmarkSettings', 'DefaultIcon' );
        $icons[] = 'search';
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $css = file_get_contents( "$root/design/$design/stylesheets/components.css" );
            foreach ( array_unique( $icons ) as $icon )
                $this->assertStringContainsString( ".a4-bi-$icon {", $css, "design/$design: no .a4-bi-$icon" );
        }
    }

    /** BV-13 an extension's kind (bookmark.ini [BookmarkTypeSettings]) is asked first; a class that is no kind is skipped */
    public function testAnExtensionAddsAKind()
    {
        $ini = eZINI::instance( 'bookmark.ini' );
        $list = $ini->variable( 'BookmarkTypeSettings', 'TypeList' );
        $classes = $ini->variable( 'BookmarkTypeSettings', 'TypeClass' );
        try
        {
            $ini->setVariable( 'BookmarkTypeSettings', 'TypeList', array( 'layout', 'broken', 'search' ) );
            $ini->setVariable( 'BookmarkTypeSettings', 'TypeClass', array( 'layout' => 'bmvtestLayoutBookmarkType', 'broken' => 'stdClass',
                                                                           'search' => 'expBookmarkTypeSearch' ) );
            expBookmarkView::reset();
            $this->assertCount( 3, expBookmarkView::types(), 'layout, search and the system page kind; stdClass is skipped' );
            $kind = expBookmarkView::typeFor( 'content', 'dashboard' );
            $this->assertSame( 'layout', $kind->identifier() );
            $this->assertSame( 'layout-sidebar-inset', $kind->icon( 'content/dashboard' ) );
            $this->assertSame( array(), $kind->queryParameters( 'content', 'dashboard' ), 'no query string by default' );
            $this->assertInstanceOf( 'expBookmarkTypeSearch', expBookmarkView::typeFor( 'content', 'search' ) );
            $this->assertInstanceOf( 'expBookmarkTypeSystemPage', expBookmarkView::typeFor( 'setup', 'info' ) );
        }
        finally
        {
            $ini->setVariable( 'BookmarkTypeSettings', 'TypeList', $list );
            $ini->setVariable( 'BookmarkTypeSettings', 'TypeClass', $classes );
            expBookmarkView::reset();
        }
    }
}

/** BV-13: a kind of bookmark as an extension would add it */
class bmvtestLayoutBookmarkType extends expBookmarkType
{
    public function identifier() { return 'layout'; }
    public function accepts( $module, $view ) { return $module === 'content' && $view === 'dashboard'; }
    public function icon( $uri ) { return 'layout-sidebar-inset'; }
    public function label() { return 'Layout'; }
}
