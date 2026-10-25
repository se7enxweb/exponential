<?php
/**
 * Bookmarks of system pages (expBookmarkView) on a live installation: the bookmark rows and addresses they write, and
 * that node bookmarks keep working beside them. Live style, like BookmarkFoldersTest: rows of the user 14 named BMVTEST
 * that are removed again; no test database. Where there is no installation (CI), or the table expbookmark_view is not
 * there yet (the update was not run), the tests are skipped.
 *
 *  BV-L1 createNew(): a bookmark row with node_id 0 and the address in expbookmark_view, without the siteaccess; the
 *        same address again updates the name and the folder instead of adding a second bookmark
 *  BV-L2 the flat list of the browse page (fetchListForUser()) has node bookmarks only, unless asked for both; the
 *        tree and the rows have both, the rows with kind, uri and can_open (decorateRows())
 *  BV-L3 rename(): only the user's own system page bookmarks, never a node bookmark, never to an empty name
 *  BV-L4 remove(): the address goes with the bookmark; removing a folder with its bookmarks removes their addresses;
 *        removeByNodeID( 0 ) removes nothing
 *  BV-L5 folders and order work for both kinds: move, shift, place
 *  BV-L6 moduleURI() accepts module views only and drops a leading siteaccess; pageState() of a module view
 *  BV-L7 handleAction(): add refuses an address that is not a module view and one the user may not open; rename
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group bookmarks
 */

