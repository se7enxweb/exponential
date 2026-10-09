<?php
/**
 * The trash view asks the database for what one page needs (Exponential\Service\TrashList): the nodes below an item
 * are counted with TrashList::belowCondition(), the summary line and the user list are counted by the database.
 *
 *  - belowCondition() accepts a path of node ids only; a final line break, which "$" in a pattern lets through, is
 *    refused like any other character.
 *  - On SQLite the range holds exactly the rows whose path starts with the item's path (not the item, not a sibling
 *    whose id starts with the same digits) and is answered from the index on path_string; LIKE gives the same rows.
 *  - On MongoDB the driver translates neither the NOT EXISTS of the summary, nor SUM( CASE ... ), nor GROUP BY: the
 *    summary and the user list come from aggregations instead, with the same numbers, a document older than the field
 *    trashed_by counting as trashed_by 0. Checked against a stand-in connection that records what is asked, and, with
 *    EXP_TEST_MONGO_SCRATCH=<host>:<port>/<database whose name contains "scratch" or "test">, against a real server
 *    (the test fills the collection ezcontentobject_trash of that database and drops it).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** A MongoDB connection as far as TrashList's counts use it: aggregate() answers from a callback, everything is recorded */
class expTestPagedTrashMongoDB extends eZDBInterface
{
    /** @var callable|null pipeline => rows */
    public $answer = null;
    /** @var array array( 'aggregate', collection, pipeline ) or array( 'arrayQuery', sql ) */
    public $calls = array();

    public function __construct()
    {
    }

    public function databaseName()
    {
        return 'mongo';
    }

    public function isConnected()
    {
        return true;
    }

    public function aggregate( $table, $pipeline = array() )
    {
        $this->calls[] = array( 'aggregate', $table, $pipeline );
        return $this->answer ? call_user_func( $this->answer, $pipeline ) : array();
    }

    public function arrayQuery( $sql, $params = array(), $server = false )
    {
        $this->calls[] = array( 'arrayQuery', $sql );
        return array();
    }

    public function query( $sql, $server = false )
    {
        $this->calls[] = array( 'query', $sql );
        return true;
    }

    public function escapeString( $str )
    {
        return (string)$str;
    }
}

class TrashListPagedTest extends PHPUnit\Framework\TestCase
{
    /** @var array|null the database instance of before the test */
    private $saved = null;

    public static function setUpBeforeClass(): void
    {
        // the kernel installs its exception handler once per process: here, not inside a test
        eZExecution::registerShutdownHandler();
    }

