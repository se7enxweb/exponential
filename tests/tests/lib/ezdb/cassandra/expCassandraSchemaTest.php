<?php
/**
 * expCassandraSchema without a server.
 *
 *  CS-01 - A .dba table as CQL: the driver's tables, the types, the key (partition: the first key field, clustering:
 *          the others), secondary indexes on the first field of other indexes, the definition kept as JSON
 *  CS-02 - A table without a primary key gets exp_row_id; an index on the partition key or twice on a column is left out;
 *          long index names stay unique and short
 *  CS-03 - The upgrade statements: ADD, DROP, an altered type as a note, DROP TABLE
 *  CS-04 - A table read back: the definition the handler wrote, or one made from the CQL types of a table made
 *          elsewhere
 *  CS-05 - The data table layer: expDataTableSchema::createStatements() for the engines cassandra and scylladb
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expCassandraTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdbschema')]
#[\PHPUnit\Framework\Attributes\Group('cassandra')]
class expCassandraSchemaTest extends PHPUnit\Framework\TestCase
{
    private static function handler()
    {
        return new expCassandraSchema( array( 'instance' => null ) );
    }

    private static function attribute()
    {
        return array( 'name' => 'ezcontentobject_attribute',
                      'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ),
                                         'version' => array( 'type' => 'int', 'length' => 11, 'not_null' => '1', 'default' => 0 ),
                                         'contentobject_id' => array( 'type' => 'int', 'length' => 11, 'not_null' => '1', 'default' => 0 ),
                                         'data_text' => array( 'type' => 'longtext', 'not_null' => '0', 'default' => null ),
                                         'data_float' => array( 'type' => 'double', 'not_null' => '0', 'default' => null ),
                                         'sort_key_string' => array( 'type' => 'varchar', 'length' => 255, 'not_null' => '1', 'default' => '' ) ),
                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id', 'version' ) ),
                                          'ezcontentobject_attribute_co_id_ver' => array( 'type' => 'non-unique', 'fields' => array( 'contentobject_id', 'version' ) ),
                                          'sort_key_string' => array( 'type' => 'non-unique', 'fields' => array( array( 'name' => 'sort_key_string', 'mysql:length' => 191 ) ) ),
                                          'by_id' => array( 'type' => 'non-unique', 'fields' => array( 'id' ) ) ) );
    }

    /** CS-01 */
    public function testCreateTable()
    {
        $list = self::handler()->generateTableSQLList( 'ezcontentobject_attribute', self::attribute(), array(), false );
        $this->assertSame( 'CREATE TABLE IF NOT EXISTS "exp_cassandra_schema" (table_name text PRIMARY KEY, definition text, created timestamp)', $list[0] );
        $this->assertSame( 'CREATE TABLE IF NOT EXISTS "exp_cassandra_sequence" (name text PRIMARY KEY, value bigint)', $list[1] );
        $this->assertSame( "CREATE TABLE IF NOT EXISTS \"ezcontentobject_attribute\" (\n  \"id\" int,\n  \"version\" int,\n  \"contentobject_id\" int,\n  \"data_text\" text,\n"
                         . "  \"data_float\" double,\n  \"sort_key_string\" text,\n  PRIMARY KEY ((\"id\"), \"version\")\n)", $list[2] );
        $this->assertSame( 'CREATE INDEX IF NOT EXISTS "ezcontentobject_attribute_co_id_ver" ON "ezcontentobject_attribute" ("contentobject_id")', $list[3] );
        $this->assertSame( 'CREATE INDEX IF NOT EXISTS "ezcontentobject_attribute__sort_key_string" ON "ezcontentobject_attribute" ("sort_key_string")', $list[4] );
        $this->assertCount( 6, $list, 'no index on the partition key' );
        $this->assertStringStartsWith( 'REPLACE INTO "exp_cassandra_schema" (table_name, definition) VALUES (\'ezcontentobject_attribute\', \'{"name":"ezcontentobject_attribute"', $list[5] );
        $parsed = expCassandraDialect::parse( $list[5] );
        $definition = json_decode( $parsed['rows'][0]['definition']['v'], true );
        $this->assertSame( 'auto_increment', $definition['fields']['id']['type'] );
        $this->assertSame( '', $definition['fields']['sort_key_string']['default'] );
        $this->assertStringEndsWith( ";\n", self::handler()->generateTableSchema( 'ezcontentobject_attribute', self::attribute(), array() ) );
        $this->assertSame( array( 'partition' => array( 'id' ), 'clustering' => array( 'version' ), 'row_id' => false ), expCassandraSchema::keyOf( self::attribute() ) );
    }

    /** CS-02 */
    public function testWithoutKeyAndNames()
    {
        $list = self::handler()->generateTableSQLList( 'log', array( 'fields' => array( 'msg' => array( 'type' => 'longtext' ) ), 'indexes' => array() ), array(), false );
        $this->assertSame( "CREATE TABLE IF NOT EXISTS \"log\" (\n  \"exp_row_id\" uuid,\n  \"msg\" text,\n  PRIMARY KEY ((\"exp_row_id\"))\n)", $list[1] );
        $this->assertCount( 3, $list, 'no counter table without an auto_increment' );
        $long = expCassandraSchema::indexName( 'ezcontentobject_attribute', 'a_very_long_index_name_that_goes_on_and_on' );
        $this->assertLessThanOrEqual( 48, strlen( $long ) );
        $this->assertNotSame( $long, expCassandraSchema::indexName( 'ezcontentobject_attribute', 'a_very_long_index_name_that_goes_on_and_on_2' ) );
        $this->assertSame( 'item_name', expCassandraSchema::indexName( 'item', 'item_name' ) );
        $this->assertSame( 'item__by_name', expCassandraSchema::indexName( 'item', 'by-name' ) );
    }

    /** CS-03 */
    public function testUpgradeStatements()
    {
        $h = self::handler();
        $this->assertSame( "ALTER TABLE \"t\" ADD \"n\" bigint;\n", $h->generateAddFieldSql( 't', 'n', array( 'type' => 'bigint' ), array() ) );
        $this->assertSame( "ALTER TABLE \"t\" DROP \"n\";\n", $h->generateDropFieldSql( 't', 'n', array() ) );
        $this->assertStringStartsWith( '-- t.n: Cassandra keeps the type', $h->generateAlterFieldSql( 't', 'n', array( 'type' => 'int' ), array() ) );
        $this->assertSame( "DROP TABLE IF EXISTS \"t\";\n", $h->generateDropTable( 't' ) );
        $this->assertSame( "CREATE INDEX IF NOT EXISTS \"t__i\" ON \"t\" (\"a\");\n", $h->generateAddIndexSql( 't', 'i', array( 'type' => 'non-unique', 'fields' => array( 'a', 'b' ) ), array() ) );
        $this->assertSame( "DROP INDEX IF EXISTS \"t__i\";\n", $h->generateDropIndexSql( 't', 'i', array(), array() ) );
        $this->assertSame( 'cassandra', $h->schemaType() );
        $this->assertSame( 'Apache Cassandra', $h->schemaName() );
    }

    /** CS-04 */
    public function testTableFromDescription()
    {
        $written = array( 'name' => 'item', 'columns' => array( 'id' => 'int', 'name' => 'text' ), 'partition' => array( 'id' ), 'clustering' => array(),
                          'indexes' => array(), 'auto' => 'id',
                          'definition' => array( 'fields' => array( 'name' => array( 'type' => 'varchar', 'length' => 50, 'default' => '' ), 'id' => array( 'type' => 'auto_increment', 'default' => false ) ),
                                                 'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) ) );
        $table = expCassandraSchema::tableFromDescription( $written, array( 'sort_columns' => true ) );
        $this->assertSame( array( 'id', 'name' ), array_keys( $table['fields'] ) );
        $this->assertSame( 50, $table['fields']['name']['length'] );
        $foreign = array( 'name' => 'events', 'columns' => array( 'day' => 'date', 'at' => 'timeuuid', 'kind' => 'text', 'payload' => 'map<text, text>', 'n' => 'counter' ),
                          'partition' => array( 'day' ), 'clustering' => array( 'at' => 'desc' ), 'indexes' => array( 'kind' => 'events_kind' ), 'definition' => null, 'auto' => null );
        $table = expCassandraSchema::tableFromDescription( $foreign );
        $this->assertSame( array( 'type' => 'primary', 'fields' => array( 'day', 'at' ) ), $table['indexes']['PRIMARY'] );
        $this->assertSame( array( 'type' => 'non-unique', 'fields' => array( 'kind' ) ), $table['indexes']['events_kind'] );
        $this->assertSame( array( 'varchar', 10, '1' ), array( $table['fields']['day']['type'], $table['fields']['day']['length'], $table['fields']['day']['not_null'] ) );
        $this->assertSame( 'longtext', $table['fields']['kind']['type'] );
        $this->assertSame( 'longtext', $table['fields']['payload']['type'] );
        $this->assertSame( 'bigint', $table['fields']['n']['type'] );
    }

    /** CS-05 */
    public function testDataTableStatements()
    {
        if ( !class_exists( 'expDataTableSchema' ) )
            $this->markTestSkipped( 'The kernel\'s data table classes are not loaded' );
        foreach ( array( 'cassandra', 'scylladb' ) as $engine )
        {
            $statements = expDataTableSchema::createStatements( $engine, 'ezcontentobject_attribute', self::attribute() );
            $this->assertSame( self::handler()->generateTableSQLList( 'ezcontentobject_attribute', self::attribute(), array(), false ), $statements, $engine );
        }
    }
}
?>
