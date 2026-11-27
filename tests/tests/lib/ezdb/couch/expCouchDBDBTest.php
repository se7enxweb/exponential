<?php
/**
 * The CouchDB driver (expCouchDBDB) over a small CouchDB in memory (expCouchDBFake), without a server:
 *
 *  CC-01 - Connecting: the welcome and the database asked; a missing database made with AutoCreateDatabase, refused
 *          without; a name CouchDB does not take refused
 *  CC-02 - CREATE TABLE from the schema handler: the definition kept, a Mango index on exp_table and per index
 *  CC-03 - INSERT: documents "table:key" with exp_table and the defaults, ids from the counter, lastSerialID(); a
 *          duplicate key is error 1062; INSERT OR IGNORE skips it
 *  CC-04 - SELECT on the in-memory copy: = conditions sent to Mango (only those), joins, GROUP BY, sub-selects
 *  CC-05 - UPDATE and DELETE with their revisions; a changed key moves the document; a conflict (a document changed
 *          by someone else) fails the statement
 *  CC-06 - Rollback: inserted documents deleted, changed and deleted ones written back
 *  CC-07 - The upsert of the helpers (SQLite's ON CONFLICT) on the copy, and REPLACE
 *  CC-08 - ALTER TABLE ADD / DROP COLUMN, DROP TABLE, relationList(), TRUNCATE
 *  CC-09 - ScanLimit: a statement that needs more documents is refused, never answered from a part
 *  CC-10 - pushdown(), memoryTableSQL(), documentId() (pure)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expCouchTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('couch')]
class expCouchDBDBTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    /** @var expCouchDBFake */
    private $couch;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The CouchDB driver needs the sqlite3 extension' );
        $this->saved = expCouchTestKit::quietDebug();
        $this->couch = new expCouchDBFake();
        $this->couch->dbs['shop'] = array();
    }

    protected function tearDown(): void
    {
        expCouchTestKit::restoreDebug( $this->saved );
    }

    private function db( array $settings = array(), $database = 'shop' )
    {
        return new expCouchDBDB( expCouchTestKit::parameters( array( 'database' => $database, 'couchdb_transport' => $this->couch, 'couchdb' => $settings ) ) );
    }

    /** A database with the table item (id counted up, name, qty, cat) */
    private function withItems()
    {
        $db = $this->db();
        $handler = new expCouchDBSchema( array( 'instance' => $db ) );
        $definition = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ), 'name' => array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => '' ),
                                                'qty' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ), 'cat' => array( 'type' => 'int', 'default' => 1 ) ),
                             'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ), 'item_name' => array( 'type' => 'non-unique', 'fields' => array( 'name' ) ) ) );
        foreach ( $handler->generateTableSQLList( 'item', $definition, array(), false ) as $sql )
            $this->assertTrue( $db->query( $sql ), $db->errorMessage() );
        $this->assertTrue( $db->query( "INSERT INTO item (name, qty) VALUES ('apple', 3), ('pear', 5), ('it''s', 1)" ), $db->errorMessage() );
        return $db;
    }

    /** CC-01 */
    public function testConnect()
    {
        $db = $this->db();
        $this->assertTrue( $db->isConnected() );
        $this->assertSame( array( 'couchdb', 'sqlite' ), array( $db->databaseName(), $db->sqlHelperDialect() ) );
        $this->assertSame( '3.4.3', $db->databaseServerVersion()['string'] );
        $made = $this->db( array( 'AutoCreateDatabase' => 'enabled' ), 'fresh' );
        $this->assertTrue( $made->isConnected() );
        $this->assertArrayHasKey( 'fresh', $this->couch->dbs );
        foreach ( array( 'missing' => 'has no database', 'Bad Name' => 'no CouchDB database name' ) as $name => $why )
        {
            try
            {
                $this->db( array(), $name );
                $this->fail( $name );
            }
            catch ( eZDBNoConnectionException $e )
            {
                $this->assertStringContainsString( $why, $e->getMessage() );
            }
        }
    }

    /** CC-02, CC-03 */
    public function testCreateAndInsert()
    {
        $db = $this->withItems();
        $this->assertSame( array( 'item' ), $db->relationList() );
        $this->assertArrayHasKey( 'exp_table', $this->couch->indexes['shop'] );
        $this->assertSame( array( 'exp_table', 'name' ), $this->couch->indexes['shop']['item__item_name'] );
        $this->assertSame( 'item', $this->couch->dbs['shop']['exp_couchdb_schema:item']['name'] );
        $doc = $this->couch->dbs['shop']['item:2'];
        $this->assertSame( array( 'exp_table' => 'item', 'id' => 2, 'name' => 'pear', 'qty' => 5, 'cat' => 1 ), array_diff_key( $doc, array( '_id' => 1, '_rev' => 1 ) ) );
        $this->assertSame( 3, $this->couch->dbs['shop']['exp_couchdb_sequence:item']['v'] );
        $this->assertSame( 3, $db->lastSerialID( 'item', 'id' ) );
        $this->assertFalse( $db->query( "INSERT INTO item (id, name) VALUES (2, 'dup')" ) );
        $this->assertSame( 1062, $db->errorNumber() );
        $this->assertTrue( $db->query( "INSERT OR IGNORE INTO item (id, name) VALUES (2, 'dup')" ) );
        $this->assertSame( 'pear', $this->couch->dbs['shop']['item:2']['name'] );
        // an id given moves the counter past it
        $this->assertTrue( $db->query( "INSERT INTO item (id, name) VALUES (10, 'ten')" ) );
        $this->assertTrue( $db->query( "INSERT INTO item (name) VALUES ('eleven')" ) );
        $this->assertSame( 11, $db->lastSerialID( 'item' ) );
    }

    /** CC-04 */
    public function testSelect()
    {
        $db = $this->withItems();
        $this->couch->log = array();
        $rows = $db->arrayQuery( "SELECT id, name, qty FROM item WHERE name = 'pear' AND qty > 1" );
        $this->assertSame( array( array( 'id' => 2, 'name' => 'pear', 'qty' => 5 ) ), $rows );
        $this->assertSame( array( 'POST', '/shop/_find' ), $this->couch->log[0] );
        // the whole key: the document by its id, no Mango
        $this->couch->log = array();
        $this->assertSame( array( array( 'name' => 'pear' ) ), $db->arrayQuery( "SELECT name FROM item WHERE id = '2'" ) );
        $this->assertSame( array( array( 'POST', '/shop/_all_docs?include_docs=true' ) ), $this->couch->log );
        $this->assertSame( array(), $db->arrayQuery( "SELECT name FROM item WHERE id = 2 AND name = 'apple'" ) );
        $this->assertSame( array(), $db->arrayQuery( 'SELECT name FROM item WHERE id = 99' ) );
        $this->assertSame( array( 'exp_table' => 'item', 'name' => 'pear' ), expCouchDBDB::pushdown( expCouchSQLLexer::tokenize( "SELECT id FROM item WHERE name = 'pear' AND qty > 1", false ),
                                                                                                    'item', $db->tableEntry( 'item' ) ) );
        $this->assertSame( array( array( 'n' => 3, 's' => 9 ) ), $db->arrayQuery( 'SELECT COUNT(*) AS n, SUM(qty) AS s FROM item' ) );
        $this->assertSame( array( 'apple', 'pear' ), $db->arrayQuery( 'SELECT name FROM item WHERE qty > 2 ORDER BY name', array( 'column' => 'name' ) ) );
        $this->assertSame( array( 1 => 'pear' ), $db->arrayQuery( 'SELECT name FROM item ORDER BY id DESC', array( 'column' => 0, 'offset' => 1, 'limit' => 1 ) ) );
        $this->assertSame( array( array( 'cat' => 1, 'n' => 3 ) ), $db->arrayQuery( 'SELECT cat, COUNT(*) AS n FROM item GROUP BY cat HAVING COUNT(*) > 1' ) );
        $this->assertSame( array( array( 'name' => 'pear' ) ), $db->arrayQuery( 'SELECT name FROM item WHERE qty = (SELECT MAX(qty) FROM item)' ) );
        $this->assertTrue( $db->query( 'CREATE TABLE cat (id int, label varchar(20), PRIMARY KEY (id))' ) );
        $this->assertTrue( $db->query( "INSERT INTO cat (id, label) VALUES (1, 'fruit')" ) );
        $this->assertSame( array( array( 'name' => 'apple', 'label' => 'fruit' ) ),
                           $db->arrayQuery( "SELECT i.name, c.label FROM item i JOIN cat c ON c.id = i.cat WHERE i.name = 'apple'" ) );
        // a string compared with a number column is not sent to Mango (SQLite converts it)
        $this->assertSame( array( 'exp_table' => 'item' ), expCouchDBDB::pushdown( expCouchSQLLexer::tokenize( "SELECT id FROM item WHERE qty = 'x'", false ), 'item', $db->tableEntry( 'item' ) ) );
        $this->assertSame( array( array( 'id' => 1 ) ), $db->arrayQuery( "SELECT id FROM item WHERE qty = '3'" ) );
    }

    /** CC-05 */
    public function testUpdateDeleteAndConflicts()
    {
        $db = $this->withItems();
        $this->assertTrue( $db->query( "UPDATE item SET qty = qty + 10 WHERE id = '2'" ) );
        $this->assertSame( 15, $this->couch->dbs['shop']['item:2']['qty'] );
        $this->assertStringStartsWith( '2-', $this->couch->dbs['shop']['item:2']['_rev'] );
        $this->assertTrue( $db->query( 'UPDATE item SET id = 20 WHERE id = 2' ) );
        $this->assertArrayHasKey( 'item:20', $this->couch->dbs['shop'] );
        $this->assertTrue( $this->couch->dbs['shop']['item:2']['_deleted'] );
        $this->assertTrue( $db->query( 'DELETE FROM item WHERE qty < 2' ) );
        $this->assertSame( array( 'apple', 'pear' ), $db->arrayQuery( 'SELECT name FROM item ORDER BY name', array( 'column' => 'name' ) ) );
        // someone else changes the document between the read and the write
        $couch = $this->couch;
        $db2 = new expCouchDBDB( expCouchTestKit::parameters( array( 'database' => 'shop', 'couchdb_transport' => function ( $method, $path, $body ) use ( $couch ) {
            $answer = $couch( $method, $path, $body );
            // the read of item 1 by its id
            if ( $path === '/shop/_all_docs?include_docs=true' && $method === 'POST' && $body['keys'] === array( 'item:1' ) )
            {
                $doc = $couch->dbs['shop']['item:1'];
                $doc['qty'] = 99;
                $couch( 'PUT', '/shop/item%3A1', $doc );
            }
            return $answer;
        } ) ) );
        $this->assertFalse( $db2->query( 'UPDATE item SET qty = 0 WHERE id = 1' ) );
        $this->assertStringContainsString( 'conflict', $db2->errorMessage() );
        $this->assertSame( 99, $this->couch->dbs['shop']['item:1']['qty'] );
    }

    /** CC-06 */
    public function testRollback()
    {
        $db = $this->withItems();
        $db->begin();
        $this->assertTrue( $db->query( "INSERT INTO item (name, qty) VALUES ('tx', 1)" ) );
        $this->assertTrue( $db->query( "UPDATE item SET name = 'changed' WHERE id = 1" ) );
        $this->assertTrue( $db->query( 'DELETE FROM item WHERE id = 3' ) );
        $db->rollback();
        $this->assertSame( array( array( 'id' => 1, 'name' => 'apple' ), array( 'id' => 2, 'name' => 'pear' ), array( 'id' => 3, 'name' => "it's" ) ),
                           $db->arrayQuery( 'SELECT id, name FROM item ORDER BY id' ) );
        // a committed transaction stays
        $db->begin();
        $db->query( "UPDATE item SET qty = 7 WHERE id = 1" );
        $db->commit();
        $this->assertSame( 7, $this->couch->dbs['shop']['item:1']['qty'] );
    }

    /** CC-07 */
    public function testUpsertAndReplace()
    {
        $db = $this->withItems();
        $this->assertTrue( $db->query( $db->sqlUpsert( 'item', array( 'id' => 1, 'name' => 'apple', 'qty' => 99 ), array( 'id' ) ) ), $db->errorMessage() );
        $this->assertSame( 99, $this->couch->dbs['shop']['item:1']['qty'] );
        $this->assertTrue( $db->query( $db->sqlUpsert( 'item', array( 'id' => 50, 'name' => 'new', 'qty' => 1 ), array( 'id' ) ) ) );
        $this->assertSame( 'new', $this->couch->dbs['shop']['item:50']['name'] );
        $this->assertTrue( $db->query( "REPLACE INTO item (id, name, qty) VALUES (2, 'replaced', 0)" ), $db->errorMessage() );
        $this->assertSame( array( 'replaced', 0, 1 ), array( $this->couch->dbs['shop']['item:2']['name'], $this->couch->dbs['shop']['item:2']['qty'], $this->couch->dbs['shop']['item:2']['cat'] ) );
    }

    /** CC-08 */
    public function testAlterDropTruncate()
    {
        $db = $this->withItems();
        $this->assertTrue( $db->query( "ALTER TABLE item ADD COLUMN note varchar(20) DEFAULT 'n'" ) );
        $this->assertSame( 'n', $this->couch->dbs['shop']['item:1']['note'] );
        $this->assertSame( array( array( 'note' => 'n' ) ), $db->arrayQuery( 'SELECT note FROM item WHERE id = 1' ) );
        $this->assertTrue( $db->query( 'ALTER TABLE item DROP COLUMN note' ) );
        $this->assertArrayNotHasKey( 'note', $this->couch->dbs['shop']['item:1'] );
        $this->assertTrue( $db->query( 'TRUNCATE TABLE item' ) );
        $this->assertSame( array( array( 'n' => 0 ) ), $db->arrayQuery( 'SELECT COUNT(*) AS n FROM item' ) );
        $this->assertTrue( $db->query( 'DROP TABLE item' ) );
        $this->assertSame( array(), $db->relationList() );
        $this->assertArrayNotHasKey( 'item__item_name', $this->couch->indexes['shop'] );
        $this->assertFalse( $db->query( "INSERT INTO item (name) VALUES ('x')" ) );
        $this->assertSame( 1146, $db->errorNumber() );
        $this->assertFalse( $db->query( 'CREATE TABLE bad (_x int)' ) );
    }

    /** CC-09 */
    public function testScanLimit()
    {
        $db = $this->withItems();
        $limited = $this->db( array( 'ScanLimit' => 2, 'BatchSize' => 1 ) );
        $this->assertFalse( $limited->arrayQuery( 'SELECT COUNT(*) FROM item' ) );
        $this->assertStringContainsString( 'ScanLimit', $limited->errorMessage() );
        $this->assertSame( array( array( 'name' => 'pear' ) ), $limited->arrayQuery( "SELECT name FROM item WHERE name = 'pear'" ) );
        unset( $db );
    }

    /** CC-10 */
    public function testPureParts()
    {
        $entry = expCouchbaseDB::catalogueEntry( array( 'fields' => array( 'id' => array( 'type' => 'auto_increment' ), 'k' => array( 'type' => 'varchar', 'default' => "a'b" ),
                                                                         'n' => array( 'type' => 'float', 'default' => 1.5 ) ),
                                                        'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id', 'k' ) ),
                                                                            'u' => array( 'type' => 'unique', 'fields' => array( 'n' ) ) ) ) );
        $this->assertSame( 'CREATE TABLE "t" ("exp_id" TEXT, "exp_rev" TEXT, "id" INT, "k" TEXT DEFAULT \'a\'\'b\', "n" REAL DEFAULT 1.5, PRIMARY KEY ("id", "k"));'
                           . ' CREATE UNIQUE INDEX "t__u" ON "t" ("n");',
                           expCouchDBDB::memoryTableSQL( 't', $entry, array( 'indexes' => array( 'u' => array( 'type' => 'unique', 'fields' => array( 'n' ) ) ) ) ) );
        $this->assertSame( 't:["1","x"]', expCouchDBDB::documentId( 't', $entry, array( 'id' => 1, 'k' => 'x' ) ) );
        $this->assertStringStartsWith( 't:', expCouchDBDB::documentId( 't', $entry, array( 'id' => null, 'k' => 'x' ) ) );
        $this->assertSame( array( 'exp_table' => 't', 'k' => 'a' ), expCouchDBDB::pushdown( expCouchSQLLexer::tokenize( "SELECT * FROM t WHERE t.k = 'a' ORDER BY id", false ), 't', $entry ) );
        $this->assertSame( array( 'exp_table' => 't' ), expCouchDBDB::pushdown( expCouchSQLLexer::tokenize( "SELECT * FROM t WHERE k = 'a' OR k = 'b'", false ), 't', $entry ) );
        $this->assertSame( array( 'exp_table' => 't', '$and' => array( array( 'k' => 'a' ), array( 'k' => 'b' ) ) ),
                           expCouchDBDB::pushdown( expCouchSQLLexer::tokenize( "SELECT * FROM t WHERE k = 'a' AND k = 'b'", false ), 't', $entry ) );
        $this->assertSame( 'error: reason (HTTP 409)', expCouchDBDB::reason( 409, array( 'error' => 'error', 'reason' => 'reason' ) ) );
        $this->assertTrue( expCouchDBClient::validDatabaseName( 'a1_$()+-/b' ) );
        $this->assertFalse( expCouchDBClient::validDatabaseName( '_users' ) );
    }
}
?>
