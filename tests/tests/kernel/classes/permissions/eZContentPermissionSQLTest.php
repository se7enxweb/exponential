<?php
/**
 * Tests of the SQL the tree node fetches add for the content/read limitations of a user, without the database:
 * eZContentObjectTreeNode::createPermissionCheckingSQL() (one OR group per policy, the limitations of a policy
 * joined by AND, Node and Subtree as alternatives of each other, User_Subtree and User_Section from limited role
 * assignments, object state groups joined once per group, the condition of the handler of an extension limitation
 * and no access for one without handler) and createShowInvisibleSQLString().
 *
 * A database handler that only escapes stands in for the real one while a test runs.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/fixtures/ezcontentpermissionsqltestdb.php';

/** Limits a fetch to nodes up to the depth given as the limitation value */
class X1PermissionSQLLimitationHandler implements ezpContentLimitationHandler
{
    public function checkAccess( $limitation, array $values, $functionName, $subject, $userID )
    {
        return false;
    }

    public function permissionSQL( $limitation, array $values, $tableAliasName, $userID )
    {
        return "$tableAliasName.depth <= " . (int)$values[0];
    }
}

/** Answers with a condition that closes the parentheses it is put in and opens a policy of its own */
class X1PermissionSQLBreakoutHandler extends X1PermissionSQLLimitationHandler
{
    public function permissionSQL( $limitation, array $values, $tableAliasName, $userID )
    {
        return '1 = 1 ) OR ( 1 = 1';
    }
}

