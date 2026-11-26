<?php
/**
 * expHANASchema without a server: the .dba as SAP HANA DDL.
 *
 *  HSC-01 - Types: every .dba type, lengths, defaults, NOT NULL, an NCLOB without a default; char and float marked
 *  HSC-02 - auto_increment: an INTEGER column with the comment exp:auto_increment, the sequence S_<table> and the
 *           trigger T_<table>_BI that numbers a row without id and moves the sequence past an id of its own
 *  HSC-03 - Index keys: a text column of an index is NVARCHAR(5000) with the comment exp:longtext; index names are
 *           <table>$<index> and read back without the table
 *  HSC-04 - The schema file for hdbsql (the trigger ends on a line of its own); upgrade statements
 *  HSC-05 - Long names are cut with a hash and stay apart
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/ezdb/hana/expHANATestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdbschema')]
#[\PHPUnit\Framework\Attributes\Group('hana')]
class expHANASchemaTest extends PHPUnit\Framework\TestCase
{
    private function handler()
    {
        return new expHANASchema( array( 'instance' => expKernelTableSchema::nullDB() ) );
    }

    public function testTypesAndFields()
    {
        $h = $this->handler();
        $this->assertSame( 'INTEGER', $h->convertFromStandardType( 'int', 11 ) );
        $this->assertSame( 'SMALLINT', $h->convertFromStandardType( 'tinyint' ) );
        $this->assertSame( 'NVARCHAR(150)', $h->convertFromStandardType( 'varchar', 150 ) );
        $this->assertSame( 'NCLOB', $h->convertFromStandardType( 'varchar', 6000 ) );
        $this->assertSame( 'NCLOB', $h->convertFromStandardType( 'longtext' ) );
        $this->assertSame( 'REAL', $h->convertFromStandardType( 'float' ) );
        $this->assertSame( 'DOUBLE', $h->convertFromStandardType( 'double' ) );
        $this->assertSame( 'DECIMAL(10,2)', $h->convertFromStandardType( 'decimal', '10, 2' ) );
        $this->assertSame( 'BLOB', $h->convertFromStandardType( 'longblob' ) );
        $this->assertSame( "value NVARCHAR(255) DEFAULT '' NOT NULL",
                           $h->generateFieldDef( 'ezsite_data', 'value', array( 'type' => 'varchar', 'length' => 255, 'not_null' => '1', 'default' => '' ) ) );
        $this->assertSame( '"ORDER" INTEGER DEFAULT 0 NOT NULL', $h->generateFieldDef( 't', 'order', array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ) ) );
        $this->assertSame( 'data_text NCLOB NOT NULL', $h->generateFieldDef( 't', 'data_text', array( 'type' => 'longtext', 'not_null' => '1', 'default' => '' ) ) );
        $this->assertSame( 'n INTEGER DEFAULT NULL', $h->generateFieldDef( 't', 'n', array( 'type' => 'int', 'default' => null ) ) );
        $length = null;
        $this->assertSame( 'varchar', $h->convertToStandardType( 'NVARCHAR', 150, 0, $length ) );
        $this->assertSame( 150, $length );
        $this->assertSame( 'decimal', $h->convertToStandardType( 'DECIMAL', 10, 2, $length ) );
        $this->assertSame( '10,2', $length );
        $this->assertSame( 'longtext', $h->convertToStandardType( 'NCLOB', 2147483647, null, $length ) );
        $this->assertSame( 0, $h->parseDefault( '0', 'int' ) );
        $this->assertSame( '', $h->parseDefault( '', 'varchar' ) );
        $this->assertFalse( $h->parseDefault( null ) );
    }

    public function testAutoIncrementAndMarks()
    {
        $h = $this->handler();
        $def = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ),
                                         'code' => array( 'type' => 'char', 'length' => 2, 'not_null' => '1', 'default' => '' ),
                                         'url' => array( 'type' => 'longtext', 'default' => false ) ),
                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                                          'ezx_url' => array( 'type' => 'non-unique', 'fields' => array( 'url' ) ) ) );
        $sql = $h->generateTableSQLList( 'ezx', $def, array(), false );
        $this->assertSame( "CREATE COLUMN TABLE ezx (\n  id INTEGER NOT NULL,\n  code NVARCHAR(2) DEFAULT '' NOT NULL,\n  url NVARCHAR(5000)\n)", $sql[0] );
        $this->assertContains( "COMMENT ON COLUMN ezx.code IS 'exp:char'", $sql );
        $this->assertContains( "COMMENT ON COLUMN ezx.url IS 'exp:longtext'", $sql );
        $this->assertContains( 'ALTER TABLE ezx ADD CONSTRAINT PK_EZX PRIMARY KEY (id)', $sql );
        $this->assertContains( 'CREATE INDEX "EZX$EZX_URL" ON ezx (url)', $sql );
        $this->assertContains( 'CREATE SEQUENCE S_EZX START WITH 1 INCREMENT BY 1 NO CYCLE', $sql );
        $this->assertContains( "COMMENT ON COLUMN ezx.id IS 'exp:auto_increment'", $sql );
        $trigger = end( $sql );
        $this->assertStringStartsWith( 'CREATE TRIGGER T_EZX_BI BEFORE INSERT ON ezx REFERENCING NEW ROW new_row FOR EACH ROW', $trigger );
        $this->assertStringContainsString( 'IF :new_row.id IS NULL OR :new_row.id = 0 THEN', $trigger );
        $this->assertStringContainsString( 'WHILE :next_id < :new_row.id DO', $trigger );
        $this->assertSame( 'ezx_url', expHANASchema::dbaIndexName( 'ezx', 'EZX$EZX_URL' ) );
        $this->assertSame( 'other', expHANASchema::dbaIndexName( 'ezx', 'OTHER' ) );
    }

    public function testScriptAndUpgrades()
    {
        $h = $this->handler();
        $def = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ) ),
                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) );
        $script = $h->generateTableSchema( 'ezx', $def, array() );
        $this->assertStringContainsString( "END\n;", $script );
        $this->assertSame( "ALTER TABLE ezx ADD (n INTEGER DEFAULT 7 NOT NULL);\n", $h->generateAddFieldSql( 'ezx', 'n', array( 'type' => 'int', 'not_null' => '1', 'default' => 7 ), array() ) );
        $this->assertSame( "ALTER TABLE ezx DROP (n);\n", $h->generateDropFieldSql( 'ezx', 'n', array() ) );
        $this->assertSame( "UPDATE ezx SET name = 'z' WHERE name IS NULL;\nALTER TABLE ezx ALTER (name NVARCHAR(50) DEFAULT 'z' NOT NULL);\n",
                           $h->generateAlterFieldSql( 'ezx', 'name', array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => 'z' ), array() ) );
        $this->assertSame( "DROP INDEX \"EZX\$I\";\n", $h->generateDropIndexSql( 'ezx', 'i', array( 'type' => 'non-unique' ) ) );
        $this->assertSame( "ALTER TABLE ezx DROP CONSTRAINT PK_EZX;\n", $h->generateDropIndexSql( 'ezx', 'PRIMARY', array( 'type' => 'primary' ) ) );
    }

    public function testLongNames()
    {
        $long = str_repeat( 'a', 130 );
        $a = expHANASchema::objectName( 'S_', $long . 'x' );
        $b = expHANASchema::objectName( 'S_', $long . 'y' );
        $this->assertNotSame( $a, $b );
        $this->assertLessThanOrEqual( 127, strlen( $a ) );
        $this->assertLessThanOrEqual( 127, strlen( expHANASchema::storedIndexName( $long, $long ) ) );
    }
}
