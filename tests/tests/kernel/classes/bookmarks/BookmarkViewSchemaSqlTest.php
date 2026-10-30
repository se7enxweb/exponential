<?php
/**
 * The table of the bookmarks of system pages (expbookmark_view) is the same everywhere it is written: the persistent
 * object, the schema definition share/db_schema.dba (which installs every engine, Oracle and MongoDB included, and
 * from which update/common/scripts/6.0/createbookmarkviewtable.php creates it), the kernel schemas of MySQL,
 * PostgreSQL and SQLite, and the 6.0.0 to 6.0.15 update files. The SQLite update runs twice on a small database it
 * builds in var/tmp and changes nothing the second time. Needs no installation.
 *
 *  BV-S1 the columns of expBookmarkView::definition() are those of share/db_schema.dba, keyed by bookmark_id, with the user index
 *  BV-S2 the three kernel schemas create the table with the same columns, the key and the index
 *  BV-S3 the update files create it only when it is missing (IF NOT EXISTS, a guarded DO block on PostgreSQL)
 *  BV-S4 the SQLite update runs twice and keeps the rows; bookmark_id is unique
 *  BV-S5 the update script exists, is executable and goes through expBookmarkView::install()
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group bookmarks
 */

class BookmarkViewSchemaSqlTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    private static $columns = array( 'bookmark_id', 'created', 'module_uri', 'user_id' );

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    /** BV-S1 */
    public function testDefinitionAndSchemaAgree()
    {
        $schema = null;
        include self::$root . '/share/db_schema.dba';
        $this->assertArrayHasKey( 'expbookmark_view', $schema );
        $table = $schema['expbookmark_view'];
        $this->assertSame( self::$columns, array_keys( $table['fields'] ) );
        $this->assertSame( 'longtext', $table['fields']['module_uri']['type'] );
        $this->assertSame( array( 'bookmark_id' ), $table['indexes']['PRIMARY']['fields'] );
        $this->assertSame( array( 'user_id' ), $table['indexes']['expbookmark_view_user']['fields'] );

        $definition = expBookmarkView::definition();
        $persistent = array_keys( $definition['fields'] );
        sort( $persistent );
        $this->assertSame( self::$columns, $persistent );
        $this->assertSame( 'expbookmark_view', $definition['name'] );
        $this->assertSame( array( 'bookmark_id' ), $definition['keys'] );
        $this->assertArrayNotHasKey( 'increment_key', $definition, 'the key is the id of the bookmark row, never counted by itself' );
    }

    /** BV-S2 */
    public function testKernelSchemas()
    {
        $files = array( 'mysql' => 'kernel/sql/mysql/kernel_schema.sql',
                        'postgresql' => 'kernel/sql/postgresql/kernel_schema.sql',
                        'sqlite' => 'kernel/sql/sqlite/schema.sql' );
        foreach ( $files as $engine => $file )
        {
            $sql = file_get_contents( self::$root . '/' . $file );
            $this->assertMatchesRegularExpression( '/CREATE TABLE `?expbookmark_view`? \((.*?)\n\)/s', $sql, $engine );
            preg_match( '/CREATE TABLE `?expbookmark_view`? \((.*?)\n\)/s', $sql, $m );
            preg_match_all( '/^[\s,]*`?([a-z_]+)`? (?:int|integer|longtext|text)/m', $m[1], $cols );
            $this->assertSame( self::$columns, $cols[1], $engine );
            $this->assertMatchesRegularExpression( '/expbookmark_view_user/', $sql, $engine );
        }
        $this->assertStringContainsString( 'PRIMARY KEY  (bookmark_id)', file_get_contents( self::$root . '/kernel/sql/mysql/kernel_schema.sql' ) );
        $this->assertStringContainsString( 'ADD CONSTRAINT expbookmark_view_pkey PRIMARY KEY (bookmark_id)', file_get_contents( self::$root . '/kernel/sql/postgresql/kernel_schema.sql' ) );
        $this->assertStringContainsString( 'PRIMARY KEY (`bookmark_id`)', file_get_contents( self::$root . '/kernel/sql/sqlite/schema.sql' ) );
    }

    /** BV-S3 */
    public function testUpdateFilesAreGuarded()
    {
        $mysql = file_get_contents( self::$root . '/update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertStringContainsString( 'CREATE TABLE IF NOT EXISTS expbookmark_view', $mysql );
        $pg = file_get_contents( self::$root . '/update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $section = substr( $pg, strpos( $pg, '-- Bookmarks of system pages' ) );
        $this->assertMatchesRegularExpression( "/IF NOT EXISTS \( SELECT 1 FROM information_schema.tables WHERE table_schema = current_schema\(\) AND table_name = 'expbookmark_view' \)/", $section );
        $this->assertStringContainsString( 'CREATE INDEX expbookmark_view_user', $section );
        $sqlite = file_get_contents( self::$root . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertStringContainsString( 'CREATE TABLE IF NOT EXISTS expbookmark_view', $sqlite );
        $this->assertStringContainsString( 'CREATE INDEX IF NOT EXISTS expbookmark_view_user', $sqlite );
    }

    /** BV-S4 */
    public function testSqliteUpdateRunsTwice()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'no pdo_sqlite' );
        $dir = self::$root . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $file = $dir . '/bookmark-view-update-test-' . getmypid() . '.db';
        $pdo = new PDO( 'sqlite:' . $file );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $apply = function () use ( $pdo ) {
            $sql = file_get_contents( self::$root . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
            $start = strpos( $sql, '-- Bookmarks of system pages' );
            $section = substr( $sql, $start, strpos( $sql, "\n-- ", strpos( $sql, 'CREATE INDEX IF NOT EXISTS expbookmark_view_user', $start ) ) - $start );
            $body = preg_replace( '/^--.*$/m', '', $section );
            foreach ( preg_split( '/;\s*$/m', $body ) as $statement )
                if ( trim( $statement ) !== '' )
                    $pdo->exec( $statement );
        };
        $apply();
        $cols = array_column( $pdo->query( 'PRAGMA table_info(expbookmark_view)' )->fetchAll( PDO::FETCH_ASSOC ), 'name' );
        $this->assertSame( self::$columns, $cols );
        $pdo->exec( "INSERT INTO expbookmark_view (bookmark_id, user_id, module_uri) VALUES (7, 14, 'setup/info')" );
        $apply();
        $this->assertSame( '1', (string)$pdo->query( 'SELECT COUNT(*) FROM expbookmark_view' )->fetchColumn(), 'the second run kept the row' );
        $refused = false;
        try { $pdo->exec( "INSERT INTO expbookmark_view (bookmark_id, user_id, module_uri) VALUES (7, 15, 'setup/cache')" ); }
        catch ( PDOException $e ) { $refused = true; }
        $this->assertTrue( $refused, 'one address per bookmark' );
        $pdo = null;
        @unlink( $file );
    }

    /** BV-S5 */
    public function testUpdateScript()
    {
        $file = self::$root . '/update/common/scripts/6.0/createbookmarkviewtable.php';
        $this->assertFileExists( $file );
        $this->assertTrue( is_executable( $file ), 'the update script is executable' );
        $code = file_get_contents( $file );
        $this->assertStringStartsWith( "#!/usr/bin/env php\n", $code );
        $this->assertStringContainsString( 'expBookmarkView::install(', $code );
        $this->assertStringContainsString( '[dry-run]', $code );
    }
}
