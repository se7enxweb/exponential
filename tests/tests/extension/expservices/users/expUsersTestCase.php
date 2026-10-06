<?php
/**
 * The common base of the expservices users and access tests. They run against the installation's own
 * database (never a test database): read services read the existing users, groups and roles, and every write
 * is tested on a test group, test users, a test role and test collaboration items that the test creates and
 * removes again. Existing users, groups and roles are never changed.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/expservices/users/
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

abstract class expUsersTestCase extends PHPUnit\Framework\TestCase
{
    const PASSWORD = 'ExpTest#12345';

    protected static $bootError = null;

    /** @var array created by the tests of a class, removed in tearDownAfterClass: kind => ids */
    protected static $made = array( 'objects' => array(), 'roles' => array(), 'items' => array(), 'groups' => array(), 'sessions' => array() );
    protected static $counter = 0;
    protected static $fixture = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // every test here calls the services against the installed site: skip the class without one (CI)
        ezpLiveInstallation::requireOrSkip();
        self::boot();
        self::$made = array( 'objects' => array(), 'roles' => array(), 'items' => array(), 'groups' => array(), 'sessions' => array() );
        self::$fixture = array();
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$bootError === null )
        {
            try
            {
                self::cleanup();
            }
            catch ( Throwable $e )
            {
                fwrite( STDERR, "\nexpservices users test cleanup: " . $e->getMessage() . "\n" );
            }
        }
        parent::tearDownAfterClass();
    }

    public function setUp(): void
    {
        parent::setUp();
        if ( self::$bootError !== null )
            $this->markTestSkipped( 'Kernel not available: ' . self::$bootError );
        expServiceBase::$trustRequest = null;
        expServiceBase::$postData = null;
        unset( $_SERVER['REQUEST_METHOD'] );
        $_POST = array();
        $this->loginAdmin();
    }

    public function tearDown(): void
    {
        expServiceBase::$trustRequest = null;
        expServiceBase::$postData = null;
        unset( $_SERVER['REQUEST_METHOD'] );
        $_POST = array();
        if ( self::$bootError === null )
            $this->loginAdmin();
        parent::tearDown();
    }

    protected static function boot()
    {
        if ( !empty( $GLOBALS['expservices_test_booted'] ) || self::$bootError !== null )
            return;
        try
        {
            $root = dirname( __DIR__, 5 );
            chdir( $root );
            if ( !class_exists( 'eZScript' ) )
                require_once $root . '/autoload.php';
            $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
            $script->startup();
            $script->setUseSiteAccess( 'admin' );
            $script->initialize();
            eZExecution::setCleanExit();
            if ( !eZDB::instance()->isConnected() )
                throw new RuntimeException( 'no database connection' );
            $GLOBALS['expservices_test_booted'] = true;
        }
        catch ( Throwable $e )
        {
            self::$bootError = $e->getMessage();
        }
    }

    // ------------------------------------------------------------------ login and calls

    protected function loginAdmin()
    {
        $admin = eZUser::fetchByName( 'admin' );
        if ( $admin instanceof eZUser )
            eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ) );
    }

    protected function loginAnonymous()
    {
        $id = eZUser::anonymousId();
        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( $id ), $id );
    }

    protected function loginAs( $userId )
    {
        $user = eZUser::fetch( (int)$userId );
        $this->assertInstanceOf( 'eZUser', $user, "user $userId exists" );
        eZUser::purgeUserCacheByUserId( (int)$userId );
        eZUser::setCurrentlyLoggedInUser( $user, (int)$userId );
        eZContentObject::clearCache();
    }

    /** Calls a read service the way ezjscore does and returns the envelope. */
    protected function call( $class, $method, array $args = array() )
    {
        return expServiceBase::invoke( $class, $method, $args );
    }

    /** Calls a write service with POST fields (the request check is overridden) and returns the envelope. */
    protected function write( $class, $method, array $post = array() )
    {
        expServiceBase::$trustRequest = true;
        expServiceBase::$postData = $post;
        try
        {
            return expServiceBase::invoke( $class, $method, array() );
        }
        finally
        {
            expServiceBase::$trustRequest = null;
            expServiceBase::$postData = null;
        }
    }

    protected function ok( array $r )
    {
        $this->assertTrue( $r['ok'], isset( $r['error'] ) ? json_encode( $r['error'] ) : 'not ok' );
        $this->assertArrayHasKey( 'data', $r );
        return $r['data'];
    }

    protected function okCall( $class, $method, array $args = array() )
    {
        return $this->ok( $this->call( $class, $method, $args ) );
    }

    protected function okWrite( $class, $method, array $post = array() )
    {
        return $this->ok( $this->write( $class, $method, $post ) );
    }

    protected function assertError( array $r, $code )
    {
        $this->assertFalse( $r['ok'], 'expected an error' );
        $this->assertSame( $code, $r['error']['code'], $r['error']['message'] );
    }

    protected function meta( array $r )
    {
        return (array)$r['meta'];
    }

    protected function assertPaged( array $r )
    {
        $this->assertTrue( $r['ok'], isset( $r['error'] ) ? json_encode( $r['error'] ) : '' );
        $m = $this->meta( $r );
        foreach ( array( 'total', 'offset', 'limit', 'count', 'has_more' ) as $k )
            $this->assertArrayHasKey( $k, $m, "meta.$k" );
        $this->assertSame( count( $r['data'] ), $m['count'] );
    }

    // ------------------------------------------------------------------ fixtures

    protected static function uniq()
    {
        self::$counter++;
        return 'exptest' . base_convert( (string)time(), 10, 36 ) . getmypid() . self::$counter;
    }

    protected function usersRootNode()
    {
        return (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'UserRootNode' );
    }

    /** A new test group (node id) below the users root or $parentNode. */
    protected function newGroup( $parentNode = null, $name = null )
    {
        $name = $name ?: ( 'Group ' . self::uniq() );
        $d = $this->okWrite( 'expUserGroupServices', 'create', array( 'parent' => $parentNode ?: $this->usersRootNode(), 'name' => $name ) );
        self::$made['groups'][] = (int)$d['id'];
        return (int)$d['node_id'];
    }

    /** The group every test class shares, created on first use. */
    protected function fixtureGroup()
    {
        if ( !isset( self::$fixture['group'] ) )
            self::$fixture['group'] = $this->newGroup();
        return self::$fixture['group'];
    }

    /** A new test user (object id) in a group; the login is returned by fetch. */
    protected function newUser( $groupNode = null, $login = null, array $extra = array() )
    {
        $login = $login ?: self::uniq();
        $d = $this->okWrite( 'expUserServices', 'create', $extra + array( 'group' => $groupNode ?: $this->fixtureGroup(), 'login' => $login,
            'email' => $login . '@example.com', 'password' => self::PASSWORD,
            'fields' => json_encode( array( 'first_name' => 'Exp', 'last_name' => $login ) ) ) );
        self::$made['objects'][] = (int)$d['id'];
        return (int)$d['id'];
    }

    /** A new test role (id) with policies given as array( array( module, function, limitations ) ). */
    protected function newRole( array $policies = array(), $name = null )
    {
        $d = $this->okWrite( 'expRoleServices', 'create', array( 'name' => $name ?: ( 'Role ' . self::uniq() ) ) );
        $id = (int)$d['id'];
        self::$made['roles'][] = $id;
        foreach ( $policies as $p )
            $this->okWrite( 'expPolicyServices', 'add', array( 'role' => $id, 'module' => $p[0], 'function' => $p[1], 'limitations' => isset( $p[2] ) ? json_encode( $p[2] ) : '{}' ) );
        return $id;
    }

    /** A test user in the fixture group who can use the self-service features, to log in as. */
    protected function memberUser()
    {
        if ( !isset( self::$fixture['member'] ) )
        {
            $group = $this->fixtureGroup();
            $role = $this->newRole( array( array( 'notification', 'use' ), array( 'user', 'password' ), array( 'user', 'preferences' ), array( 'user', 'selfedit' ),
                                           array( 'content', 'read' ) ) );
            $groupObject = eZContentObjectTreeNode::fetch( $group )->attribute( 'contentobject_id' );
            $this->okWrite( 'expRoleServices', 'assign', array( 'id' => $role, 'object' => $groupObject ) );
            self::$fixture['member'] = $this->newUser( $group );
            self::$fixture['role'] = $role;
        }
        return self::$fixture['member'];
    }

    protected static function cleanup()
    {
        $admin = eZUser::fetchByName( 'admin' );
        if ( $admin instanceof eZUser )
            eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ) );
        $db = eZDB::instance();
        foreach ( self::$made['items'] as $id )
            foreach ( array( 'ezcollab_item', 'ezcollab_item_message_link', 'ezcollab_item_participant_link', 'ezcollab_item_status', 'ezcollab_item_group_link' ) as $t )
                $db->query( "DELETE FROM $t WHERE " . ( $t === 'ezcollab_item' ? 'id' : 'collaboration_id' ) . ' = ' . (int)$id );
        foreach ( self::$made['sessions'] as $key )
            $db->query( "DELETE FROM ezsession WHERE session_key = '" . $db->escapeString( $key ) . "'" );
        // users first, then groups, deepest last
        $nodeIds = array();
        foreach ( array_reverse( array_merge( self::$made['objects'], self::$made['groups'] ) ) as $objectId )
        {
            $object = eZContentObject::fetch( (int)$objectId );
            if ( !$object instanceof eZContentObject )
                continue;
            $nodes = $object->assignedNodes();
            if ( !$nodes )
            {
                $object->purge();
                continue;
            }
            foreach ( $nodes as $n )
                $nodeIds[] = (int)$n->attribute( 'node_id' );
        }
        foreach ( array_unique( $nodeIds ) as $nid )
            if ( eZContentObjectTreeNode::fetch( $nid ) )
                eZContentObjectTreeNode::removeSubtrees( array( $nid ), false );
        // an object that went to the trash (the remove service) is purged
        foreach ( array_merge( self::$made['objects'], self::$made['groups'] ) as $objectId )
        {
            $object = eZContentObject::fetch( (int)$objectId );
            if ( $object instanceof eZContentObject )
                $object->purge();
        }
        foreach ( self::$made['roles'] as $roleId )
        {
            $draft = eZRole::fetch( 0, (int)$roleId );
            if ( $draft instanceof eZRole )
                eZRole::removeRole( (int)$draft->attribute( 'id' ) );
            if ( eZRole::fetch( (int)$roleId ) instanceof eZRole )
                eZRole::removeRole( (int)$roleId );
        }
        $db->query( "DELETE FROM ezpreferences WHERE name LIKE 'expservices_test_%'" );
        eZUser::cleanupCache();
        self::$made = array( 'objects' => array(), 'roles' => array(), 'items' => array(), 'groups' => array(), 'sessions' => array() );
        self::$fixture = array();
    }
}
