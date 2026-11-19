<?php
/**
 * How many people a role reaches (expRolePage::affected()), against a database that records the queries.
 *
 *  RA-01 - Large id lists are asked in IN lists of at most expDBInList::chunkSize() ids, and the users are counted right
 *  RA-02 - Past AFFECTED_GROUP_LIMIT groups the groups' locations are not read; the groups are still counted
 *  RA-03 - Up to the limit the users of the groups are counted, each user once
 *  RA-04 - A failing query leaves the users unknown instead of counting from a failed result
 *  RA-05 - 1000 ids take one query, 1001 two
 *  RA-06 - Exactly AFFECTED_GROUP_LIMIT groups are still counted through
 *  RA-07 - A user in two groups, assigned directly too and found past a chunk boundary, counts once
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expRolePageAffectedTestDB extends eZNullDB
{
    public $queries = array();
    /** @var int[] the ids of ezuser */
    public $userIDs = array();
    /** @var array group id => path_string */
    public $groupPaths = array();
    /** @var int[] users under the groups' paths */
    public $members = array();
    /** @var int[] ezuser_role rows: contentobject ids */
    public $assigned = array();
    /** @var string|null a query starting with this fails */
    public $failOn = null;

    public function __construct()
    {
    }

    function databaseName()
    {
        return 'mysql';
    }

    function generateSQLINStatement( $elements, $columnName = '', $not = false, $unique = true, $type = false )
    {
        return $columnName . ' IN ( ' . implode( ', ', array_map( 'intval', $elements ) ) . ' )';
    }

    function escapeString( $str )
    {
        return addslashes( (string)$str );
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        $this->queries[] = $sql;
        if ( $this->failOn !== null && strpos( $sql, $this->failOn ) === 0 )
            return false;
        $ids = self::inList( $sql );
        if ( strpos( $sql, 'SELECT contentobject_id, limit_identifier FROM ezuser_role' ) === 0 )
        {
            $rows = array();
            foreach ( $this->assigned as $id )
                $rows[] = array( 'contentobject_id' => $id, 'limit_identifier' => '' );
            return $rows;
        }
        if ( strpos( $sql, 'SELECT contentobject_id FROM ezuser WHERE' ) === 0 )
        {
            $rows = array();
            foreach ( array_intersect( $ids, $this->userIDs ) as $id )
                $rows[] = array( 'contentobject_id' => $id );
            return $rows;
        }
        if ( strpos( $sql, 'SELECT path_string FROM ezcontentobject_tree WHERE' ) === 0 )
        {
            $rows = array();
            foreach ( $ids as $id )
            {
                if ( isset( $this->groupPaths[$id] ) )
                    $rows[] = array( 'path_string' => $this->groupPaths[$id] );
            }
            return $rows;
        }
        if ( strpos( $sql, 'SELECT DISTINCT u.contentobject_id' ) === 0 )
        {
            $rows = array();
            foreach ( $this->members as $id )
                $rows[] = array( 'contentobject_id' => $id );
            return $rows;
        }
        return array();
    }

    /** @return int[] the ids of the IN list of a query */
    public static function inList( $sql )
    {
        return preg_match( '/ IN \( ([0-9, ]*) \)/', $sql, $m ) ? array_map( 'intval', explode( ', ', $m[1] ) ) : array();
    }

    /** @return string[] the queries that start with $start */
    public function queriesStartingWith( $start )
    {
        return array_values( array_filter( $this->queries, function ( $sql ) use ( $start ) { return strpos( $sql, $start ) === 0; } ) );
    }
}

class expRolePageAffectedTest extends PHPUnit\Framework\TestCase
{
    private $hadDB;
    private $previousDB;
    /** @var expRolePageAffectedTestDB */
    private $db;

    protected function setUp(): void
    {
        // the instance as it is, without creating one (eZDB::instance() would connect)
        $this->hadDB = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $this->previousDB = $this->hadDB ? $GLOBALS['eZDBGlobalInstance'] : null;
        $this->db = new expRolePageAffectedTestDB();
        eZDB::setInstance( $this->db );
        // the counts below are for lists of 1000 ids, whatever site.ini says
        expDBInList::setChunkSize( 1000 );
    }

    protected function tearDown(): void
    {
        expDBInList::setChunkSize( null );
        if ( $this->hadDB )
            eZDB::setInstance( $this->previousDB );
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
    }

    private static function role()
    {
        return new eZRole( array( 'id' => 7, 'name' => 'Test' ) );
    }

    public function testRA01LargeIDListsAreChunked()
    {
        $this->db->assigned = range( 1, 2500 );
        $this->db->userIDs = range( 1, 2500 );
        $result = expRolePage::affected( self::role() );

        $userQueries = $this->db->queriesStartingWith( 'SELECT contentobject_id FROM ezuser WHERE' );
        $this->assertCount( 3, $userQueries );
        foreach ( $userQueries as $sql )
            $this->assertLessThanOrEqual( expDBInList::chunkSize( $this->db ), count( expRolePageAffectedTestDB::inList( $sql ) ) );
        $this->assertSame( 2500, $result['users_direct'] );
        $this->assertSame( 0, $result['groups'] );
        $this->assertSame( 2500, $result['users_total'] );
        $this->assertSame( array(), $this->db->queriesStartingWith( 'SELECT path_string' ) );
    }

