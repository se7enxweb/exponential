<?php
/**
 * What the bookmark page (content/bookmark) shows, without a database: expBookmarkPage.
 *
 *  BP-01 - Unknown orders fall back to the user's own order; a search is one trimmed line of at most 100 characters
 *  BP-02 - The (folder) parameter is "top", one of the user's folders, or every bookmark; another user's id is every bookmark
 *  BP-03 - Folders get their direct count, their subtree and the number of folders below them; a lost parent is the top level
 *  BP-04 - Items take the node's name, type, path and state; not found and not readable hide the node's details
 *  BP-05 - A folder shows its own bookmarks and those of the folders below it, grouped by folder, unfiled first
 *  BP-06 - The search matches name, type, location and folder, without regard to case
 *  BP-07 - The orders name, recently added, type and recently modified; ties keep the user's own order
 *  BP-08 - Paging marks the first item of each group, its size, and a group continued from the page before
 *  BP-09 - The overview counts bookmarks, folders, unfiled, hidden and no longer available ones
 *  BP-10 - Moving up and down finds the entry to stand in front of, or refuses at the ends
 *  BP-11 - The page address is built only from checked values
 *  BP-12 - Selected ids and shift buttons accept whole positive numbers only
 *  BP-13 - Move targets leave out a folder and everything below it
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group bookmarks
 */

require_once dirname( __DIR__, 5 ) . '/kernel/classes/expbookmarkpage.php';

class expBookmarkPageTest extends PHPUnit\Framework\TestCase
{
    /**
     * Rows as eZContentBrowseBookmarkFolder::fetchRowsForUser() returns them:
     *   Work (10)              2 bookmarks (1, 2)
     *     Press (11)           1 bookmark (3)
     *   Private (12)           none
     *   top level              2 bookmarks (4, 5)
     */
    private function rows()
    {
        $b = function ( $id, $node, $folder, $name ) {
            return array( 'type' => 'bookmark', 'id' => $id, 'name' => $name, 'folder_id' => $folder, 'depth' => 0, 'path' => array(),
                          'bookmark' => array( 'node_id' => $node ) );
        };
        return array(
            array( 'type' => 'folder', 'id' => 10, 'name' => 'Work', 'parent_id' => 0, 'depth' => 0, 'path' => array(), 'count' => 3 ),
            array( 'type' => 'folder', 'id' => 11, 'name' => 'Press', 'parent_id' => 10, 'depth' => 1, 'path' => array( 'Work' ), 'count' => 1 ),
            $b( 3, 103, 11, 'press release' ),
            $b( 1, 101, 10, 'Zebra' ),
            $b( 2, 102, 10, 'apple' ),
            array( 'type' => 'folder', 'id' => 12, 'name' => 'Private', 'parent_id' => 0, 'depth' => 0, 'path' => array(), 'count' => 0 ),
            $b( 4, 104, 0, 'Home page' ),
            $b( 5, 105, 0, 'Old stored name' ),
        );
    }

    private function nodes()
    {
        $n = function ( $name, $class, $modified, $path, $extra = array() ) {
            return array_merge( array( 'name' => $name, 'class_identifier' => strtolower( $class ), 'class_name' => $class,
                                       'is_hidden' => false, 'is_invisible' => false, 'modified' => $modified, 'path' => $path,
                                       'is_container' => false, 'contentobject_id' => 1, 'can_read' => true ), $extra );
        };
        return array(
            101 => $n( 'Zebra', 'Article', 300, array( 'Home', 'News' ) ),
            102 => $n( 'apple', 'Folder', 100, array( 'Home' ), array( 'is_hidden' => true ) ),
            103 => $n( 'Press release', 'Article', 200, array( 'Home', 'Media room' ), array( 'is_invisible' => true ) ),
            104 => $n( 'Home page', 'Frontpage', 50, array(), array( 'can_read' => false ) ),
            // 105 is not found
        );
    }

    private function items()
    {
        return expBookmarkPage::items( $this->rows(), $this->nodes() );
    }

    private function ids( array $list )
    {
        return array_map( function ( $i ) { return $i['id']; }, $list );
    }

    public function testSortAndSearchInput()
    {
        $this->assertSame( 'own', expBookmarkPage::sortKey( 'nonsense' ) );
        $this->assertSame( 'own', expBookmarkPage::sortKey( array( 'name' ) ) );
        $this->assertSame( 'type', expBookmarkPage::sortKey( 'type' ) );
        $this->assertSame( 'a b', expBookmarkPage::searchText( "  a \n\t b " ) );
        $this->assertSame( '', expBookmarkPage::searchText( array( 'x' ) ) );
        $this->assertSame( 100, mb_strlen( expBookmarkPage::searchText( str_repeat( 'ä', 300 ) ) ) );
    }

