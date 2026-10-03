<?php
/**
 * The base of every expservices domain: arguments, envelopes, paging, guard, errors, writes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expServicesCoreTestCase.php';

class expServiceBaseTest extends expServicesCoreTestCase
{
    const F = 'expServicesFixtureServices';

    // ---- arg

    public function testArgInt()
    {
        $this->assertSame( 12, expServicesFixtureServices::t_arg( array( '12' ), 0, 'int' ) );
        $this->assertSame( -3, expServicesFixtureServices::t_arg( array( 'x', '-3' ), 1, 'int' ) );
    }

    public function testArgIntRejectsText()
    {
        $this->expectException( expServiceException::class );
        $this->expectExceptionCode( 400 );
        expServicesFixtureServices::t_arg( array( 'abc' ), 0, 'int' );
    }

    public function testArgIntRejectsFloat()
    {
        $this->expectExceptionCode( 400 );
        expServicesFixtureServices::t_arg( array( '1.5' ), 0, 'int' );
    }

    public function testArgRequiredWhenNoDefault()
    {
        $this->expectExceptionCode( 400 );
        expServicesFixtureServices::t_arg( array(), 0, 'int' );
    }

    public function testArgEmptyIsMissing()
    {
        $this->assertSame( 7, expServicesFixtureServices::t_arg( array( '' ), 0, 'int', 7 ) );
    }

    public function testArgDefaultWhenMissing()
    {
        $this->assertSame( 5, expServicesFixtureServices::t_arg( array(), 0, 'int', 5 ) );
        $this->assertNull( expServicesFixtureServices::t_arg( array(), 3, 'string', null ) );
    }

    public function testArgString()
    {
        $this->assertSame( 'site.ini', expServicesFixtureServices::t_arg( array( 'site.ini' ), 0, 'string' ) );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'boolProvider' )]
    public function testArgBool( $given, $expected )
    {
        $this->assertSame( $expected, expServicesFixtureServices::t_arg( array( $given ), 0, 'bool' ) );
    }

    public static function boolProvider()
    {
        return array( array( '1', true ), array( 'true', true ), array( 'YES', true ), array( 'on', true ),
                      array( '0', false ), array( 'false', false ), array( 'No', false ), array( 'off', false ) );
    }

    public function testArgBoolRejectsOther()
    {
        $this->expectExceptionCode( 400 );
        expServicesFixtureServices::t_arg( array( 'maybe' ), 0, 'bool' );
    }

    public function testArgJson()
    {
        $this->assertSame( array( 'a' => 1, 'b' => array( 2, 3 ) ), expServicesFixtureServices::t_arg( array( '{"a":1,"b":[2,3]}' ), 0, 'json' ) );
    }

    public function testArgJsonRejectsBroken()
    {
        $this->expectExceptionCode( 400 );
        expServicesFixtureServices::t_arg( array( '{a:' ), 0, 'json' );
    }

    public function testArgList()
    {
        $this->assertSame( array( 'a', 'b', 'c' ), expServicesFixtureServices::t_arg( array( 'a, b,,c' ), 0, 'list' ) );
    }

    public function testCastUnknownType()
    {
        $this->expectExceptionCode( 500 );
        expServicesFixtureServices::t_cast( 'x', 'matrix' );
    }

    public function testCastStringRejectsArray()
    {
        $this->expectExceptionCode( 400 );
        expServicesFixtureServices::t_cast( array( 'x' ), 'string' );
    }

    // ---- post

    public function testPostReadsPostData()
    {
        expServiceBase::$postData = array( 'node_id' => '43', 'dry_run' => '1', 'names' => 'a,b' );
        $this->assertSame( 43, expServicesFixtureServices::t_post( 'node_id', 'int' ) );
        $this->assertTrue( expServicesFixtureServices::t_post( 'dry_run', 'bool' ) );
        $this->assertSame( array( 'a', 'b' ), expServicesFixtureServices::t_post( 'names', 'list' ) );
    }

    public function testPostReadsSuperglobal()
    {
        $_POST = array( 'x' => 'y' );
        $this->assertSame( 'y', expServicesFixtureServices::t_post( 'x', 'string' ) );
    }

    public function testPostRequiredAndDefault()
    {
        expServiceBase::$postData = array();
        $this->assertSame( 'd', expServicesFixtureServices::t_post( 'missing', 'string', 'd' ) );
        $this->expectExceptionCode( 400 );
        expServicesFixtureServices::t_post( 'missing', 'string' );
    }

    // ---- envelopes

    public function testOkEnvelope()
    {
        $r = expServicesFixtureServices::t_ok( array( 1 ), array( 'a' => 2 ) );
        $this->assertSame( array( 'ok' => true, 'data' => array( 1 ), 'meta' => array( 'a' => 2 ) ), $r );
    }

    public function testPageEnvelope()
    {
        $r = expServicesFixtureServices::t_page( array( 'a', 'b' ), 5, 0, 2 );
        $this->assertTrue( $r['ok'] );
        $this->assertSame( array( 'total' => 5, 'offset' => 0, 'limit' => 2, 'count' => 2, 'has_more' => true ), $r['meta'] );
        $last = expServicesFixtureServices::t_page( array( 'e' ), 5, 4, 2 );
        $this->assertFalse( $last['meta']['has_more'] );
    }

    public function testErrorEnvelope()
    {
        $this->assertSame( array( 'ok' => false, 'error' => array( 'code' => 404, 'message' => 'x' ) ), expServiceBase::error( 404, 'x' ) );
    }

    // ---- paging

    public function testPagingDefaults()
    {
        $this->assertSame( array( 25, 0 ), expServicesFixtureServices::t_paging( array(), 0, 1 ) );
    }

    public function testPagingClampsToMax()
    {
        $this->assertSame( array( 200, 40 ), expServicesFixtureServices::t_paging( array( '99999', '40' ), 0, 1 ) );
    }

    public function testPagingRejectsNonsense()
    {
        $this->expectExceptionCode( 400 );
        expServicesFixtureServices::t_paging( array( '0', '0' ), 0, 1 );
    }

    public function testPagingRejectsNegativeOffset()
    {
        $this->expectExceptionCode( 400 );
        expServicesFixtureServices::t_paging( array( '5', '-1' ), 0, 1 );
    }

    public function testPageOfSlices()
    {
        $r = $this->ok( self::F, 'listing', array( '3', '4' ) );
        $this->assertSame( array( 5, 6, 7 ), $r['data'] );
        $this->assertSame( 10, $r['meta']['total'] );
        $this->assertTrue( $r['meta']['has_more'] );
        $this->assertPaged( $r );
    }

    public function testPageOfLastWindow()
    {
        $r = $this->ok( self::F, 'listing', array( '5', '8' ) );
        $this->assertSame( array( 9, 10 ), $r['data'] );
        $this->assertFalse( $r['meta']['has_more'] );
    }

    // ---- guard

    public function testGuardPublicForAnonymous()
    {
        $this->loginAnonymous();
        $this->assertSame( 'world', $this->ok( self::F, 'open' )['data']['hello'] );
    }

    public function testGuardUserNeedsLogin()
    {
        $this->loginAnonymous();
        $this->assertError( $this->call( self::F, 'members' ), 401 );
    }

    public function testGuardUserAdmin()
    {
        $this->assertSame( 'in', $this->ok( self::F, 'members' )['data'] );
    }

    public function testGuardPolicyAnonymousIs401()
    {
        $this->loginAnonymous();
        $this->assertError( $this->call( self::F, 'setupOnly' ), 401 );
    }

    public function testGuardPolicyAdmin()
    {
        $this->assertSame( 'setup', $this->ok( self::F, 'setupOnly' )['data'] );
    }

    public function testGuardUnknownPolicyAnonymous()
    {
        $this->loginAnonymous();
        $this->assertError( $this->call( self::F, 'nobody' ), 401 );
    }

    public function testGuardBadAccessDeclaration()
    {
        $this->assertError( $this->call( self::F, 'badAccess' ), 500 );
    }

    public function testGuardUndeclaredMethod()
    {
        $this->expectExceptionCode( 404 );
        expServicesFixtureServices::t_guard( 'undeclared' );
    }

    // ---- writes

    public function testWriteNeedsPost()
    {
        $this->assertError( $this->call( self::F, 'change' ), 403 );
    }

    public function testWriteNeedsToken()
    {
        if ( expServiceBase::formToken() === null )
            $this->markTestSkipped( 'The form token protection is off' );
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $r = $this->call( self::F, 'change' );
        $this->assertError( $r, 403 );
        $this->assertStringContainsString( 'token', $r['error']['message'] );
        $_POST['ezxform_token'] = 'wrong';
        $this->assertError( $this->call( self::F, 'change' ), 403 );
    }

    public function testWriteWithRightToken()
    {
        if ( expServiceBase::formToken() === null )
            $this->markTestSkipped( 'The form token protection is off' );
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['ezxform_token'] = expServiceBase::formToken();
        $this->assertSame( 'changed', $this->ok( self::F, 'change' )['data'] );
    }

    public function testWriteWithHeaderToken()
    {
        if ( expServiceBase::formToken() === null )
            $this->markTestSkipped( 'The form token protection is off' );
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_X_CSRF_TOKEN'] = expServiceBase::formToken();
        try
        {
            $this->assertSame( 'changed', $this->ok( self::F, 'change' )['data'] );
        }
        finally
        {
            unset( $_SERVER['HTTP_X_CSRF_TOKEN'] );
        }
    }

    public function testWriteTrustedRequest()
    {
        expServiceBase::$trustRequest = true;
        $this->assertSame( 'changed', $this->ok( self::F, 'change' )['data'] );
    }

    public function testWriteStillNeedsLogin()
    {
        expServiceBase::$trustRequest = true;
        $this->loginAnonymous();
        $this->assertError( $this->call( self::F, 'change' ), 401 );
    }

    // ---- invoke

    public function testInvokeKeepsEnvelope()
    {
        $r = $this->call( self::F, 'open' );
        $this->assertSame( array( 'ok' => true, 'data' => array( 'hello' => 'world' ), 'meta' => array( 'k' => 1 ) ), $r );
    }

    public function testInvokeWrapsBareArray()
    {
        $r = $this->call( self::F, 'bare' );
        $this->assertTrue( $r['ok'] );
        $this->assertSame( array( 'plain' => true ), $r['data'] );
    }

    public function testInvokeMapsServiceException()
    {
        $this->assertError( $this->call( self::F, 'fails' ), 422 );
        $this->assertError( $this->call( self::F, 'conflict' ), 409 );
    }

    public function testInvokeMapsFaultsTo500()
    {
        $r = $this->call( self::F, 'typeError' );
        $this->assertError( $r, 500 );
    }

    public function testInvokeUndeclaredIs404()
    {
        $this->assertError( $this->call( self::F, 'undeclared' ), 404 );
    }

    public function testInvokeUnknownClassIs404()
    {
        $this->assertError( $this->call( 'stdClass', 'x' ), 404 );
    }

    public function testInvokeThroughTheRouter()
    {
        $router = ezjscServerRouter::getInstance( array( 'expsystem', 'version' ), true, true );
        $this->assertNotNull( $router );
        $env = array();
        $r = $router->call( $env );
        $this->assertTrue( $r['ok'] );
        $this->assertArrayHasKey( 'version', $r['data'] );
    }

    public function testRouterTurnsErrorsIntoTheEnvelope()
    {
        $router = ezjscServerRouter::getInstance( array( 'expsystem', 'phpextensions', 'abc' ), true, true );
        $env = array();
        $r = $router->call( $env );
        $this->assertError( $r, 400 );
    }

    public function testRouterRefusesUnregisteredClass()
    {
        $this->assertNull( ezjscServerRouter::getInstance( array( 'expservicesfixture', 'open' ), true, true ) );
    }

    // ---- node

    public function testNodeReadable()
    {
        $node = expServicesFixtureServices::t_node( 2 );
        $this->assertInstanceOf( 'eZContentObjectTreeNode', $node );
    }

    public function testNodeMissing()
    {
        $this->expectExceptionCode( 404 );
        expServicesFixtureServices::t_node( 99999999 );
    }

    public function testNodeBadId()
    {
        $this->expectExceptionCode( 400 );
        expServicesFixtureServices::t_node( 'x' );
    }

    public function testNodeUnknownRight()
    {
        $this->expectExceptionCode( 500 );
        expServicesFixtureServices::t_node( 2, 'levitate' );
    }

    public function testNodeEditRight()
    {
        $this->assertInstanceOf( 'eZContentObjectTreeNode', expServicesFixtureServices::t_node( 2, 'edit' ) );
    }

    // ---- misc

    public function testCanHelper()
    {
        $this->assertTrue( expServicesFixtureServices::t_can( 'setup', 'setup' ) );
        $this->loginAnonymous();
        $this->assertFalse( expServicesFixtureServices::t_can( 'setup', 'setup' ) );
    }

    public function testAuditNeverThrows()
    {
        $r = expServicesFixtureServices::t_audit( 'not a valid name!', array() );
        $this->assertTrue( $r === null || is_string( $r ) );
    }

    public function testNoCacheTime()
    {
        $this->assertSame( -1, expServicesFixtureServices::getCacheTime( 'open' ) );
    }

    public function testDisabledSwitch()
    {
        $ini = eZINI::instance( 'expservices.ini' );
        $this->assertSame( 'enabled', $ini->variable( 'Services', 'Enabled' ) );
    }
}
