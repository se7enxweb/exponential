<?php
/**
 * The web setup wizard on a site served by several nodes: the lease, the run and the installed marker were in each
 * node's own var/, so a step that landed on another node started the wizard over ("reset after the system check").
 * Two installations under var/tmp/setup-shared-state-tests/ stand for two nodes, and a third directory for the storage
 * they share. Without a database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SetupSharedStateTest extends PHPUnit\Framework\TestCase
{
    private $base;
    private $nodeA;
    private $nodeB;
    private $shared;
    private $cookie;
    private $env;
    private $exceptionHandler;

    protected function setUp(): void
    {
        $this->base = dirname( __DIR__, 5 ) . '/var/tmp/setup-shared-state-tests/' . getmypid() . '-' . $this->name();
        $this->nodeA = $this->base . '/node-a';
        $this->nodeB = $this->base . '/node-b';
        $this->shared = $this->base . '/shared';
        foreach ( array( $this->nodeA . '/var', $this->nodeB . '/var', $this->shared ) as $dir )
            if ( !is_dir( $dir ) )
                mkdir( $dir, 0775, true );
        $this->cookie = $_COOKIE;
        $this->exceptionHandler = set_exception_handler( null );
        restore_exception_handler();
        $this->env = getenv( expSetupSharedState::ENV );
        putenv( expSetupSharedState::ENV . '=' . $this->shared );
        expSetupSharedState::reset();
    }

    protected function tearDown(): void
    {
        // the settings layer installs eZExecution's exception handler once, on first use; not this test's to keep
        $handler = set_exception_handler( null );
        restore_exception_handler();
        if ( $handler !== $this->exceptionHandler )
            restore_exception_handler();
        $_COOKIE = $this->cookie;
        putenv( $this->env === false ? expSetupSharedState::ENV : expSetupSharedState::ENV . '=' . $this->env );
        expSetupSharedState::reset();
        $this->removeTree( $this->base );
        @rmdir( dirname( $this->base ) );
    }

    private function removeTree( $dir )
    {
        if ( !is_dir( $dir ) || is_link( $dir ) )
        {
            @unlink( $dir );
            return;
        }
        foreach ( scandir( $dir ) as $entry )
            if ( $entry !== '.' && $entry !== '..' )
                $this->removeTree( $dir . '/' . $entry );
        @rmdir( $dir );
    }

    /** The cookie beginWizard() would have sent: its hash is in the marker, so a test sets one and writes its hash */
    private function holdLease( $root, $runId, $token )
    {
        $this->assertTrue( expMaintenance::beginWizard( $root, $runId ) );
        $file = expMaintenance::wizardMarker( $root );
        $state = json_decode( file_get_contents( $file ), true );
        $state['allow_token'] = hash( 'sha256', $token );
        file_put_contents( $file, json_encode( $state ) );
        $_COOKIE[expMaintenance::WIZARD_COOKIE] = $token;
    }

    public function testTheDirectoryComesFromTheEnvironmentTheSettingOrTheDfsMountAndElseIsNone()
    {
        $root = '/srv/site';
        $this->assertSame( '/mnt/shared/setup', expSetupSharedState::resolveDir( $root, array( 'env' => '/mnt/shared/setup/', 'shared_dir' => '/elsewhere' ) ) );
        $this->assertSame( '/elsewhere', expSetupSharedState::resolveDir( $root, array( 'env' => '', 'shared_dir' => '/elsewhere',
            'file_handler' => 'eZDFSFileHandler', 'dfs_mount' => '/mnt/dfs' ) ) );
        $this->assertSame( '/srv/site/var/shared-setup', expSetupSharedState::resolveDir( $root, array( 'shared_dir' => 'var/shared-setup' ) ), 'relative to the installation' );
        $this->assertSame( '/mnt/dfs/var/exp-setup', expSetupSharedState::resolveDir( $root, array( 'file_handler' => 'eZDFSFileHandler', 'dfs_mount' => '/mnt/dfs/' ) ) );
        // a single node, or a cluster without the DFS mount: everything stays in var/ as before
        $this->assertNull( expSetupSharedState::resolveDir( $root, array() ) );
        $this->assertNull( expSetupSharedState::resolveDir( $root, array( 'file_handler' => 'eZFSFileHandler', 'dfs_mount' => '/mnt/dfs' ) ) );
        $this->assertNull( expSetupSharedState::resolveDir( $root, array( 'file_handler' => 'eZDFSFileHandler', 'dfs_mount' => '' ) ) );
    }

    public function testASingleNodeKeepsItsMarkerInVar()
    {
        putenv( expSetupSharedState::ENV );
        expSetupSharedState::reset();
        // an installation under var/tmp is not the one whose settings this process reads: none of them apply
        $this->assertNull( expSetupSharedState::dir( $this->nodeA ) );
        $this->assertSame( $this->nodeA . '/' . expMaintenance::MARKER, expMaintenance::wizardMarker( $this->nodeA ) );
        $this->assertSame( array( $this->nodeA . '/' . expSetupWizardGuard::INSTALLED_MARKER ), expSetupWizardGuard::installedMarkers( $this->nodeA ) );
        $this->assertFalse( expMaintenance::sharedWizardState( $this->nodeA ), 'no second check in the kernel on a single node' );
    }

    public function testALeaseTakenOnOneNodeHoldsOnTheOther()
    {
        $this->holdLease( $this->nodeA, 'run-1', 'token-1' );
        $this->assertFileExists( $this->shared . '/maintenance.json' );
        $this->assertFileDoesNotExist( $this->nodeA . '/' . expMaintenance::MARKER, 'not in the node\'s own var/' );

        // the next step lands on node B: the same run renews the same lease
        $this->assertIsArray( expMaintenance::wizardState( $this->nodeB ) );
        $this->assertTrue( expMaintenance::renewWizard( $this->nodeB, 'run-1' ) );
        $this->assertFalse( expMaintenance::renewWizard( $this->nodeB, 'run-2' ), 'another run does not renew it' );

        // node B lets the wizard's browser through and answers everybody else with the maintenance page
        $this->assertFalse( expMaintenance::sharedWizardState( $this->nodeB ) );
        unset( $_COOKIE[expMaintenance::WIZARD_COOKIE] );
        $state = expMaintenance::sharedWizardState( $this->nodeB );
        $this->assertIsArray( $state );
        $this->assertSame( 'setup', $state['reason'] );

        // the last page, served by node B, ends it for both nodes
        $_COOKIE[expMaintenance::WIZARD_COOKIE] = 'token-1';
        $this->assertTrue( expMaintenance::endWizard( $this->nodeB, 'run-1' ) );
        $this->assertFalse( expMaintenance::wizardState( $this->nodeA ) );
        unset( $_COOKIE[expMaintenance::WIZARD_COOKIE] );
        $this->assertFalse( expMaintenance::sharedWizardState( $this->nodeA ) );
    }

    public function testASecondBrowserCannotTakeAHeldLeaseButTheFirstOneCanAndARunOutLeaseIsFree()
    {
        $this->holdLease( $this->nodeA, 'run-1', 'token-1' );
        unset( $_COOKIE[expMaintenance::WIZARD_COOKIE] );
        $this->assertFalse( expMaintenance::beginWizard( $this->nodeB, 'run-2' ), 'another browser on another node is refused' );
        $state = json_decode( file_get_contents( $this->shared . '/maintenance.json' ), true );
        $this->assertSame( 'run-1', $state['run'], 'the lease is left as it was' );

        $_COOKIE[expMaintenance::WIZARD_COOKIE] = 'token-1';
        $this->assertTrue( expMaintenance::beginWizard( $this->nodeB, 'run-2' ), 'the browser that holds it starts again' );

        $state = json_decode( file_get_contents( $this->shared . '/maintenance.json' ), true );
        $state['lease'] = time() - 1;
        file_put_contents( $this->shared . '/maintenance.json', json_encode( $state ) );
        unset( $_COOKIE[expMaintenance::WIZARD_COOKIE] );
        $this->assertTrue( expMaintenance::beginWizard( $this->nodeA, 'run-3' ), 'a lease that ran out holds nobody' );
    }

    public function testTheLockWaitsForAHolderAndBreaksAStaleOne()
    {
        $lock = $this->shared . '/.exp-setup.lock';
        mkdir( $lock );
        $this->assertNull( expSetupSharedState::lock( $this->shared, function () { return 'ran'; }, 0.1 ), 'a held lock is waited for, then given up' );
        touch( $lock, time() - expSetupSharedState::LOCK_STALE - 5 );
        $this->assertSame( 'ran', expSetupSharedState::lock( $this->shared, function () { return 'ran'; }, 0.1 ), 'a stale lock is broken' );
        $this->assertDirectoryDoesNotExist( $lock, 'released after use' );
        // below a file: no system can make that directory (Windows would make /proc/... on the current drive)
        file_put_contents( $this->shared . '/a-file', 'x' );
        $this->assertNull( @expSetupSharedState::lock( $this->shared . '/a-file/setup', function () { return 'ran'; }, 0.1 ), 'no directory, no lock' );
    }

    public function testFilesTheWizardWroteOnOneNodeReachTheOther()
    {
        $since = time() - 10;
        mkdir( $this->nodeA . '/settings/override', 0775, true );
        mkdir( $this->nodeA . '/settings/siteaccess/site', 0775, true );
        file_put_contents( $this->nodeA . '/settings/override/site.ini.append.php', "[SiteAccessSettings]\nCheckValidity=false\n" );
        // a settings file holds the database password: its mode travels with it, never wider
        chmod( $this->nodeA . '/settings/override/site.ini.append.php', 0640 );
        file_put_contents( $this->nodeA . '/settings/siteaccess/site/site.ini.append.php', "[SiteSettings]\nSiteName=Site\n" );
        // a file the run did not write (older than the run) stays where it is
        file_put_contents( $this->nodeA . '/settings/override/old.ini.append.php', "old\n" );
        touch( $this->nodeA . '/settings/override/old.ini.append.php', $since - 100 );

        $paths = array( 'settings/override', 'settings/siteaccess', '../outside', '/etc' );
        $this->assertSame( 2, expSetupSharedState::push( $this->nodeA, $this->shared, $paths, $since, 'run-1' ) );
        $this->assertSame( 0, expSetupSharedState::push( $this->nodeA, $this->shared, $paths, $since, 'run-1' ), 'nothing changed, nothing copied' );
        $manifest = json_decode( file_get_contents( $this->shared . '/' . expSetupSharedState::MANIFEST ), true );
        $this->assertSame( 'run-1', $manifest['run'] );
        $this->assertSame( array( 'settings/override/site.ini.append.php', 'settings/siteaccess/site/site.ini.append.php' ), array_keys( $manifest['files'] ) );

        // only this run's files: an earlier run's manifest never writes over the settings
        $this->assertSame( 0, expSetupSharedState::pull( $this->nodeB, $this->shared, 'run-0' ) );
        $this->assertFileDoesNotExist( $this->nodeB . '/settings/override/site.ini.append.php' );

        $this->assertSame( 2, expSetupSharedState::pull( $this->nodeB, $this->shared, 'run-1' ) );
        if ( PHP_OS_FAMILY !== 'Windows' ) // Windows has no Unix file modes
            $this->assertSame( 0640, fileperms( $this->nodeB . '/settings/override/site.ini.append.php' ) & 0777, 'the mode of node A\'s file' );
        if ( PHP_OS_FAMILY !== 'Windows' ) // Windows has no Unix file modes
            $this->assertSame( 0640, fileperms( $this->shared . '/files/settings/override/site.ini.append.php' ) & 0777, 'the shared copy too' );
        $this->assertFileEquals( $this->nodeA . '/settings/override/site.ini.append.php', $this->nodeB . '/settings/override/site.ini.append.php' );
        $this->assertFileEquals( $this->nodeA . '/settings/siteaccess/site/site.ini.append.php', $this->nodeB . '/settings/siteaccess/site/site.ini.append.php' );
        $this->assertFileDoesNotExist( $this->nodeB . '/settings/override/old.ini.append.php' );
        $this->assertSame( 0, expSetupSharedState::pull( $this->nodeB, $this->shared, 'run-1' ), 'already there' );
        // what node B pulled is not pushed back as its own change
        $this->assertSame( 0, expSetupSharedState::push( $this->nodeB, $this->shared, $paths, $since, 'run-1' ) );

        // node B changes a file in a later step: node A gets the new version
        file_put_contents( $this->nodeB . '/settings/override/site.ini.append.php', "[SiteAccessSettings]\nCheckValidity=false\nAvailableSiteAccessList[]=site\n" );
        $this->assertSame( 1, expSetupSharedState::push( $this->nodeB, $this->shared, $paths, $since, 'run-1' ) );
        $this->assertSame( 1, expSetupSharedState::pull( $this->nodeA, $this->shared, 'run-1' ) );
        $this->assertFileEquals( $this->nodeB . '/settings/override/site.ini.append.php', $this->nodeA . '/settings/override/site.ini.append.php' );

        // a new run starts its own manifest
        $this->assertSame( 2, expSetupSharedState::push( $this->nodeA, $this->shared, $paths, $since, 'run-2' ) );
        $manifest = json_decode( file_get_contents( $this->shared . '/' . expSetupSharedState::MANIFEST ), true );
        $this->assertSame( 'run-2', $manifest['run'] );
    }

    /**
     * The shared directory holds copies of the new site's settings, with the database password: what the wizard
     * creates there is never readable by others (0750 directories, 0640 files or narrower), whatever the mode of
     * the file it copies; a node that fetches a file gives it that file's own mode.
     */
    public function testNothingTheWizardWritesToTheSharedDirectoryIsReadableByOthers()
    {
        if ( PHP_OS_FAMILY === 'Windows' )
            $this->markTestSkipped( 'Windows has no Unix file modes' );
        mkdir( $this->nodeA . '/settings/override', 0775, true );
        file_put_contents( $this->nodeA . '/settings/override/site.ini.append.php', "[DatabaseSettings]\nPassword=secret\n" );
        chmod( $this->nodeA . '/settings/override/site.ini.append.php', 0664 );
        $this->assertSame( 1, expSetupSharedState::push( $this->nodeA, $this->shared, array( 'settings/override' ), time() - 10, 'run-1' ) );
        $this->assertTrue( expSetupWizardGuard::markInstalled( $this->nodeA, 'run-1' ) );
        $this->holdLease( $this->nodeA, 'run-1', 'token-a' );

        foreach ( array( 'files', 'files/settings', 'files/settings/override' ) as $dir )
            $this->assertSame( 0750, fileperms( $this->shared . '/' . $dir ) & 0777, $dir );
        foreach ( array( 'files/settings/override/site.ini.append.php', 'files/manifest.json', 'site_installed.json' ) as $file )
            $this->assertSame( 0640, fileperms( $this->shared . '/' . $file ) & 0777, $file );
        $this->assertSame( 0, fileperms( expMaintenance::wizardMarker( $this->nodeA ) ) & 0007, 'the lease' );
        $this->assertTrue( expSetupSharedState::isSharedPath( $this->shared . '/log/setup.log' ), 'setup.log gets the same modes' );
        $this->assertFalse( expSetupSharedState::isSharedPath( $this->nodeA . '/var/log/setup.log' ) );

        $manifest = json_decode( file_get_contents( $this->shared . '/' . expSetupSharedState::MANIFEST ), true );
        $this->assertSame( '0664', $manifest['files']['settings/override/site.ini.append.php']['mode'] );
        $this->assertSame( 1, expSetupSharedState::pull( $this->nodeB, $this->shared, 'run-1' ) );
        $this->assertSame( 0664, fileperms( $this->nodeB . '/settings/override/site.ini.append.php' ) & 0777, 'node B: the mode of node A\'s file' );
    }

    public function testAFileWhoseSharedCopyIsDamagedIsNotWritten()
    {
        mkdir( $this->nodeA . '/settings/override', 0775, true );
        file_put_contents( $this->nodeA . '/settings/override/site.ini.append.php', "good\n" );
        $this->assertSame( 1, expSetupSharedState::push( $this->nodeA, $this->shared, array( 'settings/override' ), time() - 10, 'run-1' ) );
        file_put_contents( $this->shared . '/files/settings/override/site.ini.append.php', "damaged\n" );
        $this->assertFalse( expSetupSharedState::pull( $this->nodeB, $this->shared, 'run-1' ) );
        $this->assertFileDoesNotExist( $this->nodeB . '/settings/override/site.ini.append.php' );
    }

    public function testTheInstalledMarkerIsSeenByEveryNode()
    {
        $this->assertFalse( expSetupWizardGuard::isInstalled( $this->nodeB, true, function () { return false; } ) );
        $this->assertTrue( expSetupWizardGuard::markInstalled( $this->nodeA, 'run-7' ) );
        $this->assertFileExists( $this->shared . '/site_installed.json' );
        $this->assertFileExists( $this->nodeA . '/' . expSetupWizardGuard::INSTALLED_MARKER );
        $this->assertTrue( expSetupWizardGuard::isInstalled( $this->nodeB, true, function () { return false; } ), 'node B did not serve the last page' );
        $this->assertSame( 'run-7', expSetupWizardGuard::sharedInstalledRun( $this->nodeB ) );
    }

    public function testTheWebWizardsRunIsKeptInTheSharedDirectory()
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? null;
        $logDir = getenv( 'EXP_SETUP_LOG_DIR' );
        putenv( 'EXP_SETUP_LOG_DIR' );
        try
        {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $this->assertSame( $this->shared . '/log', expSetupLog::setupLogDir() );
            // the kickstarter: one process on one node, var/log as before
            unset( $_SERVER['REQUEST_METHOD'] );
            $this->assertSame( expSetupLog::LOG_DIR, expSetupLog::setupLogDir() );
        }
        finally
        {
            if ( $method === null )
                unset( $_SERVER['REQUEST_METHOD'] );
            else
                $_SERVER['REQUEST_METHOD'] = $method;
            putenv( $logDir === false ? 'EXP_SETUP_LOG_DIR' : 'EXP_SETUP_LOG_DIR=' . $logDir );
        }
    }

    public function testTheKernelAndTheViewUseTheSharedState()
    {
        $root = dirname( __DIR__, 5 );
        $kernel = file_get_contents( $root . '/kernel/private/classes/ezpkernelweb.php' );
        // checked in run() before requestInit(): before the siteaccess, the session and the database
        $this->assertLessThan( strpos( $kernel, '$this->requestInit();', strpos( $kernel, 'public function run()' ) ),
                               strpos( $kernel, '$this->sharedMaintenanceResult()' ) );
        $view = file_get_contents( $root . '/kernel/private/classes/views/setup/ezsetup.php' );
        $this->assertStringContainsString( '\\expSetupSharedState::pull( $root, $sharedDir, $setupRunId )', $view );
        $this->assertStringContainsString( '\\expSetupSharedState::push(', $view );
        $ini = file_get_contents( $root . '/settings/site.ini' );
        $this->assertSame( 1, preg_match( '/^\[SetupSettings\]$(.*?)^\[/ms', $ini, $block ) );
        $this->assertMatchesRegularExpression( '/^SharedStateDir=$/m', $block[1], 'empty by default: a single node is unchanged' );
    }
}
