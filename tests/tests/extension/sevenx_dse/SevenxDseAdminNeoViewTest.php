<?php
/**
 * The dse/adminneo view (extension/sevenx_dse/classes/runnable/views/dse/adminneo.php) runs AdminNeo so that it ends
 * cleanly on PHP-FPM and in a persistent Exponential Velocity worker (#610). No database.
 *
 * - includeAdminNeo() catches AdminNeo\EzExit (AdminNeo's exit()) and nothing else: true when the script ended with
 *   it, false when it ran to its end, any other exception goes on.
 * - sessionIdFromCookie() takes AdminNeo's session id from its own cookie only when it has the form PHP accepts.
 * - retireWorkerAfterResponse() does nothing outside Velocity.
 * - run() ends with eZExecution::cleanExit() outside any try (under Velocity it ends the request with an exception
 *   that a catch would swallow) and asks for the worker to be retired before AdminNeo runs.
 *
 * Skips when extension/sevenx_dse is not installed; SEVENX_DSE_EXTENSION_DIR=<dir> checks a clone instead.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/sevenx_dse/SevenxDseAdminNeoViewTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SevenxDseAdminNeoViewTest extends PHPUnit\Framework\TestCase
{
    const VIEW = 'classes/runnable/views/dse/adminneo.php';
    const VIEW_CLASS = 'Exponential\View\Extension\SevenxDse\Dse\Adminneo';

    private static $root;
    private static $ext;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 4 );
        self::$ext = getenv( 'SEVENX_DSE_EXTENSION_DIR' ) ?: self::$root . '/extension/sevenx_dse';
    }

    protected function setUp(): void
    {
        if ( !is_file( self::$ext . '/' . self::VIEW ) || !is_file( self::$ext . '/adminneo/core/EzExit.php' ) )
        {
            $this->markTestSkipped( 'extension/sevenx_dse is not installed' );
        }
        if ( !class_exists( 'AdminNeo\EzExit', false ) )
        {
            require_once self::$ext . '/adminneo/core/EzExit.php';
        }
        if ( !class_exists( self::VIEW_CLASS, false ) )
        {
            if ( !class_exists( 'Exponential\Runnable\ModuleView' ) )
            {
                $this->markTestSkipped( 'the kernel runnable classes are not autoloadable' );
            }
            require_once self::$ext . '/' . self::VIEW;
        }
    }

    private static function fixture( $name )
    {
        return __DIR__ . '/fixtures/' . $name . '.php';
    }

    public function testIncludeAdminNeoCatchesEzExit()
    {
        $class = self::VIEW_CLASS;
        ob_start();
        $ended = $class::includeAdminNeo( self::fixture( 'adminneo_ends_with_ezexit' ) );
        $this->assertSame( 'page', ob_get_clean() );
        $this->assertTrue( $ended );
    }

    public function testIncludeAdminNeoReportsAScriptThatRanToItsEnd()
    {
        $class = self::VIEW_CLASS;
        ob_start();
        $ended = $class::includeAdminNeo( self::fixture( 'adminneo_runs_to_end' ) );
        $this->assertSame( 'page', ob_get_clean() );
        $this->assertFalse( $ended );
    }

    public function testIncludeAdminNeoLetsOtherExceptionsThrough()
    {
        $class = self::VIEW_CLASS;
        $this->expectException( RuntimeException::class );
        $this->expectExceptionMessage( 'other' );
        $class::includeAdminNeo( self::fixture( 'adminneo_throws_other' ) );
    }

    public function testSessionIdFromCookie()
    {
        $class = self::VIEW_CLASS;
        $id = '0730b5765ed130be9ea7e55313b3568d';
        $this->assertSame( $id, $class::sessionIdFromCookie( array( 'neo_sid' => $id ), 'neo_sid' ) );
        $this->assertSame( '', $class::sessionIdFromCookie( array(), 'neo_sid' ) );
        $this->assertSame( '', $class::sessionIdFromCookie( array( 'eZSESSID' => $id ), 'neo_sid' ) );
        $this->assertSame( '', $class::sessionIdFromCookie( array( 'neo_sid' => 'short' ), 'neo_sid' ) );
        $this->assertSame( '', $class::sessionIdFromCookie( array( 'neo_sid' => $id . "\n" ), 'neo_sid' ) );
        $this->assertSame( '', $class::sessionIdFromCookie( array( 'neo_sid' => '../../' . $id ), 'neo_sid' ) );
        $this->assertSame( '', $class::sessionIdFromCookie( array( 'neo_sid' => array( $id ) ), 'neo_sid' ) );
    }

    public function testRetireWorkerOnlyUnderVelocity()
    {
        if ( class_exists( 'Q_WebServer_Pool', false ) )
        {
            $this->markTestSkipped( 'running inside Exponential Velocity' );
        }
        $class = self::VIEW_CLASS;
        $this->assertFalse( $class::retireWorkerAfterResponse() );
    }

    public function testRunEndsWithCleanExitOutsideAnyTry()
    {
        $source = (string)file_get_contents( self::$ext . '/' . self::VIEW );
        $this->assertSame( 1, preg_match( '~public function run\(.*?\n    \}\n~s', $source, $m ), 'run() found' );
        $run = $m[0];
        $this->assertStringNotContainsString( 'try', preg_replace( '~//[^\n]*~', '', $run ), 'run() has no try block' );
        $retire = strpos( $run, 'self::retireWorkerAfterResponse()' );
        $include = strpos( $run, 'self::includeAdminNeo(' );
        // run() may end early (sevenx_dse 1.1.13 answers "reload soon" in a process that already ran AdminNeo), so
        // the request's own ending is the last cleanExit(), and every cleanExit() comes after the worker is retired.
        $firstExit = strpos( $run, '\eZExecution::cleanExit()' );
        $exit = strrpos( $run, '\eZExecution::cleanExit()' );
        $this->assertNotFalse( $retire );
        $this->assertNotFalse( $include );
        $this->assertNotFalse( $exit );
        $this->assertLessThan( $include, $retire, 'the worker is retired before AdminNeo runs' );
        $this->assertLessThan( $firstExit, $retire, 'the worker is retired before any early exit' );
        $this->assertLessThan( $exit, $include, 'the request ends with cleanExit() after AdminNeo' );
        $this->assertStringContainsString( "session_id( self::sessionIdFromCookie( \$_COOKIE, 'neo_sid' ) )", $run );
    }
}
