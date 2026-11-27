<?php
/**
 * The import from a Cassandra or ScyllaDB keyspace without a server (expCassandraReader over a driver whose transport
 * answers from rules; the import engine expAccessImport).
 *
 *  CR-01 - CQL types in the import's vocabulary: integers, bigint and counter, varint and decimal, double, boolean,
 *          text (255 as a key, long otherwise), uuid, timestamp and date, blob, collections
 *  CR-02 - Values: a timestamp as 'Y-m-d H:i:s', a boolean as 1/0, a collection as JSON, a blob as binary (base64)
 *  CR-03 - A table read through the driver: its key and indexes, the schema and names the import makes of it
 *  CR-04 - Rows page by page; a closed connection is refused; the command names its source
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/datatable/expDataTableTestLoader.php';
require_once dirname( __DIR__, 3 ) . '/lib/ezdb/cassandra/expCassandraTestKit.php';
foreach ( array( 'expaccessreader', 'expaccessimport' ) as $file )
    require_once expDataTableTestLoader::root() . '/kernel/classes/msaccess/' . $file . '.php';
require_once expDataTableTestLoader::root() . '/kernel/classes/cassandra/expcassandrareader.php';

#[\PHPUnit\Framework\Attributes\Group('kernel')]
#[\PHPUnit\Framework\Attributes\Group('cassandra')]
class expCassandraReaderTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        $this->saved = expCassandraTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        expCassandraTestKit::restoreDebug( $this->saved );
    }

    /** CR-01 */
    public function testFields()
    {
        $types = array();
        foreach ( array( 'int', 'smallint', 'bigint', 'counter', 'varint', 'decimal', 'double', 'float', 'boolean', 'text', 'ascii', 'uuid',
                         'timeuuid', 'timestamp', 'date', 'time', 'inet', 'blob', 'list<text>', 'frozen<map<text, int>>' ) as $type )
            $types[$type] = expCassandraReader::field( $type )['type'];
        $this->assertSame( array( 'int' => 'integer', 'smallint' => 'integer', 'bigint' => 'bigint', 'counter' => 'bigint', 'varint' => 'decimal',
                                  'decimal' => 'decimal', 'double' => 'double', 'float' => 'double', 'boolean' => 'boolean', 'text' => 'text',
                                  'ascii' => 'text', 'uuid' => 'guid', 'timeuuid' => 'guid', 'timestamp' => 'datetime', 'date' => 'datetime',
                                  'time' => 'text', 'inet' => 'text', 'blob' => 'binary', 'list<text>' => 'memo', 'frozen<map<text, int>>' => 'memo' ), $types );
        $this->assertSame( 255, expCassandraReader::field( 'text', true )['length'] );
        $this->assertTrue( expCassandraReader::field( 'text', true )['not_null'] );
        $this->assertSame( 0, expCassandraReader::field( 'text' )['length'] );
        $this->assertSame( array( 'length' => 255, 'type' => 'varchar', 'not_null' => '0', 'default' => null ),
                           expAccessImport::dbaField( expCassandraReader::field( 'text', true ) + array( 'not_null' => true ) ) );
        $this->assertSame( 'longtext', expAccessImport::dbaField( expCassandraReader::field( 'map<text, int>' ) )['type'] );
        $this->assertSame( 'int', expAccessImport::dbaField( expCassandraReader::field( 'boolean' ) )['type'] );
    }

    /** CR-02 */
    public function testValues()
    {
        $this->assertSame( '2026-11-26 10:00:00', expCassandraReader::value( '2026-11-26 10:00:00.000+0000', 'timestamp' ) );
        $this->assertSame( '2026-11-26 00:00:00', expCassandraReader::value( '2026-11-26', 'date' ) );
        $this->assertSame( '1970-01-02 00:00:00', expCassandraReader::value( 86400000, 'timestamp' ) );
        $this->assertSame( 1, expCassandraReader::value( true, 'boolean' ) );
        $this->assertSame( 0, expCassandraReader::value( false, 'boolean' ) );
        $this->assertSame( '["a","b"]', expCassandraReader::value( array( 'a', 'b' ), 'list<text>' ) );
        $this->assertSame( '{"x":1}', expCassandraReader::value( array( 'x' => 1 ), 'map<text, int>' ) );
        $blob = expCassandraReader::value( "\xca\xfe", 'blob' );
        $this->assertInstanceOf( 'expAccessBinary', $blob );
        $this->assertSame( base64_encode( "\xca\xfe" ), (string)$blob );
        $this->assertNull( expCassandraReader::value( null, 'text' ) );
        $this->assertSame( 'Grüße', expCassandraReader::value( 'Grüße', 'text' ) );
    }

    private static function driver()
    {
        $t = new expCassandraFakeTransport();
        $t->on( '/FROM system_schema\.keyspaces WHERE keyspace_name = \?/', array( array( 'keyspace_name' => 'shop' ) ) );
        $t->on( '/SELECT table_name AS name FROM system_schema\.tables/', array( array( 'name' => 'Orders' ), array( 'name' => 'exp_cassandra_sequence' ) ) );
        $t->describe( 'orders', array( 'customer' => array( 'text', 'partition_key', 0 ), 'at' => array( 'timeuuid', 'clustering', 0, 'desc' ),
                                       'total' => array( 'decimal', 'regular' ), 'paid' => array( 'boolean', 'regular' ),
                                       'items' => array( 'list<text>', 'regular' ), 'select' => array( 'text', 'regular' ) ),
                      array( 'paid' => 'orders_paid' ) );
        $pages = array( array( 'rows' => array( array( 'customer' => 'c1', 'at' => 'u1', 'total' => '9.50', 'paid' => true, 'items' => array( 'a' ), 'select' => 'x' ) ), 'paging_state' => 'p2' ),
                        array( 'rows' => array( array( 'customer' => 'c2', 'at' => 'u2', 'total' => null, 'paid' => false, 'items' => null, 'select' => null ) ), 'paging_state' => null ) );
        $t->on( '/^SELECT \* FROM "orders"$/', function ( $cql, $values, $options ) use ( $pages ) {
            return empty( $options['paging_state'] ) ? $pages[0] : $pages[1];
        } );
        return array( expCassandraTestKit::driver( $t, array(), array( 'database' => 'shop', 'server' => 'n1,n2' ) ), $t );
    }

    /** CR-03 */
    public function testTable()
    {
        list( $db, $t ) = self::driver();
        $reader = new expCassandraReader( $db );
        $this->assertSame( 'cassandra://n1,n2/shop', $reader->file() );
        $this->assertSame( 'cassandra', $reader->name() );
        $this->assertSame( array( 'Orders' ), $reader->tables(), 'the driver\'s own tables are not listed' );
        $description = $reader->table( 'orders' );
        $this->assertSame( array( 'customer', 'at' ), $description['primary'] );
        $this->assertSame( array( 'orders_paid' => array( 'unique' => false, 'fields' => array( 'paid' ) ) ), $description['indexes'] );
        $t->on( '/SELECT table_name AS name FROM system_schema\.tables/', array( array( 'name' => 'orders' ) ) );
        $schema = expAccessImport::schema( $reader, array(), 'cs_' );
        $table = $schema['schema']['cs_orders'];
        // the key columns first, the others by name (as system_schema lists them)
        $this->assertSame( array( 'customer', 'at', 'items', 'paid', 'select_', 'total' ), array_keys( $table['fields'] ) );
        $this->assertSame( '1', $table['fields']['customer']['not_null'], 'a key column is NOT NULL' );
        $this->assertSame( array( 'varchar', 255 ), array( $table['fields']['customer']['type'], $table['fields']['customer']['length'] ) );
        $this->assertSame( 'char', $table['fields']['at']['type'] );
        $this->assertSame( array( 'customer', 'at' ), $table['indexes']['PRIMARY']['fields'] );
        $this->assertSame( array( 'paid' ), array_values( array_filter( $table['indexes'], function ( $i ) { return $i['type'] === 'non-unique'; } ) )[0]['fields'] );
    }

    /** CR-04 */
    public function testRowsAndRefusals()
    {
        list( $db, $t ) = self::driver();
        $reader = new expCassandraReader( $db );
        $rows = array();
        $this->assertSame( 2, $reader->rows( 'orders', function ( $row ) use ( &$rows ) { $rows[] = $row; } ) );
        $this->assertSame( array( 'c1', 'u1', '9.50', 1, '["a"]', 'x' ), array_values( $rows[0] ) );
        $this->assertSame( 0, $rows[1]['paid'] );
        $this->assertCount( 2, $t->sent( '/^SELECT \* FROM "orders"$/' ), 'two pages' );
        $db->close();
        try
        {
            new expCassandraReader( $db );
            $this->fail( 'a closed connection was read' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'not open', $e->getMessage() );
        }
        if ( class_exists( 'Exponential\\Command\\Kernel\\Expcassandraimport' )
             || ( is_file( expDataTableTestLoader::root() . '/kernel/private/classes/runnable/command.php' ) && class_exists( 'Exponential\\Runnable\\Command' ) ) )
        {
            require_once expDataTableTestLoader::root() . '/kernel/private/classes/commands/expcassandraimport.php';
            try
            {
                \Exponential\Command\Kernel\Expcassandraimport::openSource( array( 'server' => 'n1' ) );
                $this->fail( 'a source without a keyspace was opened' );
            }
            catch ( RuntimeException $e )
            {
                $this->assertStringContainsString( '--keyspace', $e->getMessage() );
            }
            try
            {
                \Exponential\Command\Kernel\Expcassandraimport::openSource( array( 'server' => 'n1', 'keyspace' => 'k', 'engine' => 'mysql' ) );
                $this->fail( 'another engine was accepted' );
            }
            catch ( RuntimeException $e )
            {
                $this->assertStringContainsString( '--engine', $e->getMessage() );
            }
        }
    }
}
?>
