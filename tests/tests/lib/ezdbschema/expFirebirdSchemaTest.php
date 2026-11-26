<?php
/**
 * expFirebirdSchema without a server: the .dba as Firebird DDL.
 *
 *  FSC-01 - Types: every .dba type, lengths, defaults, NOT NULL; reserved column names quoted
 *  FSC-02 - auto_increment: an INTEGER column, the sequence S_<table> with the column as its description, the
 *           trigger T_<table>_BI that numbers, moves the sequence and keeps the id in the connection's context
 *  FSC-03 - Index keys: a text column in an index is VARCHAR(n) with the n that fits every index it is in, for the
 *           page size (811 for the URL alias action at 16 KB); a non-unique index too long keeps the columns that fit
 *  FSC-04 - The schema file is an isql script (the trigger between SET TERM); upgrade statements
 *  FSC-05 - Long names are cut with a hash and stay apart
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/ezdb/firebird/expFirebirdTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdbschema')]
#[\PHPUnit\Framework\Attributes\Group('firebird')]
class expFirebirdSchemaTest extends PHPUnit\Framework\TestCase
{
    private function handler( $pageSize = 16384 )
    {
        $h = new expFirebirdSchema( array( 'instance' => expKernelTableSchema::nullDB() ) );
        $h->setPageSize( $pageSize );
        return $h;
    }

    public function testTypesAndFields()
    {
        $h = $this->handler();
        $this->assertSame( 'INTEGER', $h->convertFromStandardType( 'int', 11 ) );
        $this->assertSame( 'BIGINT', $h->convertFromStandardType( 'bigint' ) );
        $this->assertSame( 'VARCHAR(150)', $h->convertFromStandardType( 'varchar', 150 ) );
        $this->assertSame( 'CHAR(32)', $h->convertFromStandardType( 'char', 32 ) );
        $this->assertSame( 'BLOB SUB_TYPE TEXT', $h->convertFromStandardType( 'longtext' ) );
        $this->assertSame( 'DOUBLE PRECISION', $h->convertFromStandardType( 'double' ) );
        $this->assertSame( 'DECIMAL(10,2)', $h->convertFromStandardType( 'decimal', '10, 2' ) );
        $this->assertSame( 'BLOB SUB_TYPE BINARY', $h->convertFromStandardType( 'longblob' ) );
        $this->assertSame( "\"VALUE\" VARCHAR(255) DEFAULT '' NOT NULL",
                           $h->generateFieldDef( 'ezsite_data', 'value', array( 'type' => 'varchar', 'length' => 255, 'not_null' => '1', 'default' => '' ) ) );
        $this->assertSame( 'version INTEGER DEFAULT 0 NOT NULL', $h->generateFieldDef( 't', 'version', array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ) ) );
        $this->assertSame( 'data_text BLOB SUB_TYPE TEXT', $h->generateFieldDef( 't', 'data_text', array( 'type' => 'longtext', 'default' => false ) ) );
        $this->assertSame( 'n INTEGER DEFAULT NULL', $h->generateFieldDef( 't', 'n', array( 'type' => 'int', 'default' => null ) ) );
        $length = null;
        $this->assertSame( 'varchar', $h->convertToStandardType( 37, 0, 0, null, 150, $length ) );
        $this->assertSame( 150, $length );
        $this->assertSame( 'decimal', $h->convertToStandardType( 16, 2, -2, 10, null, $length ) );
        $this->assertSame( '10,2', $length );
        $this->assertSame( 'longtext', $h->convertToStandardType( 261, 1, 0, null, null, $length ) );
        $this->assertSame( "it's", $h->parseDefault( "DEFAULT 'it''s'" ) );
        $this->assertSame( 0, $h->parseDefault( 'DEFAULT 0' ) );
        $this->assertNull( $h->parseDefault( 'DEFAULT NULL' ) );
        $this->assertFalse( $h->parseDefault( null ) );
    }

    public function testAutoIncrement()
    {
        $h = $this->handler();
        $def = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ), 'name' => array( 'type' => 'varchar', 'length' => 20, 'default' => '' ) ),
                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) );
        $list = $h->generateTableSQLList( 'ezfoo', $def, array(), false );
        $this->assertSame( "CREATE TABLE ezfoo (\n  id INTEGER NOT NULL,\n  name VARCHAR(20) DEFAULT ''\n)", $list[0] );
        $this->assertSame( 'ALTER TABLE ezfoo ADD CONSTRAINT PK_EZFOO PRIMARY KEY (id)', $list[1] );
        $this->assertSame( 'CREATE SEQUENCE S_EZFOO', $list[2] );
        $this->assertSame( "COMMENT ON SEQUENCE S_EZFOO IS 'id'", $list[3] );
        $this->assertStringStartsWith( 'CREATE TRIGGER T_EZFOO_BI FOR ezfoo ACTIVE BEFORE INSERT POSITION 0 AS', $list[4] );
        $this->assertStringContainsString( 'NEW.id = NEXT VALUE FOR S_EZFOO;', $list[4] );
        $this->assertStringContainsString( 'moved = GEN_ID(S_EZFOO, NEW.id - GEN_ID(S_EZFOO, 0));', $list[4] );
        $this->assertStringContainsString( "RDB\$SET_CONTEXT('USER_SESSION', 'EXP_ID.EZFOO', NEW.id);", $list[4] );
        $this->assertCount( 5, $list );
        $file = $h->generateTableSchema( 'ezfoo', $def, array() );
        $this->assertStringContainsString( "SET TERM ^ ;\nCREATE TRIGGER", $file );
        $this->assertStringContainsString( "END^\nSET TERM ; ^", $file );
    }

    public function testIndexedTextFitsThePage()
    {
        $schema = eZDbSchema::read( dirname( __DIR__, 4 ) . '/share/db_schema.dba' );
        $h = $this->handler( 16384 );
        $fields = $h->indexedTextFields( 'ezurlalias_ml', $schema['ezurlalias_ml'] );
        $this->assertSame( 'varchar', $fields['text']['type'] );
        $this->assertSame( 812, $fields['text']['length'] );
        $this->assertSame( 811, $fields['action']['length'] );
        $fields = $h->indexedTextFields( 'ezurl', $schema['ezurl'] );
        $this->assertSame( 1021, $fields['url']['length'] );
        $small = $this->handler( 8192 )->indexedTextFields( 'ezurlalias_ml', $schema['ezurlalias_ml'] );
        $this->assertSame( 402, $small['text']['length'] );
        // the key of every kernel index fits a page of 16 KB
        foreach ( $schema as $table => $def )
        {
            if ( $table === '_info' )
                continue;
            $fields = $h->indexedTextFields( $table, $def );
            foreach ( isset( $def['indexes'] ) ? $def['indexes'] : array() as $index => $indexDef )
            {
                list( $key, $notes ) = $h->fittingIndexColumns( $table, $index, $indexDef, $fields );
                $this->assertSame( array(), $notes, "$table.$index" );
                $this->assertCount( count( $indexDef['fields'] ), $key, "$table.$index" );
            }
        }
        // a non-unique index past the key length keeps the columns that fit
        $wide = array( 'a' => array( 'type' => 'varchar', 'length' => 800 ), 'b' => array( 'type' => 'varchar', 'length' => 800 ) );
        list( $key, $notes ) = $h->fittingIndexColumns( 't', 'ab', array( 'type' => 'non-unique', 'fields' => array( 'a', 'b' ) ), $wide );
        $this->assertSame( array( 'a' ), $key );
        $this->assertCount( 1, $notes );
    }

    public function testUpgradeStatementsAndNames()
    {
        $h = $this->handler();
        $this->assertSame( "ALTER TABLE t ADD n INTEGER DEFAULT 7 NOT NULL;\n", $h->generateAddFieldSql( 't', 'n', array( 'type' => 'int', 'not_null' => '1', 'default' => 7 ), array() ) );
        $this->assertSame( "ALTER TABLE t DROP \"VALUE\";\n", $h->generateDropFieldSql( 't', 'value', array() ) );
        $alter = $h->generateAlterFieldSql( 't', 'name', array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => 'z' ), array() );
        $this->assertStringContainsString( "ALTER TABLE t ALTER COLUMN name TYPE VARCHAR(50);\n", $alter );
        $this->assertStringContainsString( "ALTER TABLE t ALTER COLUMN name SET DEFAULT 'z';\n", $alter );
        $this->assertStringContainsString( "UPDATE t SET name = 'z' WHERE name IS NULL;\n", $alter );
        $this->assertStringContainsString( "ALTER TABLE t ALTER COLUMN name SET NOT NULL;\n", $alter );
        $this->assertSame( "DROP INDEX t_name;\n", $h->generateDropIndexSql( 't', 't_name', array( 'type' => 'non-unique' ) ) );
        $this->assertSame( "ALTER TABLE t DROP CONSTRAINT PK_T;\n", $h->generateDropIndexSql( 't', 'PRIMARY', array( 'type' => 'primary' ) ) );
        $long = str_repeat( 'x', 70 );
        $a = expFirebirdSchema::objectName( 'S_', $long . 'a' );
        $b = expFirebirdSchema::objectName( 'S_', $long . 'b' );
        $this->assertLessThanOrEqual( 63, strlen( $a ) );
        $this->assertNotSame( $a, $b );
        $this->assertSame( 'T_EZUSER_BI', $h->triggerName( 'ezuser' ) );
        // index names are unique per database in Firebird: a name without its table's in front gets it
        $this->assertSame( 'cjwnl_subscription_list_contentobject_id', $h->storedIndexName( 'cjwnl_subscription', 'list_contentobject_id' ) );
        $this->assertSame( "COMMENT ON INDEX cjwnl_subscription_list_contentobject_id IS 'exp:index:list_contentobject_id'",
                           $h->indexComment( 'cjwnl_subscription', 'list_contentobject_id' ) );
        $this->assertNull( $h->indexComment( 'ezuser', 'ezuser_login' ) );
        $this->assertSame( "DROP INDEX t_name;\n", $h->generateDropIndexSql( 't', 'name', array( 'type' => 'non-unique' ) ) );
    }
}
