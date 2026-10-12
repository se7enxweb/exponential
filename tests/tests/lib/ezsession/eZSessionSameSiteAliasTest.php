<?php
/**
 * site.ini [Session] SameSite, the older name of CookieSameSite, read as its alias, without a web server.
 *
 *  SA-01 - Neither set: Lax
 *  SA-02 - Only SameSite set: its value is used (Strict, Lax, empty for none) and checked like CookieSameSite
 *          (an unknown value gives Lax, None without Secure gives Lax, None with Secure stays None)
 *  SA-03 - Both set: CookieSameSite wins and nothing is noted
 *  SA-04 - The alias is noted at the deprecated level once per request, again in the next request of a persistent
 *          worker
 *
 * The debug output goes to a debug instance of its own whose log files are in a private directory under var/tmp.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit e56cb305f0), Copyright (C) Mugo Web, GPL v2
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezsession
 */

class eZSessionSameSiteAliasTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugAlwaysLog', 'eZDebugLogFileEnabled',
                               'eZDebugDeprecatedCount', 'eZDebugDeprecatedSeen', 'eZDebugWarning', 'eZDebugWarningCount' );

    const NOTE = 'site.ini [Session] SameSite is deprecated';

    private $dir;
    private $savedGlobals = array();
    private $savedServer = array();
    private $debug;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( self::GLOBAL_KEYS as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        foreach ( array( 'REQUEST_TIME_FLOAT', 'REQUEST_TIME' ) as $key )
            $this->savedServer[$key] = array_key_exists( $key, $_SERVER ) ? array( $_SERVER[$key] ) : null;

        $this->dir = 'var/tmp/phpunit-samesite-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 ) . '/';
        mkdir( $this->dir, 0777, true );
        unset( $GLOBALS['eZDebugAlwaysLog'], $GLOBALS['eZDebugLogFileEnabled'], $GLOBALS['eZDebugDeprecatedCount'], $GLOBALS['eZDebugDeprecatedSeen'] );
        $this->debug = new eZDebug();
        foreach ( $this->debug->LogFiles as $level => $file )
            $this->debug->LogFiles[$level] = array( $this->dir, $file[1] );
        $GLOBALS['eZDebugGlobalInstance'] = $this->debug;
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;

        $this->setNoted( null );
        $this->request( '1000.0001' );
        // site.ini of a test run sets neither name; make sure
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSameSite', null );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'SameSite', null );
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        $this->setNoted( null );
        foreach ( $this->savedGlobals as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        foreach ( $this->savedServer as $key => $saved )
        {
            if ( $saved === null )
                unset( $_SERVER[$key] );
            else
                $_SERVER[$key] = $saved[0];
        }
        foreach ( glob( $this->dir . '*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    private function setNoted( $value )
    {
        $property = new ReflectionProperty( 'eZSession', 'sameSiteAliasNoted' );
        if ( PHP_VERSION_ID < 80100 )
            $property->setAccessible( true );
        $property->setValue( null, $value );
    }

    /** A new request, as a persistent worker starts one: its own REQUEST_TIME_FLOAT. */
    private function request( $time )
    {
        $_SERVER['REQUEST_TIME_FLOAT'] = (float)$time;
        $_SERVER['REQUEST_TIME'] = (int)$time;
    }

    /**
     * How often the deprecation was noted: as a stored warning (DeprecatedLog=disabled), or counted as a deprecation
     * (eZDebug keeps no message of that level for the report and counts every one).
     */
    private function noted()
    {
        $count = 0;
        foreach ( $this->debug->DebugStrings as $message )
            $count += substr_count( (string)$message['String'], self::NOTE );
        $count += eZDebug::deprecatedCount();
        return $count;
    }

    private function sameSite( $secure = 'false' )
    {
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSecure', $secure );
        $params = eZSession::cookieParams();
        return $params['samesite'];
    }

    /** SA-01 */
    public function testNeitherSetGivesLax()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSecure', 'false' );
        $this->assertSame( 'Lax', $this->sameSite() );
        $this->assertSame( 0, $this->noted(), 'nothing to note' );
    }

    /** SA-02 */
    public function testOnlySameSiteIsReadAndChecked()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSecure', 'false' );
        foreach ( array( 'Strict' => 'Strict', 'strict' => 'Strict', 'Lax' => 'Lax', '' => '', 'bogus' => 'Lax', 'None' => 'Lax' ) as $value => $expected )
        {
            ezpINIHelper::setINISetting( 'site.ini', 'Session', 'SameSite', (string)$value );
            $this->assertSame( $expected, $this->sameSite(), 'SameSite=' . $value . ' without Secure' );
        }
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'SameSite', 'None' );
        $this->assertSame( 'None', $this->sameSite( 'true' ), 'SameSite=None with Secure' );
    }

    /** SA-03 */
    public function testCookieSameSiteWins()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSecure', 'false' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'SameSite', 'Lax' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSameSite', 'Strict' );
        $this->assertSame( 'Strict', $this->sameSite() );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSameSite', '' );
        $this->assertSame( '', $this->sameSite(), 'an empty CookieSameSite (no attribute) also wins' );
        $this->assertSame( 0, $this->noted(), 'the alias is not read, so not noted' );
    }

    /** SA-04 */
    public function testTheAliasIsNotedOncePerRequest()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'CookieSecure', 'false' );
        ezpINIHelper::setINISetting( 'site.ini', 'Session', 'SameSite', 'Strict' );
        $this->sameSite();
        $this->sameSite();
        eZSession::siteCookieOptions();
        $this->assertSame( 1, $this->noted(), 'once in the first request, however often the cookie parameters are read' );

        $this->request( '1000.0002' );
        $this->sameSite();
        $this->sameSite();
        $this->assertSame( 2, $this->noted(), 'once more in the next request of the same worker' );
    }
}
