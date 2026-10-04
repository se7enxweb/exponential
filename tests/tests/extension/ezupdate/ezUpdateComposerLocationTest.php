<?php
/**
 * Where ezupdate looks for Composer and PHP: the installation-local places first, and what open_basedir
 * (a default Plesk host) changes. The open_basedir cases run the probe script in a PHP started with
 * -d open_basedir=<root>:/tmp. The live installation, no test database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezupdate/
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../expservices/core/expServicesCoreTestCase.php';

class ezUpdateComposerLocationTest extends expServicesCoreTestCase
{
    const DIR = 'var/tmp/ezupdate_test/';

    protected function root()
    {
        return dirname( __DIR__, 4 );
    }

    protected function fakeComposer( $name )
    {
        if ( !is_dir( $this->root() . '/' . self::DIR ) )
        {
            mkdir( $this->root() . '/' . self::DIR, 0775, true );
        }
        $file = $this->root() . '/' . self::DIR . $name;
        file_put_contents( $file, "#!/usr/bin/env php\n<?php echo 'Composer version 0.0.0';\n" );
        return $file;
    }

    /** The probe's answer, run under open_basedir when $restricted. */
    protected function probe( array $settings, $restricted )
    {
        $root = $this->root();
        if ( !is_file( $root . '/tests/tests/extension/ezupdate/fixtures/probe_composer_location.php' ) )
        {
            $this->markTestSkipped( 'The probe script is not part of this installation.' );
        }
        $command = escapeshellarg( PHP_BINARY ) . ( $restricted ? ' -d open_basedir=' . escapeshellarg( $root . ':/tmp' ) : '' )
            . ' ' . escapeshellarg( $root . '/tests/tests/extension/ezupdate/fixtures/probe_composer_location.php' ) . ' ' . escapeshellarg( json_encode( $settings ) ) . ' 2>&1';
        $output = (string)shell_exec( $command );
        $this->assertSame( 1, preg_match( '/@@(\{[^\n]*\})/', $output, $m ), 'no probe answer: ' . $output );
        return json_decode( $m[1], true );
    }

    public function testSearchPathListsInstallationLocalPlacesBeforeSystemOnes()
    {
        $paths = eZINI::instance( 'ezupdate.ini' )->variable( 'ComposerSettings', 'SearchPath' );
        $this->assertSame( array( 'var/ezupdate/', 'bin/', './', 'vendor/bin/' ), array_slice( $paths, 0, 4 ) );
        $this->assertContains( '/usr/local/bin/', $paths );
        $this->assertLessThan( array_search( '/usr/local/bin/', $paths ), array_search( 'vendor/bin/', $paths ) );
    }

    public function testRelativeSearchPathComesBeforeSystemPath()
    {
        $file = $this->fakeComposer( 'composer.phar' );
        $a = $this->probe( array( 'SearchPath' => array( self::DIR, '/usr/local/bin/' ) ), false );
        $this->assertSame( $file, $a['binary'] );
        $this->assertFalse( $a['trusted'] );
        $this->assertSame( array( PHP_BINARY, $file ), $a['command'] );
    }

    public function testOpenBasedirHidesSystemPathsAndTheyAreReported()
    {
        $r = $this->probe( array( 'SearchPath' => array( '/usr/local/bin/', '/usr/bin/' ) ), true );
        $this->assertFalse( $r['binary'] );
        $this->assertFalse( $r['allowed_usr_local_bin'] );
        $this->assertTrue( $r['allowed_root'] );
        $states = array_unique( array_column( $r['searched'], 'state' ) );
        $this->assertSame( array( 'hidden' ), $states );
        $this->assertStringContainsString( 'open_basedir', $r['message'] );
        $this->assertStringContainsString( 'Get Composer', $r['message'] );
        $this->assertStringContainsString( 'ezupdate.ini', $r['message'] );
    }

    public function testOpenBasedirStillFindsTheInstallationLocalComposer()
    {
        $file = $this->fakeComposer( 'composer.phar' );
        $r = $this->probe( array( 'SearchPath' => array( self::DIR, '/usr/local/bin/' ) ), true );
        $this->assertSame( $file, $r['binary'] );
        $this->assertFalse( $r['trusted'] );
        $this->assertNotFalse( $r['php'] );
    }

    public function testConfiguredPathOutsideOpenBasedirIsTrustedWithoutChecking()
    {
        $r = $this->probe( array( 'Path' => '/nonexistent/place/', 'Binary' => 'composer.phar', 'PHPBinary' => '/opt/php/bin/php' ), true );
        $this->assertSame( '/nonexistent/place/composer.phar', $r['binary'] );
        $this->assertTrue( $r['trusted'] );
        $this->assertSame( array( '/opt/php/bin/php', '/nonexistent/place/composer.phar' ), $r['command'] );
    }

    public function testConfiguredPathOutsideOpenBasedirIsNotTrustedWhenNothingRestricts()
    {
        $r = $this->probe( array( 'Path' => '/nonexistent/place/', 'Binary' => 'composer.phar' ), false );
        $this->assertFalse( $r['binary'] );
        $this->assertFalse( $r['trusted'] );
    }

    public function testFullPathInBinaryIsTrustedUnderOpenBasedir()
    {
        $r = $this->probe( array( 'Binary' => '/usr/local/bin/composer' ), true );
        $this->assertSame( '/usr/local/bin/composer', $r['binary'] );
        $this->assertTrue( $r['trusted'] );
        $this->assertSame( array( '/usr/local/bin/composer' ), $r['command'] );
    }

    public function testPhpBinaryOfTheRunningVersion()
    {
        $r = $this->probe( array(), true );
        $this->assertSame( PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION, eZUpdateManager::phpVersionOf( $r['php'] ) );
    }

    public function testDownloadWithAWrongChecksumIsRefusedAndWritesNothing()
    {
        $dir = $this->root() . '/' . self::DIR;
        $this->fakeComposer( 'dl.phar' );
        file_put_contents( $dir . 'dl.phar.sha256sum', str_repeat( '0', 64 ) . "  composer.phar\n" );
        $ini = eZINI::instance( 'ezupdate.ini' );
        $ini->setVariable( 'ComposerSettings', 'DownloadURL', 'file://' . $dir . 'dl.phar' );
        $manager = eZUpdateManager::getInstance();
        $before = is_file( $manager->localComposerFile() ) ? md5_file( $manager->localComposerFile() ) : null;
        $result = $manager->downloadComposer();
        $this->assertIsString( $result );
        $this->assertStringContainsString( 'SHA-256', $result );
        $after = is_file( $manager->localComposerFile() ) ? md5_file( $manager->localComposerFile() ) : null;
        $this->assertSame( $before, $after );
        $ini->setVariable( 'ComposerSettings', 'DownloadURL', 'https://getcomposer.org/download/latest-stable/composer.phar' );
    }
}