    public function testScope()
    {
        $folders = expBookmarkPage::folders( $this->rows() );
        $this->assertSame( 0, expBookmarkPage::scope( 'top', $folders ) );
        $this->assertSame( 11, expBookmarkPage::scope( '11', $folders ) );
        $this->assertSame( expBookmarkPage::SCOPE_ALL, expBookmarkPage::scope( '999', $folders ), 'not a folder of the user' );
        $this->assertSame( expBookmarkPage::SCOPE_ALL, expBookmarkPage::scope( '11abc', $folders ) );
        $this->assertSame( expBookmarkPage::SCOPE_ALL, expBookmarkPage::scope( '', $folders ) );
        $this->assertSame( expBookmarkPage::SCOPE_ALL, expBookmarkPage::scope( '-1', $folders ) );
    }

    public function testFolders()
    {
        $folders = expBookmarkPage::folders( $this->rows() );
        $this->assertSame( array( 10, 11, 12 ), array_keys( $folders ) );
        $this->assertSame( 2, $folders[10]['direct'] );
        $this->assertSame( 3, $folders[10]['count'] );
        $this->assertSame( 1, $folders[10]['subfolders'] );
        $this->assertSame( array( 10, 11 ), $folders[10]['subtree'] );
        $this->assertSame( array( 11 ), $folders[10]['children'] );
        $this->assertSame( 0, $folders[12]['subfolders'] );

        $rows = $this->rows();
        $rows[1]['parent_id'] = 77; // damaged: a parent that is not there
        $folders = expBookmarkPage::folders( $rows );
        $this->assertSame( 0, $folders[11]['parent_id'] );
        $this->assertSame( array( 10 ), $folders[10]['subtree'] );
    }

    public function testItems()
    {
        $items = $this->items();
        $this->assertSame( array( 3, 1, 2, 4, 5 ), $this->ids( $items ), 'the user\'s own order' );
        $byID = array_column( $items, null, 'id' );
        $this->assertSame( 'Press release', $byID[3]['name'], 'the node\'s current name' );
        $this->assertSame( 'invisible', $byID[3]['state'] );
        $this->assertSame( 'hidden', $byID[2]['state'] );
        $this->assertSame( 'ok', $byID[1]['state'] );
        $this->assertSame( array( 'Home', 'News' ), $byID[1]['path'] );
        $this->assertSame( 'gone', $byID[5]['state'] );
        $this->assertSame( 'Old stored name', $byID[5]['name'] );
        $this->assertSame( 'denied', $byID[4]['state'] );
        $this->assertSame( '', $byID[4]['class_name'], 'nothing of a node the user may not read' );
        $this->assertSame( array(), $byID[4]['path'] );
    }

    public function testSelectScopeAndGrouping()
    {
        $items = $this->items();
        $folders = expBookmarkPage::folders( $this->rows() );
        $this->assertSame( array( 4, 5, 1, 2, 3 ), $this->ids( expBookmarkPage::select( $items, $folders, expBookmarkPage::SCOPE_ALL, '', 'own' ) ) );
        $this->assertSame( array( 4, 5 ), $this->ids( expBookmarkPage::select( $items, $folders, 0, '', 'own' ) ) );
        $work = expBookmarkPage::select( $items, $folders, 10, '', 'own' );
        $this->assertSame( array( 1, 2, 3 ), $this->ids( $work ), 'a folder with the folders below it' );
        $this->assertSame( array( 10, 10, 11 ), array_column( $work, 'group' ) );
        $this->assertSame( array(), expBookmarkPage::select( $items, $folders, 12, '', 'own' ) );
    }

    public function testSearch()
    {
        $items = $this->items();
        $folders = expBookmarkPage::folders( $this->rows() );
        $find = function ( $q, $scope = expBookmarkPage::SCOPE_ALL ) use ( $items, $folders ) {
            return $this->ids( expBookmarkPage::select( $items, $folders, $scope, $q, 'own' ) );
        };
        $this->assertSame( array( 1 ), $find( 'ZEB' ), 'name, any case' );
        $this->assertSame( array( 1, 3 ), $find( 'article' ), 'type' );
        $this->assertSame( array( 3 ), $find( 'media room' ), 'location' );
        $this->assertSame( array( 3 ), $find( 'work / press' ), 'folder path' );
        $this->assertSame( array(), $find( 'zebra', 0 ), 'only in the scope' );
        $this->assertSame( array(), $find( 'frontpage' ), 'not the type of a node the user may not read' );
    }

