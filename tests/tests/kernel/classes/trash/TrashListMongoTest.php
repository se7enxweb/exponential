<?php
/**
 * The trash on MongoDB: the trash view's list and count, its filters (by user, "unknown", class, dates), the class
 * filter's options and hasTrashedByColumns().
 *
 * eZContentObjectTrashNode::trashList() is one SQL statement with joins, and TrashList::classOptions() another; the
 * MongoDB driver translates neither (no joins), so on MongoDB the trash view listed nothing, counted nothing and
 * offered no classes, whatever was in the trash. trashListMongo() builds the list from one aggregation instead, with
 * trashListFilterMongo() for the filters, in which a trash document older than the column trashed_by counts as
 * trashed_by 0 (the column's default), as the "unknown" filter needs.
 *
 * The filters are checked by evaluating them over documents with MongoDB's rules for a missing field; the list,
 * the count and the class options against a stand-in MongoDB connection that answers the aggregation and records
 * the statements. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** A MongoDB connection as far as the trash code uses it: the answers are given, the calls recorded */
class expTestTrashMongoDB extends eZDBInterface
{
    /** @var array[] rows aggregate() returns per collection; a $count pipeline gets their number */
    public $aggregateRows = array();
    /** @var array SQL prefix => rows of arrayQuery() */
    public $queryRows = array();
    /** @var array what was asked: array( 'aggregate', collection, pipeline ) or array( 'arrayQuery', sql ) */
    public $calls = array();
    /** @var array|null the declaration declaredColumns() reports for ezcontentobject_trash */
    public $declared = array();

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

    public function declaredColumns( $table )
    {
        return $table === 'ezcontentobject_trash' ? $this->declared : array();
    }

    public function aggregate( $table, $pipeline = array() )
    {
        $this->calls[] = array( 'aggregate', $table, $pipeline );
        $rows = isset( $this->aggregateRows[$table] ) ? $this->aggregateRows[$table] : array();
        $last = end( $pipeline );
        if ( isset( $last['$count'] ) )
            return $rows ? array( array( $last['$count'] => count( $rows ) ) ) : array();
        return $rows;
    }

    public function arrayQuery( $sql, $params = array(), $server = false )
    {
        $this->calls[] = array( 'arrayQuery', $sql );
        foreach ( $this->queryRows as $prefix => $rows )
            if ( strpos( $sql, $prefix ) === 0 )
                return $rows;
        return array();
    }

    public function query( $sql, $server = false )
    {
        $this->calls[] = array( 'query', $sql );
        return true;
    }

    public function escapeString( $str )
    {
        return addslashes( (string)$str );
    }

    public function dropTempTableList( $tableList, $server = self::SERVER_SLAVE )
    {
    }
}

class TrashListMongoTest extends PHPUnit\Framework\TestCase
{
    /** @var array the globals and statics of before the test */
    private $saved = array();

    /** @var expTestTrashMongoDB */
    private $db;

    protected function setUp(): void
    {
        foreach ( array( 'Exponential\\Service\\TrashRecord' => 'trashrecord.php', 'Exponential\\Service\\TrashList' => 'trashlist.php' ) as $class => $file )
            if ( !class_exists( $class ) )
                require_once dirname( __DIR__, 5 ) . '/kernel/private/classes/services/' . $file;
        foreach ( array( 'eZDBGlobalInstance', 'eZContentLanguagePrioritizedLanguages', 'eZContentClassObjectCache', 'eZUserGlobalInstance_' ) as $key )
            $this->saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $this->saved['columns'] = self::columnsProperty()->getValue();

        $this->db = new expTestTrashMongoDB();
        eZDB::setInstance( $this->db );
        // the anonymous user is the current user without a database read
        $GLOBALS['eZUserGlobalInstance_'] = new eZUser( array( 'contentobject_id' => eZUser::anonymousId(), 'login' => 'anonymous' ) );
        $GLOBALS['eZContentLanguagePrioritizedLanguages'] = array(
            new eZContentLanguage( array( 'id' => 2, 'locale' => 'eng-GB', 'name' => 'English', 'disabled' => 0 ) ) );
    }

