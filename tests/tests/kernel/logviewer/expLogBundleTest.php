<?php
/**
 * "Download all logs" (expLogBundle; doc/eng-us/features/6.0/log-viewer.md, "Downloading every log"), without a database,
 * on a temporary root.
 *
 *  LV-160  the files: every listed log, with or without the archives; entry paths relative to the root, a configured
 *          (web server) log as <group>/<name>
 *  LV-161  a .zip: the logs and a MANIFEST.txt whose sizes and sha256 match what is stored
 *  LV-162  a .tar.gz written as a stream: the same content, a valid ustar archive (checksums), read back here
 *  LV-163  masked by default (an e-mail address and an IP address), as written when unredacted; a compressed rotation
 *          is unpacked and masked, or kept as it is when unredacted
 *  LV-164  over MaxBundleBytes: too_large with the total and the cap, nothing written; no files: empty
 *  LV-165  cleanup removes the bundle's directory, and only a directory under a "logbundle" directory
 *  LV-166  the file name and host; the cap's default
 *  LV-167  the view's decision: POST, the form token, setup/managelogs, the Download switch, and setup/unmasklogs for
 *          an unredacted archive
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expLogViewerTestFixtures.php';
if ( !class_exists( 'expLogBundle' ) )
    require_once dirname( __DIR__, 4 ) . '/kernel/classes/logviewer/explogbundle.php';
foreach ( array( 'expLogManagerInterface' => 'explogmanagerinterface', 'expLogManagerFallback' => 'explogmanagerfallback',
                 'expLogManagerFactory' => 'explogmanagerfactory' ) as $class => $file )
    if ( !class_exists( $class ) && !interface_exists( $class ) )
        require_once dirname( __DIR__, 4 ) . '/kernel/classes/logviewer/' . $file . '.php';
if ( !class_exists( '\Exponential\View\Kernel\Setup\Logsmanage' ) )
    require_once dirname( __DIR__, 4 ) . '/kernel/private/classes/views/setup/logsmanage.php';

class expLogBundleTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $root;

    const LINE = "[ Oct 17 2026 01:00:00 ] [198.51.100.7] [admin] [https://example.test/a] eZTemplate:\nError: mail to jane.doe@example.com failed\n";

    protected function setUp(): void
    {
        $this->root = expLogViewerTestFixtures::root( 'lvbundle' );
        file_put_contents( $this->root . '/var/log/error.log', self::LINE );
        file_put_contents( $this->root . '/var/log/error.log.1', str_repeat( self::LINE, 3 ) );
        file_put_contents( $this->root . '/var/log/error.log.2.gz', gzencode( str_repeat( self::LINE, 2 ) ) );
        file_put_contents( $this->root . '/var/site/log/storage.log', "stored 198.51.100.9\n" );
        mkdir( $this->root . '/ws' );
        file_put_contents( $this->root . '/ws/access_ssl_log', '198.51.100.7 - - [17/Oct/2026:01:00:00 +0200] "GET / HTTP/1.1" 200 1' . "\n" );
    }

    protected function tearDown(): void
    {
        expLogViewerTestFixtures::remove( $this->root );
    }

    protected function locator()
    {
        $config = expLogViewerConfig::fromArray( array( 'LogViewerSettings' => array(
            'LogPath' => array( 'webserver-access' => $this->root . '/ws/access_ssl_log' ) ) ), $this->root );
        return new expLogFileLocator( $config, 'var/log' );
    }

    protected function build( $format, $redact = true, $archives = true, $cap = 1048576 )
    {
        $files = expLogBundle::files( $this->locator(), $archives );
        $dir = expLogBundle::workDirectory( $this->root . '/logbundle' );
        $this->assertNotNull( $dir );
        $redactor = $redact ? new expLogRedactor( true, true, true ) : null;
        return array( expLogBundle::build( $files, $redactor, $dir, 'logs-test.' . ( $format === 'zip' ? 'zip' : 'tar.gz' ), $cap, $format, 1792200000 ), $dir );
    }

    /** The entries of an archive: path => content */
    protected function read( array $built )
    {
        $out = array();
        if ( $built['format'] === 'zip' )
        {
            $zip = new ZipArchive();
            $this->assertTrue( $zip->open( $built['path'] ) );
            for ( $i = 0; $i < $zip->numFiles; $i++ )
                $out[$zip->getNameIndex( $i )] = $zip->getFromIndex( $i );
            $zip->close();
            return $out;
        }
        $data = gzdecode( file_get_contents( $built['path'] ) );
        $pos = 0;
        while ( $pos + 512 <= strlen( $data ) )
        {
            $h = substr( $data, $pos, 512 );
            if ( trim( $h, "\0" ) === '' )
                break;
            $sum = 0;
            for ( $i = 0; $i < 512; $i++ )
                $sum += ( $i >= 148 && $i < 156 ) ? 32 : ord( $h[$i] );
            $this->assertSame( $sum, octdec( trim( substr( $h, 148, 8 ), "\0 " ) ), 'the header checksum' );
            $this->assertSame( "ustar\0", substr( $h, 257, 6 ) );
            $name = rtrim( substr( $h, 0, 100 ), "\0" );
            $prefix = rtrim( substr( $h, 345, 155 ), "\0" );
            $size = octdec( trim( substr( $h, 124, 12 ), "\0 " ) );
            $out[( $prefix !== '' ? $prefix . '/' : '' ) . $name] = substr( $data, $pos + 512, $size );
            $pos += 512 + (int)( ceil( $size / 512 ) * 512 );
        }
        return $out;
    }

    /** LV-160 */
    public function testFiles()
    {
        $entries = array_column( expLogBundle::files( $this->locator(), true ), 'entry' );
        sort( $entries );
        $this->assertSame( array( 'var/log/error.log', 'var/log/error.log.1', 'var/log/error.log.2.gz', 'var/site/log/storage.log',
                                  'webserver/access_ssl_log' ), $entries );
        $entries = array_column( expLogBundle::files( $this->locator(), false ), 'entry' );
        sort( $entries );
        $this->assertSame( array( 'var/log/error.log', 'var/site/log/storage.log', 'webserver/access_ssl_log' ), $entries );
    }

    /** LV-161 */
    public function testZip()
    {
        if ( !class_exists( 'ZipArchive' ) )
            $this->markTestSkipped( 'no ext-zip' );
        list( $built, $dir ) = $this->build( 'zip' );
        $this->assertTrue( $built['ok'], $built['error'] );
        $this->assertSame( 5, $built['files'] );
        $content = $this->read( $built );
        $this->assertArrayHasKey( 'MANIFEST.txt', $content );
        $this->assertArrayHasKey( 'var/log/error.log.2', $content, 'a compressed rotation unpacked when masked' );
        $this->assertManifest( $content );
        expLogBundle::cleanup( $dir );
    }

    /** LV-162 */
    public function testTarGz()
    {
        list( $built, $dir ) = $this->build( 'tar.gz' );
        $this->assertTrue( $built['ok'], $built['error'] );
        $this->assertSame( 'tar.gz', $built['format'] );
        $content = $this->read( $built );
        $this->assertCount( 6, $content );
        $this->assertManifest( $content );
        $this->assertSame( 3 * substr_count( self::LINE, "\n" ), substr_count( $content['var/log/error.log.1'], "\n" ) );
        // a long path is split into prefix and name
        $long = str_repeat( 'd/', 60 ) . 'name.log';
        $h = expLogBundle::tarHeader( $long, 10, 0 );
        $this->assertSame( 512, strlen( $h ) );
        $this->assertSame( 'name.log', rtrim( substr( $h, 0, 100 ), "\0" ) );
        expLogBundle::cleanup( $dir );
    }

    protected function assertManifest( array $content )
    {
        $lines = array_filter( explode( "\n", $content['MANIFEST.txt'] ), function ( $l ) { return $l !== '' && $l[0] !== '#'; } );
        $this->assertCount( count( $content ) - 1, $lines );
        foreach ( $lines as $line )
        {
            list( $path, $size, $mtime, $sha ) = explode( "\t", $line );
            $this->assertArrayHasKey( $path, $content );
            $this->assertSame( strlen( $content[$path] ), (int)$size, $path );
            $this->assertSame( hash( 'sha256', $content[$path] ), $sha, $path );
            $this->assertMatchesRegularExpression( '/^\d{4}-\d\d-\d\dT\d\d:\d\d:\d\dZ$/', $mtime );
        }
    }

    /** LV-163 */
    public function testRedaction()
    {
        list( $built, $dir ) = $this->build( 'tar.gz', true );
        $all = implode( '', $this->read( $built ) );
        $this->assertStringNotContainsString( 'jane.doe@example.com', $all );
        $this->assertStringNotContainsString( '198.51.100.7', $all );
        $this->assertStringContainsString( 'j***@***.com', $all );
        $this->assertStringContainsString( 'masked as in the log viewer', $all );
        expLogBundle::cleanup( $dir );

        list( $built, $dir ) = $this->build( 'tar.gz', false );
        $content = $this->read( $built );
        $this->assertStringContainsString( 'jane.doe@example.com', $content['var/log/error.log'] );
        $this->assertStringContainsString( '198.51.100.7', $content['webserver/access_ssl_log'] );
        $this->assertArrayHasKey( 'var/log/error.log.2.gz', $content, 'unredacted: the compressed rotation as it is' );
        $this->assertSame( str_repeat( self::LINE, 2 ), gzdecode( $content['var/log/error.log.2.gz'] ) );
        $this->assertStringContainsString( 'unredacted', $content['MANIFEST.txt'] );
        expLogBundle::cleanup( $dir );
    }

    /** LV-164 */
    public function testCapAndEmpty()
    {
        list( $built, $dir ) = $this->build( 'tar.gz', true, true, 100 );
        $this->assertFalse( $built['ok'] );
        $this->assertSame( 'too_large', $built['error'] );
        $this->assertSame( 100, $built['cap'] );
        $this->assertGreaterThan( 100, $built['total'] );
        $this->assertSame( array( '.', '..' ), scandir( $dir ), 'nothing written' );
        expLogBundle::cleanup( $dir );
        $dir = expLogBundle::workDirectory( $this->root . '/logbundle' );
        $empty = expLogBundle::build( array(), null, $dir, 'x.tar.gz', 1000, 'tar.gz' );
        $this->assertSame( 'empty', $empty['error'] );
        expLogBundle::cleanup( $dir );
    }

    /** LV-165 */
    public function testCleanup()
    {
        list( $built, $dir ) = $this->build( 'tar.gz' );
        $this->assertFileExists( $built['path'] );
        $this->assertDirectoryDoesNotExist( $dir . '/stage', 'the staged files are gone once the archive is written' );
        expLogBundle::cleanup( $dir );
        $this->assertDirectoryDoesNotExist( $dir );
        // only a bundle's directory
        mkdir( $this->root . '/other' );
        file_put_contents( $this->root . '/other/keep', 'x' );
        expLogBundle::cleanup( $this->root . '/other' );
        $this->assertFileExists( $this->root . '/other/keep' );
        expLogBundle::cleanup( '' );
        expLogBundle::cleanup( $this->root . '/logbundle/../var' );
        $this->assertDirectoryExists( $this->root . '/var/log' );
    }

    /** LV-166 */
    public function testNameHostAndCap()
    {
        $this->assertSame( 'logs-example.test-20261017-0100.zip', expLogBundle::fileName( 'example.test', mktime( 1, 0, 0, 10, 17, 2026 ), 'zip' ) );
        $this->assertSame( 'logs-x-20261017-0100.tar.gz', expLogBundle::fileName( 'x', mktime( 1, 0, 0, 10, 17, 2026 ), 'tar.gz' ) );
        $old = isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : null;
        $_SERVER['SERVER_NAME'] = 'evil/../host name';
        $this->assertSame( 'evil-..-host-name', expLogBundle::host() );
        if ( $old === null )
            unset( $_SERVER['SERVER_NAME'] );
        else
            $_SERVER['SERVER_NAME'] = $old;
        $this->assertSame( 1000, expLogBundle::cap( expLogViewerConfig::fromArray( array( 'ActionSettings' => array( 'MaxBundleBytes' => '1000' ) ), $this->root ) ) );
        $this->assertSame( expLogBundle::DEFAULT_MAX_BYTES, expLogBundle::cap( expLogViewerConfig::fromArray( array( 'ActionSettings' => array( 'MaxBundleBytes' => 'x' ) ), $this->root ) ) );
    }

    /** LV-167 */
    public function testDecision()
    {
        $d = array( '\Exponential\View\Kernel\Setup\Logsmanage', 'decideBundle' );
        $this->assertSame( 'build', call_user_func( $d, 'POST', true, true, true, false, false ) );
        $this->assertSame( 'denied', call_user_func( $d, 'GET', true, true, true, false, false ), 'POST only' );
        $this->assertSame( 'denied', call_user_func( $d, 'POST', false, true, true, false, false ), 'the form token' );
        $this->assertSame( 'denied', call_user_func( $d, 'POST', true, false, true, false, false ), 'setup/managelogs' );
        $this->assertSame( 'off', call_user_func( $d, 'POST', true, true, false, false, true ), '[ActionSettings] Download' );
        $this->assertSame( 'unmask_denied', call_user_func( $d, 'POST', true, true, true, true, false ), 'unredacted needs setup/unmasklogs' );
        $this->assertSame( 'build', call_user_func( $d, 'POST', true, true, true, true, true ) );
    }
}