class BookmarkViewsTest extends PHPUnit\Framework\TestCase
{
    private static $installation;
    private $user = 14;
    private $nodeBase = 99910000;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        chdir( self::$installation );
        ezpLiveInstallation::requireOrSkip();
        if ( !expBookmarkView::exists( null, true ) )
            self::markTestSkipped( 'the table expbookmark_view is missing: run update/common/scripts/6.0/createbookmarkviewtable.php' );
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        $this->wipe();
    }

    protected function tearDown(): void
    {
        $this->wipe();
    }

    private function wipe()
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT id FROM ezcontentbrowsebookmark WHERE name LIKE 'BMVTEST%'" );
        $ids = array_map( 'intval', array_column( is_array( $rows ) ? $rows : array(), 'id' ) );
        if ( $ids )
            $db->query( 'DELETE FROM expbookmark_view WHERE bookmark_id IN (' . implode( ',', $ids ) . ')' );
        $db->query( "DELETE FROM ezcontentbrowsebookmark WHERE name LIKE 'BMVTEST%'" );
        $db->query( "DELETE FROM ezcontentbrowsebookmark WHERE node_id >= {$this->nodeBase} AND node_id < {$this->nodeBase} + 1000" );
        $db->query( "DELETE FROM expbookmark_folder WHERE name LIKE 'BMVTEST%'" );
        expBookmarkView::reset();
    }

    private function view( $uri, $name, $folder = false, $user = null )
    {
        $b = expBookmarkView::createNew( $user ?: $this->user, $uri, 'BMVTEST ' . $name, $folder );
        $this->assertInstanceOf( 'eZContentBrowseBookmark', $b, $uri );
        return (int)$b->attribute( 'id' );
    }

    private function folder( $name )
    {
        return (int)eZContentBrowseBookmarkFolder::createNew( $this->user, 'BMVTEST ' . $name )->attribute( 'id' );
    }

    private function uriRow( $id )
    {
        $rows = eZDB::instance()->arrayQuery( 'SELECT user_id, module_uri FROM expbookmark_view WHERE bookmark_id=' . (int)$id );
        return $rows ? $rows[0] : null;
    }

    /** BV-L1 */
    public function testCreateAndUpdate()
    {
        $f = $this->folder( 'F' );
        $id = $this->view( '/setup/info/', 'Info', $f );
        $b = eZContentBrowseBookmark::fetch( $id );
        $this->assertSame( 0, (int)$b->attribute( 'node_id' ) );
        $this->assertSame( $f, (int)$b->attribute( 'folder_id' ) );
        $this->assertSame( 'view', $b->attribute( 'kind' ) );
        $this->assertSame( 'setup/info', $b->attribute( 'module_uri' ) );
        $this->assertNull( $b->attribute( 'node' ) );
        $this->assertSame( array( 'user_id' => (string)$this->user, 'module_uri' => 'setup/info' ), array_map( 'strval', $this->uriRow( $id ) ) );

        $again = $this->view( 'setup/info', 'Info renamed', 0 );
        $this->assertSame( $id, $again, 'one bookmark per address' );
        $b = eZContentBrowseBookmark::fetch( $id );
        $this->assertSame( 'BMVTEST Info renamed', $b->attribute( 'name' ) );
        $this->assertSame( 0, (int)$b->attribute( 'folder_id' ), 'moved to the top level' );
        $kept = $this->view( 'setup/info', 'Info kept', false );
        $this->assertSame( $id, $kept );
        $this->assertSame( 0, (int)eZContentBrowseBookmark::fetch( $id )->attribute( 'folder_id' ), 'no folder given: stays where it is' );
        $this->assertSame( $id, (int)expBookmarkView::findForUser( $this->user, 'setup/info' )->attribute( 'id' ) );
        $this->assertNull( expBookmarkView::findForUser( 10, 'setup/info' ), 'not another user\'s' );
        $this->assertFalse( expBookmarkView::createNew( $this->user, 'https://example.org/x', 'BMVTEST bad' ) );
    }

    /** BV-L2 */
    public function testListsAndRows()
    {
        $v = $this->view( 'setup/cache', 'Cache' );
        $n = (int)eZContentBrowseBookmark::createNew( $this->user, $this->nodeBase + 1, 'BMVTEST node' )->attribute( 'id' );
        $flat = array_map( function ( $b ) { return (int)$b->attribute( 'id' ); }, eZContentBrowseBookmark::fetchListForUser( $this->user ) );
        $this->assertContains( $n, $flat );
        $this->assertNotContains( $v, $flat, 'the browse page lists nodes only' );
        $all = array_map( function ( $b ) { return (int)$b->attribute( 'id' ); }, eZContentBrowseBookmark::fetchListForUser( $this->user, false, false, false ) );
        $this->assertContains( $v, $all );
        $this->assertContains( $n, $all );

        $rows = expBookmarkView::decorateRows( eZContentBrowseBookmarkFolder::fetchRowsForUser( $this->user ), $this->user, false );
        $byId = array();
        foreach ( $rows as $row )
            if ( $row['type'] === 'bookmark' )
                $byId[(int)$row['id']] = $row;
        $this->assertSame( 'view', $byId[$v]['kind'] );
        $this->assertSame( 'setup/cache', $byId[$v]['uri'] );
        $this->assertSame( 'setup', $byId[$v]['module'] );
        $this->assertSame( 'cache', $byId[$v]['view'] );
        $this->assertTrue( $byId[$v]['can_open'] );
        $this->assertSame( 'node', $byId[$n]['kind'] );
        $this->assertArrayNotHasKey( 'uri', $byId[$n] );
    }

    /** BV-L3 */
    public function testRename()
    {
        $v = $this->view( 'setup/cache', 'Cache' );
        $n = (int)eZContentBrowseBookmark::createNew( $this->user, $this->nodeBase + 2, 'BMVTEST node' )->attribute( 'id' );
        $this->assertTrue( expBookmarkView::rename( $this->user, $v, "BMVTEST <b>Caches</b>\n" ) );
        $this->assertSame( 'BMVTEST Caches', eZContentBrowseBookmark::fetch( $v )->attribute( 'name' ) );
        $this->assertFalse( expBookmarkView::rename( $this->user, $v, '  ' ), 'never an empty name' );
        $this->assertFalse( expBookmarkView::rename( 10, $v, 'BMVTEST other' ), 'not another user\'s' );
        $this->assertFalse( expBookmarkView::rename( $this->user, $n, 'BMVTEST node renamed' ), 'a node bookmark shows its node\'s name' );
    }

    /** BV-L4 */
    public function testRemoveTakesTheAddress()
    {
        $v = $this->view( 'setup/cache', 'Cache' );
        eZContentBrowseBookmark::fetch( $v )->remove();
        $this->assertNull( $this->uriRow( $v ) );

        $f = $this->folder( 'Doomed' );
        $a = $this->view( 'setup/info', 'In folder', $f );
        eZContentBrowseBookmark::removeByNodeID( 0 );
        $this->assertNotNull( eZContentBrowseBookmark::fetch( $a ), 'removeByNodeID( 0 ) removes no system page bookmark' );
        eZContentBrowseBookmarkFolder::fetch( $f )->removeFolder( true );
        $this->assertNull( eZContentBrowseBookmark::fetch( $a ) );
        $this->assertNull( $this->uriRow( $a ), 'the folder took the address with it' );
    }

    /** BV-L5 */
    public function testFoldersAndOrderForBothKinds()
    {
        $f = $this->folder( 'Mixed' );
        $n = (int)eZContentBrowseBookmark::createNew( $this->user, $this->nodeBase + 3, 'BMVTEST node', $f )->attribute( 'id' );
        $v = $this->view( 'setup/info', 'Info', $f );
        $w = $this->view( 'setup/cache', 'Cache', $f );
        $this->assertSame( array( $n, $v, $w ), eZContentBrowseBookmarkFolder::siblingIDs( $this->user, 'bookmark', $f ) );
        $this->assertTrue( eZContentBrowseBookmarkFolder::shift( $this->user, 'bookmark', $w, -1 ) );
        $this->assertSame( array( $n, $w, $v ), eZContentBrowseBookmarkFolder::siblingIDs( $this->user, 'bookmark', $f ) );
        $this->assertTrue( eZContentBrowseBookmarkFolder::place( $this->user, 'bookmark', $v, $f, $n ) );
        $this->assertSame( array( $v, $n, $w ), eZContentBrowseBookmarkFolder::siblingIDs( $this->user, 'bookmark', $f ) );
        $this->assertTrue( eZContentBrowseBookmarkFolder::moveBookmark( $this->user, $v, 0 ) );
        $this->assertSame( 0, (int)eZContentBrowseBookmark::fetch( $v )->attribute( 'folder_id' ) );
        $this->assertSame( 'setup/info', eZContentBrowseBookmark::fetch( $v )->attribute( 'module_uri' ), 'the address stays' );
    }

    /** BV-L6 */
    public function testModuleURIAndPageState()
    {
        $this->assertSame( 'setup/info', expBookmarkView::moduleURI( 'setup/info' ) );
        $this->assertSame( 'setup/info', expBookmarkView::moduleURI( 'admin/setup/info', array( 'admin', 'site' ) ), 'a leading siteaccess is dropped' );
        $this->assertSame( 'content/bookmark/(folder)/top', expBookmarkView::moduleURI( '/content/bookmark/(folder)/top' ) );
        $this->assertFalse( expBookmarkView::moduleURI( 'no_such_module_bmvtest/view' ) );
        $this->assertFalse( expBookmarkView::moduleURI( 'Media/Images' ), 'a URL alias is not a module view' );
        $this->assertFalse( expBookmarkView::moduleURI( 'https://example.org/setup/info' ) );
    }

    /** BV-L7 */
    public function testHandleAction()
    {
        $http = eZHTTPTool::instance();
        $admin = eZUser::fetch( $this->user );
        $previous = eZUser::currentUser();
        eZUser::setCurrentlyLoggedInUser( $admin, $this->user, eZUser::NO_SESSION_REGENERATE );
        try
        {
            $_POST = array( 'BookmarkViewAction' => 'add', 'BookmarkViewURI' => 'no_such_module_bmvtest/x', 'BookmarkViewName' => 'BMVTEST x' );
            $this->assertSame( 'error', expBookmarkView::handleAction( $this->user, $http )['level'] );
            $_POST = array( 'BookmarkViewAction' => 'add', 'BookmarkViewURI' => 'setup/bmvtest_no_such_view', 'BookmarkViewName' => 'BMVTEST y' );
            $this->assertSame( 'error', expBookmarkView::handleAction( $this->user, $http )['level'], 'a view that cannot be opened' );
            $_POST = array( 'BookmarkViewAction' => 'add', 'BookmarkViewURI' => '/setup/info', 'BookmarkViewName' => 'BMVTEST Info', 'BookmarkFolderID' => '0' );
            $result = expBookmarkView::handleAction( $this->user, $http );
            $this->assertSame( 'feedback', $result['level'], $result['text'] );
            $this->assertGreaterThan( 0, $result['bookmark_id'] );
            $_POST = array( 'BookmarkViewAction' => 'rename', 'BookmarkID' => (string)$result['bookmark_id'], 'BookmarkName' => 'BMVTEST Info 2' );
            $this->assertSame( 'feedback', expBookmarkView::handleAction( $this->user, $http )['level'] );
            $this->assertSame( 'BMVTEST Info 2', eZContentBrowseBookmark::fetch( $result['bookmark_id'] )->attribute( 'name' ) );
            $_POST = array( 'BookmarkViewAction' => 'other' );
            $this->assertSame( 'error', expBookmarkView::handleAction( $this->user, $http )['level'] );
        }
        finally
        {
            $_POST = array();
            eZUser::setCurrentlyLoggedInUser( $previous, $previous->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        }
    }
}
