<?php
/**
 * expViewCountRequestFilter keeps the page views: GET with 200 or 304 by default, no robots (user agent patterns,
 * an empty agent), no files (by extension) and no module or service paths. An empty list switches its check off.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expViewCountRequestFilterTest extends PHPUnit\Framework\TestCase
{
    private static function record( $target, $status = 200, $agent = 'Mozilla/5.0 Firefox/140.0', $method = 'GET' )
    {
        return array( 'host' => '', 'port' => null, 'ip' => '192.0.2.1', 'time' => '', 'method' => $method, 'target' => $target,
                      'protocol' => 'HTTP/1.1', 'status' => $status, 'referer' => '', 'agent' => $agent );
    }

    public function testAPageViewPasses()
    {
        $filter = new expViewCountRequestFilter();
        $this->assertNull( $filter->classify( self::record( '/News/A-story?x=1' ) ) );
        $this->assertNull( $filter->classify( self::record( '/', 304 ) ) );
        $this->assertNull( $filter->classify( self::record( '/content/view/full/42' ) ) );
    }

    public function testStatusesAndMethods()
    {
        $filter = new expViewCountRequestFilter();
        $this->assertSame( 'status', $filter->classify( self::record( '/a', 404 ) ) );
        $this->assertSame( 'status', $filter->classify( self::record( '/a', 301 ) ) );
        $this->assertSame( 'method', $filter->classify( self::record( '/a', 200, 'Firefox', 'POST' ) ) );
        $this->assertSame( 'method', $filter->classify( self::record( '/a', 200, 'Firefox', 'HEAD' ) ) );
        $this->assertSame( 'malformed', $filter->classify( self::record( '', 400, 'Firefox', '' ) ) );
        $this->assertSame( 'method', $filter->classify( self::record( '*', 200, 'Firefox', 'OPTIONS' ) ) );
        $this->assertSame( 'malformed', ( new expViewCountRequestFilter( array( 'methods' => array() ) ) )->classify( self::record( '*', 200, 'Firefox', 'OPTIONS' ) ) );
    }

    public function testRobots()
    {
        $filter = new expViewCountRequestFilter();
        $this->assertSame( 'bot', $filter->classify( self::record( '/a', 200, 'Mozilla/5.0 (compatible; Googlebot/2.1)' ) ) );
        $this->assertSame( 'bot', $filter->classify( self::record( '/a', 200, 'Exponential-cache-warmer' ) ) );
        $this->assertSame( 'bot', $filter->classify( self::record( '/a', 200, 'curl/8.5.0' ) ) );
        $this->assertSame( 'bot', $filter->classify( self::record( '/a', 200, '' ) ) );
        $this->assertNull( ( new expViewCountRequestFilter( array( 'skip_empty_agent' => false ) ) )->classify( self::record( '/a', 200, '' ) ) );
    }

    public function testFilesAndModulePaths()
    {
        $filter = new expViewCountRequestFilter();
        $this->assertSame( 'asset', $filter->classify( self::record( '/design/x/stylesheets/a.CSS' ) ) );
        $this->assertSame( 'asset', $filter->classify( self::record( '/favicon.ico' ) ) );
        $this->assertSame( 'excluded', $filter->classify( self::record( '/user/login' ) ) );
        $this->assertSame( 'excluded', $filter->classify( self::record( '/user' ) ) );
        $this->assertSame( 'excluded', $filter->classify( self::record( '/var/site/storage/images/a' ) ) );
        $this->assertSame( 'excluded', $filter->classify( self::record( '/content/search?SearchText=a' ) ) );
        $this->assertTrue( $filter->isExcluded( '/USER/register' ) );
        $this->assertFalse( $filter->isExcluded( '/users-guide' ) );
        $this->assertFalse( $filter->isAsset( '/a.b/story' ) );
    }

    public function testEmptyListsSwitchTheirCheckOff()
    {
        $filter = new expViewCountRequestFilter( array( 'methods' => array(), 'statuses' => array(), 'bot_patterns' => array(),
                                                        'skip_extensions' => array(), 'skip_path_prefixes' => array(), 'skip_empty_agent' => false ) );
        $this->assertNull( $filter->classify( self::record( '/user/login.css', 500, 'Googlebot', 'POST' ) ) );
    }

    public function testTheSettingsOfLogfileIni()
    {
        // the shipped file alone, without the overrides of an installation
        $ini = new eZINI( 'logfile.ini', self::root() . '/settings', null, false, null, true );
        $settings = expViewCountRequestFilter::fromINI( $ini )->settings();
        $this->assertSame( array( 'GET' ), $settings['methods'] );
        $this->assertSame( array( 200, 304 ), $settings['statuses'] );
        $this->assertContains( 'bot', $settings['bot_patterns'] );
        $this->assertContains( '/user/', $settings['skip_path_prefixes'] );
        $this->assertTrue( $settings['skip_empty_agent'] );
        // the shipped lists are the defaults of the class
        $defaults = ( new expViewCountRequestFilter() )->settings();
        $this->assertSame( $defaults, $settings );
    }

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }
}
