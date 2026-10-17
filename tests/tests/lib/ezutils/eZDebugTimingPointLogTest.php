<?php
/**
 * The timing point eZDebug writes before the first message of a request as context goes to debug.log only, on a
 * debug instance of its own whose log files are in a private directory under var/tmp:
 *   - an error, warning or notice after a timing point is written alone (no "Timing Point:" entry before it,
 *     which counted as an entry of its own in error.log, warning.log and notice.log)
 *   - a debug message after a timing point still has it before it in debug.log
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZDebugTimingPointLogTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugAlwaysLog', 'eZDebugLogFileEnabled',
                               'eZDebugShowTypes' );

    private $dir;
    private $savedGlobals = array();
    private $debug;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( self::GLOBAL_KEYS as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $this->dir = 'var/tmp/phpunit-ezdebug-timingpoint-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 ) . '/';
        mkdir( $this->dir, 0777, true );
        unset( $GLOBALS['eZDebugAlwaysLog'], $GLOBALS['eZDebugLogFileEnabled'] );
        $this->debug = new eZDebug();
        foreach ( $this->debug->LogFiles as $level => $file )
            $this->debug->LogFiles[$level] = array( $this->dir, $file[1] );
        $GLOBALS['eZDebugGlobalInstance'] = $this->debug;
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;
        eZDebug::setLogFileEnabled( true, eZDebug::LEVEL_NOTICE );
        eZDebug::setLogFileEnabled( true, eZDebug::LEVEL_WARNING );
        eZDebug::setLogFileEnabled( true, eZDebug::LEVEL_ERROR );
        eZDebug::setLogFileEnabled( true, eZDebug::LEVEL_DEBUG );
        $this->debug->ShowTypes = eZDebug::SHOW_ALL;
    }

    protected function tearDown(): void
    {
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

    public function testTheTimingPointIsWrittenToDebugLogOnly()
    {
        eZDebug::addTimingPoint( "Module start 'setup'" );
        eZDebug::writeError( 'the error', 'test' );
        eZDebug::writeWarning( 'the warning', 'test' );
        eZDebug::writeNotice( 'the notice', 'test' );
        eZDebug::writeDebug( 'the debug message', 'test' );

        foreach ( array( 'error.log' => 'the error', 'warning.log' => 'the warning', 'notice.log' => 'the notice' ) as $file => $text )
        {
            $this->assertStringContainsString( $text, $this->log( $file ) );
            $this->assertStringNotContainsString( 'Timing Point', $this->log( $file ), $file );
        }
        $this->assertStringContainsString( "Timing Point: Module start 'setup'", $this->log( 'debug.log' ) );
        $this->assertStringContainsString( 'the debug message', $this->log( 'debug.log' ) );
    }
}
