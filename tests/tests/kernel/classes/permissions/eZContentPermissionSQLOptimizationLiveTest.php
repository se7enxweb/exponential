<?php
/**
 * A user whose role is assigned for many subtrees, on the installation the tests run on. Live style: no test
 * database. Each test creates its own user (never published, address at x1.example.invalid) and its own role and
 * removes them again; where there is no installation (CI) the tests are skipped.
 *
 *  PO-01 - checkAccess() loads the locations of the object once, not once for every policy
 *  PO-02 - The tree fetches return the same nodes with the shortened permission condition as without it
 *  PO-03 - canCreateClassList() loads the locations of the object once, not once for every policy
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

#[\PHPUnit\Framework\Attributes\Group('database')]
class eZContentPermissionSQLOptimizationLiveTest extends PHPUnit\Framework\TestCase
{
    const ADDRESS_DOMAIN = 'x1.example.invalid';
    const ADMIN_ID = 14;

    private static $installation;
    private $previousUser;
    private $objects = array();
    private $roles = array();
    private $userIDs = array();

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        $this->previousUser = eZUser::currentUser();
        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( self::ADMIN_ID ), self::ADMIN_ID, eZUser::NO_SESSION_REGENERATE );
    }

    protected function tearDown(): void
    {
        if ( $this->previousUser instanceof eZUser )
        {
            eZUser::setCurrentlyLoggedInUser( $this->previousUser, $this->previousUser->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        }
        foreach ( $this->roles as $role )
        {
            $role->removeThis();
        }
        foreach ( array_reverse( $this->objects ) as $objectID )
        {
            $object = eZContentObject::fetch( $objectID );
            if ( $object instanceof eZContentObject )
            {
                $object->purge();
            }
        }
        foreach ( $this->userIDs as $userID )
        {
            if ( eZUser::fetch( $userID ) )
            {
                eZUser::removeUser( $userID );
            }
            eZUser::purgeUserCacheByUserId( $userID );
        }
        unset( $GLOBALS['ezpolicylimitation_list'] );
        ezpINIHelper::restoreINISettings();
        eZRole::expireCache();
        eZContentObject::clearCache();
        $this->roles = $this->objects = $this->userIDs = array();
    }

    /**
     * Nodes of depth 3 to assign the role for, the last one with a published child, and that child.
     *
     * @return array array( node IDs, the child's node row )
     */
    private function subtreesAndTarget()
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT t.node_id FROM ezcontentobject_tree t WHERE t.depth = 3 ORDER BY t.node_id", array( 'limit' => 40 ) );
        $child = $db->arrayQuery( "SELECT c.node_id, c.parent_node_id, c.contentobject_id
                                     FROM ezcontentobject_tree c, ezcontentobject_tree p, ezcontentobject o
                                    WHERE c.parent_node_id = p.node_id AND p.depth = 3 AND o.id = c.contentobject_id AND o.status = 1
                                 ORDER BY c.node_id", array( 'limit' => 1 ) );
        if ( count( $rows ) < 10 || count( $child ) == 0 )
        {
            $this->markTestSkipped( 'needs ten nodes of depth 3, one with a published child' );
        }
        $nodeIDs = array_diff( array_map( 'intval', array_column( $rows, 'node_id' ) ), array( (int)$child[0]['parent_node_id'] ) );
        $nodeIDs[] = (int)$child[0]['parent_node_id'];
        return array( array_values( $nodeIDs ), $child[0] );
    }

    /**
     * A user that was never published, with a role of two read policies and a create policy assigned for each
     * subtree given.
     *
     * @param int[] $nodeIDs
     * @return int the user's content object ID
     */
    private function userWithSubtreeRole( array $nodeIDs )
    {
        $login = 'x1-permission-sql-' . bin2hex( random_bytes( 4 ) );
        $userObject = eZContentClass::fetchByIdentifier( 'user' )->instantiate( self::ADMIN_ID );
        $userID = (int)$userObject->attribute( 'id' );
        $this->objects[] = $userID;
        $this->userIDs[] = $userID;

        $user = eZUser::fetch( $userID );
        if ( !$user )
        {
            $user = eZUser::create( $userID );
        }
        $user->setAttribute( 'login', $login );
        $user->setAttribute( 'email', $login . '@' . self::ADDRESS_DOMAIN );
        $user->setAttribute( 'password_hash', eZUser::createHash( $login, bin2hex( random_bytes( 12 ) ), eZUser::site(), eZUser::hashType() ) );
        $user->setAttribute( 'password_hash_type', eZUser::hashType() );
        $user->store();

        $role = eZRole::create( 'X1 permission SQL ' . $login );
        $role->store();
        $this->roles[] = $role;
        $role->appendPolicy( 'content', 'read', array( 'Class' => array( 1, 2, 3, 4, 5 ) ) );
        $role->appendPolicy( 'content', 'read', array( 'Section' => array( 1, 2, 3 ) ) );
        $role->appendPolicy( 'content', 'create', array( 'Class' => array( 1, 2 ) ) );
        foreach ( $nodeIDs as $nodeID )
        {
            $role->assignToUser( $userID, 'subtree', $nodeID );
        }
        eZRole::expireCache();
        eZUser::purgeUserCacheByUserId( $userID );
        return $userID;
    }

    /**
     * Runs $call and returns its result and the number of queries it sent.
     *
     * @param callable $call
     * @return array array( result, number of queries )
     */
    private function countQueries( $call )
    {
        $db = eZDB::instance();
        // The drivers count the queries they report, and report them while SQL output is on
        if ( !property_exists( $db, 'NumQueries' ) || $db->databaseName() === 'mongo' )
        {
            $this->markTestSkipped( 'the driver does not count its queries' );
        }
        $output = $db->OutputSQL;
        $db->OutputSQL = true;
        try
        {
            $before = (int)$db->NumQueries;
            $result = $call();
            return array( $result, (int)$db->NumQueries - $before );
        }
        finally
        {
            $db->OutputSQL = $output;
        }
    }

    /** PO-01 */
    public function testCheckAccessLoadsTheLocationsOnce()
    {
        list( $nodeIDs, $target ) = $this->subtreesAndTarget();
        $userID = $this->userWithSubtreeRole( $nodeIDs );
        $policies = eZUser::fetch( $userID )->hasAccessTo( 'content', 'read' );
        $this->assertSame( 'limited', $policies['accessWord'] );
        $this->assertCount( 2 * count( $nodeIDs ), $policies['policies'] );

        eZContentObject::clearCache();
        $object = eZContentObject::fetch( (int)$target['contentobject_id'] );
        list( $access, $queries ) = $this->countQueries( function () use ( $object, $userID )
        {
            return $object->checkAccess( 'read', false, false, false, false, $userID );
        } );
        $this->assertSame( 1, (int)$access );
        $this->assertLessThanOrEqual( 3, $queries, 'the policies of ' . count( $nodeIDs ) . ' subtrees ask for the locations once' );
    }

    /** PO-02 */
    public function testFetchesReturnTheSameNodes()
    {
        list( $nodeIDs, $target ) = $this->subtreesAndTarget();
        $userID = $this->userWithSubtreeRole( $nodeIDs );
        $policies = eZUser::fetch( $userID )->hasAccessTo( 'content', 'read' );
        $limitation = $policies['policies'];

        $fetches = array(
            'children of the target subtree' => array( (int)$target['parent_node_id'], array( 'Depth' => 1 ) ),
            'the target subtree' => array( (int)$target['parent_node_id'], array() ),
            'the whole tree' => array( 1, array() ),
            'children of the content root' => array( 2, array( 'Depth' => 1 ) ),
            'folders of the whole tree' => array( 1, array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'folder' ) ) ),
            'everything but folders' => array( 1, array( 'ClassFilterType' => 'exclude', 'ClassFilterArray' => array( 'folder' ) ) ),
            'two subtrees' => array( array( $nodeIDs[0], $nodeIDs[1] ), array() ),
        );
        $foundAny = 0;
        foreach ( $fetches as $label => list( $nodeID, $params ) )
        {
            $found = array();
            foreach ( array( 'disabled', 'enabled' ) as $setting )
            {
                ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'PermissionSQLOptimization', $setting );
                $rows = eZContentObjectTreeNode::subTreeByNodeID( $params + array( 'Limitation' => $limitation, 'AsObject' => false ), $nodeID );
                $ids = array_map( 'intval', array_column( (array)$rows, 'node_id' ) );
                sort( $ids );
                $found[$setting] = $ids;
                $count = eZContentObjectTreeNode::subTreeCountByNodeID( $params + array( 'Limitation' => $limitation ), $nodeID );
                $this->assertSame( count( $ids ), (int)$count, "$label, $setting: count and list agree" );
            }
            $this->assertSame( $found['disabled'], $found['enabled'], $label );
            $foundAny += count( $found['enabled'] );
        }
        $this->assertGreaterThan( 0, $foundAny, 'the fetches find nodes' );
    }

    /** PO-03 */
    public function testCanCreateClassListLoadsTheLocationsOnce()
    {
        list( $nodeIDs, $target ) = $this->subtreesAndTarget();
        $userID = $this->userWithSubtreeRole( $nodeIDs );
        unset( $GLOBALS['ezpolicylimitation_list'] );
        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( $userID ), $userID, eZUser::NO_SESSION_REGENERATE );

        // The access array is built before the count: here only the comparison with the locations counts.
        $this->assertSame( 'limited', eZUser::currentUser()->hasAccessTo( 'content', 'create' )['accessWord'] );
        // The object of the last subtree: every create policy before its own is compared with its locations.
        $parent = eZContentObjectTreeNode::fetch( (int)$target['parent_node_id'] );
        eZContentObject::clearCache();
        $object = eZContentObject::fetch( (int)$parent->attribute( 'contentobject_id' ) );
        list( $classes, $queries ) = $this->countQueries( function () use ( $object )
        {
            return $object->canCreateClassList( false );
        } );
        $this->assertEqualsCanonicalizing( array( 1, 2 ), array_map( 'intval', array_column( $classes, 'id' ) ) );
        $this->assertLessThanOrEqual( 5, $queries, 'the create policies of ' . count( $nodeIDs ) . ' subtrees ask for the locations once' );
    }
}
