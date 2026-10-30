<?php
/**
 * "Remember me" lasts the whole sign-in, without a web server or a database.
 *
 *  RM-01 - rememberCookieLifetime() keeps the lifetime in the session; 0 forgets it; without a session, 0 starts none
 *  RM-02 - The cookie sent with every response (CookieAlwaysAddToHttpResponse) and the is_logged_in cookie last as
 *          long as Remember me says, not as long as the session started with
 *  RM-03 - Several requests in one process (a persistent worker): a lifetime belongs to its session only
 *  RM-04 - otherCookieHeaders(): the Set-Cookie lines of the other cookies, so the session cookie is replaced, not
 *          sent twice
 *  RM-05 - A new session id (regenerate(), also on a later request: the second step of 2FA) goes out with the
 *          remembered lifetime; the login view remembers it before the login handlers run
 *  RM-06 - The defaults: RememberMeTimeout and SessionTimeout 360 days; the box is checked on every login form
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZSessionRememberMeTest extends PHPUnit\Framework\TestCase
{
    /** @var bool */
    private $hasStarted;

    /** @var array|null */
    private $session;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->hasStarted = eZSession::hasStarted();
        $this->session = isset( $_SESSION ) ? $_SESSION : null;
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        $this->setStarted( $this->hasStarted );
        if ( $this->session === null )
        {
            unset( $_SESSION );
        }
        else
        {
            $_SESSION = $this->session;
        }
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

    /** A request in which eZSession has started the session $id with the data $data, or none ($id null). */
    private function request( $id, array $data = array() )
    {
        if ( headers_sent() )
        {
            $this->markTestSkipped( 'headers already sent in this process: session_id() cannot be set' );
        }
        session_id( $id === null ? '' : $id );
        $_SESSION = $data;
        $this->setStarted( $id !== null );
    }

    /** RM-01 */
    public function testTheLifetimeIsKeptInTheSession()
    {
        $this->request( null );
        $this->assertFalse( eZSession::rememberCookieLifetime( 0 ), 'nothing to forget' );
        $this->assertFalse( eZSession::hasStarted(), 'forgetting starts no session' );
        $this->assertSame( 0, eZSession::rememberedCookieLifetime(), 'no session: none' );

        $this->request( 'rm01session0123456789abcd' );
        $this->assertSame( 0, eZSession::rememberedCookieLifetime(), 'a session without Remember me' );
        eZSession::rememberCookieLifetime( 31104000 );
        $this->assertSame( 31104000, $_SESSION[eZSession::COOKIE_LIFETIME_KEY], 'kept in the session' );
        $this->assertSame( 31104000, eZSession::rememberedCookieLifetime() );

        $this->assertFalse( eZSession::rememberCookieLifetime( 0 ), 'the box left empty' );
        $this->assertArrayNotHasKey( eZSession::COOKIE_LIFETIME_KEY, $_SESSION, 'forgotten' );
        $this->assertSame( 0, eZSession::rememberedCookieLifetime() );

        $_SESSION[eZSession::COOKIE_LIFETIME_KEY] = 'junk';
        $this->assertSame( 0, eZSession::rememberedCookieLifetime(), 'a value that is no lifetime is none' );
    }

    /** RM-02 */
    public function testTheCookiesLastAsLongAsRememberMeSays()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieAlwaysAddToHttpResponse', 'enabled' );
        $this->request( 'rm02session0123456789abcd', array( eZSession::COOKIE_LIFETIME_KEY => 31104000 ) );

        $before = time();
        $cookie = eZSession::responseCookie();
        $this->assertSame( 'rm02session0123456789abcd', $cookie['value'] );
        $this->assertGreaterThanOrEqual( $before + 31104000, $cookie['options']['expires'], 'the session cookie: 360 days' );
        $this->assertLessThanOrEqual( time() + 31104000, $cookie['options']['expires'] );

        $site = eZSession::siteCookieOptions();
        $this->assertGreaterThanOrEqual( $before + 31104000, $site['expires'], 'is_logged_in: as long as the session cookie' );

        $this->request( 'rm02other0123456789abcdef' );
        $lifetime = session_get_cookie_params()['lifetime'];
        $cookie = eZSession::responseCookie();
        $this->assertSame( $lifetime > 0 ? time() + $lifetime : 0, $cookie['options']['expires'],
                           'without Remember me: the lifetime the session started with' );
    }

    /** RM-03 */
    public function testALifetimeBelongsToItsSessionOnly()
    {
        $this->request( 'useronesession0123456789', array( eZSession::COOKIE_LIFETIME_KEY => 31104000 ) );
        $this->assertSame( 31104000, eZSession::rememberedCookieLifetime(), 'request 1: user one remembered' );
        eZSession::stop();
        $this->assertSame( 0, eZSession::rememberedCookieLifetime(), 'after the end of request 1: none' );

        $this->request( 'usertwosession0123456789', array( 'eZUserLoggedInID' => 14 ) );
        $this->assertSame( 0, eZSession::rememberedCookieLifetime(), 'request 2: user two, who did not ask for it' );
    }

    /** RM-04 */
    public function testTheOtherCookiesOfTheResponseStay()
    {
        $headers = array( 'Content-Type: text/html', 'Set-Cookie: is_logged_in=true; path=/',
                          'Set-Cookie: eZSESSID=old; path=/; HttpOnly', 'set-cookie:eZSESSIDx=keep', 'Set-Cookie: eZSESSID=older' );
        $this->assertSame( array( 'Set-Cookie: is_logged_in=true; path=/', 'Set-Cookie: eZSESSIDx=keep' ),
                           eZSession::otherCookieHeaders( $headers, 'eZSESSID' ) );
        $this->assertSame( array(), eZSession::otherCookieHeaders( array( 'Set-Cookie: eZSESSID=abc' ), 'eZSESSID' ) );
        $this->assertSame( array(), eZSession::otherCookieHeaders( array( 'X-Note: Set-Cookie: a=b' ), 'eZSESSID' ) );
    }

    /** RM-05 */
    public function testANewSessionIdKeepsTheLifetime()
    {
        $method = new ReflectionMethod( 'eZSession', 'regenerate' );
        $lines = file( $method->getFileName() );
        $source = implode( '', array_slice( $lines, $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1 ) );
        $this->assertMatchesRegularExpression( '/->regenerate\(.*\);.*rememberedCookieLifetime\(\).*self::sendCookie\( \$lifetime \)/s', $source,
                                               'after the handler gave the session a new id, the cookie goes out again with the remembered lifetime' );

        $login = file_get_contents( 'kernel/private/classes/views/user/login.php' );
        $remember = strpos( $login, "\\eZSession::rememberCookieLifetime( \$http->hasPostVariable( 'Cookie' ) ? \$rememberMeTimeout : 0 );" );
        $this->assertNotFalse( $remember, 'the login view remembers the lifetime when the box is checked, and forgets it when not' );
        $this->assertLessThan( strpos( $login, '->loginUser( $userLogin, $userPassword )' ), $remember, 'before the login handlers run' );
    }

    /** RM-06 */
    public function testTheDefaults()
    {
        $this->assertSame( 1, preg_match( '/^\[Session\]\R(.*?)^\[/ms', file_get_contents( 'settings/site.ini' ), $m ), 'site.ini [Session]' );
        $this->assertMatchesRegularExpression( '/^RememberMeTimeout=31104000$/m', $m[1], 'Remember me: 360 days' );
        $this->assertMatchesRegularExpression( '/^SessionTimeout=31104000$/m', $m[1], 'the server keeps an idle session as long' );

        foreach ( array( 'design/standard/templates/user/login.tpl', 'design/admin/templates/user/login.tpl',
                         'design/admin4/templates/user/login.tpl' ) as $template )
        {
            $this->assertMatchesRegularExpression( '/<input type="checkbox"[^>]*name="Cookie"[^>]*checked="checked"/', file_get_contents( $template ),
                                                   $template . ': Remember me is checked by default' );
        }
    }
}
