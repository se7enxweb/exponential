<?php
/**
 * The base of the content service tests (expservices: node, object, class, class group, attribute, version,
 * translation, relation, location, trash, section, state, URL alias, search and content job services).
 *
 * Live-database style: the kernel is started once on the admin siteaccess against the installation's own database,
 * the admin user is logged in, and each service is called through expServiceBase::invoke() like the ezjscore router
 * does. Read services run against existing content (the stable nodes of content.ini). Write services run on test
 * content created in a test folder under the Media root (node 43), which tearDown removes together with every object,
 * class, group, section, state group and alias the test made. There is never a test database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/expservices/content/
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

abstract class expContentServicesTestCase extends PHPUnit\Framework\TestCase
{
    protected static $script = null;
    protected static $bootError = null;
    /** @var int[] object ids created by the test, removed in tearDown */
    protected $createdObjects = array();
    /** @var callable[] clean-up actions run in reverse order in tearDown */
    protected $cleanups = array();
    protected $testFolderNode = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( self::$script !== null || self::$bootError !== null )
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
            if ( !class_exists( 'expServiceBase' ) || !class_exists( 'expNodeServices' ) )
                throw new RuntimeException( 'expservices is not active' );
            self::$script = $script;
        }
        catch ( Throwable $e )
        {
            self::$bootError = $e->getMessage();
        }
    }

    public function setUp(): void
    {
        parent::setUp();
        if ( self::$bootError !== null )
            $this->markTestSkipped( 'Kernel not available: ' . self::$bootError );
        expServiceBase::$trustRequest = true;
        expServiceBase::$postData = array();
        $this->loginAdmin();
    }

    public function tearDown(): void
    {
        if ( self::$bootError === null )
        {
            $this->loginAdmin();
            foreach ( array_reverse( $this->cleanups ) as $cleanup )
            {
                try { call_user_func( $cleanup ); } catch ( Throwable $e ) { }
            }
            $this->cleanups = array();
            foreach ( array_reverse( $this->createdObjects ) as $id )
            {
                try
                {
                    $object = eZContentObject::fetch( $id );
                    if ( $object )
                    {
                        $ids = array();
                        foreach ( $object->assignedNodes() as $n )
                            $ids[] = (int)$n->attribute( 'node_id' );
                        if ( $ids )
                            eZContentObjectTreeNode::removeSubtrees( $ids, false );
                    }
                    eZContentObjectTrashNode::purgeForObject( $id );
                    eZContentObjectOperations::remove( $id );
                }
                catch ( Throwable $e ) { }
            }
            $this->createdObjects = array();
            $this->testFolderNode = null;
            expServiceBase::$trustRequest = null;
            expServiceBase::$postData = null;
        }
        parent::tearDown();
    }

    protected function loginAdmin()
    {
        $admin = eZUser::fetchByName( 'admin' );
        eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ) );
    }

    protected function loginAnonymous()
    {
        $id = (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' );
        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( $id ), $id );
    }

    /** Calls a service as the ezjscore router does. @return array the envelope */
    protected function call( $class, $method, array $args = array(), array $post = array() )
    {
        expServiceBase::$postData = $post;
        return expServiceBase::invoke( $class, $method, $args );
    }

    /** Calls a service, asserts the envelope is ok, returns its data. */
    protected function ok( $class, $method, array $args = array(), array $post = array() )
    {
        $r = $this->call( $class, $method, $args, $post );
        $this->assertTrue( $r['ok'], "$class::$method failed: " . json_encode( isset( $r['error'] ) ? $r['error'] : null ) );
        return $r['data'];
    }

    /** Calls a service, asserts ok, returns the whole envelope (data and meta). */
    protected function envelope( $class, $method, array $args = array(), array $post = array() )
    {
        $r = $this->call( $class, $method, $args, $post );
        $this->assertTrue( $r['ok'], "$class::$method failed: " . json_encode( isset( $r['error'] ) ? $r['error'] : null ) );
        return array( 'data' => $r['data'], 'meta' => (array)$r['meta'] );
    }

    /** Calls a service and asserts it fails with $code. */
    protected function fails( $code, $class, $method, array $args = array(), array $post = array() )
    {
        $r = $this->call( $class, $method, $args, $post );
        $this->assertFalse( $r['ok'], "$class::$method should fail" );
        $this->assertSame( $code, $r['error']['code'], "$class::$method: " . $r['error']['message'] );
        return $r;
    }

    /** Asserts the declaration of every service of a class is complete and its method exists. */
    protected function assertDeclarations( $class, $minimum )
    {
        $this->assertGreaterThanOrEqual( $minimum, count( $class::$services ), "$class declares at least $minimum services" );
        foreach ( $class::$services as $method => $d )
        {
            $this->assertTrue( method_exists( $class, $method ), "$class::$method exists" );
            foreach ( array( 'summary', 'access', 'write', 'args', 'returns' ) as $key )
                $this->assertArrayHasKey( $key, $d, "$class::$method declares $key" );
            $this->assertIsBool( $d['write'], "$class::$method write is a bool" );
            $this->assertNotSame( '', $d['summary'] );
            $this->assertTrue( $d['access'] === 'public' || $d['access'] === 'user' || ( is_array( $d['access'] ) && count( $d['access'] ) === 2 ), "$class::$method access" );
            foreach ( $d['args'] as $name => $type )
                $this->assertContains( $type, array( 'int', 'string', 'bool', 'json', 'list' ), "$class::$method arg $name" );
        }
    }

    // ---------------------------------------------------------------- test content

    protected function mediaRootNode()
    {
        return (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' );
    }

    /** The test folder under the Media root (node 43), created once per test. */
    protected function testFolder()
    {
        if ( $this->testFolderNode )
            return $this->testFolderNode;
        $object = $this->createObject( 43, 'folder', array( 'name' => 'expservices test ' . uniqid() ) );
        $this->testFolderNode = (int)$object->attribute( 'main_node_id' );
        return $this->testFolderNode;
    }

    /** Creates and publishes an object directly through the kernel, remembered for tearDown. */
    protected function createObject( $parentNode, $class, array $attributes )
    {
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => $parentNode, 'class_identifier' => $class, 'attributes' => $attributes ) );
        $this->assertInstanceOf( 'eZContentObject', $object, "created a $class" );
        $this->createdObjects[] = (int)$object->attribute( 'id' );
        return eZContentObject::fetch( (int)$object->attribute( 'id' ) );
    }

    /** A folder in the test folder; returns its node id. */
    protected function createFolder( $name = null, $parent = null )
    {
        $o = $this->createObject( $parent ?: $this->testFolder(), 'folder', array( 'name' => $name ?: 'Test folder ' . uniqid() ) );
        return (int)$o->attribute( 'main_node_id' );
    }

    /** An article with a title and short body in the test folder; returns the node id. */
    protected function createArticle( $name = null, $parent = null )
    {
        $o = $this->createObject( $parent ?: $this->testFolder(), 'article', array( 'title' => $name ?: 'Test article ' . uniqid(), 'intro' => 'x' ) );
        return (int)$o->attribute( 'main_node_id' );
    }

    protected function objectOf( $nodeId )
    {
        return (int)eZContentObjectTreeNode::fetch( (int)$nodeId )->attribute( 'contentobject_id' );
    }

    protected function cleanup( $callable )
    {
        $this->cleanups[] = $callable;
    }
}
