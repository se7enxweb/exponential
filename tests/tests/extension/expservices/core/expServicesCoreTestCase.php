<?php
/**
 * The common base of the expservices core tests: the live installation, the admin siteaccess, the admin user
 * (or the anonymous user) logged in. No test database is ever created: read-only services run against the
 * existing site, writes are tested with dry runs or with the request check overridden.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/expservices/core/
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** A domain class used by the base tests only (not registered in ezjscore.ini). */
class expServicesFixtureServices extends expServiceBase
{
    public static $services = array(
        'open' => array( 'summary' => 's', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'r' ),
        'members' => array( 'summary' => 's', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'r' ),
        'setupOnly' => array( 'summary' => 's', 'access' => array( 'setup', 'setup' ), 'write' => false, 'args' => array(), 'returns' => 'r' ),
        'nobody' => array( 'summary' => 's', 'access' => array( 'nomodule', 'nofunction' ), 'write' => false, 'args' => array(), 'returns' => 'r' ),
        'badAccess' => array( 'summary' => 's', 'access' => 7, 'write' => false, 'args' => array(), 'returns' => 'r' ),
        'change' => array( 'summary' => 's', 'access' => 'user', 'write' => true, 'args' => array(), 'returns' => 'r' ),
        'fails' => array( 'summary' => 's', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'r' ),
        'conflict' => array( 'summary' => 's', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'r' ),
        'typeError' => array( 'summary' => 's', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'r' ),
        'bare' => array( 'summary' => 's', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'r' ),
        'listing' => array( 'summary' => 's', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'r' ),
    );

    public static function open( $args ) { self::guard( __FUNCTION__ ); return self::ok( array( 'hello' => 'world' ), array( 'k' => 1 ) ); }
    public static function members( $args ) { self::guard( __FUNCTION__ ); return self::ok( 'in' ); }
    public static function setupOnly( $args ) { self::guard( __FUNCTION__ ); return self::ok( 'setup' ); }
    public static function nobody( $args ) { self::guard( __FUNCTION__ ); return self::ok( 'nobody' ); }
    public static function badAccess( $args ) { self::guard( __FUNCTION__ ); return self::ok( 'x' ); }
    public static function change( $args ) { self::guard( __FUNCTION__ ); return self::ok( 'changed' ); }
    public static function fails( $args ) { throw new expServiceException( 'bad input', 422 ); }
    public static function conflict( $args ) { throw new expServiceException( 'in the way', 409 ); }
    public static function typeError( $args ) { return strlen( array() ); }
    public static function bare( $args ) { return array( 'plain' => true ); }
    public static function listing( $args ) { return self::pageOf( range( 1, 10 ), $args, 0, 1 ); }
    public static function undeclared( $args ) { return self::ok( 1 ); }

    // the protected helpers, public for the tests
    public static function t_arg( array $args, $i, $type ) { return func_num_args() > 3 ? self::arg( $args, $i, $type, func_get_arg( 3 ) ) : self::arg( $args, $i, $type ); }
    public static function t_post( $name, $type ) { return func_num_args() > 2 ? self::post( $name, $type, func_get_arg( 2 ) ) : self::post( $name, $type ); }
    public static function t_cast( $v, $type ) { return self::cast( $v, $type ); }
    public static function t_ok( $d, array $m = array() ) { return self::ok( $d, $m ); }
    public static function t_page( array $i, $t, $o, $l ) { return self::page( $i, $t, $o, $l ); }
    public static function t_paging( array $a, $l, $o ) { return self::paging( $a, $l, $o ); }
    public static function t_node( $id, $f = 'read' ) { return self::node( $id, $f ); }
    public static function t_can( $m, $f ) { return self::can( $m, $f ); }
    public static function t_guard( $m ) { self::guard( $m ); return true; }
    public static function t_audit( $n, array $e ) { return self::audit( $n, $e ); }
}

abstract class expServicesCoreTestCase extends PHPUnit\Framework\TestCase
{
    protected static $script = null;
    protected static $bootError = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::boot();
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
        parent::tearDown();
    }

    protected static function boot()
    {
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

    /** Calls a service the way ezjscore does and returns the envelope. */
    protected function call( $class, $method, array $args = array() )
    {
        return expServiceBase::invoke( $class, $method, $args );
    }

    /** Calls a service and asserts the envelope is ok; returns it. */
    protected function ok( $class, $method, array $args = array() )
    {
        $r = $this->call( $class, $method, $args );
        $this->assertTrue( $r['ok'], "$class::$method failed: " . ( isset( $r['error'] ) ? json_encode( $r['error'] ) : '' ) );
        $this->assertArrayHasKey( 'data', $r );
        $this->assertArrayHasKey( 'meta', $r );
        return $r;
    }

    protected function assertError( array $r, $code )
    {
        $this->assertFalse( $r['ok'] );
        $this->assertSame( $code, $r['error']['code'], $r['error']['message'] );
        $this->assertNotSame( '', $r['error']['message'] );
    }

    protected function assertPaged( array $r )
    {
        foreach ( array( 'total', 'offset', 'limit', 'count', 'has_more' ) as $k )
            $this->assertArrayHasKey( $k, $r['meta'], "meta.$k" );
        $this->assertSame( count( $r['data'] ), $r['meta']['count'] );
    }
}
