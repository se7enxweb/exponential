<?php
/**
 * The MonetDB schema handler (expMonetDBSchema) without a server: the DDL of a table and of the kernel schema (equal to
 * kernel/sql/monetdb/kernel_schema.sql), types and defaults read back.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expColumnarTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('columnar')]
class expMonetDBSchemaTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    public function testTableDDL()
    {
        $handler = new expMonetDBSchema( array( 'instance' => null ) );
        $definition = array( 'name' => 'ezx', 'fields' => array(
                                 'id' => array( 'type' => 'auto_increment', 'default' => false ),
                                 'name' => array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => "it's" ),
                                 'version' => array( 'type' => 'int', 'length' => 11, 'not_null' => '1', 'default' => 0 ),
                                 'day' => array( 'type' => 'varchar', 'length' => 10, 'not_null' => '1', 'default' => '' ),
                                 'score' => array( 'type' => 'float', 'not_null' => '1', 'default' => '' ),
                                 'price' => array( 'type' => 'decimal', 'length' => '10,2', 'default' => null ),
                                 'body' => array( 'type' => 'longtext', 'default' => null ),
                                 'data' => array( 'type' => 'longblob', 'default' => null ) ),
                             'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id', 'version' ) ),
                                                 'ezx_name' => array( 'type' => 'unique', 'fields' => array( 'name' ) ),
                                                 'ezx_day' => array( 'type' => 'non-unique', 'fields' => array( 'day' ) ) ) );
        $sql = $handler->generateTableSQLList( 'ezx', $definition, array(), false );
        $this->assertSame( array(
            'CREATE SEQUENCE ezx_id_seq AS INTEGER START WITH 1',
            "CREATE TABLE IF NOT EXISTS ezx (\n"
            . "  id integer DEFAULT NEXT VALUE FOR ezx_id_seq NOT NULL,\n"
            . "  name character varying(50) DEFAULT 'it''s' NOT NULL,\n"
            . "  \"version\" integer DEFAULT 0 NOT NULL,\n"
            . "  \"day\" character varying(10) DEFAULT '' NOT NULL,\n"
            . "  score real NOT NULL,\n"
            . "  price numeric(10,2) DEFAULT NULL,\n"
            . "  body text DEFAULT NULL,\n"
            . "  data blob DEFAULT NULL,\n"
            . "  PRIMARY KEY ( id, \"version\" ),\n"
            . "  CONSTRAINT ezx_name UNIQUE ( name )\n"
            . ')',
            'CREATE INDEX ezx_day ON ezx ( "day" )',
        ), $sql );
        $this->assertSame( "ALTER TABLE ezx ADD CONSTRAINT ezx_code UNIQUE ( code );\n",
                           $handler->generateAddIndexSql( 'ezx', 'ezx_code', array( 'type' => 'unique', 'fields' => array( 'code' ) ), array() ) );
        $this->assertSame( "ALTER TABLE ezx ADD COLUMN extra integer DEFAULT 1 NOT NULL;\n",
                           $handler->generateAddFieldSql( 'ezx', 'extra', array( 'type' => 'int', 'not_null' => '1', 'default' => 1 ), array() ) );
        $this->assertSame( "DROP TABLE ezx CASCADE;\n", $handler->generateDropTable( 'ezx' ) );
    }

    public function testKernelSchemaFile()
    {
        $schema = eZDbSchema::read( self::root() . '/share/db_schema.dba', true );
        $handler = new expMonetDBSchema( $schema + array( 'instance' => null ) );
        $generated = $handler->generateSchemaFile( $handler->schema( array( 'format' => 'local' ) ) );
        $this->assertSame( file_get_contents( self::root() . '/kernel/sql/monetdb/kernel_schema.sql' ), $generated,
                           'kernel/sql/monetdb/kernel_schema.sql is what the handler writes from share/db_schema.dba' );
        $this->assertSame( 153, substr_count( $generated, 'CREATE TABLE' ) );
        $this->assertStringNotContainsString( '::', $generated, 'no PostgreSQL casts' );
        $this->assertStringNotContainsString( 'CREATE UNIQUE INDEX', $generated, 'unique keys are constraints, which MonetDB enforces' );
    }

    public static function types()
    {
        return array(
            array( 'int', 32, 0, 'int', 11 ), array( 'bigint', 64, 0, 'bigint', 20 ), array( 'smallint', 16, 0, 'int', 6 ),
            array( 'varchar', 255, 0, 'varchar', 255 ), array( 'char', 26, 0, 'char', 26 ), array( 'clob', 0, 0, 'longtext', null ),
            array( 'double', 53, 0, 'double', null ), array( 'real', 24, 0, 'float', null ), array( 'decimal', 10, 2, 'decimal', '10,2' ),
            array( 'timestamp', 7, 0, 'timestamp', null ), array( 'date', 0, 0, 'date', null ), array( 'blob', 0, 0, 'longtext', null ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('types')]
    public function testStandardType( $type, $digits, $scale, $expected, $length )
    {
        $found = null;
        $this->assertSame( $expected, expMonetDBSchema::standardType( $type, $digits, $scale, $found ) );
        $this->assertSame( $length, $found );
    }

    public function testDefaultValue()
    {
        $this->assertSame( "it's", expMonetDBSchema::defaultValue( "'it''s'" ) );
        $this->assertSame( 'a\\b', expMonetDBSchema::defaultValue( "'a\\\\b'" ) );
        $this->assertSame( '0', expMonetDBSchema::defaultValue( '0' ) );
        $this->assertNull( expMonetDBSchema::defaultValue( 'NULL' ) );
        $this->assertSame( '', expMonetDBSchema::defaultValue( "''" ) );
    }

    public function testColumnTypes()
    {
        $this->assertSame( 'character varying(255)', expMonetDBSchema::columnType( 'varchar', null ) );
        $this->assertSame( 'smallint', expMonetDBSchema::columnType( 'tinyint', 4 ) );
        $this->assertSame( 'timestamp', expMonetDBSchema::columnType( 'datetime', null ) );
        $this->expectException( RuntimeException::class );
        expMonetDBSchema::columnType( 'geometry', null );
    }
}
