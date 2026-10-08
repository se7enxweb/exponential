<?php
/**
 * What reads the logs and audit records of a site follows where they are written (multi-site hosting, site.ini
 * [FileSettings] UseGlobalLogDir, LogDir, LogVarDir), without the database:
 *
 *  - expSetupLog counts the errors of a step in the error.log eZDebug writes, not in var/log/error.log;
 *  - the system report names the log directory of the site and finds the cronjob log there;
 *  - the audit records stay inside VarDir (or an absolute [AuditSettings] LogDir), whatever LogVarDir says, also
 *    with an absolute VarDir;
 *  - eZSiteAccess::resetSitePaths() gives back the paths of a request whose siteaccess is not known yet (the Velocity
 *    warm-up).
 *
 * Every test runs in its own process, because it changes the global site.ini and shared instances.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
class eZSiteLogReadersTest extends PHPUnit\Framework\TestCase
{
    private $root;
    private $dirs = array();

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 4 );
        chdir( $this->root );
    }

    protected function tearDown(): void
    {
        chdir( $this->root );
        putenv( 'EXP_SETUP_LOG_DIR' );
        eZDebug::setLogDirectory( false );
        // What a shutdown function would still write (expiry.php, storage.log) goes to the defaults, not into a
        // directory removed below
        unset( $GLOBALS['eZExpiryHandlerInstance'] );
        foreach ( array_reverse( $this->dirs ) as $dir )
        {
            self::removeTree( $dir );
        }
        parent::tearDown();
    }

    /** Removes a directory, also outside the installation, where eZDir::recursiveDelete() does not delete */
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
        $dir = sys_get_temp_dir() . '/x1-site-readers-' . $name . '-' . uniqid();
        mkdir( $dir, 0777, true );
        $this->dirs[] = $dir;
        return $dir;
    }

    /** The errors of a setup step are read where eZDebug wrote them: the log directory of the site */
    public function testTheSetupLogCountsTheErrorsInTheLogDirectoryOfTheSite()
    {
        $logDir = $this->tempDir( 'log' );
        $setupDir = $this->tempDir( 'setup' );
        putenv( 'EXP_SETUP_LOG_DIR=' . $setupDir );
        eZDebug::setLogDirectory( $logDir );
        eZDebug::updateSettings( array( 'debug-enabled' => false, 'always-log' => array( eZDebug::LEVEL_ERROR => true ) ) );

        $this->assertNotNull( expSetupLog::start( 'x1-test' ) );
        expSetupLog::stepBegin( 'x1 step' );
        eZDebug::writeError( 'x1 setup reader error', __METHOD__ );
        expSetupLog::stepEnd( 'ok' );

        $this->assertStringContainsString( 'x1 setup reader error', (string)@file_get_contents( $logDir . '/error.log' ) );
        $setupLog = (string)file_get_contents( $setupDir . '/setup.log' );
        $this->assertMatchesRegularExpression( '/ERROR\s+.*x1 setup reader error/', $setupLog );
        $this->assertStringContainsString( '<< x1 step ok', $setupLog );
        $this->assertMatchesRegularExpression( '/<< x1 step ok, [0-9.]+s, 1 errors/', $setupLog );
        // The BEGIN marker of the run is in the same error.log
        $this->assertStringContainsString( 'expSetupLog:', file_get_contents( $logDir . '/error.log' ) );
    }

    /** The system report names the log directory the site writes to, and finds the cronjob log there */
    public function testTheSystemReportNamesTheLogDirectoryOfTheSite()
    {
        $logDir = $this->tempDir( 'log' );
        $ini = eZINI::instance();
        $ini->setVariable( 'FileSettings', 'LogDir', $logDir );
        $ini->setVariable( 'FileSettings', 'UseGlobalLogDir', 'disabled' );
        eZSiteAccess::updateLogDirectory( $ini );
        file_put_contents( $logDir . '/cronjob-x1part.log', "ran\n" );
        touch( $logDir . '/cronjob-x1part.log', time() + 3600 );

        $storage = new ReflectionMethod( 'expSystemReport', 'collectStorage' );
        $facts = $storage->invoke( null, $this->root, false, microtime( true ) + 10 );
        $this->assertSame( $logDir, $facts['log'] );
        $this->assertTrue( $facts['log_writable'] );

        $cronjobs = new ReflectionMethod( 'expSystemReport', 'collectCronjobs' );
        $facts = $cronjobs->invoke( null );
        $this->assertSame( 'log', $facts['source'] );
        $this->assertSame( 'x1part', $facts['part'] );
    }

    /** The audit trail stays with the storage of the site: LogVarDir and LogDir of site.ini do not move it */
    public function testAuditRecordsStayInsideVarDir()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'FileSettings', 'VarDir', 'var/x1example' );
        $ini->setVariable( 'FileSettings', 'LogVarDir', 'var_log' );
        $ini->setVariable( 'FileSettings', 'LogDir', '/srv/x1-logs' );
        expAuditConfig::reset();

        $root = expAuditConfig::root();
        $this->assertSame( $root . 'var/x1example/log/audit', expAuditConfig::get()['logDir'] );
        $this->assertSame( $root . 'var/x1example/log/audit/spool', expAuditConfig::path( 'log/audit/spool' ) );
        $audit = eZAudit::fetchAuditNameSettings();
        foreach ( $audit as $name => $setting )
        {
            $this->assertSame( expAuditConfig::path( 'log/audit' ), rtrim( $setting['dir'], '/' ), $name );
        }

        // An absolute VarDir is used as it is, not put inside the installation
        $varDir = $this->tempDir( 'var' );
        $ini->setVariable( 'FileSettings', 'VarDir', $varDir );
        expAuditConfig::reset();
        $this->assertSame( $varDir . '/log/audit', expAuditConfig::get()['logDir'] );
        $this->assertSame( $varDir . '/log/audit/archive', expAuditConfig::path( 'log/audit/archive' ) );
    }

    /** The Velocity warm-up renders a site, then gives every request the paths of one without a siteaccess */
    public function testResetSitePathsGivesBackThePathsOfARequestWithoutSiteaccess()
    {
        $logDir = $this->tempDir( 'log' );
        $iniDir = $this->tempDir( 'ini' );
        $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = $iniDir . '/before/';
        $ini = eZINI::instance();
        $ini->setVariable( 'FileSettings', 'UseGlobalLogDir', 'disabled' );
        $ini->setVariable( 'FileSettings', 'LogDir', $logDir );
        $ini->setVariable( 'FileSettings', 'CacheDir', $iniDir . '/site-cache' );
        $ini->setVariable( 'FileSettings', 'INICacheDir', 'site' );
        eZSiteAccess::updateLogDirectory( $ini );
        eZSiteAccess::updateINICacheDirectory( $ini );
        $this->assertSame( $logDir . '/', eZDebug::instance()->logDirectory() );
        $this->assertSame( $iniDir . '/site-cache/ini/', $GLOBALS['eZINI_CONFIG_CACHE_DIR'] );

        eZSiteAccess::resetSitePaths();

        $this->assertSame( 'var/log/', eZDebug::instance()->logDirectory() );
        $this->assertArrayNotHasKey( 'eZDebugLogDir', $GLOBALS );
        $this->assertSame( $iniDir . '/before/', $GLOBALS['eZINI_CONFIG_CACHE_DIR'] );
        $this->assertArrayNotHasKey( 'eZSiteAccessINICacheDir', $GLOBALS );
    }
}
