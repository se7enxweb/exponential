<?php
/**
 * expFirebirdDB against a real Firebird 4 or 5 (skipped without one, see expFirebirdTestKit::live()).
 *
 *  FL-01 - New ids: a row without id, with NULL, with 0 and with its own id; lastSerialID() per table, per
 *          connection (another connection's insert does not change it)
 *  FL-02 - Paging and the kernel's MySQL words: limit/offset, LIMIT clauses, reserved column names, bit masks,
 *          LIKE with a backslash, CONCAT, LENGTH, SELECT without FROM, TRUNCATE, IF EXISTS
 *  FL-03 - Transactions: rollback, commit, nested; FOR UPDATE takes the row
 *  FL-04 - Text: Unicode in and out and in a WHERE, 300 KB through a parameter, quotes and backslashes
 *  FL-05 - Temporary tables of the kernel: made once, rows per connection, emptied on drop
 *  FL-06 - Errors: a duplicate key is false (an eZDBException with exceptions), its category; a lost connection is
 *          opened again
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expFirebirdTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('firebird')]
#[\PHPUnit\Framework\Attributes\Group('firebird-live')]
class expFirebirdLiveTest extends PHPUnit\Framework\TestCase
{
    /** @var expFirebirdDB */
    private $db;

    /** @var expFirebirdDB[] */
    private $others = array();

    private $saved;

    protected function setUp(): void
    {
        $this->saved = expFirebirdTestKit::quietDebug();
        $db = expFirebirdTestKit::liveDriver();
        if ( !$db instanceof expFirebirdDB )
            $this->markTestSkipped( $db );
        $this->db = $db;
        foreach ( array( 'fl_item', 'fl_text', 'fl_counter' ) as $table )
            $this->db->query( "DROP TABLE IF EXISTS $table" );
        $this->db->query( 'DROP SEQUENCE IF EXISTS S_FL_ITEM' );
        $h = new expFirebirdSchema( array( 'instance' => $this->db ) );
        $def = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ),
                                         'name' => array( 'type' => 'varchar', 'length' => 100, 'not_null' => '1', 'default' => '' ),
                                         'value' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                                         'language_mask' => array( 'type' => 'bigint', 'not_null' => '1', 'default' => 0 ) ),
                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) );
        foreach ( $h->generateTableSQLList( 'fl_item', $def, array(), false ) as $statement )
            $this->assertTrue( $this->db->query( $statement ), (string)$this->db->errorMessage() );
        $this->assertTrue( $this->db->query( 'CREATE TABLE fl_text (id INTEGER NOT NULL PRIMARY KEY, body BLOB SUB_TYPE TEXT, title VARCHAR(255))' ) );
        $this->assertTrue( $this->db->query( 'CREATE TABLE fl_counter (node_id INTEGER NOT NULL PRIMARY KEY, "COUNT" INTEGER DEFAULT 0 NOT NULL)' ) );
    }

    protected function tearDown(): void
    {
        foreach ( $this->others as $other )
            $other->close();
        if ( $this->db )
        {
            $this->db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
            while ( $this->db->isConnected() && $this->db->transactionCounter() > 0 )
                $this->db->rollback();
            foreach ( array( 'fl_item', 'fl_text', 'fl_counter' ) as $table )
                $this->db->query( "DROP TABLE IF EXISTS $table" );
            $this->db->query( 'DROP SEQUENCE IF EXISTS S_FL_ITEM' );
            $this->db->close();
        }
        expFirebirdTestKit::restoreDebug( $this->saved );
    }

    private function other()
    {
        $db = expFirebirdTestKit::liveDriver();
        $this->assertInstanceOf( 'expFirebirdDB', $db );
        $this->others[] = $db;
        return $db;
    }

    public function testNewIds()
    {
        $db = $this->db;
        $this->assertTrue( $db->query( "INSERT INTO fl_item (name) VALUES ('a')" ) );
        $this->assertSame( 1, $db->lastSerialID( 'fl_item', 'id' ) );
        $this->assertTrue( $db->query( "INSERT INTO fl_item (id, name) VALUES (NULL, 'b')" ) );
        $this->assertSame( 2, $db->lastSerialID( 'fl_item', 'id' ) );
        $this->assertTrue( $db->query( "INSERT INTO fl_item (id, name) VALUES (0, 'c')" ) );
        $this->assertSame( 3, $db->lastSerialID( 'fl_item' ) );
        $this->assertTrue( $db->query( "INSERT INTO fl_item (id, name) VALUES (40, 'd')" ) );
        $this->assertTrue( $db->query( "INSERT INTO fl_item (name) VALUES ('e')" ) );
        $this->assertSame( 41, $db->lastSerialID( 'fl_item' ) );
        // another connection's insert leaves this connection's id alone
        $other = $this->other();
        $this->assertTrue( $other->query( "INSERT INTO fl_item (name) VALUES ('f')" ) );
        $this->assertSame( 42, $other->lastSerialID( 'fl_item' ) );
        $this->assertSame( 41, $db->lastSerialID( 'fl_item' ) );
        $this->assertFalse( $db->lastSerialID( 'fl_text' ) );
        // eZPersistentObject's way: the id column left out
        $this->assertSame( array( 'a', 'b', 'c', 'd', 'e', 'f' ), array_values( $db->arrayQuery( 'SELECT name FROM fl_item ORDER BY id', array( 'column' => 'name' ) ) ) );
    }

    public function testKernelWords()
    {
        $db = $this->db;
        foreach ( array( array( 'a', 1, 6 ), array( 'b\\_x', 2, 1 ), array( 'c', 3, 4 ), array( 'd', 4, 3 ) ) as $row )
            $this->assertTrue( $db->query( "INSERT INTO fl_item (name, value, language_mask) VALUES ('" . $db->escapeString( $row[0] ) . "', {$row[1]}, {$row[2]})" ) );
        $this->assertSame( array( 2 => 'c', 3 => 'd' ), $db->arrayQuery( 'SELECT name FROM fl_item ORDER BY id', array( 'limit' => 2, 'offset' => 2, 'column' => 'name' ) ) );
        $this->assertSame( array( 'b\\_x' ), array_values( $db->arrayQuery( 'SELECT name FROM fl_item ORDER BY id LIMIT 1, 1', array( 'column' => 'name' ) ) ) );
        $this->assertSame( array( '3', '4' ), array_values( $db->arrayQuery( 'SELECT value FROM fl_item WHERE value > 2 ORDER BY value', array( 'column' => 'value' ) ) ) );
        $this->assertSame( array( 'a', 'c' ), array_values( $db->arrayQuery( 'SELECT name FROM fl_item WHERE fl_item.language_mask & 4 > 0 ORDER BY id', array( 'column' => 'name' ) ) ) );
        $this->assertSame( array( 'b\\_x' ), array_values( $db->arrayQuery( "SELECT name FROM fl_item WHERE name LIKE 'b\\\\\\_%'", array( 'column' => 'name' ) ) ) );
        $this->assertSame( 'a-1', $db->arrayQuery( "SELECT CONCAT(name, '-', value) AS c FROM fl_item WHERE id = 1" )[0]['c'] );
        $this->assertSame( '4', $db->arrayQuery( 'SELECT LENGTH(name) AS l FROM fl_item WHERE id = 2' )[0]['l'] );
        $this->assertSame( '2', $db->arrayQuery( 'SELECT 1 + 1 AS two' )[0]['two'] );
        $this->assertSame( '7', $db->arrayQuery( 'SELECT ' . $db->bitOr( '3', '4' ) . ' AS b' )[0]['b'] );
        $this->assertTrue( $db->query( 'INSERT INTO fl_counter (node_id, count) VALUES (2, 1)' ) );
        $this->assertTrue( $db->query( 'UPDATE fl_counter SET count = count + 1 WHERE node_id = 2' ) );
        $this->assertSame( '2', $db->arrayQuery( 'SELECT count FROM fl_counter WHERE node_id = 2' )[0]['count'] );
        $this->assertTrue( $db->query( 'TRUNCATE TABLE fl_counter' ) );
        $this->assertSame( '0', $db->arrayQuery( 'SELECT COUNT(*) AS count FROM fl_counter' )[0]['count'] );
        $this->assertTrue( $db->query( 'DROP TABLE IF EXISTS fl_none' ) );
        $this->assertTrue( $db->query( 'CREATE TABLE IF NOT EXISTS fl_counter (x INTEGER)' ) );
    }

    public function testTransactions()
    {
        $db = $this->db;
        $other = $this->other();
        $db->begin();
        $this->assertTrue( $db->query( "INSERT INTO fl_item (name) VALUES ('in')" ) );
        $this->assertSame( '0', $other->arrayQuery( 'SELECT COUNT(*) AS c FROM fl_item' )[0]['c'] );
        $db->rollback();
        $this->assertSame( '0', $db->arrayQuery( 'SELECT COUNT(*) AS c FROM fl_item' )[0]['c'] );
        $db->begin();
        $db->begin();
        $this->assertTrue( $db->query( "INSERT INTO fl_item (name) VALUES ('kept')" ) );
        $db->commit();
        $this->assertSame( '0', $other->arrayQuery( 'SELECT COUNT(*) AS c FROM fl_item' )[0]['c'] );
        $db->commit();
        $this->assertSame( '1', $other->arrayQuery( 'SELECT COUNT(*) AS c FROM fl_item' )[0]['c'] );
        // FOR UPDATE: the row is this transaction's until it ends
        $db->begin();
        $rows = $db->arrayQuery( $db->sqlForUpdate( "SELECT * FROM fl_item WHERE name = 'kept'" ) );
        $this->assertCount( 1, $rows );
        $this->assertTrue( $db->query( "UPDATE fl_item SET value = 5 WHERE name = 'kept'" ) );
        $db->commit();
        $this->assertSame( '5', $other->arrayQuery( "SELECT value FROM fl_item WHERE name = 'kept'" )[0]['value'] );
    }

    public function testText()
    {
        $db = $this->db;
        $unicode = "äöü ß 😀 漢字 e\u{0301} it's a \\ backslash";
        $this->assertTrue( $db->query( "INSERT INTO fl_text (id, body, title) VALUES (1, '" . $db->escapeString( $unicode ) . "', '" . $db->escapeString( $unicode ) . "')" ) );
        $row = $db->arrayQuery( 'SELECT body, title FROM fl_text WHERE id = 1' )[0];
        $this->assertSame( $unicode, $row['body'] );
        $this->assertSame( $unicode, $row['title'] );
        $this->assertSame( '1', $db->arrayQuery( "SELECT id FROM fl_text WHERE title = '" . $db->escapeString( $unicode ) . "'" )[0]['id'] );
        $long = str_repeat( "Exponential ä 😀\n", 20000 );
        $this->assertTrue( $db->query( "INSERT INTO fl_text (id, body) VALUES (2, '" . $db->escapeString( $long ) . "')" ), (string)$db->errorMessage() );
        $this->assertSame( $long, $db->arrayQuery( 'SELECT body FROM fl_text WHERE id = 2' )[0]['body'] );
        $this->assertTrue( $db->query( "UPDATE fl_text SET body = '" . $db->escapeString( $long . 'x' ) . "' WHERE id = 2" ) );
        $this->assertSame( strlen( $long ) + 1, (int)$db->arrayQuery( 'SELECT OCTET_LENGTH(body) AS n FROM fl_text WHERE id = 2' )[0]['n'] );
        $this->assertSame( '', $db->arrayQuery( "SELECT '' AS e" )[0]['e'] );
        $this->assertNull( $db->arrayQuery( 'SELECT NULL AS n' )[0]['n'] );
    }

    public function testTemporaryTables()
    {
        $db = $this->db;
        $other = $this->other();
        $name = $db->generateUniqueTempTableName( 'fl_tmp_%', 0 );
        $this->assertSame( 'fl_tmp_0', $name );
        $db->createTempTable( "CREATE TEMPORARY TABLE $name ( contentobject_id int primary key not null, count int )" );
        $this->assertTrue( $db->query( "INSERT INTO $name (contentobject_id, count) VALUES (1, 2)" ) );
        $other->createTempTable( "CREATE TEMPORARY TABLE $name ( contentobject_id int primary key not null, count int )" );
        $this->assertSame( '0', $other->arrayQuery( "SELECT COUNT(*) AS c FROM $name" )[0]['c'] );
        $this->assertSame( '2', $db->arrayQuery( "SELECT count FROM $name" )[0]['count'] );
        $db->dropTempTable( "DROP TABLE $name" );
        $this->assertSame( '0', $db->arrayQuery( "SELECT COUNT(*) AS c FROM $name" )[0]['c'] );
        $other->dropTempTable( "DROP TABLE $name" );
        $other->close();
        $this->assertTrue( $db->query( "DROP TABLE $name" ) );
    }

    public function testErrors()
    {
        $db = $this->db;
        $this->assertTrue( $db->query( "INSERT INTO fl_text (id) VALUES (9)" ) );
        $this->assertFalse( $db->query( "INSERT INTO fl_text (id) VALUES (9)" ) );
        $this->assertSame( 'duplicate', $db->ErrorCategory );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $db->query( "INSERT INTO fl_text (id) VALUES (9)" );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertStringContainsString( 'PRIMARY or UNIQUE KEY', $e->getMessage() );
        }
        $db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        // a connection that is gone is opened again for the next statement outside a transaction
        $db->Transport->close();
        $this->assertSame( '1', $db->arrayQuery( 'SELECT COUNT(*) AS c FROM fl_text' )[0]['c'] );
        $this->assertSame( 1, $db->Reconnects );
    }
}
