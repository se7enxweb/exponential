<?php
/**
 * BF-12 - The upgrade SQL of SQLite turns the old bookmark table into the new one, keeps the bookmarks at the top level,
 * and a second run stops on the duplicate column without changing anything. Needs no installation: it runs on a
 * small SQLite database it builds in var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group bookmarks
 */

class BookmarkFolderUpgradeSqlTest extends PHPUnit\Framework\TestCase
{
    private static $installation;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
    }

    /** BF-12 */
    public function testUpgradeSqlOnSqlite()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'no pdo_sqlite' );
        $dir = self::$installation . '/var/tmp';
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
