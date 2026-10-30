<?php
/**
 * The parts of the passkeys (doc/eng-us/guides/passkeys.md) that need no database: the challenges a session keeps,
 * the settings ([PasskeySettings]) with their defaults from the request, and the values a passkey row stores.
 *
 *  PK-01 a challenge is accepted once, for its purpose and user, until it ends; any answer naming it uses it up
 *  PK-02 a session keeps at most MAX challenges, ended ones are dropped
 *  PK-03 the relying party id defaults to the host, a parent domain is kept, a foreign one falls back to the host
 *  PK-04 origins default to the request's, listed ones are normalised; algorithms keep only what the PHP can check
 *  PK-05 user verification, challenge time and the passkeys per account are kept within their bounds
 *  PK-06 names are one line, at most 100 characters, the default when empty; transports only the known ones
 *  PK-07 the credential hash is SHA-256 hex
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expPasskeyTest extends PHPUnit\Framework\TestCase
{
    /** PK-01 */
    public function testChallengeIsUsedOnce()
    {
        $c = random_bytes( 32 );
        $list = expWebAuthnChallenge::add( array(), 'login', $c, 0, 1000, 500 );
        $copy = $list;
        $this->assertFalse( expWebAuthnChallenge::take( $copy, 'reauth', $c, 0, 600 ), 'another purpose' );
        $this->assertSame( array(), $copy, 'and it is used up' );
        $copy = $list;
        $this->assertFalse( expWebAuthnChallenge::take( $copy, 'login', $c, 14, 600 ), 'another user' );
        $copy = $list;
        $this->assertFalse( expWebAuthnChallenge::take( $copy, 'login', $c, 0, 1000 ), 'ended' );
        $copy = $list;
        $this->assertFalse( expWebAuthnChallenge::take( $copy, 'login', random_bytes( 32 ), 0, 600 ), 'unknown' );
        $this->assertCount( 1, $copy, 'an unknown answer leaves the others' );
        $this->assertTrue( expWebAuthnChallenge::take( $copy, 'login', $c, 0, 600 ) );
        $this->assertFalse( expWebAuthnChallenge::take( $copy, 'login', $c, 0, 600 ), 'not twice' );
        $this->assertFalse( expWebAuthnChallenge::take( $list, 'login', '', 0, 600 ) );
    }

    /** PK-02 */
    public function testChallengeListIsBounded()
    {
        $list = array();
        for ( $i = 0; $i < 20; $i++ )
            $list = expWebAuthnChallenge::add( $list, 'login', random_bytes( 32 ), 0, 1000 + $i, 500 );
        $this->assertCount( expWebAuthnChallenge::MAX, $list );
        $list = expWebAuthnChallenge::add( $list, 'login', random_bytes( 32 ), 0, 5000, 2000 );
        $this->assertCount( 1, $list, 'the ended ones are dropped' );
    }

    private static function settings( array $raw, $origin = 'https://www.example.invalid' )
    {
        return expPasskeySettings::fromValues( $raw, $origin, 'Example' );
    }

    /** PK-03 */
    public function testRelyingPartyId()
    {
        $this->assertSame( 'www.example.invalid', self::settings( array() )->rpId() );
        $this->assertSame( 'example.invalid', self::settings( array( 'RelyingPartyID' => 'Example.invalid' ) )->rpId() );
        $this->assertSame( 'www.example.invalid', self::settings( array( 'RelyingPartyID' => 'other.invalid' ) )->rpId() );
        $this->assertSame( 'www.example.invalid', self::settings( array( 'RelyingPartyID' => 'ample.invalid' ) )->rpId(), 'not a parent domain' );
        $this->assertSame( 'Example', self::settings( array() )->rpName() );
        $this->assertSame( 'Shop', self::settings( array( 'RelyingPartyName' => ' Shop ' ) )->rpName() );
        $this->assertTrue( self::settings( array() )->enabled() );
        $this->assertFalse( self::settings( array( 'Passkeys' => 'disabled' ) )->enabled() );
        $this->assertFalse( self::settings( array(), false )->enabled(), 'no host, no relying party' );
        $this->assertFalse( self::settings( array( 'SignInForm' => 'disabled' ) )->signInForm() );
    }

    /** PK-04 */
    public function testOriginsAndAlgorithms()
    {
        $this->assertSame( array( 'https://www.example.invalid' ), self::settings( array() )->origins() );
        $this->assertSame( array( 'https://admin.example.invalid', 'http://localhost:8080' ),
                           self::settings( array( 'AllowedOrigins' => array( 'HTTPS://admin.example.invalid:443/', 'http://localhost:8080', 'junk' ) ) )->origins() );
        $s = self::settings( array( 'Algorithms' => array( '-257', '-7', '-7', '-999', 'x' ) ) );
        $this->assertSame( array( -257, -7 ), $s->algorithms() );
        $this->assertSame( array( -7, -257 ), self::settings( array( 'Algorithms' => array( '-999' ) ) )->algorithms() );
        $e = $s->expect( 'c' );
        $this->assertSame( array( 'c', 'www.example.invalid', 'required' ), array( $e['challenge'], $e['rp_id'], $e['user_verification'] ) );
        $this->assertSame( 'preferred', $s->expect( 'c', 'preferred' )['user_verification'] );
    }

    /** PK-05 */
    public function testBounds()
    {
        $s = self::settings( array( 'UserVerification' => 'whatever', 'ChallengeSeconds' => '5', 'MaxPerUser' => '0' ) );
        $this->assertSame( array( 'required', 30, 1 ), array( $s->userVerification(), $s->challengeSeconds(), $s->maxPerUser() ) );
        $s = self::settings( array( 'UserVerification' => 'preferred', 'ChallengeSeconds' => '99999', 'MaxPerUser' => '7' ) );
        $this->assertSame( array( 'preferred', 3600, 7 ), array( $s->userVerification(), $s->challengeSeconds(), $s->maxPerUser() ) );
    }

    /** PK-06 */
    public function testStoredValues()
    {
        $this->assertSame( 'My phone', expPasskey::cleanName( "  My\n\tphone \x07" ) );
        $this->assertSame( 'Default', expPasskey::cleanName( '   ', 'Default' ) );
        $this->assertSame( 'Default', expPasskey::cleanName( array( 'x' ), 'Default' ) );
        $this->assertSame( 100, mb_strlen( expPasskey::cleanName( str_repeat( 'ä', 300 ) ) ) );
        $this->assertSame( 'internal,hybrid', expPasskey::cleanTransports( 'internal, HYBRID,internal,carrier-pigeon' ) );
        $this->assertSame( 'usb,nfc', expPasskey::cleanTransports( array( 'usb', 'nfc' ) ) );
        $this->assertSame( '', expPasskey::cleanTransports( '' ) );
    }

    /** PK-07 */
    public function testCredentialHash()
    {
        $this->assertSame( hash( 'sha256', "\x01\x02" ), expPasskey::hashCredential( "\x01\x02" ) );
        $this->assertSame( 64, strlen( expPasskey::hashCredential( random_bytes( 100 ) ) ) );
    }
}