    public function testRA02NoLocationsPastTheGroupLimit()
    {
        $groups = expRolePage::AFFECTED_GROUP_LIMIT + 1;
        $this->db->assigned = range( 1, $groups + 5 );
        $this->db->userIDs = range( $groups + 1, $groups + 5 );
        $result = expRolePage::affected( self::role() );

        $this->assertSame( array(), $this->db->queriesStartingWith( 'SELECT path_string' ) );
        $this->assertSame( array(), $this->db->queriesStartingWith( 'SELECT DISTINCT' ) );
        $this->assertSame( $groups, $result['groups'] );
        $this->assertSame( 5, $result['users_direct'] );
        $this->assertNull( $result['users_total'] );
    }

    public function testRA03UsersOfTheGroupsCountedOnce()
    {
        $this->db->assigned = array( 10, 11, 100 );
        $this->db->userIDs = array( 100, 200, 201 );
        $this->db->groupPaths = array( 10 => '/1/5/10/', 11 => '/1/5/11/' );
        $this->db->members = array( 100, 200, 201 );
        $result = expRolePage::affected( self::role() );

        $this->assertSame( 1, $result['users_direct'] );
        $this->assertSame( 2, $result['groups'] );
        $this->assertSame( 3, $result['users_total'] );
        $members = $this->db->queriesStartingWith( 'SELECT DISTINCT' );
        $this->assertCount( 1, $members );
        $this->assertStringContainsString( "t.path_string LIKE '/1/5/10/%' OR t.path_string LIKE '/1/5/11/%'", $members[0] );
    }

    public function testRA04FailedQueryLeavesUsersUnknown()
    {
        $this->db->assigned = array( 10, 100 );
        $this->db->userIDs = array( 100 );
        $this->db->groupPaths = array( 10 => '/1/5/10/' );

        // the failing query => users_direct, groups as far as they were known
        foreach ( array( 'SELECT contentobject_id, limit_identifier' => array( 0, 0 ),
                         'SELECT contentobject_id FROM ezuser WHERE' => array( 0, 0 ),
                         'SELECT path_string' => array( 1, 1 ),
                         'SELECT DISTINCT' => array( 1, 1 ) ) as $failing => $known )
        {
            $this->db->queries = array();
            $this->db->failOn = $failing;
            $result = expRolePage::affected( self::role() );
            $this->assertNull( $result['users_total'], $failing );
            $this->assertSame( $known[0], $result['users_direct'], $failing );
            $this->assertSame( $known[1], $result['groups'], $failing );
        }
    }

    public function testRA05ChunkBoundary()
    {
        foreach ( array( 1000 => 1, 1001 => 2 ) as $count => $queries )
        {
            $this->db->queries = array();
            $this->db->assigned = range( 1, $count );
            $this->db->userIDs = range( 1, $count );
            $result = expRolePage::affected( self::role() );
            $this->assertCount( $queries, $this->db->queriesStartingWith( 'SELECT contentobject_id FROM ezuser WHERE' ), (string)$count );
            $this->assertSame( $count, $result['users_direct'] );
            $this->assertSame( $count, $result['users_total'] );
        }
    }

    public function testRA06ExactlyTheGroupLimit()
    {
        $groups = expRolePage::AFFECTED_GROUP_LIMIT;
        $this->db->assigned = range( 1, $groups );
        foreach ( range( 1, $groups ) as $id )
            $this->db->groupPaths[$id] = '/1/5/' . $id . '/';
        $this->db->members = array( 5000, 5001 );
        $result = expRolePage::affected( self::role() );

        $this->assertSame( $groups, $result['groups'] );
        $this->assertCount( 1, $this->db->queriesStartingWith( 'SELECT path_string' ) );
        $this->assertCount( 1, $this->db->queriesStartingWith( 'SELECT DISTINCT' ) );
        $this->assertSame( 2, $result['users_total'] );
    }

    public function testRA07UserCountedOnce()
    {
        // 1500 direct users (two chunks) and two groups; user 1200 is in both groups and assigned directly
        $this->db->assigned = array_merge( range( 1, 1500 ), array( 9001, 9002 ) );
        $this->db->userIDs = range( 1, 1500 );
        $this->db->groupPaths = array( 9001 => '/1/5/9001/', 9002 => '/1/5/9002/' );
        $this->db->members = array( 1200, 1200, 1600 );
        $result = expRolePage::affected( self::role() );

        $this->assertCount( 2, $this->db->queriesStartingWith( 'SELECT contentobject_id FROM ezuser WHERE' ) );
        $this->assertSame( 1500, $result['users_direct'] );
        $this->assertSame( 2, $result['groups'] );
        $this->assertSame( 1501, $result['users_total'] );
    }
}
