<?php
/**
 * File containing expOAuthPasswordGrantTest class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\TestCase;

/**
 * The OAuth2 password grant (expOAuthPasswordGrant): which clients may use it, which grant types the token endpoint
 * offers, the decision after the password check and the failure limit per login name. No database: users are stubs,
 * settings are a stub, the lock check is passed in.
 */
class expOAuthPasswordGrantTest extends TestCase
{
    protected $savedClients = null;

    protected function setUp(): void
    {
        $ini = eZINI::instance( 'rest.ini' );
        $this->savedClients = $ini->hasVariable( 'OAuthSettings', 'PasswordGrantClients' )
            ? $ini->variable( 'OAuthSettings', 'PasswordGrantClients' ) : array();
    }

    protected function tearDown(): void
    {
        eZINI::instance( 'rest.ini' )->setVariable( 'OAuthSettings', 'PasswordGrantClients', $this->savedClients );
    }

    private static function user( $enabled, $id = 42 )
    {
        return new expOAuthPasswordGrantTestUser( $enabled, $id );
    }

    private static function unlocked()
    {
        return function( $userID ) { return true; };
    }

    private static function locked()
    {
        return function( $userID ) { return false; };
    }

    public function testNoClientIsAllowedByDefault()
    {
        self::assertSame( array(), expOAuthPasswordGrant::clients( new expOAuthPasswordGrantTestIni( array() ) ) );
        self::assertSame( array(), expOAuthPasswordGrant::clients( new expOAuthPasswordGrantTestIni( array( 'PasswordGrantClients' => array() ) ) ) );
    }

    public function testShippedSettingsAllowNoClient()
    {
        $file = eZINI::fetchFromFile( 'settings/rest.ini' );
        self::assertSame( array(), expOAuthPasswordGrant::clients( $file ), 'settings/rest.ini lists no client' );
        self::assertSame( array( 10, 900 ), expOAuthPasswordGrant::limits( $file ) );
    }

    public function testClientsSkipEmptyEntries()
    {
        $ini = new expOAuthPasswordGrantTestIni( array( 'PasswordGrantClients' => array( '', ' app1 ', 'app2', array( 'x' ) ) ) );
        self::assertSame( array( 'app1', 'app2' ), expOAuthPasswordGrant::clients( $ini ) );
    }

    public function testClientAllowedOnlyWhenListed()
    {
        self::assertTrue( expOAuthPasswordGrant::clientAllowed( 'app1', array( 'app0', 'app1' ) ) );
        self::assertFalse( expOAuthPasswordGrant::clientAllowed( 'app2', array( 'app0', 'app1' ) ) );
        self::assertFalse( expOAuthPasswordGrant::clientAllowed( 'APP1', array( 'app1' ) ), 'client identifiers are compared exactly' );
        self::assertFalse( expOAuthPasswordGrant::clientAllowed( 'app1', array() ) );
        self::assertFalse( expOAuthPasswordGrant::clientAllowed( '', array( '' ) ), 'an empty identifier never matches' );
        self::assertFalse( expOAuthPasswordGrant::clientAllowed( null, array( 'app1' ) ) );
        self::assertFalse( expOAuthPasswordGrant::clientAllowed( array( 'app1' ), array( 'app1' ) ) );
    }

    public function testLimitsDefaultAndBounds()
    {
        self::assertSame( array( 10, 900 ), expOAuthPasswordGrant::limits( new expOAuthPasswordGrantTestIni( array() ) ) );
        self::assertSame( array( 0, 1 ), expOAuthPasswordGrant::limits( new expOAuthPasswordGrantTestIni(
            array( 'PasswordGrantMaxFailures' => '-3', 'PasswordGrantFailureWindow' => '0' ) ) ) );
    }

    public function testGrantTypesOfferPasswordOnlyWithAClient()
    {
        self::assertSame( array( 'authorization_code', 'refresh_token' ), ezpRestOauthTokenController::grantTypes( array() ) );
        self::assertSame( array( 'authorization_code', 'refresh_token', 'password' ), ezpRestOauthTokenController::grantTypes( array( 'app1' ) ) );
    }

    public function testPasswordGrantIsUnsupportedWithoutAClient()
    {
        eZINI::instance( 'rest.ini' )->setVariable( 'OAuthSettings', 'PasswordGrantClients', array() );
        $request = new ezpRestRequest();
        $request->post = array( 'grant_type' => 'password', 'client_id' => 'app1', 'username' => 'u', 'password' => 'p' );
        $controller = new ezpRestOauthTokenController( 'handleRequest', $request );

        $this->expectException( ezpOauthInvalidRequestException::class );
        $this->expectExceptionMessage( 'unsupported_grant_type' );
        $controller->doHandleRequest();
    }

    public function testPasswordGrantNamesMissingCredentials()
    {
        eZINI::instance( 'rest.ini' )->setVariable( 'OAuthSettings', 'PasswordGrantClients', array( 'app1' ) );
        $request = new ezpRestRequest();
        $request->post = array( 'grant_type' => 'password', 'client_id' => 'app1' );
        $controller = new ezpRestOauthTokenController( 'handleRequest', $request );

        $this->expectException( ezpOauthInvalidRequestException::class );
        $this->expectExceptionMessage( 'Missing username, password parameters.' );
        $controller->doHandleRequest();
    }

    public function testPasswordGrantRefusesCredentialsPostedAsLists()
    {
        eZINI::instance( 'rest.ini' )->setVariable( 'OAuthSettings', 'PasswordGrantClients', array( 'app1' ) );
        $request = new ezpRestRequest();
        $request->post = array( 'grant_type' => 'password', 'client_id' => 'app1', 'username' => array( 'u' ), 'password' => 'p' );
        $controller = new ezpRestOauthTokenController( 'handleRequest', $request );

        $this->expectException( ezpOauthInvalidRequestException::class );
        $this->expectExceptionMessage( 'Invalid username parameter.' );
        $controller->doHandleRequest();
    }

