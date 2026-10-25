<?php
/**
 * File containing ezpOauthGrantChecksTest class
 *
 * Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

use PHPUnit\Framework\TestCase;

/**
 * The decisions the OAuth layer takes about a token's user, about the
 * client a grant was issued to, about a refresh token's lifetime, and about missing token endpoint parameters. No database:
 * the user is a stub, the lock check is passed in.
 */
class ezpOauthGrantChecksTest extends TestCase
{
    private static function user( $enabled, $id = 42 )
    {
        return new ezpOauthGrantChecksTestUser( $enabled, $id );
    }

    private static function lockCheck( $result, &$seen = null )
    {
        return function( $userID ) use ( $result, &$seen )
        {
            $seen = $userID;
            return $result;
        };
    }

    public function testEnabledUnlockedUserMayUseToken()
    {
        $seen = null;
        self::assertTrue( ezpOauthUtility::userMayUseToken( self::user( true, 42 ), self::lockCheck( true, $seen ) ) );
        self::assertSame( 42, $seen, 'the lock check gets the contentobject_id' );
    }

    public function testDisabledUserMayNotUseToken()
    {
        self::assertFalse( ezpOauthUtility::userMayUseToken( self::user( false ), self::lockCheck( true ) ) );
    }

    public function testUserLockedByFailedLoginsMayNotUseToken()
    {
        self::assertFalse( ezpOauthUtility::userMayUseToken( self::user( true ), self::lockCheck( false ) ) );
    }

    public function testMissingUserMayNotUseToken()
    {
        self::assertFalse( ezpOauthUtility::userMayUseToken( null, self::lockCheck( true ) ) );
        self::assertFalse( ezpOauthUtility::userMayUseToken( false, self::lockCheck( true ) ) );
    }

    public static function refusedUsers()
    {
        return array( 'disabled' => array( false, true ), 'locked by failed logins' => array( true, false ), 'missing' => array( null, true ) );
    }

