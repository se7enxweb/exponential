<?php
/**
 * Logging out ends the session and its cookie (eZUser::logoutCurrent() -> eZSession::remove()), without a web
 * server and without a database. Sessions are PHP's own (the files handler) in a private directory under var/tmp,
 * or a recording handler; no session of the site is read or written.
 *
 *  LO-01 - The cookie that removes the session cookie: the session name, no value, expired, with exactly the path,
 *          domain, Secure, HttpOnly and SameSite eZSession::start() sets from site.ini [Session]
 *  LO-02 - listsExpiredCookie() finds a removal of the cookie in the response headers, and only a removal of it
 *  LO-03 - logoutCurrent() destroys the session through its handler (not a new id with the data kept), once, with
 *          the id of the session; afterwards no session runs, the id the browser sent is dropped and the user is
 *          anonymous
 *  LO-04 - With the PHP handler the session file is gone and the id is not taken up again: a session started
 *          after the logout, in the same request, gets a new id and none of the old data
 *  LO-05 - A handler that only deletes what it stored (as ezpSessionHandlerDB does) leaves PHP running the session;
 *          remove() closes it without writing it back, so the data of the ended session is not stored again
 *  LO-06 - Nothing happens without a running session
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 13ace6c9c8), Copyright (C) Mugo Web, GPL v2
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZUserLogoutTest extends PHPUnit\Framework\TestCase
{
    const NAME = 'eZLOGOUTTEST';

    private $dir;
    private $savedIni = array();
    private $savedName;
    private $savedCookieParams;
    private $savedCookie = array();
    private $hadDB;
    private $previousDB;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        if ( headers_sent() )
        {
            $this->markTestSkipped( 'headers already sent in this process: no session can be started' );
        }
        $this->dir = 'var/tmp/phpunit-logout-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir, 0700, true );
        foreach ( array( 'session.save_path', 'session.save_handler', 'session.use_strict_mode', 'session.use_cookies' ) as $key )
            $this->savedIni[$key] = ini_get( $key );
        ini_set( 'session.save_handler', 'files' );
        ini_set( 'session.save_path', $this->dir );
        $this->savedName = session_name();
        $this->savedCookieParams = session_get_cookie_params();
        $this->savedCookie = $_COOKIE;
        session_name( self::NAME );

        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookiePath', '/logout-test/' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieDomain', '.logout-test.invalid' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSecure', 'true' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieHttponly', 'true' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSameSite', 'Strict' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieTimeout', '3600' );
        ezpINIHelper::setINISetting( 'site.ini', 'ShopSettings', 'ClearBasketOnLogout', 'disabled' );
        // The database is a null one: logoutCurrent() opens a transaction for the basket and nothing else here
        $this->hadDB = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $this->previousDB = $this->hadDB ? $GLOBALS['eZDBGlobalInstance'] : null;
        eZDB::setInstance( new eZUserLogoutTestDB() );
        $this->resetSession();
    }

    protected function tearDown(): void
    {
        if ( $this->dir === null )
            return;
        if ( session_status() === PHP_SESSION_ACTIVE )
            session_abort();
        $this->resetSession();
        if ( !headers_sent() )
            session_id( '' );
        session_name( $this->savedName );
        session_set_cookie_params( $this->savedCookieParams );
        foreach ( $this->savedIni as $key => $value )
            ini_set( $key, (string)$value );
        $_COOKIE = $this->savedCookie;
        $_SESSION = array();
        ezpINIHelper::restoreINISettings();
        $GLOBALS['eZUserGlobalInstance_'] = false;
        $this->setStatic( 'eZUser', 'userHasLoggedOut', false );
        if ( $this->hadDB )
            eZDB::setInstance( $this->previousDB );
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
        foreach ( glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    private function setStatic( $class, $name, $value )
    {
        $property = new ReflectionProperty( $class, $name );
        if ( PHP_VERSION_ID < 80100 )
            $property->setAccessible( true );
        $property->setValue( null, $value );
    }

    private function getStatic( $class, $name )
    {
        $property = new ReflectionProperty( $class, $name );
        if ( PHP_VERSION_ID < 80100 )
            $property->setAccessible( true );
        return $property->getValue();
    }

    /** eZSession as at the start of a request */
    private function resetSession()
    {
        foreach ( array( 'hasStarted' => false, 'hasSessionCookie' => null, 'handlerInstance' => null, 'namespace' => null ) as $name => $value )
            $this->setStatic( 'eZSession', $name, $value );
    }

    /**
     * A request whose browser sent the session cookie $id, with the session started the way eZSession::start()
     * does (cookie parameters from site.ini, then the handler's start), PHP's files handler or $handler.
     */
    private function startSession( $id, $handler = null, array $data = array() )
    {
        $_COOKIE[self::NAME] = $id;
        session_id( $id );
        $this->setStatic( 'eZSession', 'hasSessionCookie', true );
        $this->setStatic( 'eZSession', 'handlerInstance', $handler ?: new ezpSessionHandlerPHP( true ) );
        eZSession::setCookieParams();
        $this->assertTrue( session_start(), 'the session starts' );
        $this->setStatic( 'eZSession', 'hasStarted', true );
        foreach ( $data as $key => $value )
            $_SESSION[$key] = $value;
    }

    private function sessionFile( $id )
    {
        return $this->dir . '/sess_' . $id;
    }

    /** LO-01 */
    public function testTheCookieIsExpiredWithTheParametersOfTheStart()
    {
        $start = eZSession::cookieParams();
        $this->assertSame( '/logout-test/', $start['path'] );
        $this->assertSame( '.logout-test.invalid', $start['domain'] );
        $this->assertTrue( $start['secure'] );
        $this->assertTrue( $start['httponly'] );
        $this->assertSame( 'Strict', $start['samesite'] );

        $expected = array( 'expires' => 1, 'path' => '/logout-test/', 'domain' => '.logout-test.invalid',
                           'secure' => true, 'httponly' => true, 'samesite' => 'Strict' );

        $cookie = eZSession::expiredCookie();
        $this->assertSame( self::NAME, $cookie['name'], 'before a start: the cookie it would be' );
        $this->assertSame( '', $cookie['value'] );
        $this->assertEquals( $expected, $cookie['options'] );

        $this->startSession( 'lo01session0123456789abcdef' );
        $cookie = eZSession::expiredCookie();
        $this->assertSame( self::NAME, $cookie['name'], 'a running session: the cookie it was set with' );
        $this->assertSame( '', $cookie['value'] );
        $this->assertEquals( $expected, $cookie['options'] );

        $set = eZSession::sessionCookie( session_name(), session_id(), session_get_cookie_params(), time() )['options'];
        unset( $set['expires'] );
        $removed = $cookie['options'];
        unset( $removed['expires'] );
        $this->assertSame( $set, $removed, 'path, domain, Secure, HttpOnly and SameSite as the cookie the session sent' );
    }

    /** LO-02 */
    public function testListsExpiredCookie()
    {
        $name = self::NAME;
        $this->assertTrue( eZSession::listsExpiredCookie(
            array( "Set-Cookie: $name=deleted; expires=Thu, 01 Jan 1970 00:00:01 GMT; Max-Age=0; path=/; HttpOnly" ), $name ) );
        $this->assertTrue( eZSession::listsExpiredCookie( array( "set-cookie:$name=; Path=/; Max-Age=0" ), $name ) );
        $this->assertFalse( eZSession::listsExpiredCookie( array( "Set-Cookie: $name=abc; Max-Age=3600; path=/" ), $name ), 'a cookie set, not removed' );
        $this->assertFalse( eZSession::listsExpiredCookie( array( "Set-Cookie: other=; Max-Age=0" ), $name ), 'another cookie' );
        $this->assertFalse( eZSession::listsExpiredCookie( array( "Set-Cookie: {$name}X=; Max-Age=0" ), $name ), 'a longer name' );
        $this->assertFalse( eZSession::listsExpiredCookie( array( "X-Note: $name=; Max-Age=0" ), $name ), 'not a Set-Cookie' );
        $this->assertFalse( eZSession::listsExpiredCookie( array(), $name ) );
    }

    /** LO-03 */
    public function testLogoutDestroysTheSessionThroughItsHandler()
    {
        $handler = new eZUserLogoutTestRecordingHandler( true );
        $this->startSession( 'lo03session0123456789abcdef', $handler, array( 'kept' => 'data' ) );

        eZUser::logoutCurrent();

        $this->assertSame( array( 'lo03session0123456789abcdef' ), $handler->destroyed, 'destroyed once, with the id of the session' );
        $this->assertSame( 0, $handler->regenerated, 'not merely a new id' );
        $this->assertFalse( eZSession::hasStarted(), 'no session runs after the logout' );
        $this->assertNotSame( PHP_SESSION_ACTIVE, session_status(), 'PHP runs none either' );
        $this->assertSame( '', session_id(), 'the id is dropped' );
        $this->assertArrayNotHasKey( self::NAME, $_COOKIE, 'the id the browser sent is not used again' );
        $this->assertFalse( (bool)eZSession::userHasSessionCookie() );
        $this->assertTrue( $this->getStatic( 'eZUser', 'userHasLoggedOut' ), 'single sign-on does not sign the user in again' );
        $this->assertSame( (int)eZUser::anonymousId(), (int)eZSession::userID(), 'the user is anonymous' );
    }

    /** LO-04 */
    public function testThePHPHandlerRemovesTheSessionAndItsIdForGood()
    {
        $id = 'lo04session0123456789abcdef';
        $this->startSession( $id, null, array( 'eZUserPasswordStamp' => 'x', 'secret' => 'of the session' ) );
        session_write_close();
        $this->assertFileExists( $this->sessionFile( $id ) );
        $this->assertTrue( session_start() );
        $this->assertSame( 'of the session', $_SESSION['secret'] );

        eZUser::logoutCurrent();

        $this->assertFileDoesNotExist( $this->sessionFile( $id ), 'the session file is gone' );
        $this->assertSame( PHP_SESSION_NONE, session_status() );
        $this->assertSame( '', session_id() );
        $this->assertFalse( eZSession::hasStarted() );

        // A session started later in the same request (the logout view stores a flag in one)
        eZSession::set( 'after', 'logout' );
        $this->assertTrue( eZSession::hasStarted() );
        $this->assertNotSame( $id, session_id(), 'a new id, never the one that ended' );
        $this->assertNotSame( '', session_id() );
        $this->assertArrayNotHasKey( 'secret', $_SESSION, 'none of the old data' );
        session_write_close();
        $this->assertFileDoesNotExist( $this->sessionFile( $id ), 'the old id is not stored again' );
    }

    /** LO-05 */
    public function testASessionLeftRunningByItsHandlerIsNotWrittenBack()
    {
        $id = 'lo05session0123456789abcdef';
        $handler = new eZUserLogoutTestRecordingHandler( true );
        // PHP's files handler stores it, the recording handler is the one eZSession calls (as with the database handler,
        // whose destroy() deletes its row and leaves PHP's session running)
        $this->startSession( $id, $handler, array( 'secret' => 'before' ) );
        session_write_close();
        $this->assertTrue( session_start() );
        $_SESSION['secret'] = 'changed in the request that logs out';

        $this->assertTrue( eZSession::remove() );

        $this->assertSame( array( $id ), $handler->destroyed );
        $this->assertSame( PHP_SESSION_NONE, session_status(), 'closed' );
        $this->assertStringNotContainsString( 'changed in the request', (string)file_get_contents( $this->sessionFile( $id ) ),
            'closed without writing the ended session back' );
        $this->assertSame( array(), $_SESSION );
    }

    /** LO-06 */
    public function testNothingWithoutASession()
    {
        $handler = new eZUserLogoutTestRecordingHandler( false );
        $this->setStatic( 'eZSession', 'handlerInstance', $handler );
        $this->assertFalse( eZSession::remove(), 'no session started' );
        $this->assertSame( array(), $handler->destroyed );
    }
}

/** A session handler that records what eZSession asks of it and stores nothing (PHP keeps the session). */
class eZUserLogoutTestRecordingHandler extends ezpSessionHandler
{
    public $destroyed = array();
    public $regenerated = 0;

    public function setSaveHandler()
    {
        return true;
    }

    public function read( $sessionId )
    {
        return '';
    }

    public function write( $sessionId, $sessionData )
    {
        return true;
    }

    public function destroy( $sessionId )
    {
        $this->destroyed[] = $sessionId;
        return true;
    }

    public function regenerate( $updateBackendData = true )
    {
        ++$this->regenerated;
        return true;
    }

    public function gc( $maxLifeTime )
    {
        return true;
    }

    public function cleanup()
    {
        return true;
    }

    public function deleteByUserIDs( array $userIDArray )
    {
    }
}

/** A database that is never there: every query answers nothing, a transaction does nothing. */
class eZUserLogoutTestDB extends eZNullDB
{
    public function __construct()
    {
    }
}
