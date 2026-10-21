<?php
/**
 * PKCE (RFC 7636) for the OAuth authorization code flow (expOAuthPkce): the checks of oauth/authorize (S256
 * accepted, plain refused unless AllowPlainPkce, malformed challenges refused), the verification at the token end
 * point (the right verifier redeems the code, a wrong or missing one does not, a code issued without a challenge is
 * refused when PKCE is required), and client authentication of public and confidential clients.
 *
 * No kernel and no database: clients, codes and rest.ini are stand-ins.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/private/oauth/expOAuthPkceTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group oauth
 */

class expOAuthPkceTest extends PHPUnit\Framework\TestCase
{
    // computed independently: printf %s VERIFIER | openssl dgst -sha256 -binary | base64 | tr '+/' '-_' | tr -d '='
    const VERIFIER = 'dBjftJeZ4CVP-mJ92K1qUdtn6O2-yCK3lQrNT3rTtsw';
    const CHALLENGE = 'Fu7DwriVMYUK_uIfg3yACE6SZ22UhWekgh_0o9vhH28';

    private static function ini( array $values = array() )
    {
        return new class( $values )
        {
            private $values;
            public function __construct( array $values ) { $this->values = $values; }
            public function hasVariable( $section, $name ) { return $section === 'OAuthSettings' && isset( $this->values[$name] ); }
            public function variable( $section, $name ) { return $this->values[$name]; }
        };
    }

    private static function client( $secret )
    {
        return new class( $secret )
        {
            public $client_secret;
            public function __construct( $secret ) { $this->client_secret = $secret; }
            public function validateSecret( $secret )
            {
                return is_string( $secret ) && is_string( $this->client_secret ) && $this->client_secret !== ''
                       && hash_equals( $this->client_secret, $secret );
            }
        };
    }

    private static function code( $challenge = null, $method = null )
    {
        $code = new stdClass();
        $code->code_challenge = $challenge;
        $code->code_challenge_method = $method;
        return $code;
    }

    public function testS256MatchesAnIndependentlyComputedValue()
    {
        $this->assertSame( self::CHALLENGE, expOAuthPkce::s256( self::VERIFIER ) );
    }

    public function testS256ChallengeIsAccepted()
    {
        $this->assertNull( expOAuthPkce::checkChallenge( self::CHALLENGE, 'S256', false ) );
        $this->assertNull( expOAuthPkce::checkChallenge( false, false, false ), 'a request without PKCE' );
    }

