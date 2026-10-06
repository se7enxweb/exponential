<?php
/**
 * Tests of the shortened permission condition of the tree node fetches, without the database:
 * eZContentObjectTreeNode::mergeLimitationList() (policies that differ only in the subtree their role is assigned
 * for become one), pruneLimitationList() (what cannot give access to a fetched node stays out) and their use in
 * createPermissionCheckingSQL(), with site.ini [RoleSettings] PermissionSQLOptimization.
 *
 * A database handler that only escapes stands in for the real one while a test runs.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/fixtures/ezcontentpermissionsqltestdb.php';

class eZContentPermissionSQLOptimizationTest extends PHPUnit\Framework\TestCase
{
    private $db;
    private $hadDB;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->hadDB = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $this->db = $GLOBALS['eZDBGlobalInstance'] ?? null;
        eZDB::setInstance( new eZContentPermissionSQLTestDB() );
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'PermissionSQLOptimization', 'enabled' );
    }

    protected function tearDown(): void
    {
        if ( $this->hadDB )
            $GLOBALS['eZDBGlobalInstance'] = $this->db;
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
        ezpINIHelper::restoreINISettings();
    }

    /**
     * The read policies of a member role assigned for one subtree after the other, as the access array has them.
     *
     * @param int $subtreeCount
     * @return array
     */
    private function memberPolicies( $subtreeCount )
    {
        $list = array( 'p_1' => array( 'Section' => array( '1' ) ) );
        for ( $i = 0; $i < $subtreeCount; $i++ )
        {
            $path = '/1/2/' . ( 100 + $i ) . '/';
            $list["p_10_$i"] = array( 'Class' => array( '1', '2' ), 'Section' => array( '99' ), 'User_Subtree' => array( $path ) );
            $list["p_11_$i"] = array( 'Class' => array( '5' ), 'User_Subtree' => array( $path ) );
        }
        return $list;
    }

    public function testPoliciesThatDifferOnlyInTheirUserSubtreeBecomeOne()
    {
        $this->assertSame(
            array(
                'p_1' => array( 'Section' => array( '1' ) ),
                'p_10_0' => array( 'Class' => array( '1', '2' ), 'Section' => array( '99' ), 'User_Subtree' => array( '/1/2/100/', '/1/2/101/', '/1/2/102/' ) ),
                'p_11_0' => array( 'Class' => array( '5' ), 'User_Subtree' => array( '/1/2/100/', '/1/2/101/', '/1/2/102/' ) ),
            ),
            eZContentObjectTreeNode::mergeLimitationList( $this->memberPolicies( 3 ) ) );
    }

    public function testPoliciesWithAnotherDifferenceStayApart()
    {
        $list = array(
            'a' => array( 'Class' => array( '2' ), 'User_Subtree' => array( '/1/2/' ) ),
            'b' => array( 'Class' => array( '3' ), 'User_Subtree' => array( '/1/5/' ) ),
            'c' => array( 'Class' => array( '2' ) ),
            'd' => array( 'Class' => array( '2' ), 'Subtree' => array( '/1/7/' ), 'User_Subtree' => array( '/1/5/' ) ),
        );
        $this->assertSame( $list, eZContentObjectTreeNode::mergeLimitationList( $list ) );
    }

    public function testASubtreeGivenTwiceIsListedOnce()
    {
        $list = array(
            'a' => array( 'Class' => array( '2' ), 'User_Subtree' => array( '/1/2/' ) ),
            'b' => array( 'Class' => array( '2' ), 'User_Subtree' => array( '/1/2/', '/1/5/' ) ),
        );
        $this->assertSame( array( 'a' => array( 'Class' => array( '2' ), 'User_Subtree' => array( '/1/2/', '/1/5/' ) ) ),
                           eZContentObjectTreeNode::mergeLimitationList( $list ) );
    }

    public function testAListAskedForAgainGetsTheSameAnswer()
    {
        $list = $this->memberPolicies( 5 );
        $first = eZContentObjectTreeNode::mergeLimitationList( $list );
        $this->assertSame( $first, eZContentObjectTreeNode::mergeLimitationList( $list ) );
        $other = eZContentObjectTreeNode::mergeLimitationList( $this->memberPolicies( 2 ) );
        $this->assertCount( 2, $other['p_11_0']['User_Subtree'] );
        $this->assertSame( $first, eZContentObjectTreeNode::mergeLimitationList( $this->memberPolicies( 5 ) ) );
    }

    public function testAThousandAssignmentsGiveTheConditionOfThreePolicies()
    {
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( $this->memberPolicies( 600 ) );
        $this->assertSame( 2, substr_count( $sql['where'], ') OR (' ), 'three policies, ORed' );
        $this->assertSame( 1200, substr_count( $sql['where'], 'path_string like' ) );
    }

    public function testDisabledOrsEveryPolicy()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'PermissionSQLOptimization', 'disabled' );
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( $this->memberPolicies( 10 ),
                                                                     'ezcontentobject_tree', 'ezcontentobject_tree',
                                                                     array( 'paths' => array( '/1/2/105/' ) ) );
        $this->assertSame( 20, substr_count( $sql['where'], ') OR (' ) );
    }

    public function testOnlyTheSubtreesOfTheFetchedNodeStay()
    {
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( $this->memberPolicies( 120 ),
                                                                     'ezcontentobject_tree', 'ezcontentobject_tree',
                                                                     array( 'paths' => array( '/1/2/160/12345/' ) ) );
        $this->assertSame(
            " AND ((ezcontentobject.section_id in (1)) OR (ezcontentobject.contentclass_id in (1, 2) AND ezcontentobject.section_id in (99) AND ezcontentobject_tree.path_string like '/1/2/160/%')" .
            " OR (ezcontentobject.contentclass_id in (5) AND ezcontentobject_tree.path_string like '/1/2/160/%')) ",
            $sql['where'] );
    }

    public function testASubtreeInsideTheFetchedNodeStays()
    {
        $list = array( 'a' => array( 'User_Subtree' => array( '/1/2/160/12345/', '/1/2/161/' ) ) );
        $this->assertSame( array( 'a' => array( 'User_Subtree' => array( '/1/2/160/12345/' ) ) ),
                           eZContentObjectTreeNode::pruneLimitationList( $list, array( '/1/2/160/' ) ) );
    }

    public function testAPolicyWithoutSubtreeAtTheFetchedNodeIsLeftOut()
    {
        $list = array(
            'a' => array( 'Class' => array( '2' ), 'User_Subtree' => array( '/1/2/161/' ) ),
            'b' => array( 'Subtree' => array( '/1/2/161/', '/1/5/' ) ),
            'c' => array( 'Section' => array( '1' ) ),
        );
        $this->assertSame( array( 'c' => array( 'Section' => array( '1' ) ) ),
                           eZContentObjectTreeNode::pruneLimitationList( $list, array( '/1/2/160/' ) ) );
    }

    public function testASubtreeNextToANodeLimitationLeavesTheNode()
    {
        // Node and Subtree are alternatives: the node may lie in the fetched subtree.
        $list = array( 'a' => array( 'Node' => array( '77' ), 'Subtree' => array( '/1/5/' ) ) );
        $this->assertSame( array( 'a' => array( 'Node' => array( '77' ) ) ),
                           eZContentObjectTreeNode::pruneLimitationList( $list, array( '/1/2/160/' ) ) );
    }

    public function testAValueThatIsNoPathOfNodeIDsIsKept()
    {
        $list = array( 'a' => array( 'User_Subtree' => array( '/1/%/', '/1/5/' ) ) );
        $this->assertSame( array( 'a' => array( 'User_Subtree' => array( '/1/%/' ) ) ),
                           eZContentObjectTreeNode::pruneLimitationList( $list, array( '/1/2/160/' ) ) );
    }

    public function testEveryFetchedPathCounts()
    {
        $list = array( 'a' => array( 'User_Subtree' => array( '/1/2/160/', '/1/2/161/', '/1/2/162/' ) ) );
        $this->assertSame( array( 'a' => array( 'User_Subtree' => array( '/1/2/160/', '/1/2/162/' ) ) ),
                           eZContentObjectTreeNode::pruneLimitationList( $list, array( '/1/2/160/', '/1/2/162/9/' ) ) );
    }

    public function testAClassFilterLeavesOutPoliciesForOtherClasses()
    {
        $list = array(
            'a' => array( 'Class' => array( '2', '5' ) ),
            'b' => array( 'Class' => array( '5' ) ),
            'c' => array( 'Section' => array( '1' ) ),
        );
        $this->assertSame( array( 'a', 'c' ), array_keys( eZContentObjectTreeNode::pruneLimitationList( $list, null, 'include', array( 2 ) ) ) );
        $this->assertSame( array( 'a', 'c' ), array_keys( eZContentObjectTreeNode::pruneLimitationList( $list, null, 'exclude', array( 5 ) ) ) );
        $this->assertSame( array( 'a', 'b', 'c' ), array_keys( eZContentObjectTreeNode::pruneLimitationList( $list, null, 'include', array() ) ) );
    }

    public function testNothingLeftGivesAConditionNoNodeMeets()
    {
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( array( 'a' => array( 'User_Subtree' => array( '/1/2/161/' ) ) ),
                                                                     'ezcontentobject_tree', 'ezcontentobject_tree',
                                                                     array( 'paths' => array( '/1/2/160/' ) ) );
        $this->assertSame( array( 'from' => '', 'where' => ' AND 0 = 1 ', 'temp_tables' => array() ), $sql );
    }

    public function testWithoutFetchScopeNoPolicyIsLeftOut()
    {
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( array(
            'a' => array( 'User_Subtree' => array( '/1/2/161/' ) ),
            'b' => array( 'Class' => array( '5' ) ),
        ) );
        $this->assertSame( " AND ((ezcontentobject_tree.path_string like '/1/2/161/%') OR (ezcontentobject.contentclass_id in (5))) ", $sql['where'] );
    }
}
