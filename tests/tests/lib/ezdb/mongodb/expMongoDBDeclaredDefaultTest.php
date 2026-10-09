<?php
/**
 * A document without a field that the .dba schema declares NOT NULL with a default reads, and is compared, as the
 * SQL engines read that column: with its default.
 *
 * A SQL table gives every row every column, so a row written before a column was added holds the column's default.
 * A MongoDB document written before then has no such field, and MongoDB decides a missing field its own way:
 * $eq, $in and the ranges never match it, $ne and $nin always do. Seen with ezcontentobject_trash.trashed_by: the
 * update script's "UPDATE ... SET trashed_by = 14 ... WHERE node_id = 74 AND trashed_by = 0" matched no document
 * older than the column (and the script still counted it as moved), and "SELECT node_id, trashed_by" returned
 * rows without a trashed_by key.
 *
 *  - expMongoDB::withDeclaredDefault() (through the WHERE translation of UPDATE, DELETE and SELECT) matches the
 *    missing field exactly when the column's default satisfies the comparison; other columns keep the plain filter.
 *  - A SELECT that names a declared column fills it in for a document that lacks it.
 *
 * The filters are checked here by evaluating them over documents with MongoDB's own rules for a missing field (no
 * server needed). With EXP_TEST_MONGO_SCRATCH=<host>:<port>/<database> (a database whose name contains "scratch"
 * or "test", which the test fills and drops) the same statements also run against a real server.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group mongodb
 */

require_once __DIR__ . '/stubs.php';

/** An in-process collection that evaluates filters with MongoDB's rules for missing fields */
class expMongoDBDeclaredDefaultCollection
{
    /** @var array[] */
    public $rows;

    public function __construct( array &$rows )
    {
        $this->rows = &$rows;
    }

    public function find( array $filter = array(), array $options = array() )
    {
        $result = array();
        foreach ( $this->rows as $row )
        {
            if ( !expMongoDBDeclaredDefaultTest::mongoMatches( $row, $filter ) )
                continue;
            if ( !empty( $options['projection'] ) )
            {
                $projected = array();
                foreach ( $options['projection'] as $field => $on )
                {
                    if ( $on && array_key_exists( $field, $row ) )
                        $projected[$field] = $row[$field];
                }
                $row = $projected;
            }
            $result[] = new StubMongoDocument( $row );
        }
        return $result;
    }

    public function updateMany( array $filter, array $update, array $options = array() )
    {
        foreach ( $this->rows as $i => $row )
        {
            if ( expMongoDBDeclaredDefaultTest::mongoMatches( $row, $filter ) && isset( $update['$set'] ) )
                $this->rows[$i] = array_merge( $row, $update['$set'] );
        }
    }

    public function deleteMany( array $filter, array $options = array() )
    {
        $this->rows = array_values( array_filter( $this->rows, function ( $row ) use ( $filter ) {
            return !expMongoDBDeclaredDefaultTest::mongoMatches( $row, $filter );
        } ) );
    }

    public function listIndexes()
    {
        return array();
    }
}

class expMongoDBDeclaredDefaultClient
{
    private $collections;

    public function __construct( array &$collections )
    {
        $this->collections = &$collections;
    }

    public function selectCollection( $dbName, $collName )
    {
        if ( !isset( $this->collections[$collName] ) )
            $this->collections[$collName] = array();
        return new expMongoDBDeclaredDefaultCollection( $this->collections[$collName] );
    }
}

/** The driver with its client replaced by the evaluating collections */
class expMongoDBDeclaredDefaultDriver extends expMongoDB
{
    public $stubCollections = array();
    public $DB;

    public function __construct()
    {
        $this->TransactionCounter = 0;
        $this->IsConnected = true;
        $this->OutputSQL = false;
        $this->DB = 'mongo';
    }

    public function getClient()
    {
        return new expMongoDBDeclaredDefaultClient( $this->stubCollections );
    }

    /** Sets the schema the driver read from the .dba files (each static only where the driver has it) */
    public static function useSchema( $declared, $numeric )
    {
        foreach ( array( 'DeclaredColumns' => $declared, 'NumericColumns' => $numeric ) as $name => $value )
        {
            $property = self::schemaProperty( $name );
            if ( $property )
                $property->setValue( null, $value );
        }
    }

