<?php
/**
 * The base of the media and miscellaneous service tests (image, file, media, tags, layout, audit, subitems,
 * newsletter, sitemap, stats, design, language, url, pdf): the live installation, the admin siteaccess, admin logged
 * in; each service is called through expServiceBase::invoke() as the ezjscore router does. Read services run
 * against existing content. Write services run on test content created under the Media root (node 43), which
 * tearDown removes (objects purged, test tags deleted, changed settings put back). There is never a test database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/expservices/media/ tests/tests/extension/expservices/misc/
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

abstract class expMediaTestCase extends PHPUnit\Framework\TestCase
{
    protected static $script = null;
    protected static $bootError = null;
    /** @var int[] object ids made by a test, purged in tearDown */
    protected $createdObjects = array();
    /** @var callable[] clean-up actions, run in reverse order in tearDown */
    protected $cleanups = array();

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
        foreach ( array_reverse( $this->cleanups ) as $cleanup )
        {
            try
            {
                $cleanup();
            }
            catch ( Throwable $e )
            {
            }
        }
        $this->cleanups = array();
        foreach ( $this->createdObjects as $id )
        {
            $object = eZContentObject::fetch( (int)$id );
            if ( $object )
                $object->purge();
        }
        $this->createdObjects = array();
        unset( $_SERVER['REQUEST_METHOD'] );
        $_POST = array();
        parent::tearDown();
    }

    protected function loginAdmin()
    {
        $admin = eZUser::fetchByName( 'admin' );
        eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ) );
    }

    protected function loginAnonymous()
    {
        $id = eZUser::anonymousId();
        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( $id ), $id );
    }

    /** Calls a service as the router does. A non-empty $post makes it a trusted POST request. */
    protected function call( $class, $method, array $args = array(), array $post = array() )
    {
        if ( $post )
        {
            expServiceBase::$trustRequest = true;
            expServiceBase::$postData = $post;
        }
        $r = expServiceBase::invoke( $class, $method, $args );
        expServiceBase::$trustRequest = null;
        expServiceBase::$postData = null;
        return $r;
    }

    protected function ok( $class, $method, array $args = array(), array $post = array() )
    {
        $r = $this->call( $class, $method, $args, $post );
        $this->assertTrue( $r['ok'], "$class::$method failed: " . ( isset( $r['error'] ) ? json_encode( $r['error'] ) : '' ) );
        $this->assertArrayHasKey( 'data', $r );
        $this->assertArrayHasKey( 'meta', $r );
        return $r;
    }

    protected function fails( $code, $class, $method, array $args = array(), array $post = array() )
    {
        $r = $this->call( $class, $method, $args, $post );
        $this->assertFalse( $r['ok'], "$class::$method should fail" );
        $this->assertSame( $code, $r['error']['code'], $r['error']['message'] );
        return $r;
    }

    protected function assertPaged( array $r )
    {
        foreach ( array( 'total', 'offset', 'limit', 'count', 'has_more' ) as $k )
            $this->assertArrayHasKey( $k, $r['meta'], "meta.$k" );
        $this->assertSame( count( $r['data'] ), $r['meta']['count'] );
    }

    /** Every service of a domain class is declared completely and has its method. */
    protected function assertDeclared( $class, $minimum )
    {
        $this->assertGreaterThanOrEqual( $minimum, count( $class::$services ), "$class declares at least $minimum services" );
        foreach ( $class::$services as $method => $d )
        {
            $this->assertTrue( method_exists( $class, $method ), "$class::$method exists" );
            foreach ( array( 'summary', 'access', 'write', 'args', 'returns' ) as $k )
                $this->assertArrayHasKey( $k, $d, "$class::$method declares $k" );
            $this->assertNotSame( '', $d['summary'] );
            $this->assertIsBool( $d['write'] );
            $this->assertTrue( $d['access'] === 'public' || $d['access'] === 'user' || ( is_array( $d['access'] ) && count( $d['access'] ) === 2 ), "$class::$method access" );
        }
    }

    /** Every declared write service refuses a plain request (no POST) with 403 and every one needs access. */
    protected function assertWritesNeedPost( $class, array $args = array() )
    {
        foreach ( $class::$services as $method => $d )
        {
            if ( empty( $d['write'] ) )
                continue;
            $r = $this->call( $class, $method, isset( $args[$method] ) ? $args[$method] : array( 1, 'x' ) );
            $this->assertFalse( $r['ok'], "$class::$method must not run without POST" );
            $this->assertContains( $r['error']['code'], array( 403, 404, 400 ), "$class::$method refused: " . $r['error']['message'] );
        }
    }

    /** The first published node whose current version has an attribute of the datatype with content, or a skip. */
    protected function nodeWith( $datatype, $needContent = true )
    {
        $rows = eZDB::instance()->arrayQuery(
            "SELECT n.node_id AS node, a.id AS attr, a.contentobject_id AS obj FROM ezcontentobject_attribute a, ezcontentobject o, ezcontentobject_tree n
              WHERE a.data_type_string='" . eZDB::instance()->escapeString( $datatype ) . "' AND a.contentobject_id=o.id AND a.version=o.current_version
                AND n.contentobject_id=o.id AND n.main_node_id=n.node_id AND o.status=1" . ( $needContent && $datatype === 'ezimage' ? " AND a.data_text<>''" : '' ) . ' ORDER BY n.node_id', array( 'limit' => 30 ) );
        foreach ( $rows as $r )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$r['node'] );
            if ( !$node )
                continue;
            foreach ( $node->dataMap() as $a )
                if ( $a->attribute( 'data_type_string' ) === $datatype && ( !$needContent || $a->hasContent() ) )
                    return array( $node, $a );
        }
        $this->markTestSkipped( "No published object with a $datatype attribute" );
    }

    /** Creates a test object under the Media root and registers it for removal. */
    protected function createTestObject( $class, array $attributes )
    {
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => 43, 'class_identifier' => $class, 'creator_id' => 14,
                                                                     'attributes' => $attributes ) );
        if ( !$object )
            $this->markTestSkipped( "Could not create a $class test object" );
        $this->createdObjects[] = (int)$object->attribute( 'id' );
        return $object;
    }

    protected function tableExists( $table )
    {
        $r = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS n FROM ' . $table );
        return is_array( $r );
    }
}
