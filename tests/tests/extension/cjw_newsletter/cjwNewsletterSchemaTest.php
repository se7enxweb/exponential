<?php
/**
 * The schema files of cjw_newsletter against share/db_schema.dba, the source of truth: the MySQL and PostgreSQL
 * files name every table and column of the .dba; the SQLite file, executed into a throwaway SQLite file under
 * var/tmp, has every table, column, primary key and index of the .dba; the upgrade SQL of each engine exists for
 * every release that changes the schema; on SQLite the 4.1 -> 4.2 upgrade applied to a 4.1 schema gives the 4.2
 * schema, and a second run of it fails loudly. On the installation's own database (SQLite only) every table and
 * column of the .dba exists.
 *
 * Only throwaway SQLite files are created, and removed afterwards; the installation's database is only read.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/cjw_newsletter/cjwNewsletterSchemaTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\TestCase;

class cjwNewsletterSchemaTest extends TestCase
{
    private static $ext;
    private static $live = null;
    private $files = array();

    public static function setUpBeforeClass(): void
    {
        self::$ext = getenv( 'CJWNL_EXTENSION_DIR' ) ? getenv( 'CJWNL_EXTENSION_DIR' ) : dirname( __DIR__, 4 ) . '/extension/cjw_newsletter';
        if ( !is_file( self::$ext . '/share/db_schema.dba' ) )
            self::markTestSkipped( 'cjw_newsletter is not installed in extension/' );
        if ( !class_exists( 'SQLite3' ) )
            self::markTestSkipped( 'the sqlite3 extension of PHP is missing' );
        // the kernel is started here, outside a test, when there is an installation (the last test reads its database)
        self::$live = ezpLiveInstallation::unavailableReason();
    }

    protected function tearDown(): void
    {
        foreach ( $this->files as $file )
            if ( is_file( $file ) )
                unlink( $file );
        $this->files = array();
    }

    private static function dba( $file = null )
    {
        $schema = null;
        include $file === null ? self::$ext . '/share/db_schema.dba' : $file;
        unset( $schema['_info'] );
        return $schema;
    }

    /** @return SQLite3 a new empty SQLite database under var/tmp */
    private function throwaway()
    {
        $dir = dirname( __DIR__, 4 ) . '/var/tmp/cjwnl-schema-phpunit';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0770, true );
        $file = $dir . '/db-' . getmypid() . '-' . count( $this->files ) . '.sqlite';
        $this->files[] = $file;
        $db = new SQLite3( $file );
        $db->enableExceptions( true );
        return $db;
    }

    /** @return array table => column names, from CREATE TABLE and ALTER TABLE ADD COLUMN of a SQL file */
    private static function sqlColumns( $sql )
    {
        $sql = preg_replace( '/--[^\n]*/', '', preg_replace( '/^\xEF\xBB\xBF/', '', $sql ) );
        $tables = array();
        preg_match_all( '/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?[`"]?(\w+)[`"]?\s*\((.*?)\)\s*(?:COMMENT=\'[^\']*\'\s*)?(?:ENGINE=\w+\s*)?(?:DEFAULT CHARSET=\w+\s*)?;/is', $sql, $m, PREG_SET_ORDER );
        foreach ( $m as $t )
        {
            $cols = array();
            foreach ( explode( "\n", $t[2] ) as $line )
            {
                $line = trim( $line, " \t\r,`" );
                if ( $line === '' || preg_match( '/^(PRIMARY|KEY|UNIQUE|CONSTRAINT|INDEX|FULLTEXT)\b/i', $line ) )
                    continue;
                if ( preg_match( '/^[`"]?(\w+)[`"]?\s+\w/', $line, $c ) )
                    $cols[] = $c[1];
            }
            $tables[$t[1]] = $cols;
        }
        preg_match_all( '/ALTER\s+TABLE\s+[`"]?(\w+)[`"]?\s+ADD\s+(?:COLUMN\s+)?(?:IF\s+NOT\s+EXISTS\s+)?[`"]?(\w+)[`"]?/i', $sql, $m, PREG_SET_ORDER );
        foreach ( $m as $a )
            if ( isset( $tables[$a[1]] ) && !in_array( $a[2], $tables[$a[1]] ) )
                $tables[$a[1]][] = $a[2];
        return $tables;
    }

    /** @return array table => array( columns => array( name => notnull ), pk => fields, indexes => name => array( unique, fields ) ) */
    private static function sqliteTables( SQLite3 $db )
    {
        $out = array();
        $res = $db->query( "SELECT name FROM sqlite_master WHERE type = 'table' AND name LIKE 'cjwnl_%'" );
        while ( $r = $res->fetchArray( SQLITE3_ASSOC ) )
        {
            $t = $r['name'];
            $out[$t] = array( 'columns' => array(), 'pk' => array(), 'indexes' => array() );
            $cres = $db->query( "PRAGMA table_info($t)" );
            while ( $c = $cres->fetchArray( SQLITE3_ASSOC ) )
            {
                $out[$t]['columns'][$c['name']] = (int)$c['notnull'];
                if ( $c['pk'] )
                    $out[$t]['pk'][(int)$c['pk']] = $c['name'];
            }
            ksort( $out[$t]['pk'] );
            $out[$t]['pk'] = array_values( $out[$t]['pk'] );
            $ires = $db->query( "PRAGMA index_list($t)" );
            while ( $i = $ires->fetchArray( SQLITE3_ASSOC ) )
            {
                if ( strpos( $i['name'], 'sqlite_autoindex_' ) === 0 )
                    continue;
                $fields = array();
                $fres = $db->query( 'PRAGMA index_info(' . $i['name'] . ')' );
                while ( $f = $fres->fetchArray( SQLITE3_ASSOC ) )
                    $fields[(int)$f['seqno']] = $f['name'];
                ksort( $fields );
                $out[$t]['indexes'][$i['name']] = array( (int)$i['unique'], array_values( $fields ) );
            }
            ksort( $out[$t]['indexes'] );
        }
        ksort( $out );
        return $out;
    }

    /** The SQLite shape the .dba describes, in the form of sqliteTables(). */
    private static function expected( array $dba )
    {
        $out = array();
        foreach ( $dba as $t => $def )
        {
            $out[$t] = array( 'columns' => array(), 'pk' => array(), 'indexes' => array() );
            foreach ( $def['fields'] as $name => $f )
                $out[$t]['columns'][$name] = ( $f['type'] === 'auto_increment' ) ? 0 : ( empty( $f['not_null'] ) ? 0 : 1 );
            foreach ( $def['indexes'] as $name => $index )
            {
                $fields = array();
                foreach ( $index['fields'] as $f )
                    $fields[] = is_array( $f ) ? $f['name'] : $f;
                if ( $index['type'] === 'primary' )
                    $out[$t]['pk'] = $fields;
                else
                    $out[$t]['indexes'][strpos( $name, $t . '_' ) === 0 ? $name : $t . '__' . $name] = array( $index['type'] === 'unique' ? 1 : 0, $fields );
            }
            ksort( $out[$t]['indexes'] );
        }
        ksort( $out );
        return $out;
    }

    /** a NOT NULL column read back through PRAGMA: the AUTOINCREMENT key reports 0, as expected() does */
    private static function normalise( array $tables, array $dba )
    {
        foreach ( $tables as $t => $info )
            foreach ( $info['columns'] as $name => $notNull )
                if ( isset( $dba[$t]['fields'][$name] ) && $dba[$t]['fields'][$name]['type'] === 'auto_increment' )
                    $tables[$t]['columns'][$name] = 0;
        return $tables;
    }

    public function testMysqlAndPostgresqlSchemaFilesHaveEveryTableAndColumnOfTheDba()
    {
        $dba = self::dba();
        foreach ( array( 'mysql', 'postgresql' ) as $engine )
        {
            $file = self::$ext . "/sql/$engine/schema.sql";
            $this->assertFileExists( $file );
            $this->assertStringStartsNotWith( "\xEF\xBB\xBF", file_get_contents( $file ), "$engine: no byte order mark" );
            $tables = self::sqlColumns( file_get_contents( $file ) );
            $this->assertEqualsCanonicalizing( array_keys( $dba ), array_keys( $tables ), "$engine: the tables" );
            foreach ( $dba as $t => $def )
                $this->assertEqualsCanonicalizing( array_keys( $def['fields'] ), $tables[$t], "$engine: the columns of $t" );
        }
    }

    public function testSqliteSchemaFileCreatesExactlyTheDba()
    {
        $dba = self::dba();
        $db = $this->throwaway();
        $db->exec( file_get_contents( self::$ext . '/sql/sqlite/schema.sql' ) );
        $this->assertSame( self::expected( $dba ), self::normalise( self::sqliteTables( $db ), $dba ) );
        $db->close();
    }

    public function testEveryTableOfTheDbaCarriesTheCjwnlPrefix()
    {
        foreach ( array_keys( self::dba() ) as $t )
            $this->assertStringStartsWith( 'cjwnl_', $t );
    }

    public function testPostgresqlRepairOf4120AddsTheColumnsThatWereMissing()
    {
        $file = self::$ext . '/update/database/postgresql/4.1/dbupdate-4.1.19-to-4.1.20.sql';
        $this->assertFileExists( $file );
        $sql = file_get_contents( $file );
        foreach ( array( 'cjwnl_edition_send' => array( 'list_contentobject_version', 'list_is_virtual', 'mailqueue_process_scheduled', 'email_reply_to', 'email_return_path' ),
                         'cjwnl_list' => array( 'email_reply_to', 'email_return_path', 'is_virtual', 'virtual_filter' ),
                         'cjwnl_user' => array( 'custom_data_text_1', 'custom_data_text_2', 'custom_data_text_3', 'custom_data_text_4' ) ) as $t => $cols )
            foreach ( $cols as $c )
                $this->assertMatchesRegularExpression( "/ALTER TABLE $t ADD COLUMN IF NOT EXISTS $c /", $sql );
    }

    /**
     * The 4.1 -> 4.2 upgrade of each engine: present. On SQLite: applied to the 4.1.20 schema it gives the schema of
     * the .dba, and a second run fails (it must never silently pass over an installation that already has it).
     */
    public function testUpgradeTo42OnSqliteGivesTheDbaAndASecondRunFails()
    {
        $base = __DIR__ . '/fixtures/cjwnl-schema-4.1.20-sqlite.sql';
        $upgrade = self::$ext . '/update/database/sqlite/4.2/dbupdate-4.1.20-to-4.2.0.sql';
        if ( !is_file( $upgrade ) )
            $this->markTestSkipped( 'no 4.2 upgrade in this version of cjw_newsletter' );
        foreach ( array( 'mysql', 'postgresql' ) as $engine )
            $this->assertFileExists( self::$ext . "/update/database/$engine/4.2/dbupdate-4.1.20-to-4.2.0.sql" );
        $this->assertFileExists( $base, 'the 4.1.20 SQLite schema the upgrade starts from' );
        $dba = self::dba();
        $db = $this->throwaway();
        $db->exec( file_get_contents( $base ) );
        $db->exec( file_get_contents( $upgrade ) );
        $this->assertSame( self::expected( $dba ), self::normalise( self::sqliteTables( $db ), $dba ) );
        $failed = false;
        try
        {
            $db->exec( file_get_contents( $upgrade ) );
        }
        catch ( Exception $e )
        {
            $failed = true;
        }
        $this->assertTrue( $failed, 'a second run of the upgrade fails' );
        $db->close();
    }

    /**
     * A small copy: the 4.1.20 tables with one row each. The upgrade keeps the rows and gives every new column its
     * default (an ADD COLUMN NOT NULL without a default would fail here).
     */
    public function testUpgradeTo42KeepsTheRowsOfA41DatabaseAndFillsTheDefaults()
    {
        $upgrade = self::$ext . '/update/database/sqlite/4.2/dbupdate-4.1.20-to-4.2.0.sql';
        if ( !is_file( $upgrade ) )
            $this->markTestSkipped( 'no 4.2 upgrade in this version of cjw_newsletter' );
        $db = $this->throwaway();
        $db->exec( file_get_contents( __DIR__ . '/fixtures/cjwnl-schema-4.1.20-sqlite.sql' ) );
        $old = self::sqliteTables( $db );
        foreach ( $old as $t => $info )
        {
            $cols = array_keys( $info['columns'] );
            $values = array();
            foreach ( $cols as $c )
                $values[] = $c === 'id' ? '1' : "'1'";
            $db->exec( "INSERT INTO $t (" . implode( ', ', $cols ) . ') VALUES (' . implode( ', ', $values ) . ')' );
        }
        $db->exec( file_get_contents( $upgrade ) );
        $dba = self::dba();
        foreach ( $old as $t => $info )
        {
            $row = $db->querySingle( "SELECT * FROM $t", true );
            $this->assertNotEmpty( $row, "the row of $t is kept" );
            foreach ( array_diff( array_keys( $dba[$t]['fields'] ), array_keys( $info['columns'] ) ) as $new )
                $this->assertEquals( $dba[$t]['fields'][$new]['default'], $row[$new], "$t.$new has its default" );
        }
        $db->close();
    }

    /** The MySQL and PostgreSQL upgrades add exactly what the .dba has more than 4.1.20. */
    public function testMysqlAndPostgresqlUpgradesTo42AddEveryNewTableAndColumn()
    {
        if ( !is_file( self::$ext . '/update/database/sqlite/4.2/dbupdate-4.1.20-to-4.2.0.sql' ) )
            $this->markTestSkipped( 'no 4.2 upgrade in this version of cjw_newsletter' );
        $db = $this->throwaway();
        $db->exec( file_get_contents( __DIR__ . '/fixtures/cjwnl-schema-4.1.20-sqlite.sql' ) );
        $old = self::sqliteTables( $db );
        $db->close();
        $dba = self::dba();
        foreach ( array( 'mysql', 'postgresql' ) as $engine )
        {
            $sql = file_get_contents( self::$ext . "/update/database/$engine/4.2/dbupdate-4.1.20-to-4.2.0.sql" );
            $created = self::sqlColumns( $sql );
            preg_match_all( '/ALTER\s+TABLE\s+(\w+)\s+ADD\s+COLUMN\s+"?(\w+)"?/i', $sql, $m, PREG_SET_ORDER );
            $added = array();
            foreach ( $m as $a )
                $added[$a[1]][] = $a[2];
            foreach ( $dba as $t => $def )
            {
                $want = array_keys( $def['fields'] );
                $have = isset( $old[$t] ) ? array_merge( array_keys( $old[$t]['columns'] ), isset( $added[$t] ) ? $added[$t] : array() )
                                          : ( isset( $created[$t] ) ? $created[$t] : array() );
                $this->assertEqualsCanonicalizing( $want, $have, "$engine upgrade: the columns of $t" );
            }
        }
        $pg = file_get_contents( self::$ext . '/update/database/postgresql/4.2/dbupdate-4.1.20-to-4.2.0.sql' );
        foreach ( array_keys( $old ) as $t )
            if ( $t !== 'cjwnl_edition' && $t !== 'cjwnl_list' )
                $this->assertStringContainsString( "ALTER SEQUENCE {$t}_s RENAME TO {$t}_id_seq;", $pg, "the sequence of $t gets the name the driver reads" );
        $this->assertStringNotContainsString( "_s'::text", file_get_contents( self::$ext . '/sql/postgresql/schema.sql' ) );
    }

    public function testTheInstallationsSqliteDatabaseHasEveryTableAndColumnOfTheDba()
    {
        if ( self::$live !== null )
            $this->markTestSkipped( self::$live );
        $db = eZDB::instance();
        if ( $db->databaseName() !== 'sqlite' )
            $this->markTestSkipped( 'the installation does not run on SQLite' );
        foreach ( self::dba() as $t => $def )
        {
            $rows = $db->arrayQuery( "PRAGMA table_info($t)" );
            $this->assertNotEmpty( $rows, "table $t exists" );
            $have = array();
            foreach ( $rows as $r )
                $have[] = $r['name'];
            $this->assertEqualsCanonicalizing( array_keys( $def['fields'] ), $have, "the columns of $t" );
        }
    }
}