class eZContentPermissionSQLTest extends PHPUnit\Framework\TestCase
{
    private $db;
    private $hadDB;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->hadDB = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $this->db = $GLOBALS['eZDBGlobalInstance'] ?? null;
        eZDB::setInstance( new eZContentPermissionSQLTestDB() );
    }

    protected function tearDown(): void
    {
        if ( $this->hadDB )
            $GLOBALS['eZDBGlobalInstance'] = $this->db;
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
        ezpINIHelper::restoreINISettings();
    }

    private function where( $limitationList )
    {
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( $limitationList );
        $this->assertSame( array(), $sql['temp_tables'] );
        return $sql['where'];
    }

    public function testFullAccessAddsNoCondition()
    {
        // An empty list: accessWord 'yes', or a fetch given an empty Limitation
        $this->assertSame( array( 'from' => '', 'where' => '', 'temp_tables' => array() ), eZContentObjectTreeNode::createPermissionCheckingSQL( array() ) );
    }

    public function testNoReadAccessGivesAConditionNoNodeMeets()
    {
        // false: a user without any content/read policy, or whose access array could not be read (fail-closed).
        // The empty condition of before listed every node to such a user.
        $this->assertSame( array( 'from' => '', 'where' => ' AND 0 = 1 ', 'temp_tables' => array() ), eZContentObjectTreeNode::createPermissionCheckingSQL( false ) );
        foreach ( array( 'enabled', 'disabled' ) as $setting )
        {
            ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'PermissionSQLOptimization', $setting );
            $this->assertSame( ' AND 0 = 1 ', eZContentObjectTreeNode::createPermissionCheckingSQL( false, 'ezcontentobject_trash', 'ezcot',
                                                                                                    array( 'paths' => array( '/1/2/' ) ) )['where'], $setting );
        }
    }

    public function testClassAndSectionOfOnePolicyAreJoinedByAnd()
    {
        $this->assertSame( ' AND ((ezcontentobject.contentclass_id in (2, 5) AND ezcontentobject.section_id in (1))) ',
                           $this->where( array( 'p_1' => array( 'Class' => array( 2, 5 ), 'Section' => array( 1 ) ) ) ) );
    }

    public function testPoliciesAreAlternatives()
    {
        $this->assertSame( ' AND ((ezcontentobject.contentclass_id in (2)) OR (ezcontentobject.section_id in (3, 4))) ',
                           $this->where( array( 'p_1' => array( 'Class' => array( 2 ) ), 'p_2' => array( 'User_Section' => array( 3, 4 ) ) ) ) );
    }

    public function testEmptyClassListIsNoCondition()
    {
        $this->assertSame( '', $this->where( array( 'p_1' => array( 'Class' => array() ) ) ) );
        $this->assertSame( ' AND ((ezcontentobject.section_id in (1))) ',
                           $this->where( array( 'p_1' => array( 'Class' => array(), 'Section' => array( 1 ) ) ) ) );
    }

    public function testNodeAndSubtreeArePlacementsThatAreAlternatives()
    {
        $this->assertSame(
            " AND ((( ( ezcontentobject_tree.node_id in (43, 44) ) OR ( ezcontentobject_tree.path_string like '/1/2/%' OR ezcontentobject_tree.path_string like '/1/5/%' ) ))) ",
            $this->where( array( 'p_1' => array( 'Node' => array( 43, 44 ), 'Subtree' => array( '/1/2/', '/1/5/' ) ) ) ) );
    }

    public function testUserSubtreeNarrowsThePolicyWithAnd()
    {
        $this->assertSame(
            " AND ((ezcontentobject.contentclass_id in (2) AND ezcontentobject_tree.path_string like '/1/2/77/%')) ",
            $this->where( array( 'p_1' => array( 'Class' => array( 2 ), 'User_Subtree' => array( '/1/2/77/' ) ) ) ) );
    }

    public function testSubtreeValuesAreEscaped()
    {
        $this->assertSame(
            " AND ((( ( ezcontentobject_tree.path_string like '/1/\\'x/%' ) ))) ",
            $this->where( array( 'p_1' => array( 'Subtree' => array( "/1/'x/" ) ) ) ) );
    }

    public function testTableAliasIsUsedForPlacements()
    {
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( array( array( 'Node' => array( 2 ), 'User_Subtree' => array( '/1/' ) ) ), 'ezcontentobject_tree', 't' );
        $this->assertSame( " AND ((t.path_string like '/1/%' AND ( ( t.node_id in (2) ) ))) ", $sql['where'] );
    }

    public function testStateGroupsAreJoinedOncePerGroup()
    {
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( array(
            'p_1' => array( 'StateGroup_ez_lock' => array( 1 ), 'StateGroup_other' => array( 5, 6 ) ),
            'p_2' => array( 'StateGroup_ez_lock' => array( 2 ) ),
        ) );
        $this->assertSame( 2, substr_count( $sql['from'], 'INNER JOIN ezcobj_state_link' ) );
        $this->assertStringContainsString( "ezcobj_state_grp_0_perm.identifier = 'ez_lock'", $sql['from'] );
        $this->assertStringContainsString( "ezcobj_state_grp_1_perm.identifier = 'other'", $sql['from'] );
        $this->assertSame( ' AND ((ezcobj_state_0_perm.id = 1 AND ezcobj_state_1_perm.id IN ( 5, 6 )) OR (ezcobj_state_0_perm.id = 2)) ', $sql['where'] );
    }

    public function testStateGroupIdentifierIsEscaped()
    {
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( array( array( "StateGroup_a'b" => array( 1 ) ) ) );
        $this->assertStringContainsString( "identifier = 'a\\'b'", $sql['from'] );
    }

    public function testKernelLimitationsOfOtherFunctionsAreIgnored()
    {
        $this->assertSame( '', $this->where( array( 'p_1' => array( 'ParentClass' => array( 1 ), 'Language' => array( 'eng-GB' ) ) ) ) );
    }

    /**
     * A limitation of an extension without a handler (site.ini [RoleSettings] LimitationHandlers[]) gives its policy
     * no access; it was ignored, and the fetch listed every object the rest of the policy allowed
     */
    public function testExtensionLimitationWithoutAHandlerClosesItsPolicy()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'LimitationHandlers', array() );
        $this->assertSame( ' AND ((ezcontentobject.contentclass_id in (2) AND 1 = 0) OR (ezcontentobject.section_id in (3))) ',
                           $this->where( array( 'p_1' => array( 'Class' => array( 2 ), 'X1Limitation' => array( 1 ) ),
                                                'p_2' => array( 'Section' => array( 3 ) ) ) ) );
        $this->assertSame( ' AND ((1 = 0)) ', $this->where( array( 'p_1' => array( 'X1Limitation' => array( 1 ) ) ) ) );
    }

    /** The condition of the handler joins the other limitations of its policy */
    public function testExtensionLimitationWithAHandlerAddsItsCondition()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'LimitationHandlers', array( 'X1Limitation' => 'X1PermissionSQLLimitationHandler' ) );
        // The handler is asked for the current user: a stand-in, so no session or database is needed
        $hadUser = array_key_exists( 'eZUserGlobalInstance_', $GLOBALS );
        $user = $hadUser ? $GLOBALS['eZUserGlobalInstance_'] : null;
        $GLOBALS['eZUserGlobalInstance_'] = new eZUser( array( 'contentobject_id' => eZUser::anonymousId(), 'login' => 'x1anonymous' ) );
        try
        {
            $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( array( 'p_1' => array( 'Class' => array( 2 ), 'X1Limitation' => array( 4 ) ) ), 'ezcontentobject_tree', 't' );
        }
        finally
        {
            if ( $hadUser )
                $GLOBALS['eZUserGlobalInstance_'] = $user;
            else
                unset( $GLOBALS['eZUserGlobalInstance_'] );
        }
        $this->assertSame( ' AND ((ezcontentobject.contentclass_id in (2) AND ( t.depth <= 4 ))) ', $sql['where'] );
    }

    /**
     * The values of the Class, Section, Node and state limitations are cast to integers: a value that got into a
     * policy some other way than through the role editor cannot change the condition
     */
    public function testKernelLimitationValuesAreIntegers()
    {
        $this->assertSame( ' AND ((ezcontentobject.contentclass_id in (2, 1) AND ezcontentobject.section_id in (3))) ',
                           $this->where( array( 'p_1' => array( 'Class' => array( '2', '1) OR (1=1' ), 'Section' => array( '3 OR 1=1' ) ) ) ) );
        $this->assertSame( ' AND ((( ( ezcontentobject_tree.node_id in (43) ) ))) ',
                           $this->where( array( 'p_1' => array( 'Node' => array( '43) OR (1=1' ) ) ) ) );
        $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( array( array( 'StateGroup_a' => array( '1 OR 1=1' ) ),
                                                                            array( 'StateGroup_b' => array( '2 x', '3' ) ) ) );
        $this->assertSame( ' AND ((ezcobj_state_0_perm.id = 1) OR (ezcobj_state_1_perm.id IN ( 2, 3 ))) ', $sql['where'] );
    }

    /** A handler's condition that would leave its parentheses closes the policy instead */
    public function testAConditionThatLeavesItsParenthesesClosesThePolicy()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'LimitationHandlers', array( 'X1Limitation' => 'X1PermissionSQLBreakoutHandler' ) );
        ezpContentLimitation::resetCache();
        $hadUser = array_key_exists( 'eZUserGlobalInstance_', $GLOBALS );
        $user = $hadUser ? $GLOBALS['eZUserGlobalInstance_'] : null;
        $GLOBALS['eZUserGlobalInstance_'] = new eZUser( array( 'contentobject_id' => eZUser::anonymousId(), 'login' => 'x1anonymous' ) );
        try
        {
            $sql = eZContentObjectTreeNode::createPermissionCheckingSQL( array( 'p_1' => array( 'Class' => array( 2 ), 'X1Limitation' => array( 4 ) ) ), 'ezcontentobject_tree', 't' );
        }
        finally
        {
            if ( $hadUser )
                $GLOBALS['eZUserGlobalInstance_'] = $user;
            else
                unset( $GLOBALS['eZUserGlobalInstance_'] );
        }
        $this->assertSame( ' AND ((ezcontentobject.contentclass_id in (2) AND 1 = 0)) ', $sql['where'] );
    }

    public function testShowInvisibleSQLString()
    {
        $this->assertSame( '', eZContentObjectTreeNode::createShowInvisibleSQLString( false, true ) );
        $this->assertSame( 'AND ezcontentobject_tree.is_invisible = 0', eZContentObjectTreeNode::createShowInvisibleSQLString( false, false ) );
    }
}