    public function testOrders()
    {
        $items = $this->items();
        $folders = expBookmarkPage::folders( $this->rows() );
        $order = function ( $sort ) use ( $items, $folders ) { return $this->ids( expBookmarkPage::select( $items, $folders, 10, '', $sort ) ); };
        $this->assertSame( array( 2, 1, 3 ), $order( 'name' ), 'natural, case-insensitive, within the group' );
        $this->assertSame( array( 2, 1, 3 ), $order( 'added' ), 'higher id first' );
        $this->assertSame( array( 1, 2, 3 ), $order( 'type' ), 'Article before Folder' );
        $this->assertSame( array( 1, 2, 3 ), $order( 'modified' ) );
        $all = $this->ids( expBookmarkPage::select( $items, $folders, expBookmarkPage::SCOPE_ALL, '', 'type' ) );
        $this->assertSame( array( 4, 5, 1, 2, 3 ), $all, 'items without a type last; here both unfiled have none: own order' );
    }

    public function testPage()
    {
        $folders = expBookmarkPage::folders( $this->rows() );
        $list = expBookmarkPage::select( $this->items(), $folders, expBookmarkPage::SCOPE_ALL, '', 'own' );
        $page = expBookmarkPage::page( $list, 3, 2 );
        $this->assertSame( 5, $page['count'] );
        $this->assertSame( array( 2, 3 ), $this->ids( $page['items'] ) );
        $this->assertTrue( $page['items'][0]['group_start'] );
        $this->assertTrue( $page['items'][0]['group_continued'], 'group 10 began on the page before' );
        $this->assertSame( 2, $page['items'][0]['group_count'] );
        $this->assertTrue( $page['items'][1]['group_start'] );
        $this->assertFalse( $page['items'][1]['group_continued'] );

        $behind = expBookmarkPage::page( $list, 50, 2 );
        $this->assertSame( 0, $behind['offset'], 'an offset behind the end goes back to the first page' );
        $this->assertFalse( $behind['items'][1]['group_start'] );
    }

    public function testSummary()
    {
        $summary = expBookmarkPage::summary( $this->items(), expBookmarkPage::folders( $this->rows() ) );
        $this->assertSame( array( 'bookmarks' => 5, 'folders' => 3, 'unfiled' => 2, 'hidden' => 2, 'gone' => 2 ), $summary );
    }

    public function testShiftBefore()
    {
        $this->assertSame( 7, expBookmarkPage::shiftBefore( array( 7, 8, 9 ), 8, -1 ) );
        $this->assertFalse( expBookmarkPage::shiftBefore( array( 7, 8, 9 ), 7, -1 ) );
        $this->assertSame( 0, expBookmarkPage::shiftBefore( array( 7, 8, 9 ), 8, 1 ), 'down to the end' );
        $this->assertSame( 9, expBookmarkPage::shiftBefore( array( 7, 8, 9 ), 7, 1 ) );
        $this->assertFalse( expBookmarkPage::shiftBefore( array( 7, 8, 9 ), 9, 1 ) );
        $this->assertFalse( expBookmarkPage::shiftBefore( array( 7, 8, 9 ), 4, 1 ), 'not among them' );
    }

    public function testPath()
    {
        $this->assertSame( 'content/bookmark', expBookmarkPage::path( expBookmarkPage::SCOPE_ALL ) );
        $this->assertSame( 'content/bookmark/(folder)/top/(sort)/name', expBookmarkPage::path( 0, 'name' ) );
        $this->assertSame( 'content/bookmark/(folder)/12/(offset)/25?q=a%2Fb%20%3Cx%3E', expBookmarkPage::path( 12, 'evil', 'a/b <x>', 25 ) );
        $this->assertStringNotContainsString( '//', expBookmarkPage::path( 3, 'own', '//evil.example' ) );
    }

    public function testSelectedIDsAndShiftRequest()
    {
        $this->assertSame( array( 3, 12 ), expBookmarkPage::selectedIDs( array( '3', '12', '3', '-1', '0', '1x', array( 4 ), 'DROP' ) ) );
        $this->assertSame( array(), expBookmarkPage::selectedIDs( 'abc' ) );
        $this->assertSame( array( 'type' => 'bookmark', 'id' => 12, 'direction' => -1 ), expBookmarkPage::shiftRequest( 'up-12' ) );
        $this->assertSame( array( 'type' => 'folder', 'id' => 3, 'direction' => 1 ), expBookmarkPage::shiftRequest( 'fdown-3' ) );
        $this->assertFalse( expBookmarkPage::shiftRequest( 'up-0' ) );
        $this->assertFalse( expBookmarkPage::shiftRequest( 'up-1;x' ) );
        $this->assertFalse( expBookmarkPage::shiftRequest( array( 'up-1' ) ) );
    }

    public function testTargets()
    {
        $folders = expBookmarkPage::folders( $this->rows() );
        $this->assertSame( array( 10, 11, 12 ), array_column( expBookmarkPage::targets( $folders ), 'id' ) );
        $this->assertSame( 'Work / Press', expBookmarkPage::targets( $folders )[1]['label'] );
        $this->assertSame( array( 12 ), array_column( expBookmarkPage::targets( $folders, $folders[10]['subtree'] ), 'id' ) );
    }
}
