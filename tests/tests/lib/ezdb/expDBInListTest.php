<?php
/**
 * expDBInList: queries with an IN list of a set of any size, asked in lists of chunkSize() values, against a database
 * that records the statements. No database.
 *
 *  IL-01 - 1000 values take one statement, 1001 two, 2500 three, each list at most chunkSize() values
 *  IL-02 - No values: no statement, an empty result (rows, column), 0 (count), true (query)
 *  IL-03 - A statement that fails in a middle list makes the whole result false, the lists after it are not asked
 *  IL-04 - Values are made unique and cast before they are split; strings are escaped and quoted
 *  IL-05 - The tail follows the IN list in every statement
 *  IL-06 - count() sums the counts of the lists and is false on a failed or empty answer
 *  IL-07 - Chunk size: site.ini InListChunkSize, per engine InListChunkSizes[], Oracle at most 1000, setChunkSize()
 *  IL-08 - query() runs one write per list and stops at the first that fails
 *  IL-09 - column() returns one field of every row
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDBInListTestDB extends eZNullDB
{
    public $name = 'mysql';
    /** @var string[] every statement asked */
    public $queries = array();
    /** @var int|null the statement with this index (0-based) fails */
    public $failAt = null;
    /** @var mixed what a COUNT(*) statement returns, null: the count of its list */
    public $countAnswer = null;

    public function __construct()
    {
    }

    function databaseName()
    {
        return $this->name;
    }

    function generateSQLINStatement( $elements, $columnName = '', $not = false, $unique = true, $type = false )
    {
        return $columnName . ( $not ? ' NOT IN' : ' IN' ) . ' ( ' . implode( ', ', $elements ) . ' )';
    }

    function escapeString( $str )
    {
        return str_replace( "'", "''", (string)$str );
    }

    private function fails()
    {
        $index = count( $this->queries ) - 1;
        return $this->failAt !== null && $index === $this->failAt;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        $this->queries[] = $sql;
        if ( $this->fails() )
            return false;
        $values = self::inList( $sql );
        if ( strpos( $sql, 'SELECT COUNT(*) AS count' ) === 0 )
            return $this->countAnswer !== null ? $this->countAnswer : array( array( 'count' => (string)count( $values ) ) );
        $rows = array();
        foreach ( $values as $value )
            $rows[] = array( 'id' => $value );
        return $rows;
    }

    function query( $sql, $server = false )
    {
        $this->queries[] = $sql;
        return $this->fails() ? false : true;
    }

    /** @return string[] the values of the IN list of a statement, as written */
    public static function inList( $sql )
    {
        return preg_match( '/ IN \( (.*?) \)/', $sql, $m ) ? explode( ', ', $m[1] ) : array();
    }
}

class expDBInListTest extends PHPUnit\Framework\TestCase
{
    /** @var expDBInListTestDB */
    private $db;

    protected function setUp(): void
    {
        $this->db = new expDBInListTestDB();
        expDBInList::setChunkSize( 1000 );
    }

    protected function tearDown(): void
    {
        expDBInList::setChunkSize( null );
        $ini = eZINI::instance( 'site.ini' );
        $ini->setVariable( 'DatabaseSettings', 'InListChunkSize', '1000' );
        $ini->setVariable( 'DatabaseSettings', 'InListChunkSizes', array() );
    }

    public function testIL01ChunkBoundaries()
    {
        foreach ( array( 1000 => 1, 1001 => 2, 2500 => 3 ) as $count => $statements )
        {
            $this->db->queries = array();
            $rows = expDBInList::rows( $this->db, 'SELECT id FROM t WHERE', range( 1, $count ), 'id' );
            $this->assertCount( $statements, $this->db->queries, (string)$count );
            foreach ( $this->db->queries as $sql )
                $this->assertLessThanOrEqual( 1000, count( expDBInListTestDB::inList( $sql ) ) );
            $this->assertCount( $count, $rows );
            $this->assertSame( range( 1, $count ), array_map( 'intval', array_column( $rows, 'id' ) ) );
        }
        $this->assertSame( 'SELECT id FROM t WHERE id IN ( 2001, ', substr( $this->db->queries[2], 0, 37 ) );
    }

    public function testIL02NoValues()
    {
        $this->assertSame( array(), expDBInList::rows( $this->db, 'SELECT id FROM t WHERE', array(), 'id' ) );
        $this->assertSame( array(), expDBInList::column( $this->db, 'SELECT id FROM t WHERE', array(), 'id', 'id' ) );
        $this->assertSame( 0, expDBInList::count( $this->db, 'FROM t WHERE', array(), 'id' ) );
        $this->assertTrue( expDBInList::query( $this->db, 'DELETE FROM t WHERE', array(), 'id' ) );
        $this->assertSame( array(), expDBInList::chunks( array() ) );
        $this->assertSame( array(), $this->db->queries );
    }

    public function testIL03FailureInAMiddleChunk()
    {
        $this->db->failAt = 1;
        $this->assertFalse( expDBInList::rows( $this->db, 'SELECT id FROM t WHERE', range( 1, 2500 ), 'id' ) );
        $this->assertCount( 2, $this->db->queries, 'the third list is not asked' );

        foreach ( array( 'column', 'count' ) as $method )
        {
            $this->db->queries = array();
            $result = $method === 'column'
                ? expDBInList::column( $this->db, 'SELECT id FROM t WHERE', range( 1, 2500 ), 'id', 'id' )
                : expDBInList::count( $this->db, 'FROM t WHERE', range( 1, 2500 ), 'id' );
            $this->assertFalse( $result, $method );
            $this->assertCount( 2, $this->db->queries, $method );
        }

        // the first list fails too
        $this->db->queries = array();
        $this->db->failAt = 0;
        $this->assertFalse( expDBInList::rows( $this->db, 'SELECT id FROM t WHERE', array( 1, 2 ), 'id' ) );
    }

