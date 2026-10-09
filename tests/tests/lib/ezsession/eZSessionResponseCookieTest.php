<?php
/**
 * The session cookie in every response and the flags of the is_logged_in cookie, without a web server.
 *
 *  SR-01 - site.ini [Session] CookieAlwaysAddToHttpResponse is off without the setting and with disabled; enabled and
 *          true switch it on
 *  SR-02 - Off, or without a running session, responseCookie() gives nothing, also with a session id left from an
 *          earlier request; on, it gives the cookie of the session running now
 *  SR-03 - Several requests in one process (a persistent worker): each response carries its own session, or none,
 *          never the one of a request before it
 *  SR-04 - sessionCookie(): name, id and the flags of the session cookie; expires from the lifetime, 0 without one
 *  SR-05 - siteCookieOptions(): path, domain, lifetime, Secure and SameSite of the session cookie, never HttpOnly
 *          (scripts of cached pages read is_logged_in); site.ini [Session] decides them as for the session cookie
 *  SR-06 - cookieParams() gives the parameters setCookieParams() sets and changes nothing
 *  SR-07 - A response with the session cookie is private: Cache-Control without public and s-maxage, the browser's
 *          max-age kept, a private or no-store one left as it is, Surrogate-Control no-store
 *  SR-08 - The cookie is not set twice when PHP already set it in the response
 *  SR-09 - The kernel sends both cookies with these options
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZSessionResponseCookieTest extends PHPUnit\Framework\TestCase
{
    /** @var bool */
    private $hasStarted;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->hasStarted = eZSession::hasStarted();
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        $this->setStarted( $this->hasStarted );
        if ( session_status() !== PHP_SESSION_ACTIVE && !headers_sent() )
        {
            session_id( '' );
        }
    }

    /** eZSession's own "a session runs" flag, as a request that started one leaves it. */
    private function setStarted( $started )
    {
        $property = new ReflectionProperty( 'eZSession', 'hasStarted' );
        if ( PHP_VERSION_ID < 80100 )
        {
            $property->setAccessible( true );
        }
        $property->setValue( null, (bool)$started );
    }

    /** A request in which eZSession has started the session $id, or none ($id null), as far as the cookie goes. */
    private function request( $id )
    {
        if ( headers_sent() )
        {
            $this->markTestSkipped( 'headers already sent in this process: session_id() cannot be set' );
        }
        session_id( $id === null ? '' : $id );
        $this->setStarted( $id !== null );
    }

    /** SR-01 */
    public function testTheSetting()
    {
        $this->assertFalse( eZSession::cookieOnEveryResponse(), 'off by default' );
        foreach ( array( 'disabled' => false, 'false' => false, '' => false, 'enabled' => true, 'true' => true, ' Enabled ' => true ) as $value => $on )
        {
            ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieAlwaysAddToHttpResponse', (string)$value );
            $this->assertSame( $on, eZSession::cookieOnEveryResponse(), var_export( $value, true ) );
        }
    }

    /** SR-02 */
    public function testNoCookieWhenOffOrWithoutASession()
    {
        $this->request( 'sr02running0123456789abc' );
        $this->assertNull( eZSession::responseCookie(), 'off by default: nothing, also with a session running' );
        $this->assertFalse( eZSession::sendResponseCookie(), 'off: nothing sent' );

        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieAlwaysAddToHttpResponse', 'enabled' );
        $cookie = eZSession::responseCookie();
        $this->assertNotNull( $cookie, 'on, with a session running' );
        $this->assertSame( session_name(), $cookie['name'] );
        $this->assertSame( 'sr02running0123456789abc', $cookie['value'] );

        $this->setStarted( false );
        $this->assertNull( eZSession::responseCookie(), 'a session id but no session started (an anonymous visitor)' );
        $this->request( null );
        $this->assertNull( eZSession::responseCookie(), 'no session at all' );
    }

    /** SR-03 */
    public function testEachRequestOfAPersistentWorkerCarriesItsOwnSession()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieAlwaysAddToHttpResponse', 'enabled' );

        $this->request( 'useronesession0123456789' );
        $first = eZSession::responseCookie();
        $this->assertSame( 'useronesession0123456789', $first['value'], 'request 1: user one' );
        eZSession::stop();
        $this->assertFalse( eZSession::hasStarted(), 'the end of a request stops the session' );
        $this->assertNull( eZSession::responseCookie(), 'after the end of request 1: nothing, although its id is left in PHP' );

        $this->request( null );
        $this->assertNull( eZSession::responseCookie(), 'request 2: an anonymous visitor gets no session cookie' );

        $this->request( 'usertwosession0123456789' );
        $third = eZSession::responseCookie();
        $this->assertSame( 'usertwosession0123456789', $third['value'], 'request 3: user two, never user one' );
        $this->assertSame( $first['options'], $third['options'], 'same flags' );
    }

    /** SR-04 */
    public function testTheSessionCookie()
    {
        $params = array( 'lifetime' => 3600, 'path' => '/site/', 'domain' => '.example.org', 'secure' => true, 'httponly' => true, 'samesite' => 'Strict' );
        $cookie = eZSession::sessionCookie( 'eZSESSID', 'abc123', $params, 1000000 );
        $this->assertSame( array( 'name' => 'eZSESSID', 'value' => 'abc123',
                                  'options' => array( 'expires' => 1003600, 'path' => '/site/', 'domain' => '.example.org',
                                                      'secure' => true, 'httponly' => true, 'samesite' => 'Strict' ) ), $cookie );

        $cookie = eZSession::sessionCookie( 'eZSESSID', 'abc123', array( 'lifetime' => 0, 'path' => '', 'domain' => '',
                                                                         'secure' => false, 'httponly' => false, 'samesite' => '' ), 1000000 );
        $this->assertSame( array( 'expires' => 0, 'path' => '/', 'domain' => '', 'secure' => false, 'httponly' => false ), $cookie['options'],
                           'a session cookie: expires 0, no SameSite when none is set' );
    }

    /** SR-05 */
    public function testTheFlagsOfTheSiteCookie()
    {
        $params = array( 'lifetime' => 600, 'path' => '/exp/', 'domain' => '.example.org', 'secure' => true, 'httponly' => true, 'samesite' => 'Strict' );
        $session = eZSession::sessionCookie( 'eZSESSID', 'abc123', $params, 5000 );
        $site = eZSession::siteCookieOptions( $params, 5000 );
        $this->assertFalse( $site['httponly'], 'never HttpOnly' );
        unset( $session['options']['httponly'], $site['httponly'] );
        $this->assertSame( $session['options'], $site, 'path, domain, expires, Secure and SameSite of the session cookie' );

        $this->setStarted( false );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSecure', 'true' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSameSite', 'Strict' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieDomain', '.example.org' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookiePath', '/exp/' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieTimeout', '360' );
        $this->assertSame( array( 'expires' => 1360, 'path' => '/exp/', 'domain' => '.example.org', 'secure' => true, 'httponly' => false, 'samesite' => 'Strict' ),
                           eZSession::siteCookieOptions( null, 1000 ), 'no session yet: site.ini [Session]' );

        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSecure', 'false' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSameSite', 'None' );
        $options = eZSession::siteCookieOptions();
        $this->assertFalse( $options['secure'] );
        $this->assertFalse( $options['httponly'], 'never HttpOnly' );
        $this->assertSame( 'Lax', $options['samesite'], 'SameSite=None without Secure falls back to Lax, as for the session cookie' );
    }

    /** SR-06 */
    public function testCookieParamsChangesNothing()
    {
        $before = session_get_cookie_params();
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSameSite', 'Strict' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieTimeout', '360' );
        $params = eZSession::cookieParams();
        $this->assertSame( 'Strict', $params['samesite'] );
        $this->assertSame( 360, $params['lifetime'] );
        $this->assertSame( 77, eZSession::cookieParams( 77 )['lifetime'], 'a lifetime given wins' );
        $this->assertSame( $before, session_get_cookie_params(), 'nothing is set' );
    }

    /** SR-07 */
    public function testAResponseWithTheSessionCookieIsPrivate()
    {
        foreach ( array( 'no-cache, must-revalidate' => 'private, no-cache, must-revalidate',
                         'public, max-age=60, s-maxage=300' => 'private, max-age=60',
                         'Public, S-MaxAge=300, proxy-revalidate' => 'private, no-cache, must-revalidate',
                         'max-age=0' => 'private, max-age=0',
                         '' => 'private, no-cache, must-revalidate',
                         'private, max-age=600' => 'private, max-age=600',
                         'no-store' => 'no-store',
                         'public, no-store' => 'public, no-store' ) as $given => $expected )
        {
            $this->assertSame( $expected, eZSession::privateCacheControl( $given ), var_export( $given, true ) );
        }

        $this->assertSame( array( 'Cache-Control' => 'private, max-age=60', 'Surrogate-Control' => 'no-store' ),
                           eZSession::privateCacheHeaders( array( 'Content-Type: text/html', 'Cache-Control: no-cache',
                                                                  'cache-control: public, max-age=60', 'Surrogate-Control: max-age=3600' ) ),
                           'the last Cache-Control counts, as header() replaces it' );
        $this->assertSame( array(), eZSession::privateCacheHeaders( array( 'Cache-Control: private, no-cache' ) ), 'nothing to change' );
        $this->assertSame( array( 'Cache-Control' => 'private, no-cache, must-revalidate' ), eZSession::privateCacheHeaders( array() ),
                           'none: private' );
    }

    /** SR-08 */
    public function testTheCookieIsNotSetTwice()
    {
        $this->assertTrue( eZSession::listsCookie( array( 'Cache-Control: no-cache', 'Set-Cookie: eZSESSID=abc; path=/; HttpOnly' ), 'eZSESSID' ) );
        $this->assertTrue( eZSession::listsCookie( array( 'set-cookie:eZSESSID=abc' ), 'eZSESSID' ) );
        $this->assertFalse( eZSession::listsCookie( array( 'Set-Cookie: eZSESSIDx=abc', 'Set-Cookie: is_logged_in=true' ), 'eZSESSID' ) );
        $this->assertFalse( eZSession::listsCookie( array( 'X-Note: Set-Cookie: eZSESSID=abc' ), 'eZSESSID' ) );
    }

    /** SR-09 */
    public function testTheKernelSendsTheCookies()
    {
        $source = file_get_contents( 'kernel/private/classes/ezpkernelweb.php' );
        $this->assertStringContainsString( "setcookie( 'is_logged_in', 'true', eZSession::siteCookieOptions() );", $source );
        $this->assertStringContainsString( "setcookie( 'is_logged_in', '', array( 'expires' => 1 ) + eZSession::siteCookieOptions() );", $source );
        $this->assertStringContainsString( "|| eZSession::cookieOnEveryResponse() )", $source );
        $this->assertStringContainsString( 'eZSession::sendResponseCookie();', $source );
    }
}
