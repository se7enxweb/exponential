<?php
/**
 * The log and expiry paths of a site with a VarDir of its own (multi-site hosting), without the database:
 *
 *  - eZSys::logDirectory(): LogDir inside VarDir, or LogDir itself when absolute;
 *  - eZDebug::setLogDirectory(): the debug logs go there, also for an instance created later, and back to var/log;
 *  - eZLog::write() writes its default logs where eZDebug does, a directory given explicitly stays;
 *  - eZLog::writeStorageLog() writes into the log directory of the site;
 *  - eZUpdateDebugLogDirectory() follows site.ini [FileSettings] UseGlobalLogDir;
 *  - eZExpiryHandler::filePath() follows site.ini [FileSettings] ExpiryDir, and the shared instance is replaced
 *    when ExpiryDir changes.
 *
 * Every test works in its own VarDir under the system temp directory and runs in its own process, because it
 * changes the global site.ini and shared instances.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

require_once dirname( __DIR__, 4 ) . '/kernel/private/classes/debug_settings_functions.php';

#[RunTestsInSeparateProcesses]
class eZSiteLogAndExpiryPathsTest extends PHPUnit\Framework\TestCase
{
    private $dirs = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    protected function tearDown(): void
    {
        foreach ( $this->dirs as $dir )
        {
            self::removeTree( $dir );
        }
        parent::tearDown();
    }

    /** Removes a directory outside the installation, where eZDir::recursiveDelete() does not delete */
    private static function removeTree( $dir )
    {
        if ( !is_dir( $dir ) || is_link( $dir ) )
        {
            @unlink( $dir );
            return;
        }
        foreach ( scandir( $dir ) as $entry )
        {
            if ( $entry !== '.' && $entry !== '..' )
            {
                self::removeTree( $dir . '/' . $entry );
            }
        }
        @rmdir( $dir );
    }

    /** A new directory under the system temp directory, removed after the test */
    private function tempDir( $name )
    {
        $dir = sys_get_temp_dir() . '/x1-site-paths-' . $name . '-' . uniqid();
        mkdir( $dir, 0777, true );
        $this->dirs[] = $dir;
        return $dir;
    }

    /** Points VarDir at a new directory with the given FileSettings */
    private function useSite( array $fileSettings = array() )
    {
        $varDir = $this->tempDir( 'var' );
        $ini = eZINI::instance();
        $ini->setVariable( 'FileSettings', 'VarDir', $varDir );
        $ini->setVariable( 'FileSettings', 'CacheDir', 'cache' );
        $ini->setVariable( 'FileSettings', 'LogDir', 'log' );
        foreach ( $fileSettings as $name => $value )
        {
            $ini->setVariable( 'FileSettings', $name, $value );
        }
        return $varDir;
    }

    public function testLogDirectoryIsInsideVarDir()
    {
        $varDir = $this->useSite();
        $this->assertSame( $varDir . '/log', eZSys::logDirectory() );
    }

    public function testAnAbsoluteLogDirIsUsedAsItIs()
    {
        $logDir = $this->tempDir( 'logs' );
        $this->useSite( array( 'LogDir' => $logDir ) );
        $this->assertSame( $logDir, eZSys::logDirectory() );
    }

    /** One setting moves the logs and caches of every site into a tree of their own, as on a multi-site installation */
    public function testLogVarDirAndCacheVarDirReplaceTheFirstDirectoryOfVarDir()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'FileSettings', 'VarDir', 'var/example' );
        $ini->setVariable( 'FileSettings', 'LogDir', 'log' );
        $ini->setVariable( 'FileSettings', 'CacheDir', 'cache' );
        $ini->setVariable( 'FileSettings', 'LogVarDir', 'var_log' );
        $ini->setVariable( 'FileSettings', 'CacheVarDir', 'var_cache' );

        $this->assertSame( 'var_log/example/log', eZSys::logDirectory() );
        $this->assertSame( 'var_cache/example/cache', eZSys::cacheDirectory() );
        $this->assertSame( 'var/example/storage', eZSys::storageDirectory() );

        $ini->setVariable( 'FileSettings', 'VarDir', 'var' );
        $this->assertSame( 'var_cache/cache', eZSys::cacheDirectory() );

        $ini->setVariable( 'FileSettings', 'VarDir', 'var/example' );
        $ini->setVariable( 'FileSettings', 'LogVarDir', '/srv/logs/' );
        $this->assertSame( '/srv/logs/example/log', eZSys::logDirectory() );
    }

    public function testWithoutLogVarDirAndCacheVarDirNothingMoves()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'FileSettings', 'VarDir', 'var/example' );
        $ini->setVariable( 'FileSettings', 'LogDir', 'log' );
        $ini->setVariable( 'FileSettings', 'CacheDir', 'cache' );
        $ini->setVariable( 'FileSettings', 'LogVarDir', '' );
        $ini->setVariable( 'FileSettings', 'CacheVarDir', '' );

        $this->assertSame( 'var/example/log', eZSys::logDirectory() );
        $this->assertSame( 'var/example/cache', eZSys::cacheDirectory() );

        // An absolute VarDir is not relocated
        $ini->setVariable( 'FileSettings', 'CacheVarDir', 'var_cache' );
        $ini->setVariable( 'FileSettings', 'VarDir', '/srv/example/var' );
        $this->assertSame( '/srv/example/var/cache', eZSys::cacheDirectory() );
    }

    /** With the cache in a tree of its own, ExpiryDir keeps the timestamps inside VarDir */
    public function testExpiryDirStaysInsideVarDirWhenTheCacheMoves()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'FileSettings', 'VarDir', 'var/example' );
        $ini->setVariable( 'FileSettings', 'CacheDir', 'cache' );
        $ini->setVariable( 'FileSettings', 'CacheVarDir', 'var_cache' );
        $ini->setVariable( 'FileSettings', 'ExpiryDir', '' );
        $this->assertSame( 'var_cache/example/cache/expiry.php', eZExpiryHandler::filePath() );

        $ini->setVariable( 'FileSettings', 'ExpiryDir', 'expiry' );
        $this->assertSame( 'var/example/expiry/expiry.php', eZExpiryHandler::filePath() );
    }

    public function testDebugLogsGoToTheDirectoryGivenAndBack()
    {
        $logDir = $this->tempDir( 'logs' );
        $debug = eZDebug::instance();
        $this->assertSame( 'var/log/', $debug->logDirectory() );

        eZDebug::setLogDirectory( $logDir );
        $this->assertSame( $logDir . '/', $debug->logDirectory() );
        foreach ( $debug->logFiles() as $logFile )
        {
            $this->assertSame( $logDir . '/', $logFile[0] );
        }

        eZDebug::setLogDirectory( false );
        $this->assertSame( 'var/log/', $debug->logDirectory() );
    }

    public function testAnInstanceCreatedLaterWritesToTheDirectoryGiven()
    {
        $logDir = $this->tempDir( 'logs' );
        unset( $GLOBALS['eZDebugGlobalInstance'] );
        eZDebug::setLogDirectory( $logDir );

        $this->assertSame( $logDir . '/', eZDebug::instance()->logDirectory() );
    }

    public function testAnErrorIsWrittenIntoTheLogDirectory()
    {
        $logDir = $this->tempDir( 'logs' );
        eZDebug::setLogDirectory( $logDir );
        eZDebug::updateSettings( array( 'debug-enabled' => false, 'always-log' => array( eZDebug::LEVEL_ERROR => true ) ) );

        eZDebug::writeError( 'x1 site paths error', __METHOD__ );

        $this->assertFileExists( $logDir . '/error.log' );
        $this->assertStringContainsString( 'x1 site paths error', file_get_contents( $logDir . '/error.log' ) );
    }

    public function testDefaultLogsOfEZLogFollowTheDebugLogs()
    {
        $logDir = $this->tempDir( 'logs' );
        $otherDir = $this->tempDir( 'other' );
        eZDebug::setLogDirectory( $logDir );

        eZLog::write( 'x1 default log', 'x1-site-paths.log' );
        eZLog::write( 'x1 explicit log', 'x1-site-paths.log', $otherDir );

        $this->assertStringContainsString( 'x1 default log', file_get_contents( $logDir . '/x1-site-paths.log' ) );
        $this->assertStringContainsString( 'x1 explicit log', file_get_contents( $otherDir . '/x1-site-paths.log' ) );
        $this->assertFileDoesNotExist( 'var/log/x1-site-paths.log' );
    }

    public function testStorageLogIsWrittenIntoAnAbsoluteLogDir()
    {
        $logDir = $this->tempDir( 'logs' );
        $this->useSite( array( 'LogDir' => $logDir ) );

        eZLog::writeStorageLog( 'x1-file.png', 'var/storage' );

        $this->assertFileExists( $logDir . '/storage.log' );
    }

    public function testUseGlobalLogDirChoosesTheDirectory()
    {
        $varDir = $this->useSite( array( 'UseGlobalLogDir' => 'disabled' ) );
        $this->assertSame( $varDir . '/log', eZUpdateDebugLogDirectory() );
        $this->assertSame( $varDir . '/log/', eZDebug::instance()->logDirectory() );

        eZINI::instance()->setVariable( 'FileSettings', 'UseGlobalLogDir', 'enabled' );
        $this->assertFalse( eZUpdateDebugLogDirectory() );
        $this->assertSame( 'var/log/', eZDebug::instance()->logDirectory() );
    }

    public function testExpiryFileFollowsExpiryDir()
    {
        $varDir = $this->useSite( array( 'ExpiryDir' => '' ) );
        $this->assertSame( $varDir . '/cache/expiry.php', eZExpiryHandler::filePath() );

        eZINI::instance()->setVariable( 'FileSettings', 'ExpiryDir', 'expiry' );
        $this->assertSame( $varDir . '/expiry/expiry.php', eZExpiryHandler::filePath() );

        $absolute = $this->tempDir( 'expiry' );
        eZINI::instance()->setVariable( 'FileSettings', 'ExpiryDir', $absolute );
        $this->assertSame( $absolute . '/expiry.php', eZExpiryHandler::filePath() );
    }

    /** Timestamps kept outside the cache survive a cache directory that is emptied (a memory file system) */
    public function testTimestampsInExpiryDirSurviveAnEmptiedCacheDirectory()
    {
        $varDir = $this->useSite( array( 'ExpiryDir' => 'expiry' ) );
        eZExpiryHandler::registerShutdownFunction();
        $handler = eZExpiryHandler::instance();
        $handler->setTimestamp( 'image-alias', 1234 );
        $handler->store();
        $this->assertFileExists( $varDir . '/expiry/expiry.php' );

        self::removeTree( $varDir . '/cache' );
        unset( $GLOBALS['eZExpiryHandlerInstance'] );

        $this->assertSame( 1234, eZExpiryHandler::getTimestamp( 'image-alias' ) );
    }

    /** Without any of the new settings the directories are exactly those of before */
    public function testTheDefaultsKeepTheDirectoriesOfBefore()
    {
        $ini = eZINI::instance();
        foreach ( array( 'LogVarDir', 'CacheVarDir', 'ExpiryDir' ) as $name )
        {
            $this->assertSame( '', (string)$ini->variable( 'FileSettings', $name ), $name );
        }
        $this->assertSame( 'enabled', $ini->variable( 'FileSettings', 'UseGlobalLogDir' ) );
        $this->assertSame( 'global', $ini->variable( 'FileSettings', 'INICacheDir' ) );

        $varDir = $ini->variable( 'FileSettings', 'VarDir' );
        $this->assertSame( eZDir::path( array( $varDir, $ini->variable( 'FileSettings', 'CacheDir' ) ) ), eZSys::cacheDirectory() );
        $this->assertSame( eZDir::path( array( $varDir, $ini->variable( 'FileSettings', 'LogDir' ) ) ), eZSys::logDirectory() );
        $this->assertSame( eZSys::cacheDirectory() . '/expiry.php', eZExpiryHandler::filePath() );
        $this->assertFalse( eZUpdateDebugLogDirectory() );
        $this->assertSame( 'var/log/', eZDebug::instance()->logDirectory() );
    }

    /** A VarDir that leads out of the installation has no first directory to replace */
    public function testAVarDirStartingWithDotDotIsNotRelocated()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'FileSettings', 'VarDir', '../shared/var/example' );
        $ini->setVariable( 'FileSettings', 'CacheDir', 'cache' );
        $ini->setVariable( 'FileSettings', 'LogDir', 'log' );
        $ini->setVariable( 'FileSettings', 'CacheVarDir', 'var_cache' );
        $ini->setVariable( 'FileSettings', 'LogVarDir', 'var_log' );

        $this->assertSame( '../shared/var/example/cache', eZSys::cacheDirectory() );
        $this->assertSame( '../shared/var/example/log', eZSys::logDirectory() );

        // "./var/example" is the same directory as "var/example"
        $ini->setVariable( 'FileSettings', 'VarDir', './var/example' );
        $this->assertSame( 'var_cache/example/cache', eZSys::cacheDirectory() );
    }

    /** An empty CacheDir or LogDir no longer reads past the end of the string */
    public function testEmptyDirectorySettings()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'FileSettings', 'VarDir', 'var/example' );
        $ini->setVariable( 'FileSettings', 'CacheDir', '' );
        $ini->setVariable( 'FileSettings', 'LogDir', '' );
        $ini->setVariable( 'FileSettings', 'CacheVarDir', '' );
        $ini->setVariable( 'FileSettings', 'LogVarDir', '' );

        $this->assertSame( 'var/example', eZSys::cacheDirectory() );
        $this->assertSame( 'var/example/log', eZSys::logDirectory() );
    }

    /**
     * The cache directory of another siteaccess, as the caches of the classic menu and the toolbar clear it: derived as
     * that of the current site, also below CacheVarDir
     */
    public function testTheCacheDirectoryOfAnotherSiteaccess()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'FileSettings', 'VarDir', 'var/example' );
        $ini->setVariable( 'FileSettings', 'CacheDir', 'cache' );
        $ini->setVariable( 'FileSettings', 'CacheVarDir', 'var_cache' );

        // Settings of another siteaccess, without reading any file
        $other = new eZINI( 'x1-site-paths.ini', 'var/tmp', null, false, false, false, false, false );
        $this->assertSame( eZSys::cacheDirectory(), eZSys::cacheDirectoryOf( $other ) );

        $other->setVariable( 'FileSettings', 'VarDir', 'var/other' );
        $this->assertSame( 'var_cache/other/cache', eZSys::cacheDirectoryOf( $other ) );

        $other->setVariable( 'FileSettings', 'CacheVarDir', '' );
        $this->assertSame( 'var/other/cache', eZSys::cacheDirectoryOf( $other ) );

        $other->setVariable( 'FileSettings', 'CacheDir', '/srv/other-cache/' );
        $this->assertSame( '/srv/other-cache', eZSys::cacheDirectoryOf( $other ) );
    }

    public function testTheSharedInstanceFollowsAChangedExpiryDir()
    {
        $varDir = $this->useSite( array( 'ExpiryDir' => '' ) );
        eZExpiryHandler::instance();

        eZINI::instance()->setVariable( 'FileSettings', 'ExpiryDir', 'expiry' );

        $this->assertTrue( eZExpiryHandler::resetForCurrentCacheDirectory() );
        $this->assertSame( $varDir . '/expiry/expiry.php', eZExpiryHandler::instance()->CacheFile->name() );
    }
}
