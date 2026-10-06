<?php
/**
 * The base of the commerce and community service tests (expservices).
 *
 * Live-database style: the kernel is started once on the admin siteaccess against the installation's own database,
 * the admin user is logged in, and each service is called through expServiceBase::invoke() like the ezjscore router
 * does. Read services run against existing content. Write services run on test content created in a test folder
 * under the Media root (node 43) which tearDown removes. Baskets exist only for a test session key and are removed.
 * No orders or payments are created, and there is never a test database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/expservices/commerce/ tests/tests/extension/expservices/community/
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

abstract class expCommerceTestCase extends PHPUnit\Framework\TestCase
{
    protected static $script = null;
    protected static $bootError = null;
    /** @var int[] object ids created by the test, removed in tearDown */
    protected $createdObjects = array();
    /** @var string[] test session keys whose baskets and wish lists are removed in tearDown */
    protected $sessionKeys = array();
    protected $testFolderNode = null;
    protected $savedSession = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // every test here calls the services against the installed site: skip the class without one (CI)
        ezpLiveInstallation::requireOrSkip();
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
            if ( !class_exists( 'expServiceBase' ) )
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
            if ( $this->testFolderNode )
            {
                $db = eZDB::instance();
                foreach ( $db->arrayQuery( "SELECT DISTINCT contentobject_id FROM ezcontentobject_trash WHERE path_string LIKE '%/" . (int)$this->testFolderNode . "/%'" ) as $row )
                    if ( $o = eZContentObject::fetch( (int)$row['contentobject_id'] ) )
                        $o->purge();
            }
            foreach ( array_reverse( $this->createdObjects ) as $id )
                eZContentObjectOperations::remove( $id );
            $this->createdObjects = array();
            foreach ( $this->sessionKeys as $key )
                $this->removeSessionData( $key );
            $this->sessionKeys = array();
            if ( $this->savedSession !== null )
                eZHTTPTool::instance()->setSessionID( $this->savedSession );
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
        $user = eZUser::fetch( $id );
        eZUser::setCurrentlyLoggedInUser( $user, $id );
    }

    /** Calls a service as the ezjscore router does. @return array the envelope */
    protected function call( $class, $method, array $args = array(), array $post = array() )
    {
        expServiceBase::$postData = $post;
        return expServiceBase::invoke( $class, $method, $args );
    }

    /** Calls a service and asserts the envelope is ok, returns its data. */
    protected function ok( $class, $method, array $args = array(), array $post = array() )
    {
        $r = $this->call( $class, $method, $args, $post );
        $this->assertTrue( $r['ok'], "$class::$method failed: " . json_encode( $r['error'] ?? null ) );
        return $r['data'];
    }

    /** Calls a service and asserts it fails with $code. */
    protected function fails( $code, $class, $method, array $args = array(), array $post = array() )
    {
        $r = $this->call( $class, $method, $args, $post );
        $this->assertFalse( $r['ok'], "$class::$method should fail" );
        $this->assertSame( $code, $r['error']['code'], "$class::$method: " . $r['error']['message'] );
        return $r;
    }

    /** The meta of the last paged answer, via a fresh call. */
    protected function meta( $class, $method, array $args = array() )
    {
        $r = $this->call( $class, $method, $args );
        $this->assertTrue( $r['ok'], json_encode( $r['error'] ?? null ) );
        return (array)$r['meta'];
    }

    // ---------------------------------------------------------------- test content

    /** The test folder under the Media root (node 43), created once per test. */
    protected function testFolder()
    {
        if ( $this->testFolderNode )
            return $this->testFolderNode;
        $object = $this->createObject( 43, 'folder', array( 'name' => 'expservices test ' . uniqid() ) );
        $this->testFolderNode = (int)$object->attribute( 'main_node_id' );
        return $this->testFolderNode;
    }

    /** Creates and publishes an object, remembered for tearDown. */
    protected function createObject( $parentNode, $class, array $attributes )
    {
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => $parentNode, 'class_identifier' => $class, 'attributes' => $attributes ) );
        $this->assertInstanceOf( 'eZContentObject', $object, "created a $class" );
        $this->createdObjects[] = (int)$object->attribute( 'id' );
        return $object;
    }

    /** A product in the test folder; returns the node id. */
    protected function createProduct( $price = '12.5', $name = null )
    {
        $o = $this->createObject( $this->testFolder(), 'product', array( 'name' => $name ?: 'Test product ' . uniqid(), 'product_number' => 'T' . mt_rand( 1000, 9999 ),
            'price' => $price . '|1|1' ) );
        return (int)$o->attribute( 'main_node_id' );
    }

    // ---------------------------------------------------------------- session baskets

    /** Makes a test session the current one: baskets and wish lists are then the test session's. */
    protected function testSession()
    {
        $http = eZHTTPTool::instance();
        if ( $this->savedSession === null )
            $this->savedSession = (string)$http->sessionID();
        $key = 'exptest' . bin2hex( random_bytes( 8 ) );
        $http->setSessionID( $key );
        $this->sessionKeys[] = $key;
        return $key;
    }

    protected function removeSessionData( $key )
    {
        $db = eZDB::instance();
        $k = $db->escapeString( $key );
        foreach ( $db->arrayQuery( "SELECT productcollection_id FROM ezbasket WHERE session_id='$k'" ) as $row )
        {
            $id = (int)$row['productcollection_id'];
            $db->query( "DELETE FROM ezproductcollection_item_opt WHERE item_id IN (SELECT id FROM ezproductcollection_item WHERE productcollection_id=$id)" );
            $db->query( "DELETE FROM ezproductcollection_item WHERE productcollection_id=$id" );
            $db->query( "DELETE FROM ezproductcollection WHERE id=$id" );
        }
        $db->query( "DELETE FROM ezbasket WHERE session_id='$k'" );
    }
}
