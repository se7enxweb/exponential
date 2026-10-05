<?php
/**
 * expbookmark: the bookmark tree services. Live style, like the other users services: the rows of the test are
 * named BMTEST and sit on node ids that do not exist, and are removed again.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group bookmarks
 */

require_once __DIR__ . '/expUsersTestCase.php';

class expBookmarkServicesTest extends expUsersTestCase
{
    private $nodeBase = 99900000;

    private function wipe()
    {
        $db = eZDB::instance();
        $db->query( "DELETE FROM ezcontentbrowsebookmark WHERE node_id >= {$this->nodeBase} AND node_id < {$this->nodeBase} + 1000" );
        $db->query( "DELETE FROM expbookmark_folder WHERE name LIKE 'BMTEST%'" );
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->wipe();
    }

    public function tearDown(): void
    {
        $this->wipe();
        parent::tearDown();
    }

    private function bookmarkRow( $n, $folder = 0 )
    {
        $user = (int)eZUser::currentUserID();
        return (int)eZContentBrowseBookmark::createNew( $user, $this->nodeBase + $n, 'BMTEST bookmark ' . $n, $folder )->attribute( 'id' );
    }

    public function testAnonymousIsRefused()
    {
        $this->loginAnonymous();
        $this->assertError( $this->call( 'expBookmarkServices', 'tree' ), 401 );
        $this->assertError( $this->write( 'expBookmarkServices', 'createFolder', array( 'name' => 'BMTEST x' ) ), 401 );
    }

    public function testFolderLifecycle()
    {
        $this->loginAdmin();
        $a = $this->okWrite( 'expBookmarkServices', 'createFolder', array( 'name' => 'BMTEST A' ) );
        $b = $this->okWrite( 'expBookmarkServices', 'createFolder', array( 'name' => 'BMTEST B', 'parent_id' => $a['id'] ) );
        $this->assertSame( $a['id'], $b['parent_id'] );
        $this->assertSame( 'BMTEST A2', $this->okWrite( 'expBookmarkServices', 'renameFolder', array( 'id' => $a['id'], 'name' => 'BMTEST A2' ) )['name'] );
        $this->assertError( $this->write( 'expBookmarkServices', 'createFolder', array( 'name' => '  ' ) ), 422 );
        $this->assertError( $this->write( 'expBookmarkServices', 'createFolder', array( 'name' => '<i></i>' ) ), 422 );
        $this->assertError( $this->write( 'expBookmarkServices', 'createFolder', array( 'name' => 'BMTEST z', 'parent_id' => 99999999 ) ), 404 );
        $this->assertError( $this->write( 'expBookmarkServices', 'moveFolder', array( 'id' => $a['id'], 'parent_id' => $b['id'] ) ), 409 );
        $moved = $this->okWrite( 'expBookmarkServices', 'moveFolder', array( 'id' => $b['id'], 'parent_id' => 0 ) );
        $this->assertSame( 0, $moved['parent_id'] );
        $deleted = $this->okWrite( 'expBookmarkServices', 'deleteFolder', array( 'id' => $a['id'] ) );
        $this->assertTrue( $deleted['removed'] );
        $this->assertError( $this->write( 'expBookmarkServices', 'renameFolder', array( 'id' => $a['id'], 'name' => 'BMTEST gone' ) ), 404 );
    }

