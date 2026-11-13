<?php
/**
 * The log viewer under a document root made of symbolic links to an installation: a second host (edit.<site>)
 * served from its own folder whose index.php and var/ link to the real installation. The installation is the folder
 * of the index.php that runs, so its var/log is inside the root; before, every log directory resolved outside the
 * link folder and Setup > Log files showed none (found on exponential.earth, 2026-11-12). A link out of the
 * installation is still never followed.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expLogViewerSymlinkedRootTest extends ezpTestCase
{
    private $base;

    protected function setUp(): void
    {
        parent::setUp();
        $this->base = dirname( __DIR__, 4 ) . '/var/tmp/logviewer-symlinked-root-' . getmypid();
        @mkdir( $this->base . '/real/var/log', 0700, true );
        @mkdir( $this->base . '/links', 0700, true );
        @mkdir( $this->base . '/elsewhere/log', 0700, true );
        file_put_contents( $this->base . '/real/index.php', "<?php\n" );
        file_put_contents( $this->base . '/real/var/log/error.log', "[ Nov 12 2026 12:00:00 ] [127.0.0.1] something\n" );
        @symlink( '../real/index.php', $this->base . '/links/index.php' );
        @symlink( '../real/var', $this->base . '/links/var' );
        if ( !is_link( $this->base . '/links/index.php' ) )
            $this->markTestSkipped( 'symbolic links cannot be made here' );
    }

    protected function tearDown(): void
    {
        foreach ( array( '/links/index.php', '/links/var', '/real/var/log/error.log', '/real/index.php' ) as $file )
            @unlink( $this->base . $file );
        foreach ( array( '/real/var/log', '/real/var', '/real', '/links', '/elsewhere/log', '/elsewhere', '' ) as $dir )
            @rmdir( $this->base . $dir );
        parent::tearDown();
    }

    private function config( $root )
    {
        return expLogViewerConfig::fromArray( array( 'LogViewerSettings' => array( 'Directories' => array( 'var/log' ) ) ), $root );
    }

    /** LV-SYM-01: the root of a link folder is the folder of the index.php that runs */
    public function testTheRootOfALinkFolderIsTheRealInstallation()
    {
        $this->assertSame( realpath( $this->base . '/real' ), $this->config( $this->base . '/links' )->root() );
        $this->assertSame( realpath( $this->base . '/real' ), $this->config( $this->base . '/real' )->root() );
    }

    /** LV-SYM-02: its var/log is found, by the same id as from the installation itself */
    public function testTheLogsOfTheInstallationAreFound()
    {
        $locator = new expLogFileLocator( $this->config( $this->base . '/links' ), 'var/log' );
        $this->assertSame( array( realpath( $this->base . '/real/var/log' ) => 'var/log' ), $locator->directories() );
    }

    /** LV-SYM-04: the installation's own var linked to another volume: its logs are found under their var/ ids */
    public function testAVarLinkedToAnotherVolumeIsTheInstallations()
    {
        @mkdir( $this->base . '/volume/var/log', 0700, true );
        file_put_contents( $this->base . '/volume/var/log/error.log', "[ Nov 12 2026 12:00:00 ] [127.0.0.1] elsewhere\n" );
        @mkdir( $this->base . '/site', 0700, true );
        file_put_contents( $this->base . '/site/index.php', "<?php\n" );
        @symlink( '../volume/var', $this->base . '/site/var' );
        @symlink( '../../elsewhere/log', $this->base . '/volume/var/other' );
        $config = expLogViewerConfig::fromArray( array( 'LogViewerSettings' => array( 'Directories' => array( 'var/log', 'var/other' ) ) ), $this->base . '/site' );
        $this->assertSame( realpath( $this->base . '/volume/var' ), $config->varRoot() );
        $locator = new expLogFileLocator( $config, 'var/log' );
        $found = $locator->directories();
        $file = $locator->resolve( 'var/log/error.log' );
        $logDir = realpath( $this->base . '/volume/var/log' );
        @unlink( $this->base . '/volume/var/other' );
        @unlink( $this->base . '/site/var' );
        @unlink( $this->base . '/site/index.php' );
        @rmdir( $this->base . '/site' );
        @unlink( $this->base . '/volume/var/log/error.log' );
        foreach ( array( '/volume/var/log', '/volume/var', '/volume' ) as $dir )
            @rmdir( $this->base . $dir );
        $this->assertSame( array( $logDir => 'var/log' ), $found, 'var/log found; var/other leads out of var and stays out' );
        $this->assertIsArray( $file, 'the log resolves by its var/ id' );
        $this->assertSame( $logDir . '/error.log', $file['path'] );
    }

    /** LV-SYM-03: a log directory that links out of the installation is still left out */
    public function testALinkOutOfTheInstallationIsStillNotFollowed()
    {
        @symlink( '../../elsewhere/log', $this->base . '/real/var/outside' );
        $config = expLogViewerConfig::fromArray( array( 'LogViewerSettings' => array( 'Directories' => array( 'var/outside' ) ) ), $this->base . '/links' );
        $locator = new expLogFileLocator( $config, 'var/log' );
        $found = $locator->directories();
        @unlink( $this->base . '/real/var/outside' );
        $this->assertSame( array(), $found );
    }
}