    /** @return ReflectionProperty|null */
    public static function schemaProperty( $name )
    {
        if ( !property_exists( 'expMongoDB', $name ) )
            return null;
        $property = new ReflectionProperty( 'expMongoDB', $name );
        if ( PHP_VERSION_ID < 80100 )
            $property->setAccessible( true );
        return $property;
    }

    public function where( $sql, $table )
    {
        $method = new ReflectionMethod( 'expMongoDB', 'parseWhereClause' );
        if ( PHP_VERSION_ID < 80100 )
            $method->setAccessible( true );
        return $method->invoke( $this, $sql, $table );
    }
}

/** A real server: the declared schema of the trash table, on a scratch database */
class expMongoDBDeclaredDefaultLiveDriver extends expMongoDB
{
    public $Server;
    public $Port;
    public $DB;
    public $User;
    public $Password;

    public function __construct( $server, $port, $database )
    {
        $this->Server = $server;
        $this->Port = $port;
        $this->DB = $database;
        $this->User = '';
        $this->Password = '';
        $this->TransactionCounter = 0;
        $this->OutputSQL = false;
        $this->IsConnected = true;
    }
}

class expMongoDBDeclaredDefaultTest extends PHPUnit\Framework\TestCase
{
    const TABLE = 'ezcontentobject_trash';

    /** @var expMongoDBDeclaredDefaultDriver */
    private $db;

    /** @var array|null the statics of before the test */
    private $saved = null;

    protected function setUp(): void
    {
        $value = function ( $name ) {
            $property = expMongoDBDeclaredDefaultDriver::schemaProperty( $name );
            return $property ? $property->getValue() : null;
        };
        $this->saved = array( $value( 'DeclaredColumns' ), $value( 'NumericColumns' ) );
        expMongoDBDeclaredDefaultDriver::useSchema(
            array( self::TABLE => array(
                'node_id'     => array( 'type' => 'int', 'not_null' => true, 'default' => 0 ),
                'trashed_by'  => array( 'type' => 'int', 'not_null' => true, 'default' => 0 ),
                'trashed_via' => array( 'type' => 'varchar', 'not_null' => true, 'default' => '' ),
                'remote_id'   => array( 'type' => 'varchar', 'not_null' => false, 'default' => null ) ) ),
            array( self::TABLE => array( 'node_id' => 'int', 'trashed_by' => 'int' ) ) );
        $this->db = new expMongoDBDeclaredDefaultDriver();
        $this->db->stubCollections[self::TABLE] = array(
            array( 'node_id' => 52, 'trashed_by' => 14, 'trashed_via' => 'cli x.php' ),
            array( 'node_id' => 74 ),                                   // trashed before the columns
            array( 'node_id' => 75, 'trashed_by' => 0, 'trashed_via' => '' ),
        );
    }

    protected function tearDown(): void
    {
        expMongoDBDeclaredDefaultDriver::useSchema( $this->saved[0], $this->saved[1] );
    }

    /**
     * Whether $doc matches a MongoDB filter, with MongoDB's rules for a missing field. Covers what the WHERE
     * translation produces: $and, $or, equality, $ne, $in, $nin, the ranges and $exists.
     */
    public static function mongoMatches( array $doc, array $filter )
    {
        foreach ( $filter as $key => $condition )
        {
            if ( $key === '$and' || $key === '$or' )
            {
                $results = array();
                foreach ( $condition as $branch )
                    $results[] = self::mongoMatches( $doc, $branch );
                if ( $key === '$and' ? in_array( false, $results, true ) : !in_array( true, $results, true ) )
                    return false;
                continue;
            }
            $has = array_key_exists( $key, $doc );
            $value = $has ? $doc[$key] : null;
            if ( !is_array( $condition ) || !$condition || strpos( (string)key( $condition ), '$' ) !== 0 )
                $condition = array( '$eq' => $condition );
            foreach ( $condition as $operator => $operand )
            {
                switch ( $operator )
                {
                    case '$eq':  $ok = $operand === null ? $value === null : ( $has && $value == $operand ); break;
                    case '$ne':  $ok = $operand === null ? $value !== null : !( $has && $value == $operand ); break;
                    case '$in':  $ok = ( $has && in_array( $value, $operand ) ) || ( !$has && in_array( null, $operand, true ) ); break;
                    case '$nin': $ok = !( ( $has && in_array( $value, $operand ) ) || ( !$has && in_array( null, $operand, true ) ) ); break;
                    case '$lt':  $ok = $has && $value < $operand; break;
                    case '$lte': $ok = $has && $value <= $operand; break;
                    case '$gt':  $ok = $has && $value > $operand; break;
                    case '$gte': $ok = $has && $value >= $operand; break;
                    case '$exists': $ok = $operand ? $has : !$has; break;
                    default: throw new LogicException( "operator $operator" );
                }
                if ( !$ok )
                    return false;
            }
        }
        return true;
    }

