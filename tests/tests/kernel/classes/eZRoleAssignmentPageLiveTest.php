<?php
/**
 * The users and user groups of a role a page at a time, for role/view: eZRole::assignmentCount() and
 * eZRole::assignmentPage(). Live style: on the installation the tests run on, with a role of its own that has no
 * policies (so the users it is assigned to gain nothing) and is removed with its assignments afterwards; where there
 * is no installation (CI) the tests are skipped.
 *
 *  RA-01 - The count is every assignment, a limited one counted on its own
 *  RA-02 - The pages are sorted by name, then by assignment, and together hold every assignment once
 *  RA-03 - A page has the form of fetchUserByRole()
 *  RA-04 - An offset past the end gives an empty page; a negative offset or a limit below one is corrected
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
        $rows = eZDB::instance()->arrayQuery( "SELECT o.id FROM ezcontentobject o, ezcontentclass c
                                               WHERE o.contentclass_id = c.id AND c.version = 0 AND o.status = 1
                                                 AND c.identifier IN ( 'user', 'user_group' )
                                               ORDER BY o.id", array( 'limit' => 5 ) );
        if ( count( $rows ) < 3 )
        {
            $this->markTestSkipped( 'needs three users or user groups' );
        }
        foreach ( $rows as $row )
        {
            $this->role->assignToUser( (int)$row['id'] );
            $this->assigned[] = (int)$row['id'];
        }
        // A limited assignment of the first one counts on its own
        $this->role->assignToUser( $this->assigned[0], 'subtree', 2 );
    }

    protected function tearDown(): void
    {
        if ( $this->role instanceof eZRole )
        {
            $this->role->removeThis();
        }
        eZRole::expireCache();
    }

    private function names( array $page )
    {
        $names = array();
        foreach ( $page as $assignment )
        {
            $names[] = $assignment['user_object']->attribute( 'name' ) . '#' . $assignment['user_role_id'];
        }
        return $names;
    }

    /** RA-01 */
    public function testTheCountIsEveryAssignment()
    {
        $this->assertSame( count( $this->assigned ) + 1, $this->role->assignmentCount() );
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
            $all = array_merge( $all, $this->names( $page ) );
        }
        $this->assertCount( $count, $all );
        $this->assertSame( $all, array_values( array_unique( $all ) ) );

        $expected = $this->names( $this->role->fetchUserByRole() );
        usort( $expected, function ( $a, $b )
        {
            list( $nameA, $idA ) = explode( '#', $a );
            list( $nameB, $idB ) = explode( '#', $b );
            return strcmp( $nameA, $nameB ) ?: ( (int)$idA <=> (int)$idB );
        } );
        $this->assertSame( $expected, $all );
    }

    /** RA-03 */
    public function testAPageHasTheFormOfFetchUserByRole()
    {
        $page = $this->role->assignmentPage( 0, 100 );
        $this->assertSame( array( 'user_object', 'user_role_id', 'limit_ident', 'limit_value' ), array_keys( $page[0] ) );
        $limited = array_values( array_filter( $page, function ( $a ) { return $a['limit_ident'] !== ''; } ) );
        $this->assertCount( 1, $limited );
        $this->assertSame( 'Subtree', $limited[0]['limit_ident'] );
        $this->assertSame( $this->assigned[0], (int)$limited[0]['user_object']->attribute( 'id' ) );
    }

    /** RA-04 */
    public function testOffsetsAndLimitsAreCorrected()
    {
        $count = $this->role->assignmentCount();
        $this->assertSame( array(), $this->role->assignmentPage( $count, 10 ) );
        $this->assertSame( $this->names( $this->role->assignmentPage( 0, 2 ) ), $this->names( $this->role->assignmentPage( -5, 2 ) ) );
        $this->assertCount( 1, $this->role->assignmentPage( 0, 0 ) );
        $this->assertCount( 1, $this->role->assignmentPage( '0', '1; DROP TABLE ezuser_role' ) );
    }
}
