<?php
/**
 * The readers of the imports (expCouchbaseReader, expCouchDBReader), without a server:
 *
 *  CR-01 - infer(): integers, big integers, numbers, booleans, short and long text, objects and arrays; the key
 *          first; fields left out
 *  CR-02 - fromDefinition(): the .dba types in the import's vocabulary, the key and the indexes
 *  CR-03 - value(): booleans as 1/0, objects as JSON, numbers and text as their column takes them
 *  CR-04 - CouchDB, one table: every document but the design documents, _id the key, the fields inferred
 *  CR-05 - CouchDB by a type field: one table per value, its documents only
 *  CR-06 - CouchDB written by the driver: its tables with their definitions
 *  CR-07 - Couchbase: collections, a sampled collection with _id, rows page by page in key order
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 3 ) . '/lib/ezdb/couch/expCouchTestKit.php';
foreach ( array( 'kernel/classes/msaccess/expaccessreader.php', 'kernel/classes/couch/expcouchbasereader.php', 'kernel/classes/couch/expcouchdbreader.php' ) as $file )
    require_once dirname( __DIR__, 5 ) . '/' . $file;

#[\PHPUnit\Framework\Attributes\Group('kernel')]
#[\PHPUnit\Framework\Attributes\Group('couch')]
class expCouchReadersTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        $this->saved = expCouchTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        expCouchTestKit::restoreDebug( $this->saved );
    }

    /** CR-01 */
    public function testInfer()
    {
        $d = expCouchbaseReader::infer( array(
            array( '_id' => 'a', 'n' => 1, 'big' => 5000000000, 'x' => 1.5, 'ok' => true, 's' => 'short', 'l' => str_repeat( 'x', 300 ), 'o' => array( 'k' => 1 ), 'skip' => 1 ),
            array( '_id' => 'b', 'n' => 2, 'x' => 2, 'ok' => false, 's' => null, 'mixed' => 1 ),
            array( '_id' => 'c', 'mixed' => 'text' ),
        ), '_id', array( 'skip' ) );
        $types = array();
        foreach ( $d['fields'] as $name => $field )
            $types[$name] = $field['type'] . ( $field['length'] ? '(' . $field['length'] . ')' : '' );
        $this->assertSame( array( '_id' => 'text(255)', 'n' => 'integer', 'big' => 'bigint', 'x' => 'double', 'ok' => 'boolean', 's' => 'text(255)', 'l' => 'memo',
                                  'o' => 'memo', 'mixed' => 'text(255)' ), $types );
        $this->assertSame( array( '_id' ), $d['primary'] );
        $this->assertTrue( $d['fields']['_id']['not_null'] );
        $this->assertSame( array(), expCouchbaseReader::infer( array(), null )['fields'] );
    }

    /** CR-02 */
    public function testFromDefinition()
    {
        $d = expCouchbaseReader::fromDefinition( array( 'fields' => array( 'id' => array( 'type' => 'auto_increment' ), 'name' => array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1' ),
                                                                          'body' => array( 'type' => 'longtext' ), 'price' => array( 'type' => 'decimal', 'length' => 10, 'scale' => 2 ),
                                                                          'w' => array( 'type' => 'float' ), 'b' => array( 'type' => 'bigint' ) ),
                                                        'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                                                                            'n' => array( 'type' => 'unique', 'fields' => array( array( 'name' => 'name', 'length' => 10 ) ) ) ) ) );
        $this->assertSame( array( 'counter', 'text', 'memo', 'decimal', 'double', 'bigint' ), array_column( $d['fields'], 'type' ) );
        $this->assertTrue( $d['fields']['id']['auto'] );
        $this->assertSame( array( 50, true ), array( $d['fields']['name']['length'], $d['fields']['name']['not_null'] ) );
        $this->assertSame( array( 10, 2 ), array( $d['fields']['price']['length'], $d['fields']['price']['scale'] ) );
        $this->assertSame( array( 'id' ), $d['primary'] );
        $this->assertSame( array( 'n' => array( 'unique' => true, 'fields' => array( 'name' ) ) ), $d['indexes'] );
    }

    /** CR-03 */
    public function testValue()
    {
        $this->assertSame( 1, expCouchbaseReader::value( true, 'boolean' ) );
        $this->assertSame( '{"a":[1,2]}', expCouchbaseReader::value( array( 'a' => array( 1, 2 ) ), 'memo' ) );
        $this->assertSame( 5, expCouchbaseReader::value( '5', 'integer' ) );
        $this->assertNull( expCouchbaseReader::value( 'x', 'integer' ) );
        $this->assertSame( '1.5', expCouchbaseReader::value( 1.5, 'text' ) );
        $this->assertNull( expCouchbaseReader::value( null, 'text' ) );
        $this->assertSame( 'order_items', expCouchDBReader::tableName( 'Order Items' ) );
        $this->assertSame( 't_9lives', expCouchDBReader::tableName( '9lives' ) );
    }

    private function couch( array $docs )
    {
        $fake = new expCouchDBFake();
        $fake->dbs['shop'] = array();
        foreach ( $docs as $doc )
            $fake( 'PUT', '/shop/' . rawurlencode( $doc['_id'] ), $doc );
        return array( $fake, new expCouchDBClient( 'h', 5984, '', '', array( 'transport' => $fake ) ) );
    }

    /** CR-04 */
    public function testCouchDBOneTable()
    {
        list( , $client ) = $this->couch( array( array( '_id' => 'b', 'name' => 'B', 'n' => 2 ), array( '_id' => 'a', 'name' => 'A', 'n' => 1, 'tags' => array( 'x' ) ),
                                                 array( '_id' => '_design/x', 'views' => array() ) ) );
        $reader = new expCouchDBReader( $client, 'shop' );
        $this->assertSame( array( 'shop' ), $reader->tables() );
        $this->assertSame( array( 'doc_id', 'name', 'n', 'tags' ), array_keys( $reader->table( 'shop' )['fields'] ) );
        $rows = array();
        $this->assertSame( 2, $reader->rows( 'shop', function ( $row ) use ( &$rows ) { $rows[] = $row; } ) );
        $this->assertSame( array( array( 'doc_id' => 'a', 'name' => 'A', 'n' => 1, 'tags' => '["x"]' ), array( 'doc_id' => 'b', 'name' => 'B', 'n' => 2, 'tags' => null ) ), $rows );
        $this->assertSame( 'couchdb://h:5984/shop', $reader->file() );
        $this->assertSame( 'Apache CouchDB 3.4.3', $reader->format() );
    }

    /** CR-05 */
    public function testCouchDBByType()
    {
        list( , $client ) = $this->couch( array( array( '_id' => 'o1', 'type' => 'order', 'total' => 10 ), array( '_id' => 'c1', 'type' => 'Customer', 'name' => 'Ann' ),
                                                 array( '_id' => 'o2', 'type' => 'order', 'total' => 12.5 ), array( '_id' => 'x', 'other' => 1 ) ) );
        $reader = new expCouchDBReader( $client, 'shop', 'type' );
        $this->assertSame( array( 'customer', 'order' ), $reader->tables() );
        $this->assertSame( array( 'doc_id', 'total' ), array_keys( $reader->table( 'order' )['fields'] ) );
        $this->assertSame( 'double', $reader->table( 'order' )['fields']['total']['type'] );
        $rows = array();
        $reader->rows( 'order', function ( $row ) use ( &$rows ) { $rows[] = $row; } );
        $this->assertSame( array( 'o1', 'o2' ), array_column( $rows, 'doc_id' ) );
        $this->expectException( RuntimeException::class );
        $reader->table( 'nothing' );
    }

    /** CR-06 */
    public function testCouchDBOfTheDriver()
    {
        list( $fake, $client ) = $this->couch( array() );
        $db = new expCouchDBDB( expCouchTestKit::parameters( array( 'database' => 'shop', 'couchdb_transport' => $fake ) ) );
        $this->assertTrue( $db->query( "CREATE TABLE item (id int AUTO_INCREMENT, name varchar(20) DEFAULT '', PRIMARY KEY (id))" ) );
        $this->assertTrue( $db->query( "INSERT INTO item (name) VALUES ('a'), ('b')" ) );
        $reader = new expCouchDBReader( $client, 'shop' );
        $this->assertSame( array( 'item' ), $reader->tables() );
        $this->assertSame( array( 'id', 'name' ), array_keys( $reader->table( 'item' )['fields'] ) );
        $this->assertSame( 'counter', $reader->table( 'item' )['fields']['id']['type'] );
        $rows = array();
        $reader->rows( 'item', function ( $row ) use ( &$rows ) { $rows[] = $row; } );
        $this->assertSame( array( array( 'id' => 1, 'name' => 'a' ), array( 'id' => 2, 'name' => 'b' ) ), $rows );
    }

    /** CR-07 */
    public function testCouchbase()
    {
        $service = new expCouchbaseFakeService();
        $service->answer = function ( $request ) {
            $s = $request['statement'];
            if ( strpos( $s, 'system:scopes' ) !== false )
                return expCouchbaseFakeService::ok( array( 'inv' ) );
            if ( strpos( $s, 'system:keyspaces' ) !== false )
                return expCouchbaseFakeService::ok( array( 'airline', 'exp_couchbase_schema' ) );
            if ( strpos( $s, 'SELECT RAW t FROM `airline`' ) === 0 )
                return expCouchbaseFakeService::ok( array( array( 'name' => 'A', 'id' => 1 ), array( 'name' => 'B', 'country' => 'X' ) ) );
            if ( strpos( $s, 'SELECT META(t).id AS `_exp_key`' ) === 0 )
                return expCouchbaseFakeService::ok( strpos( $s, ' > ' ) === false
                    ? array( array( '_exp_key' => 'airline_1', '_exp_doc' => array( 'name' => 'A', 'id' => 1 ) ), array( '_exp_key' => 'airline_2', '_exp_doc' => array( 'name' => 'B', 'country' => 'X' ) ) )
                    : array() );
            return null;
        };
        $db = new expCouchbaseDB( expCouchTestKit::parameters( array( 'database' => 'travel.inv', 'couchbase_service' => $service ) ) );
        $reader = new expCouchbaseReader( $db, 10 );
        $this->assertSame( array( 'airline' ), $reader->tables() );
        $this->assertSame( array( 'doc_id', 'name', 'id', 'country' ), array_keys( $reader->table( 'airline' )['fields'] ) );
        $rows = array();
        $this->assertSame( 2, $reader->rows( 'airline', function ( $row ) use ( &$rows ) { $rows[] = $row; } ) );
        $this->assertSame( array( 'doc_id' => 'airline_2', 'name' => 'B', 'id' => null, 'country' => 'X' ), $rows[1] );
        $this->assertSame( 'couchbase://travel.inv', $reader->file() );
        $this->assertStringContainsString( 'LIMIT 10', implode( "\n", $service->statements() ) );
    }
}
?>
