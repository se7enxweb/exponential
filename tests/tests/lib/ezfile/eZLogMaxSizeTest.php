<?php
/**
 * The size and rotation of the log files from site.ini [FileSettings] LogFileMaxSize and LogFileRotateCount, for
 * eZLog (custom logs) and eZDebug (error.log, warning.log, deprecated.log ...), in a private directory under
 * var/tmp (the site's var/log is never written):
 *   - the shipped default is 2 MB and three rotated files, and that is what both classes use
 *   - a file over the INI size is rotated (.1, .2), the oldest is dropped at the INI count
 *   - setMaxLogSize() / setLogrotateFiles() win over the INI; an empty or invalid setting falls back to the class
 *     default; LogFileRotateCount=0 is accepted (no rotation)
 *   - the constants in config.php (CUSTOM_LOG_MAX_FILE_SIZE, EZPUBLISH_LOG_MAX_FILE_SIZE) still apply and win over
 *     the INI (each in a process of its own, a constant cannot be undefined)
 *
 * The INI values are changed on the loaded site.ini instance and put back in tearDown().
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit e39dde30ff), Copyright (C) Mugo Web, GPL v2
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezfile
 */

class eZLogMaxSizeTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZMaxLogSize', 'eZMaxLogrotateFiles', 'eZDebugMaxLogSize', 'eZDebugMaxLogrotateFiles' );

    private $dir;
    private $savedGlobals = array();
    private $savedINI = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( self::GLOBAL_KEYS as $key )
        {
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
            unset( $GLOBALS[$key] );
        }
        $ini = eZINI::instance();
        foreach ( array( 'LogFileMaxSize', 'LogFileRotateCount' ) as $name )
            $this->savedINI[$name] = $ini->hasVariable( 'FileSettings', $name ) ? array( $ini->variable( 'FileSettings', $name ) ) : null;
        $this->dir = 'var/tmp/phpunit-ezlog-size-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir, 0777, true );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        foreach ( $this->savedINI as $name => $saved )
        {
            if ( $saved === null )
                $ini->removeSetting( 'FileSettings', $name );
            else
                $ini->setVariable( 'FileSettings', $name, $saved[0] );
        }
        foreach ( $this->savedGlobals as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        foreach ( glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    private function setINI( $size, $count )
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'FileSettings', 'LogFileMaxSize', (string)$size );
        $ini->setVariable( 'FileSettings', 'LogFileRotateCount', (string)$count );
    }

    public function testTheShippedDefaultIsTwoMegabytesAndThreeFiles()
    {
        $shipped = eZINI::fetchFromFile( 'settings/site.ini' );
        $this->assertSame( '2097152', $shipped->variable( 'FileSettings', 'LogFileMaxSize' ) );
        $this->assertSame( '3', $shipped->variable( 'FileSettings', 'LogFileRotateCount' ) );
        $this->assertSame( 2097152, eZLog::MAX_LOGFILE_SIZE, 'the fallback without the setting' );
        $this->assertSame( 2097152, eZDebug::MAX_LOGFILE_SIZE );

        $this->setINI( 2097152, 3 );
        if ( !defined( 'CUSTOM_LOG_MAX_FILE_SIZE' ) )
            $this->assertSame( 2097152, eZLog::maxLogSize() );
        if ( !defined( 'EZPUBLISH_LOG_MAX_FILE_SIZE' ) )
            $this->assertSame( 2097152, eZDebug::maxLogSize() );
    }

    public function testRotationFollowsTheINISize()
    {
        if ( defined( 'CUSTOM_LOG_MAX_FILE_SIZE' ) || defined( 'CUSTOM_LOG_ROTATE_FILES' ) )
            $this->markTestSkipped( 'config.php sets the eZLog constants' );
        $this->setINI( 300, 2 );
        $this->assertSame( 300, eZLog::maxLogSize() );
        $this->assertSame( 2, eZLog::maxLogrotateFiles() );

        for ( $i = 1; $i <= 4; ++$i )
            eZLog::write( str_repeat( (string)$i, 400 ), 'size.log', $this->dir );
        $this->assertStringContainsString( '4444', file_get_contents( $this->dir . '/size.log' ) );
        $this->assertStringContainsString( '3333', file_get_contents( $this->dir . '/size.log.1' ) );
        $this->assertStringContainsString( '2222', file_get_contents( $this->dir . '/size.log.2' ) );
        $this->assertFileDoesNotExist( $this->dir . '/size.log.3', 'only LogFileRotateCount files are kept' );

        // Under the size: appended, not rotated
        $this->setINI( 1048576, 2 );
        eZLog::write( 'appended', 'size.log', $this->dir );
        $this->assertStringContainsString( '4444', file_get_contents( $this->dir . '/size.log' ) );
        $this->assertStringContainsString( 'appended', file_get_contents( $this->dir . '/size.log' ) );
    }

    public function testEZDebugReadsTheSameSettings()
    {
        if ( defined( 'EZPUBLISH_LOG_MAX_FILE_SIZE' ) || defined( 'EZPUBLISH_LOG_ROTATE_FILES' ) )
            $this->markTestSkipped( 'config.php sets the eZDebug constants' );
        $this->setINI( 4096, 5 );
        $this->assertSame( 4096, eZDebug::maxLogSize() );
        $this->assertSame( 5, eZDebug::maxLogrotateFiles() );
    }

    public function testOverridesWinAndInvalidSettingsFallBack()
    {
        if ( defined( 'CUSTOM_LOG_MAX_FILE_SIZE' ) || defined( 'CUSTOM_LOG_ROTATE_FILES' ) )
            $this->markTestSkipped( 'config.php sets the eZLog constants' );
        $this->setINI( 4096, 5 );
        eZLog::setMaxLogSize( 777 );
        eZLog::setLogrotateFiles( 1 );
        $this->assertSame( 777, eZLog::maxLogSize() );
        $this->assertSame( 1, eZLog::maxLogrotateFiles() );
        unset( $GLOBALS['eZMaxLogSize'], $GLOBALS['eZMaxLogrotateFiles'] );

        foreach ( array( '', 'big', '-5', '0', '2M' ) as $bad )
        {
            eZINI::instance()->setVariable( 'FileSettings', 'LogFileMaxSize', $bad );
            $this->assertSame( eZLog::MAX_LOGFILE_SIZE, eZLog::maxLogSize(), var_export( $bad, true ) );
        }
        eZINI::instance()->removeSetting( 'FileSettings', 'LogFileMaxSize' );
        $this->assertSame( eZLog::MAX_LOGFILE_SIZE, eZLog::maxLogSize(), 'missing setting' );

        eZINI::instance()->setVariable( 'FileSettings', 'LogFileRotateCount', '0' );
        $this->assertSame( 0, eZLog::maxLogrotateFiles(), '0 turns rotation off' );
        $this->assertNull( eZLog::fileSetting( 'LogFileRotateCount', 1 ) );
    }

    /**
     * A size set in config.php is what the administrator chose: it still applies, over the INI default.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testTheConstantsStillApply()
    {
        if ( !defined( 'CUSTOM_LOG_MAX_FILE_SIZE' ) )
            define( 'CUSTOM_LOG_MAX_FILE_SIZE', 5000 );
        if ( !defined( 'EZPUBLISH_LOG_MAX_FILE_SIZE' ) )
            define( 'EZPUBLISH_LOG_MAX_FILE_SIZE', 6000 );
        unset( $GLOBALS['eZMaxLogSize'], $GLOBALS['eZDebugMaxLogSize'] );
        $this->setINI( 4096, 3 );
        $this->assertSame( (int)CUSTOM_LOG_MAX_FILE_SIZE, eZLog::maxLogSize() );
        $this->assertSame( (int)EZPUBLISH_LOG_MAX_FILE_SIZE, eZDebug::maxLogSize() );
    }
}
