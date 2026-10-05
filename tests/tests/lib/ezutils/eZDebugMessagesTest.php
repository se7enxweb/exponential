<?php
/**
 * eZDebug (lib/ezutils) messages, log files and timing, on a debug instance of its own whose log files are in a
 * private directory under var/tmp (the site's var/log is never written):
 *   - writeNotice/Warning/Error/Debug/Strict: stored with level, label and text while debug is enabled, nothing
 *     stored while it is disabled, errors logged to file even then (always log), the "label:" line in the file
 *   - log files per level switched off, the global switch, log-only mode
 *   - log rotation: the file over the maximum size is renamed .1, older ones move on, the oldest is dropped
 *   - repeated entries while a log context is set are counted, not written again, and the count is written when
 *     something else is logged
 *   - timing points, accumulators and accumulator groups, the text report, top and bottom reports, dumpVariable()
 *   - isIPInNet() for IPv4 networks, updateSettings() with debug by IP from the command line, messageName(),
 *     showMessage(), maxLogSize()/maxLogrotateFiles() and their settings
 *
 * The global debug instance and the debug globals are restored in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZDebugMessagesTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugMaxLogSize', 'eZDebugMaxLogrotateFiles',
                               'eZDebugAllowed', 'eZDebugAllowedByIP', 'eZDebugAlwaysLog', 'eZDebugIPMatch', 'eZDebugLogFileEnabled' );

    private $dir;
    private $savedGlobals = array();
    private $debug;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( self::GLOBAL_KEYS as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $this->dir = 'var/tmp/phpunit-ezdebug-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 ) . '/';
        mkdir( $this->dir, 0777, true );

        unset( $GLOBALS['eZDebugAlwaysLog'], $GLOBALS['eZDebugLogFileEnabled'] );
        $this->debug = new eZDebug();
        foreach ( $this->debug->LogFiles as $level => $file )
            $this->debug->LogFiles[$level] = array( $this->dir, $file[1] );
        $GLOBALS['eZDebugGlobalInstance'] = $this->debug;
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;
    }

    protected function tearDown(): void
    {
        eZDebug::setLogContext( '' );
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
        return file_exists( $file ) ? file_get_contents( $file ) : '';
    }

    public function testMessagesAreStoredWithTheirLevel()
    {
        eZDebug::writeNotice( 'a notice', 'labelN' );
        eZDebug::writeWarning( 'a warning', 'labelW' );
        eZDebug::writeError( 'an error', 'labelE' );
        eZDebug::writeDebug( 'a debug', 'labelD' );
        eZDebug::writeStrict( 'a strict', 'labelS' );
        $levels = array_map( function ( $m ) { return array( $m['Level'], $m['Label'], $m['String'] ); }, $this->debug->DebugStrings );
        $this->assertSame( array(
            array( eZDebug::LEVEL_NOTICE, 'labelN', 'a notice' ),
            array( eZDebug::LEVEL_WARNING, 'labelW', 'a warning' ),
            array( eZDebug::LEVEL_ERROR, 'labelE', 'an error' ),
            array( eZDebug::LEVEL_DEBUG, 'labelD', 'a debug' ),
            array( eZDebug::LEVEL_STRICT, 'labelS', 'a strict' ),
        ), $levels );
        $this->assertStringContainsString( "labelE:\nan error", $this->log( 'error.log' ) );
        $this->assertStringContainsString( "labelW:\na warning", $this->log( 'warning.log' ) );
    }

    public function testNonStringMessagesAreDumped()
    {
        eZDebug::writeNotice( array( 'k' => 'v' ), 'arr' );
        $this->assertStringContainsString( 'k', (string)$this->debug->DebugStrings[0]['String'] );
    }

    public function testDisabledDebugStoresNothingButStillLogsErrors()
    {
        $GLOBALS['eZDebugEnabled'] = false;
        eZDebug::writeNotice( 'hidden notice', 'n' );
        eZDebug::writeError( 'logged error', 'e' );
        $this->assertSame( array(), $this->debug->DebugStrings );
        $this->assertSame( '', $this->log( 'notice.log' ) );
        $this->assertStringContainsString( 'logged error', $this->log( 'error.log' ), 'errors are always logged' );
    }

    public function testLogFilesCanBeSwitchedOff()
    {
        eZDebug::setLogFileEnabled( false, eZDebug::LEVEL_WARNING );
        eZDebug::writeWarning( 'not in a file', 'w' );
        eZDebug::writeNotice( 'in a file', 'n' );
        $this->assertSame( '', $this->log( 'warning.log' ) );
        $this->assertStringContainsString( 'in a file', $this->log( 'notice.log' ) );
        $this->assertFalse( $this->debug->isLogFileEnabled( eZDebug::LEVEL_WARNING ) );

        $this->debug->setIsGlobalLogFileEnabled( false );
        $this->assertFalse( $this->debug->isLogFileEnabled( eZDebug::LEVEL_NOTICE ) );
        eZDebug::writeNotice( 'nowhere', 'n' );
        $this->assertStringNotContainsString( 'nowhere', $this->log( 'notice.log' ) );
        $this->assertCount( 3, $this->debug->DebugStrings, 'still stored for the report' );
    }

    public function testLogOnlyKeepsMessagesOutOfTheReport()
    {
        $this->debug->setLogOnly( true );
        $this->assertTrue( eZDebug::isLogOnlyEnabled() );
        eZDebug::writeNotice( 'only in the file', 'n' );
        $this->assertSame( array(), $this->debug->DebugStrings );
        $this->assertStringContainsString( 'only in the file', $this->log( 'notice.log' ) );
    }

    public function testRotation()
    {
        eZDebug::setMaxLogSize( 100 );
        eZDebug::setLogrotateFiles( 2 );
        $this->assertSame( 100, eZDebug::maxLogSize() );
        $this->assertSame( 2, eZDebug::maxLogrotateFiles() );
        for ( $i = 1; $i <= 4; ++$i )
            eZDebug::writeNotice( str_repeat( (string)$i, 150 ), 'r' );
        $this->assertStringContainsString( '4444', $this->log( 'notice.log' ) );
        $this->assertStringContainsString( '3333', $this->log( 'notice.log.1' ) );
        $this->assertStringContainsString( '2222', $this->log( 'notice.log.2' ) );
        $this->assertFileDoesNotExist( $this->dir . 'notice.log.3', 'no more than two rotated files' );
        $this->assertFalse( eZDebug::rotateLog( $this->dir . 'missing.log' ) );
    }

    public function testRotationIsOffWithZeroFiles()
    {
        eZDebug::setLogrotateFiles( 0 );
        file_put_contents( $this->dir . 'plain.log', 'x' );
        $this->assertNull( eZDebug::rotateLog( $this->dir . 'plain.log' ) );
        $this->assertFileExists( $this->dir . 'plain.log' );
    }

    public function testRepeatsAreCountedDuringALogContext()
    {
        eZDebug::setLogContext( "setup\nstep" );
        $this->assertSame( 'setup step', eZDebug::logContext() );
        for ( $i = 0; $i < 3; ++$i )
            eZDebug::writeNotice( 'same thing', 'rep' );
        eZDebug::writeNotice( 'something else', 'rep' );
        $log = $this->log( 'notice.log' );
        $this->assertSame( 1, substr_count( $log, 'same thing' ) );
        $this->assertStringContainsString( 'The entry above was written 2 more times', $log );
        $this->assertStringContainsString( '(setup step)', $log );
    }

    public function testTimingAndAccumulators()
    {
        eZDebug::addTimingPoint( 'start' );
        eZDebug::createAccumulatorGroup( 'x3group', 'X3 group' );
        eZDebug::accumulatorStart( 'x3acc', 'x3group', 'X3 accumulator' );
        usleep( 0 );
        eZDebug::accumulatorStop( 'x3acc' );
        eZDebug::accumulatorStart( 'x3acc' );
        eZDebug::accumulatorStop( 'x3acc' );
        eZDebug::addTimingPoint( 'end' );

        $this->assertCount( 2, $this->debug->TimePoints );
        $this->assertSame( 'start', $this->debug->TimePoints[0]['Description'] );
        $accumulator = $this->debug->TimeAccumulatorList['x3acc'];
        $this->assertSame( 2, $accumulator['count'] );
        $this->assertSame( 'x3group', $accumulator['in_group'] );
        $this->assertGreaterThanOrEqual( 0, $accumulator['time'] );
        $this->assertArrayHasKey( 'x3group', $this->debug->TimeAccumulatorGroupList );
    }

    public function testTextReport()
    {
        eZDebug::writeWarning( 'warning for the report', 'report-label' );
        eZDebug::addTimingPoint( 'report point' );
        eZDebug::appendTopReport( 'Top part', 'top content' );
        eZDebug::appendBottomReport( 'Bottom part', 'bottom content' );
        // $returnReport returns the report and prints nothing: the command line scripts write it to STDERR
        ob_start();
        $report = eZDebug::printReport( false, false, true );
        $this->assertSame( '', ob_get_clean(), 'nothing printed when the report is returned' );
        $this->assertIsString( $report );
        $this->assertStringContainsString( 'warning for the report', $report );
        $this->assertStringContainsString( 'report-label', $report );
        $this->assertStringContainsString( 'top content', $report );

        // without $returnReport the report is printed and nothing returned
        ob_start();
        $returned = eZDebug::printReport( false, false, false );
        $printed = ob_get_clean();
        $this->assertNull( $returned );
        $this->assertStringContainsString( 'warning for the report', $printed );

        $GLOBALS['eZDebugEnabled'] = false;
        ob_start();
        $this->assertNull( eZDebug::printReport( false, false, true ), 'no report with debug off' );
        $this->assertSame( '', ob_get_clean() );
    }

    public function testDumpVariable()
    {
        $this->assertStringContainsString( 'value', eZDebug::dumpVariable( array( 'key' => 'value' ) ) );
    }

    public static function networkProvider()
    {
        return array(
            'inside /24'            => array( '192.168.1.77', '192.168.1.0', 24, true ),
            'outside /24'           => array( '192.168.2.77', '192.168.1.0', 24, false ),
            'inside /16'            => array( '10.20.99.1', '10.20.0.0', 16, true ),
            'exact /32'             => array( '203.0.113.7', '203.0.113.7', 32, true ),
            'other /32'             => array( '203.0.113.8', '203.0.113.7', 32, false ),
            'everything /0'         => array( '8.8.8.8', '0.0.0.0', 0, true ),
            'odd mask /25'          => array( '192.168.1.200', '192.168.1.128', 25, true ),
            'odd mask outside /25'  => array( '192.168.1.100', '192.168.1.128', 25, false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('networkProvider')]
    public function testIsIPInNet( $ip, $network, $mask, $expected )
    {
        $this->assertSame( $expected, eZDebug::isIPInNet( $ip, $network, $mask ) );
    }

    public function testDebugByIpFromTheCommandLine()
    {
        eZDebug::updateSettings( array( 'debug-enabled' => true, 'debug-by-ip' => true, 'debug-ip-list' => array( 'commandline' ) ) );
        $this->assertTrue( eZDebug::isDebugEnabled() );
        eZDebug::updateSettings( array( 'debug-enabled' => true, 'debug-by-ip' => true, 'debug-ip-list' => array( '203.0.113.7' ) ) );
        $this->assertFalse( eZDebug::isDebugEnabled(), 'the command line has no address in the list' );
        eZDebug::updateSettings( array( 'debug-enabled' => false ) );
        $this->assertFalse( eZDebug::isDebugEnabled() );
        eZDebug::updateSettings( array( 'debug-enabled' => true, 'log-only' => 'enabled' ) );
        $this->assertTrue( eZDebug::isLogOnlyEnabled() );
    }

    public function testNamesAndTypes()
    {
        $this->assertSame( 'Warning', $this->debug->messageName( eZDebug::LEVEL_WARNING ) );
        $this->assertSame( 'TimingPoint', $this->debug->messageName( eZDebug::LEVEL_TIMING_POINT ) );
        $this->assertCount( 6, $this->debug->messageTypes() );
        $this->assertSame( eZDebug::SHOW_ALL, eZDebug::showTypes() );
        $this->assertNotEquals( 0, eZDebug::showMessage( eZDebug::SHOW_ERROR ) );
        eZDebug::showTypes( eZDebug::SHOW_ERROR );
        $this->assertSame( 0, eZDebug::showMessage( eZDebug::SHOW_NOTICE ) );
        $this->assertTrue( eZDebug::alwaysLogMessage( eZDebug::LEVEL_ERROR ) );
        $this->assertFalse( eZDebug::alwaysLogMessage( eZDebug::LEVEL_NOTICE ) );
        $this->assertFalse( eZDebug::alwaysLogMessage( 99 ) );
    }

    public function testDefaultSizes()
    {
        unset( $GLOBALS['eZDebugMaxLogSize'], $GLOBALS['eZDebugMaxLogrotateFiles'] );
        if ( !defined( 'EZPUBLISH_LOG_MAX_FILE_SIZE' ) )
            $this->assertSame( eZDebug::MAX_LOGFILE_SIZE, eZDebug::maxLogSize() );
        if ( !defined( 'EZPUBLISH_LOG_ROTATE_FILES' ) )
            $this->assertSame( eZDebug::MAX_LOGROTATE_FILES, eZDebug::maxLogrotateFiles() );
    }

    /**
     * A config.php that sets the log size but not the number of rotated files gets the default number. The check
     * looked for the size constant and then read the other one, an undefined constant error.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testSizeConstantAloneKeepsTheDefaultRotation()
    {
        if ( defined( 'EZPUBLISH_LOG_ROTATE_FILES' ) )
            $this->markTestSkipped( 'EZPUBLISH_LOG_ROTATE_FILES is defined' );
        if ( !defined( 'EZPUBLISH_LOG_MAX_FILE_SIZE' ) )
            define( 'EZPUBLISH_LOG_MAX_FILE_SIZE', 4096 );
        unset( $GLOBALS['eZDebugMaxLogSize'], $GLOBALS['eZDebugMaxLogrotateFiles'] );
        $this->assertSame( EZPUBLISH_LOG_MAX_FILE_SIZE, eZDebug::maxLogSize() );
        $this->assertSame( eZDebug::MAX_LOGROTATE_FILES, eZDebug::maxLogrotateFiles() );
    }
}
