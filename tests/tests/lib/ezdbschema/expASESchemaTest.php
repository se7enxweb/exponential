<?php
/**
 * expASESchema without a server: the .dba as SAP ASE DDL (no ASE server was available for live tests).
 *
 *  ASC-01 - Types: every .dba type; NULL written out (an ASE column is NOT NULL unless it says NULL); no default on
 *           UNITEXT; reserved column names bracketed
 *  ASC-02 - auto_increment as INT IDENTITY; primary key PK_<table>; a text column of an index as UNIVARCHAR(1000)
 *  ASC-03 - The isql script ("go"); upgrade statements; the catalogue's types read back
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/ezdb/ase/expASETestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdbschema')]
#[\PHPUnit\Framework\Attributes\Group('ase')]
class expASESchemaTest extends PHPUnit\Framework\TestCase
{
    private function handler()
    {
        return new expASESchema( array( 'instance' => expKernelTableSchema::nullDB() ) );
    }

    public function testTypesAndFields()
    {
        $h = $this->handler();
        $this->assertSame( 'INT', $h->convertFromStandardType( 'int', 11 ) );
        $this->assertSame( 'INT IDENTITY', $h->convertFromStandardType( 'auto_increment' ) );
        $this->assertSame( 'UNIVARCHAR(150)', $h->convertFromStandardType( 'varchar', 150 ) );
        $this->assertSame( 'UNITEXT', $h->convertFromStandardType( 'longtext' ) );
        $this->assertSame( 'DOUBLE PRECISION', $h->convertFromStandardType( 'double' ) );
        $this->assertSame( 'BIGDATETIME', $h->convertFromStandardType( 'datetime' ) );
        $this->assertSame( 'IMAGE', $h->convertFromStandardType( 'longblob' ) );
        $this->assertSame( "value UNIVARCHAR(255) DEFAULT '' NOT NULL",
                           $h->generateFieldDef( 'ezsite_data', 'value', array( 'type' => 'varchar', 'length' => 255, 'not_null' => '1', 'default' => '' ) ) );
        $this->assertSame( '[count] INT DEFAULT 0 NOT NULL', $h->generateFieldDef( 'ezview_counter', 'count', array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ) ) );
        $this->assertSame( 'n INT NULL', $h->generateFieldDef( 't', 'n', array( 'type' => 'int', 'default' => null ) ) );
        $this->assertSame( 'data_text UNITEXT NOT NULL', $h->generateFieldDef( 't', 'data_text', array( 'type' => 'longtext', 'not_null' => '1', 'default' => '' ) ) );
        $length = null;
        $this->assertSame( 'varchar', $h->convertToStandardType( 'univarchar', 300, null, null, $length ) );
        $this->assertSame( 150, $length );
        $this->assertSame( 'longtext', $h->convertToStandardType( 'unitext', 16, null, null, $length ) );
        $this->assertSame( "it's", $h->parseDefault( "DEFAULT 'it''s'" ) );
        $this->assertSame( 0, $h->parseDefault( 'DEFAULT 0' ) );
        $this->assertFalse( $h->parseDefault( null ) );
    }

    public function testTableAndUpgrades()
    {
        $h = $this->handler();
        $def = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ),
                                         'url' => array( 'type' => 'longtext', 'default' => false ) ),
                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                                          'ezx_url' => array( 'type' => 'non-unique', 'fields' => array( 'url' ) ) ) );
        $this->assertSame( array( "CREATE TABLE ezx (\n  id INT IDENTITY NOT NULL,\n  url UNIVARCHAR(1000) NULL\n)",
                                  'ALTER TABLE ezx ADD CONSTRAINT PK_ezx PRIMARY KEY (id)',
                                  'CREATE INDEX ezx_url ON ezx (url)' ),
                           $h->generateTableSQLList( 'ezx', $def, array(), false ) );
        $this->assertStringContainsString( "\ngo\n", $h->generateTableSchema( 'ezx', $def, array() ) );
        $this->assertSame( "ALTER TABLE ezx ADD n INT DEFAULT 7 NOT NULL;\n", $h->generateAddFieldSql( 'ezx', 'n', array( 'type' => 'int', 'not_null' => '1', 'default' => 7 ), array() ) );
        $this->assertSame( "DROP INDEX ezx.ezx_url;\n", $h->generateDropIndexSql( 'ezx', 'ezx_url', array( 'type' => 'non-unique' ) ) );
        $this->assertSame( "ALTER TABLE ezx MODIFY name UNIVARCHAR(50) NULL;\n", $h->generateAlterFieldSql( 'ezx', 'name', array( 'type' => 'varchar', 'length' => 50, 'default' => null ), array() ) );
    }
}
