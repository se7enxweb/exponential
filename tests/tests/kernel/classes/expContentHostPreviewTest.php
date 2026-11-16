<?php
/**
 * The one-time token of a version preview on the host of its siteaccess (expContentHostPreview): signing and
 * reading back, tampering, expiry, the siteaccess it is bound to, replay, the form of its claims, the lifetime, and
 * the frame-ancestors that let the administration frame the preview.
 *
 * No database, no files except the code read by the last test: the store of used tokens is given in memory.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expContentHostPreviewTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expContentHostPreviewTest extends PHPUnit\Framework\TestCase
{
    const KEY = 'a-test-key-of-the-secret-store';
    const NOW = 1900000000;

    /** @var array nonce => issued (true) or used (false) */
    private $issued = array();

    protected function setUp(): void
    {
        $this->issued = array();
        $issued = &$this->issued;
        expContentHostPreview::setStore( array(
            'issue' => function ( $nonce, $expires, $now ) use ( &$issued ) { $issued[$nonce] = true; return true; },
            'claim' => function ( $nonce ) use ( &$issued )
            {
                if ( empty( $issued[$nonce] ) )
                    return false;
                $issued[$nonce] = false;
                return true;
            },
        ) );
    }

    protected function tearDown(): void
    {
        expContentHostPreview::setStore( null );
    }

    private static function claims( array $change = array() )
    {
        return $change + array( 'u' => 14, 'o' => 9316, 'v' => 83, 'l' => 'ger-DE', 's' => 'wedding_ger', 'm' => 'full', 'p' => 0,
                                'e' => self::NOW + 300, 'n' => str_repeat( 'ab', 12 ), 'a' => 'https://admin.example.com' );
    }

    private function issued( array $change = array() )
    {
        $claims = self::claims( $change );
        $this->issued[$claims['n']] = true;
        return expContentHostPreview::encode( self::KEY, $claims );
    }

    public function testATokenReadsBackToItsClaims()
    {
        $decoded = expContentHostPreview::decode( self::KEY, expContentHostPreview::encode( self::KEY, self::claims() ), self::NOW );
        $this->assertSame( self::claims(), $decoded['claims'] );
    }

    public function testAnotherKeyOrAChangedTokenIsInvalid()
    {
        $token = expContentHostPreview::encode( self::KEY, self::claims() );
        $this->assertSame( array( 'error' => 'invalid' ), expContentHostPreview::decode( 'another-key', $token, self::NOW ) );
        $this->assertSame( array( 'error' => 'invalid' ), expContentHostPreview::decode( '', $token, self::NOW ) );
        list( $payload, $signature ) = explode( '.', $token );
        // claims of another version under the old signature
        $forged = rtrim( strtr( base64_encode( json_encode( self::claims( array( 'v' => 84 ) ) ) ), '+/', '-_' ), '=' );
        $this->assertSame( array( 'error' => 'invalid' ), expContentHostPreview::decode( self::KEY, $forged . '.' . $signature, self::NOW ) );
        $flipped = $signature;
        $flipped[5] = $flipped[5] === 'A' ? 'B' : 'A';
        $this->assertSame( array( 'error' => 'invalid' ), expContentHostPreview::decode( self::KEY, $payload . '.' . $flipped, self::NOW ) );
        foreach ( array( '', 'x', $payload, $token . '.x', str_repeat( 'a', 3000 ) . '.' . $signature, null, 42 ) as $bad )
            $this->assertSame( array( 'error' => 'invalid' ), expContentHostPreview::decode( self::KEY, $bad, self::NOW ), var_export( $bad, true ) );
    }

    public function testEveryClaimIsCoveredBySignature()
    {
        $base = expContentHostPreview::encode( self::KEY, self::claims() );
        foreach ( array( array( 'u' => 15 ), array( 'o' => 9317 ), array( 'l' => 'eng-GB' ), array( 's' => 'main_ger' ), array( 'm' => 'print' ),
                         array( 'p' => 3 ), array( 'e' => self::NOW + 301 ), array( 'n' => str_repeat( 'cd', 12 ) ),
                         array( 'a' => 'https://evil.example.com' ) ) as $change )
            $this->assertNotSame( explode( '.', $base )[1], explode( '.', expContentHostPreview::encode( self::KEY, self::claims( $change ) ) )[1], json_encode( $change ) );
    }

    public function testATokenExpires()
    {
        $token = expContentHostPreview::encode( self::KEY, self::claims() );
        $this->assertArrayHasKey( 'claims', expContentHostPreview::decode( self::KEY, $token, self::NOW + 299 ) );
        $this->assertSame( 'expired', expContentHostPreview::decode( self::KEY, $token, self::NOW + 300 )['error'] );
        $this->assertSame( 'expired', expContentHostPreview::check( self::KEY, $this->issued(), 'wedding_ger', self::NOW + 1000 )['error'] );
    }

    public function testATokenWorksOnlyOnItsSiteaccessAndAWrongOneDoesNotUseItUp()
    {
        $token = $this->issued();
        $this->assertSame( array( 'error' => 'siteaccess' ), expContentHostPreview::check( self::KEY, $token, 'main_ger', self::NOW ) );
        $this->assertArrayHasKey( 'claims', expContentHostPreview::check( self::KEY, $token, 'wedding_ger', self::NOW ) );
    }

    public function testATokenWorksOnce()
    {
        $token = $this->issued();
        $first = expContentHostPreview::check( self::KEY, $token, 'wedding_ger', self::NOW );
        $this->assertSame( 9316, $first['claims']['o'] );
        $this->assertSame( array( 'error' => 'used' ), expContentHostPreview::check( self::KEY, $token, 'wedding_ger', self::NOW ) );
        // a correctly signed token that was never issued here (its row is gone or never written) is refused too
        $never = expContentHostPreview::encode( self::KEY, self::claims( array( 'n' => str_repeat( 'ef', 12 ) ) ) );
        $this->assertSame( array( 'error' => 'used' ), expContentHostPreview::check( self::KEY, $never, 'wedding_ger', self::NOW ) );
    }

    public function testClaimsOfTheWrongFormAreRefused()
    {
        $this->assertTrue( expContentHostPreview::valid( self::claims() ) );
        foreach ( array( array( 'u' => 0 ), array( 'o' => -1 ), array( 'v' => 0 ), array( 'l' => '../x' ), array( 's' => 'a/b' ),
                         array( 's' => '' ), array( 'm' => 'node/view' ), array( 'p' => -2 ), array( 'n' => 'short' ),
                         array( 'a' => 'javascript:alert(1)' ), array( 'a' => 'https://admin.example.com/path' ),
                         array( 'a' => "https://admin.example.com\r\nX: y" ) ) as $change )
            $this->assertFalse( expContentHostPreview::valid( self::claims( $change ) ), json_encode( $change ) );
        $missing = self::claims();
        unset( $missing['a'] );
        $this->assertFalse( expContentHostPreview::valid( $missing ) );
        $bad = expContentHostPreview::encode( self::KEY, self::claims( array( 's' => 'a/b' ) ) );
        $this->assertSame( array( 'error' => 'invalid' ), expContentHostPreview::decode( self::KEY, $bad, self::NOW ) );
    }

    public function testTheLifetimeStaysBetweenOneAndFifteenMinutes()
    {
        $this->assertSame( 300, expContentHostPreview::lifetime( null ) );
        $this->assertSame( 300, expContentHostPreview::lifetime( 0 ) );
        $this->assertSame( 60, expContentHostPreview::lifetime( 5 ) );
        $this->assertSame( 600, expContentHostPreview::lifetime( 600 ) );
        $this->assertSame( 900, expContentHostPreview::lifetime( 86400 ) );
    }

    public function testTheAdministrationIsAddedToFrameAncestorsAndNothingElseChanges()
    {
        $origin = 'https://admin.example.com';
        $this->assertSame( "frame-ancestors 'self' https://admin.example.com", expContentHostPreview::frameAncestors( "frame-ancestors 'self'", $origin ) );
        $this->assertSame( "default-src 'self'; frame-ancestors https://admin.example.com; img-src 'self'",
                           expContentHostPreview::frameAncestors( "default-src 'self'; frame-ancestors 'none'; img-src 'self'", $origin ) );
        $this->assertSame( "frame-ancestors 'self' https://admin.example.com",
                           expContentHostPreview::frameAncestors( "frame-ancestors 'self' https://admin.example.com", $origin ) );
        $this->assertSame( "default-src 'self'", expContentHostPreview::frameAncestors( "default-src 'self'", $origin ) );
        $this->assertSame( "frame-ancestors 'self'", expContentHostPreview::frameAncestors( "frame-ancestors 'self'", "https://x.example.com; script-src *" ) );
    }

    public function testTheViewIsRegisteredWithoutPolicyAndRendersOnlyWithARedeemedToken()
    {
        $root = __DIR__ . '/../../../../';
        $module = file_get_contents( $root . 'kernel/content/module.php' );
        $this->assertMatchesRegularExpression( '/\$ViewList\[\'hostpreview\'\] = array\([^;]*\'script\' => \'hostpreview\.php\',[^;]*\'params\' => array\( \'Token\' \) \);/s', $module );
        $view = file_get_contents( $root . 'kernel/private/classes/views/content/hostpreview.php' );
        $redeem = strpos( $view, 'expContentHostPreview::redeem(' );
        $this->assertNotFalse( $redeem );
        $this->assertGreaterThan( $redeem, strpos( $view, 'Sharedpreview::renderVersion(' ) );
        $this->assertStringContainsString( "header( 'Cache-Control: private, no-store' );", $view );
        $this->assertStringContainsString( 'keepOutOfSharedCaches()', $view );
    }
}
