<?php
/**
 * The signed links of the administrator's "New user registered" mail (expRegistrationAdminAction,
 * kernel/classes/user/expregistrationadminaction.php). DB-less: the key and the used-token store are the in-memory
 * test overrides (expRegistrationAdminAction::setForTest()), so no site secret is read and no ezsite_data row is
 * written. No mail is sent: the eZMail of RA-10 is only built, never handed to a transport.
 *
 *  RA-01 - create() and verify(): the round trip and the five parts (user id, action, expiry, nonce, signature)
 *  RA-02 - Tampering: each part changed, truncated, extra dots, non-digits, another key -> malformed or signature
 *  RA-03 - Expiry: the second of the expiry still works, the one after is "expired"
 *  RA-04 - The expected action: an approve token is refused as "action" where reject is expected
 *  RA-05 - Reuse: an action token is consumed once; a view token any number of times; a bad token marks nothing
 *  RA-06 - create() refuses an unknown action
 *  RA-07 - buildBaseURL(): the setting, the SiteURL (https, loopback and example hosts skipped), the root URL
 *  RA-08 - url(): user/registration/<token> under the base
 *  RA-09 - stateOf() for every combination of facts (the sign-in included), allowedInState() for every action and state
 *  RA-10 - setAlternativeBody(): multipart/alternative with boundary, text part then HTML part, the ezcMail parts
 *  RA-11 - A disabled account without a key that has signed in before is "disabled": not waiting, no approve or reject
 *  RA-12 - The protected accounts (anonymous, administrator, the user who rejects): protectedIDsOf(), isProtected(),
 *          and reject() checks them before anything else
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

if ( !class_exists( 'expRegistrationAdminAction' ) )
    require_once dirname( __DIR__, 5 ) . '/kernel/classes/user/expregistrationadminaction.php';

class expRegistrationAdminActionTest extends PHPUnit\Framework\TestCase
{
    const NOW = 1790000000;
    const TTL = 3600;
    const USER = 4711;

    protected function setUp(): void
    {
        expRegistrationAdminAction::setForTest( str_repeat( "\x5a", 32 ), array() );
    }

    protected function tearDown(): void
    {
        expRegistrationAdminAction::setForTest( null );
    }

    private function token( $action = expRegistrationAdminAction::ACTION_APPROVE, $userID = self::USER )
    {
        return expRegistrationAdminAction::create( $userID, $action, self::TTL, self::NOW );
    }

    /** verify() must refuse $token with $error */
    private function assertRefused( $token, $error, $action = null, $now = self::NOW )
    {
        $this->assertFalse( expRegistrationAdminAction::verify( $token, $action, $now ), "accepted: $token" );
        $this->assertSame( $error, expRegistrationAdminAction::lastError(), "the reason for: $token" );
    }

    // ---- RA-01 ---------------------------------------------------------------------------------------------------

    public function testRoundTripAndParts()
    {
        foreach ( expRegistrationAdminAction::actions() as $action )
        {
            $token = $this->token( $action );
            $parts = explode( '.', $token );
            $this->assertCount( 5, $parts );
            $this->assertSame( (string)self::USER, $parts[0] );
            $this->assertSame( $action, $parts[1] );
            $this->assertSame( (string)( self::NOW + self::TTL ), $parts[2] );
            $this->assertMatchesRegularExpression( '/^[0-9a-f]{24}$/', $parts[3] );
            $this->assertMatchesRegularExpression( '/^[A-Za-z0-9_-]{43}$/', $parts[4] );

            $data = expRegistrationAdminAction::verify( $token, $action, self::NOW );
            $this->assertSame( array( 'user_id' => self::USER, 'action' => $action, 'expiry' => self::NOW + self::TTL, 'nonce' => $parts[3] ), $data );
            $this->assertNull( expRegistrationAdminAction::lastError() );
            // without an expected action, any action is accepted
            $this->assertSame( $data, expRegistrationAdminAction::verify( $token, null, self::NOW ) );
        }
    }

    public function testEachTokenHasItsOwnNonce()
    {
        $a = explode( '.', $this->token() );
        $b = explode( '.', $this->token() );
        $this->assertNotSame( $a[3], $b[3] );
        $this->assertNotSame( $a[4], $b[4] );
    }

    public function testSignIsDeterministicAndMatchesCreate()
    {
        $token = $this->token();
        $parts = explode( '.', $token );
        $this->assertSame( $token, expRegistrationAdminAction::sign( $parts[0], $parts[1], $parts[2], $parts[3] ) );
    }

    public function testTtlBelowOneSecondIsOneSecond()
    {
        $parts = explode( '.', expRegistrationAdminAction::create( self::USER, 'view', 0, self::NOW ) );
        $this->assertSame( (string)( self::NOW + 1 ), $parts[2] );
        $parts = explode( '.', expRegistrationAdminAction::create( self::USER, 'view', -50, self::NOW ) );
        $this->assertSame( (string)( self::NOW + 1 ), $parts[2] );
    }

    // ---- RA-02 ---------------------------------------------------------------------------------------------------

    /** Replaces part $i of a token */
    private function withPart( $token, $i, $value )
    {
        $parts = explode( '.', $token );
        $parts[$i] = $value;
        return implode( '.', $parts );
    }

    public function testEachChangedPartIsRefusedBySignature()
    {
        $token = $this->token();
        $parts = explode( '.', $token );
        $this->assertRefused( $this->withPart( $token, 0, (string)( self::USER + 1 ) ), 'signature' );
        $this->assertRefused( $this->withPart( $token, 1, 'reject' ), 'signature' );
        $this->assertRefused( $this->withPart( $token, 1, 'view' ), 'signature' );
        $this->assertRefused( $this->withPart( $token, 2, (string)( self::NOW + self::TTL + 86400 ) ), 'signature' );
        $nonce = $parts[3];
        $nonce[0] = $nonce[0] === 'a' ? 'b' : 'a';
        $this->assertRefused( $this->withPart( $token, 3, $nonce ), 'signature' );
        $signature = $parts[4];
        $signature[10] = $signature[10] === 'A' ? 'B' : 'A';
        $this->assertRefused( $this->withPart( $token, 4, $signature ), 'signature' );
        // the signature of another token of the same user
        $other = explode( '.', $this->token() );
        $this->assertRefused( $this->withPart( $token, 4, $other[4] ), 'signature' );
    }

    public function testMalformedTokens()
    {
        $token = $this->token();
        $parts = explode( '.', $token );
        $malformed = array(
            '',
            'garbage',
            substr( $token, 0, -1 ),                                  // signature one character short
            substr( $token, 0, strrpos( $token, '.' ) ),               // no signature
            $token . '.',                                             // an extra dot
            $token . '.x',                                            // a sixth part
            '.' . $token,                                             // a leading dot
            str_replace( '.', '..', $token ),                         // doubled dots
            $this->withPart( $token, 0, 'abc' ),                      // non-digit user id
            $this->withPart( $token, 0, '-5' ),                       // negative user id
            $this->withPart( $token, 0, '0' ),                        // user id 0
            $this->withPart( $token, 0, '' ),                         // empty user id
            $this->withPart( $token, 0, '12a' ),
            $this->withPart( $token, 1, 'delete' ),                   // unknown action
            $this->withPart( $token, 1, 'APPROVE' ),
            $this->withPart( $token, 2, '17x' ),                      // non-digit expiry
            $this->withPart( $token, 2, '' ),
            $this->withPart( $token, 2, '-1' ),
            $this->withPart( $token, 3, strtoupper( $parts[3] ) ),    // nonce in capitals
            $this->withPart( $token, 3, substr( $parts[3], 0, 23 ) ), // nonce too short
            $this->withPart( $token, 3, $parts[3] . '0' ),            // nonce too long
            $this->withPart( $token, 3, str_repeat( 'g', 24 ) ),      // not hex
            $this->withPart( $token, 4, substr( $parts[4], 0, 42 ) . '+' ), // not base64url
            $this->withPart( $token, 4, $parts[4] . '=' ),            // padding
        );
        foreach ( $malformed as $bad )
            $this->assertRefused( $bad, 'malformed' );
        // a token that is not a string at all
        $this->assertRefused( null, 'malformed' );
    }

    public function testTokenOfAnotherKeyIsRefused()
    {
        expRegistrationAdminAction::setForTest( str_repeat( "\x33", 32 ), array() );
        $foreign = $this->token();
        expRegistrationAdminAction::setForTest( str_repeat( "\x5a", 32 ), array() );
        $this->assertRefused( $foreign, 'signature' );
        // and the token signed under the right key still works
        $this->assertIsArray( expRegistrationAdminAction::verify( $this->token(), null, self::NOW ) );
    }

    public function testCreateOfUserZeroGivesATokenVerifyRefuses()
    {
        $this->assertRefused( $this->token( 'view', 0 ), 'malformed' );
    }

    // ---- RA-03 ---------------------------------------------------------------------------------------------------

    public function testExpiry()
    {
        $token = $this->token();
        $expiry = self::NOW + self::TTL;
        $this->assertIsArray( expRegistrationAdminAction::verify( $token, null, $expiry - 1 ) );
        $this->assertIsArray( expRegistrationAdminAction::verify( $token, null, $expiry ) );
        $this->assertRefused( $token, 'expired', null, $expiry + 1 );
        $this->assertRefused( $token, 'expired', null, $expiry + 86400 );
        // an expired token is not consumed either
        $this->assertFalse( expRegistrationAdminAction::consume( $token, null, $expiry + 1 ) );
        $this->assertSame( 'expired', expRegistrationAdminAction::lastError() );
        $this->assertTrue( expRegistrationAdminAction::consume( $token, null, $expiry ) !== false );
    }

    // ---- RA-04 ---------------------------------------------------------------------------------------------------

    public function testWrongExpectedAction()
    {
        $approve = $this->token( 'approve' );
        $this->assertRefused( $approve, 'action', 'reject' );
        $this->assertRefused( $approve, 'action', 'view' );
        $this->assertRefused( $this->token( 'view' ), 'action', 'approve' );
        $this->assertRefused( $this->token( 'reject' ), 'action', 'approve' );
        // a refused action does not use the token
        $this->assertFalse( expRegistrationAdminAction::consume( $approve, 'reject', self::NOW ) );
        $this->assertSame( 'action', expRegistrationAdminAction::lastError() );
        $this->assertIsArray( expRegistrationAdminAction::consume( $approve, 'approve', self::NOW ) );
    }

    // ---- RA-05 ---------------------------------------------------------------------------------------------------

    public function testActionTokenIsConsumedOnce()
    {
        foreach ( array( 'approve', 'reject' ) as $action )
        {
            $token = $this->token( $action );
            $nonce = explode( '.', $token )[3];
            $this->assertFalse( expRegistrationAdminAction::isUsed( $nonce ) );
            $data = expRegistrationAdminAction::consume( $token, $action, self::NOW );
            $this->assertIsArray( $data );
            $this->assertSame( $nonce, $data['nonce'] );
            $this->assertTrue( expRegistrationAdminAction::isUsed( $nonce ) );

            $this->assertFalse( expRegistrationAdminAction::consume( $token, $action, self::NOW ) );
            $this->assertSame( 'used', expRegistrationAdminAction::lastError() );
            $this->assertRefused( $token, 'used', $action );
            $this->assertRefused( $token, 'used' );
        }
    }

    public function testViewTokenIsNeverUsedUp()
    {
        $token = $this->token( 'view' );
        $nonce = explode( '.', $token )[3];
        for ( $i = 0; $i < 3; $i++ )
        {
            $this->assertIsArray( expRegistrationAdminAction::consume( $token, 'view', self::NOW ) );
            $this->assertIsArray( expRegistrationAdminAction::verify( $token, 'view', self::NOW ) );
        }
        $this->assertFalse( expRegistrationAdminAction::isUsed( $nonce ) );
    }

    public function testBadTokenMarksNothingUsed()
    {
        $token = $this->token( 'approve' );
        $parts = explode( '.', $token );
        $this->assertFalse( expRegistrationAdminAction::consume( $this->withPart( $token, 0, (string)( self::USER + 1 ) ), null, self::NOW ) );
        $this->assertSame( 'signature', expRegistrationAdminAction::lastError() );
        $this->assertFalse( expRegistrationAdminAction::consume( substr( $token, 0, -3 ), null, self::NOW ) );
        $this->assertSame( 'malformed', expRegistrationAdminAction::lastError() );
        $this->assertFalse( expRegistrationAdminAction::isUsed( $parts[3] ) );
        // the genuine token still works afterwards
        $this->assertIsArray( expRegistrationAdminAction::consume( $token, 'approve', self::NOW ) );
    }

    public function testUsedStoreFromSetForTest()
    {
        $token = $this->token( 'approve' );
        $nonce = explode( '.', $token )[3];
        expRegistrationAdminAction::setForTest( str_repeat( "\x5a", 32 ), array( $nonce => self::NOW + self::TTL ) );
        $this->assertRefused( $token, 'used' );
        $this->assertFalse( expRegistrationAdminAction::markUsed( $nonce, self::NOW + self::TTL ) );
        $this->assertTrue( expRegistrationAdminAction::markUsed( str_repeat( 'a', 24 ), self::NOW + self::TTL ) );
    }

    // ---- RA-06 ---------------------------------------------------------------------------------------------------

    public function testCreateRefusesUnknownAction()
    {
        foreach ( array( 'delete', 'APPROVE', '', 'view.x' ) as $action )
        {
            try
            {
                expRegistrationAdminAction::create( self::USER, $action, self::TTL, self::NOW );
                $this->fail( "create() accepted the action '$action'" );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertStringContainsString( 'Unknown registration action', $e->getMessage() );
            }
        }
    }

    public function testActionsList()
    {
        $this->assertSame( array( 'view', 'approve', 'reject' ), expRegistrationAdminAction::actions() );
    }

    // ---- RA-07 ---------------------------------------------------------------------------------------------------

    /** @return array[] setting, SiteURL, root URL, expected */
    public static function baseURLCases()
    {
        return array(
            'setting with https' => array( 'https://admin.example.net/admin', 'site.example.net', 'https://root.example.net', 'https://admin.example.net/admin' ),
            'setting without scheme' => array( 'admin.example.net/admin', '', '', 'https://admin.example.net/admin' ),
            'setting with http kept' => array( 'http://intranet.example.net/admin', '', '', 'http://intranet.example.net/admin' ),
            'setting scheme in capitals' => array( 'HTTP://intranet.example.net', '', '', 'HTTP://intranet.example.net' ),
            'setting trailing slashes' => array( 'https://admin.example.net/admin//', '', '', 'https://admin.example.net/admin' ),
            'setting with spaces' => array( '  admin.example.net/  ', '', '', 'https://admin.example.net' ),
            'blank setting ignored' => array( '   ', 'site.example.net/admin', '', 'https://site.example.net/admin' ),
            'SiteURL bare' => array( '', 'site.example.net/admin', 'https://root.example.net', 'https://site.example.net/admin' ),
            'SiteURL http made https' => array( '', 'http://site.example.net/admin/', '', 'https://site.example.net/admin' ),
            'SiteURL https' => array( '', 'https://site.example.net', '', 'https://site.example.net' ),
            'SiteURL with port' => array( '', 'site.example.net:8443/admin', '', 'https://site.example.net:8443/admin' ),
            'SiteURL localhost skipped' => array( '', 'localhost/admin', 'http://root.example.net/admin', 'https://root.example.net/admin' ),
            'SiteURL LOCALHOST skipped' => array( '', 'http://LOCALHOST:8080', 'https://root.example.net', 'https://root.example.net' ),
            'SiteURL 127.0.0.1 skipped' => array( '', '127.0.0.1:8080/admin', 'https://root.example.net/admin/', 'https://root.example.net/admin' ),
            'SiteURL example.com skipped' => array( '', 'example.com', 'https://root.example.net', 'https://root.example.net' ),
            'SiteURL www.example.com skipped' => array( '', 'https://www.example.com/admin', 'https://root.example.net', 'https://root.example.net' ),
            'root http made https' => array( '', '', 'http://root.example.net/admin', 'https://root.example.net/admin' ),
            'root https kept' => array( '', '', 'https://root.example.net/', 'https://root.example.net' ),
            'loopback SiteURL, no root' => array( '', 'localhost', '', '' ),
            'all empty' => array( '', '', '', '' ),
            'all null' => array( null, null, null, '' ),
            'setting with another scheme ignored' => array( 'ftp://admin.example.net', 'site.example.net', '', 'https://site.example.net' ),
            'setting protocol-relative' => array( '//admin.example.net/admin', '', '', 'https://admin.example.net/admin' ),
            'SiteURL IPv6 loopback skipped' => array( '', '[::1]:8080/admin', 'https://root.example.net', 'https://root.example.net' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider( 'baseURLCases' )]
    public function testBuildBaseURL( $setting, $siteURL, $root, $expected )
    {
        $this->assertSame( $expected, expRegistrationAdminAction::buildBaseURL( $setting, $siteURL, $root ) );
    }

    // ---- RA-08 ---------------------------------------------------------------------------------------------------

    public function testUrl()
    {
        $token = $this->token();
        $this->assertSame( 'https://admin.example.net/admin/user/registration/' . $token,
                           expRegistrationAdminAction::url( $token, 'https://admin.example.net/admin' ) );
        $this->assertSame( 'https://admin.example.net/user/registration/' . $token,
                           expRegistrationAdminAction::url( $token, 'https://admin.example.net/' ) );
        $this->assertSame( '/user/registration/' . $token, expRegistrationAdminAction::url( $token, '' ) );
        // the token characters need no escaping; anything else is escaped
        $this->assertSame( 'https://a.example.net/user/registration/a%2Fb%20c', expRegistrationAdminAction::url( 'a/b c', 'https://a.example.net' ) );
    }

    // ---- RA-09 ---------------------------------------------------------------------------------------------------

    public function testStateOfEveryCombination()
    {
        foreach ( array( false, true ) as $enabled )
        {
            foreach ( array( false, true ) as $hasKey )
            {
                foreach ( array( 0, 17 ) as $collaborationID )
                {
                    foreach ( array( false, true ) as $published )
                    {
                        foreach ( array( false, true ) as $signedIn )
                        {
                            if ( $collaborationID > 0 )
                                $expected = 'workflow';
                            elseif ( !$enabled )
                                $expected = $hasKey ? 'email' : ( $signedIn ? 'disabled' : 'administrator' );
                            else
                                $expected = $published ? 'active' : 'pending';
                            $this->assertSame( $expected, expRegistrationAdminAction::stateOf( $enabled, $hasKey, $collaborationID, $published, $signedIn ),
                                               var_export( array( $enabled, $hasKey, $collaborationID, $published, $signedIn ), true ) );
                        }
                    }
                }
            }
        }
        $this->assertSame( 'administrator', expRegistrationAdminAction::stateOf( false, false, -1, true ) );
    }

    public function testAllowedInEveryState()
    {
        $expected = array(
            //                 view   approve reject
            'missing' => array( false, false, false ),
            'email' => array( true, true, true ),
            'administrator' => array( true, true, true ),
            'workflow' => array( true, true, true ),
            'pending' => array( true, false, false ),
            'active' => array( true, false, false ),
            'disabled' => array( true, false, false ),
        );
        foreach ( $expected as $state => $allowed )
        {
            foreach ( expRegistrationAdminAction::actions() as $i => $action )
                $this->assertSame( $allowed[$i], expRegistrationAdminAction::allowedInState( $action, $state ), "$action in $state" );
        }
    }

    // ---- RA-11 ---------------------------------------------------------------------------------------------------

    public function testDeactivatedAccountIsNotAWaitingRegistration()
    {
        // disabled, no key, signed in before: an administrator deactivated it
        $state = expRegistrationAdminAction::stateOf( false, false, 0, true, true );
        $this->assertSame( expRegistrationAdminAction::STATE_DISABLED, $state );
        $this->assertTrue( expRegistrationAdminAction::allowedInState( 'view', $state ) );
        $this->assertFalse( expRegistrationAdminAction::allowedInState( 'approve', $state ), 'the old Activate link does not enable it again' );
        $this->assertFalse( expRegistrationAdminAction::allowedInState( 'reject', $state ), 'the old Reject link does not remove it' );
        // the same account before any sign-in still waits for an administrator; the sign-in counts only without a key
        $this->assertSame( 'administrator', expRegistrationAdminAction::stateOf( false, false, 0, true, false ) );
        $this->assertSame( 'email', expRegistrationAdminAction::stateOf( false, true, 0, true, true ) );
        $this->assertSame( 'workflow', expRegistrationAdminAction::stateOf( false, false, 17, false, true ) );
        // the default of the fifth argument keeps the four-argument call as it was
        $this->assertSame( 'administrator', expRegistrationAdminAction::stateOf( false, false, 0, true ) );
    }

    // ---- RA-12 ---------------------------------------------------------------------------------------------------

    public function testProtectedAccounts()
    {
        $protected = expRegistrationAdminAction::protectedIDsOf( 41, 10, 14, 14 );
        sort( $protected );
        $this->assertSame( array( 10, 14, 41 ), $protected, 'current user, anonymous, administrator; no duplicates' );
        $this->assertSame( array( 10 ), expRegistrationAdminAction::protectedIDsOf( 0, 10, 0, 0 ), 'none (0) is left out' );
        foreach ( array( 10, 14, 41 ) as $id )
            $this->assertTrue( expRegistrationAdminAction::isProtected( $id, $protected ), "$id" );
        $this->assertTrue( expRegistrationAdminAction::isProtected( '14', $protected ), 'a string id' );
        $this->assertTrue( expRegistrationAdminAction::isProtected( 0, $protected ), 'no account' );
        $this->assertFalse( expRegistrationAdminAction::isProtected( 4711, $protected ) );
        $this->assertFalse( expRegistrationAdminAction::isProtected( 4711, array() ) );
    }

    public function testRejectChecksTheProtectedAccountsFirst()
    {
        // reject() refuses a protected account before it reads its state or removes anything (source check: the
        // removal needs a database, the order does not)
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/classes/user/expregistrationadminaction.php' );
        $start = strpos( $source, 'public static function reject(' );
        $body = substr( $source, $start, strpos( $source, "\n    }\n", $start ) - $start );
        $guard = strpos( $body, 'self::isProtected(' );
        $this->assertNotFalse( $guard, 'reject() checks isProtected()' );
        $this->assertLessThan( strpos( $body, '->purge()' ), $guard );
        $this->assertLessThan( strpos( $body, "self::audit( 'access.registration.reject'" ), $guard );
        $this->assertStringContainsString( "'reason' => 'protected'", $body );
    }

    // ---- RA-10 ---------------------------------------------------------------------------------------------------

    public function testUnknownActionIsNeverAllowed()
    {
        foreach ( array( 'email', 'administrator', 'workflow', 'active' ) as $state )
            $this->assertFalse( expRegistrationAdminAction::allowedInState( 'delete', $state ), $state );
    }

    public function testSetAlternativeBody()
    {
        $text = "RATEST plain text\nsecond line";
        $html = '<html><body><p>RATEST <b>html</b></p></body></html>';
        $mail = new eZMail();
        expRegistrationAdminAction::setAlternativeBody( $mail, $text, $html );

        $this->assertSame( 'multipart/alternative', $mail->contentType() );
        $boundary = $mail->ContentType['boundary'];
        $this->assertMatchesRegularExpression( '/^exp-alt-[0-9a-f]{24}$/', $boundary );

        $headers = $mail->headers();
        $contentType = null;
        foreach ( $headers as $header )
        {
            if ( $header['name'] === 'Content-Type' )
                $contentType = $header['content'];
        }
        $this->assertIsArray( $contentType );
        $this->assertSame( 'multipart/alternative', $contentType[0] );
        $this->assertContains( 'boundary="' . $boundary . '"', $contentType );

        // the raw body for the transports that send the text: the line ending of the site is normalized here
        $body = str_replace( "\r\n", "\n", $mail->body( false ) );
        $textAt = strpos( $body, "--$boundary\nContent-Type: text/plain; charset=utf-8\n" );
        $htmlAt = strpos( $body, "--$boundary\nContent-Type: text/html; charset=utf-8\n" );
        $closeAt = strpos( $body, "--$boundary--" );
        $this->assertNotFalse( $textAt );
        $this->assertNotFalse( $htmlAt );
        $this->assertNotFalse( $closeAt );
        $this->assertLessThan( $htmlAt, $textAt, 'the text part comes first' );
        $this->assertLessThan( $closeAt, $htmlAt );
        $this->assertSame( 3, substr_count( $body, "--$boundary" ), 'two openings and the closing boundary' );
        $textPart = substr( $body, $textAt, $htmlAt - $textAt );
        $htmlPart = substr( $body, $htmlAt, $closeAt - $htmlAt );
        $this->assertStringContainsString( $text, $textPart );
        $this->assertStringNotContainsString( $html, $textPart );
        $this->assertStringContainsString( $html, $htmlPart );
        $this->assertStringEndsWith( "--$boundary--\n", $body );

        // the ezcMail object for the transport that sends it (SMTP)
        $this->assertInstanceOf( 'ezcMailMultipartAlternative', $mail->Mail->body );
        $parts = $mail->Mail->body->getParts();
        $this->assertCount( 2, $parts );
        $this->assertInstanceOf( 'ezcMailText', $parts[0] );
        $this->assertSame( 'plain', $parts[0]->subType );
        $this->assertSame( $text, $parts[0]->text );
        $this->assertSame( 'utf-8', $parts[0]->charset );
        $this->assertInstanceOf( 'ezcMailText', $parts[1] );
        $this->assertSame( 'html', $parts[1]->subType );
        $this->assertSame( $html, $parts[1]->text );
    }

    public function testSetAlternativeBodyUsesANewBoundaryEachTime()
    {
        $a = new eZMail();
        $b = new eZMail();
        expRegistrationAdminAction::setAlternativeBody( $a, 'x', '<p>x</p>' );
        expRegistrationAdminAction::setAlternativeBody( $b, 'x', '<p>x</p>' );
        $this->assertNotSame( $a->ContentType['boundary'], $b->ContentType['boundary'] );
    }
}
