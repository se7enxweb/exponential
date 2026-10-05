<?php
/**
 * The schema handlers of lib/ezdbschema, without a database: the SQL they write from a schema array, the
 * differences between two schemas, and the lint checker.
 *   - eZMysqlSchema, eZSQLiteSchema, eZPgsqlSchema: CREATE TABLE with an auto increment key, varchar/int/longtext/
 *     decimal fields, NOT NULL and defaults, primary, unique and plain indexes; INSERTs with quotes, backslashes and
 *     NULL escaped for each database; the upgrade file for added and removed fields and indexes and a removed table;
 *     the _info entry skipped; schemaType/schemaName/isMultiInsertSupported
 *   - eZSQLiteSchema adds a field in an upgrade file (it wrote nothing for an added field)
 *   - eZDbSchemaChecker::diff(): new, removed and changed tables, fields and indexes, a field or table flagged as
 *     removed, the length of int fields ignored, and diff() of a schema with itself
 *   - eZLintSchema: shortenIdentifier() with the name map and the hard limit, lintCheckSchema() renaming a long
 *     table, field and index and an index named like a table (its fields joined with underscores)
 *
 * No database: the handlers are created without an instance. The lint checker reads settings/dbschema.ini.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdbschema
 */

class eZDbSchemaGenerationTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    private static function table()
    {
        return array(
            'name' => 't',
            'fields' => array(
                'id' => array( 'type' => 'auto_increment', 'default' => false ),
                'name' => array( 'length' => 50, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
                'n' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                'body' => array( 'type' => 'longtext', 'default' => false ),
                'price' => array( 'type' => 'decimal', 'length' => '10,2', 'default' => false ),
            ),
            'indexes' => array(
                'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                't_name' => array( 'type' => 'non-unique', 'fields' => array( 'name' ) ),
                't_n' => array( 'type' => 'unique', 'fields' => array( 'n', 'name' ) ),
            ),
        );
    }

    private static function schema()
    {
        return array( 't' => self::table(), '_info' => array( 'format' => 'generic' ) );
    }

    private static function data()
    {
        return array( 't' => array(
            'fields' => array( 'id', 'name', 'n', 'body', 'price' ),
            'rows' => array( array( 1, "it's", 3, null, '1.50' ), array( 2, 'b\\c', 4, 'x', null ) ),
        ) );
    }

    private static function upgrade()
    {
        return array(
            'table_changes' => array( 't' => array(
                'added_fields' => array( 'extra' => array( 'type' => 'int', 'length' => 11, 'not_null' => '1', 'default' => 5 ) ),
                'removed_fields' => array( 'body' => true ),
                'added_indexes' => array( 't_extra' => array( 'type' => 'non-unique', 'fields' => array( 'extra' ) ) ),
                'removed_indexes' => array( 't_name' => array( 'type' => 'non-unique', 'fields' => array( 'name' ) ) ),
            ) ),
            'removed_tables' => array( 'old' => array() ),
        );
    }

    public function testMysqlSql()
    {
        $s = new eZMysqlSchema( array( 'instance' => null ) );
        $this->assertSame( 'mysql', $s->schemaType() );
        $this->assertSame( 'MySQL', $s->schemaName() );
        $this->assertTrue( $s->isMultiInsertSupported() );
        $this->assertSame(
            "CREATE TABLE t (\n" .
            "  id int(11) NOT NULL AUTO_INCREMENT,\n" .
            "  name varchar(50) NOT NULL DEFAULT '',\n" .
            "  n int(11) NOT NULL DEFAULT '0',\n" .
            "  body longtext,\n" .
            "  price decimal(10,2),\n" .
            "  PRIMARY KEY ( id ),\n" .
            "  KEY t_name ( name ),\n" .
            "  UNIQUE KEY t_n ( n, name )\n" .
            ");\n",
            $s->generateSchemaFile( self::schema() )
        );
        $this->assertSame(
            "INSERT INTO t (id, name, n, body, price) VALUES (1,'it\\'s',3,NULL,'1.50');\n" .
            "INSERT INTO t (id, name, n, body, price) VALUES (2,'b\\\\c',4,'x',NULL);",
            $s->generateDataFile( self::schema(), self::data(), array() )
        );
        $this->assertSame(
            "ALTER TABLE t ADD COLUMN extra int(11) NOT NULL DEFAULT '5';\n" .
            "ALTER TABLE t DROP COLUMN body;\n" .
            "ALTER TABLE t DROP INDEX t_name;\n" .
            "ALTER TABLE t ADD INDEX t_extra ( extra );\n" .
            "DROP TABLE old;\n",
            $s->generateUpgradeFile( self::upgrade() )
        );
        $this->assertSame( "a\\'b\\\\c", $s->escapeSQLString( "a'b\\c" ) );
    }

    public function testSqliteSql()
    {
        $s = new eZSQLiteSchema( array( 'instance' => null ) );
        $this->assertSame( 'sqlite', $s->schemaType() );
        $this->assertSame( 'SQLite', $s->schemaName() );
        $this->assertFalse( $s->isMultiInsertSupported() );
        $sql = $s->generateSchemaFile( self::schema() );
        $this->assertStringContainsString(
            "CREATE TABLE t (\n" .
            "  id INTEGER PRIMARY KEY AUTOINCREMENT,\n" .
            "  name varchar(50) NOT NULL DEFAULT '',\n" .
            "  n INTEGER(11) NOT NULL DEFAULT '0',\n" .
            "  body longtext,\n" .
            "  price decimal(10,2)\n" .
            ");\n",
            $sql
        );
        $this->assertMatchesRegularExpression( '/CREATE\s+INDEX t_name ON t\s+\( name \);/', $sql );
        $this->assertMatchesRegularExpression( '/CREATE\s+UNIQUE INDEX t_n ON t\s+\( n, name \);/', $sql );
        $this->assertSame(
            "INSERT INTO t (id, name, n, body, price) VALUES (1,'it''s',3,NULL,'1.50');\n" .
            "INSERT INTO t (id, name, n, body, price) VALUES (2,'b\\c',4,'x',NULL);",
            $s->generateDataFile( self::schema(), self::data(), array() )
        );
        $this->assertSame( "a''b\\c", $s->escapeSQLString( "a'b\\c" ) );
    }

    /**
     * An upgrade file for SQLite adds the new fields. The base class generated nothing for an added field, so the
     * upgrade dropped the old field and indexed the new one without ever creating it.
     */
    public function testSqliteUpgradeAddsFields()
    {
        $s = new eZSQLiteSchema( array( 'instance' => null ) );
        $sql = $s->generateUpgradeFile( self::upgrade() );
        $this->assertStringContainsString( "ALTER TABLE t ADD COLUMN extra INTEGER(11) NOT NULL DEFAULT '5';\n", $sql );
        $this->assertStringContainsString( "ALTER TABLE t DROP COLUMN body;\n", $sql );
        $this->assertStringContainsString( 'DROP INDEX IF EXISTS "t_name";', $sql );
        $this->assertMatchesRegularExpression( '/CREATE\s+INDEX t_extra ON t\s+\( extra \);/', $sql );
        $this->assertStringContainsString( "DROP TABLE old;\n", $sql );
        $this->assertLessThan( strpos( $sql, 't_extra' ), strpos( $sql, 'ADD COLUMN extra' ), 'the field exists before its index' );
        $this->assertSame( "ALTER TABLE t ADD COLUMN note varchar(20) DEFAULT '';\n",
                           $s->generateAddFieldSql( 't', 'note', array( 'type' => 'varchar', 'length' => 20 ), array() ) );
    }

    public function testPostgresqlSql()
    {
        $s = new eZPgsqlSchema( array( 'instance' => null ) );
        $this->assertSame( 'postgresql', $s->schemaType() );
        $this->assertSame( 'PostgreSQL', $s->schemaName() );
        $sql = $s->generateSchemaFile( self::schema() );
        $this->assertStringContainsString( "CREATE SEQUENCE IF NOT EXISTS t_id_seq\n", $sql );
        $this->assertStringContainsString( "  id integer DEFAULT nextval('t_id_seq'::text) NOT NULL,\n", $sql );
        $this->assertStringContainsString( "  name character varying(50) DEFAULT ''::character varying NOT NULL,\n", $sql );
        $this->assertStringContainsString( "  n integer DEFAULT 0 NOT NULL,\n", $sql );
        $this->assertStringContainsString( "  body text,\n", $sql );
        $this->assertStringContainsString( "  price numeric(10,2)\n", $sql );
        $this->assertStringContainsString( "CREATE INDEX t_name ON t USING btree ( name );", $sql );
        $this->assertStringContainsString( "CREATE UNIQUE INDEX t_n ON t USING btree ( n, name );", $sql );
        $this->assertStringContainsString( "ALTER TABLE ONLY t ADD CONSTRAINT t_pkey PRIMARY KEY ( id );", $sql );

        $data = $s->generateDataFile( self::schema(), self::data(), array() );
        $this->assertStringContainsString( "VALUES (1,'it''s',3,NULL,'1.50');", $data );
        $this->assertStringContainsString( "SELECT setval('t_id_seq',max(id)+1) FROM t;", $data, 'the sequence continues after the inserted rows' );

        $upgrade = $s->generateUpgradeFile( self::upgrade() );
        $this->assertStringContainsString( "ALTER TABLE t ADD COLUMN extra integer;\n", $upgrade );
        $this->assertStringContainsString( "ALTER TABLE t ALTER extra SET DEFAULT 5 ;\n", $upgrade );
        $this->assertStringContainsString( "ALTER TABLE t ALTER extra SET NOT NULL ;\n", $upgrade );
        $this->assertStringContainsString( "DROP INDEX t_name;\n", $upgrade );
        $this->assertStringContainsString( "DROP TABLE old;\n", $upgrade );
    }

    public function testUpgradeCommentsAreWrittenAsSqlComments()
    {
        $s = new eZMysqlSchema( array( 'instance' => null ) );
        $sql = $s->generateUpgradeFile( array( 'removed_tables' => array( 'old' => array( 'comments' => array( "line one\nline two" ) ) ) ) );
        $this->assertSame( "\n-- line one\n-- line two\nDROP TABLE old;\n", $sql );
    }

    public function testDiffOfASchemaWithItselfIsEmpty()
    {
        $this->assertSame( array(), eZDbSchemaChecker::diff( self::schema(), self::schema() ) );
        $this->assertFalse( eZDbSchemaChecker::diff( 'not a schema' ) );
    }

    public function testDiffFindsEveryKindOfChange()
    {
        $old = self::schema();
        $old['gone'] = array( 'fields' => array(), 'indexes' => array() );
        $new = self::schema();
        $new['added'] = array( 'fields' => array( 'a' => array( 'type' => 'int' ) ), 'indexes' => array() );
        $new['t']['fields']['extra'] = array( 'type' => 'int', 'length' => 11 );
        unset( $new['t']['fields']['body'] );
        $new['t']['fields']['name']['length'] = 100;
        $new['t']['fields']['n']['length'] = 20; // ignored for int
        $new['t']['indexes']['t_extra'] = array( 'type' => 'non-unique', 'fields' => array( 'extra' ) );
        unset( $new['t']['indexes']['t_name'] );
        $new['t']['indexes']['t_n']['fields'] = array( 'n' );

        $diff = eZDbSchemaChecker::diff( $old, $new );
        $this->assertSame( array( 'added' ), array_keys( $diff['new_tables'] ) );
        $this->assertSame( array( 'gone' ), array_keys( $diff['removed_tables'] ) );
        $changes = $diff['table_changes']['t'];
        $this->assertSame( array( 'extra' ), array_keys( $changes['added_fields'] ) );
        $this->assertSame( array( 'body' => true ), $changes['removed_fields'] );
        $this->assertSame( array( 'name' ), array_keys( $changes['changed_fields'] ) );
        $this->assertSame( array( 'length' ), $changes['changed_fields']['name']['different-options'] );
        $this->assertSame( 100, $changes['changed_fields']['name']['field-def']['length'] );
        $this->assertSame( array( 't_extra' ), array_keys( $changes['added_indexes'] ) );
        $this->assertSame( array( 't_name' ), array_keys( $changes['removed_indexes'] ) );
        $this->assertSame( array( 'n' ), $changes['changed_indexes']['t_n']['fields'] );
    }

    public function testDiffOfFlaggedRemovals()
    {
        $old = self::schema();
        $new = self::schema();
        $new['t']['fields']['body']['removed'] = true;
        $new['t']['indexes']['t_name']['removed'] = true;
        $new['t']['indexes']['t_name']['comments'] = array( 'renamed' );
        $diff = eZDbSchemaChecker::diff( $old, $new );
        $this->assertSame( array( 'body' => true ), $diff['table_changes']['t']['removed_fields'] );
        $this->assertSame( array( 'renamed' ), $diff['table_changes']['t']['removed_indexes']['t_name']['comments'] );

        $new = self::schema();
        $new['t']['removed'] = true;
        $this->assertArrayHasKey( 't', eZDbSchemaChecker::diff( $old, $new )['removed_tables'] );
    }

    public static function fieldDiffProvider()
    {
        return array(
            'same'                => array( array( 'type' => 'int', 'default' => 0 ), array( 'type' => 'int', 'default' => 0 ), false ),
            'type'                => array( array( 'type' => 'int' ), array( 'type' => 'varchar' ), array( 'type' ) ),
            'default'             => array( array( 'type' => 'int', 'default' => 0 ), array( 'type' => 'int', 'default' => 1 ), array( 'default' ) ),
            'not null added'      => array( array( 'type' => 'int' ), array( 'type' => 'int', 'not_null' => '1' ), array( 'not_null' ) ),
            'not null removed'    => array( array( 'type' => 'int', 'not_null' => '1' ), array( 'type' => 'int' ), array( 'not_null' ) ),
            'int length ignored'  => array( array( 'type' => 'int', 'length' => 11 ), array( 'type' => 'int', 'length' => 4 ), false ),
            'varchar length'      => array( array( 'type' => 'varchar', 'length' => 10 ), array( 'type' => 'varchar', 'length' => 20 ), array( 'length' ) ),
            'falsy option added'  => array( array( 'type' => 'int' ), array( 'type' => 'int', 'not_null' => '0' ), false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('fieldDiffProvider')]
    public function testDiffField( $a, $b, $expected )
    {
        $result = eZDbSchemaChecker::diffField( $a, $b, false, false );
        if ( $expected === false )
            $this->assertFalse( $result );
        else
        {
            $this->assertSame( $expected, $result['different-options'] );
            $this->assertSame( $b, $result['field-def'] );
        }
    }

    public function testUpgradeFromADiffRecreatesTheChange()
    {
        $old = self::schema();
        $new = self::schema();
        $new['t']['fields']['extra'] = array( 'type' => 'int', 'length' => 11, 'not_null' => '1', 'default' => 5 );
        $s = new eZMysqlSchema( array( 'instance' => null ) );
        $this->assertSame( "ALTER TABLE t ADD COLUMN extra int(11) NOT NULL DEFAULT '5';\n",
                           $s->generateUpgradeFile( eZDbSchemaChecker::diff( $old, $new ) ) );
    }

    public function testShortenIdentifier()
    {
        $lint = new eZLintSchema( false, null );
        $map = array( 'contentobject' => 'cobj', 'attribute' => 'attr' );
        $this->assertSame( 'short', $lint->shortenIdentifier( 'short', 10, $map ) );
        $this->assertSame( 'cobj_attr_x', $lint->shortenIdentifier( 'contentobject_attribute_x', 12, $map ) );
        $this->assertSame( 'cobj_attribute_x', $lint->shortenIdentifier( 'contentobject_attribute_x', 16, $map ), 'stops once short enough' );
        $this->assertSame( 'abcdefghij', $lint->shortenIdentifier( 'abcdefghijklmnop', 10, $map ), 'cut at the limit' );
        $this->assertSame( 'abcde', $lint->shortenIdentifier( 'abcdefgh', 5, array() ) );
    }

    public function testLintRenamesLongNamesAndAnIndexNamedLikeATable()
    {
        $longTable = 'a_table_name_that_is_far_too_long';
        $longField = 'a_field_name_that_is_much_longer_than_thirty';
        $schema = array(
            $longTable => array( 'fields' => array( 'id' => array( 'type' => 'int' ) ), 'indexes' => array() ),
            'u' => array(
                'fields' => array( $longField => array( 'type' => 'int' ), 'a' => array( 'type' => 'int' ), 'b' => array( 'type' => 'int' ) ),
                'indexes' => array(
                    'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'a' ) ),
                    'u' => array( 'type' => 'non-unique', 'fields' => array( 'a', 'b' ) ),
                ),
            ),
        );
        $lint = new eZLintSchema( false, null );
        $this->assertFalse( $lint->isLintChecked() );
        $this->assertFalse( @$lint->lintCheckSchema( $schema ) );

        $shortTable = substr( $longTable, 0, 26 );
        $this->assertArrayHasKey( $shortTable, $schema );
        $this->assertTrue( $schema[$longTable]['removed'] );
        $this->assertNotEmpty( $schema[$longTable]['comments'] );

        $shortField = substr( $longField, 0, 30 );
        $this->assertArrayHasKey( $shortField, $schema['u']['fields'] );
        $this->assertTrue( $schema['u']['fields'][$longField]['removed'] );

        $this->assertArrayHasKey( 'u_a_b_i', $schema['u']['indexes'], 'the fields of the new index name are joined with underscores' );
        $this->assertTrue( $schema['u']['indexes']['u']['removed'] );
        $this->assertArrayNotHasKey( 'removed', $schema['u']['indexes']['PRIMARY'] );
    }

    public function testLintOfACleanSchema()
    {
        $schema = self::schema();
        $lint = new eZLintSchema( false, new eZMysqlSchema( array( 'instance' => null ) ) );
        $this->assertTrue( $lint->lintCheckSchema( $schema ) );
        $this->assertSame( self::schema(), $schema );
        $this->assertSame( 'mysql', $lint->schemaType(), 'forwarded to the checked schema' );
        $this->assertSame( 'MySQL', $lint->schemaName() );
        $this->assertTrue( $lint->isMultiInsertSupported() );
        $this->assertFalse( $lint->DBInstance, 'the instance it was given' );
    }
}
