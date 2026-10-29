<?php
/**
 * The passkey policy, the names of authenticators, the relying party of a multi-host installation, the attestation
 * asked for, the passkey alerts and the sign-in that counts as "Verify it's you", without a database
 * (doc/eng-us/guides/passkeys.md).
 *
 *  PP-01 the shipped settings: optional, password sign-in on, every list empty, the exempt views listed
 *  PP-02 decide(): the strictest of the default and the user's groups; password sign-in off in any group wins; the
 *        account's own entry wins over both
 *  PP-03 the password is refused only for an account that has a passkey and whose policy turns it off; the hint of
 *        the sign-in form shows where any setting or handler can turn it off
 *  PP-04 the last passkey stays while the account could not sign in without it
 *  PP-05 gate(): required sends every request, preferred one GET per session, optional none; exempt views and an
 *        account with a passkey go on
 *  PP-06 authenticator names: built-in, site names win, zeros and malformed AAGUIDs name nothing
 *  PP-07 settings: RelyingPartyIDMap per host (only the host or a parent of it), Attestation none/indirect/direct,
 *        creation options carry it
 *  PP-08 alerts: a passkey added, removed or revoked mails the owner; a refused registration does not
 *  PP-09 a sign-in counts as "Verify it's you" only when it asked for a combination that would be offered
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expPasskeyPolicyTest extends PHPUnit\Framework\TestCase
{
    /** @return array the [PasskeySettings] of the shipped settings/site.ini, as eZINI would give them */
    private static function shippedRaw()
    {
        $ini = file_get_contents( dirname( __DIR__, 5 ) . '/settings/site.ini' );
        $raw = array();
        $in = false;
        foreach ( explode( "\n", $ini ) as $line )
        {
            $line = rtrim( $line, "\r" );
            if ( preg_match( '/^\[(.+)\]$/', $line, $m ) )
            {
                $in = $m[1] === 'PasskeySettings';
                continue;
            }
            if ( !$in || $line === '' || $line[0] === '#' )
                continue;
            if ( preg_match( '/^([A-Za-z]+)\[\]$/', $line, $m ) )
                $raw[$m[1]] = array();
            elseif ( preg_match( '/^([A-Za-z]+)\[\]=(.*)$/', $line, $m ) )
                $raw[$m[1]][] = $m[2];
            elseif ( preg_match( '/^([A-Za-z]+)\[([^\]]+)\]=(.*)$/', $line, $m ) )
                $raw[$m[1]][$m[2]] = $m[3];
            elseif ( preg_match( '/^([A-Za-z]+)=(.*)$/', $line, $m ) )
                $raw[$m[1]] = $m[2];
        }
        return $raw;
    }

    /** PP-01 */
    public function testShipped()
    {
        $s = expPasskeyPolicy::parse( self::shippedRaw() );
        $this->assertSame( 'optional', $s['policy'] );
        $this->assertTrue( $s['password_sign_in'] );
        $this->assertSame( array(), $s['group_policy'] );
        $this->assertSame( array(), $s['user_policy'] );
        $this->assertSame( array(), $s['handlers'] );
        $this->assertFalse( expPasskeyPolicy::passwordOffAnywhere( $s ) );
        foreach ( array( 'user/passkeys', 'user/logout', 'user/login', 'user2fa' ) as $view )
            $this->assertContains( $view, $s['exempt_views'] );
        $this->assertSame( 'none', self::shippedRaw()['Attestation'] );
    }

    /** PP-02 */
    public function testDecide()
    {
        $s = expPasskeyPolicy::parse( array( 'Policy' => 'preferred', 'PasswordSignIn' => 'enabled',
                                             'GroupPolicy' => array( '13' => 'required', '14' => 'optional', 'x' => 'required' ),
                                             'GroupPasswordSignIn' => array( '12' => 'disabled' ),
                                             'UserPolicy' => array( '99' => 'optional' ), 'UserPasswordSignIn' => array( '99' => 'enabled' ) ) );
        $this->assertSame( array( 'policy' => 'preferred', 'password_sign_in' => true ), expPasskeyPolicy::decide( $s, 10, array( 11 ) ) );
        $this->assertSame( array( 'policy' => 'required', 'password_sign_in' => true ), expPasskeyPolicy::decide( $s, 10, array( 14, 13 ) ) );
        $this->assertSame( array( 'policy' => 'preferred', 'password_sign_in' => false ), expPasskeyPolicy::decide( $s, 10, array( 12, 14 ) ),
                           'a group cannot lower the default' );
        $this->assertSame( array( 'policy' => 'optional', 'password_sign_in' => true ), expPasskeyPolicy::decide( $s, 99, array( 12, 13 ) ),
                           'the account\'s own entry wins' );
        $this->assertSame( 'optional', expPasskeyPolicy::policyValue( 'strict' ) );
        $this->assertSame( 'required', expPasskeyPolicy::policyValue( ' Required ' ) );
    }

    /** PP-03 */
    public function testPasswordRefused()
    {
        $off = array( 'policy' => 'optional', 'password_sign_in' => false );
        $on = array( 'policy' => 'required', 'password_sign_in' => true );
        $this->assertTrue( expPasskeyPolicy::refusesPassword( $off, 1 ) );
        $this->assertFalse( expPasskeyPolicy::refusesPassword( $off, 0 ), 'no passkey yet: the password still signs in' );
        $this->assertFalse( expPasskeyPolicy::refusesPassword( $on, 3 ) );
        $this->assertTrue( expPasskeyPolicy::passwordOffAnywhere( expPasskeyPolicy::parse( array( 'GroupPasswordSignIn' => array( '5' => 'disabled' ) ) ) ) );
        $this->assertTrue( expPasskeyPolicy::passwordOffAnywhere( expPasskeyPolicy::parse( array( 'PolicyHandlers' => array( 'X' ) ) ) ) );
        $this->assertTrue( expPasskeyPolicy::passwordOffAnywhere( expPasskeyPolicy::parse( array( 'PasswordSignIn' => 'disabled' ) ) ) );
    }

    /** PP-04 */
    public function testMayRemove()
    {
        $open = array( 'policy' => 'optional', 'password_sign_in' => true );
        $this->assertTrue( expPasskeyPolicy::mayRemove( $open, 1, true ) );
        $this->assertFalse( expPasskeyPolicy::mayRemove( $open, 1, false ), 'no password: the last passkey is the only way in' );
        $this->assertFalse( expPasskeyPolicy::mayRemove( array( 'policy' => 'optional', 'password_sign_in' => false ), 1, true ) );
        $this->assertFalse( expPasskeyPolicy::mayRemove( array( 'policy' => 'required', 'password_sign_in' => true ), 1, true ) );
        $this->assertTrue( expPasskeyPolicy::mayRemove( array( 'policy' => 'required', 'password_sign_in' => false ), 2, false ) );
    }

    /** PP-05 */
    public function testGate()
    {
        $this->assertSame( 'require', expPasskeyPolicy::gate( 'required', 0, true, false, 'POST' ) );
        $this->assertNull( expPasskeyPolicy::gate( 'required', 1, false, false, 'GET' ) );
        $this->assertNull( expPasskeyPolicy::gate( 'required', 0, false, true, 'GET' ), 'exempt view' );
        $this->assertSame( 'suggest', expPasskeyPolicy::gate( 'preferred', 0, false, false, 'GET' ) );
        $this->assertNull( expPasskeyPolicy::gate( 'preferred', 0, true, false, 'GET' ), 'once per session' );
        $this->assertNull( expPasskeyPolicy::gate( 'preferred', 0, false, false, 'POST' ), 'a form is never lost to a suggestion' );
        $this->assertNull( expPasskeyPolicy::gate( 'optional', 0, false, false, 'GET' ) );
        $this->assertTrue( expPasskeyPolicy::isExempt( 'user2fa', 'verify', array( 'user2fa' ) ) );
        $this->assertTrue( expPasskeyPolicy::isExempt( 'user', 'passkeys', array( 'user/passkeys' ) ) );
        $this->assertFalse( expPasskeyPolicy::isExempt( 'user', 'edit', array( 'user/passkeys' ) ) );
    }

    /** PP-06 */
    public function testAuthenticatorNames()
    {
        $this->assertSame( 'iCloud Keychain', expPasskeyAuthenticators::name( 'FBFC3007-154E-4ECC-8C0B-6E020557D7BD' ) );
        $this->assertSame( 'Google Password Manager', expPasskeyAuthenticators::name( 'ea9b8d664d011d213ce4b6b48cb575d4' ) );
        $this->assertSame( '', expPasskeyAuthenticators::name( expPasskeyAuthenticators::ZERO ) );
        $this->assertSame( '', expPasskeyAuthenticators::name( 'not-an-aaguid' ) );
        $this->assertSame( '', expPasskeyAuthenticators::name( '11111111-2222-3333-4444-555555555555' ) );
        $site = expPasskeySettings::authenticatorNames( array( '11111111-2222-3333-4444-555555555555' => 'Our hardware key',
                                                              'FBFC3007-154E-4ECC-8C0B-6E020557D7BD' => 'Apple passwords', 'bad' => 'x', '00000000-0000-0000-0000-000000000001' => '' ) );
        $this->assertSame( 'Our hardware key', expPasskeyAuthenticators::name( '11111111-2222-3333-4444-555555555555', $site ) );
        $this->assertSame( 'Apple passwords', expPasskeyAuthenticators::name( 'fbfc3007-154e-4ecc-8c0b-6e020557d7bd', $site ), 'a site name wins' );
        $this->assertCount( 2, $site );
        foreach ( array_keys( expPasskeyAuthenticators::KNOWN ) as $aaguid )
            $this->assertSame( $aaguid, expPasskeyAuthenticators::normalise( $aaguid ) );
    }

    /** PP-07 */
    public function testRelyingPartyMapAndAttestation()
    {
        $map = array( 'RelyingPartyIDMap' => array( 'admin.example.com' => 'example.com', 'Shop.Example.org' => 'example.org', 'evil.example.net' => 'example.com' ) );
        $this->assertSame( 'example.com', expPasskeySettings::fromValues( $map, 'https://admin.example.com' )->rpId() );
        $this->assertSame( 'example.org', expPasskeySettings::fromValues( $map, 'https://shop.example.org' )->rpId(), 'host names in any case' );
        $this->assertSame( 'evil.example.net', expPasskeySettings::fromValues( $map, 'https://evil.example.net' )->rpId(),
                           'a mapped id that is not the host or a parent of it falls back to the host' );
        $this->assertSame( 'www.example.com', expPasskeySettings::fromValues( $map + array( 'RelyingPartyID' => '' ), 'https://www.example.com' )->rpId() );
        $this->assertSame( 'none', expPasskeySettings::fromValues( array(), 'https://a.example' )->attestation() );
        $this->assertSame( 'indirect', expPasskeySettings::fromValues( array( 'Attestation' => 'Indirect' ), 'https://a.example' )->attestation() );
        $this->assertSame( 'none', expPasskeySettings::fromValues( array( 'Attestation' => 'enterprise' ), 'https://a.example' )->attestation() );
        $o = expWebAuthn::creationOptions( array( 'id' => 'a.example', 'name' => 'A' ), array( 'id' => 'u', 'name' => 'u', 'displayName' => 'U' ),
                                           'c', array( -7 ), array(), 'required', 1000, 'direct' );
        $this->assertSame( 'direct', $o['attestation'] );
        $o = expWebAuthn::creationOptions( array( 'id' => 'a.example', 'name' => 'A' ), array( 'id' => 'u', 'name' => 'u', 'displayName' => 'U' ), 'c', array( -7 ) );
        $this->assertSame( 'none', $o['attestation'] );
    }

    /** PP-08 */
    public function testAlerts()
    {
        $s = array( 'enabled' => true, 'threshold' => 5 );
        $now = 1800000000;
        $row = array( 'target_type' => 'user', 'target_id' => 14, 'created' => $now - 5, 'result' => 'success' );
        $this->assertSame( 'passkey_added', expUserSecurityAlerts::decide( $row + array( 'event_name' => 'access.passkey.add' ), $s, $now ) );
        $this->assertSame( 'passkey_removed', expUserSecurityAlerts::decide( $row + array( 'event_name' => 'access.passkey.remove' ), $s, $now ) );
        $this->assertSame( 'passkey_removed', expUserSecurityAlerts::decide( $row + array( 'event_name' => 'access.passkey.revoke' ), $s, $now ) );
        $this->assertNull( expUserSecurityAlerts::decide( array( 'result' => 'failed' ) + $row + array( 'event_name' => 'access.passkey.add' ), $s, $now ) );
        $this->assertNull( expUserSecurityAlerts::decide( $row + array( 'event_name' => 'access.passkey.rename' ), $s, $now ) );
        $this->assertNull( expUserSecurityAlerts::decide( $row + array( 'event_name' => 'access.passkey.add' ), array( 'enabled' => false ) + $s, $now ) );
    }

    /** PP-09 */
    public function testSignInCounts()
    {
        $f = expReauth::parseFactors( array() );
        $this->assertTrue( expReauth::signInCounts( array( 'password' ), $f, array( 'password' ) ) );
        $this->assertFalse( expReauth::signInCounts( array( 'password', 'passkey' ), $f, array( 'password' ) ),
                            'the password alone does not stand for password and passkey' );
        $this->assertTrue( expReauth::signInCounts( array( 'password', 'code' ), $f, array( 'password', 'code' ) ) );
        $this->assertTrue( expReauth::signInCounts( array( 'password', 'passkey', 'code' ), $f, array( 'password', 'code' ) ),
                           'the two-step sign-in with the app is as strong as what would be offered beside the passkey' );
        $this->assertFalse( expReauth::signInCounts( array( 'password', 'passkey' ), $f, array( 'passkey' ) ) );
        $this->assertTrue( expReauth::signInCounts( array( 'passkey' ), $f, array( 'passkey' ) ), 'an account without a password' );
        $this->assertTrue( expReauth::signInCounts( array( 'password', 'passkey' ), $f, array( 'password', 'passkey' ) ) );
    }
}
