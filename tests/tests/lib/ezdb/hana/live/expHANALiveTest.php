<?php
/**
 * expHANADB against a real SAP HANA 2.0 or HANA Cloud (skipped without one, see expHANATestKit::live()).
 *
 *  HL-01 - New ids: a row without id, with NULL, with 0 and with its own id; lastSerialID() per table, per session
 *          (another session's insert does not change it)
 *  HL-02 - Paging and the kernel's MySQL words: limit/offset, LIMIT m, n, bit masks, LIKE with a backslash, CONCAT
 *          of three, LENGTH, SELECT without FROM, TRUNCATE, IF EXISTS, an INSERT of several rows
 *  HL-03 - Transactions: rollback, commit, nested; FOR UPDATE takes the row
 *  HL-04 - Text: Unicode in and out and in a WHERE, an NCLOB compared and sorted, 340 KB through a parameter
 *  HL-05 - Temporary tables of the kernel: made once, rows per session, emptied on drop
 *  HL-06 - Errors: a duplicate key is false (an eZDBException with exceptions), its category; a lost connection is
 *          opened again
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expHANATestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('hana')]
#[\PHPUnit\Framework\Attributes\Group('hana-live')]
class expHANALiveTest extends PHPUnit\Framework\TestCase
{
    /** @var expHANADB */
    private $db;

    /** @var expHANADB[] */
    private $others = array();

    private $saved;

    protected function setUp(): void
    {
        $this->saved = expHANATestKit::quietDebug();
        $db = expHANATestKit::liveDriver();
        if ( !$db instanceof expHANADB )
            $this->markTestSkipped( $db );
        $this->db = $db;
        foreach ( array( 'hl_item', 'hl_text', 'hl_counter' ) as $table )
            $this->db->query( "DROP TABLE IF EXISTS $table" );
        $this->db->query( 'DROP SEQUENCE IF EXISTS S_HL_ITEM' );
        $h = new expHANASchema( array( 'instance' => $this->db ) );
        $def = array( 'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ),
                                         'name' => array( 'type' => 'varchar', 'length' => 100, 'not_null' => '1', 'default' => '' ),
                                         'value' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                                         'language_mask' => array( 'type' => 'bigint', 'not_null' => '1', 'default' => 0 ) ),
                      'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) );
        foreach ( $h->generateTableSQLList( 'hl_item', $def, array(), false ) as $statement )
            $this->assertTrue( $this->db->query( $statement ), (string)$this->db->errorMessage() );
        $this->assertTrue( $this->db->query( 'CREATE COLUMN TABLE hl_text (id INTEGER NOT NULL PRIMARY KEY, body NCLOB, title NVARCHAR(255))' ) );
        $this->assertTrue( $this->db->query( 'CREATE COLUMN TABLE hl_counter (node_id INTEGER NOT NULL PRIMARY KEY, count INTEGER DEFAULT 0 NOT NULL)' ) );
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
            foreach ( array( 'hl_item', 'hl_text', 'hl_counter' ) as $table )
                $this->db->query( "DROP TABLE IF EXISTS $table" );
            $this->db->query( 'DROP SEQUENCE IF EXISTS S_HL_ITEM' );
            $this->db->close();
        }
        expHANATestKit::restoreDebug( $this->saved );
    }

    private function other()
    {
        $db = expHANATestKit::liveDriver();
        $this->assertInstanceOf( 'expHANADB', $db );
        $this->others[] = $db;
        return $db;
    }

    public function testNewIds()
    {
        $db = $this->db;
        $this->assertTrue( $db->query( "INSERT INTO hl_item (name) VALUES ('a')" ), (string)$db->errorMessage() );
        $this->assertSame( 1, $db->lastSerialID( 'hl_item', 'id' ) );
        $this->assertTrue( $db->query( "INSERT INTO hl_item (id, name) VALUES (NULL, 'b')" ) );
        $this->assertSame( 2, $db->lastSerialID( 'hl_item', 'id' ) );
        $this->assertTrue( $db->query( "INSERT INTO hl_item (id, name) VALUES (0, 'c')" ) );
        $this->assertSame( 3, $db->lastSerialID( 'hl_item' ) );
        $this->assertTrue( $db->query( "INSERT INTO hl_item (id, name) VALUES (40, 'd')" ) );
        $this->assertTrue( $db->query( "INSERT INTO hl_item (name) VALUES ('e')" ) );
        $this->assertSame( 41, $db->lastSerialID( 'hl_item' ) );
        $this->assertSame( 41, $db->lastSerialID() );
        // another session's insert leaves this session's id alone
        $other = $this->other();
        $this->assertTrue( $other->query( "INSERT INTO hl_item (name) VALUES ('f')" ) );
        $this->assertSame( 42, $other->lastSerialID( 'hl_item' ) );
        $this->assertSame( 41, $db->lastSerialID( 'hl_item' ) );
        $this->assertFalse( $db->lastSerialID( 'hl_text' ) );
        $this->assertSame( array( 'a', 'b', 'c', 'd', 'e', 'f' ), array_values( $db->arrayQuery( 'SELECT name FROM hl_item ORDER BY id', array( 'column' => 'name' ) ) ) );
    }

    public function testKernelWords()
    {
        $db = $this->db;
        foreach ( array( array( 'a', 1, 6 ), array( 'b\\_x', 2, 1 ), array( 'c', 3, 4 ), array( 'd', 4, 3 ) ) as $row )
            $this->assertTrue( $db->query( "INSERT INTO hl_item (name, value, language_mask) VALUES ('" . $db->escapeString( $row[0] ) . "', {$row[1]}, {$row[2]})" ) );
        $this->assertSame( array( 2 => 'c', 3 => 'd' ), $db->arrayQuery( 'SELECT name FROM hl_item ORDER BY id', array( 'limit' => 2, 'offset' => 2, 'column' => 'name' ) ) );
        $this->assertSame( array( 'b\\_x' ), array_values( $db->arrayQuery( 'SELECT name FROM hl_item ORDER BY id LIMIT 1, 1', array( 'column' => 'name' ) ) ) );
        $this->assertSame( array( '3', '4' ), array_values( $db->arrayQuery( 'SELECT value FROM hl_item WHERE value > 2 ORDER BY value', array( 'column' => 'value' ) ) ) );
        $this->assertSame( array( 'a', 'c' ), array_values( $db->arrayQuery( 'SELECT name FROM hl_item WHERE hl_item.language_mask & 4 > 0 ORDER BY id', array( 'column' => 'name' ) ) ) );
        $this->assertSame( array( 'b\\_x' ), array_values( $db->arrayQuery( "SELECT name FROM hl_item WHERE name LIKE 'b\\\\\\_%'", array( 'column' => 'name' ) ) ) );
        $this->assertSame( 'a-1', $db->arrayQuery( "SELECT CONCAT(name, '-', value) AS c FROM hl_item WHERE value = 1" )[0]['c'] );
        $this->assertSame( '4', $db->arrayQuery( "SELECT LENGTH(name) AS l FROM hl_item WHERE value = 2" )[0]['l'] );
        $this->assertSame( '2', $db->arrayQuery( 'SELECT 1 + 1 AS two' )[0]['two'] );
        $this->assertSame( '7', $db->arrayQuery( 'SELECT ' . $db->bitOr( '3', '4' ) . ' AS b' )[0]['b'] );
        $this->assertTrue( $db->query( 'INSERT INTO hl_counter (node_id, count) VALUES (2, 1), (3, 5)' ), (string)$db->errorMessage() );
        $this->assertTrue( $db->query( 'UPDATE hl_counter SET count = count + 1 WHERE node_id = 2' ) );
        $this->assertSame( '2', $db->arrayQuery( 'SELECT count FROM hl_counter WHERE node_id = 2' )[0]['count'] );
        $this->assertSame( '2', $db->arrayQuery( 'SELECT COUNT(*) AS count FROM hl_counter' )[0]['count'] );
        $this->assertTrue( $db->query( 'TRUNCATE TABLE hl_counter' ) );
        $this->assertSame( '0', $db->arrayQuery( 'SELECT COUNT(*) AS count FROM hl_counter' )[0]['count'] );
        $this->assertTrue( $db->query( 'DROP TABLE IF EXISTS hl_none' ) );
        $this->assertTrue( $db->query( 'CREATE TABLE IF NOT EXISTS hl_counter (x INTEGER)' ) );
    }

    public function testTransactions()
    {
        $db = $this->db;
        $other = $this->other();
        $db->begin();
        $this->assertTrue( $db->query( "INSERT INTO hl_item (name) VALUES ('in')" ) );
        $this->assertSame( '0', $other->arrayQuery( 'SELECT COUNT(*) AS c FROM hl_item' )[0]['c'] );
        $db->rollback();
        $this->assertSame( '0', $db->arrayQuery( 'SELECT COUNT(*) AS c FROM hl_item' )[0]['c'] );
        $db->begin();
        $db->begin();
        $this->assertTrue( $db->query( "INSERT INTO hl_item (name) VALUES ('kept')" ) );
        $db->commit();
        $this->assertSame( '0', $other->arrayQuery( 'SELECT COUNT(*) AS c FROM hl_item' )[0]['c'] );
        $db->commit();
        $this->assertSame( '1', $other->arrayQuery( 'SELECT COUNT(*) AS c FROM hl_item' )[0]['c'] );
        $db->begin();
        $rows = $db->arrayQuery( $db->sqlForUpdate( "SELECT * FROM hl_item WHERE name = 'kept'" ) );
        $this->assertCount( 1, $rows );
        $this->assertTrue( $db->query( "UPDATE hl_item SET value = 5 WHERE name = 'kept'" ) );
        $db->commit();
        $this->assertSame( '5', $other->arrayQuery( "SELECT value FROM hl_item WHERE name = 'kept'" )[0]['value'] );
    }

    public function testText()
    {
        $db = $this->db;
        $unicode = "äöü ß 😀 漢字 e\u{0301} it's a \\ backslash";
        $this->assertTrue( $db->query( "INSERT INTO hl_text (id, body, title) VALUES (1, '" . $db->escapeString( $unicode ) . "', '" . $db->escapeString( $unicode ) . "')" ) );
        $row = $db->arrayQuery( 'SELECT body, title FROM hl_text WHERE id = 1' )[0];
        $this->assertSame( $unicode, $row['body'] );
        $this->assertSame( $unicode, $row['title'] );
        $this->assertSame( '1', $db->arrayQuery( "SELECT id FROM hl_text WHERE title = '" . $db->escapeString( $unicode ) . "'" )[0]['id'] );
        // an NCLOB compared and sorted (TO_NVARCHAR)
        $this->assertSame( '1', $db->arrayQuery( "SELECT id FROM hl_text WHERE body = '" . $db->escapeString( $unicode ) . "' ORDER BY body" )[0]['id'] );
        $long = str_repeat( "Exponential ä 😀\n", 20000 );
        $this->assertTrue( $db->query( "INSERT INTO hl_text (id, body) VALUES (2, '" . $db->escapeString( $long ) . "')" ), (string)$db->errorMessage() );
        $this->assertSame( $long, $db->arrayQuery( 'SELECT body FROM hl_text WHERE id = 2' )[0]['body'] );
        $this->assertTrue( $db->query( "UPDATE hl_text SET body = '" . $db->escapeString( $long . 'x' ) . "' WHERE id = 2" ) );
        // HANA counts a character outside the Basic Multilingual Plane (the emoji) as two
        $this->assertSame( mb_strlen( $long ) + 20000 + 1, (int)$db->arrayQuery( 'SELECT LENGTH(body) AS n FROM hl_text WHERE id = 2' )[0]['n'] );
        $this->assertSame( $long . 'x', $db->arrayQuery( 'SELECT body FROM hl_text WHERE id = 2' )[0]['body'] );
        $this->assertSame( '', $db->arrayQuery( "SELECT '' AS e" )[0]['e'] );
        $this->assertNull( $db->arrayQuery( 'SELECT NULL AS n' )[0]['n'] );
        $this->assertSame( md5( 'abc' ), $db->arrayQuery( 'SELECT ' . $db->md5( "'abc'" ) . ' AS m' )[0]['m'] );
    }

    public function testTemporaryTables()
    {
        $db = $this->db;
        $other = $this->other();
        $name = $db->generateUniqueTempTableName( 'hl_tmp_%', 0 );
        $this->assertSame( 'hl_tmp_0', $name );
        $this->assertTrue( $db->createTempTable( "CREATE TEMPORARY TABLE $name ( contentobject_id int primary key not null, count int )" ), (string)$db->errorMessage() );
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
        $this->assertTrue( $db->query( "INSERT INTO hl_text (id) VALUES (9)" ) );
        $this->assertFalse( $db->query( "INSERT INTO hl_text (id) VALUES (9)" ) );
        $this->assertSame( 'duplicate', $db->ErrorCategory );
        $this->assertSame( 301, $db->errorNumber() );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $db->query( "INSERT INTO hl_text (id) VALUES (9)" );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertStringContainsString( 'unique constraint violated', $e->getMessage() );
        }
        $db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        // a connection that is gone is opened again for the next statement outside a transaction
        $db->Transport->close();
        $this->assertSame( '1', $db->arrayQuery( 'SELECT COUNT(*) AS c FROM hl_text' )[0]['c'] );
        $this->assertSame( 1, $db->Reconnects );
    }
}
