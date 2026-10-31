<?php
/**
 * Tests of the REST authentication style without authentication (ezpRestNoAuthStyle): the anonymous user comes
 * from the user cache, as eZUser::instance() takes it for a web request, so a REST request that needs nothing else
 * of the database (a cached GraphQL answer) does not open it; the database is asked as before when the default user
 * is not the anonymous user, when the user cache is off, and when the cache does not know the user.
 *
 * The user cache is the test's (a subclass); the database is never reached: a request that would ask it runs an
 * authentication filter of the test instead. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group rest
 */

class ezpRestNoAuthStyleTest extends PHPUnit\Framework\TestCase
{
    /** @var string|false RoleSettings EnableCaching before the test */
    private $enableCaching;

    protected function setUp(): void
    {
        if ( !class_exists( 'ezcMvcRequest' ) || !class_exists( 'ezcAuthentication' ) )
            $this->markTestSkipped( 'the MVC and Authentication components of the Zeta Components are not installed' );
        $ini = eZINI::instance();
        $this->enableCaching = $ini->hasVariable( 'RoleSettings', 'EnableCaching' ) ? $ini->variable( 'RoleSettings', 'EnableCaching' ) : false;
        $ini->setVariable( 'RoleSettings', 'EnableCaching', 'true' );
        ezpRestNoAuthStyleTestStyle::$cache = array();
        ezpRestNoAuthStyleTestStyle::$cacheAsked = 0;
        ezpRestNoAuthStyleTestFilter::$runs = 0;
        ezpRestNoAuthStyleTestFilter::$status = ezcAuthenticationFilter::STATUS_OK;
    }

    protected function tearDown(): void
    {
        if ( $this->enableCaching !== false )
            eZINI::instance()->setVariable( 'RoleSettings', 'EnableCaching', $this->enableCaching );
    }

    private static function anonymousId()
    {
        return (int)eZUser::anonymousId();
    }

    private static function cacheOf( $userId )
    {
        return array( 'info' => array( $userId => array( 'contentobject_id' => $userId, 'login' => 'anonymous', 'email' => 'nobody@example.com',
                                                         'is_enabled' => true, 'password_hash' => '', 'password_hash_type' => 2 ) ),
                      'groups' => array( 42 ), 'roles' => array( 1 ), 'role_limitations' => array(), 'access_array' => array(),
                      'discount_rules' => array() );
    }

    private static function authenticate( $userId, $uri = '/api/graphql' )
    {
        $auth = new ezcAuthentication( new ezcAuthenticationIdCredentials( $userId ) );
        $auth->addFilter( new ezpRestNoAuthStyleTestFilter() );
        $request = new ezpRestRequest();
        $request->uri = $uri;
        $style = new ezpRestNoAuthStyleTestStyle();
        return $style->authenticate( $auth, $request );
    }

    public function testTheAnonymousUserComesFromTheUserCacheWithoutTheDatabase()
    {
        $id = self::anonymousId();
        ezpRestNoAuthStyleTestStyle::$cache = self::cacheOf( $id );
        $user = self::authenticate( $id );
        $this->assertInstanceOf( 'eZUser', $user );
        $this->assertSame( $id, (int)$user->attribute( 'contentobject_id' ) );
        $this->assertSame( 'anonymous', $user->attribute( 'login' ) );
        $this->assertSame( 0, ezpRestNoAuthStyleTestFilter::$runs, 'the authentication filter (a database query) did not run' );
        $this->assertSame( self::cacheOf( $id ), $user->getUserCache(), 'the user carries the cache it was made from' );
    }

    public function testAUserTheCacheDoesNotKnowIsAskedOfTheDatabase()
    {
        $id = self::anonymousId();
        ezpRestNoAuthStyleTestStyle::$cache = array( 'info' => array(), 'groups' => array(), 'roles' => array(),
                                                      'role_limitations' => array(), 'access_array' => array(), 'discount_rules' => array() );
        ezpRestNoAuthStyleTestFilter::$status = ezpNativeUserAuthFilter::STATUS_INVALID_USER;
        try
        {
            self::authenticate( $id );
            $this->fail( 'a user the database does not know either is refused' );
        }
        catch ( ezpUserNotFoundException $e )
        {
        }
        $this->assertSame( 1, ezpRestNoAuthStyleTestFilter::$runs );
    }

    public function testACacheOfAnotherUserIsNotTaken()
    {
        $id = self::anonymousId();
        ezpRestNoAuthStyleTestStyle::$cache = self::cacheOf( $id + 1 );
        ezpRestNoAuthStyleTestFilter::$status = ezpNativeUserAuthFilter::STATUS_INVALID_USER;
        $this->expectException( 'ezpUserNotFoundException' );
        self::authenticate( $id );
    }

    public function testAnotherDefaultUserIsAskedOfTheDatabase()
    {
        $id = self::anonymousId() + 1000;
        ezpRestNoAuthStyleTestStyle::$cache = self::cacheOf( $id );
        ezpRestNoAuthStyleTestFilter::$status = ezpNativeUserAuthFilter::STATUS_INVALID_USER;
        try
        {
            self::authenticate( $id );
            $this->fail( 'a default user other than the anonymous user is checked in the database' );
        }
        catch ( ezpUserNotFoundException $e )
        {
        }
        $this->assertSame( 0, ezpRestNoAuthStyleTestStyle::$cacheAsked );
        $this->assertSame( 1, ezpRestNoAuthStyleTestFilter::$runs );
    }

    public function testWithTheUserCacheOffTheDatabaseIsAsked()
    {
        eZINI::instance()->setVariable( 'RoleSettings', 'EnableCaching', 'false' );
        $id = self::anonymousId();
        ezpRestNoAuthStyleTestStyle::$cache = self::cacheOf( $id );
        ezpRestNoAuthStyleTestFilter::$status = ezpNativeUserAuthFilter::STATUS_INVALID_USER;
        $this->expectException( 'ezpUserNotFoundException' );
        self::authenticate( $id );
    }
}

/**
 * The no-auth style with the test's user cache.
 */
class ezpRestNoAuthStyleTestStyle extends ezpRestNoAuthStyle
{
    /** @var array */
    public static $cache = array();

    /** @var int */
    public static $cacheAsked = 0;

    protected static function anonymousUserCache()
    {
        self::$cacheAsked++;
        return self::$cache;
    }
}

/**
 * Stands in for ezpNativeUserAuthFilter (its count query): counts its runs and answers the test's status.
 */
class ezpRestNoAuthStyleTestFilter extends ezcAuthenticationFilter
{
    /** @var int */
    public static $runs = 0;

    /** @var int */
    public static $status = ezcAuthenticationFilter::STATUS_OK;

    public function run( $credentials )
    {
        self::$runs++;
        return self::$status;
    }
}

?>
