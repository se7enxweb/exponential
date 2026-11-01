<?php
/**
 * kernel/sql/sqlite/schema.sql keys every table as share/db_schema.dba does (a SQLite installation made from it
 * stores a second version of an object attribute, a class, a workflow), and the upgrade step
 * 6.0.15-sqlite-composite-keys rebuilds a table an older schema.sql keyed on id alone: rows and indexes kept, a
 * second run does nothing. SQLite files under var/tmp, removed afterwards; no installation.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUpgradeSqliteCompositeKeysTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private static $root;

    /** @var eZSQLite3DB|null */
    private $db;

    /** @var string */
    private $file;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $dir = self::$root . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $this->file = $dir . '/upgrade-sqlite-keys-' . getmypid() . '-' . mt_rand() . '.sqlite';
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->file,
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
    }

    protected function tearDown(): void
    {
        if ( $this->db )
            $this->db->close();
        if ( $this->file && file_exists( $this->file ) )
            unlink( $this->file );
    }

    private function context( $apply )
    {
        return new expUpgradeContext( $this->db, $apply, array(), self::$root . '/share/db_schema.dba' );
    }

    private function insertAttribute( $id, $version, $text )
    {
        return $this->db->query( "INSERT INTO ezcontentobject_attribute (id, version, contentobject_id, contentclassattribute_id, language_code, data_text)"
                               . " VALUES ($id, $version, 1, 1, 'eng-GB', '$text')" );
    }

    public function testSchemaSqlStoresTwoVersionsOfAnAttribute()
    {
        foreach ( array( 'schema.sql', 'cleandata.sql' ) as $sql )
            $this->assertNotFalse( $this->db->insertFile( self::$root . '/kernel/sql/sqlite', $sql, false ), "kernel/sql/sqlite/$sql loads" );
        $this->assertSame( array( 'id', 'version' ), expUpgradeStepSqliteCompositeKeys::currentKey( $this->db, 'ezcontentobject_attribute' ) );
        $this->assertNotFalse( $this->insertAttribute( 900001, 1, 'first' ) );
        $this->assertNotFalse( $this->insertAttribute( 900001, 2, 'second' ) );
        $rows = $this->db->arrayQuery( 'SELECT version, data_text FROM ezcontentobject_attribute WHERE id = 900001 ORDER BY version' );
        $this->assertSame( array( '1', '2' ), array_map( 'strval', array_column( $rows, 'version' ) ), 'both versions are stored' );

        $step = new expUpgradeStepSqliteCompositeKeys();
        $this->assertSame( array(), expUpgradeStepSqliteCompositeKeys::wrongKeys( $this->context( false ) ),
                           'every table of schema.sql has the primary key of the .dba' );
        $this->assertSame( expUpgradeStep::PRESENT, $step->detect( $this->context( false ) )['effect'] );
    }

    public function testStepRebuildsATableKeyedOnIdAloneAndASecondRunDoesNothing()
    {
        // The definition of ezcontentobject_attribute in schema.sql before 6.0.15.
        $this->db->query( "CREATE TABLE `ezcontentobject_attribute` (
  `attribute_original_id` integer DEFAULT '0'
,  `contentclassattribute_id` integer NOT NULL DEFAULT '0'
,  `contentobject_id` integer NOT NULL DEFAULT '0'
,  `data_float` double DEFAULT NULL
,  `data_int` integer DEFAULT NULL
,  `data_text` longtext
,  `data_type_string` varchar(50) DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `language_code` varchar(20) NOT NULL DEFAULT ''
,  `language_id` integer NOT NULL DEFAULT '0'
,  `sort_key_int` integer NOT NULL DEFAULT '0'
,  `sort_key_string` varchar(255) NOT NULL DEFAULT ''
,  `version` integer NOT NULL DEFAULT '0'
)" );
        $this->db->query( 'CREATE INDEX "idx_ezcontentobject_attribute_sort_key_int" ON "ezcontentobject_attribute" (`sort_key_int`)' );
        $this->assertNotFalse( $this->insertAttribute( 7, 1, 'kept' ) );
        $this->assertNotFalse( $this->insertAttribute( 8, 1, 'also kept' ) );

        $step = new expUpgradeStepSqliteCompositeKeys();
        $detect = $step->detect( $this->context( false ) );
        $this->assertSame( expUpgradeStep::MISSING, $detect['effect'] );
        $this->assertStringContainsString( 'ezcontentobject_attribute (id)', $detect['detail'] );
        $plan = $step->plan( $this->context( false ) );
        $this->assertCount( 1, $plan, 'the dry run lists the one table' );
        $this->assertStringContainsString( 'rebuild ezcontentobject_attribute with the primary key (id, version)', $plan[0] );

        $this->assertCount( 1, $step->apply( $this->context( true ) ) );
        $this->assertSame( array( 'id', 'version' ), expUpgradeStepSqliteCompositeKeys::currentKey( $this->db, 'ezcontentobject_attribute' ) );
        $rows = $this->db->arrayQuery( 'SELECT id, data_text FROM ezcontentobject_attribute ORDER BY id' );
        $this->assertSame( array( 'kept', 'also kept' ), array_column( $rows, 'data_text' ), 'the rows are kept' );
        $indexes = array_column( $this->db->arrayQuery( "SELECT name FROM sqlite_master WHERE type = 'index' AND tbl_name = 'ezcontentobject_attribute' AND sql IS NOT NULL" ), 'name' );
        $this->assertContains( 'idx_ezcontentobject_attribute_sort_key_int', $indexes, 'the installation\'s own index is created again' );
        $this->assertContains( 'ezcontentobject_attribute_language_code', $indexes, 'an index of the .dba it lacked is added' );
        $this->assertSame( 0, (int)$this->db->arrayQuery( "SELECT COUNT(*) AS n FROM sqlite_master WHERE name = 'ezcontentobject_attribute__exp_rekey'" )[0]['n'] );

        $this->assertNotFalse( $this->insertAttribute( 7, 2, 'second version' ) );
        $this->assertSame( 2, (int)$this->db->arrayQuery( 'SELECT COUNT(*) AS n FROM ezcontentobject_attribute WHERE id = 7' )[0]['n'], 'a second version is stored now' );

        $this->assertSame( expUpgradeStep::PRESENT, $step->detect( $this->context( false ) )['effect'] );
        $this->assertSame( array(), $step->plan( $this->context( false ) ) );
        $this->assertSame( array(), $step->apply( $this->context( true ) ), 'a second run does nothing' );
    }

    public function testStepIsForSqliteOnlyAndRegistered()
    {
        $step = new expUpgradeStepSqliteCompositeKeys();
        $this->assertSame( array( 'sqlite' ), $step->engines() );
        $this->assertSame( 'database', $step->stage() );
        $this->assertStringContainsString( "StepList[]=expUpgradeStepSqliteCompositeKeys\n", file_get_contents( self::$root . '/settings/upgrade.ini' ) );
    }
}
