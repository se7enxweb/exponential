<?php
/**
 * Bookmark folders: the model (eZContentBrowseBookmarkFolder and the folder columns of eZContentBrowseBookmark)
 * and the upgrade SQL. Live style: the model tests read and write the installation they run on, with
 * rows named BMTEST... of the admin user that are removed again; no test database. Where there is no
 * installation (CI) they are skipped. The upgrade test needs no installation: it runs on a small SQLite
 * database it builds in var/tmp.
 *
 *  BF-01 - fetchListForUser() still returns all bookmarks of the user, flat, newest first, folders or not
 *  BF-02 - The tree: depth, path, counts including subfolders, bookmarks in their folder, top level bookmarks
 *  BF-03 - fetchRowsForUser() is the tree depth first, folders before the bookmarks of their level; one folder only
 *  BF-04 - A folder cannot move into itself or below itself; moving up and to the top level works
 *  BF-05 - Deleting a folder moves its bookmarks and folders up one level and deletes no bookmark
 *  BF-06 - Deleting a folder with its bookmarks removes the whole subtree
 *  BF-07 - Another user's folders and bookmarks are refused
 *  BF-08 - createNew( ..., folder ) puts the bookmark into the folder; a second add keeps the folder
 *  BF-09 - place() puts an entry before another, in another folder, and numbers the folder
 *  BF-10 - Damaged data (a cycle, a missing parent) is shown at the top level, not lost and not an endless loop
 *  BF-11 - handleAction: create, rename, delete, move, with messages; empty names are refused
 *  BF-12 - The upgrade SQL of SQLite turns the old table into the new one, keeps the bookmarks at the top level, and runs twice
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group bookmarks
 */