    public function testRightPasswordOfAnEnabledUserIsAccepted()
    {
        self::assertNull( expOAuthPasswordGrant::refusal( ezcAuthenticationFilter::STATUS_OK, self::user( true ), null, false, self::unlocked() ) );
    }

    public static function refusedSignIns()
    {
        return array(
            'wrong password' => array( expRestPasswordAuthFilter::STATUS_INVALID_CREDENTIALS, true, true ),
            'disabled account, right password' => array( expRestPasswordAuthFilter::STATUS_USER_DISABLED, false, true ),
            'disabled account the filter let pass' => array( ezcAuthenticationFilter::STATUS_OK, false, true ),
            'locked by failed logins' => array( ezcAuthenticationFilter::STATUS_OK, true, false ),
            'no user' => array( ezcAuthenticationFilter::STATUS_OK, null, true ),
        );
    }

    /**
     * Wrong credentials and an account that may not sign in get one answer, so it does not tell whether the account
     * exists or is disabled.
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'refusedSignIns' )]
    public function testRefusedSignInsGetOneAnswer( $status, $enabled, $unlocked )
    {
        $user = $enabled === null ? null : self::user( $enabled );
        self::assertSame( 'invalid_grant: The user name or password is not right.',
                          expOAuthPasswordGrant::refusal( $status, $user, null, false, $unlocked ? self::unlocked() : self::locked() ) );
    }

    public function testSecondFactorRefusalIsPassedOn()
    {
        $refusal = array( 'reason' => 'second_factor_required', 'message' => 'Send the code.', 'status' => 401, 'channel' => 'rest' );
        self::assertSame( 'invalid_grant: second_factor_required: Send the code.',
                          expOAuthPasswordGrant::refusal( ezcAuthenticationFilter::STATUS_OK, self::user( true ), $refusal, false, self::unlocked() ) );
    }

    public function testPasswordChangeRequiredIsRefused()
    {
        self::assertStringStartsWith( 'invalid_grant: password_change_required: ',
                                      expOAuthPasswordGrant::refusal( ezcAuthenticationFilter::STATUS_OK, self::user( true ), null, true, self::unlocked() ) );
    }

    public function testFailuresBlockTheLoginAfterTheLimitUntilTheWindowEnds()
    {
        $state = array();
        $now = 1000;
        for ( $i = 1; $i <= 3; $i++ )
        {
            self::assertSame( 0, expOAuthPasswordGrant::throttleWait( $state, 3, 900, $now ), "attempt $i may go on" );
            $state = expOAuthPasswordGrant::throttleRecord( $state, true, 900, $now );
            $now += 10;
        }
        self::assertSame( array( 'start' => 1000, 'count' => 3 ), $state );
        self::assertSame( 870, expOAuthPasswordGrant::throttleWait( $state, 3, 900, 1030 ) );
        self::assertSame( 1, expOAuthPasswordGrant::throttleWait( $state, 3, 900, 1899 ) );
        self::assertSame( 0, expOAuthPasswordGrant::throttleWait( $state, 3, 900, 1900 ), 'the window is over' );
        self::assertSame( array( 'start' => 1900, 'count' => 1 ), expOAuthPasswordGrant::throttleRecord( $state, true, 900, 1900 ),
                          'a failure after the window starts a new one' );
    }

    public function testSuccessClearsTheFailures()
    {
        self::assertSame( array(), expOAuthPasswordGrant::throttleRecord( array( 'start' => 1000, 'count' => 2 ), false, 900, 1010 ) );
    }

    public function testNoLimitWhenMaxIsZero()
    {
        self::assertSame( 0, expOAuthPasswordGrant::throttleWait( array( 'start' => 1000, 'count' => 99 ), 0, 900, 1001 ) );
    }

    public function testClockGoingBackDoesNotBlock()
    {
        self::assertSame( 0, expOAuthPasswordGrant::throttleWait( array( 'start' => 2000, 'count' => 99 ), 3, 900, 1000 ) );
    }

    public function testThrottleKeyIgnoresCaseAndSpaceAndNamesNoLogin()
    {
        $key = expOAuthPasswordGrant::throttleKey( 'Someone@Example.com' );
        self::assertSame( $key, expOAuthPasswordGrant::throttleKey( ' someone@example.com ' ) );
        self::assertNotSame( $key, expOAuthPasswordGrant::throttleKey( 'someone-else@example.com' ) );
        self::assertMatchesRegularExpression( '/^[0-9a-f]{64}$/', $key );
        self::assertStringNotContainsString( 'someone', $key );
    }
}

/**
 * eZUser without database: enabled state and id are given.
 */
class expOAuthPasswordGrantTestUser extends eZUser
{
    private $stubEnabled;
    private $stubId;

    public function __construct( $enabled, $id )
    {
        $this->stubEnabled = $enabled;
        $this->stubId = $id;
    }

    function isEnabled( $useCache = true )
    {
        return $this->stubEnabled;
    }

    function attribute( $attr, $noFunction = false )
    {
        return $attr === 'contentobject_id' ? $this->stubId : null;
    }
}

/**
 * The [OAuthSettings] block of rest.ini, given.
 */
class expOAuthPasswordGrantTestIni
{
    private $values;

    public function __construct( array $values )
    {
        $this->values = $values;
    }

    public function hasVariable( $group, $name )
    {
        return $group === 'OAuthSettings' && array_key_exists( $name, $this->values );
    }

    public function variable( $group, $name )
    {
        return $this->hasVariable( $group, $name ) ? $this->values[$name] : false;
    }
}
