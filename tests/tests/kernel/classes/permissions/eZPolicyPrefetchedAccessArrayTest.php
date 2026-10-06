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
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZPolicyPrefetchedAccessArrayTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
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
        );
        eZPolicy::$prefetchedLimitArrays = array();
    }

    protected function tearDown(): void
    {
        eZPolicy::$prefetchedLimitationRows = null;
        eZPolicy::$prefetchedLimitArrays = array();
        eZPolicyLimitation::$prefetchedValueRows = null;
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
}