    protected function setUp(): void
    {
        foreach ( array( 'Exponential\\Service\\TrashRecord' => 'trashrecord.php', 'Exponential\\Service\\TrashList' => 'trashlist.php' ) as $class => $file )
            if ( !class_exists( $class ) )
                require_once dirname( __DIR__, 5 ) . '/kernel/private/classes/services/' . $file;
        $this->saved = array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) ? array( $GLOBALS['eZDBGlobalInstance'] ) : null;
    }

    protected function tearDown(): void
    {
        if ( $this->saved === null )
            unset( $GLOBALS['eZDBGlobalInstance'] );
        else
            $GLOBALS['eZDBGlobalInstance'] = $this->saved[0];
    }

    public function testBelowConditionRefusesAFinalLineBreak()
    {
        $db = new expTestPagedTrashMongoDB();
        $this->assertFalse( Exponential\Service\TrashList::belowCondition( $db, "/1/2/43/\n" ) );
        $this->assertFalse( Exponential\Service\TrashList::belowCondition( $db, "/1/2/43/\r\n" ) );
        $this->assertNotFalse( Exponential\Service\TrashList::belowCondition( $db, '/1/2/43/' ) );
    }

    /**
     * The trash rows of the tests below: two subtrees in the trash, siblings whose ids begin with the same digits
     */
    private static function paths()
    {
        return array( '/1/2/43/', '/1/2/43/5/', '/1/2/43/5/6/', '/1/2/43/50/', '/1/2/430/', '/1/2/430/7/', '/1/2/4300/',
                      '/1/2/42/', '/1/2/44/', '/1/2/4/', '/1/2/4/3/', '/1/43/', '/1/2/43', '/1/2/43/9/10/11/' );
    }

    public function testBelowConditionOnSQLiteHoldsExactlyThePrefixAndUsesTheIndex()
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'no SQLite3' );
        $sqlite = new SQLite3( ':memory:' );
        $sqlite->exec( 'CREATE TABLE ezcontentobject_trash ( node_id integer PRIMARY KEY, path_string varchar(255) NOT NULL DEFAULT \'\' )' );
        $sqlite->exec( 'CREATE INDEX ezcontentobject_trash__ezcobj_trash_path ON ezcontentobject_trash ( path_string )' );
        foreach ( self::paths() as $k => $path )
            $sqlite->exec( "INSERT INTO ezcontentobject_trash VALUES ( $k, '$path' )" );

        $engines = array( 'sqlite' => new expTestTrashedByListDB( 'sqlite' ), 'other' => new expTestTrashedByListDB( 'postgresql' ) );
        foreach ( self::paths() as $path )
        {
            if ( substr( $path, -1 ) !== '/' )
                continue;
            $expected = 0;
            foreach ( self::paths() as $other )
                if ( $other !== $path && strpos( $other, $path ) === 0 )
                    $expected++;
            foreach ( $engines as $name => $db )
            {
                $condition = Exponential\Service\TrashList::belowCondition( $db, $path );
                $this->assertSame( $expected, (int)$sqlite->querySingle( 'SELECT COUNT(*) FROM ezcontentobject_trash WHERE ' . $condition ), "$name $path" );
            }
        }
        $plan = '';
        $result = $sqlite->query( 'EXPLAIN QUERY PLAN SELECT COUNT(*) FROM ezcontentobject_trash WHERE '
                                . Exponential\Service\TrashList::belowCondition( $engines['sqlite'], '/1/2/43/' ) );
        while ( $row = $result->fetchArray( SQLITE3_ASSOC ) )
            $plan .= $row['detail'] . "\n";
        $this->assertStringContainsString( 'ezcontentobject_trash__ezcobj_trash_path (path_string>? AND path_string<?)', $plan );
    }

    /**
     * Before: the summary sent SQL the MongoDB driver cannot translate (NOT EXISTS with table aliases,
     * SUM( CASE ... )), which it answers with nothing: 0 items, the summary line gone.
     */
    public function testSummaryOnMongoComesFromAggregations()
    {
        $db = new expTestPagedTrashMongoDB();
        $db->answer = function ( $pipeline )
        {
            if ( isset( $pipeline[0]['$group'] ) )
                return array( array( '_id' => null, 'items' => 7, 'oldest' => 1780000000, 'recorded' => 4 ) );
            if ( isset( $pipeline[0]['$lookup'] ) )
                return array( array( 'top' => 3 ) );
            return array();
        };
        eZDB::setInstance( $db );
        $summary = Exponential\Service\TrashList::summary( array( 'columns' => true, 'records' => array( 301 => array( 'user_id' => 14 ) ) ) );
        $this->assertSame( array( 'items' => 7, 'top' => 3, 'below' => 4, 'oldest' => 1780000000, 'recorded' => 5 ), $summary );
        foreach ( $db->calls as $call )
            $this->assertSame( 'aggregate', $call[0], 'no SQL the driver cannot translate: ' . json_encode( $call ) );
        // a document older than trashed_by counts as 0; the parent is looked up in the trash itself
        $this->assertSame( array( '$ifNull' => array( '$trashed_by', 0 ) ), $db->calls[0][2][0]['$group']['recorded']['$sum']['$cond'][0]['$gt'][0] );
        $this->assertSame( array( 'from' => 'ezcontentobject_trash', 'localField' => 'parent_node_id', 'foreignField' => 'node_id', 'as' => '_parent' ),
                           $db->calls[1][2][0]['$lookup'] );

        // without the columns no recorded count is asked; an empty trash
        $db->calls = array();
        $db->answer = function ( $pipeline ) { return array(); };
        $summary = Exponential\Service\TrashList::summary( array( 'columns' => false, 'records' => array() ) );
        $this->assertSame( array( 'items' => 0, 'top' => 0, 'below' => 0, 'oldest' => false, 'recorded' => 0 ), $summary );
        $this->assertArrayNotHasKey( 'recorded', $db->calls[0][2][0]['$group'] );
    }

    /**
     * Before: GROUP BY, which the MongoDB driver does not translate: the user list was empty.
     */
    public function testUserOptionsOnMongoComeFromAGroup()
    {
        $db = new expTestPagedTrashMongoDB();
        $db->answer = function ( $pipeline )
        {
            return array( array( '_id' => 14, 'items' => 5 ), array( '_id' => 10, 'items' => 2 ) );
        };
        eZDB::setInstance( $db );
        $options = Exponential\Service\TrashList::userOptions( array( 'columns' => true, 'records' => array( 301 => array( 'user_id' => 14, 'user_name' => 'x' ) ) ) );
        $counts = array();
        foreach ( $options as $option )
            $counts[$option['id']] = $option['count'];
        ksort( $counts );
        $this->assertSame( array( 10 => 2, 14 => 6 ), $counts, 'the rows per user, plus the one known from the old file' );
        $this->assertSame( 'aggregate', $db->calls[0][0] );
        $this->assertSame( array( '$match' => array( 'trashed_by' => array( '$gt' => 0 ) ) ), $db->calls[0][2][0] );
        foreach ( $db->calls as $call )
            $this->assertStringNotContainsString( 'GROUP BY', json_encode( $call ) );
    }

    /**
     * The same counts from a real MongoDB server, through the real driver: summary, user list, and the nodes below an
     * item (belowCondition() through the driver's LIKE translation).
     */
    public function testCountsAgainstARealMongoServer()
    {
        $target = (string)getenv( 'EXP_TEST_MONGO_SCRATCH' );
        if ( $target === '' || !preg_match( '#^([^:/]+):(\d+)/(\w*(?:scratch|test)\w*)$#', $target, $m ) )
            $this->markTestSkipped( 'EXP_TEST_MONGO_SCRATCH=<host>:<port>/<scratch or test database> not set' );
        if ( !class_exists( 'MongoDB\\Client' ) || !class_exists( 'expMongoDB' ) )
            $this->markTestSkipped( 'no MongoDB library' );
        $db = new expTestPagedTrashLiveMongoDB( $m[1], (int)$m[2], $m[3] );
        $collection = $db->getClient()->selectCollection( $m[3], 'ezcontentobject_trash' );
        $collection->drop();
        $documents = array();
        foreach ( self::paths() as $k => $path )
        {
            if ( substr( $path, -1 ) !== '/' )
                continue;
            $ids = explode( '/', trim( $path, '/' ) );
            $document = array( 'node_id' => (int)end( $ids ), 'parent_node_id' => (int)$ids[count( $ids ) - 2],
                               'contentobject_id' => 300 + $k, 'path_string' => $path, 'trashed' => 1780000000 + $k );
            // by 14, by 10, nobody known, and older than the field (no trashed_by at all)
            if ( $k % 4 === 0 )
                $document += array( 'trashed_by' => 14, 'trashed_via' => 'admin' );
            else if ( $k % 4 === 1 )
                $document += array( 'trashed_by' => 10, 'trashed_via' => 'cli' );
            else if ( $k % 4 === 2 )
                $document += array( 'trashed_by' => 0, 'trashed_via' => '' );
            $documents[] = $document;
        }
        try
        {
            $collection->insertMany( $documents );
            eZDB::setInstance( $db );

            $byID = array();
            foreach ( $documents as $document )
                $byID[$document['node_id']] = $document;
            $top = 0;
            $recorded = 0;
            $users = array();
            foreach ( $documents as $document )
            {
                if ( !isset( $byID[$document['parent_node_id']] ) )
                    $top++;
                if ( !empty( $document['trashed_by'] ) )
                {
                    $recorded++;
                    $users[$document['trashed_by']] = isset( $users[$document['trashed_by']] ) ? $users[$document['trashed_by']] + 1 : 1;
                }
            }
            $this->assertSame( array( 'items' => count( $documents ), 'top' => $top, 'below' => count( $documents ) - $top,
                                      'oldest' => 1780000000, 'recorded' => $recorded ),
                               Exponential\Service\TrashList::summary( array( 'columns' => true, 'records' => array() ) ) );

            $counts = array();
            foreach ( Exponential\Service\TrashList::userOptions( array( 'columns' => true, 'records' => array() ) ) as $option )
                $counts[$option['id']] = $option['count'];
            ksort( $users );
            ksort( $counts );
            $this->assertSame( $users, $counts );

            foreach ( $documents as $document )
            {
                $expected = 0;
                foreach ( $documents as $other )
                    if ( $other['path_string'] !== $document['path_string'] && strpos( $other['path_string'], $document['path_string'] ) === 0 )
                        $expected++;
                $rows = $db->arrayQuery( 'SELECT COUNT(*) AS below FROM ezcontentobject_trash WHERE '
                                       . Exponential\Service\TrashList::belowCondition( $db, $document['path_string'] ) );
                $this->assertSame( $expected, (int)$rows[0]['below'], $document['path_string'] );
            }
        }
        finally
        {
            $collection->drop();
        }
    }
}

/** A database as far as TrashList::belowCondition() asks it, by engine name */
class expTestTrashedByListDB
{
    private $name;

    public function __construct( $name )
    {
        $this->name = $name;
    }

    public function databaseName()
    {
        return $this->name;
    }

    public function escapeString( $value )
    {
        return str_replace( "'", "''", $value );
    }
}

if ( class_exists( 'expMongoDB' ) )
{
    /** The real MongoDB driver, connected to the scratch database without the kernel's settings */
    class expTestPagedTrashLiveMongoDB extends expMongoDB
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
}