    /**
     * The grant's answer goes to whoever holds the token: a disabled, locked or missing user is refused with the text
     * of a token of another client, not with a reason that tells the account's state.
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'refusedUsers' )]
    public function testRefusedGrantUserDoesNotTellTheAccountState( $enabled, $unlocked )
    {
        $user = $enabled === null ? null : self::user( $enabled );
        try
        {
            ezpOauthUtility::checkGrantUser( $user, 42, 'Specified refresh-token does not exist.', self::lockCheck( $unlocked ) );
            self::fail( 'the grant was not refused' );
        }
        catch ( ezpOauthInvalidRequestException $e )
        {
            self::assertSame( 'invalid_grant: Specified refresh-token does not exist.', $e->getMessage() );
        }
    }

    public function testGrantUserWhoMayUseTheTokenPasses()
    {
        $this->expectNotToPerformAssertions();
        ezpOauthUtility::checkGrantUser( self::user( true ), 42, 'Specified authorization code does not exist.', self::lockCheck( true ) );
    }

    public function testGrantIsBoundToItsClient()
    {
        self::assertTrue( ezpOauthUtility::clientMatches( 'a1b2c3', 'a1b2c3' ) );
        self::assertFalse( ezpOauthUtility::clientMatches( 'a1b2c3', 'ffffff' ) );
        self::assertFalse( ezpOauthUtility::clientMatches( 'a1b2c3', 'A1B2C3' ), 'client ids compare exactly' );
    }

    public function testClientBindingRefusesEmptyAndNonScalarIds()
    {
        self::assertFalse( ezpOauthUtility::clientMatches( '', '' ) );
        self::assertFalse( ezpOauthUtility::clientMatches( null, null ) );
        self::assertFalse( ezpOauthUtility::clientMatches( 'a1b2c3', null ) );
        self::assertFalse( ezpOauthUtility::clientMatches( 'a1b2c3', array( 'a1b2c3' ) ) );
    }

    public function testClientBindingComparesNumericIdsAsStrings()
    {
        // the persistent object map types client_id as string; a numeric id from the request still matches
        self::assertTrue( ezpOauthUtility::clientMatches( '17', 17 ) );
        self::assertFalse( ezpOauthUtility::clientMatches( '017', 17 ) );
    }

    public function testRefreshTokenExpiresAfterItsTTL()
    {
        // issued at 1000 with TokenTTL 3600 (expirytime 4600), RefreshTokenTTL 86400
        self::assertFalse( ezpOauthUtility::refreshTokenExpired( 4600, 3600, 86400, 1000 ) );
        self::assertFalse( ezpOauthUtility::refreshTokenExpired( 4600, 3600, 86400, 87400 ), 'the last second still counts' );
        self::assertTrue( ezpOauthUtility::refreshTokenExpired( 4600, 3600, 86400, 87401 ) );
        // the access token expired long ago, the refresh token is still good
        self::assertFalse( ezpOauthUtility::refreshTokenExpired( 4600, 3600, 86400, 50000 ) );
    }

    public function testRefreshTokenTTLZeroMeansNoExpiry()
    {
        self::assertFalse( ezpOauthUtility::refreshTokenExpired( 4600, 3600, 0, PHP_INT_MAX ) );
        self::assertFalse( ezpOauthUtility::refreshTokenExpired( 0, 3600, 0, PHP_INT_MAX ) );
        self::assertFalse( ezpOauthUtility::refreshTokenExpired( 4600, 3600, -5, PHP_INT_MAX ), 'a negative TTL is no TTL' );
    }

    public function testRefreshTokenWithoutIssueTimeIsRefusedWhileATTLIsSet()
    {
        self::assertTrue( ezpOauthUtility::refreshTokenExpired( 0, 3600, 86400, 1 ) );
        self::assertTrue( ezpOauthUtility::refreshTokenExpired( null, 3600, 86400, 1 ) );
    }

    public function testShippedRefreshTokenTTLIsThirtyDays()
    {
        self::assertSame( '2592000', eZINI::instance( 'rest.ini' )->variable( 'OAuthSettings', 'RefreshTokenTTL' ) );
    }

    public function testAllMissingParamsAreReported()
    {
        self::assertSame( array( 'client_id', 'refresh_token' ),
                          ezpRestOauthTokenController::missingParams( array( 'grant_type' => 'refresh_token' ),
                                                                      array( 'grant_type', 'client_id', 'refresh_token' ) ) );
        self::assertSame( array(), ezpRestOauthTokenController::missingParams( array( 'a' => '1', 'b' => '' ), array( 'a', 'b' ) ) );
    }

    public function testRequestWithoutClientIdIsRefusedBeforeAnyLookup()
    {
        // Measured before the fix: only a missing last parameter was refused; this request went on
        // to fetchByClientId( null ) with an "Undefined array key" warning.
        $request = new ezpRestRequest();
        $request->post = array( 'grant_type' => 'refresh_token', 'client_secret' => 's', 'refresh_token' => 'r' );
        $controller = new ezpRestOauthTokenController( 'handleRequest', $request );

        $this->expectException( ezpOauthInvalidRequestException::class );
        $this->expectExceptionMessage( 'Missing client_id parameter.' );
        $controller->doHandleRequest();
    }

    public function testListParamsAreNamedAsInvalid()
    {
        self::assertSame( array( 'client_id', 'refresh_token' ),
                          ezpRestOauthTokenController::invalidParams( array( 'grant_type' => 'refresh_token', 'client_id' => array( 'x' ), 'refresh_token' => array() ),
                                                                      array( 'grant_type', 'client_id', 'refresh_token' ) ) );
        self::assertSame( array(), ezpRestOauthTokenController::invalidParams( array( 'a' => '1' ), array( 'a', 'missing' ) ) );
    }

    public function testRequestWithAListClientIdIsRefusedBeforeAnyLookup()
    {
        // client_id[]=x used to reach ezpRestClient::fetchByClientId() as an array
        $request = new ezpRestRequest();
        $request->post = array( 'grant_type' => 'refresh_token', 'client_id' => array( 'x' ), 'refresh_token' => 'r' );
        $controller = new ezpRestOauthTokenController( 'handleRequest', $request );

        $this->expectException( ezpOauthInvalidRequestException::class );
        $this->expectExceptionMessage( 'Invalid client_id parameter.' );
        $controller->doHandleRequest();
    }

    public function testRequestWithAListRefreshTokenIsRefusedBeforeAnyLookup()
    {
        $request = new ezpRestRequest();
        $request->post = array( 'grant_type' => 'refresh_token', 'client_id' => 'c', 'refresh_token' => array( 'r' ) );
        $controller = new ezpRestOauthTokenController( 'handleRequest', $request );

        $this->expectException( ezpOauthInvalidRequestException::class );
        $this->expectExceptionMessage( 'Invalid refresh_token parameter.' );
        $controller->doHandleRequest();
    }

    public function testRequestNamesEveryMissingParameter()
    {
        $request = new ezpRestRequest();
        $request->post = array();
        $controller = new ezpRestOauthTokenController( 'handleRequest', $request );

        $this->expectException( ezpOauthInvalidRequestException::class );
        $this->expectExceptionMessage( 'Missing grant_type, client_id parameters.' );
        $controller->doHandleRequest();
    }
}

/**
 * eZUser without database: enabled state and id are given.
 */
class ezpOauthGrantChecksTestUser extends eZUser
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
