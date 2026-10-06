<?php
/**
 * The access array of a policy while eZRole::accessArrayByUserID() has the rows loaded ahead, without the database:
 * the limitation part computed once per policy and reused for every assignment (eZPolicy::prefetchedAccessArray()) is
 * the same as the one limitationList() builds from the same rows.
 *
 *  PP-01 - A policy without limitations, not assigned with a limitation: unlimited ('*')
 *  PP-02 - A policy with limitations, not assigned with a limitation
 *  PP-03 - Assigned for a subtree or a section: the User_ limitation is added last, the policy name carries the
 *          assignment, also for a policy without limitations of its own
 *  PP-04 - The limitation part is computed once per policy and reused for the next assignment
 *  PP-05 - A policy with a limitation of the assignment's identifier goes the way of limitationList(), which narrows it
 *  PP-06 - Merging the parts in one call gives what merging them one by one gave
 *  PP-07 - A policy whose rows were not loaded ahead (not of the roles being built) is not answered from the rows:
 *          prefetchedAccessArray() declines and limitationList() would ask the database; the same for a limitation
 *  PP-08 - The limitation an assignment adds is answered from the rows (id -1), not asked of the database once per
 *          policy and assignment
 *  PP-09 - A build inside a build gets the outer one its rows back afterwards, and the last one leaves none behind
 *  PP-10 - The ids of one IN () list stay below Oracle's limit of 1000
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

require_once __DIR__ . '/fixtures/ezcontentpermissionsqltestdb.php';

class eZPolicyPrefetchedAccessArrayTest extends PHPUnit\Framework\TestCase
{
    /** @var eZDBInterface|null the database before a test put a recording one in place */
    private $previousDB;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->previousDB = $GLOBALS['eZDBGlobalInstance'] ?? null;
        eZPolicy::$prefetchedLimitationRows = array(
            901 => array(),
            902 => array( array( 'id' => 9021, 'policy_id' => 902, 'identifier' => 'Class' ),
                          array( 'id' => 9022, 'policy_id' => 902, 'identifier' => 'Section' ) ),
            903 => array( array( 'id' => 9031, 'policy_id' => 903, 'identifier' => 'User_Subtree' ) ),
        );
        eZPolicyLimitation::$prefetchedValueRows = array(
            9021 => array( array( 'id' => 1, 'limitation_id' => 9021, 'value' => '16' ), array( 'id' => 2, 'limitation_id' => 9021, 'value' => '2' ) ),
            9022 => array( array( 'id' => 3, 'limitation_id' => 9022, 'value' => '1' ) ),
            9031 => array( array( 'id' => 4, 'limitation_id' => 9031, 'value' => '/1/2/' ) ),
            // The limitation an assignment adds (User_Subtree, User_Section) is built with id -1 and has no rows
            -1 => array(),
        );
        eZPolicy::$prefetchedLimitArrays = array();
    }

    protected function tearDown(): void
    {
        eZPolicy::$prefetchedLimitationRows = null;
        eZPolicy::$prefetchedLimitArrays = array();
        eZPolicyLimitation::$prefetchedValueRows = null;
        eZRole::$prefetchedPolicyRows = null;
        eZDB::setInstance( $this->previousDB );
    }

    /** A policy as policyList() makes it, assigned with $limit = array( identifier, value, user role id ) or not */
    private function policy( $id, array $limit = array() )
    {
        $policy = new eZPolicy( array( 'id' => $id, 'role_id' => 77, 'module_name' => 'content', 'function_name' => 'read', 'original_id' => 0 ) );
        if ( $limit )
        {
            $policy->setAttribute( 'limit_identifier', 'User_' . $limit[0] );
            $policy->setAttribute( 'limit_value', $limit[1] );
            $policy->setAttribute( 'user_role_id', $limit[2] );
        }
        return $policy;
    }

    /** The access array the way of limitationList() gives, from the same rows */
    private function byLimitationList( $id, array $limit = array() )
    {
        $policy = $this->policy( $id, $limit );
        $policy->limitationList();
        return $policy->accessArray();
    }

    private function prefetched( $id, array $limit = array() )
    {
        return $this->policy( $id, $limit )->accessArray();
    }

    /** PP-01 */
    public function testUnlimitedPolicy()
    {
        $this->assertSame( array( 'content' => array( 'read' => array( '*' => '*' ) ) ), $this->prefetched( 901 ) );
        $this->assertSame( $this->byLimitationList( 901 ), $this->prefetched( 901 ) );
    }

    /** PP-02 */
    public function testPolicyWithLimitations()
    {
        $this->assertSame( array( 'content' => array( 'read' => array( 'p_902' => array( 'Class' => array( '16', '2' ), 'Section' => array( '1' ) ) ) ) ),
                           $this->prefetched( 902 ) );
        $this->assertSame( $this->byLimitationList( 902 ), $this->prefetched( 902 ) );
    }

    /** PP-03 */
    public function testAssignedWithALimitation()
    {
        foreach ( array( array( 'Subtree', '/1/2/58/', 555 ), array( 'Section', '3', 556 ) ) as $limit )
        {
            $this->assertSame( $this->byLimitationList( 902, $limit ), $this->prefetched( 902, $limit ) );
            $this->assertSame( $this->byLimitationList( 901, $limit ), $this->prefetched( 901, $limit ) );
        }
        $this->assertSame( array( 'content' => array( 'read' => array( 'p_902_555' => array( 'Class' => array( '16', '2' ), 'Section' => array( '1' ),
                                                                                             'User_Subtree' => array( '/1/2/58/' ) ) ) ) ),
                           $this->prefetched( 902, array( 'Subtree', '/1/2/58/', 555 ) ) );
        $this->assertSame( array( 'content' => array( 'read' => array( 'p_901_555' => array( 'User_Subtree' => array( '/1/2/58/' ) ) ) ) ),
                           $this->prefetched( 901, array( 'Subtree', '/1/2/58/', 555 ) ) );
    }

    /** PP-04 */
    public function testTheLimitationPartIsComputedOncePerPolicy()
    {
        $this->prefetched( 902, array( 'Subtree', '/1/2/58/', 555 ) );
        $this->assertArrayHasKey( 902, eZPolicy::$prefetchedLimitArrays );
        // Changing the rows now shows that the next assignment reuses the part computed before
        eZPolicyLimitation::$prefetchedValueRows[9022] = array();
        $second = $this->prefetched( 902, array( 'Subtree', '/1/2/60/', 557 ) );
        $this->assertSame( array( '1' ), $second['content']['read']['p_902_557']['Section'] );
    }

    /** PP-05 */
    public function testALimitationOfTheAssignmentsIdentifierGoesTheWayOfLimitationList()
    {
        foreach ( array( array( 'Subtree', '/1/2/58/', 555 ), array( 'Subtree', '/1/5/', 556 ), array( 'Subtree', '/1/', 557 ) ) as $limit )
        {
            $policy = $this->policy( 903, $limit );
            $this->assertNull( ( new ReflectionMethod( 'eZPolicy', 'prefetchedAccessArray' ) )->invoke( $policy ) );
            $this->assertSame( $this->byLimitationList( 903, $limit ), $this->prefetched( 903, $limit ), $limit[1] );
        }
    }

    /** PP-06 */
    public function testMergingInOneCallIsMergingOneByOne()
    {
        $parts = array( $this->prefetched( 902 ), $this->prefetched( 901, array( 'Subtree', '/1/2/', 5 ) ), $this->prefetched( 902, array( 'Section', '1', 6 ) ),
                        array( 'user' => array( 'login' => array( '*' => '*' ) ) ), array( 'content' => array( '*' => array( '*' => '*' ) ) ) );
        $oneByOne = array();
        foreach ( $parts as $part )
        {
            $oneByOne = array_merge_recursive( $oneByOne, $part );
        }
        $this->assertSame( $oneByOne, array_merge_recursive( ...$parts ) );
    }

    /** A database that runs nothing and records what it was asked */
    private function recordingDB()
    {
        $db = new class extends eZContentPermissionSQLTestDB {
            public $asked = array();
            function arrayQuery( $sql, $params = array(), $server = false )
            {
                $this->asked[] = $sql;
                return array();
            }
        };
        eZDB::setInstance( $db );
        return $db;
    }

    /** PP-07 */
    public function testRowsNotLoadedAheadAreNotAnsweredFromThem()
    {
        $method = new ReflectionMethod( 'eZPolicy', 'prefetchedAccessArray' );
        $this->assertNull( $method->invoke( $this->policy( 999 ) ) );
        $this->assertNull( $method->invoke( $this->policy( 999, array( 'Subtree', '/1/2/', 5 ) ) ) );

        $db = $this->recordingDB();
        $this->policy( 999 )->limitationList();
        ( new eZPolicyLimitation( array( 'id' => 9999, 'policy_id' => 999, 'identifier' => 'Class' ) ) )->valueList();
        $this->assertCount( 2, $db->asked, 'a policy and a limitation of another role ask the database' );
        $this->assertStringContainsString( 'ezpolicy_limitation', $db->asked[0] );
        $this->assertStringContainsString( 'ezpolicy_limitation_value', $db->asked[1] );
    }

    /** PP-08 */
    public function testTheAssignmentsLimitationAsksNothing()
    {
        $db = $this->recordingDB();
        foreach ( array( 901, 902, 903 ) as $id )
        {
            foreach ( array( array( 'Subtree', '/1/2/58/', 555 ), array( 'Section', '3', 556 ) ) as $limit )
            {
                $this->prefetched( $id, $limit );
                $this->byLimitationList( $id, $limit );
            }
        }
        $this->assertSame( array(), $db->asked );
        $assigned = new eZPolicyLimitation( array( 'id' => -1, 'policy_id' => 902, 'identifier' => 'User_Section' ) );
        $assigned->setAttribute( 'limit_value', '3' );
        $this->assertSame( array( 'User_Section' => array( '3' ) ), $assigned->limitArray() );
    }

    /** PP-09 */
    public function testABuildInsideABuildGivesTheOuterOneItsRowsBack()
    {
        $swap = new ReflectionMethod( 'eZRole', 'swapPrefetchState' );
        $outer = array( eZRole::$prefetchedPolicyRows, eZPolicy::$prefetchedLimitationRows,
                        eZPolicyLimitation::$prefetchedValueRows, eZPolicy::$prefetchedLimitArrays );
        $before = $swap->invoke( null, array( array( 5 => array() ), array( 6 => array() ), array( -1 => array() ), array() ) );
        $this->assertSame( array( 6 => array() ), eZPolicy::$prefetchedLimitationRows );
        $swap->invoke( null, $before );
        $this->assertSame( $outer, array( eZRole::$prefetchedPolicyRows, eZPolicy::$prefetchedLimitationRows,
                                          eZPolicyLimitation::$prefetchedValueRows, eZPolicy::$prefetchedLimitArrays ) );

        // The outermost build puts back "nothing loaded"
        $swap->invoke( null, null );
        $this->assertNull( eZRole::$prefetchedPolicyRows );
        $this->assertNull( eZPolicy::$prefetchedLimitationRows );
        $this->assertNull( eZPolicyLimitation::$prefetchedValueRows );
        $this->assertSame( array(), eZPolicy::$prefetchedLimitArrays );
    }

    /** PP-10 */
    public function testTheInListsStayBelowOraclesLimit()
    {
        $this->assertGreaterThan( 0, eZRole::PREFETCH_IN_LIST_SIZE );
        $this->assertLessThanOrEqual( 1000, eZRole::PREFETCH_IN_LIST_SIZE );
    }
}