    public static function refusedChallengeProvider()
    {
        return array(
            'plain, not allowed'       => array( self::VERIFIER, 'plain' ),
            'no method means plain'    => array( self::VERIFIER, false ),
            'unknown method'           => array( self::CHALLENGE, 'S512' ),
            'method in lower case'     => array( self::CHALLENGE, 's256' ),
            'S256 too short'           => array( substr( self::CHALLENGE, 1 ), 'S256' ),
            'S256 with padding'        => array( substr( self::CHALLENGE, 1 ) . '=', 'S256' ),
            'S256 with a newline'      => array( self::CHALLENGE . "\n", 'S256' ),
            'empty challenge'          => array( '', 'S256' ),
            'challenge as array'       => array( array( self::CHALLENGE ), 'S256' ),
            'method without challenge' => array( false, 'S256' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('refusedChallengeProvider')]
    public function testAMalformedOrPlainChallengeIsRefused( $challenge, $method )
    {
        $this->assertIsString( expOAuthPkce::checkChallenge( $challenge, $method, false ) );
    }

    public function testPlainIsAcceptedOnlyWhenAllowed()
    {
        $this->assertNull( expOAuthPkce::checkChallenge( self::VERIFIER, 'plain', true ) );
        $this->assertNull( expOAuthPkce::checkChallenge( self::VERIFIER, false, true ) );
        $this->assertSame( 'plain', expOAuthPkce::normalizeMethod( false ) );
        $this->assertFalse( expOAuthPkce::plainAllowed( self::ini() ), 'plain allowed by default' );
    }

    public function testTheRightVerifierRedeemsAnS256Code()
    {
        $this->assertNull( expOAuthPkce::checkRedemption( self::client( '' ), self::code( self::CHALLENGE, 'S256' ), self::VERIFIER, self::ini() ) );
        $this->assertNull( expOAuthPkce::checkRedemption( self::client( 's3cret' ), self::code( self::CHALLENGE, 'S256' ), self::VERIFIER, self::ini() ) );
    }

    public static function wrongVerifierProvider()
    {
        return array(
            'other verifier'        => array( strrev( self::VERIFIER ) ),
            'the challenge itself'  => array( self::CHALLENGE ),
            'too short'             => array( 'abc' ),
            'not a string'          => array( array( self::VERIFIER ) ),
            'empty'                 => array( '' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('wrongVerifierProvider')]
    public function testAWrongVerifierIsRefused( $verifier )
    {
        $this->assertSame( 'Invalid code_verifier',
                           expOAuthPkce::checkRedemption( self::client( '' ), self::code( self::CHALLENGE, 'S256' ), $verifier, self::ini() ) );
    }

    public function testAMissingVerifierIsRefusedForACodeWithAChallenge()
    {
        $this->assertSame( 'Missing code_verifier',
                           expOAuthPkce::checkRedemption( self::client( 's3cret' ), self::code( self::CHALLENGE, 'S256' ), null, self::ini() ) );
    }

    public function testACodeWithoutChallengeIsRefusedWhenPkceIsRequired()
    {
        // a public client must use PKCE by default
        $this->assertSame( 'PKCE is required for this client',
                           expOAuthPkce::checkRedemption( self::client( '' ), self::code(), null, self::ini() ) );
        // a confidential client only when RequirePkceForConfidentialClients is enabled
        $this->assertNull( expOAuthPkce::checkRedemption( self::client( 's3cret' ), self::code(), null, self::ini() ) );
        $this->assertSame( 'PKCE is required for this client',
                           expOAuthPkce::checkRedemption( self::client( 's3cret' ), self::code(), null,
                                                          self::ini( array( 'RequirePkceForConfidentialClients' => 'enabled' ) ) ) );
        // and a public client is let through without PKCE only when the requirement is switched off
        $this->assertNull( expOAuthPkce::checkRedemption( self::client( '' ), self::code(), null,
                                                          self::ini( array( 'RequirePkceForPublicClients' => 'disabled' ) ) ) );
    }

    public function testAVerifierForACodeWithoutChallengeIsRefused()
    {
        $this->assertIsString( expOAuthPkce::checkRedemption( self::client( 's3cret' ), self::code(), self::VERIFIER, self::ini() ) );
    }

    public function testAPlainCodeIsNotRedeemedOncePlainIsSwitchedOff()
    {
        $code = self::code( self::VERIFIER, 'plain' );
        $this->assertNull( expOAuthPkce::checkRedemption( self::client( '' ), $code, self::VERIFIER, self::ini( array( 'AllowPlainPkce' => 'enabled' ) ) ) );
        $this->assertSame( 'Invalid code_verifier', expOAuthPkce::checkRedemption( self::client( '' ), $code, self::VERIFIER, self::ini() ) );
    }

    public function testClientAuthentication()
    {
        // a confidential client needs its secret, as before
        $this->assertTrue( expOAuthPkce::clientAuthenticates( self::client( 's3cret' ), 's3cret' ) );
        $this->assertFalse( expOAuthPkce::clientAuthenticates( self::client( 's3cret' ), null ) );
        $this->assertFalse( expOAuthPkce::clientAuthenticates( self::client( 's3cret' ), 'wrong' ) );
        // a public client sends none
        $this->assertTrue( expOAuthPkce::clientAuthenticates( self::client( '' ), null ) );
        $this->assertTrue( expOAuthPkce::clientAuthenticates( self::client( null ), '' ) );
        $this->assertFalse( expOAuthPkce::clientAuthenticates( self::client( '' ), 'anything' ) );
    }

    public function testRequirementDefaults()
    {
        $this->assertTrue( expOAuthPkce::isRequiredFor( self::client( '' ), self::ini() ) );
        $this->assertFalse( expOAuthPkce::isRequiredFor( self::client( 's3cret' ), self::ini() ) );
        $ini = (string)file_get_contents( dirname( __DIR__, 5 ) . '/settings/rest.ini' );
        $this->assertMatchesRegularExpression( '/^RequirePkceForPublicClients=enabled$/m', $ini );
        $this->assertMatchesRegularExpression( '/^RequirePkceForConfidentialClients=disabled$/m', $ini );
        $this->assertMatchesRegularExpression( '/^AllowPlainPkce=disabled$/m', $ini );
    }
}
