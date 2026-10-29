<?php
/**
 * The schema of data tables (expDataTableSchema; doc/eng-us/features/6.0/data-tables.md, "Schema"). The SQL of every
 * engine is checked as text; plan() and apply() run on a SQLite connection of the fixture (expDataTableSqliteFixture).
 *
 *  DS-01 - fromDefinition(): field types, the counted key, PRIMARY, an index per relation column, unique keys
 *  DS-02 - indexName() is at most 30 characters and stable
 *  DS-03 - installSql(): MySQL AUTO_INCREMENT, PostgreSQL sequence, SQLite AUTOINCREMENT, Oracle sequence and trigger
 *          named as expPDODB reads them, CLOB, SQL Server IDENTITY and NVARCHAR(MAX); a quote in a default
 *  DS-04 - split(): statements on a semicolon at a line end, comments dropped
 *  DS-05 - plan() of a missing table: create; apply() creates it; plan() again: none
 *  DS-06 - a new field: upgrade with ALTER TABLE ... ADD; apply() adds the column
 *  DS-07 - the old schema against the upgraded table: dropping is held, no statement; with drop the plan says so
 *  DS-08 - SQL Server and Oracle create statements (pure)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDataTableSqliteFixture.php';

class expDataTableSchemaTest extends PHPUnit\Framework\TestCase
{
    use expDataTableSqliteFixture;

    protected function tearDown(): void
    {
        $this->tearDownFixture();
    }

    private static function table()
    {
        $schema = expDataTableSchema::fromDefinition( expDTFixtureRow::definition() );
        return $schema['t691_m2_fixture'];
    }

    /** DS-01 */
    public function testFromDefinition()
    {
        $table = self::table();
        $this->assertSame( 't691_m2_fixture', $table['name'] );
        $this->assertSame( array( 'type' => 'auto_increment', 'default' => false ), $table['fields']['id'] );
        $this->assertSame( 'int', $table['fields']['node_id']['type'] );
        $this->assertSame( 0, $table['fields']['node_id']['default'] );
        $this->assertSame( 'varchar', $table['fields']['title']['type'] );
        $this->assertSame( 255, $table['fields']['title']['length'] );
        $this->assertSame( 'float', $table['fields']['price']['type'] );
        $this->assertSame( array( 'type' => 'primary', 'fields' => array( 'id' ) ), $table['indexes']['PRIMARY'] );
        $this->assertSame( array( 'type' => 'non-unique', 'fields' => array( 'node_id' ) ), $table['indexes']['t691_m2_fixture_node_id'] );
        $this->assertSame( array( 'type' => 'unique', 'fields' => array( 'remote_id' ) ), $table['indexes']['t691_m2_fixture_remote'] );
        expDTFixtureRow::$note = true;
        $this->assertSame( array( 'type' => 'longtext', 'default' => false ), self::table()['fields']['note'] );
        $lengths = expDataTableSchema::fromDefinition( expDTFixtureRow::definition(), array( 'title' => 80 ) );
        $this->assertSame( 80, $lengths['t691_m2_fixture']['fields']['title']['length'] );
    }

    /** DS-02 */
    public function testIndexName()
    {
        $this->assertSame( 'short_col', expDataTableSchema::indexName( 'short', 'col' ) );
        $long = expDataTableSchema::indexName( 'a_very_long_table_name_for_tests', 'a_long_column_name' );
        $this->assertLessThanOrEqual( 30, strlen( $long ) );
        $this->assertSame( $long, expDataTableSchema::indexName( 'a_very_long_table_name_for_tests', 'a_long_column_name' ) );
        $this->assertNotSame( $long, expDataTableSchema::indexName( 'a_very_long_table_name_for_tests', 'another_long_column' ) );
    }

    /** DS-03 */
    public function testInstallSqlOfEveryEngine()
    {
        $definition = expDTFixtureRow::definition();
        $definition['fields']['label'] = array( 'name' => 'Label', 'datatype' => 'string', 'default' => "it's", 'required' => true );
        expDTFixtureRow::$note = true;
        $definition['fields']['note'] = array( 'name' => 'Note', 'datatype' => 'text', 'default' => '', 'required' => false );
        $sql = expDataTableSchema::installSql( expDataTableSchema::fromDefinition( $definition ) );
        $this->assertSame( array( 'mysql', 'postgresql', 'sqlite', 'oracle', 'sqlserver' ), array_keys( $sql ) );
        $this->assertStringContainsString( 'AUTO_INCREMENT', $sql['mysql'] );
        $this->assertStringContainsString( 'UNIQUE KEY t691_m2_fixture_remote', $sql['mysql'] );
        $this->assertStringContainsString( "nextval('t691_m2_fixture_id_seq'", $sql['postgresql'] );
        $this->assertStringContainsString( 'AUTOINCREMENT', $sql['sqlite'] );
        $sequence = expPDODB::oracleSequence( 't691_m2_fixture' );
        $this->assertStringContainsString( 'CREATE SEQUENCE ' . $sequence, $sql['oracle'] );
        $this->assertStringContainsString( 'CREATE OR REPLACE TRIGGER', $sql['oracle'] );
        $this->assertStringContainsString( $sequence . '.NEXTVAL INTO :new.id', $sql['oracle'] );
        $this->assertStringContainsString( "END;\n/", $sql['oracle'] );
        $this->assertMatchesRegularExpression( '/note CLOB/', $sql['oracle'] );
        $this->assertStringContainsString( "DEFAULT 'it''s'", $sql['oracle'] );
        $this->assertStringContainsString( '[id] INT IDENTITY(1,1) NOT NULL', $sql['sqlserver'] );
        $this->assertStringContainsString( '[note] NVARCHAR(MAX) NULL', $sql['sqlserver'] );
        $this->assertStringContainsString( "DEFAULT N'it''s'", $sql['sqlserver'] );
        $this->assertStringContainsString( 'CREATE UNIQUE INDEX [t691_m2_fixture_remote]', $sql['sqlserver'] );
        foreach ( $sql as $engine => $text )
            $this->assertStringContainsString( 't691_m2_fixture', $text, $engine );
    }

    /** DS-04 */
    public function testSplit()
    {
        $this->assertSame( array( 'CREATE TABLE a ( x int )', "CREATE INDEX b ON a ( x )" ),
                           expDataTableSchema::split( "-- a comment\nCREATE TABLE a ( x int );\n\n# another\nCREATE INDEX b ON a ( x );\n" ) );
        $this->assertSame( array(), expDataTableSchema::split( "\n-- only a comment\n" ) );
        $this->assertSame( array( 'SELECT 1' ), expDataTableSchema::split( 'SELECT 1' ) );
    }

    /** DS-05 */
    public function testCreateThenNothingToDo()
    {
        $this->setUpFixture( false );
        $db = self::fixtureDB();
        $schema = expDataTableSchema::fromDefinition( expDTFixtureRow::definition() );
        $plan = expDataTableSchema::plan( $schema, $db );
        $this->assertSame( 'sqlite', $plan['engine'] );
        $this->assertSame( 'create', $plan['tables']['t691_m2_fixture']['action'] );
        $this->assertNotEmpty( $plan['tables']['t691_m2_fixture']['statements'] );
        $result = expDataTableSchema::apply( $schema, $db );
        $this->assertTrue( $result['ok'], json_encode( $result['tables'] ) );
        $this->assertTrue( expKernelTableSchema::exists( 't691_m2_fixture', $db, true ) );
        $this->assertFalse( expKernelTableSchema::exists( 't691_m2_fixture', eZDB::instance(), true ), 'not on the site\'s database' );
        $plan = expDataTableSchema::plan( $schema, $db );
        $this->assertSame( 'none', $plan['tables']['t691_m2_fixture']['action'], json_encode( $plan['tables'] ) );
        $this->assertSame( array(), $plan['tables']['t691_m2_fixture']['statements'] );
    }

    /** DS-06 */
    public function testUpgradeAddsAColumn()
    {
        $this->setUpFixture();
        $db = self::fixtureDB();
        expDTFixtureRow::$note = true;
        $schema = expDataTableSchema::fromDefinition( expDTFixtureRow::definition() );
        $plan = expDataTableSchema::plan( $schema, $db );
        $step = $plan['tables']['t691_m2_fixture'];
        $this->assertSame( 'upgrade', $step['action'] );
        $this->assertCount( 1, $step['statements'] );
        $this->assertMatchesRegularExpression( '/^ALTER TABLE t691_m2_fixture ADD COLUMN note/i', $step['statements'][0] );
        $result = expDataTableSchema::apply( $schema, $db );
        $this->assertTrue( $result['ok'], json_encode( $result['tables'] ) );
        $this->assertSame( 1, $result['tables']['t691_m2_fixture']['done'] );
        $this->assertArrayHasKey( 'note', expDataTableDescriber::columns( $db, 't691_m2_fixture' ) );
    }

    /** DS-07 */
    public function testDroppingIsHeld()
    {
        $this->setUpFixture();
        $db = self::fixtureDB();
        expDTFixtureRow::$note = true;
        expDataTableSchema::apply( expDataTableSchema::fromDefinition( expDTFixtureRow::definition() ), $db );
        expDTFixtureRow::$note = false;
        $old = expDataTableSchema::fromDefinition( expDTFixtureRow::definition() );
        $plan = expDataTableSchema::plan( $old, $db );
        $step = $plan['tables']['t691_m2_fixture'];
        $this->assertSame( 'none', $step['action'] );
        $this->assertSame( array(), $step['statements'] );
        $this->assertContains( 'removed fields: note', $step['held'] );
        // asked to drop: the held line says it is done, and the kernel's SQLite handler writes the statement
        $plan = expDataTableSchema::plan( $old, $db, array( 'drop' => true ) );
        $step = $plan['tables']['t691_m2_fixture'];
        $this->assertContains( 'removed fields: note (done)', $step['held'] );
        $this->assertSame( 'upgrade', $step['action'], json_encode( $step ) );
        $this->assertMatchesRegularExpression( '/note/i', implode( ' ', $step['statements'] ) );
        // the column is still there: nothing ran
        $this->assertArrayHasKey( 'note', expDataTableDescriber::columns( $db, 't691_m2_fixture' ) );
    }

    /** DS-08 */
    public function testSqlServerAndOracleCreate()
    {
        $table = self::table();
        $sqlServer = expDataTableSchema::sqlServerCreate( 't691_m2_fixture', $table );
        $this->assertStringStartsWith( 'CREATE TABLE [t691_m2_fixture] (', $sqlServer[0] );
        $this->assertStringContainsString( '[price] FLOAT DEFAULT 0 NOT NULL', $sqlServer[0] );
        $this->assertStringContainsString( '[title] NVARCHAR(255) DEFAULT N\'\' NOT NULL', $sqlServer[0] );
        $this->assertStringContainsString( 'PRIMARY KEY ( [id] )', $sqlServer[0] );
        $this->assertCount( 3, $sqlServer, 'the table and two indexes' );
        $oracle = expDataTableSchema::oracleCreate( 't691_m2_fixture', $table );
        $this->assertStringContainsString( 'title VARCHAR2(255 CHAR)', $oracle[0] );
        $this->assertStringNotContainsString( "DEFAULT ''", $oracle[0], 'Oracle stores an empty text as NULL' );
        $this->assertStringContainsString( 'id INTEGER NOT NULL', $oracle[0] );
        $this->assertCount( 5, $oracle, 'table, two indexes, sequence, trigger' );
        $this->assertSame( 'NVARCHAR(4000) NULL', expDataTableSchema::sqlServerType( array( 'type' => 'varchar', 'length' => 99999 ) ) );
        $this->assertSame( '[a]]b]', expDataTableSchema::quoteSqlServer( 'a]b' ) );
    }
}
