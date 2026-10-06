<?php
/**
 * The access array of a user, built with the rows of all roles loaded ahead (site.ini [RoleSettings]
 * AccessArrayPrefetch=enabled), is the same as the one built role by role. Live style: on the installation the tests
 * run on, for its users and user groups, with a role of its own that carries limitations, assigned for 40 subtrees
 * (as for a member of many teamrooms) and for a section, removed afterwards; where there is no installation (CI) the
 * tests are skipped.
 *
 *  AP-01 - For every user and user group (up to 300) both ways give the same access array: the same modules,
 *          functions, policies, limitations and values, in the same order (===): the values of a limitation come
 *          sorted by value either way, as valueList() reads them
 *  AP-02 - The rows loaded ahead are dropped afterwards, so later calls ask the database again
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZRoleAccessArrayPrefetchLiveTest extends PHPUnit\Framework\TestCase
{
    const ADMIN_ID = 14;

    private static $installation;
    private $role;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 4 );
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        $node = eZContentObjectTreeNode::fetch( 2, false, false );
        $this->role = eZRole::create( 'X1 access array prefetch ' . bin2hex( random_bytes( 4 ) ) );
        $this->role->store();
        $this->role->appendPolicy( 'content', 'read', array( 'Class' => array( 1, 2 ), 'Section' => array( 1 ) ) );
        $this->role->appendPolicy( 'content', 'edit', array( 'Subtree' => array( $node['path_string'] ) ) );
        $this->role->appendPolicy( 'content', 'create', array( 'Class' => array( 1 ), 'ParentClass' => array( 1 ) ) );
        $this->role->appendPolicy( 'user', 'login', array( 'SiteAccess' => array( crc32( 'site' ) ) ) );
        $this->role->appendPolicy( 'setup', '*' );
        // Assigned plainly, for a subtree and for a section: the limited assignments change the policies
        $this->role->assignToUser( self::ADMIN_ID );
        $this->role->assignToUser( self::ADMIN_ID, 'subtree', 2 );
        $this->role->assignToUser( eZUser::anonymousId(), 'section', 1 );
        // As a member of many teamrooms: the role assigned for 40 subtrees
        foreach ( eZDB::instance()->arrayQuery( 'SELECT node_id FROM ezcontentobject_tree WHERE node_id > 2 ORDER BY node_id', array( 'limit' => 40 ) ) as $row )
        {
            $this->role->assignToUser( self::ADMIN_ID, 'subtree', (int)$row['node_id'] );
        }
        eZRole::expireCache();
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        if ( $this->role instanceof eZRole )
        {
            $this->role->removeThis();
        }
        eZRole::expireCache();
    }

    private function accessArray( $user, $prefetch )
    {
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'AccessArrayPrefetch', $prefetch ? 'enabled' : 'disabled' );
        $idList = $user->groups();
        $idList[] = $user->attribute( 'contentobject_id' );
        return eZRole::accessArrayByUserID( $idList );
    }

    /** AP-01 */
    public function testBothWaysGiveTheSameAccessArray()
    {
        $rows = eZDB::instance()->arrayQuery( 'SELECT contentobject_id FROM ezuser ORDER BY contentobject_id', array( 'limit' => 300 ) );
        $this->assertNotEmpty( $rows );
        $compared = 0;
        foreach ( $rows as $row )
        {
            $user = eZUser::fetch( (int)$row['contentobject_id'] );
            if ( !$user instanceof eZUser )
                continue;
            $roleByRole = $this->accessArray( $user, false );
            $prefetched = $this->accessArray( $user, true );
            $this->assertSame( $roleByRole, $prefetched, 'user ' . $row['contentobject_id'] );
            $compared++;
        }
        // Every user group on its own, as the role cache of its members starts from it
        $groups = eZDB::instance()->arrayQuery( "SELECT o.id FROM ezcontentobject o, ezcontentclass c
                                                 WHERE o.contentclass_id = c.id AND c.version = 0 AND c.identifier = 'user_group'
                                                 ORDER BY o.id", array( 'limit' => 300 ) );
        foreach ( $groups as $group )
        {
            ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'AccessArrayPrefetch', 'disabled' );
            $roleByRole = eZRole::accessArrayByUserID( array( (int)$group['id'] ) );
            ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'AccessArrayPrefetch', 'enabled' );
            $prefetched = eZRole::accessArrayByUserID( array( (int)$group['id'] ) );
            $this->assertSame( $roleByRole, $prefetched, 'user group ' . $group['id'] );
            $compared++;
        }
        $this->assertGreaterThan( 2, $compared );

        // The administrator has this role three times, two of them limited
        $admin = $this->accessArray( eZUser::fetch( self::ADMIN_ID ), true );
        $this->assertArrayHasKey( 'content', $admin );
    }

    /** AP-02 */
    public function testTheRowsAreDroppedAfterwards()
    {
        $this->accessArray( eZUser::fetch( self::ADMIN_ID ), true );
        $this->assertNull( eZRole::$prefetchedPolicyRows );
        $this->assertNull( eZPolicy::$prefetchedLimitationRows );
        $this->assertNull( eZPolicyLimitation::$prefetchedValueRows );
    }
}
