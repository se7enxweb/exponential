<?php
/**
 * The imports from Couchbase and CouchDB (expCouchbaseReader, expCouchDBReader with the import engine of
 * expAccessImport; the commands exp:couchbase:import and exp:couchdb:import) against the live servers of
 * expCouchTestKit (each skips without its server). The target is a SQLite file through expPDODB.
 *
 *  CI-01 - CouchDB, documents of any shape by a type field: one table per type, doc_id the key, the fields inferred
 *          (numbers, text, booleans as 1/0, objects as JSON), design documents left out; every row written
 *  CI-02 - CouchDB, a database the CouchDB driver wrote: its table with its columns, key and values
 *  CI-03 - Couchbase, a collection made outside Exponential: doc_id and the fields of the documents, nested values
 *          as JSON; 1,200 documents read page by page arrive complete
 *  CI-04 - The modes: create refuses a table with rows, append adds, replace empties first
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 2 ) . '/datatable/expDataTableTestLoader.php';
require_once dirname( __DIR__, 4 ) . '/lib/ezdb/couch/expCouchTestKit.php';
foreach ( array( 'kernel/classes/msaccess/expaccessreader.php', 'kernel/classes/msaccess/expaccessimport.php', 'kernel/classes/couch/expcouchbasereader.php',
                 'kernel/classes/couch/expcouchdbreader.php' ) as $file )
    require_once expDataTableTestLoader::root() . '/' . $file;

#[\PHPUnit\Framework\Attributes\Group('kernel')]
#[\PHPUnit\Framework\Attributes\Group('couch')]
#[\PHPUnit\Framework\Attributes\Group('couch-live')]
class expCouchImportLiveTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    /** @var string[] */
    private $files = array();

    protected function setUp(): void
    {
        $this->saved = expCouchTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        foreach ( $this->files as $file )
            if ( is_file( $file ) )
                unlink( $file );
        expKernelTableSchema::reset();
        expCouchTestKit::restoreDebug( $this->saved );
    }

    private function target()
    {
        $file = sys_get_temp_dir() . '/exp-couch-import-' . getmypid() . '-' . count( $this->files ) . '.sqlite';
        $this->files[] = $file;
        return new expPDODB( array( 'pdo_dsn' => 'sqlite:' . $file ) );
    }

    /** A CouchDB database of its own, made fresh, with the documents given */
    private function couchSource( $name, array $docs )
    {
        $db = expCouchTestKit::liveDriver( 'couchdb', array(), 'exp_live' );
        if ( is_string( $db ) )
            $this->markTestSkipped( $db );
        $client = $db->client();
        $client->request( 'DELETE', expCouchDBClient::path( $name ) );
        list( $status, ) = $client->request( 'PUT', expCouchDBClient::path( $name ) );
        $this->assertContains( $status, array( 201, 202 ) );
        if ( $docs )
        {
            list( $status, ) = $client->request( 'POST', expCouchDBClient::path( $name ) . '/_bulk_docs', array( 'docs' => $docs ) );
            $this->assertSame( 201, $status );
        }
        return $client;
    }

    /** CI-01 */
    public function testCouchDBByType()
    {
        $client = $this->couchSource( 'exp_live_src', array(
            array( '_id' => 'o1', 'type' => 'order', 'total' => 10, 'paid' => true, 'lines' => array( array( 'sku' => 'a', 'n' => 2 ) ) ),
            array( '_id' => 'o2', 'type' => 'order', 'total' => 12.5, 'paid' => false, 'note' => "it's" ),
            array( '_id' => 'c1', 'type' => 'customer', 'name' => 'Ann', 'id' => 7 ),
            array( '_id' => '_design/v', 'views' => array( 'x' => array( 'map' => 'function(d){}' ) ) ),
        ) );
        $reader = new expCouchDBReader( $client, 'exp_live_src', 'type' );
        $this->assertSame( array( 'customer', 'order' ), $reader->tables() );
        $target = $this->target();
        $result = expAccessImport::run( $reader, $target, array( 'prefix' => 'cd_' ) );
        $this->assertTrue( $result['ok'], json_encode( $result ) );
        $this->assertSame( array( 2, 2 ), array( $result['tables']['order']['read'], $result['tables']['order']['written'] ) );
        $rows = $target->arrayQuery( 'SELECT doc_id, total, paid, lines, note FROM cd_order ORDER BY doc_id' );
        $this->assertSame( array( 'o1', 'o2' ), array_column( $rows, 'doc_id' ) );
        $this->assertEquals( array( 10, 12.5 ), array_map( 'floatval', array_column( $rows, 'total' ) ) );
        $this->assertSame( array( 1, 0 ), array_map( 'intval', array_column( $rows, 'paid' ) ) );
        $this->assertSame( '[{"sku":"a","n":2}]', $rows[0]['lines'] );
        $this->assertSame( "it's", $rows[1]['note'] );
        // a field named id stays a column of its own next to doc_id
        $this->assertSame( array( array( 'doc_id' => 'c1', 'id' => 7 ) ), array_map( function ( $r ) { return array( 'doc_id' => $r['doc_id'], 'id' => (int)$r['id'] ); },
                                                                                       $target->arrayQuery( 'SELECT doc_id, id FROM cd_customer' ) ) );
    }

    /** CI-02 */
    public function testCouchDBOfTheDriver()
    {
        $client = $this->couchSource( 'exp_live_src2', array() );
        $db = expCouchTestKit::liveDriver( 'couchdb', array(), 'exp_live_src2' );
        $this->assertTrue( $db->query( "CREATE TABLE item (id int AUTO_INCREMENT, name varchar(20) NOT NULL DEFAULT '', qty int DEFAULT 0, PRIMARY KEY (id))" ) );
        $this->assertTrue( $db->query( "INSERT INTO item (name, qty) VALUES ('a', 1), ('b', 2)" ) );
        $reader = new expCouchDBReader( $client, 'exp_live_src2' );
        $target = $this->target();
        $result = expAccessImport::run( $reader, $target, array() );
        $this->assertTrue( $result['ok'], json_encode( $result ) );
        $this->assertSame( array( array( 'id' => 1, 'name' => 'a', 'qty' => 1 ), array( 'id' => 2, 'name' => 'b', 'qty' => 2 ) ),
                           array_map( function ( $r ) { return array( 'id' => (int)$r['id'], 'name' => $r['name'], 'qty' => (int)$r['qty'] ); },
                                      $target->arrayQuery( 'SELECT id, name, qty FROM item ORDER BY id' ) ) );
    }

    /** CI-03, CI-04 */
    public function testCouchbaseAndModes()
    {
        $db = expCouchTestKit::liveDriver( 'couchbase', array(), 'exp_live_src' );
        if ( is_string( $db ) )
            $this->markTestSkipped( $db );
        $this->assertTrue( $db->ensureCollection( 'airline' ), $db->errorMessage() );
        $this->assertNotFalse( $db->send( 'DELETE FROM `airline`' ) );
        $values = array();
        for ( $i = 1; $i <= 1200; $i++ )
            $values[] = '(' . expCouchbaseDialect::stringLiteral( sprintf( 'airline_%04d', $i ) ) . ', '
                      . json_encode( array( 'id' => $i, 'name' => "Air $i", 'country' => $i % 2 ? 'France' : 'Österreich', 'active' => $i % 3 === 0,
                                            'fleet' => array( 'size' => $i % 50 ) ) ) . ')';
        foreach ( array_chunk( $values, 300 ) as $chunk )
            $this->assertNotFalse( $db->send( 'INSERT INTO `airline` (KEY, VALUE) VALUES ' . implode( ', ', $chunk ) ), $db->errorMessage() );
        $reader = new expCouchbaseReader( $db );
        $this->assertContains( 'airline', $reader->tables() );
        $description = $reader->table( 'airline' );
        $this->assertSame( array( 'doc_id', 'id', 'name', 'country', 'active', 'fleet' ), array_keys( $description['fields'] ) );
        $this->assertSame( array( 'text', 'integer', 'text', 'text', 'boolean', 'memo' ), array_column( $description['fields'], 'type' ) );
        $target = $this->target();
        $result = expAccessImport::run( $reader, $target, array( 'tables' => array( 'airline' ), 'prefix' => 'cb_' ) );
        $this->assertTrue( $result['ok'], json_encode( $result ) );
        $this->assertSame( array( 1200, 1200 ), array( $result['tables']['airline']['read'], $result['tables']['airline']['written'] ) );
        $row = $target->arrayQuery( "SELECT * FROM cb_airline WHERE doc_id = 'airline_0003'" )[0];
        $this->assertSame( array( 3, 'Air 3', 'France', 1, '{"size":3}' ), array( (int)$row['id'], $row['name'], $row['country'], (int)$row['active'], $row['fleet'] ) );
        $this->assertSame( 'Österreich', $target->arrayQuery( "SELECT country FROM cb_airline WHERE doc_id = 'airline_0002'" )[0]['country'] );

        // CI-04: create refuses a table with rows, append adds, replace empties first
        $again = expAccessImport::run( $reader, $target, array( 'tables' => array( 'airline' ), 'prefix' => 'cb_' ) );
        $this->assertFalse( $again['ok'] );
        $replace = expAccessImport::run( $reader, $target, array( 'tables' => array( 'airline' ), 'prefix' => 'cb_', 'mode' => 'replace' ) );
        $this->assertTrue( $replace['ok'], json_encode( $replace ) );
        $this->assertSame( 1200, (int)$target->arrayQuery( 'SELECT COUNT(*) AS n FROM cb_airline' )[0]['n'] );
    }
}
?>