    /** The node ids of the documents a WHERE selects */
    private function selected( $where, $table = self::TABLE )
    {
        $filter = $this->db->where( $where, $table );
        $this->assertIsArray( $filter, $where );
        $ids = array();
        foreach ( $this->db->stubCollections[self::TABLE] as $doc )
            if ( self::mongoMatches( $doc, $filter ) )
                $ids[] = $doc['node_id'];
        return $ids;
    }

    /** Every comparison selects what a SQL table, where the old row holds the default 0, selects */
    public function testAMissingFieldIsComparedAsTheDeclaredDefault()
    {
        $this->assertSame( array( 74, 75 ), $this->selected( 'trashed_by = 0' ) );
        $this->assertSame( array( 74, 75 ), $this->selected( "trashed_by = '0'" ) );
        $this->assertSame( array( 52 ), $this->selected( 'trashed_by = 14' ) );
        $this->assertSame( array( 52 ), $this->selected( 'trashed_by <> 0' ) );
        $this->assertSame( array( 52 ), $this->selected( 'trashed_by != 0' ) );
        $this->assertSame( array( 74, 75 ), $this->selected( 'trashed_by != 14' ) );
        $this->assertSame( array( 74, 75 ), $this->selected( 'trashed_by IN ( 0, 3 )' ) );
        $this->assertSame( array( 52 ), $this->selected( 'trashed_by IN ( 14 )' ) );
        $this->assertSame( array( 52 ), $this->selected( 'trashed_by NOT IN ( 0 )' ) );
        $this->assertSame( array( 74, 75 ), $this->selected( 'trashed_by NOT IN ( 14 )' ) );
        $this->assertSame( array( 52 ), $this->selected( 'trashed_by > 0' ) );
        $this->assertSame( array( 74, 75 ), $this->selected( 'trashed_by < 1' ) );
        $this->assertSame( array( 52, 74, 75 ), $this->selected( 'trashed_by >= 0' ) );
        $this->assertSame( array( 74, 75 ), $this->selected( 'trashed_by <= 0' ) );
        $this->assertSame( array( 74, 75 ), $this->selected( "trashed_via = ''" ) );
        $this->assertSame( array( 52 ), $this->selected( "trashed_via <> ''" ) );
        $this->assertSame( array( 74 ), $this->selected( 'node_id = 74 AND trashed_by = 0' ) );
        $this->assertSame( array( 52, 74 ), $this->selected( 'trashed_by = 14 OR node_id = 74' ) );
    }

    /** Only a declared NOT NULL column with a default of the clause's own table changes */
    public function testOtherColumnsAndUnknownTablesKeepThePlainFilter()
    {
        $this->assertSame( array( 'trashed_by' => 0 ), $this->db->where( 'trashed_by = 0', null ) );
        $this->assertSame( array( 'trashed_by' => 0 ), $this->db->where( 'trashed_by = 0', 'ezcontentobject' ) );
        $this->assertSame( array( 'ezcot.trashed_by' => 0 ), $this->db->where( 'ezcot.trashed_by = 0', self::TABLE ) );
        $this->assertSame( array( 'remote_id' => 'x' ), $this->db->where( "remote_id = 'x'", self::TABLE ) );
        $this->assertSame( array( 'trashed_by' => array( '$ne' => 14 ) ), $this->db->where( 'trashed_by != 14', self::TABLE ) );
        $this->assertSame( array( 'trashed_by' => 14 ), $this->db->where( 'trashed_by = 14', self::TABLE ) );
        $this->assertSame( array( 'node_id' => array( '$gt' => 0 ) ), $this->db->where( 'node_id > 0', self::TABLE ) );
        $this->assertSame( array( 'node_id' => null ), $this->db->where( 'node_id IS NULL', self::TABLE ), 'IS NULL keeps its meaning' );
    }

