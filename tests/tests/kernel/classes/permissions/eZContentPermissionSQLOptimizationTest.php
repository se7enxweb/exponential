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
        // ') OR (ezcontentobject.' only between policies; the 600 subtrees of a policy are ORed in groups
        $this->assertSame( 2, substr_count( $sql['where'], ') OR (ezcontentobject.' ), 'three policies, ORed' );
        $this->assertSame( 1200, substr_count( $sql['where'], 'path_string like' ) );
    }

    public function testAMergedPolicyKeepsItsOtherLimitationsForEverySubtree()
    {
        // Without the parentheses "Class AND a OR b" would give subtree b to every class
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( array(
            'a' => array( 'Class' => array( '5' ), 'User_Subtree' => array( '/1/2/100/' ) ),
            'b' => array( 'Class' => array( '5' ), 'User_Subtree' => array( '/1/2/101/' ) ),
        ) );
        $this->assertSame( " AND ((ezcontentobject.contentclass_id in (5) AND ( ezcontentobject_tree.path_string like '/1/2/100/%' OR ezcontentobject_tree.path_string like '/1/2/101/%' ))) ",
                           $sql['where'] );
    }

    public function testAClassLimitationWithoutValuesNarrowsNothing()
    {
        // The condition skips a Class limitation without values, so a class filter must not leave the policy out
        $list = array( 'a' => array( 'Class' => array(), 'Section' => array( '1' ) ) );
        $this->assertSame( $list, eZContentObjectTreeNode::pruneLimitationList( $list, null, 'include', array( 2 ) ) );
        $this->assertSame( $list, eZContentObjectTreeNode::pruneLimitationList( $list, null, 'exclude', array( 2 ) ) );
    }

    public function testClassIDsAreComparedAsTheDatabaseComparesThem()
    {
        // The filter puts its numbers into the query as given ('1e1' is 10), the policy's are intval()ed
        $list = array( 'a' => array( 'Class' => array( '10' ) ), 'b' => array( 'Class' => array( '16' ) ) );
        $this->assertSame( array( 'a' ), array_keys( eZContentObjectTreeNode::pruneLimitationList( $list, null, 'include', array( '1e1' ) ) ) );
        $this->assertSame( array( 'b' ), array_keys( eZContentObjectTreeNode::pruneLimitationList( $list, null, 'exclude', array( 10.0 ) ) ) );
        // 16.5 is no class id: it matches no node, so it neither keeps a policy in nor leaves one out
        $this->assertSame( array(), array_keys( eZContentObjectTreeNode::pruneLimitationList( $list, null, 'include', array( '16.5' ) ) ) );
        $this->assertSame( array( 'a', 'b' ), array_keys( eZContentObjectTreeNode::pruneLimitationList( $list, null, 'exclude', array( '16.5' ) ) ) );
    }

    public function testTheClassFilterTypeIsReadAsTheFetchesReadIt()
    {
        // The fetches compare loosely and take 'include' first: true is an include filter, not both
        $scope = eZContentObjectTreeNode::permissionFetchScope( array( 'a' => array( 'Class' => array( '2' ) ) ), 2, array( '/1/2/' ),
                                                                array( 'ClassFilterType' => true, 'ClassFilterArray' => array( 2 ) ) );
        $this->assertSame( 'include', $scope['class_filter_type'] );
        $scope = eZContentObjectTreeNode::permissionFetchScope( array( 'a' => array( 'Class' => array( '2' ) ) ), 2, array( '/1/2/' ),
                                                                array( 'ClassFilterType' => 'other', 'ClassFilterArray' => array( 2 ) ) );
        $this->assertFalse( $scope['class_filter_type'] );
        $this->assertSame( array(), $scope['class_ids'] );
    }

    public function testAPolicyWithoutConditionLeftAloneAfterPruningGivesNoAccess()
    {
        // 'b' adds no condition (ParentClass does not narrow a read) and was left out of the OR before as well:
        // the condition was the subtree of 'a' alone. Under another subtree that is no node, not every node.
        $list = array(
            'a' => array( 'Subtree' => array( '/1/2/50/' ) ),
            'b' => array( 'ParentClass' => array( '1' ) ),
        );
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( $list, 'ezcontentobject_tree', 'ezcontentobject_tree',
                                                                     array( 'paths' => array( '/1/2/60/' ) ) );
        $this->assertSame( ' AND 0 = 1 ', $sql['where'] );
        // Without pruning, nothing changes: such a list alone has no condition, as always
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( array( 'b' => $list['b'] ), 'ezcontentobject_tree', 'ezcontentobject_tree',
                                                                     array( 'paths' => array( '/1/2/60/' ) ) );
        $this->assertSame( '', $sql['where'] );
    }

    public function testShortOrChainsAreWrittenAsBefore()
    {
        $parts = array();
        for ( $i = 0; $i < eZContentObjectTreeNode::PERMISSION_SQL_OR_GROUP_SIZE; $i++ )
        {
            $parts[] = "p$i";
        }
        $this->assertSame( implode( ' OR ', $parts ), eZContentObjectTreeNode::permissionSQLOr( $parts ) );
        $parts[] = 'last';
        $this->assertSame( '( ' . implode( ' OR ', array_slice( $parts, 0, -1 ) ) . ' ) OR ( last )', eZContentObjectTreeNode::permissionSQLOr( $parts ) );
    }

    /**
     * SQLite refuses an expression deeper than 1000 levels, and reads an OR chain of n parts as n levels. With 1,500
     * policies that cannot be merged (each has its own Subtree) and a merged policy of 2,500 subtrees, the
     * condition of the optimisation runs on SQLite and finds the same rows; the plain OR of before is refused.
     */
    public function testAConditionOfThousandsOfPoliciesAndSubtreesRunsOnSQLite()
    {
        if ( !extension_loaded( 'pdo_sqlite' ) )
        {
            $this->markTestSkipped( 'pdo_sqlite is not loaded' );
        }
        $list = array();
        for ( $i = 0; $i < 1500; $i++ )
        {
            $list["p_1_$i"] = array( 'Subtree' => array( '/1/2/' . ( 1000 + $i ) . '/' ), 'User_Subtree' => array( '/1/2/' . ( 1000 + $i ) . '/' ) );
        }
        for ( $i = 0; $i < 2500; $i++ )
        {
            $list["p_2_$i"] = array( 'Class' => array( '5' ), 'User_Subtree' => array( '/1/2/' . ( 5000 + $i ) . '/' ) );
        }

        $pdo = new PDO( 'sqlite::memory:' );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $pdo->exec( 'CREATE TABLE ezcontentobject_tree ( node_id INTEGER, contentobject_id INTEGER, path_string TEXT )' );
        $pdo->exec( 'CREATE TABLE ezcontentobject ( id INTEGER, contentclass_id INTEGER, section_id INTEGER, owner_id INTEGER )' );
        $rows = array( array( 1, '/1/2/1499/', 2 ), array( 2, '/1/2/1499/7/', 2 ), array( 3, '/1/2/7499/', 5 ),
                       array( 4, '/1/2/7499/', 2 ), array( 5, '/1/2/9999/', 5 ) );
        foreach ( $rows as $row )
        {
            $pdo->exec( "INSERT INTO ezcontentobject_tree VALUES ( $row[0], $row[0], '$row[1]' )" );
            $pdo->exec( "INSERT INTO ezcontentobject VALUES ( $row[0], $row[2], 1, 14 )" );
        }
        $query = 'SELECT ezcontentobject_tree.node_id FROM ezcontentobject_tree ' .
                 'INNER JOIN ezcontentobject ON ezcontentobject.id = ezcontentobject_tree.contentobject_id WHERE 1 = 1 %s ORDER BY 1';

        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( $list );
        $this->assertSame( array( 1, 2, 3 ), array_map( 'intval', $pdo->query( sprintf( $query, $sql['where'] ) )->fetchAll( PDO::FETCH_COLUMN ) ) );

        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'PermissionSQLOptimization', 'disabled' );
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( $list );
        try
        {
            $pdo->query( sprintf( $query, $sql['where'] ) );
            $this->fail( 'the plain OR of 4,000 policies was expected to be refused by SQLite' );
        }
        catch ( PDOException $e )
        {
            $this->assertStringContainsString( 'too large', $e->getMessage() );
        }
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
