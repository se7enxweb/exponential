<?php
/**
 * The ClickHouse schema handler (expClickHouseSchema) without a server: a table of the .dba schema as a MergeTree
 * table, the types, and what is left out.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expColumnarTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('columnar')]
class expClickHouseSchemaTest extends PHPUnit\Framework\TestCase
{
    public function testTableDDL()
    {
        $saved = expColumnarTestKit::quietDebug();
        $handler = new expClickHouseSchema( array( 'instance' => null ) );
        $definition = array( 'name' => 'ezx', 'fields' => array(
                                 'id' => array( 'type' => 'auto_increment', 'default' => false ),
                                 'version' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                                 'name' => array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => "it's" ),
                                 'note' => array( 'type' => 'longtext', 'default' => null ),
                                 'price' => array( 'type' => 'decimal', 'length' => '10,2', 'not_null' => '1', 'default' => '' ),
                                 'flag' => array( 'type' => 'tinyint', 'not_null' => '1', 'default' => 1 ) ),
                             'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id', 'version' ) ),
                                                 'ezx_name' => array( 'type' => 'unique', 'fields' => array( 'name' ) ),
                                                 'ezx_flag' => array( 'type' => 'non-unique', 'fields' => array( 'flag' ) ) ) );
        $sql = $handler->generateTableSQLList( 'ezx', $definition, array(), false );
        expColumnarTestKit::restoreDebug( $saved );
        $this->assertSame( array(
            "CREATE TABLE IF NOT EXISTS `ezx` (\n"
            . "  `id` Int32 COMMENT 'auto_increment',\n"
            . "  `version` Int32 DEFAULT 0,\n"
            . "  `name` String DEFAULT 'it\\'s',\n"
            . "  `note` Nullable(String),\n"
            . "  `price` Decimal(10, 2),\n"
            . "  `flag` Int16 DEFAULT 1,\n"
            . "  INDEX `ezx_flag` (`flag`) TYPE minmax GRANULARITY 1\n"
            . ")\nENGINE = MergeTree ORDER BY (`id`, `version`)\nSETTINGS enable_block_number_column = 1, enable_block_offset_column = 1",
        ), $sql, 'the unique index is left out: ClickHouse would not enforce it' );

        $keyless = $handler->generateTableSQLList( 'log', array( 'fields' => array( 'line' => array( 'type' => 'text', 'not_null' => '1', 'default' => '' ) ),
                                                                 'indexes' => array() ), array(), false );
        $this->assertStringContainsString( 'ENGINE = MergeTree ORDER BY tuple()', $keyless[0] );
        $this->assertSame( "ALTER TABLE `ezx` ADD COLUMN IF NOT EXISTS `extra` Nullable(Int64);\n",
                           $handler->generateAddFieldSql( 'ezx', 'extra', array( 'type' => 'bigint', 'default' => null ), array() ) );
        $this->assertSame( '', $handler->generateAddIndexSql( 'ezx', 'ezx_u', array( 'type' => 'unique', 'fields' => array( 'a' ) ), array() ) );
    }

    public static function types()
    {
        return array(
            array( 'Int32', 'int', 11 ), array( 'Nullable(Int64)', 'bigint', 20 ), array( 'UInt8', 'int', 6 ), array( 'String', 'longtext', null ),
            array( 'LowCardinality(Nullable(String))', 'longtext', null ), array( 'Float64', 'double', null ), array( 'Float32', 'float', null ),
            array( 'Decimal(10, 2)', 'decimal', '10,2' ), array( 'DateTime', 'timestamp', null ), array( "DateTime64(6, 'UTC')", 'timestamp', null ),
            array( 'Date', 'date', null ), array( 'Array(String)', 'longtext', null ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('types')]
    public function testStandardType( $type, $expected, $length )
    {
        $found = null;
        $this->assertSame( $expected, expClickHouseSchema::standardType( $type, $found ) );
        $this->assertSame( $length, $found );
    }

    public function testDefaultValue()
    {
        $this->assertSame( "it's", expClickHouseSchema::defaultValue( "'it\\'s'" ) );
        $this->assertSame( '0', expClickHouseSchema::defaultValue( '0' ) );
        $this->assertNull( expClickHouseSchema::defaultValue( 'NULL' ) );
    }

    public function testColumnTypes()
    {
        $this->assertSame( 'Decimal(10, 0)', expClickHouseSchema::columnType( 'decimal', null ) );
        $this->assertSame( 'DateTime', expClickHouseSchema::columnType( 'datetime', null ) );
        $this->expectException( RuntimeException::class );
        expClickHouseSchema::columnType( 'geometry', null );
    }
}