class BookmarkFoldersTest extends PHPUnit\Framework\TestCase
{
    private static $installation;
    private static $live = false;
    private $user = 14;
    private $nodeBase = 99900000;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        chdir( self::$installation );
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        if ( strpos( $this->name(), 'Upgrade' ) === false )
        {
            ezpLiveInstallation::requireOrSkip();
            $this->wipe();
        }
    }

    protected function tearDown(): void
    {
        // PHPUnit runs tearDown() also after setUp() skipped the test; without a
        // usable installation there is nothing to remove and no connection to ask
        if ( strpos( $this->name(), 'Upgrade' ) === false && ezpLiveInstallation::unavailableReason() === null )
            $this->wipe();
    }

    /** removes everything this test made: folders named BMTEST*, bookmarks on the test node ids, of any user */
    private function wipe()
    {
        $db = eZDB::instance();
        $db->query( "DELETE FROM ezcontentbrowsebookmark WHERE node_id >= {$this->nodeBase} AND node_id < {$this->nodeBase} + 1000" );
        $db->query( "DELETE FROM expbookmark_folder WHERE name LIKE 'BMTEST%'" );
    }

    private function folder( $name, $parent = 0, $user = null )
    {
        $f = eZContentBrowseBookmarkFolder::createNew( $user ?: $this->user, 'BMTEST ' . $name, $parent );
        $this->assertNotFalse( $f, "folder $name" );
        return (int) $f->attribute( 'id' );
    }

    private function bookmark( $n, $folder = false, $user = null )
    {
        return (int) eZContentBrowseBookmark::createNew( $user ?: $this->user, $this->nodeBase + $n, 'BMTEST bookmark ' . $n, $folder )->attribute( 'id' );
    }

    private function names( array $rows )
    {
        return array_map( function ( $r ) { return $r['name']; }, $rows );
    }

    /** BF-01 */
    public function testFlatListIsUnchanged()
    {
        $a = $this->bookmark( 1 );
        $f = $this->folder( 'F' );
        $b = $this->bookmark( 2, $f );
        $c = $this->bookmark( 3 );
        $ids = array_map( function ( $x ) { return (int) $x->attribute( 'id' ); }, eZContentBrowseBookmark::fetchListForUser( $this->user ) );
        $mine = array_values( array_filter( $ids, function ( $i ) use ( $a, $b, $c ) { return in_array( $i, array( $a, $b, $c ), true ); } ) );
        $this->assertSame( array( $c, $b, $a ), $mine, 'all bookmarks, newest first, the folder does not hide any' );
        $this->assertSame( count( $ids ), count( eZContentBrowseBookmark::fetchListForUser( $this->user ) ) );
        $this->assertCount( 2, eZContentBrowseBookmark::fetchListForUser( $this->user, 0, 2 ), 'offset and limit still work' );
    }

    /** BF-02 */
    public function testTree()
    {
        $a = $this->folder( 'A' );
        $b = $this->folder( 'B', $a );
        $c = $this->folder( 'C', $b );
        $this->bookmark( 1, $a );
        $this->bookmark( 2, $c );
        $this->bookmark( 3, $c );
        $this->bookmark( 4 );
        list( $tree, $top ) = eZContentBrowseBookmarkFolder::fetchTreeForUser( $this->user );
        $mine = array_values( array_filter( $tree, function ( $n ) use ( $a ) { return $n['id'] === $a; } ) );
        $this->assertCount( 1, $mine );
        $nodeA = $mine[0];
        $this->assertSame( 3, $nodeA['count'], 'bookmarks inside including subfolders' );
        $this->assertSame( 0, $nodeA['depth'] );
        $nodeC = $nodeA['folders'][0]['folders'][0];
        $this->assertSame( 2, $nodeC['depth'] );
        $this->assertSame( array( 'BMTEST A', 'BMTEST B', 'BMTEST C' ), $nodeC['path'] );
        $this->assertCount( 2, $nodeC['bookmarks'] );
        $inTop = array_filter( $top, function ( $x ) { return (int) $x->attribute( 'node_id' ) === $this->nodeBase + 4; } );
        $this->assertCount( 1, $inTop, 'a bookmark without a folder is at the top level' );
    }

    /** BF-03 */
    public function testRowsAreDepthFirst()
    {
        $a = $this->folder( 'A' );
        $b = $this->folder( 'B', $a );
        $z = $this->folder( 'Z' );
        $this->bookmark( 1, $a );
        $this->bookmark( 2, $b );
        $rows = eZContentBrowseBookmarkFolder::fetchRowsForUser( $this->user );
        $mine = array_values( array_filter( $rows, function ( $r ) { return strpos( $r['name'], 'BMTEST' ) === 0; } ) );
        $this->assertSame( array( 'BMTEST A', 'BMTEST B', 'BMTEST bookmark 2', 'BMTEST bookmark 1', 'BMTEST Z' ), $this->names( $mine ) );
        $this->assertSame( array( 0, 1, 2, 1, 0 ), array_map( function ( $r ) { return $r['depth']; }, $mine ) );
        $only = eZContentBrowseBookmarkFolder::fetchRowsForUser( $this->user, $a );
        $this->assertSame( array( 'BMTEST B', 'BMTEST bookmark 2', 'BMTEST bookmark 1' ), $this->names( $only ), 'the content of one folder' );
        $this->assertSame( 0, $only[0]['depth'] );
    }

    /** BF-04 */
    public function testCycleGuardAndMoves()
    {
        $a = $this->folder( 'A' );
        $b = $this->folder( 'B', $a );
        $c = $this->folder( 'C', $b );
        $this->assertFalse( eZContentBrowseBookmarkFolder::moveFolder( $this->user, $a, $a ), 'into itself' );
        $this->assertFalse( eZContentBrowseBookmarkFolder::moveFolder( $this->user, $a, $c ), 'below itself' );
        $this->assertFalse( eZContentBrowseBookmarkFolder::moveFolder( $this->user, $a, $b ) );
        $this->assertTrue( eZContentBrowseBookmarkFolder::isInside( $this->user, $c, $a ) );
        $this->assertTrue( eZContentBrowseBookmarkFolder::moveFolder( $this->user, $c, 0 ), 'to the top level' );
        $this->assertSame( 0, (int) eZContentBrowseBookmarkFolder::fetch( $c )->attribute( 'parent_id' ) );
        $this->assertTrue( eZContentBrowseBookmarkFolder::moveFolder( $this->user, $a, $c ), 'now it may go below c' );
        $this->assertFalse( eZContentBrowseBookmarkFolder::moveFolder( $this->user, $a, 99999999 ), 'a folder that does not exist' );
    }

    /** BF-05 */
    public function testDeleteMovesUp()
    {
        $a = $this->folder( 'A' );
        $b = $this->folder( 'B', $a );
        $c = $this->folder( 'C', $b );
        $x = $this->bookmark( 1, $b );
        $y = $this->bookmark( 2, $c );
        eZContentBrowseBookmarkFolder::fetch( $b )->removeFolder();
        $this->assertNull( eZContentBrowseBookmarkFolder::fetch( $b ) );
        $this->assertSame( $a, (int) eZContentBrowseBookmarkFolder::fetch( $c )->attribute( 'parent_id' ), 'the subfolder moved up one level' );
        $this->assertSame( $a, (int) eZContentBrowseBookmark::fetch( $x )->attribute( 'folder_id' ), 'the bookmark moved up one level' );
        $this->assertSame( $c, (int) eZContentBrowseBookmark::fetch( $y )->attribute( 'folder_id' ), 'deeper bookmarks stay' );
        eZContentBrowseBookmarkFolder::fetch( $a )->removeFolder();
        $this->assertSame( 0, (int) eZContentBrowseBookmark::fetch( $x )->attribute( 'folder_id' ), 'a top level folder: up is the top level' );
        $this->assertNotNull( eZContentBrowseBookmark::fetch( $x ), 'no bookmark was deleted' );
        $this->assertNotNull( eZContentBrowseBookmark::fetch( $y ) );
    }

    /** BF-06 */
    public function testDeleteWithBookmarks()
    {
        $a = $this->folder( 'A' );
        $b = $this->folder( 'B', $a );
        $keep = $this->bookmark( 9 );
        $x = $this->bookmark( 1, $a );
        $y = $this->bookmark( 2, $b );
        eZContentBrowseBookmarkFolder::fetch( $a )->removeFolder( true );
        $this->assertNull( eZContentBrowseBookmarkFolder::fetch( $b ) );
        $this->assertNull( eZContentBrowseBookmark::fetch( $x ) );
        $this->assertNull( eZContentBrowseBookmark::fetch( $y ) );
        $this->assertNotNull( eZContentBrowseBookmark::fetch( $keep ), 'bookmarks outside the folder stay' );
    }

    /** BF-07 */
    public function testOtherUsersAreRefused()
    {
        $other = 10; // anonymous
        $mine = $this->folder( 'Mine' );
        $theirs = $this->folder( 'Theirs', 0, $other );
        $b = $this->bookmark( 1 );
        $this->assertFalse( eZContentBrowseBookmarkFolder::createNew( $this->user, 'BMTEST x', $theirs ), 'a parent of another user' );
        $this->assertFalse( eZContentBrowseBookmarkFolder::moveBookmark( $this->user, $b, $theirs ) );
        $this->assertFalse( eZContentBrowseBookmarkFolder::moveBookmark( $other, $b, 0 ), 'a bookmark of another user' );
        $this->assertFalse( eZContentBrowseBookmarkFolder::moveFolder( $this->user, $theirs, 0 ) );
        $this->assertNull( eZContentBrowseBookmarkFolder::fetchForUser( $this->user, $theirs ) );
        $this->assertNotNull( eZContentBrowseBookmarkFolder::fetchForUser( $this->user, $mine ) );
        $b2 = $this->bookmark( 2, $theirs );
        $this->assertSame( 0, (int) eZContentBrowseBookmark::fetch( $b2 )->attribute( 'folder_id' ), 'a foreign folder falls back to the top level' );
        $ids = array_map( function ( $f ) { return (int) $f->attribute( 'id' ); }, eZContentBrowseBookmarkFolder::fetchListForUser( $this->user ) );
        $this->assertNotContains( $theirs, $ids );
    }

    /** BF-08 */
    public function testCreateNewInFolderAndReAdd()
    {
        $f = $this->folder( 'F' );
        $id = $this->bookmark( 1, $f );
        $this->assertSame( $f, (int) eZContentBrowseBookmark::fetch( $id )->attribute( 'folder_id' ) );
        $again = (int) eZContentBrowseBookmark::createNew( $this->user, $this->nodeBase + 1, 'BMTEST bookmark 1' )->attribute( 'id' );
        $this->assertSame( $f, (int) eZContentBrowseBookmark::fetch( $again )->attribute( 'folder_id' ), 'added again without a folder: stays where it was' );
        $moved = (int) eZContentBrowseBookmark::createNew( $this->user, $this->nodeBase + 1, 'BMTEST bookmark 1', 0 )->attribute( 'id' );
        $this->assertSame( 0, (int) eZContentBrowseBookmark::fetch( $moved )->attribute( 'folder_id' ), 'added again to the top level' );
        $this->assertCount( 1, array_filter( eZContentBrowseBookmark::fetchListForUser( $this->user ), function ( $b ) { return (int) $b->attribute( 'node_id' ) === $this->nodeBase + 1; } ), 'one bookmark per node' );
    }

    /** BF-09 */
    public function testPlace()
    {
        $a = $this->folder( 'A' );
        $b = $this->folder( 'B' );
        $c = $this->folder( 'C' );
        $this->assertTrue( eZContentBrowseBookmarkFolder::place( $this->user, 'folder', $c, 0, $a ) );
        $order = array_map( function ( $f ) { return (int) $f->attribute( 'id' ); }, eZContentBrowseBookmarkFolder::fetchChildren( $this->user, 0 ) );
        $mine = array_values( array_intersect( $order, array( $a, $b, $c ) ) );
        $this->assertSame( array( $c, $a, $b ), $mine );
        $x = $this->bookmark( 1 );
        $y = $this->bookmark( 2, $a );
        $z = $this->bookmark( 3, $a );
        $this->assertTrue( eZContentBrowseBookmarkFolder::place( $this->user, 'bookmark', $x, $a, $y ) );
        $inA = array_map( function ( $r ) { return (int) $r->attribute( 'id' ); }, eZContentBrowseBookmark::fetchListForUserInFolder( $this->user, $a ) );
        $this->assertSame( array( $x, $y, $z ), $inA );
        $this->assertTrue( eZContentBrowseBookmarkFolder::place( $this->user, 'bookmark', $x, $a, 0 ), 'last' );
        $inA = array_map( function ( $r ) { return (int) $r->attribute( 'id' ); }, eZContentBrowseBookmark::fetchListForUserInFolder( $this->user, $a ) );
        $this->assertSame( array( $y, $z, $x ), $inA );
        $this->assertFalse( eZContentBrowseBookmarkFolder::place( $this->user, 'folder', $a, $a, 0 ) );
    }

    /** BF-10 */
    public function testDamagedDataIsShownAtTheTop()
    {
        $a = $this->folder( 'A' );
        $b = $this->folder( 'B', $a );
        $o = $this->folder( 'Orphan' );
        $db = eZDB::instance();
        $db->query( "UPDATE expbookmark_folder SET parent_id=$b WHERE id=$a" );   // a cycle a -> b -> a
        $db->query( "UPDATE expbookmark_folder SET parent_id=88888888 WHERE id=$o" ); // a parent that does not exist
        $x = $this->bookmark( 1, $b );
        $rows = eZContentBrowseBookmarkFolder::fetchRowsForUser( $this->user );
        $names = $this->names( array_filter( $rows, function ( $r ) { return strpos( $r['name'], 'BMTEST' ) === 0; } ) );
        $this->assertContains( 'BMTEST A', $names );
        $this->assertContains( 'BMTEST B', $names );
        $this->assertContains( 'BMTEST Orphan', $names );
        $this->assertContains( 'BMTEST bookmark 1', $names, 'no bookmark is lost' );
        $this->assertCount( 2, eZContentBrowseBookmarkFolder::fetch( $a )->subtreeIDs(), 'subtreeIDs ends on a cycle' );
        $this->assertTrue( eZContentBrowseBookmarkFolder::isInside( $this->user, $b, 0 ) === false, 'isInside ends on a cycle' );
    }

    /** BF-11 */
    public function testHandleAction()
    {
        $http = eZHTTPTool::instance();
        $saved = $_POST;
        $run = function ( array $post ) use ( $http ) {
            $_POST = $post;
            return eZContentBrowseBookmarkFolder::handleAction( $this->user, $http );
        };
        $r = $run( array( 'BookmarkFolderAction' => 'create', 'FolderName' => '   ' ) );
        $this->assertSame( 'error', $r['level'], 'an empty name' );
        $r = $run( array( 'BookmarkFolderAction' => 'create', 'FolderName' => 'BMTEST <b>Bold</b>', 'ParentFolderID' => 0 ) );
        $this->assertSame( 'feedback', $r['level'] );
        $this->assertStringContainsString( 'BMTEST Bold', $r['text'], 'tags are removed from the name' );
        $list = eZContentBrowseBookmarkFolder::fetchListForUser( $this->user );
        $id = 0;
        foreach ( $list as $f )
            if ( $f->attribute( 'name' ) === 'BMTEST Bold' )
                $id = (int) $f->attribute( 'id' );
        $this->assertGreaterThan( 0, $id );
        $r = $run( array( 'BookmarkFolderAction' => 'rename', 'FolderID' => $id, 'FolderName' => 'BMTEST Renamed' ) );
        $this->assertSame( 'BMTEST Renamed', eZContentBrowseBookmarkFolder::fetch( $id )->attribute( 'name' ) );
        $b = $this->bookmark( 1 );
        $r = $run( array( 'BookmarkFolderAction' => 'move_bookmark', 'BookmarkID' => $b, 'FolderID' => $id ) );
        $this->assertSame( 'feedback', $r['level'] );
        $this->assertSame( $id, (int) eZContentBrowseBookmark::fetch( $b )->attribute( 'folder_id' ) );
        $r = $run( array( 'BookmarkFolderAction' => 'move_folder', 'FolderID' => $id, 'ParentFolderID' => $id ) );
        $this->assertSame( 'error', $r['level'], 'a folder into itself' );
        $r = $run( array( 'BookmarkFolderAction' => 'delete', 'FolderID' => $id ) );
        $this->assertSame( 'feedback', $r['level'] );
        $this->assertSame( 0, (int) eZContentBrowseBookmark::fetch( $b )->attribute( 'folder_id' ) );
        $r = $run( array( 'BookmarkFolderAction' => 'nonsense' ) );
        $this->assertSame( 'error', $r['level'] );
        $_POST = $saved;
    }

    /** BF-12 */
    public function testUpgradeSqlOnSqlite()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'no pdo_sqlite' );
        $dir = self::$installation . '/var/tmp';
        // var/tmp is not in git: a fresh checkout has it only when an earlier test made it
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $file = $dir . '/bookmark-folders-upgrade-test-' . getmypid() . '.db';
        $pdo = new PDO( 'sqlite:' . $file );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $pdo->exec( "CREATE TABLE ezcontentbrowsebookmark (id integer NOT NULL PRIMARY KEY AUTOINCREMENT, name varchar(255) NOT NULL DEFAULT '', node_id integer NOT NULL DEFAULT '0', user_id integer NOT NULL DEFAULT '0')" );
        $pdo->exec( 'CREATE INDEX idx_ezcontentbrowsebookmark_ezcontentbrowsebookmark_user ON ezcontentbrowsebookmark (user_id)' );
        $pdo->exec( "INSERT INTO ezcontentbrowsebookmark (name,node_id,user_id) VALUES ('A',2,14),('B',43,14),('C',5,10)" );

        $apply = function () use ( $pdo ) {
            $sql = file_get_contents( self::$installation . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
            $section = substr( $sql, strpos( $sql, '-- Bookmark folders.' ) );
            $body = preg_replace( '/^--.*$/m', '', $section );
            foreach ( preg_split( '/;\s*$/m', $body ) as $statement )
                if ( trim( $statement ) !== '' )
                    $pdo->exec( $statement );
        };
        $apply();
        $cols = array_column( $pdo->query( 'PRAGMA table_info(ezcontentbrowsebookmark)' )->fetchAll( PDO::FETCH_ASSOC ), 'name' );
        $this->assertContains( 'folder_id', $cols );
        $this->assertContains( 'priority', $cols );
        $folderCols = array_column( $pdo->query( 'PRAGMA table_info(expbookmark_folder)' )->fetchAll( PDO::FETCH_ASSOC ), 'name' );
        $this->assertSame( array( 'created', 'id', 'name', 'parent_id', 'priority', 'user_id' ), $folderCols );
        $this->assertSame( '3', (string) $pdo->query( 'SELECT COUNT(*) FROM ezcontentbrowsebookmark WHERE folder_id=0 AND priority=0' )->fetchColumn(), 'every existing bookmark is at the top level' );
        $pdo->exec( "INSERT INTO expbookmark_folder (name,user_id) VALUES ('F',14)" );
        $this->assertSame( '1', (string) $pdo->query( 'SELECT id FROM expbookmark_folder' )->fetchColumn(), 'the folder id counts up by itself' );
        // the second run is the user's mistake and must fail loudly, not damage anything
        $failed = false;
        try { $apply(); } catch ( PDOException $e ) { $failed = true; }
        $this->assertTrue( $failed, 'a second run stops on the duplicate column' );
        $this->assertSame( '3', (string) $pdo->query( 'SELECT COUNT(*) FROM ezcontentbrowsebookmark' )->fetchColumn() );
        $pdo = null;
        // the copy is left in var/tmp for the owner to remove
        $this->assertFileExists( $file );
    }
}
