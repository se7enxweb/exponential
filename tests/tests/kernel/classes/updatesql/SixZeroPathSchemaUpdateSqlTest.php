<?php
/**
 * The 6.0 update path gives a database that comes from 5.4 the two schema changes the 6.0 kernel uses and that
 * upstream only shipped in its 7.2 and 7.3 files: ezcontentobject_trash.trashed (MySQL, PostgreSQL) and the
 * <table>_<column>_seq sequence names (PostgreSQL), in the 5.4 to 6.0.0 file and, for a site already on 6.0, in the
 * 6.0.0 to 6.0.15 file. Both are guarded so they can run again. Reads the files only; needs no database. (They were
 * run against throwaway MariaDB and PostgreSQL databases when they were written.)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group updatesql
 */

use PHPUnit\Framework\Attributes\DataProvider;

class SixZeroPathSchemaUpdateSqlTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    private static function read( $file )
    {
        return file_get_contents( self::$root . '/' . $file );
    }

    /**
     * The SQL of a file without its comment lines.
     */
    private static function statements( $file )
    {
        return preg_replace( '/^\s*--.*$/m', '', self::read( $file ) );
    }

    public static function mysqlFiles()
    {
        return array(
            '5.4.0 to 6.0.0' => array( 'update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql' ),
            '6.0.0 to 6.0.15' => array( 'update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql' ),
        );
    }

    public static function postgresqlFiles()
    {
        return array(
            '5.4 to 6.0' => array( 'update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql' ),
            '6.0.0 to 6.0.15' => array( 'update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql' ),
        );
    }

    #[DataProvider( 'mysqlFiles' )]
    public function testMysqlAddsTrashedOnlyWhenMissing( $file )
    {
        $sql = self::statements( $file );
        // the definition of the 7.3 file and the kernel schema, chosen by a look at information_schema
        $this->assertSame( 1, preg_match_all( "/'ALTER TABLE ezcontentobject_trash ADD trashed int\(11\) NOT NULL DEFAULT ''0'''/", $sql ), 'one guarded ADD' );
        $this->assertMatchesRegularExpression( "/TABLE_SCHEMA = DATABASE\(\)\s+AND TABLE_NAME = 'ezcontentobject_trash'\s+AND COLUMN_NAME = 'trashed' \) = 0/", $sql );
        $this->assertMatchesRegularExpression( '/^PREPARE exp_trashed_stmt FROM @exp_trashed_sql;\s*^EXECUTE exp_trashed_stmt;\s*^DEALLOCATE PREPARE exp_trashed_stmt;/m', $sql );
        // no unguarded ADD of the column anywhere
        $this->assertDoesNotMatchRegularExpression( '/^\s*ALTER\s+TABLE\s+ezcontentobject_trash\s+ADD/mi', $sql );
        $this->assertMatchesRegularExpression( "/^\s*trashed int\(11\) NOT NULL default '0',/m", self::read( 'kernel/sql/mysql/kernel_schema.sql' ) );
    }

    #[DataProvider( 'postgresqlFiles' )]
    public function testPostgresqlAddsTrashedOnlyWhenMissing( $file )
    {
        $sql = self::statements( $file );
        $this->assertSame( 1, preg_match_all( '/^\s*ALTER TABLE ezcontentobject_trash ADD trashed integer DEFAULT 0 NOT NULL;/m', $sql ) );
        $this->assertMatchesRegularExpression( "/IF NOT EXISTS \( SELECT 1 FROM information_schema.columns\s+WHERE table_schema = current_schema\(\)\s+AND table_name = 'ezcontentobject_trash'\s+AND column_name = 'trashed' \) THEN\s+ALTER TABLE ezcontentobject_trash ADD/", $sql );
        $this->assertMatchesRegularExpression( '/^\s*trashed integer DEFAULT 0 NOT NULL/m', self::read( 'kernel/sql/postgresql/kernel_schema.sql' ) );
    }

    /**
     * The (table, column, old name) rows of the rename block of a file.
     */
    private static function renameRows( $file )
    {
        preg_match_all( "/^\s*\( '([a-z_0-9]+)', '([a-z_0-9]+)', '([a-z_0-9]+)' \),?$/m", self::read( $file ), $m, PREG_SET_ORDER );
        $rows = array();
        foreach ( $m as $row )
            $rows[] = array( $row[1], $row[2], $row[3] );
        return $rows;
    }

    #[DataProvider( 'postgresqlFiles' )]
    public function testPostgresqlRenamesTheSequencesOfTheSevenTwoFile( $file )
    {
        $upstream = self::read( 'update/database/postgresql/7.2/dbupdate-6.13.0-to-7.2.0.sql' );
        preg_match_all( '/^ALTER SEQUENCE ([a-z_0-9]+) RENAME TO ([a-z_0-9]+);$/m', $upstream, $renames, PREG_SET_ORDER );
        preg_match_all( "/^ALTER TABLE ([a-z_0-9]+) ALTER COLUMN ([a-z_0-9]+) SET DEFAULT nextval\('([a-z_0-9]+)'\);$/m", $upstream, $defaults, PREG_SET_ORDER );
        $expected = array();
        $newToOld = array();
        foreach ( $renames as $r )
            $newToOld[$r[2]] = $r[1];
        foreach ( $defaults as $d )
        {
            $this->assertSame( $d[1] . '_' . $d[2] . '_seq', $d[3], 'the upstream names follow <table>_<column>_seq' );
            $this->assertArrayHasKey( $d[3], $newToOld );
            $expected[] = array( $d[1], $d[2], $newToOld[$d[3]] );
        }
        $this->assertCount( 88, $expected );
        $this->assertSame( $expected, self::renameRows( $file ) );

        $sql = self::statements( $file );
        // renamed only when the old one exists and the new name is free
        $this->assertMatchesRegularExpression( "/IF EXISTS \( SELECT 1 FROM pg_class\s+WHERE relkind = 'S' AND relname = r.old_name AND pg_table_is_visible\( oid \) \)\s+AND NOT EXISTS \( SELECT 1 FROM pg_class\s+WHERE relname = new_name AND pg_table_is_visible\( oid \) \) THEN\s+EXECUTE 'ALTER SEQUENCE '/", $sql );
        // the default is reset only when it does not name the new sequence yet
        $this->assertMatchesRegularExpression( '/position\( quote_literal\( new_name \) in column_default \) = 0 \) \) THEN\s+EXECUTE \'ALTER TABLE \'/', $sql );
        $this->assertDoesNotMatchRegularExpression( '/^\s*ALTER SEQUENCE/m', $sql, 'no unguarded rename' );
    }

    /**
     * Every sequence of the 6.0 kernel schema comes from the rename block or is created by the 6.0.15 file.
     */
    public function testEveryKernelSequenceIsCovered()
    {
        preg_match_all( '/^CREATE SEQUENCE ([a-z_0-9]+)\b/m', self::read( 'kernel/sql/postgresql/kernel_schema.sql' ), $kernel );
        preg_match_all( '/^CREATE SEQUENCE ([a-z_0-9]+)\b/m', self::read( 'update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql' ), $created );
        $renamed = array();
        foreach ( self::renameRows( 'update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql' ) as $row )
            $renamed[] = $row[0] . '_' . $row[1] . '_seq';
        $missing = array_values( array_diff( $kernel[1], $renamed, $created[1] ) );
        $this->assertSame( array(), $missing );
    }

    /**
     * SQLite: the schema has trashed (and always had it), and there are no sequences to rename.
     */
    public function testSqliteNeedsNeither()
    {
        $this->assertMatchesRegularExpression( '/`trashed` integer NOT NULL DEFAULT \'0\'/', self::read( 'kernel/sql/sqlite/schema.sql' ) );
        $this->assertStringContainsString( 'nor the', self::read( 'update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' ) );
        // trashed_by and trashed_via are added there; trashed itself is not
        $this->assertDoesNotMatchRegularExpression( '/\btrashed\b/', self::statements( 'update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' ) );
    }
}
