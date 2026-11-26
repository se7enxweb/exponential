<?php
/**
 * The SQL fallback of the MongoDB adapter (expMongoDB::sqlFallback()): a SELECT the adapter cannot translate
 * into MongoDB operations - a join, GROUP BY with HAVING, UNION, a derived table - is answered by running the
 * SQL itself on an in-memory SQLite copy of the collections it names, instead of an empty result.
 *
 *  SF-01 - an inner join over two collections, with a quoted number compared with an integer column
 *  SF-02 - LEFT JOIN, GROUP BY, COUNT and HAVING
 *  SF-03 - UNION and DISTINCT
 *  SF-04 - the limit, offset and column parameters of arrayQuery()
 *  SF-05 - a write through the adapter drops the copy, so the next SELECT sees the new row
 *  SF-06 - a collection over MongoSQLFallbackMaxRows is refused as before (empty result, error logged)
 *  SF-07 - turned off with MongoSQLFallback=disabled: the empty result of before
 *  SF-08 - the MySQL spellings the kernel builds are rewritten for SQLite (substring FROM FOR, IF, CAST SIGNED)
 *  SF-09 - the table names of a statement: FROM, JOIN and comma lists, not aliases or keywords
 *  SF-10 - BIT_OR, CONCAT and MD5 behave as on MySQL
 *  SF-11 - a NOT NULL column a document lacks reads as its declared default
 *  SF-12 - an unquoted number written into a declared text column is stored as text
 *  SF-13 - an UPDATE whose SET uses SQL functions (CONCAT, SUBSTRING: the node move) is computed per document
 *
 * No server needed: an in-process client serves the collections.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group mongodb
 */

require_once __DIR__ . '/stubs.php';

/** A collection of plain rows with the calls the fallback makes */
class expMongoDBSqlFallbackCollection
{
    public $rows;

    public function __construct( array &$rows )
    {
        $this->rows = &$rows;
    }

    public function estimatedDocumentCount()
    {
        return count( $this->rows );
    }

    public function find( array $filter = array(), array $options = array() )
    {
        $result = array();
        foreach ( $this->rows as $index => $row )
        {
            $match = true;
            foreach ( $filter as $key => $value )
            {
                if ( !array_key_exists( $key, $row ) )
                    $match = false;
                else if ( $value instanceof MongoDB\BSON\Regex )
                    $match = $match && preg_match( '#' . str_replace( '#', '\#', $value->getPattern() ) . '#' . $value->getFlags(), (string)$row[$key] ) === 1;
                else if ( $row[$key] !== $value )
                    $match = false;
            }
            if ( $match )
                $result[] = new StubMongoDocument( $row + array( '_id' => $index ) );
        }
        return $result;
    }

    public function updateOne( array $filter, array $update )
    {
        $index = $filter['_id'];
        foreach ( $update['$set'] as $key => $value )
            $this->rows[$index][$key] = $value;
    }

    public function insertOne( $document, array $options = array() )
    {
        $this->rows[] = (array)$document;
    }

    public function listIndexes()
    {
        return array();
    }
}

class expMongoDBSqlFallbackClient
{
    private $collections;

    public function __construct( array &$collections )
    {
        $this->collections = &$collections;
    }

    public function selectCollection( $dbName, $name )
    {
        if ( !isset( $this->collections[$name] ) )
            $this->collections[$name] = array();
        return new expMongoDBSqlFallbackCollection( $this->collections[$name] );
    }

    public function selectDatabase( $dbName )
    {
        return $this;
    }

    public function listCollectionNames()
    {
        return array_keys( $this->collections );
    }
}

class expMongoDBSqlFallbackDriver extends expMongoDB
{
    public $collections = array();
    public $DB;
    public $errors = array();

    public function __construct()
    {
        $this->TransactionCounter = 0;
        $this->IsConnected = true;
        $this->OutputSQL = false;
        $this->DB = 'mongo';
    }

    public function getClient()
    {
        return new expMongoDBSqlFallbackClient( $this->collections );
    }

