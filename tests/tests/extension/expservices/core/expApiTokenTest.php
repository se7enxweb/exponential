<?php
/**
 * Personal API tokens: expsession::tokenCreate/tokenList/tokenRevoke and the bearer sign-in of the base.
 * Tokens made here are removed in tearDown. Live database, no test database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../commerce/expCommerceTestCase.php';

class expApiTokenTest extends expCommerceTestCase
{
    protected $tokenIds = array();

    public function tearDown(): void
    {
        unset( $_SERVER['HTTP_AUTHORIZATION'], $_SERVER['HTTP_X_EXP_TOKEN'], $_SERVER['REQUEST_METHOD'] );
        if ( self::$bootError === null )
            foreach ( $this->tokenIds as $id )
                eZPersistentObject::removeObject( expServiceToken::definition(), array( 'id' => $id ) );
        parent::tearDown();
    }

    protected function create( $name = 'test token', $days = 90 )
    {
        $d = $this->ok( 'expSessionServices', 'tokenCreate', array(), array( 'name' => $name, 'expires_in_days' => $days ) );
        $this->tokenIds[] = $d['id'];
        return $d;
    }

    protected function bearer( $token )
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
    }

    public function testCreateReturnsTheTokenOnceAndStoresOnlyItsHash()
    {
        $d = $this->create( 'phone' );
        $this->assertStringStartsWith( 'expt_', $d['token'] );
        $this->assertSame( 53, strlen( $d['token'] ) );
        $this->assertSame( 'phone', $d['name'] );
        $this->assertNotNull( $d['expires'] );
        $row = eZPersistentObject::fetchObject( expServiceToken::definition(), null, array( 'id' => $d['id'] ) );
        $this->assertSame( hash( 'sha256', $d['token'] ), $row->attribute( 'token_hash' ) );
        $this->assertStringNotContainsString( $d['token'], json_encode( $row->attribute( 'token_hash' ) . $row->attribute( 'token_hint' ) ) );
    }

    public function testListShowsOwnTokensNeverTheToken()
    {
        $d = $this->create( 'listed' );
        $list = $this->ok( 'expSessionServices', 'tokenList' );
        $ids = array_column( $list, 'id' );
        $this->assertContains( $d['id'], $ids );
        $this->assertStringNotContainsString( $d['token'], json_encode( $list ) );
        $mine = $list[array_search( $d['id'], $ids )];
        $this->assertSame( array( 'id', 'name', 'hint', 'created', 'last_used', 'expires', 'revoked', 'valid' ), array_keys( $mine ) );
        $this->assertNull( $mine['last_used'] );
        $this->assertTrue( $mine['valid'] );
    }

    public function testInputIsValidated()
    {
        $this->fails( 400, 'expSessionServices', 'tokenCreate', array(), array() );
        $this->fails( 422, 'expSessionServices', 'tokenCreate', array(), array( 'name' => 'x', 'expires_in_days' => 0 ) );
        $this->fails( 422, 'expSessionServices', 'tokenCreate', array(), array( 'name' => 'x', 'expires_in_days' => 400 ) );
        $this->fails( 422, 'expSessionServices', 'tokenCreate', array(), array( 'name' => str_repeat( 'n', 101 ) ) );
        $this->fails( 404, 'expSessionServices', 'tokenRevoke', array(), array( 'id' => 99999999 ) );
    }

    public function testAnonymousCannotUseTheTokenServices()
    {
        $this->loginAnonymous();
        $this->fails( 401, 'expSessionServices', 'tokenCreate', array(), array( 'name' => 'x' ) );
        $this->fails( 401, 'expSessionServices', 'tokenList' );
        $this->fails( 401, 'expSessionServices', 'tokenRevoke', array(), array( 'id' => 1 ) );
    }

    public function testBearerSignsTheRequestInAndNothingStays()
    {
        $d = $this->create();
        $this->loginAnonymous();
        $this->assertTrue( $this->ok( 'expSessionServices', 'whoami' )['anonymous'] );
        $this->bearer( $d['token'] );
        $me = $this->ok( 'expSessionServices', 'whoami' );
        $this->assertSame( 'admin', $me['login'] );
        $this->assertTrue( $me['registered'] );
        $this->assertFalse( expServiceBase::$viaToken, 'the token sign-in ends with the request' );
        unset( $_SERVER['HTTP_AUTHORIZATION'] );
        $this->assertTrue( $this->ok( 'expSessionServices', 'whoami' )['anonymous'], 'the user is back to what it was' );
        $row = expServiceToken::fetchById( $d['id'] );
        $this->assertGreaterThan( 0, (int)$row->attribute( 'last_used' ) );
    }

    public function testBearerWorksAsHeaderXExpTokenToo()
    {
        $d = $this->create();
        $this->loginAnonymous();
        $_SERVER['HTTP_X_EXP_TOKEN'] = $d['token'];
        $this->assertSame( 'admin', $this->ok( 'expSessionServices', 'whoami' )['login'] );
    }

    public function testBearerWriteNeedsPostButNoFormToken()
    {
        $d = $this->create();
        $other = $this->create( 'to revoke' );
        $this->loginAnonymous();
        expServiceBase::$trustRequest = null;
        $this->bearer( $d['token'] );
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->fails( 403, 'expSessionServices', 'tokenRevoke', array(), array( 'id' => $other['id'] ) );
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $r = $this->ok( 'expSessionServices', 'tokenRevoke', array(), array( 'id' => $other['id'] ) );
        $this->assertTrue( $r['revoked'] );
    }

    public function testWithoutBearerAWriteStillNeedsTheFormToken()
    {
        if ( expServiceBase::formToken() === null )
            $this->markTestSkipped( 'the form token protection is off' );
        $d = $this->create();
        expServiceBase::$trustRequest = null;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->fails( 403, 'expSessionServices', 'tokenRevoke', array(), array( 'id' => $d['id'] ) );
    }

    public function testATokenCannotCreateTokens()
    {
        $d = $this->create();
        $this->loginAnonymous();
        $this->bearer( $d['token'] );
        $this->fails( 403, 'expSessionServices', 'tokenCreate', array(), array( 'name' => 'chained' ) );
    }

    public function testRevokedTokenIs401()
    {
        $d = $this->create();
        $this->assertTrue( $this->ok( 'expSessionServices', 'tokenRevoke', array(), array( 'id' => $d['id'] ) )['revoked'] );
        $this->loginAnonymous();
        $this->bearer( $d['token'] );
        $r = $this->fails( 401, 'expSessionServices', 'whoami' );
        $this->assertStringContainsString( 'revoked', $r['error']['message'] );
        $this->fails( 401, 'expSessionServices', 'ping', array() );
    }

    public function testExpiredTokenIs401()
    {
        $d = $this->create();
        $row = expServiceToken::fetchById( $d['id'] );
        $row->setAttribute( 'expires', time() - 10 );
        $row->store();
        $this->loginAnonymous();
        $this->bearer( $d['token'] );
        $r = $this->fails( 401, 'expSessionServices', 'whoami' );
        $this->assertStringContainsString( 'expired', $r['error']['message'] );
        $this->assertFalse( $this->listedValid( $d['id'] ) );
    }

    protected function listedValid( $id )
    {
        unset( $_SERVER['HTTP_AUTHORIZATION'] );
        $this->loginAdmin();
        foreach ( $this->ok( 'expSessionServices', 'tokenList' ) as $t )
            if ( $t['id'] === $id )
                return $t['valid'];
        return null;
    }

    public function testUnknownOrMalformedTokenIs401EvenOnPublicServices()
    {
        $this->loginAnonymous();
        $this->bearer( 'expt_' . str_repeat( '0', 48 ) );
        $this->fails( 401, 'expSessionServices', 'whoami' );
        $this->bearer( 'garbage' );
        $this->fails( 401, 'expSessionServices', 'token' );
    }

    public function testOtherUsersTokensAreNotVisibleOrRevocable()
    {
        $d = $this->create();
        $anon = (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' );
        $row = expServiceToken::fetchById( $d['id'] );
        $row->setAttribute( 'user_id', $anon + 1000000 );
        $row->store();
        $this->assertNotContains( $d['id'], array_column( $this->ok( 'expSessionServices', 'tokenList' ), 'id' ) );
        $this->fails( 404, 'expSessionServices', 'tokenRevoke', array(), array( 'id' => $d['id'] ) );
    }

    public function testTheServicesAreDeclaredAndTheTokensAudited()
    {
        foreach ( array( 'tokenCreate' => true, 'tokenList' => false, 'tokenRevoke' => true ) as $m => $write )
            $this->assertSame( $write, expSessionServices::$services[$m]['write'], $m );
        $names = array_keys( expAuditTaxonomy::registry() );
        foreach ( array( 'create', 'revoke', 'failed' ) as $e )
            $this->assertContains( 'access.expservices.token.' . $e, $names );
    }
}
