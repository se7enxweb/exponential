<?php
/**
 * eZDebug's deprecation level (site.ini [DebugSettings] DeprecatedLog), on a debug instance of its own whose log
 * files are in a private directory under var/tmp (the site's var/log is never written):
 *   - a PHP E_DEPRECATED / E_USER_DEPRECATED goes to deprecated.log, not to warning.log, and is not kept for the
 *     debug report (DebugStrings), while debug output is on
 *   - it is counted every time (deprecatedCount(), and the warning count as before), written once per message
 *   - resetDeprecations() (a new request) writes it again
 *   - with DeprecatedLog=disabled it is a warning, as before
 *   - with debug output off nothing is written, unless AlwaysLog[]=deprecated
 *   - SHOW_ALL includes SHOW_DEPRECATED; the site.ini name "deprecated" maps to the level
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commits fbbd333a85, e20570a7ad, b7832d110e),
 * Copyright (C) Mugo Web, GPL v2
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZDebugDeprecatedTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugAlwaysLog', 'eZDebugLogFileEnabled',
                               'eZDebugDeprecatedLog', 'eZDebugDeprecatedCount', 'eZDebugDeprecatedSeen', 'eZDebugWarning',
                               'eZDebugWarningCount' );

    private $dir;
    private $savedGlobals = array();
    private $debug;
    private $oldReporting;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( self::GLOBAL_KEYS as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $this->dir = 'var/tmp/phpunit-ezdebug-deprecated-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 ) . '/';
        mkdir( $this->dir, 0777, true );

        unset( $GLOBALS['eZDebugAlwaysLog'], $GLOBALS['eZDebugLogFileEnabled'], $GLOBALS['eZDebugDeprecatedLog'],
               $GLOBALS['eZDebugWarning'], $GLOBALS['eZDebugWarningCount'] );
        $this->debug = new eZDebug();
        foreach ( $this->debug->LogFiles as $level => $file )
            $this->debug->LogFiles[$level] = array( $this->dir, $file[1] );
        $GLOBALS['eZDebugGlobalInstance'] = $this->debug;
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;
        eZDebug::resetDeprecations();
        $this->oldReporting = error_reporting( E_ALL );
    }

    protected function tearDown(): void
    {
        error_reporting( $this->oldReporting );
        foreach ( $this->savedGlobals as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        foreach ( glob( $this->dir . '*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    private function log( $name )
    {
        $file = $this->dir . $name;
        clearstatcache( true, $file );
        return file_exists( $file ) ? file_get_contents( $file ) : '';
    }

    /** What PHP's error handler would get for a deprecation, as eZDebug's handler receives it. */
    private function raise( $message, $errno = E_DEPRECATED )
    {
        $this->debug->errorHandler( $errno, $message, '/srv/example.php', 12 );
    }

    public function testAPhpDeprecationGoesToDeprecatedLogAndIsNotKept()
    {
        $this->raise( 'Function strftime() is deprecated' );

        $this->assertSame( array(), $this->debug->DebugStrings, 'not kept for the debug report' );
        $deprecated = $this->log( 'deprecated.log' );
        $this->assertStringContainsString( "PHP: E_DEPRECATED:\nFunction strftime() is deprecated in /srv/example.php on line 12", $deprecated );
        $this->assertSame( '', $this->log( 'warning.log' ), 'no longer a warning' );
        $this->assertSame( 1, eZDebug::deprecatedCount() );
    }

    public function testUserDeprecationsAndWriteDeprecatedUseTheSameLevel()
    {
        $this->raise( 'old API', E_USER_DEPRECATED );
        eZDebug::writeDeprecated( 'call newThing() instead', 'myExtension' );

        $deprecated = $this->log( 'deprecated.log' );
        $this->assertStringContainsString( "PHP: E_USER_DEPRECATED:\nold API", $deprecated );
        $this->assertStringContainsString( "myExtension:\ncall newThing() instead", $deprecated );
        $this->assertSame( array(), $this->debug->DebugStrings );
        $this->assertSame( 2, eZDebug::deprecatedCount() );
    }

    public function testEachMessageIsWrittenOncePerRequestButCountedEveryTime()
    {
        for ( $i = 0; $i < 50; ++$i )
            $this->raise( 'Creation of dynamic property Foo::$bar is deprecated' );
        $this->raise( 'another deprecation' );

        $deprecated = $this->log( 'deprecated.log' );
        $this->assertSame( 1, substr_count( $deprecated, 'Creation of dynamic property' ), 'written once' );
        $this->assertSame( 1, substr_count( $deprecated, 'another deprecation' ) );
        $this->assertSame( 51, eZDebug::deprecatedCount(), 'counted every time' );
        $this->assertSame( 51, $GLOBALS['eZDebugWarningCount'], 'still part of the warning count' );
        $this->assertTrue( $GLOBALS['eZDebugWarning'] );
        $this->assertLessThanOrEqual( 2, count( $GLOBALS['eZDebugDeprecatedSeen'] ), 'one small key per distinct message' );

        // A new request (the web kernel calls resetDeprecations() when it starts) writes it again
        eZDebug::resetDeprecations();
        $this->assertSame( 0, eZDebug::deprecatedCount() );
        $this->raise( 'Creation of dynamic property Foo::$bar is deprecated' );
        $this->assertSame( 2, substr_count( $this->log( 'deprecated.log' ), 'Creation of dynamic property' ) );
        $this->assertSame( 1, eZDebug::deprecatedCount() );
    }

    public function testResetForgetsTheDeprecationsToo()
    {
        $this->raise( 'gone after reset' );
        $this->debug->reset();
        $this->assertSame( 0, eZDebug::deprecatedCount() );
        $this->assertArrayNotHasKey( 'eZDebugDeprecatedSeen', $GLOBALS );
    }

    public function testDeprecatedLogDisabledKeepsThemWarnings()
    {
        eZDebug::updateSettings( array( 'debug-enabled' => true, 'deprecated-log' => 'disabled' ) );
        $this->assertFalse( eZDebug::isDeprecatedLogEnabled() );
        $this->raise( 'as before' );
        $this->raise( 'as before' );

        $this->assertSame( '', $this->log( 'deprecated.log' ) );
        $this->assertSame( 2, substr_count( $this->log( 'warning.log' ), 'as before' ), 'every one, as before' );
        $this->assertCount( 2, $this->debug->DebugStrings );
        $this->assertSame( eZDebug::LEVEL_WARNING, $this->debug->DebugStrings[0]['Level'] );
        $this->assertSame( 0, eZDebug::deprecatedCount() );

        eZDebug::updateSettings( array( 'debug-enabled' => true, 'deprecated-log' => 'enabled' ) );
        $this->assertTrue( eZDebug::isDeprecatedLogEnabled() );
    }

    public function testDebugOffWritesNothingUnlessAlwaysLogged()
    {
        $GLOBALS['eZDebugEnabled'] = false;
        $this->raise( 'quiet' );
        $this->assertSame( '', $this->log( 'deprecated.log' ) );
        $this->assertSame( 0, eZDebug::deprecatedCount() );

        $GLOBALS['eZDebugAlwaysLog'] = array( eZDebug::LEVEL_DEPRECATED => true );
        $this->raise( 'logged with AlwaysLog[]=deprecated' );
        $this->assertStringContainsString( 'logged with AlwaysLog[]=deprecated', $this->log( 'deprecated.log' ) );
        $this->assertSame( array(), $this->debug->DebugStrings );
        // An ordinary warning is still not handled while debug output is off
        $this->debug->errorHandler( E_WARNING, 'a warning', '/srv/example.php', 3 );
        $this->assertSame( '', $this->log( 'warning.log' ) );
    }

    public function testLevelsAndSettingNames()
    {
        $this->assertSame( eZDebug::SHOW_DEPRECATED, eZDebug::SHOW_ALL & eZDebug::SHOW_DEPRECATED );
        $this->assertSame( 64, eZDebug::SHOW_DEPRECATED );
        $this->assertSame( 'Deprecated', $this->debug->messageName( eZDebug::LEVEL_DEPRECATED ) );
        $this->assertContains( eZDebug::LEVEL_DEPRECATED, $this->debug->messageTypes() );
        $files = $this->debug->logFiles();
        $this->assertSame( 'deprecated.log', $files[eZDebug::LEVEL_DEPRECATED][1] );

        require_once 'kernel/private/classes/debug_settings_functions.php';
        $levels = eZDebugAlwaysLogLevels( array( 'error', 'deprecated' ) );
        $this->assertTrue( $levels[eZDebug::LEVEL_DEPRECATED] );
        $this->assertFalse( $levels[eZDebug::LEVEL_WARNING] );
        $this->assertSame( 'enabled', eZDebugDeprecatedLogSetting( eZINI::instance() ), 'the shipped default' );
    }

    public function testHiddenTypeIsNotWritten()
    {
        eZDebug::showTypes( eZDebug::SHOW_ALL & ~eZDebug::SHOW_DEPRECATED );
        eZDebug::writeDeprecated( 'not shown', 'x' );
        $this->assertSame( '', $this->log( 'deprecated.log' ) );
        $this->assertSame( 0, eZDebug::deprecatedCount() );
    }

    public function testTheDebugBarCountsThemWithoutListingThem()
    {
        $this->raise( 'counted in the bar' );
        $this->raise( 'counted in the bar' );
        $before = eZDebug::deprecatedCount();

        // Whatever handler an earlier test left installed (an eZDebug one counts into this instance) stays out:
        // render() runs under a handler of the test's own, which collects what render() itself raises
        $raised = array();
        set_error_handler( function ( $errno, $errstr, $errfile, $errline ) use ( &$raised )
        {
            $raised[] = "$errstr in $errfile on line $errline";
            return true;
        } );
        try
        {
            $html = expDebugBarReport::render( $this->debug, true, false, false, false, false );
        }
        finally
        {
            restore_error_handler();
        }

        $this->assertSame( array(), $raised, 'render() raises nothing' );
        $this->assertSame( $before, eZDebug::deprecatedCount() );
        $this->assertMatchesRegularExpression( '/data-key="deprecations"[^>]*>' . $before . ' depr</', $html );
        $this->assertStringNotContainsString( 'counted in the bar', $html, 'no row for it' );
    }
}