    function logError( $message )
    {
        $this->errors[] = $message;
    }
}

class expMongoDBSqlFallbackTest extends PHPUnit\Framework\TestCase
{
    /** @var array the driver's statics before the test */
    private $saved = array();

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'the sqlite3 extension is not loaded' );
        foreach ( array( 'DeclaredColumns', 'NumericColumns', 'FallbackSettings' ) as $name )
        {
            $property = new ReflectionProperty( 'expMongoDB', $name );
            if ( PHP_VERSION_ID < 80100 )
                $property->setAccessible( true );
            $this->saved[$name] = $property->getValue();
        }
        $this->setStatic( 'NumericColumns', array(
            'ezcontentobject_tree' => array( 'node_id' => 'int', 'parent_node_id' => 'int', 'contentobject_id' => 'int', 'depth' => 'int' ),
            'ezcontentobject' => array( 'id' => 'int', 'section_id' => 'int' ),
        ) );
        $this->setStatic( 'DeclaredColumns', array(
            'ezcontentobject_tree' => array(
                'node_id' => array( 'type' => 'int', 'not_null' => true, 'default' => 0 ),
                'parent_node_id' => array( 'type' => 'int', 'not_null' => true, 'default' => 0 ),
                'contentobject_id' => array( 'type' => 'int', 'not_null' => false, 'default' => null ),
                'depth' => array( 'type' => 'int', 'not_null' => true, 'default' => 0 ),
                'path_string' => array( 'type' => 'varchar', 'not_null' => true, 'default' => '' ) ),
            'ezcontentobject' => array(
                'id' => array( 'type' => 'int', 'not_null' => true, 'default' => 0 ),
                'name' => array( 'type' => 'varchar', 'not_null' => false, 'default' => null ),
                'section_id' => array( 'type' => 'int', 'not_null' => true, 'default' => 1 ),
                'remote_id' => array( 'type' => 'varchar', 'not_null' => true, 'default' => '' ) ),
        ) );
        $this->setStatic( 'FallbackSettings', array( true, 1000, 2.0 ) );
    }

    protected function tearDown(): void
    {
        foreach ( $this->saved as $name => $value )
            $this->setStatic( $name, $value );
    }

    private function setStatic( $name, $value )
    {
        $property = new ReflectionProperty( 'expMongoDB', $name );
        if ( PHP_VERSION_ID < 80100 )
            $property->setAccessible( true );
        $property->setValue( null, $value );
    }

    private function driver()
    {
        $db = new expMongoDBSqlFallbackDriver();
        $db->collections['ezcontentobject'] = array(
            array( 'id' => 1, 'name' => 'Home', 'section_id' => 1, 'remote_id' => 'r1' ),
            array( 'id' => 4, 'name' => 'Users', 'section_id' => 2, 'remote_id' => '0004' ),
            array( 'id' => 57, 'name' => 'Fitness', 'remote_id' => 'r57' ),
            array( 'id' => 58, 'name' => 'Agency', 'section_id' => 1, 'remote_id' => 'r58' ),
        );
        $db->collections['ezcontentobject_tree'] = array(
            array( 'node_id' => 2, 'parent_node_id' => 1, 'contentobject_id' => 1 ),
            array( 'node_id' => 5, 'parent_node_id' => 1, 'contentobject_id' => 4 ),
            array( 'node_id' => 60, 'parent_node_id' => 2, 'contentobject_id' => 57 ),
            array( 'node_id' => 61, 'parent_node_id' => 2, 'contentobject_id' => 58 ),
        );
        return $db;
    }

    public function testInnerJoinComparesQuotedNumbersWithIntegerColumns()
    {
        $db = $this->driver();
        $rows = $db->arrayQuery( "SELECT t.node_id, o.name FROM ezcontentobject_tree t, ezcontentobject o
            WHERE t.contentobject_id = o.id AND t.parent_node_id = '2' ORDER BY o.name" );
        $this->assertSame( array( array( 'node_id' => 61, 'name' => 'Agency' ), array( 'node_id' => 60, 'name' => 'Fitness' ) ), $rows );
        $this->assertSame( array(), $db->errors );
    }

    public function testLeftJoinGroupByHaving()
    {
        $db = $this->driver();
        $rows = $db->arrayQuery( 'SELECT p.node_id, COUNT( c.node_id ) AS children FROM ezcontentobject_tree p
            LEFT JOIN ezcontentobject_tree c ON c.parent_node_id = p.node_id GROUP BY p.node_id HAVING COUNT( c.node_id ) > 0
            ORDER BY p.node_id' );
        $this->assertSame( array( array( 'node_id' => 2, 'children' => 2 ) ), $rows );
    }

    public function testUnionAndDistinct()
    {
        $db = $this->driver();
        $rows = $db->arrayQuery( 'SELECT DISTINCT o.section_id FROM ezcontentobject o INNER JOIN ezcontentobject_tree t
            ON t.contentobject_id = o.id ORDER BY o.section_id' );
        $this->assertSame( array( array( 'section_id' => 1 ), array( 'section_id' => 2 ) ), $rows );
        $rows = $db->arrayQuery( 'SELECT id AS v FROM ezcontentobject WHERE id = 1 UNION SELECT node_id AS v FROM ezcontentobject_tree WHERE node_id = 5' );
        $this->assertSame( array( array( 'v' => 1 ), array( 'v' => 5 ) ), $rows );
    }

    public function testLimitOffsetAndColumnParameters()
    {
        $db = $this->driver();
        $sql = 'SELECT o.id FROM ezcontentobject o JOIN ezcontentobject_tree t ON t.contentobject_id = o.id ORDER BY o.id';
        $this->assertSame( array( 4, 57 ), $db->arrayQuery( $sql, array( 'limit' => 2, 'offset' => 1, 'column' => 'id' ) ) );
        $this->assertSame( array( 1, 4, 57, 58 ), $db->arrayQuery( $sql, array( 'column' => 0 ) ) );
    }

    public function testWriteDropsTheCopy()
    {
        $db = $this->driver();
        $sql = 'SELECT COUNT(*) AS n FROM ezcontentobject o JOIN ezcontentobject_tree t ON t.contentobject_id = o.id';
        $this->assertSame( 4, $db->arrayQuery( $sql )[0]['n'] );
        $db->insert( 'ezcontentobject_tree', array( 'node_id' => 62, 'parent_node_id' => 2, 'contentobject_id' => 58 ) );
        $this->assertSame( 5, $db->arrayQuery( $sql )[0]['n'] );
    }

    public function testLargeCollectionIsRefused()
    {
        $this->setStatic( 'FallbackSettings', array( true, 3, 2.0 ) );
        $db = $this->driver();
        $rows = $db->arrayQuery( 'SELECT o.id FROM ezcontentobject o JOIN ezcontentobject_tree t ON t.contentobject_id = o.id' );
        $this->assertSame( array(), $rows );
        $this->assertStringContainsString( 'MongoSQLFallbackMaxRows', implode( "\n", $db->errors ) );
    }

    public function testDisabledFallbackKeepsTheEmptyResult()
    {
        $this->setStatic( 'FallbackSettings', array( false, 1000, 2.0 ) );
        $db = $this->driver();
        $this->assertSame( array(), $db->arrayQuery( 'SELECT o.id FROM ezcontentobject o JOIN ezcontentobject_tree t ON t.contentobject_id = o.id' ) );
    }

    public function testDialect()
    {
        $this->assertSame( 'SELECT substr( name, 1, 3 ) FROM t', expMongoDB::fallbackDialect( 'SELECT substring( name from 1 for 3 ) FROM t' ) );
        $this->assertSame( 'SELECT iif( a > 1, 1, 0 ) FROM t', expMongoDB::fallbackDialect( 'SELECT IF( a > 1, 1, 0 ) FROM t' ) );
        $this->assertSame( 'SELECT CAST( a & 3 AS INTEGER ) FROM t', expMongoDB::fallbackDialect( 'SELECT cast( a & 3 AS SIGNED ) FROM t' ) );
        $this->assertSame( 'SELECT a FROM t JOIN u ON 1', expMongoDB::fallbackDialect( 'SELECT a FROM t STRAIGHT_JOIN u ON 1 FOR UPDATE' ) );
    }

    public function testTableNames()
    {
        $names = expMongoDB::fallbackTableNames(
            'SELECT a.x FROM ezcontentobject_tree a, ezcontentobject b INNER JOIN ezcontentobject_name n ON n.id = b.id WHERE a.y IN ( SELECT 1 )' );
        sort( $names );
        $this->assertSame( array( 'ezcontentobject', 'ezcontentobject_name', 'ezcontentobject_tree' ), $names );
    }

    public function testMySqlFunctions()
    {
        $db = $this->driver();
        $rows = $db->arrayQuery( "SELECT BIT_OR( o.section_id ) AS mask, CONCAT( 'a', o2.name ) AS c, MD5( 'x' ) AS h
            FROM ezcontentobject o, ezcontentobject o2 WHERE o2.id = 1 GROUP BY o2.name" );
        $this->assertSame( array( array( 'mask' => 3, 'c' => 'aHome', 'h' => md5( 'x' ) ) ), $rows );
    }

    /** SF-13 - an UPDATE whose SET uses SQL functions (the node move) is computed per document by the fallback */
    public function testUpdateWithFunctionsInSetIsComputedPerDocument()
    {
        $db = $this->driver();
        $db->collections['ezcontentobject_tree'] = array(
            array( 'node_id' => 60, 'parent_node_id' => 2, 'path_string' => '/1/2/60/', 'depth' => 2 ),
            array( 'node_id' => 70, 'parent_node_id' => 60, 'path_string' => '/1/2/60/70/', 'depth' => 3 ),
            array( 'node_id' => 5, 'parent_node_id' => 1, 'path_string' => '/1/5/', 'depth' => 1 ),
        );
        $this->assertTrue( $db->query( "UPDATE ezcontentobject_tree SET path_string = concat( '/1/5/60', substring( path_string from 8 ) ),
            depth = depth + 1 - 2 + 1 WHERE path_string LIKE '/1/2/60/%'" ) );
        $rows = $db->collections['ezcontentobject_tree'];
        $this->assertSame( array( '/1/5/60/', 2 ), array( $rows[0]['path_string'], $rows[0]['depth'] ) );
        $this->assertSame( array( '/1/5/60/70/', 3 ), array( $rows[1]['path_string'], $rows[1]['depth'] ) );
        $this->assertSame( '/1/5/', $rows[2]['path_string'] );
    }

    /** SF-12 - an unquoted number written into a declared text column is stored as text, into a numeric one as a number */
    public function testUnquotedNumberInTextColumnIsText()
    {
        $this->assertSame( '1', expMongoDB::castSqlLiteralForColumn( '1', 'ezcontentobject', 'remote_id' ) );
        $this->assertSame( 1, expMongoDB::castSqlLiteralForColumn( '1', 'ezcontentobject', 'section_id' ) );
        $this->assertSame( '7', expMongoDB::castSqlLiteralForColumn( "'7'", 'ezcontentobject', 'remote_id' ) );
        $this->assertSame( 5, expMongoDB::castSqlLiteralForColumn( '5', 'undeclared_table', 'value' ) );
        $this->assertNull( expMongoDB::castSqlLiteralForColumn( 'NULL', 'ezcontentobject', 'remote_id' ) );
    }

    public function testMissingNotNullColumnReadsItsDefault()
    {
        $db = $this->driver();
        $rows = $db->arrayQuery( 'SELECT o.section_id FROM ezcontentobject o JOIN ezcontentobject_tree t ON t.contentobject_id = o.id WHERE o.id = 57' );
        $this->assertSame( array( array( 'section_id' => 1 ) ), $rows );
    }
}
