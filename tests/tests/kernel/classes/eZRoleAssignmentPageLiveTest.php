<?php
/**
 * The users and user groups of a role a page at a time, for role/view: eZRole::assignmentCount() and
 * eZRole::assignmentPage(). Live style: on the installation the tests run on, with a role of its own that has no
 * policies (so the users it is assigned to gain nothing) and is removed with its assignments afterwards; where there
 * is no installation (CI) the tests are skipped.
 *
 *  RA-01 - The count is every assignment, a limited one and one whose object is gone counted on their own
 *  RA-02 - The pages are sorted (gone first, then by name without case, then by assignment) and hold every one once
 *  RA-03 - A page has the form of fetchUserByRole(), with the user id, main node and limitation node added
 *  RA-04 - An offset past the end gives an empty page; a negative offset or a limit below one is corrected
 *  RA-05 - An assignment whose object is gone is counted, listed first without an object and does not break a page
 *  RA-06 - The name filter counts and pages only matching names, without case; % and _ match only themselves
 *  RA-07 - The role list gets the count of every role in one call, 0 for a role without assignments
 *  RA-08 - A page costs the same few queries whatever its size (no query per assignment)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZRoleAssignmentPageLiveTest extends PHPUnit\Framework\TestCase
{
    private static $installation;
    private $role;
    private $assigned = array();
    private $goneID;
    private $names = array();

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 4 );
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        $this->role = eZRole::create( 'X1 assignment page ' . bin2hex( random_bytes( 4 ) ) );
        $this->role->store();

        // Existing users and groups; the role has no policies, so assigning it changes no one's access
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT o.id, o.name FROM ezcontentobject o, ezcontentclass c
                                  WHERE o.contentclass_id = c.id AND c.version = 0 AND o.status = 1
                                    AND c.identifier IN ( 'user', 'user_group' )
                                  ORDER BY o.id", array( 'limit' => 6 ) );
        if ( count( $rows ) < 3 )
        {
            $this->markTestSkipped( 'needs three users or user groups' );
        }
        foreach ( $rows as $row )
        {
            $this->role->assignToUser( (int)$row['id'] );
            $this->assigned[] = (int)$row['id'];
            $this->names[(int)$row['id']] = (string)$row['name'];
        }
        // A limited assignment of the first one counts on its own
        $this->role->assignToUser( $this->assigned[0], 'subtree', 2 );

        // And one whose object is gone: an id past the highest object
        $max = $db->arrayQuery( 'SELECT MAX(id) AS max_id FROM ezcontentobject' );
        $this->goneID = (int)$max[0]['max_id'] + 100000;
        $this->role->assignToUser( $this->goneID );
    }

    protected function tearDown(): void
    {
        if ( $this->role instanceof eZRole )
        {
            $this->role->removeThis();
        }
        eZRole::expireCache();
    }

    private function keys( array $page )
    {
        $keys = array();
        foreach ( $page as $assignment )
        {
            $name = $assignment['user_object'] instanceof eZContentObject ? $assignment['user_object']->attribute( 'name' ) : '';
            $keys[] = $assignment['user_id'] . '#' . $assignment['user_role_id'];
        }
        return $keys;
    }

    private function storedName( $userID )
    {
        return isset( $this->names[$userID] ) ? $this->names[$userID] : null;
    }

    /** RA-01 */
    public function testTheCountIsEveryAssignment()
    {
        $this->assertSame( count( $this->assigned ) + 2, $this->role->assignmentCount() );
        $this->assertSame( count( $this->role->fetchUserByRole() ), $this->role->assignmentCount() );
    }

    /** RA-02 */
    public function testThePagesAreSortedAndHoldEveryAssignmentOnce()
    {
        $all = array();
        $count = $this->role->assignmentCount();
        for ( $offset = 0; $offset < $count; $offset += 2 )
        {
            $page = $this->role->assignmentPage( $offset, 2 );
            $this->assertLessThanOrEqual( 2, count( $page ) );
            $all = array_merge( $all, $this->keys( $page ) );
        }
        $this->assertCount( $count, $all );
        $this->assertSame( $all, array_values( array_unique( $all ) ) );

        // The expected order from the rows and the names stored with the objects
        $expected = array();
        foreach ( $this->role->fetchUserByRole() as $assignment )
        {
            $userID = $assignment['user_object'] instanceof eZContentObject ? (int)$assignment['user_object']->attribute( 'id' ) : $this->goneID;
            $expected[] = eZRole::assignmentRow( $userID, $assignment['user_role_id'], '', '', $userID === $this->goneID ? null : $this->storedName( $userID ) );
        }
        $expected = array_map( function ( $row ) { return $row['user_id'] . '#' . $row['id']; }, eZRole::sortAssignmentRows( $expected ) );
        $this->assertSame( $expected, $all );
    }

    /** RA-03 */
    public function testAPageHasTheFormOfFetchUserByRole()
    {
        $page = $this->role->assignmentPage( 0, 100 );
        foreach ( array( 'user_object', 'user_role_id', 'limit_ident', 'limit_value', 'user_id', 'user_name', 'main_node_id', 'limit_node', 'limit_section' ) as $key )
            $this->assertArrayHasKey( $key, $page[0] );
        $limited = array_values( array_filter( $page, function ( $a ) { return $a['limit_ident'] !== ''; } ) );
        $this->assertCount( 1, $limited );
        $this->assertSame( 'Subtree', $limited[0]['limit_ident'] );
        $this->assertSame( $this->assigned[0], (int)$limited[0]['user_object']->attribute( 'id' ) );
        $this->assertInstanceOf( 'eZContentObjectTreeNode', $limited[0]['limit_node'] );
        $this->assertSame( 2, (int)$limited[0]['limit_node']->attribute( 'node_id' ) );
        foreach ( $page as $assignment )
        {
            if ( $assignment['user_object'] instanceof eZContentObject )
            {
                $this->assertSame( (int)$assignment['user_object']->attribute( 'main_node_id' ), $assignment['main_node_id'] );
                $this->assertSame( (string)$assignment['user_object']->attribute( 'name' ), $assignment['user_name'] );
            }
        }
    }

    /** RA-04 */
    public function testOffsetsAndLimitsAreCorrected()
    {
        $count = $this->role->assignmentCount();
        $this->assertSame( array(), $this->role->assignmentPage( $count, 10 ) );
        $this->assertSame( $this->keys( $this->role->assignmentPage( 0, 2 ) ), $this->keys( $this->role->assignmentPage( -5, 2 ) ) );
        $this->assertCount( 1, $this->role->assignmentPage( 0, 0 ) );
        $this->assertCount( 1, $this->role->assignmentPage( '0', '1; DROP TABLE ezuser_role' ) );
    }

    /** RA-05 */
    public function testAnAssignmentWhoseObjectIsGoneIsCountedAndListedFirst()
    {
        $this->assertSame( 1, $this->role->assignmentOrphanCount() );
        $first = $this->role->assignmentPage( 0, 1 );
        $this->assertCount( 1, $first );
        $this->assertNull( $first[0]['user_object'] );
        $this->assertSame( $this->goneID, $first[0]['user_id'] );
        $this->assertSame( 0, $first[0]['main_node_id'] );
        $this->assertGreaterThan( 0, (int)$first[0]['user_role_id'] );
    }

    /** RA-06 */
    public function testTheNameFilter()
    {
        $name = $this->storedName( $this->assigned[1] );
        if ( $name === null || mb_strlen( $name ) < 3 )
            $this->markTestSkipped( 'the second user or group needs a name of three characters' );
        // A part of the name in the other case
        $part = mb_strtoupper( mb_substr( $name, 1, 3 ) );
        $expected = 0;
        foreach ( $this->assigned as $userID )
        {
            if ( mb_stripos( $this->storedName( $userID ), mb_substr( $name, 1, 3 ) ) !== false )
                $expected += $userID === $this->assigned[0] ? 2 : 1;
        }
        $this->assertSame( $expected, $this->role->assignmentCount( $part ) );
        $page = $this->role->assignmentPage( 0, 100, $part );
        $this->assertCount( $expected, $page );
        foreach ( $page as $assignment )
        {
            $this->assertInstanceOf( 'eZContentObject', $assignment['user_object'] );
        }
        // The gone one never matches, and wildcards match only themselves
        $this->assertSame( 0, $this->role->assignmentCount( '%' ) );
        $this->assertSame( 0, $this->role->assignmentCount( '_' ) );
        $this->assertSame( array(), $this->role->assignmentPage( 0, 10, "no such name o'brien" ) );
        // A filter that normalises to nothing is no filter
        $this->assertSame( $this->role->assignmentCount(), $this->role->assignmentCount( ' / ' ) );
    }

    /** RA-07 */
    public function testTheRoleListCountsEveryRoleInOneCall()
    {
        $empty = eZRole::create( 'X1 assignment page empty ' . bin2hex( random_bytes( 4 ) ) );
        $empty->store();
        try
        {
            $counts = eZRole::assignmentCounts( array( $this->role->attribute( 'id' ), $empty->attribute( 'id' ), 0, 'x' ) );
            $this->assertSame( array( (int)$this->role->attribute( 'id' ) => $this->role->assignmentCount(),
                                      (int)$empty->attribute( 'id' ) => 0 ), $counts );
            $this->assertSame( array(), eZRole::assignmentCounts( array() ) );
        }
        finally
        {
            $empty->removeThis();
        }
    }

    /** RA-08 */
    public function testAPageCostsTheSameQueriesWhateverItsSize()
    {
        // The drivers count the queries they report, and report them while SQL output is on
        $db = eZDB::instance();
        if ( !property_exists( $db, 'NumQueries' ) || $db->databaseName() === 'mongo' )
            $this->markTestSkipped( 'the driver does not count its queries' );
        // Once, so caches of classes and sections are warm for both measurements
        $this->role->assignmentPage( 0, 100 );

        $output = $db->OutputSQL;
        $db->OutputSQL = true;
        try
        {
            $before = (int)$db->NumQueries;
            $this->role->assignmentPage( 0, 2 );
            $small = (int)$db->NumQueries - $before;

            $before = (int)$db->NumQueries;
            $this->role->assignmentPage( 0, 100 );
            $large = (int)$db->NumQueries - $before;
        }
        finally
        {
            $db->OutputSQL = $output;
        }

        $this->assertGreaterThan( 0, $small );
        $this->assertLessThanOrEqual( $small + 1, $large, "a page of every assignment took $large queries, a page of two $small" );
        $this->assertLessThanOrEqual( 6, $large );
    }
}