    public function testBookmarksInTheTreeAndTheFlatListStaysComplete()
    {
        $this->loginAdmin();
        $f = $this->okWrite( 'expBookmarkServices', 'createFolder', array( 'name' => 'BMTEST F' ) );
        $g = $this->okWrite( 'expBookmarkServices', 'createFolder', array( 'name' => 'BMTEST G', 'parent_id' => $f['id'] ) );
        $x = $this->bookmarkRow( 1 );
        $y = $this->bookmarkRow( 2 );
        $this->okWrite( 'expBookmarkServices', 'moveBookmark', array( 'id' => $y, 'folder_id' => $g['id'] ) );

        $tree = $this->okCall( 'expBookmarkServices', 'tree' );
        $found = null;
        foreach ( $tree['folders'] as $folder )
            if ( $folder['id'] === $f['id'] )
                $found = $folder;
        $this->assertNotNull( $found );
        $this->assertSame( 1, $found['count'] );
        $this->assertSame( 'BMTEST G', $found['folders'][0]['name'] );
        $this->assertSame( array( 'BMTEST F', 'BMTEST G' ), $found['folders'][0]['bookmarks'][0]['path'] );
        $this->assertContains( $x, array_column( $tree['bookmarks'], 'id' ), 'the top level bookmark' );

        $all = $this->call( 'expBookmarkServices', 'bookmarks', array( '200', '0' ) );
        $this->assertPaged( $all );
        $ids = array_column( $all['data'], 'id' );
        $this->assertContains( $x, $ids );
        $this->assertContains( $y, $ids, 'a bookmark in a folder is in the flat list as well' );
        $in = $this->call( 'expBookmarkServices', 'bookmarks', array( '50', '0', (string)$g['id'] ) );
        $this->assertSame( array( $y ), array_column( $in['data'], 'id' ) );

        $rows = $this->okCall( 'expBookmarkServices', 'rows' );
        $types = array_column( array_filter( $rows, function ( $r ) { return strpos( $r['name'], 'BMTEST' ) === 0; } ), 'type' );
        $this->assertSame( array( 'folder', 'folder', 'bookmark', 'bookmark' ), array_values( $types ) );

        $this->okWrite( 'expBookmarkServices', 'place', array( 'type' => 'bookmark', 'id' => $x, 'parent_id' => $g['id'], 'before_id' => $y ) );
        $in = $this->call( 'expBookmarkServices', 'bookmarks', array( '50', '0', (string)$g['id'] ) );
        $this->assertSame( array( $x, $y ), array_column( $in['data'], 'id' ) );
        $this->assertError( $this->write( 'expBookmarkServices', 'place', array( 'type' => 'file', 'id' => $x ) ), 400 );
        $this->okWrite( 'expBookmarkServices', 'removeBookmark', array( 'id' => $x ) );
        $this->assertError( $this->write( 'expBookmarkServices', 'removeBookmark', array( 'id' => $x ) ), 404 );
    }

    public function testOtherUsersDataDoesNotExist()
    {
        $this->loginAdmin();
        $f = $this->okWrite( 'expBookmarkServices', 'createFolder', array( 'name' => 'BMTEST mine' ) );
        $b = $this->bookmarkRow( 5 );
        $this->loginAs( $this->memberUser() );
        $this->assertError( $this->write( 'expBookmarkServices', 'renameFolder', array( 'id' => $f['id'], 'name' => 'BMTEST stolen' ) ), 404 );
        $this->assertError( $this->write( 'expBookmarkServices', 'moveBookmark', array( 'id' => $b, 'folder_id' => 0 ) ), 404 );
        $this->assertError( $this->write( 'expBookmarkServices', 'removeBookmark', array( 'id' => $b ) ), 404 );
        $this->assertError( $this->write( 'expBookmarkServices', 'createFolder', array( 'name' => 'BMTEST x', 'parent_id' => $f['id'] ) ), 404 );
        $names = array_column( $this->okCall( 'expBookmarkServices', 'tree' )['folders'], 'name' );
        $this->assertNotContains( 'BMTEST mine', $names );
    }

    public function testDeleteFolderWithBookmarks()
    {
        $this->loginAdmin();
        $f = $this->okWrite( 'expBookmarkServices', 'createFolder', array( 'name' => 'BMTEST D' ) );
        $b = $this->bookmarkRow( 7, $f['id'] );
        $this->okWrite( 'expBookmarkServices', 'deleteFolder', array( 'id' => $f['id'] ) );
        $this->assertNotNull( eZContentBrowseBookmark::fetch( $b ), 'the bookmark stays' );
        $this->assertSame( 0, (int)eZContentBrowseBookmark::fetch( $b )->attribute( 'folder_id' ) );
        $f = $this->okWrite( 'expBookmarkServices', 'createFolder', array( 'name' => 'BMTEST D2' ) );
        $b2 = $this->bookmarkRow( 8, $f['id'] );
        $r = $this->okWrite( 'expBookmarkServices', 'deleteFolder', array( 'id' => $f['id'], 'delete_bookmarks' => '1' ) );
        $this->assertTrue( $r['bookmarks_deleted'] );
        $this->assertNull( eZContentBrowseBookmark::fetch( $b2 ) );
    }
}