    public function testIL04UniqueCastAndQuoted()
    {
        $this->assertSame( array( array( 1, 2, 3 ) ), expDBInList::chunks( array( '1', 2, 2, '3x', 1.0 ), 'int', $this->db ) );
        $this->assertSame( array( array( '1', '01' ) ), expDBInList::chunks( array( '1', '01', 1 ), 'string', $this->db ), 'strings stay apart from equal-looking ints' );

        expDBInList::rows( $this->db, 'SELECT id FROM t WHERE', array( "o'brien", 'a', 'a' ), 'name', 'string' );
        $this->assertSame( array( "SELECT id FROM t WHERE name IN ( 'o''brien', 'a' )" ), $this->db->queries );
    }

    public function testIL05Tail()
    {
        expDBInList::setChunkSize( 2 );
        expDBInList::rows( $this->db, 'SELECT id FROM t WHERE', array( 1, 2, 3 ), 'id', 'int', 'AND status = 1 ORDER BY id' );
        $this->assertSame( array( 'SELECT id FROM t WHERE id IN ( 1, 2 ) AND status = 1 ORDER BY id',
                                  'SELECT id FROM t WHERE id IN ( 3 ) AND status = 1 ORDER BY id' ), $this->db->queries );
    }

    public function testIL06Count()
    {
        $this->assertSame( 2500, expDBInList::count( $this->db, 'FROM t WHERE', range( 1, 2500 ), 'id', 'int', 'AND depth > 1' ) );
        $this->assertCount( 3, $this->db->queries );
        $this->assertSame( 'SELECT COUNT(*) AS count FROM t WHERE id IN ( ', substr( $this->db->queries[0], 0, 46 ) );
        $this->assertStringEndsWith( ' ) AND depth > 1', $this->db->queries[0] );

        // Oracle answers COUNT in upper case
        $this->db->countAnswer = array( array( 'COUNT' => '7' ) );
        $this->assertSame( 14, expDBInList::count( $this->db, 'FROM t WHERE', range( 1, 1500 ), 'id' ) );

        $this->db->countAnswer = array();
        $this->assertFalse( expDBInList::count( $this->db, 'FROM t WHERE', array( 1 ), 'id' ), 'no row is no count' );
    }

    public function testIL07ChunkSizeSettings()
    {
        expDBInList::setChunkSize( null );
        $ini = eZINI::instance( 'site.ini' );
        $ini->setVariable( 'DatabaseSettings', 'InListChunkSize', '1000' );
        $ini->setVariable( 'DatabaseSettings', 'InListChunkSizes', array() );
        $this->assertSame( 1000, expDBInList::chunkSize( $this->db ) );

        $ini->setVariable( 'DatabaseSettings', 'InListChunkSize', '400' );
        $ini->setVariable( 'DatabaseSettings', 'InListChunkSizes', array( 'postgresql' => '5000', 'oracle' => '5000', 'sqlite' => 'x' ) );
        expDBInList::setChunkSize( null );
        $this->assertSame( 400, expDBInList::chunkSize( $this->db ), 'mysql: the general setting' );
        foreach ( array( 'postgresql' => 5000, 'oracle' => 1000, 'sqlite' => 400 ) as $engine => $size )
        {
            $this->db->name = $engine;
            $this->assertSame( $size, expDBInList::chunkSize( $this->db ), $engine );
        }

        $ini->setVariable( 'DatabaseSettings', 'InListChunkSize', '0' );
        $ini->setVariable( 'DatabaseSettings', 'InListChunkSizes', array() );
        expDBInList::setChunkSize( null );
        $this->db->name = 'mysql';
        $this->assertSame( expDBInList::DEFAULT_CHUNK_SIZE, expDBInList::chunkSize( $this->db ), 'an invalid setting is the default' );

        expDBInList::setChunkSize( 3000 );
        $this->assertSame( 3000, expDBInList::chunkSize( $this->db ) );
        $this->db->name = 'oracle';
        $this->assertSame( 1000, expDBInList::chunkSize( $this->db ), 'Oracle never more than 1000' );
        $this->db->name = 'pdo_oci';
        $this->assertSame( 1000, expDBInList::chunkSize( $this->db ) );
        expDBInList::setChunkSize( 0 );
        $this->assertSame( 1, expDBInList::chunkSize( $this->db ) );
    }

    public function testIL08QueryWritesPerList()
    {
        $this->assertTrue( expDBInList::query( $this->db, 'DELETE FROM t WHERE', range( 1, 2001 ), 'id' ) );
        $this->assertCount( 3, $this->db->queries );
        $this->assertSame( 'DELETE FROM t WHERE id IN ( 2001 )', $this->db->queries[2] );

        $this->db->queries = array();
        $this->db->failAt = 0;
        $this->assertFalse( expDBInList::query( $this->db, 'DELETE FROM t WHERE', range( 1, 2001 ), 'id' ) );
        $this->assertCount( 1, $this->db->queries );
    }

    public function testIL09Column()
    {
        $this->assertSame( array( '5', '6' ), expDBInList::column( $this->db, 'SELECT id FROM t WHERE', array( 5, 6 ), 'id', 'id' ) );
        $this->assertSame( array(), expDBInList::column( $this->db, 'SELECT id FROM t WHERE', array( 5, 6 ), 'id', 'missing' ) );
    }
}
