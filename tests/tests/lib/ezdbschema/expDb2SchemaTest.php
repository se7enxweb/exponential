<?php
/**
 * expDb2Schema and expInformixSchema without a server: the .dba as Db2 and Informix DDL.
 *
 *  DSC-01 - Types: every .dba type, lengths in characters (CODEUNITS32), defaults, NOT NULL; text as VARCHAR(32672)
 *           and the document columns as CLOB; the catalogue's types read back with the handler's marks
 *  DSC-02 - auto_increment: an INTEGER column, the sequence S_<table> described as table.column, the BEFORE INSERT
 *           trigger that numbers a row without id, NULL or 0
 *  DSC-03 - Index keys: a text column in an index is VARCHAR(n CODEUNITS32) with the n that fits every index it is
 *           in; a non-unique index too long keeps the columns that fit; index names unique per schema
 *  DSC-04 - Upgrade statements: add, alter (with the REORG that Db2 needs), drop
 *  DSC-06 - The kernel's share/db_schema.dba as Db2 and Informix DDL, and kernel/sql/db2 and kernel/sql/informix
 *           kernel_schema.sql made from it
 *  DSC-05 - Informix: SERIAL, VARCHAR/LVARCHAR of four bytes per character, rows kept within 32,767 bytes, the
 *           primary key constraint written Informix's way, no descriptions
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/ezdb/db2/expDb2TestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdbschema')]
#[\PHPUnit\Framework\Attributes\Group('db2')]
class expDb2SchemaTest extends PHPUnit\Framework\TestCase
{
    private function handler( $class = 'expDb2Schema' )
    {
        return new $class( array( 'instance' => expKernelTableSchema::nullDB() ) );
    }

    public function testTypesAndFields()
    {
        $h = $this->handler();
        $this->assertSame( 'INTEGER', $h->convertFromStandardType( 'int', 11 ) );
        $this->assertSame( 'SMALLINT', $h->convertFromStandardType( 'tinyint' ) );
        $this->assertSame( 'VARCHAR(150 CODEUNITS32)', $h->convertFromStandardType( 'varchar', 150 ) );
        $this->assertSame( 'VARCHAR(32 CODEUNITS32)', $h->convertFromStandardType( 'char', 32 ) );
        $this->assertSame( 'VARCHAR(32672)', $h->convertFromStandardType( 'longtext' ) );
        $this->assertSame( 'VARCHAR(32672)', $h->convertFromStandardType( 'varchar', 9000 ) );
        $this->assertSame( 'CLOB(1G)', $h->convertFromStandardType( 'clob' ) );
        $this->assertSame( 'REAL', $h->convertFromStandardType( 'float' ) );
        $this->assertSame( 'DECIMAL(10,2)', $h->convertFromStandardType( 'decimal', '10, 2' ) );
        $this->assertSame( 'BLOB(1G)', $h->convertFromStandardType( 'longblob' ) );
        $this->assertSame( "\"USER\" VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL",
                           $h->generateFieldDef( 't', 'user', array( 'type' => 'varchar', 'length' => 255, 'not_null' => '1', 'default' => '' ) ) );
        $this->assertSame( 'value INTEGER DEFAULT 0 NOT NULL', $h->generateFieldDef( 't', 'value', array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ) ) );
        $this->assertSame( 'n INTEGER DEFAULT NULL', $h->generateFieldDef( 't', 'n', array( 'type' => 'int', 'default' => null ) ) );
        $this->assertSame( 'data_text CLOB(1G)', $h->generateFieldDef( 't', 'data_text', array( 'type' => 'clob', 'default' => '' ) ) );

        $fields = $h->indexedTextFields( 'ezcontentobject_attribute', array( 'fields' => array(
            'data_text' => array( 'type' => 'longtext' ), 'flag' => array( 'type' => 'tinyint' ), 'code' => array( 'type' => 'char', 'length' => 2 ) ) ) );
        $this->assertSame( array( 'clob', 'exp:clob' ), array( $fields['data_text']['type'], $fields['data_text']['db2:mark'] ) );
        $this->assertSame( 'exp:tinyint', $fields['flag']['db2:mark'] );
        $this->assertSame( 'exp:char', $fields['code']['db2:mark'] );

        $length = null;
        $this->assertSame( 'varchar', $h->convertToStandardType( 'VARCHAR', 600, 0, 150, 'CODEUNITS32', null, $length ) );
        $this->assertSame( 150, $length );
        $this->assertSame( 'char', $h->convertToStandardType( 'VARCHAR', 8, 0, 2, 'CODEUNITS32', 'exp:char', $length ) );
        $this->assertSame( 'longtext', $h->convertToStandardType( 'VARCHAR', 32672, 0, 0, 'OCTETS', null, $length ) );
        $this->assertSame( 'longtext', $h->convertToStandardType( 'VARCHAR', 8076, 0, 2019, 'CODEUNITS32', 'exp:longtext', $length ) );
        $this->assertSame( 'longtext', $h->convertToStandardType( 'CLOB', 1073741824, 0, 0, null, 'exp:clob', $length ) );
        $this->assertSame( 'decimal', $h->convertToStandardType( 'DECIMAL', 10, 2, 0, null, null, $length ) );
        $this->assertSame( '10,2', $length );
        $this->assertSame( 'int', $h->convertToStandardType( 'SMALLINT', 2, 0, 0, null, 'exp:tinyint', $length ) );
        $this->assertSame( 4, $length );
        $this->assertSame( "it's", $h->parseDefault( "'it''s'" ) );
        $this->assertSame( 0, $h->parseDefault( '0' ) );
        $this->assertNull( $h->parseDefault( 'NULL' ) );
        $this->assertFalse( $h->parseDefault( null ) );
    }

    public function testAutoIncrement()
    {
        $h = $this->handler();
        $this->assertSame( array(
            'CREATE SEQUENCE S_EZSECTION AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20',
            "COMMENT ON SEQUENCE S_EZSECTION IS 'ezsection.id'",
            'CREATE TRIGGER T_EZSECTION_BI NO CASCADE BEFORE INSERT ON ezsection REFERENCING NEW AS N FOR EACH ROW'
          . ' WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZSECTION',
        ), $h->autoIncrementSQLList( 'ezsection', 'id' ) );
        $this->assertSame( 'id INTEGER NOT NULL', $h->generateFieldDef( 'ezsection', 'id', array( 'type' => 'auto_increment', 'default' => false ) ) );
        $long = str_repeat( 'a', 140 );
        $this->assertLessThanOrEqual( 128, strlen( expDb2Schema::objectName( 'S_', $long ) ) );
        $this->assertNotSame( expDb2Schema::objectName( 'S_', $long . 'x' ), expDb2Schema::objectName( 'S_', $long . 'y' ) );
    }

    public function testIndexKeys()
    {
        $h = $this->handler();
        $def = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment' ), 'parent' => array( 'type' => 'int' ),
                                         'text' => array( 'type' => 'longtext' ), 'action' => array( 'type' => 'longtext' ) ),
                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                                          'ezurlalias_ml_text' => array( 'type' => 'non-unique', 'fields' => array( 'text', 'id', 'parent' ) ),
                                          'ezurlalias_ml_act' => array( 'type' => 'non-unique', 'fields' => array( 'action' ) ) ) );
        $fields = $h->indexedTextFields( 'ezurlalias_ml', $def );
        $this->assertSame( 'varchar', $fields['text']['type'] );
        $this->assertSame( 2020, $fields['text']['length'] );
        $this->assertSame( 2022, $fields['action']['length'] );
        $this->assertSame( 'exp:longtext', $fields['text']['db2:mark'] );
        $sql = $h->generateTableSQLList( 'ezurlalias_ml', $def, array(), false );
        $this->assertContains( 'CREATE INDEX ezurlalias_ml_text ON ezurlalias_ml (text, id, parent)', $sql );
        $this->assertContains( 'ALTER TABLE ezurlalias_ml ADD CONSTRAINT PK_EZURLALIAS_ML PRIMARY KEY (id)', $sql );
        $this->assertContains( "COMMENT ON COLUMN ezurlalias_ml.text IS 'exp:longtext'", $sql );

        // a .dba index name that is no table's prefix: renamed, the old name kept as the description
        $sql = $h->generateTableSQLList( 'cjwnl_edition_send', array( 'fields' => array( 'id' => array( 'type' => 'int' ) ),
                                         'indexes' => array( 'edition_send_id' => array( 'type' => 'non-unique', 'fields' => array( 'id' ) ) ) ), array(), false );
        $this->assertContains( 'CREATE INDEX cjwnl_edition_send_edition_send_id ON cjwnl_edition_send (id)', $sql );
        $this->assertContains( "COMMENT ON INDEX cjwnl_edition_send_edition_send_id IS 'exp:index:edition_send_id'", $sql );

        list( $key, $notes ) = $h->fittingIndexColumns( 't', 'big', array( 'type' => 'non-unique', 'fields' => array( 'a', 'b', 'c' ) ),
            array( 'a' => array( 'type' => 'varchar', 'length' => 1500 ), 'b' => array( 'type' => 'varchar', 'length' => 1500 ), 'c' => array( 'type' => 'int' ) ) );
        $this->assertSame( array( 'a', 'c' ), $key );
        $this->assertCount( 1, $notes );
    }

    public function testUpgradeStatements()
    {
        $h = $this->handler();
        $this->assertSame( "ALTER TABLE t ADD n INTEGER DEFAULT 7 NOT NULL;\n", $h->generateAddFieldSql( 't', 'n', array( 'type' => 'int', 'not_null' => '1', 'default' => 7 ), array() ) );
        $this->assertSame( "ALTER TABLE t DROP COLUMN n;\nCALL SYSPROC.ADMIN_CMD('REORG TABLE T');\n", $h->generateDropFieldSql( 't', 'n', array() ) );
        $alter = $h->generateAlterFieldSql( 't', 'name', array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => 'z' ), array() );
        $this->assertStringStartsWith( "ALTER TABLE t ALTER COLUMN name SET DATA TYPE VARCHAR(50 CODEUNITS32);\nCALL SYSPROC.ADMIN_CMD('REORG TABLE T');\n", $alter );
        $this->assertStringContainsString( "ALTER TABLE t ALTER COLUMN name SET DEFAULT 'z';\n", $alter );
        $this->assertStringContainsString( "UPDATE t SET name = 'z' WHERE name IS NULL;\n", $alter );
        $this->assertStringEndsWith( "ALTER TABLE t ALTER COLUMN name SET NOT NULL;\nCALL SYSPROC.ADMIN_CMD('REORG TABLE T');\n", $alter );
        $this->assertSame( "DROP INDEX t_n;\n", $h->generateDropIndexSql( 't', 't_n', array( 'type' => 'non-unique' ) ) );
    }

    public function testInformix()
    {
        $h = $this->handler( 'expInformixSchema' );
        $this->assertSame( 'informix', $h->schemaType() );
        $this->assertSame( 'SERIAL', $h->convertFromStandardType( 'auto_increment' ) );
        $this->assertSame( 'VARCHAR(200)', $h->convertFromStandardType( 'varchar', 50 ) );
        $this->assertSame( 'LVARCHAR(1020)', $h->convertFromStandardType( 'varchar', 255 ) );
        $this->assertSame( 'LVARCHAR(32739)', $h->convertFromStandardType( 'longtext' ) );
        $this->assertSame( 'DATETIME YEAR TO SECOND', $h->convertFromStandardType( 'datetime' ) );
        $this->assertSame( array(), $h->autoIncrementSQLList( 't', 'id' ) );
        $def = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment' ), 'name' => array( 'type' => 'varchar', 'length' => 255, 'default' => '' ),
                                         'a' => array( 'type' => 'longtext' ), 'b' => array( 'type' => 'longtext' ) ),
                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) );
        $sql = $h->generateTableSQLList( 'kv', $def, array(), false );
        $this->assertStringContainsString( 'id SERIAL NOT NULL', $sql[0] );
        $this->assertStringContainsString( 'a LVARCHAR(15479)', $sql[0] );
        $this->assertContains( 'ALTER TABLE kv ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_kv', $sql );
        $this->assertCount( 2, $sql );
        $length = null;
        $this->assertSame( 'varchar', $h->informixStandardType( 13 + 256, 200, 0, $length ) );
        $this->assertSame( 50, $length );
        $this->assertSame( 'longtext', $h->informixStandardType( 43, 32739, 1, $length ) );
        $this->assertSame( 'auto_increment', $h->informixStandardType( 262, 4, 0, $length ) );
        $this->assertStringEndsWith( "ALTER TABLE t MODIFY (name VARCHAR(200) DEFAULT 'z' NOT NULL);\n",
                                     $h->generateAlterFieldSql( 't', 'name', array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => 'z' ), array() ) );
    }

    public function testKernelSchemaFiles()
    {
        $root = dirname( __DIR__, 4 );
        $schema = eZDbSchema::read( $root . '/share/db_schema.dba' );
        $this->assertIsArray( $schema );
        foreach ( array( 'db2' => 'expDb2Schema', 'informix' => 'expInformixSchema' ) as $type => $class )
        {
            $sql = $this->handler( $class )->generateSchemaFile( $schema );
            $this->assertSame( count( $schema ) - ( isset( $schema['_info'] ) ? 1 : 0 ), substr_count( $sql, 'CREATE TABLE ' ), $type );
            $this->assertSame( $sql, (string)file_get_contents( "$root/kernel/sql/$type/kernel_schema.sql" ),
                               "kernel/sql/$type/kernel_schema.sql is generated from share/db_schema.dba by $class::generateSchemaFile()" );
        }
    }
}
