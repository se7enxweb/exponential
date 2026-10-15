<?php
/**
 * Deactivate and move against the installation's own database (the kernel started on the admin siteaccess by
 * ezpLiveInstallation; skipped where there is none, as on CI). Every test runs inside one database transaction that
 * is rolled back afterwards, so nothing it creates (users, groups, the group of deactivated users, the remembered
 * locations) stays. The settings it changes are put back.
 *
 *  UL-01 - Switched off (the default): nothing is offered, and nothing is created
 *  UL-02 - Switched on without a group: created on first use below the user root node, hidden, with the remote id,
 *          without roles; found again by the remote id; only by a user who may create it
 *  UL-03 - A configured node id is the group; one that does not exist is never replaced
 *  UL-04 - Deactivate and move: disabled, the other locations removed, the main one in the group, the old ones
 *          remembered; Reactivate and move back: enabled, the main one and the others back, the record gone
 *  UL-05 - A former location that is gone: left out and counted; the main one gone: the default user group
 *  UL-06 - Rights: a user who may not read the account gets "gone"; the protected accounts are never touched
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUserDeactivationLiveTest extends PHPUnit\Framework\TestCase
{
    /** @var array settings changed by a test: name => old value (null = not set) */
    private $changed = array();

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
        foreach ( array( 'user', 'user_group' ) as $identifier )
        {
            if ( !eZContentClass::fetchByIdentifier( $identifier ) )
                self::markTestSkipped( "needs the content class '$identifier' of the standard installation" );
        }
    }

    protected function setUp(): void
    {
        self::loginAdmin();
        eZContentLanguage::setCronjobMode( true );
        $this->set( 'DeactivateAndMove', 'enabled' );
        $this->set( 'DeactivatedUserGroupNodeID', '' );
        eZDB::instance()->begin();
        if ( expUserDeactivation::groupNode() || !expUserDeactivation::groupMayBeCreated() )
            $this->markTestSkipped( 'the installation has a group of deactivated users already; the tests start without one' );
    }

    protected function tearDown(): void
    {
        $db = eZDB::instance();
        while ( $db->transactionCounter() > 0 )
            $db->rollback();
        $ini = eZINI::instance();
        foreach ( $this->changed as $name => $old )
        {
            if ( $old === null )
                $ini->removeSetting( 'UserSettings', $name );
            else
                $ini->setVariable( 'UserSettings', $name, $old );
        }
        $this->changed = array();
        eZContentObject::clearCache();
        self::loginAdmin();
    }

    private function set( $name, $value )
    {
        $ini = eZINI::instance();
        if ( !array_key_exists( $name, $this->changed ) )
            $this->changed[$name] = $ini->hasVariable( 'UserSettings', $name ) ? $ini->variable( 'UserSettings', $name ) : null;
        $ini->setVariable( 'UserSettings', $name, $value );
    }

    private static function loginAdmin()
    {
        $admin = eZUser::fetch( 14 ) ?: eZUser::fetchByName( 'admin' );
        eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
    }

    private static function create( $class, $parent, array $attributes )
    {
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => $parent, 'class_identifier' => $class,
                                                                     'creator_id' => eZUser::currentUserID(), 'attributes' => $attributes ) );
        if ( !$object instanceof eZContentObject )
            throw new RuntimeException( "could not create the $class below node $parent" );
        eZContentObject::clearCache();
        return eZContentObject::fetch( (int)$object->attribute( 'id' ) );
    }

    /** A user group below the user root node, and a user in it and in a second group */
    private function account( $groups = 2 )
    {
        $root = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'UserRootNode' );
        $id = uniqid();
        $a = self::create( 'user_group', $root, array( 'name' => "UL group A $id" ) );
        $b = self::create( 'user_group', $root, array( 'name' => "UL group B $id" ) );
        $user = self::create( 'user', (int)$a->attribute( 'main_node_id' ),
                              array( 'first_name' => 'UL', 'last_name' => "Test $id", 'user_account' => "ul$id|ul$id@deactivate.example.invalid" ) );
        $this->assertInstanceOf( 'eZUser', eZUser::fetch( (int)$user->attribute( 'id' ) ), 'the account exists' );
        if ( $groups > 1 )
        {
            $user->addLocation( (int)$b->attribute( 'main_node_id' ), true );
            eZContentObject::clearCache();
        }
        return array( 'user' => (int)$user->attribute( 'id' ), 'a' => (int)$a->attribute( 'main_node_id' ), 'b' => (int)$b->attribute( 'main_node_id' ) );
    }

    private static function parents( $objectID )
    {
        eZContentObject::clearCache();
        $parents = array();
        foreach ( eZContentObject::fetch( (int)$objectID )->assignedNodes( true ) as $node )
            $parents[] = (int)$node->attribute( 'parent_node_id' );
        sort( $parents );
        return $parents;
    }

    private static function enabled( $objectID )
    {
        $setting = eZUserSetting::fetch( (int)$objectID );
        return !$setting || (int)$setting->attribute( 'is_enabled' ) !== 0;
    }

    /** The bulk run as the page does it, for the current user */
    private static function bulk( $action, array $ids )
    {
        $group = expUserDeactivation::groupNode();
        if ( !$group && $action === 'deactivate_move' && expUserDeactivation::canCreateGroup() )
            $group = expUserDeactivation::createGroup()['node'];
        $protected = expUserHygiene::protectedIDs( eZUser::currentUserID() );
        return expUserDeactivation::applyWith( $action, $ids,
            function ( $id ) use ( $group ) { return expUserDeactivation::accountState( $id, $group ); },
            function ( $action, $id ) use ( $group )
            {
                return $action === 'deactivate_move' ? expUserDeactivation::deactivate( $id, $group ) : expUserDeactivation::reactivate( $id, $group );
            }, $protected );
    }

    /** UL-01 */
    public function testOffByDefaultShowsAndCreatesNothing()
    {
        $account = $this->account( 1 );
        $this->set( 'DeactivateAndMove', 'disabled' );
        $this->assertFalse( expUserDeactivation::isEnabled() );
        $this->assertSame( array( 'deactivate' => false, 'reactivate' => false ), expUserDeactivation::offers( $account['user'] ) );
        $this->assertNull( expUserDeactivation::groupNode() );
        $this->assertNull( eZContentObject::fetchByRemoteID( expUserDeactivation::REMOTE_ID ), 'nothing was created' );

        $this->set( 'DeactivateAndMove', 'enabled' );
        $this->assertSame( array( 'deactivate' => true, 'reactivate' => false ), expUserDeactivation::offers( $account['user'] ) );
        $this->assertNull( eZContentObject::fetchByRemoteID( expUserDeactivation::REMOTE_ID ), 'offering creates nothing either' );
    }

    /** UL-02 */
    public function testGroupCreatedOnFirstUse()
    {
        $this->assertNull( expUserDeactivation::groupNode() );
        $this->assertTrue( expUserDeactivation::canCreateGroup(), 'the administrator may create it' );
        $created = expUserDeactivation::createGroup();
        $this->assertTrue( $created['ok'], $created['error'] );
        $node = $created['node'];
        $root = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'UserRootNode' );
        $this->assertSame( $root, (int)$node->attribute( 'parent_node_id' ) );
        $this->assertTrue( (bool)$node->attribute( 'is_hidden' ), 'hidden' );
        $object = $node->object();
        $this->assertSame( expUserDeactivation::REMOTE_ID, $object->attribute( 'remote_id' ) );
        $this->assertSame( (int)eZINI::instance()->variable( 'UserSettings', 'UserGroupClassID' ), (int)$object->attribute( 'contentclass_id' ) );
        $this->assertSame( array(), eZDB::instance()->arrayQuery( 'SELECT id FROM ezuser_role WHERE contentobject_id = ' . (int)$object->attribute( 'id' ) ), 'no roles' );
        $this->assertSame( (int)$node->attribute( 'node_id' ), (int)expUserDeactivation::groupNode()->attribute( 'node_id' ), 'found by the remote id' );
        $this->assertFalse( expUserDeactivation::groupMayBeCreated(), 'never a second one' );
        $this->assertSame( 'exists', expUserDeactivation::createGroup()['error'] );

        // the anonymous user may not create it
        $anonymous = eZUser::fetch( (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
        eZUser::setCurrentlyLoggedInUser( $anonymous, $anonymous->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        $this->assertFalse( expUserDeactivation::canCreateGroup() );
    }

    /** UL-03 */
    public function testConfiguredNodeIsTheGroup()
    {
        $account = $this->account( 1 );
        $this->set( 'DeactivatedUserGroupNodeID', (string)$account['b'] );
        $this->assertSame( $account['b'], (int)expUserDeactivation::groupNode()->attribute( 'node_id' ) );
        $result = self::bulk( 'deactivate_move', array( $account['user'] ) );
        $this->assertSame( array( $account['user'] ), $result['done'], json_encode( $result ) );
        $this->assertSame( array( $account['b'] ), self::parents( $account['user'] ) );
        $this->assertNull( eZContentObject::fetchByRemoteID( expUserDeactivation::REMOTE_ID ), 'no group of its own was made' );

        $this->set( 'DeactivatedUserGroupNodeID', '999999999' );
        $this->assertNull( expUserDeactivation::groupNode() );
        $this->assertFalse( expUserDeactivation::groupMayBeCreated(), 'a configured node that is missing is never replaced' );
    }

    /** UL-04 */
    public function testDeactivateAndMoveAndBack()
    {
        $account = $this->account();
        $id = $account['user'];
        $this->assertSame( array( $account['a'], $account['b'] ), self::parents( $id ) );
        $mainBefore = (int)eZContentObject::fetch( $id )->attribute( 'main_node_id' );

        $confirm = expUserDeactivation::applyWith( 'deactivate_move', array( $id ),
            function ( $id ) { return expUserDeactivation::accountState( $id, null, expUserDeactivation::canCreateGroup() ); },
            function () { throw new LogicException( 'the confirmation does nothing' ); }, array(), true );
        $this->assertSame( array( $id ), $confirm['done'], 'offered before the group exists' );
        $preview = expUserDeactivation::preview( $id, 'deactivate_move' );
        $this->assertCount( 2, $preview['groups'] );

        $result = self::bulk( 'deactivate_move', array( $id ) );
        $this->assertSame( array( $id ), $result['done'], json_encode( $result ) );
        $group = (int)expUserDeactivation::groupNode()->attribute( 'node_id' );
        $this->assertFalse( self::enabled( $id ) );
        $this->assertSame( array( $group ), self::parents( $id ), 'one location, in the group' );
        $this->assertSame( $mainBefore, (int)eZContentObject::fetch( $id )->attribute( 'main_node_id' ), 'the main node moved, it kept its id' );
        $record = expUserDeactivation::remembered( $id );
        $this->assertSame( $account['a'], $record['main'] );
        $this->assertSame( array( $account['b'] ), $record['parents'] );
        $this->assertSame( $group, $record['group'] );
        $this->assertSame( array( 'deactivate' => false, 'reactivate' => true ), expUserDeactivation::offers( $id ) );

        $again = self::bulk( 'deactivate_move', array( $id ) );
        $this->assertSame( array( 'unchanged' => array( $id ) ), $again['skipped'] );

        $back = self::bulk( 'reactivate_move', array( $id ) );
        $this->assertSame( array( $id ), $back['done'], json_encode( $back ) );
        $this->assertTrue( self::enabled( $id ) );
        $this->assertSame( array( $account['a'], $account['b'] ), self::parents( $id ) );
        $main = eZContentObject::fetch( $id )->mainNode();
        $this->assertSame( $account['a'], (int)$main->attribute( 'parent_node_id' ), 'the main location where it was' );
        $this->assertFalse( (bool)$main->attribute( 'is_invisible' ), 'visible again outside the hidden group' );
        $this->assertNull( expUserDeactivation::remembered( $id ), 'the record is gone' );
        $this->assertSame( 0, $back['details'][$id]['not_restored'] );
        $this->assertFalse( $back['details'][$id]['fallback'] );
    }

    /** UL-05 */
    public function testFormerLocationsGone()
    {
        $account = $this->account();
        $id = $account['user'];
        self::bulk( 'deactivate_move', array( $id ) );
        // the second group is removed meanwhile, and the main parent is one that does not exist
        eZContentOperationCollection::removeNodes( array( $account['b'] ) );
        $record = expUserDeactivation::remembered( $id );
        $record['main'] = 999999999;
        expUserDeactivation::remember( $id, $record );

        $preview = expUserDeactivation::preview( $id, 'reactivate_move', expUserDeactivation::groupNode() );
        $this->assertTrue( $preview['fallback'] );
        $this->assertSame( 2, $preview['not_restored'] );

        $back = self::bulk( 'reactivate_move', array( $id ) );
        $this->assertSame( array( $id ), $back['done'], json_encode( $back ) );
        $fallback = expUserDeactivation::fallbackNodeID();
        $this->assertSame( array( $fallback ), self::parents( $id ), 'the default user group' );
        $this->assertTrue( $back['details'][$id]['fallback'] );
        $this->assertSame( 2, $back['details'][$id]['not_restored'], 'the main and the second group, counted together' );
        $this->assertTrue( self::enabled( $id ) );
    }

    /** UL-06 */
    public function testRights()
    {
        $account = $this->account( 1 );
        $protected = expUserHygiene::protectedIDs( 14 );
        $result = self::bulk( 'deactivate_move', array( 14, (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) ) );
        $this->assertSame( array(), $result['done'] );
        $this->assertSame( array( 'protected' ), array_keys( $result['skipped'] ) );
        $this->assertContains( 14, $protected );

        // the anonymous user may not read user accounts: the account is "gone" to them, not "denied"
        $anonymous = eZUser::fetch( (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
        eZUser::setCurrentlyLoggedInUser( $anonymous, $anonymous->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        $this->assertNull( expUserDeactivation::accountState( $account['user'] ) );
        $this->assertSame( array( 'gone' => array( $account['user'], 999999999 ) ),
                           expUserDeactivation::applyWith( 'deactivate_move', array( $account['user'], 999999999 ),
                               function ( $id ) { return expUserDeactivation::accountState( $id ); },
                               function () { throw new LogicException( 'never' ); }, array() )['skipped'],
                           'an account they may not read and one that does not exist: the same answer' );
        $this->assertSame( array( 'deactivate' => false, 'reactivate' => false ), expUserDeactivation::offers( $account['user'] ) );
        $this->assertTrue( self::enabled( $account['user'] ) );
    }
}
