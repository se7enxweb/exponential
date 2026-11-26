<?php
/**
 * expDuckDBSchema, the schema handler of DuckDB, and the value conversions of the library binding (expDuckDBFFI), without
 * DuckDB:
 *
 *  DS-01 - A table: its sequence without CACHE, the primary key inside CREATE TABLE, indexes without USING btree
 *  DS-02 - Adding and dropping an index; no setval() after the data (correctSequenceValues() does that)
 *  DS-03 - The kernel's share/db_schema.dba as DuckDB DDL, and kernel/sql/duckdb/kernel_schema.sql made from it
 *  DS-04 - Reading back: DuckDB's types and defaults as the standard schema's, lists of the catalog
 *  DS-05 - expDuckDBFFI: 128-bit integers, decimals, timestamps and floats as text
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDuckDBTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdbschema')]
#[\PHPUnit\Framework\Attributes\Group('duckdb')]
class expDuckDBSchemaTest extends PHPUnit\Framework\TestCase
{
    private function handler()
    {
        return new expDuckDBSchema( array( 'instance' => null ) );
    }

    private static function table()
    {
        return array( 'name' => 'ezx_item',
                      'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ),
                                         'name' => array( 'type' => 'varchar', 'length' => 255, 'not_null' => '1', 'default' => '' ),
                                         'section' => array( 'type' => 'int', 'length' => 11, 'not_null' => '1', 'default' => 0 ),
                                         'body' => array( 'type' => 'longtext', 'default' => null ) ),
                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                                          'ezx_item_name' => array( 'type' => 'unique', 'fields' => array( 'name' ) ),
                                          'ezx_item_section' => array( 'type' => 'non-unique', 'fields' => array( 'section', 'name' ) ) ) );
    }

    /** DS-01 */
    public function testTable()
    {
        $arrays = $this->handler()->generateTableArrays( 'ezx_item', self::table(), array(), false );
        $this->assertCount( 1, $arrays['sequences'] );
        $this->assertStringNotContainsString( 'CACHE', $arrays['sequences'][0] );
        $this->assertStringStartsWith( 'CREATE SEQUENCE IF NOT EXISTS ezx_item_id_seq', $arrays['sequences'][0] );
        $this->assertStringContainsString( "id integer DEFAULT nextval('ezx_item_id_seq'::text) NOT NULL", $arrays['tables'][0] );
        $this->assertMatchesRegularExpression( '/,\n  PRIMARY KEY \( id \)\n\)$/', $arrays['tables'][0] );
        $this->assertSame( array( 'CREATE UNIQUE INDEX ezx_item_name ON ezx_item ( name )', 'CREATE INDEX ezx_item_section ON ezx_item ( section, name )' ),
                           $arrays['indexes'] );
        $this->assertSame( array(), $arrays['constraints'] );
        $this->assertSame( 'duckdb', $this->handler()->schemaType() );
    }

    /** DS-02 */
    public function testIndexesAndData()
    {
        $handler = $this->handler();
        $this->assertSame( "ALTER TABLE ezx_item ADD PRIMARY KEY ( id, \"version\" );\n",
                           $handler->generateAddIndexSql( 'ezx_item', 'PRIMARY', array( 'type' => 'primary', 'fields' => array( 'id', 'version' ) ), array() ) );
        $this->assertSame( 'CREATE INDEX ix ON ezx_item ( name )',
                           $handler->generateAddIndexSql( 'ezx_item', 'ix', array( 'type' => 'non-unique', 'fields' => array( 'name' ) ), array(), false ) );
        $rows = array( 'fields' => array( 'id', 'name', 'section', 'body' ), 'rows' => array( array( 1, "it's", 2, null ) ) );
        $sql = implode( "\n", $handler->generateTableInsertSQLList( 'ezx_item', self::table(), $rows, array(), false ) );
        $this->assertStringContainsString( 'INSERT INTO ezx_item', $sql );
        $this->assertStringNotContainsString( 'setval', $sql );
    }

    /** DS-03 */
    public function testKernelSchema()
    {
        $root = dirname( __DIR__, 5 );
        $schema = eZDbSchema::read( $root . '/share/db_schema.dba' );
        $this->assertIsArray( $schema );
        $sql = $this->handler()->generateSchemaFile( $schema );
        $this->assertStringNotContainsString( 'CACHE 1', $sql );
        $this->assertStringNotContainsString( 'USING btree', $sql );
        $this->assertStringNotContainsString( 'ALTER TABLE ONLY', $sql );
        $tables = 0;
        foreach ( $schema as $name => $table )
            if ( $name !== '_info' )
                $tables++;
        $this->assertSame( $tables, substr_count( $sql, 'CREATE TABLE IF NOT EXISTS ' ) );
        $this->assertSame( $sql, (string)file_get_contents( $root . '/kernel/sql/duckdb/kernel_schema.sql' ),
                           'kernel/sql/duckdb/kernel_schema.sql is generated from share/db_schema.dba: bin/php/ezsqldumpschema.php --type=duckdb share/db_schema.dba kernel/sql/duckdb/kernel_schema.sql' );
    }

    /** DS-04 */
    public function testReadingBack()
    {
        $length = null;
        $this->assertSame( 'int', expDuckDBSchema::standardType( 'INTEGER', $length ) );
        $this->assertSame( 11, $length );
        $this->assertSame( 'bigint', expDuckDBSchema::standardType( 'BIGINT', $length ) );
        $this->assertSame( 'varchar', expDuckDBSchema::standardType( 'VARCHAR', $length ) );
        $this->assertSame( 'decimal', expDuckDBSchema::standardType( 'DECIMAL(10,2)', $length ) );
        $this->assertSame( 'double', expDuckDBSchema::standardType( 'DOUBLE', $length ) );
        $this->assertSame( 'float', expDuckDBSchema::standardType( 'FLOAT', $length ) );
        $this->assertSame( 'timestamp', expDuckDBSchema::standardType( 'TIMESTAMP', $length ) );
        $this->assertSame( 'longtext', expDuckDBSchema::standardType( 'BLOB', $length ) );
        $this->assertSame( '', expDuckDBSchema::defaultValue( "CAST('' AS VARCHAR)" ) );
        $this->assertSame( "it's", expDuckDBSchema::defaultValue( "'it''s'" ) );
        $this->assertSame( '0', expDuckDBSchema::defaultValue( '0' ) );
        $this->assertSame( '1.5', expDuckDBSchema::defaultValue( 'CAST(1.5 AS FLOAT)' ) );
        $this->assertNull( expDuckDBSchema::defaultValue( 'NULL' ) );
        $this->assertNull( expDuckDBSchema::defaultValue( 'CAST(NULL AS VARCHAR)' ) );
        $this->assertSame( array( 'id', 'version' ), expDuckDBSchema::listValue( '[id, version]' ) );
        $this->assertSame( array( 'a' ), expDuckDBSchema::listValue( "['a']" ) );
        $this->assertSame( array(), expDuckDBSchema::listValue( '[]' ) );
        $this->assertSame( array(), expDuckDBSchema::listValue( null ) );
    }

    /** DS-05 */
    public function testValueConversions()
    {
        $this->assertSame( '5', expDuckDBFFI::hugeintText( 0, 5 ) );
        $this->assertSame( '-5', expDuckDBFFI::hugeintText( -1, -5 ) );
        $this->assertSame( '18446744073709551616', expDuckDBFFI::hugeintText( 1, 0 ) );
        $this->assertSame( '18446744073709551615', expDuckDBFFI::hugeintText( 0, -1, true ) );
        $this->assertSame( '-18446744073709551616', expDuckDBFFI::hugeintText( -1, 0 ) );
        $this->assertSame( '-170141183460469231731687303715884105728', expDuckDBFFI::hugeintText( PHP_INT_MIN, 0 ) );
        $this->assertSame( '340282366920938463463374607431768211455', expDuckDBFFI::hugeintText( -1, -1, true ) );
        $this->assertSame( '123.45', expDuckDBFFI::decimalText( '12345', 2 ) );
        $this->assertSame( '-0.05', expDuckDBFFI::decimalText( '-5', 2 ) );
        $this->assertSame( '7', expDuckDBFFI::decimalText( '7', 0 ) );
        $this->assertSame( '2026-01-02 03:04:05', expDuckDBFFI::timestampText( 1767323045000000, 1000000 ) );
        $this->assertSame( '2026-01-02 03:04:05.500000', expDuckDBFFI::timestampText( 1767323045500000, 1000000 ) );
        $this->assertSame( '1969-12-31 23:59:59.500000', expDuckDBFFI::timestampText( -500, 1000 ) );
        $this->assertSame( '1.5', expDuckDBFFI::floatText( 1.5 ) );
        $this->assertSame( 'Infinity', expDuckDBFFI::floatText( INF ) );
        $this->assertSame( '579', expDuckDBFFI::decimalAdd( '123', '456' ) );
        $this->assertSame( '56088', expDuckDBFFI::decimalMultiply( '123', '456' ) );
        $this->assertSame( '1', expDuckDBFFI::decimalSubtract( '1000', '999' ) );
    }
}