    /** The update script's UPDATE reaches a document older than the column, once */
    public function testUpdateReachesADocumentWithoutTheField()
    {
        $sql = "UPDATE ezcontentobject_trash SET trashed_by = 10, trashed_via = 'web admin' WHERE node_id = 74 AND trashed_by = 0";
        $this->assertTrue( $this->db->query( $sql ) );
        $rows = $this->db->stubCollections[self::TABLE];
        $this->assertSame( 10, $rows[1]['trashed_by'] );
        $this->assertSame( 'web admin', $rows[1]['trashed_via'] );
        $this->assertTrue( $this->db->query( "UPDATE ezcontentobject_trash SET trashed_by = 11 WHERE node_id = 74 AND trashed_by = 0" ) );
        $this->assertSame( 10, $this->db->stubCollections[self::TABLE][1]['trashed_by'], 'a second run keeps the first value' );
        $this->assertSame( 14, $this->db->stubCollections[self::TABLE][0]['trashed_by'] );
    }

    /** A DELETE decides old documents the same way */
    public function testDeleteDecidesADocumentWithoutTheFieldAsTheDefault()
    {
        $this->assertTrue( $this->db->query( 'DELETE FROM ezcontentobject_trash WHERE trashed_by = 0' ) );
        $this->assertSame( array( 52 ), array_column( $this->db->stubCollections[self::TABLE], 'node_id' ) );
    }

    /** A SELECT that names the column gets the default for a document without it */
    public function testSelectFillsInTheDeclaredDefault()
    {
        $rows = $this->db->arrayQuery( 'SELECT node_id, trashed_by, trashed_via, remote_id FROM ezcontentobject_trash WHERE trashed_by = 0' );
        $this->assertSame( array( array( 'node_id' => 74, 'trashed_by' => 0, 'trashed_via' => '', 'remote_id' => null ),
                                  array( 'node_id' => 75, 'trashed_by' => 0, 'trashed_via' => '' , 'remote_id' => null ) ), $rows );
        $all = $this->db->arrayQuery( 'SELECT * FROM ezcontentobject_trash' );
        $this->assertSame( array( 'node_id' => 74 ), $all[1], 'SELECT * returns the document as it is' );
    }

    /** The same statements against a real server, when a scratch database is given */
    public function testOnARealServer()
    {
        $target = (string)getenv( 'EXP_TEST_MONGO_SCRATCH' );
        if ( $target === '' || !preg_match( '#^([^:/]+):(\d+)/(\w*(?:scratch|test)\w*)$#', $target, $m ) )
            $this->markTestSkipped( 'EXP_TEST_MONGO_SCRATCH=<host>:<port>/<scratch or test database> not set' );
        if ( !class_exists( 'MongoDB\\Client' ) )
            $this->markTestSkipped( 'no MongoDB library' );
        $db = new expMongoDBDeclaredDefaultLiveDriver( $m[1], (int)$m[2], $m[3] );
        $collection = $db->getClient()->selectCollection( $m[3], self::TABLE );
        $collection->drop();
        try
        {
            $collection->insertMany( $this->db->stubCollections[self::TABLE] );
            $ids = function ( $where ) use ( $db ) {
                return array_map( 'intval', array_column( (array)$db->arrayQuery( "SELECT node_id FROM ezcontentobject_trash WHERE $where ORDER BY node_id" ), 'node_id' ) );
            };
            $this->assertSame( array( 74, 75 ), $ids( 'trashed_by = 0' ) );
            $this->assertSame( array( 52 ), $ids( 'trashed_by <> 0' ) );
            $this->assertSame( array( 74, 75 ), $ids( 'trashed_by NOT IN ( 14 )' ) );
            $this->assertSame( array( 52 ), $ids( 'trashed_by NOT IN ( 0 )' ) );
            $this->assertSame( array( 74, 75 ), $ids( 'trashed_by < 1' ) );
            $count = $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject_trash WHERE trashed_by = 0' );
            $this->assertSame( 2, (int)$count[0]['c'] );
            $rows = $db->arrayQuery( 'SELECT node_id, trashed_by FROM ezcontentobject_trash WHERE node_id = 74' );
            $this->assertSame( array( array( 'node_id' => 74, 'trashed_by' => 0 ) ), $rows );
            $this->assertTrue( $db->query( "UPDATE ezcontentobject_trash SET trashed_by = 10, trashed_via = 'web admin' WHERE node_id = 74 AND trashed_by = 0" ) );
            $doc = $collection->findOne( array( 'node_id' => 74 ) );
            $this->assertSame( 10, $doc['trashed_by'] );
            $this->assertSame( 'web admin', $doc['trashed_via'] );
        }
        finally
        {
            $collection->drop();
        }
    }
}
