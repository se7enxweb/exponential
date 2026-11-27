<?php
/**
 * expCassandraDB against a real Apache Cassandra or ScyllaDB (skipped without one, see expCassandraTestKit::live()).
 * expCassandraLiveTest and expScyllaDBLiveTest run these on each product.
 *
 *  CL-01 - The schema handler: a table with an auto_increment key, defaults, an index; the definition read back as it
 *          was written; new ids 1, 2, 3 and lastSerialID(); an id given explicitly moves the counter past it
 *  CL-02 - SELECT: a key lookup, an index lookup, a filter the server does (ALLOW FILTERING) and one done in PHP (OR,
 *          LIKE, IN on another column, functions), ORDER BY, LIMIT and OFFSET, DISTINCT, COUNT, MIN, MAX, SUM
 *  CL-03 - INSERT: a duplicate key refused (InsertCheck read and lwt), INSERT IGNORE, REPLACE, the three forms of
 *          sqlUpsert(); a table without a primary key gets exp_row_id
 *  CL-04 - UPDATE and DELETE by key, by another column, with arithmetic, changing the key; DELETE without WHERE
 *  CL-05 - Transactions: rollback() undoes inserts, updates and deletes of the transaction; commit() keeps them
 *  CL-06 - What is refused: a join, GROUP BY, a sub-select, UNION, temporary tables, more rows than ScanLimit; the
 *          error names it and nothing is answered wrongly
 *  CL-07 - Two connections take ids of the same counter: no id twice
 *  CL-08 - Text: Unicode, quotes, a backslash and 200 KB survive a write and a WHERE; types of a table made outside
 *          Exponential (uuid, timestamp, boolean, list) read back as text
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expCassandraTestKit.php';

abstract class expCassandraLiveTestBase extends PHPUnit\Framework\TestCase
{
    /** @var expCassandraDB */
    protected $db;

    /** @var expCassandraDB[] */
    protected $others = array();

    protected $saved;

    /** @return string cassandra or scylladb */
    abstract protected function product();

    /** The table of the tests */
    protected static function itemDefinition()
    {
        return array( 'name' => 'cl_item',
                      'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ),
                                         'name' => array( 'type' => 'varchar', 'length' => 100, 'not_null' => '1', 'default' => '' ),
                                         'value' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                                         'flags' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                                         'price' => array( 'type' => 'decimal', 'length' => '10,2', 'not_null' => '0', 'default' => null ),
                                         'body' => array( 'type' => 'longtext', 'not_null' => '0', 'default' => null ),
                                         'section' => array( 'type' => 'varchar', 'length' => 50, 'not_null' => '1', 'default' => 'standard' ) ),
                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                                          'cl_item_name' => array( 'type' => 'non-unique', 'fields' => array( 'name' ) ) ) );
    }

    protected function setUp(): void
    {
        $this->saved = expCassandraTestKit::quietDebug();
        $db = expCassandraTestKit::liveDriver( $this->product() );
        if ( !$db instanceof expCassandraDB )
            $this->markTestSkipped( $db );
        $this->db = $db;
        foreach ( array( 'cl_item', 'cl_pair', 'cl_nokey', 'cl_foreign', 'cl_text' ) as $table )
            $this->assertTrue( $this->db->query( "DROP TABLE IF EXISTS $table" ), (string)$this->db->errorMessage() );
        $h = new expCassandraSchema( array( 'instance' => $this->db ) );
        foreach ( $h->generateTableSQLList( 'cl_item', self::itemDefinition(), array(), false ) as $statement )
            $this->assertTrue( $this->db->query( $statement ), $statement . ': ' . (string)$this->db->errorMessage() );
        $pair = array( 'fields' => array( 'object_id' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                                          'version' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                                          'title' => array( 'type' => 'varchar', 'length' => 255, 'not_null' => '1', 'default' => '' ) ),
                       'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'object_id', 'version' ) ) ) );
        foreach ( $h->generateTableSQLList( 'cl_pair', $pair, array(), false ) as $statement )
            $this->assertTrue( $this->db->query( $statement ), $statement . ': ' . (string)$this->db->errorMessage() );
        $this->db->forgetTables();
    }

    protected function tearDown(): void
    {
        foreach ( $this->others as $other )
            $other->close();
        if ( $this->db instanceof expCassandraDB )
            $this->db->close();
        expCassandraTestKit::restoreDebug( $this->saved );
    }

    protected function insertItems()
    {
        $this->assertTrue( $this->db->query( "INSERT INTO cl_item (name, value, flags, price) VALUES ('apple', 10, 1, '1.50')" ), $this->db->errorMessage() );
        $this->assertTrue( $this->db->query( "INSERT INTO cl_item (name, value, flags, price) VALUES ('banana', 20, 3, '0.25')" ), $this->db->errorMessage() );
        $this->assertTrue( $this->db->query( "INSERT INTO cl_item (name, value, flags, section) VALUES ('cherry', 30, 4, 'fruit')" ), $this->db->errorMessage() );
        $this->assertTrue( $this->db->query( "INSERT INTO cl_item (id, name, value, flags) VALUES (0, 'date', 20, 6)" ), $this->db->errorMessage() );
    }

    /** CL-01 */
    public function testSchemaAndNewIDs()
    {
        $h = new expCassandraSchema( array( 'instance' => $this->db ) );
        $schema = $h->schema( array( 'format' => 'generic' ) );
        $this->assertArrayHasKey( 'cl_item', $schema );
        $this->assertSame( 'auto_increment', $schema['cl_item']['fields']['id']['type'] );
        $this->assertSame( 'standard', $schema['cl_item']['fields']['section']['default'] );
        $this->assertArrayNotHasKey( expCassandraDB::SEQUENCE_TABLE, $schema );
        $this->assertContains( 'cl_item', $this->db->eZTableList() ? array_keys( $this->db->eZTableList() ) : array() );
        $this->assertContains( 'cl_item_name', $this->db->relationList( eZDBInterface::RELATION_INDEX ) );

        $this->insertItems();
        $this->assertSame( 4, $this->db->lastSerialID( 'cl_item', 'id' ) );
        $rows = $this->db->arrayQuery( 'SELECT id, name, section FROM cl_item WHERE id = 3' );
        $this->assertSame( array( array( 'id' => '3', 'name' => 'cherry', 'section' => 'fruit' ) ), $rows );
        $rows = $this->db->arrayQuery( 'SELECT section FROM cl_item WHERE id = 1' );
        $this->assertSame( 'standard', $rows[0]['section'], 'the default of the definition' );

        $this->assertTrue( $this->db->query( "INSERT INTO cl_item (id, name) VALUES (100, 'explicit')" ) );
        $this->assertTrue( $this->db->query( "INSERT INTO cl_item (name) VALUES ('after')" ) );
        $this->assertSame( 101, $this->db->lastSerialID( 'cl_item' ) );
    }

    /** CL-02 */
    public function testSelect()
    {
        $this->insertItems();
        $before = $this->db->Plans;
        $this->assertSame( 'banana', $this->db->arrayQuery( "SELECT name FROM cl_item WHERE id = '2'" )[0]['name'], 'a key lookup with the value as text' );
        $this->assertSame( $before['key'] + 1, $this->db->Plans['key'] );
        $this->assertSame( array( '3' ), array_column( $this->db->arrayQuery( "SELECT id FROM cl_item WHERE name = 'cherry'" ), 'id' ) );
        $this->assertSame( $before['index'] + 1, $this->db->Plans['index'] );
        $this->assertSame( array( '2', '4' ), array_column( $this->db->arrayQuery( 'SELECT id FROM cl_item WHERE value = 20 ORDER BY id' ), 'id' ) );
        $this->assertSame( $before['filter'] + 1, $this->db->Plans['filter'] );
        $this->assertSame( array( '1', '3' ), array_column( $this->db->arrayQuery( "SELECT id FROM cl_item WHERE name = 'apple' OR value = 30 ORDER BY id" ), 'id' ) );
        $this->assertSame( array( 'apple', 'banana', 'date' ), array_column( $this->db->arrayQuery( "SELECT name FROM cl_item WHERE name LIKE '%a%e%' OR name LIKE 'b%' ORDER BY name" ), 'name' ) );
        $this->assertSame( array( 'cherry', 'date' ), array_column( $this->db->arrayQuery( 'SELECT name FROM cl_item WHERE value IN (20, 30) AND id > 2 ORDER BY value DESC, id DESC' ), 'name' ) );
        $this->assertSame( array( '1', '2', '4' ), array_column( $this->db->arrayQuery( 'SELECT id FROM cl_item WHERE id IN (1, 2, 4, 99) ORDER BY id' ), 'id' ) );
        $this->assertSame( array( '3' ), array_column( $this->db->arrayQuery( 'SELECT id FROM cl_item WHERE ( flags & 2 ) = 0 AND value >= 10 ORDER BY id', array( 'offset' => 1, 'limit' => 2 ) ), 'id' ) );
        $this->assertSame( array( 'BANANA' ), array_column( $this->db->arrayQuery( "SELECT UPPER(name) AS n FROM cl_item WHERE LOWER(name) = 'banana'" ), 'n' ) );
        $this->assertSame( array( array( 'n' => '4', 'lo' => '10', 'hi' => '30', 's' => '80' ) ),
                           $this->db->arrayQuery( 'SELECT COUNT(*) AS n, MIN(value) AS lo, MAX(value) AS hi, SUM(value) AS s FROM cl_item' ) );
        $this->assertSame( '2', $this->db->arrayQuery( 'SELECT COUNT(*) AS n FROM cl_item WHERE value = 20' )[0]['n'] );
        $this->assertSame( array( '10', '20', '30' ), array_column( $this->db->arrayQuery( 'SELECT DISTINCT value FROM cl_item ORDER BY value' ), 'value' ) );
        $this->assertSame( array( 'apple', 'banana' ), array_column( $this->db->arrayQuery( 'SELECT name FROM cl_item ORDER BY name LIMIT 2' ), 'name' ) );
        $this->assertSame( array( 'cherry' ), array_column( $this->db->arrayQuery( 'SELECT name FROM cl_item ORDER BY name LIMIT 1 OFFSET 2' ), 'name' ) );
        $this->assertSame( array( 2 => 'cherry' ), $this->db->arrayQuery( 'SELECT name FROM cl_item ORDER BY name', array( 'offset' => 2, 'limit' => 1, 'column' => 'name' ) ) );
        $this->assertSame( array( '1.50' ), array_column( $this->db->arrayQuery( 'SELECT price FROM cl_item WHERE id = 1' ), 'price' ) );
        $this->assertSame( array(), $this->db->arrayQuery( 'SELECT * FROM cl_item WHERE id IN ()' ) );
        $this->assertSame( array( array( 'one' => '1' ) ), $this->db->arrayQuery( 'SELECT 1 AS one' ) );
        $this->assertFalse( $this->db->arrayQuery( 'SELECT nothing FROM cl_item' ) );
        $this->assertSame( 1054, $this->db->errorNumber() );
        $this->assertFalse( $this->db->arrayQuery( 'SELECT * FROM cl_missing' ) );
        $this->assertSame( 1146, $this->db->errorNumber() );
        // a composite key: the first column is the partition key
        $this->assertTrue( $this->db->query( "INSERT INTO cl_pair (object_id, version, title) VALUES (7, 1, 'one'), (7, 2, 'two'), (8, 1, 'other')" ) );
        $before = $this->db->Plans['key'];
        $this->assertSame( array( 'two', 'one' ), array_column( $this->db->arrayQuery( 'SELECT title FROM cl_pair WHERE object_id = 7 ORDER BY version DESC' ), 'title' ) );
        $this->assertSame( array( 'two' ), array_column( $this->db->arrayQuery( 'SELECT title FROM cl_pair WHERE object_id = 7 AND version = 2' ), 'title' ) );
        $this->assertSame( $before + 2, $this->db->Plans['key'] );
    }

    /** CL-03 */
    public function testInsertForms()
    {
        $this->insertItems();
        $this->assertFalse( $this->db->query( "INSERT INTO cl_item (id, name) VALUES (1, 'again')" ) );
        $this->assertSame( 1062, $this->db->errorNumber() );
        $this->assertSame( 'apple', $this->db->arrayQuery( 'SELECT name FROM cl_item WHERE id = 1' )[0]['name'] );
        $lwt = expCassandraTestKit::liveDriver( $this->product(), array( 'InsertCheck' => 'lwt' ) );
        $this->others[] = $lwt;
        $this->assertFalse( $lwt->query( "INSERT INTO cl_pair (object_id, version, title) VALUES (1, 1, 'a')" ) && $lwt->query( "INSERT INTO cl_pair (object_id, version, title) VALUES (1, 1, 'b')" ) );
        $this->assertSame( 'duplicate', $lwt->ErrorCategory );
        $this->assertTrue( $this->db->query( "INSERT IGNORE INTO cl_item (id, name) VALUES (1, 'ignored')" ) );
        $this->assertSame( 'apple', $this->db->arrayQuery( 'SELECT name FROM cl_item WHERE id = 1' )[0]['name'] );
        $this->assertTrue( $this->db->query( "REPLACE INTO cl_item (id, name, value) VALUES (1, 'replaced', 11)" ) );
        $this->assertSame( array( 'replaced', '11' ), array_values( $this->db->arrayQuery( 'SELECT name, value FROM cl_item WHERE id = 1' )[0] ) );
        // sqlUpsert(): every column, insert only, some columns
        $this->assertTrue( $this->db->query( $this->db->sqlUpsert( 'cl_pair', array( 'object_id' => 9, 'version' => 1, 'title' => 'new' ), array( 'object_id', 'version' ) ) ) );
        $this->assertTrue( $this->db->query( $this->db->sqlUpsert( 'cl_pair', array( 'object_id' => 9, 'version' => 1, 'title' => 'kept?' ), array( 'object_id', 'version' ), array() ) ) );
        $this->assertSame( 'new', $this->db->arrayQuery( 'SELECT title FROM cl_pair WHERE object_id = 9 AND version = 1' )[0]['title'] );
        $this->assertTrue( $this->db->query( $this->db->sqlUpsert( 'cl_item', array( 'id' => 2, 'name' => 'renamed', 'value' => 999 ), array( 'id' ), array( 'name' ) ) ) );
        $this->assertSame( array( 'renamed', '20' ), array_values( $this->db->arrayQuery( 'SELECT name, value FROM cl_item WHERE id = 2' )[0] ) );
        // no primary key: exp_row_id
        $h = new expCassandraSchema( array( 'instance' => $this->db ) );
        foreach ( $h->generateTableSQLList( 'cl_nokey', array( 'fields' => array( 'word' => array( 'type' => 'varchar', 'length' => 20 ) ), 'indexes' => array() ), array(), false ) as $statement )
            $this->assertTrue( $this->db->query( $statement ) );
        $this->assertTrue( $this->db->query( "INSERT INTO cl_nokey (word) VALUES ('x'), ('x')" ) );
        $this->assertSame( '2', $this->db->arrayQuery( "SELECT COUNT(*) AS n FROM cl_nokey WHERE word = 'x'" )[0]['n'] );
    }

    /** CL-04 */
    public function testUpdateAndDelete()
    {
        $this->insertItems();
        $this->assertTrue( $this->db->query( "UPDATE cl_item SET name = 'APPLE', value = value + 5 WHERE id = 1" ) );
        $this->assertSame( array( 'APPLE', '15' ), array_values( $this->db->arrayQuery( 'SELECT name, value FROM cl_item WHERE id = 1' )[0] ) );
        $this->assertTrue( $this->db->query( 'UPDATE cl_item SET flags = flags | 8 WHERE value = 20' ) );
        $this->assertSame( 2, $this->db->AffectedRows );
        $this->assertSame( array( '11', '14' ), array_column( $this->db->arrayQuery( 'SELECT flags FROM cl_item WHERE value = 20 ORDER BY id' ), 'flags' ) );
        $this->assertTrue( $this->db->query( "UPDATE cl_item SET value = 1 WHERE id = 999" ) );
        $this->assertSame( 0, $this->db->AffectedRows, 'an UPDATE of a missing row makes none (CQL alone would)' );
        $this->assertSame( array(), $this->db->arrayQuery( 'SELECT id FROM cl_item WHERE id = 999' ) );
        // a changed key moves the row
        $this->assertTrue( $this->db->query( "INSERT INTO cl_pair (object_id, version, title) VALUES (7, 1, 'one')" ) );
        $this->assertTrue( $this->db->query( 'UPDATE cl_pair SET version = 5 WHERE object_id = 7 AND version = 1' ) );
        $this->assertSame( array( '5' ), array_column( $this->db->arrayQuery( 'SELECT version FROM cl_pair WHERE object_id = 7' ), 'version' ) );
        $this->assertTrue( $this->db->query( 'DELETE FROM cl_item WHERE id = 1' ) );
        $this->assertTrue( $this->db->query( "DELETE FROM cl_item WHERE name LIKE 'b%' OR flags = 4" ) );
        $this->assertSame( array( 'date' ), array_column( $this->db->arrayQuery( 'SELECT name FROM cl_item' ), 'name' ) );
        $this->assertTrue( $this->db->query( 'DELETE FROM cl_item' ) );
        $this->assertSame( '0', $this->db->arrayQuery( 'SELECT COUNT(*) AS n FROM cl_item' )[0]['n'] );
    }

    /** CL-05 */
    public function testTransactions()
    {
        $this->insertItems();
        $this->db->begin();
        $this->assertTrue( $this->db->query( "INSERT INTO cl_item (name, value) VALUES ('new', 1)" ) );
        $newID = $this->db->lastSerialID( 'cl_item' );
        $this->assertTrue( $this->db->query( "UPDATE cl_item SET name = 'changed' WHERE id = 1" ) );
        $this->assertTrue( $this->db->query( 'DELETE FROM cl_item WHERE id = 2' ) );
        $this->assertTrue( $this->db->query( "REPLACE INTO cl_item (id, name, value) VALUES (3, 'replaced', 0)" ) );
        $this->assertTrue( $this->db->rollback() );
        $rows = $this->db->arrayQuery( 'SELECT id, name, value FROM cl_item ORDER BY id' );
        $this->assertSame( array( '1', '2', '3', '4' ), array_column( $rows, 'id' ), "the new row $newID is gone, the deleted one back" );
        $this->assertSame( array( 'apple', 'banana', 'cherry', 'date' ), array_column( $rows, 'name' ) );
        $this->assertSame( '30', $rows[2]['value'] );
        $this->db->begin();
        $this->assertTrue( $this->db->query( "UPDATE cl_item SET name = 'kept' WHERE id = 4" ) );
        $this->assertTrue( $this->db->commit() );
        $this->assertSame( 'kept', $this->db->arrayQuery( 'SELECT name FROM cl_item WHERE id = 4' )[0]['name'] );
    }

    /** CL-06 */
    public function testRefusals()
    {
        $this->insertItems();
        foreach ( array( 'SELECT a.id FROM cl_item a JOIN cl_pair p ON p.object_id = a.id' => 'join',
                         'SELECT value, COUNT(*) FROM cl_item GROUP BY value' => 'GROUP BY',
                         'SELECT id FROM cl_item WHERE id IN (SELECT object_id FROM cl_pair)' => 'sub-select',
                         'SELECT id FROM cl_item UNION SELECT object_id FROM cl_pair' => 'UNION' ) as $sql => $what )
        {
            $this->assertFalse( $this->db->arrayQuery( $sql ), $sql );
            $this->assertStringContainsString( $what, $this->db->errorMessage(), $sql );
            $this->assertSame( 'unsupported', $this->db->ErrorCategory );
        }
        $this->assertFalse( $this->db->createTempTable( 'CREATE TEMPORARY TABLE t (id int)' ) );
        $small = expCassandraTestKit::liveDriver( $this->product(), array( 'ScanLimit' => 2 ) );
        $this->others[] = $small;
        $this->assertFalse( $small->arrayQuery( "SELECT id FROM cl_item WHERE name LIKE '%e%'" ) );
        $this->assertSame( 'scan_limit', $small->ErrorCategory );
        $this->assertCount( 1, $small->arrayQuery( 'SELECT id FROM cl_item WHERE id = 1' ), 'a key lookup reads one row' );
        $small->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $this->expectException( eZDBException::class );
        $small->arrayQuery( 'SELECT id FROM cl_item a, cl_pair b' );
    }

    /** CL-07 */
    public function testConcurrentIDs()
    {
        $second = expCassandraTestKit::liveDriver( $this->product() );
        $this->others[] = $second;
        $ids = array();
        for ( $i = 0; $i < 10; $i++ )
        {
            $this->assertTrue( $this->db->query( "INSERT INTO cl_item (name) VALUES ('a$i')" ) );
            $ids[] = $this->db->lastSerialID( 'cl_item' );
            $this->assertTrue( $second->query( "INSERT INTO cl_item (name) VALUES ('b$i')" ) );
            $ids[] = $second->lastSerialID( 'cl_item' );
        }
        $this->assertCount( 20, array_unique( $ids ) );
        $this->assertSame( '20', $this->db->arrayQuery( 'SELECT COUNT(*) AS n FROM cl_item' )[0]['n'] );
    }

    /** CL-08 */
    public function testTextAndForeignTypes()
    {
        $text = "Grüße – \"quoted\" 'single' back\\slash 🙂";
        $this->assertTrue( $this->db->query( "INSERT INTO cl_item (name, body) VALUES ('" . $this->db->escapeString( $text ) . "', '" . str_repeat( 'x', 200000 ) . "')" ), $this->db->errorMessage() );
        $id = $this->db->lastSerialID( 'cl_item' );
        $rows = $this->db->arrayQuery( "SELECT id, body FROM cl_item WHERE name = '" . $this->db->escapeString( $text ) . "'" );
        $this->assertSame( (string)$id, $rows[0]['id'] );
        $this->assertSame( 200000, strlen( $rows[0]['body'] ) );
        $this->assertTrue( $this->db->query( 'CREATE TABLE cl_foreign (id uuid PRIMARY KEY, at timestamp, ok boolean, tags list<text>, amount varint)' ), $this->db->errorMessage() );
        // CQL of its own (literals SQL does not have): through cql()
        $this->assertNotFalse( $this->db->cql( "INSERT INTO cl_foreign (id, at, ok, tags, amount) VALUES (5c3b0d2e-8f4c-4d4e-9b8a-1a2b3c4d5e6f, '2026-11-26 10:00:00+0000', true, ['a', 'b'], 12345678901234567890)" ), $this->db->errorMessage() );
        $row = $this->db->arrayQuery( 'SELECT * FROM cl_foreign' )[0];
        $this->assertSame( '5c3b0d2e-8f4c-4d4e-9b8a-1a2b3c4d5e6f', $row['id'] );
        $this->assertSame( '1', $row['ok'] );
        $this->assertSame( '["a","b"]', $row['tags'] );
        $this->assertSame( '12345678901234567890', $row['amount'] );
        $this->assertStringStartsWith( '2026-11-26', $row['at'] );
        $h = new expCassandraSchema( array( 'instance' => $this->db ) );
        $schema = $h->schema( array( 'format' => 'generic' ) );
        $this->assertEquals( array( 'type' => 'char', 'length' => 36, 'not_null' => '1', 'default' => '' ), array_intersect_key( $schema['cl_foreign']['fields']['id'], array_flip( array( 'type', 'length', 'not_null', 'default' ) ) ) );
        $this->assertSame( 'longtext', $schema['cl_foreign']['fields']['tags']['type'] );
        $this->assertSame( array( 'id' ), $schema['cl_foreign']['indexes']['PRIMARY']['fields'] );
    }
}
?>