    protected function tearDown(): void
    {
        foreach ( array( 'eZDBGlobalInstance', 'eZContentLanguagePrioritizedLanguages', 'eZContentClassObjectCache', 'eZUserGlobalInstance_' ) as $key )
        {
            if ( $this->saved[$key] === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $this->saved[$key][0];
        }
        self::columnsProperty()->setValue( null, $this->saved['columns'] );
    }

    private static function columnsProperty()
    {
        $property = new ReflectionProperty( 'eZContentObjectTrashNode', 'trashedByColumns' );
        if ( PHP_VERSION_ID < 80100 )
            $property->setAccessible( true );
        return $property;
    }

    /**
     * Whether $doc matches a MongoDB filter, with MongoDB's rules for a missing field: $and, $or, equality, $in,
     * $nin, the ranges and $exists.
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
                    case '$eq':  $ok = $has && $value == $operand; break;
                    case '$in':  $ok = $has && in_array( $value, $operand ); break;
                    case '$nin': $ok = !( $has && in_array( $value, $operand ) ); break;
                    case '$lte': $ok = $has && $value <= $operand; break;
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

    /** The trash documents: by user 14, one older than the column (whose trasher the old file names: 10), by nobody known, by 10 */
    private static function documents()
    {
        return array(
            array( 'node_id' => 52, 'contentobject_id' => 50, 'trashed' => 1000, 'trashed_by' => 14, 'trashed_via' => 'cli x.php' ),
            array( 'node_id' => 74, 'contentobject_id' => 72, 'trashed' => 2000 ),
            array( 'node_id' => 75, 'contentobject_id' => 73, 'trashed' => 3000, 'trashed_by' => 0, 'trashed_via' => '' ),
            array( 'node_id' => 76, 'contentobject_id' => 74, 'trashed' => 4000, 'trashed_by' => 10, 'trashed_via' => 'web admin' ),
        );
    }

    /** The objects of the documents a filter selects */
    private static function selected( array $params )
    {
        $filter = eZContentObjectTrashNode::trashListFilterMongo( $params );
        $ids = array();
        foreach ( self::documents() as $doc )
            if ( self::mongoMatches( $doc, $filter ) )
                $ids[] = $doc['contentobject_id'];
        return $ids;
    }

    /** Each filter selects what trashListFilterSQL() selects on a table where the old row holds trashed_by 0 */
    public function testTheFiltersSelectWhatTheSqlFiltersSelect()
    {
        $this->assertSame( array( 50, 72, 73, 74 ), self::selected( array() ) );
        $this->assertSame( array( 50 ), self::selected( array( 'TrashedBy' => 14, 'TrashedByFileObjectIDList' => array() ) ) );
        $this->assertSame( array( 72, 74 ), self::selected( array( 'TrashedBy' => 10, 'TrashedByFileObjectIDList' => array( 72 ) ) ) );
        $this->assertSame( array( 73 ), self::selected( array( 'TrashedByUnknown' => true, 'TrashedByFileObjectIDList' => array( 72 ) ) ),
                           'unknown: by nobody in the column, and not known from the old file' );
        $this->assertSame( array( 72, 73 ), self::selected( array( 'TrashedByUnknown' => true, 'TrashedByFileObjectIDList' => array() ) ),
                           'unknown includes a document without the field' );
        $this->assertSame( array( 72, 73 ), self::selected( array( 'TrashedFrom' => 1500, 'TrashedTo' => 3500 ) ) );
        $this->assertSame( array( 50, 72 ), self::selected( array( 'Trashed' => 2000 ) ) );
        $this->assertSame( array(), self::selected( array( 'ContentObjectIDList' => array() ) ) );
        $this->assertSame( array( 50, 74 ), self::selected( array( 'ContentObjectIDList' => array( 50, '74' ) ) ) );
        $this->assertSame( array( 72, 73 ), self::selected( array( 'ExcludeContentObjectIDList' => array( 50, 74 ) ) ) );
    }

    /** The sort keys and directions of createSortingSQLStrings(), as the view and the fetch functions pass them */
    public function testSort()
    {
        $this->assertSame( array( 'name' => 1, 'node_id' => 1 ), eZContentObjectTrashNode::trashListSortMongo( array( array( 'name' ) ) ) );
        $this->assertSame( array( 'trashed' => -1, 'node_id' => 1 ), eZContentObjectTrashNode::trashListSortMongo( array( 'trashed', '0' ) ) );
        $this->assertSame( array( 'trashed' => 1, 'node_id' => 1 ), eZContentObjectTrashNode::trashListSortMongo( array( 'trashed', '1' ) ) );
        $this->assertSame( array( 'contentclass_name' => -1, 'node_id' => 1 ), eZContentObjectTrashNode::trashListSortMongo( array( array( 'class_name', false ) ) ) );
        $this->assertSame( array( 'section_id' => 1, 'published' => -1, 'node_id' => 1 ),
                           eZContentObjectTrashNode::trashListSortMongo( array( array( 'section', true ), array( 'published', false ) ) ) );
        $this->assertSame( array( 'path_string' => 1, 'node_id' => 1 ), eZContentObjectTrashNode::trashListSortMongo( array( array( 'nonsense' ) ) ) );
    }

    /** The list and the count come from one aggregation over the trash, with the view's filter in front */
    public function testTheListAndTheCountOnMongoDB()
    {
        $rows = array(
            array( 'node_id' => 74, 'contentobject_id' => 72, 'contentobject_version' => 1, 'trashed' => 2000, 'trashed_by' => 0, 'trashed_via' => '',
                   'path_string' => '/1/2/74/', 'name' => 'Old', 'class_identifier' => 'image' ),
            array( 'node_id' => 75, 'contentobject_id' => 73, 'contentobject_version' => 1, 'trashed' => 3000, 'trashed_by' => 0, 'trashed_via' => '',
                   'path_string' => '/1/2/75/', 'name' => 'Unknown', 'class_identifier' => 'image' ),
        );
        $this->db->aggregateRows['ezcontentobject_trash'] = $rows;
        $params = array( 'Limitation' => array(), 'TrashedByUnknown' => true, 'TrashedByFileObjectIDList' => array(),
                         'SortBy' => array( 'trashed', '0' ), 'Offset' => 10, 'Limit' => 5 );

        $this->assertSame( 2, eZContentObjectTrashNode::trashListCount( $params ) );
        $nodes = eZContentObjectTrashNode::trashList( $params );
        $this->assertCount( 2, $nodes );
        $this->assertInstanceOf( 'eZContentObjectTrashNode', $nodes[0] );
        $this->assertSame( 74, (int)$nodes[0]->attribute( 'node_id' ) );
        $this->assertSame( 0, (int)$nodes[0]->attribute( 'trashed_by' ) );
        $this->assertSame( $rows, eZContentObjectTrashNode::trashList( array_merge( $params, array( 'AsObject' => false ) ) ) );

        $aggregations = array_values( array_filter( $this->db->calls, function ( $call ) { return $call[0] === 'aggregate'; } ) );
        $this->assertCount( 3, $aggregations, 'nothing but aggregations' );
        $this->assertCount( 3, $this->db->calls );
        $list = $aggregations[1][2];
        $this->assertSame( 'ezcontentobject_trash', $aggregations[1][1] );
        $this->assertSame( array( '$match' => eZContentObjectTrashNode::trashListFilterMongo( $params ) ), $list[0], 'the filter first' );
        $stages = array_map( function ( $stage ) { return key( $stage ); }, $list );
        $this->assertSame( array( '$sort', '$skip', '$limit' ), array_slice( $stages, -3 ) );
        $this->assertSame( array( '$sort' => array( 'trashed' => -1, 'node_id' => 1 ) ), $list[count( $list ) - 3] );
        $this->assertSame( array( '$skip' => 10 ), $list[count( $list ) - 2] );
        $this->assertSame( array( '$count' => 'count' ), end( $aggregations[0][2] ) );
    }

    /** A user whose content/read is limited gets no trash rather than all of it; an AttributeFilter neither */
    public function testWhatCannotBeAppliedListsNothing()
    {
        $this->db->aggregateRows['ezcontentobject_trash'] = array( array( 'node_id' => 1, 'contentobject_id' => 1 ) );
        $limited = array( 'Limitation' => array( array( 'Section' => array( 1 ) ) ) );
        $this->assertSame( 0, eZContentObjectTrashNode::trashListCount( $limited ) );
        $this->assertSame( array(), eZContentObjectTrashNode::trashList( $limited ) );
        $filtered = array( 'Limitation' => array(), 'AttributeFilter' => array( array( 'name', '=', 'x' ) ) );
        $this->assertSame( 0, eZContentObjectTrashNode::trashListCount( $filtered ) );
        $this->assertSame( array(), eZContentObjectTrashNode::trashList( $filtered ) );
        $this->assertSame( array(), $this->db->calls, 'nothing was asked of the database' );
    }

    /** The class filter's options come from two single-table reads, no join */
    public function testClassOptionsOnMongoDB()
    {
        $this->db->queryRows = array( 'SELECT contentobject_id FROM ezcontentobject_trash' => array( array( 'contentobject_id' => 50 ), array( 'contentobject_id' => 72 ) ),
                                      'SELECT contentclass_id FROM ezcontentobject WHERE' => array( array( 'contentclass_id' => 9001 ), array( 'contentclass_id' => 9001 ) ) );
        $GLOBALS['eZContentClassObjectCache'][9001] = new eZContentClass( array( 'id' => 9001, 'version' => 0, 'identifier' => 'pr330_test',
            'serialized_name_list' => serialize( array( 'eng-GB' => 'Test class', 'always-available' => 'eng-GB' ) ) ) );
        $options = Exponential\Service\TrashList::classOptions();
        $this->assertCount( 1, $options );
        $this->assertSame( 9001, $options[0]['id'] );
        $this->assertSame( 'Test class', $options[0]['name'] );
        $this->assertSame( 'SELECT contentclass_id FROM ezcontentobject WHERE id IN ( 50, 72 )', $this->db->calls[1][1] );
        foreach ( $this->db->calls as $call )
            $this->assertDoesNotMatchRegularExpression( '/\bFROM\s+\w+\s*,|\bJOIN\b/i', $call[1], 'single-table SQL only' );
    }

    /** On MongoDB the columns are there unless the schema declares the trash table without them */
    public function testHasTrashedByColumnsOnMongoDB()
    {
        $this->db->declared = array( 'trashed_by' => array(), 'trashed_via' => array(), 'node_id' => array() );
        $this->assertTrue( eZContentObjectTrashNode::hasTrashedByColumns( true ) );
        $this->db->declared = array();
        $this->assertTrue( eZContentObjectTrashNode::hasTrashedByColumns( true ), 'no schema read: as before, the documents can hold them' );
        $this->db->declared = array( 'node_id' => array() );
        $this->assertFalse( eZContentObjectTrashNode::hasTrashedByColumns( true ), 'a schema of the table without them' );
        $this->assertSame( array(), $this->db->calls, 'no SQL catalogue query on MongoDB' );
    }
}
