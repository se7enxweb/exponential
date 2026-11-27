<?php
/**
 * The Couchbase and CouchDB drivers against live servers (expCouchTestKit::live(); each product skips without its
 * server). The same SQL on both: what the kernel's persistent classes and the data table layer write.
 *
 *  CL-01 - Tables made by the schema handler; INSERT with ids from the counter (1, 2, 3), lastSerialID(), an id given
 *          moves the counter; the defaults fill the columns left out
 *  CL-02 - SELECT: = and IN on the key, a string for a number, LIKE, BETWEEN, IS NULL, ORDER BY, LIMIT/OFFSET,
 *          COUNT, SUM, GROUP BY with HAVING, DISTINCT, a join, a LEFT JOIN whose missing side IS NULL, a sub-select
 *  CL-03 - UPDATE (by key and by a condition), a changed key, DELETE; a duplicate key is error 1062
 *  CL-04 - Transactions: a rollback leaves nothing of the writes; a commit keeps them
 *  CL-05 - The upsert of the helpers; ALTER TABLE ADD COLUMN gives the rows the default
 *  CL-06 - Text: quotes, backslashes, Unicode and 100 KB survive the round trip
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expCouchTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('couch')]
#[\PHPUnit\Framework\Attributes\Group('couch-live')]
class expCouchLiveTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    /** @var array product => driver, made once (Couchbase makes collections slowly) */
    private static $drivers = array();

    public static function products()
    {
        return array( 'couchbase' => array( 'couchbase' ), 'couchdb' => array( 'couchdb' ) );
    }

    protected function setUp(): void
    {
        $this->saved = expCouchTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        expCouchTestKit::restoreDebug( $this->saved );
    }

    /**
     * The driver of a product with the tables cl_item and cl_cat made fresh and filled.
     */
    private function db( $product )
    {
        $db = expCouchTestKit::liveDriver( $product );
        if ( is_string( $db ) )
            $this->markTestSkipped( $db );
        $class = $product === 'couchbase' ? 'expCouchbaseSchema' : 'expCouchDBSchema';
        $handler = new $class( array( 'instance' => $db ) );
        $schema = array(
            'cl_item' => array( 'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ),
                                                   'name' => array( 'type' => 'varchar', 'length' => 100, 'not_null' => '1', 'default' => '' ),
                                                   'qty' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                                                   'cat_id' => array( 'type' => 'int', 'default' => 0 ),
                                                   'body' => array( 'type' => 'longtext', 'default' => false ) ),
                                'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                                                    'cl_item_cat' => array( 'type' => 'non-unique', 'fields' => array( 'cat_id' ) ) ) ),
            'cl_cat' => array( 'fields' => array( 'id' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                                                  'label' => array( 'type' => 'varchar', 'length' => 50, 'default' => '' ) ),
                               'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) ),
        );
        $existing = (array)$db->relationList();
        foreach ( $schema as $table => $definition )
        {
            if ( in_array( $table, $existing, true ) )
            {
                // emptied instead of made again: a Couchbase collection and its indexes take seconds to make
                $this->assertTrue( $db->query( "DELETE FROM $table" ), $db->errorMessage() );
                continue;
            }
            foreach ( $handler->generateTableSQLList( $table, $definition, array(), false ) as $sql )
                $this->assertTrue( $db->query( $sql ), $db->errorMessage() );
        }
        if ( $product === 'couchbase' )
            $db->send( 'DELETE FROM `exp_couchbase_sequence` USE KEYS "cl_item"', array( 'report' => false ) );
        else
        {
            list( $status, $doc ) = $db->client()->request( 'GET', '/exp_live/exp_couchdb_sequence%3Acl_item' );
            if ( $status === 200 )
                $db->client()->request( 'DELETE', '/exp_live/exp_couchdb_sequence%3Acl_item?rev=' . rawurlencode( $doc['_rev'] ) );
        }
        $this->assertTrue( $db->query( "INSERT INTO cl_cat (id, label) VALUES (1, 'fruit'), (2, 'veg')" ), $db->errorMessage() );
        $this->assertTrue( $db->query( "INSERT INTO cl_item (name, qty, cat_id) VALUES ('apple', 3, 1), ('pear', 5, 1), ('leek', 1, 2)" ), $db->errorMessage() );
        return $db;
    }

    /** CL-01 */
    #[\PHPUnit\Framework\Attributes\DataProvider('products')]
    public function testSchemaAndIds( $product )
    {
        $db = $this->db( $product );
        $this->assertSame( 3, (int)$db->lastSerialID( 'cl_item', 'id' ) );
        $this->assertSame( array( 1, 2, 3 ), array_map( 'intval', $db->arrayQuery( 'SELECT id FROM cl_item ORDER BY id', array( 'column' => 'id' ) ) ) );
        $this->assertTrue( $db->query( "INSERT INTO cl_item (id, name) VALUES (10, 'ten')" ) );
        $this->assertTrue( $db->query( "INSERT INTO cl_item (name) VALUES ('eleven')" ) );
        $this->assertSame( 11, (int)$db->lastSerialID( 'cl_item' ) );
        $row = $db->arrayQuery( 'SELECT qty, cat_id, body FROM cl_item WHERE id = 11' );
        $this->assertSame( array( 0, 0, null ), array( (int)$row[0]['qty'], (int)$row[0]['cat_id'], $row[0]['body'] ) );
        $this->assertContains( 'cl_item', $db->relationList() );
    }

    /** CL-02 */
    #[\PHPUnit\Framework\Attributes\DataProvider('products')]
    public function testSelect( $product )
    {
        $db = $this->db( $product );
        $names = function ( $sql ) use ( $db ) {
            $rows = $db->arrayQuery( $sql );
            $this->assertIsArray( $rows, $sql . ': ' . $db->errorMessage() );
            return array_column( $rows, 'name' );
        };
        $this->assertSame( array( 'pear' ), $names( "SELECT name FROM cl_item WHERE id = '2'" ) );
        $this->assertSame( array( 'apple', 'leek' ), $names( 'SELECT name FROM cl_item WHERE id IN (1, 3) ORDER BY name' ) );
        $this->assertSame( array( 'pear' ), $names( "SELECT name FROM cl_item WHERE name LIKE 'pe%'" ) );
        $this->assertSame( array( 'apple', 'pear' ), $names( 'SELECT name FROM cl_item WHERE qty BETWEEN 2 AND 6 ORDER BY qty' ) );
        $this->assertSame( array(), $names( 'SELECT name FROM cl_item WHERE body IS NOT NULL' ) );
        $this->assertSame( array( 'leek' ), $names( 'SELECT name FROM cl_item ORDER BY name LIMIT 1 OFFSET 1' ) );
        $agg = $db->arrayQuery( 'SELECT COUNT(*) AS n, SUM(qty) AS s FROM cl_item' );
        $this->assertSame( array( 3, 9 ), array( (int)$agg[0]['n'], (int)$agg[0]['s'] ) );
        $groups = $db->arrayQuery( 'SELECT cat_id, COUNT(*) AS n FROM cl_item GROUP BY cat_id HAVING COUNT(*) > 1' );
        $this->assertSame( array( array( 1, 2 ) ), array_map( function ( $r ) { return array( (int)$r['cat_id'], (int)$r['n'] ); }, $groups ) );
        $this->assertCount( 2, $db->arrayQuery( 'SELECT DISTINCT cat_id FROM cl_item' ) );
        $joined = $db->arrayQuery( 'SELECT i.name, c.label FROM cl_item i, cl_cat c WHERE c.id = i.cat_id AND i.qty > 2 ORDER BY i.name' );
        $this->assertSame( array( 'apple fruit', 'pear fruit' ), array_map( function ( $r ) { return $r['name'] . ' ' . $r['label']; }, $joined ) );
        $this->assertTrue( $db->query( "INSERT INTO cl_item (name, cat_id) VALUES ('orphan', 9)" ) );
        $this->assertSame( array( 'orphan' ), $names( 'SELECT i.name FROM cl_item i LEFT JOIN cl_cat c ON c.id = i.cat_id WHERE c.label IS NULL' ) );
        $this->assertSame( array( 'pear' ), $names( 'SELECT name FROM cl_item WHERE qty = (SELECT MAX(qty) FROM cl_item)' ) );
        $this->assertSame( array( 'leek' ), $names( 'SELECT name FROM cl_item WHERE cat_id IN (SELECT id FROM cl_cat WHERE label = \'veg\')' ) );
    }

    /** CL-03 */
    #[\PHPUnit\Framework\Attributes\DataProvider('products')]
    public function testWrites( $product )
    {
        $db = $this->db( $product );
        $this->assertTrue( $db->query( "UPDATE cl_item SET qty = qty + 10 WHERE id = '2'" ) );
        $this->assertTrue( $db->query( 'UPDATE cl_item SET body = \'b\' WHERE cat_id = 1' ) );
        $rows = $db->arrayQuery( 'SELECT id, qty, body FROM cl_item ORDER BY id' );
        $this->assertSame( array( '3 b', '15 b', '1 ' ), array_map( function ( $r ) { return (int)$r['qty'] . ' ' . $r['body']; }, $rows ) );
        $this->assertTrue( $db->query( 'UPDATE cl_item SET id = 20 WHERE id = 3' ), $db->errorMessage() );
        $this->assertSame( array( 'leek' ), array_column( $db->arrayQuery( 'SELECT name FROM cl_item WHERE id = 20' ), 'name' ) );
        $this->assertSame( array(), $db->arrayQuery( 'SELECT name FROM cl_item WHERE id = 3' ) );
        $this->assertTrue( $db->query( 'DELETE FROM cl_item WHERE qty < 4' ) );
        $this->assertSame( array( 2 ), array_map( 'intval', $db->arrayQuery( 'SELECT id FROM cl_item', array( 'column' => 'id' ) ) ) );
        $this->assertFalse( $db->query( "INSERT INTO cl_item (id, name) VALUES (2, 'dup')" ) );
        $this->assertSame( 1062, $db->errorNumber(), $db->errorMessage() );
    }

    /** CL-04 */
    #[\PHPUnit\Framework\Attributes\DataProvider('products')]
    public function testTransactions( $product )
    {
        $db = $this->db( $product );
        $db->begin();
        $this->assertTrue( $db->query( "INSERT INTO cl_item (name) VALUES ('tx')" ) );
        $this->assertTrue( $db->query( "UPDATE cl_item SET name = 'changed' WHERE id = 1" ) );
        $this->assertTrue( $db->query( 'DELETE FROM cl_item WHERE id = 3' ) );
        $this->assertSame( array( 'changed', 'pear', 'tx' ), array_column( $db->arrayQuery( 'SELECT name FROM cl_item ORDER BY name' ), 'name' ) );
        $db->rollback();
        $this->assertSame( array( 'apple', 'leek', 'pear' ), array_column( $db->arrayQuery( 'SELECT name FROM cl_item ORDER BY name' ), 'name' ) );
        $db->begin();
        $this->assertTrue( $db->query( "UPDATE cl_item SET qty = 7 WHERE id = 1" ) );
        $db->commit();
        $this->assertSame( 7, (int)$db->arrayQuery( 'SELECT qty FROM cl_item WHERE id = 1' )[0]['qty'] );
    }

    /** CL-05 */
    #[\PHPUnit\Framework\Attributes\DataProvider('products')]
    public function testUpsertAndAlter( $product )
    {
        $db = $this->db( $product );
        $this->assertTrue( $db->query( $db->sqlUpsert( 'cl_cat', array( 'id' => 1, 'label' => 'fruits' ), array( 'id' ) ) ), $db->errorMessage() );
        $this->assertTrue( $db->query( $db->sqlUpsert( 'cl_cat', array( 'id' => 3, 'label' => 'nuts' ), array( 'id' ) ) ), $db->errorMessage() );
        $this->assertSame( array( 'fruits', 'veg', 'nuts' ), array_column( $db->arrayQuery( 'SELECT label FROM cl_cat ORDER BY id' ), 'label' ) );
        $this->assertTrue( $db->query( "ALTER TABLE cl_cat ADD COLUMN note varchar(20) DEFAULT 'n'" ), $db->errorMessage() );
        $this->assertSame( array( 'n', 'n', 'n' ), array_column( $db->arrayQuery( 'SELECT note FROM cl_cat ORDER BY id' ), 'note' ) );
        $this->assertTrue( $db->query( 'ALTER TABLE cl_cat DROP COLUMN note' ), $db->errorMessage() );
    }

    /** CL-06 */
    #[\PHPUnit\Framework\Attributes\DataProvider('products')]
    public function testText( $product )
    {
        $db = $this->db( $product );
        $text = "it's \"quoted\" \\ back\\slash, Ünïcødé ✓ " . str_repeat( 'x', 100000 );
        $this->assertTrue( $db->query( "INSERT INTO cl_item (name, body) VALUES ('t', '" . $db->escapeString( $text ) . "')" ), $db->errorMessage() );
        $this->assertSame( $text, $db->arrayQuery( "SELECT body FROM cl_item WHERE name = 't'" )[0]['body'] );
        $this->assertSame( 1, count( $db->arrayQuery( "SELECT id FROM cl_item WHERE body = '" . $db->escapeString( $text ) . "'" ) ) );
    }
}
?>
