<?php
/**
 * The session cookie in every response and the flags of the is_logged_in cookie, without a web server.
 *
 *  SR-01 - site.ini [Session] CookieAlwaysAddToHttpResponse is off without the setting and with disabled; enabled and
 *          true switch it on
 *  SR-02 - Off, or without a running session, responseCookie() gives nothing
 *  SR-03 - sessionCookie(): name, id and the flags of the session cookie; expires from the lifetime, 0 without one
 *  SR-04 - siteCookieOptions(): Secure and SameSite of the session cookie, not HttpOnly (scripts of cached pages read
 *          is_logged_in), the path asked for, no domain and expires 0; site.ini [Session] decides them as for the
 *          session cookie
 *  SR-05 - cookieParams() gives the parameters setCookieParams() sets and changes nothing
 *  SR-06 - The kernel sends both cookies with these options
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZSessionResponseCookieTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
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
        $this->assertNull( eZSession::responseCookie(), 'off' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieAlwaysAddToHttpResponse', 'enabled' );
        if ( !eZSession::hasStarted() )
        {
            $this->assertNull( eZSession::responseCookie(), 'no session running' );
        }
    }

    /** SR-03 */
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

    /** SR-04 */
    public function testTheFlagsOfTheSiteCookie()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSecure', 'true' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSameSite', 'Strict' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieDomain', '.example.org' );
        $this->assertSame( array( 'expires' => 0, 'path' => '/exp/', 'secure' => true, 'httponly' => false, 'samesite' => 'Strict' ),
                           eZSession::siteCookieOptions( '/exp/' ) );

        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSecure', 'false' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSameSite', 'None' );
        $options = eZSession::siteCookieOptions( '/' );
        $this->assertFalse( $options['secure'] );
        $this->assertFalse( $options['httponly'], 'never HttpOnly' );
        $this->assertSame( 'Lax', $options['samesite'], 'SameSite=None without Secure falls back to Lax, as for the session cookie' );
    }

    /** SR-05 */
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

    /** SR-06 */
    public function testTheKernelSendsTheCookies()
    {
        $source = file_get_contents( 'kernel/private/classes/ezpkernelweb.php' );
        $this->assertStringContainsString( "setcookie( 'is_logged_in', 'true', eZSession::siteCookieOptions( \$cookiePath ) );", $source );
        $this->assertStringContainsString( "setcookie( 'is_logged_in', '', array( 'expires' => 1 ) + eZSession::siteCookieOptions( \$cookiePath ) );", $source );
        $this->assertStringContainsString( '$sessionCookie = eZSession::responseCookie();', $source );
        $this->assertStringContainsString( "setcookie( \$sessionCookie['name'], \$sessionCookie['value'], \$sessionCookie['options'] );", $source );
    }
}
