<?php
/**
 * Tests of eZPackageUploadInspector and of the archive check in eZPackage::import(): an uploaded package archive
 * is refused, before anything of it is extracted, for a wrong suffix, no gzip signature, a size or entry count
 * over the limits, an entry outside the package ("../", an absolute path, a back slash), a missing, malformed or
 * incomplete package.xml, an invalid package name (a path in <name>) and a vendor that gives no repository name;
 * a good archive passes. eZPackage::import() refuses the zip-slip archives itself, so every caller (ezpm, the setup
 * wizard) is covered; nothing reaches the package storage. A link to a file of the package is imported as a copy; a
 * link out of it, to a directory or to nothing, a device and a fifo are passed over with a notice, and the package
 * is imported without them.
 *
 * Archives are written byte by byte under var/tmp (eZPackageTestFixtures) and removed in tearDown(). No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/eZPackageTestFixtures.php';

class eZPackageUploadInspectorTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $limits = array( 'max_archive_size' => 1000000, 'max_unpacked_size' => 2000000, 'max_entries' => 50,
                             'suffixes' => array( 'ezpkg', 'tar.gz', 'tgz' ) );

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->dir = eZPackageTestFixtures::tempDir( 'packageupload' );
    }

    protected function tearDown(): void
    {
        if ( $this->dir && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
        // a guard that failed would have written here (the test package's own vendor repository)
        $escaped = eZPackage::repositoryPath() . '/k1-zipslip-vendor';
        if ( is_dir( $escaped ) )
            eZDir::recursiveDelete( $escaped );
    }

    private function archive( array $entries, $name = 'k1_upload.ezpkg' )
    {
        return eZPackageTestFixtures::archive( $this->dir . '/' . $name, $entries );
    }

    private function good( array $extra = array(), array $definition = array() )
    {
        return array_merge( array( array( 'package.xml', eZPackageTestFixtures::definition( $definition + array( 'name' => 'k1_upload' ) ) ),
                                   array( 'ezcontentclass/', '', '5' ),
                                   array( 'ezcontentclass/class-article.xml', '<content-class/>' ) ), $extra );
    }

    private function code( $path, $originalName = 'k1_upload.ezpkg' )
    {
        $result = eZPackageUploadInspector::inspect( $path, $originalName, $this->limits );
        return $result['ok'] ? 'ok' : $result['code'];
    }

    public function testGoodArchivePasses()
    {
        $result = eZPackageUploadInspector::inspect( $this->archive( $this->good() ), 'k1_upload.ezpkg', $this->limits );
        $this->assertTrue( $result['ok'], (string)$result['code'] );
        $this->assertSame( 'k1_upload', $result['name'] );
        $this->assertSame( 'local', $result['vendor_dir'] );
        $this->assertSame( '1.0-2', $result['version'] );
        $this->assertSame( 3, $result['entries'] );
        $this->assertSame( 'ok', $this->code( $this->archive( $this->good() ), 'K1_UPLOAD.TAR.GZ' ) );
        $this->assertSame( 'ok', $this->code( $this->archive( $this->good( array( array( './', '', '5' ) ) ) ) ) );
    }

    public function testSuffixSignatureAndSize()
    {
        $path = $this->archive( $this->good() );
        $this->assertSame( 'suffix', $this->code( $path, 'k1_upload.zip' ) );
        $this->assertSame( 'suffix', $this->code( $path, 'k1_upload.ezpkg.php' ) );
        file_put_contents( $this->dir . '/plain.ezpkg', 'PK not a gzip' );
        $this->assertSame( 'not_gzip', $this->code( $this->dir . '/plain.ezpkg' ) );
        // an uncompressed tar archive, as some 4.x installations exported their packages, is read as it is
        $tar = '';
        foreach ( $this->good() as $entry )
            $tar .= eZPackageTestFixtures::tarEntry( $entry[0], isset( $entry[1] ) ? $entry[1] : '', isset( $entry[2] ) ? $entry[2] : '0' );
        file_put_contents( $this->dir . '/uncompressed.ezpkg', $tar . str_repeat( "\0", 1024 ) );
        $this->assertSame( 'ok', $this->code( $this->dir . '/uncompressed.ezpkg' ) );
        $this->assertTrue( expPackageJson::extractArchive( $this->dir . '/uncompressed.ezpkg', $this->dir . '/uncompressed', $error ), (string)$error );
        $this->assertFileExists( $this->dir . '/uncompressed/ezcontentclass/class-article.xml' );
        file_put_contents( $this->dir . '/empty.ezpkg', '' );
        $this->assertSame( 'no_file', $this->code( $this->dir . '/empty.ezpkg' ) );
        $this->assertSame( 'no_file', $this->code( $this->dir . '/missing.ezpkg' ) );
        $this->assertSame( 'too_large', eZPackageUploadInspector::inspect( $path, 'a.ezpkg', array( 'max_archive_size' => 10 ) + $this->limits )['code'] );
        $many = array();
        for ( $i = 0; $i < 60; $i++ )
            $many[] = array( "f$i.txt", 'x' );
        $this->assertSame( 'too_many_entries', $this->code( $this->archive( $this->good( $many ) ) ) );
        $big = array( array( 'big.bin', str_repeat( 'a', 300000 ) ) );
        $this->assertSame( 'too_large_unpacked', eZPackageUploadInspector::inspect( $this->archive( $this->good( $big ) ), 'a.ezpkg',
                                                                                    array( 'max_unpacked_size' => 100000 ) + $this->limits )['code'] );
        file_put_contents( $this->dir . '/broken.ezpkg', "\x1f\x8b" . 'not really gzip' );
        $this->assertContains( $this->code( $this->dir . '/broken.ezpkg' ), array( 'unreadable', 'no_definition' ) );
    }

    public static function unsafeEntries()
    {
        return array(
            'parent' => array( array( '../evil.txt', 'x' ), 'unsafe_path' ),
            'deep parent' => array( array( 'ezcontentclass/../../../settings/override/x.php', '<?php' ), 'unsafe_path' ),
            'absolute' => array( array( '/tmp/evil.txt', 'x' ), 'unsafe_path' ),
            'backslash' => array( array( '..\\..\\evil.txt', 'x' ), 'unsafe_path' ),
            'symlink named outside' => array( array( '../link', '', '2', 'package.xml' ), 'unsafe_path' ),
        );
    }

    /**
     * Links, devices and fifos do not refuse the package (the packages of eZ Publish 3.x to 5.x and hand repacked
     * ones hold them): a link to a file of the package is imported as a copy, the rest is passed over with a notice.
     */
    public static function passedOverEntries()
    {
        return array(
            'symlink outside' => array( array( 'link', '', '2', '/etc/passwd' ), 'link_outside' ),
            'symlink up and out' => array( array( 'ezcontentclass/link', '', '2', '../../etc/passwd' ), 'link_outside' ),
            'symlink inside' => array( array( 'link', '', '2', 'package.xml' ), null ),
            'symlink to a directory' => array( array( 'link', '', '2', 'ezcontentclass' ), 'link_directory' ),
            'symlink to nothing' => array( array( 'link', '', '2', 'missing.xml' ), 'link_missing' ),
            'hard link' => array( array( 'hard', '', '1', 'package.xml' ), null ),
            'device' => array( array( 'dev', '', '3' ), 'special_file' ),
            'fifo' => array( array( 'fifo', '', '6' ), 'special_file' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'passedOverEntries' )]
    public function testLinksAndDevicesArePassedOverOrCopied( array $entry, $notice )
    {
        $path = $this->archive( $this->good( array( $entry ) ) );
        $result = eZPackageUploadInspector::inspect( $path, 'k1.ezpkg', $this->limits );
        $this->assertTrue( $result['ok'], (string)$result['code'] );
        $this->assertSame( $notice === null ? array() : array( $notice ), array_column( $result['notices'], 'code' ) );
        $target = $this->dir . '/out';
        $this->assertTrue( expPackageJson::extractArchive( $path, $target, $error ), (string)$error );
        $this->assertFalse( is_link( "$target/{$entry[0]}" ) );
        if ( $notice === null )
            $this->assertSame( file_get_contents( "$target/package.xml" ), file_get_contents( "$target/{$entry[0]}" ) );
        else
            $this->assertFalse( file_exists( "$target/{$entry[0]}" ) );
    }

    /**
     * @dataProvider unsafeEntries
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'unsafeEntries' )]
    public function testUnsafeEntriesAreRefused( array $entry, $code )
    {
        $result = eZPackageUploadInspector::inspect( $this->archive( $this->good( array( $entry ) ) ), 'k1.ezpkg', $this->limits );
        $this->assertFalse( $result['ok'] );
        $this->assertSame( $code, $result['code'] );
        $this->assertNotSame( '', eZPackageUploadInspector::message( $result, $this->limits ) );
    }

    public function testSafeEntryPath()
    {
        foreach ( array( 'package.xml', 'a/b/c.xml', 'a/', './', '.', 'a/./b', 'a..b/c' ) as $path )
            $this->assertTrue( eZPackageUploadInspector::safeEntryPath( $path ), $path );
        foreach ( array( '', '..', '../a', 'a/..', 'a/../b', '/a', 'C:/a', 'c:a', "a\0b", "a\nb", 'a\\b', null, str_repeat( 'a', 5000 ) ) as $path )
            $this->assertFalse( eZPackageUploadInspector::safeEntryPath( $path ), var_export( $path, true ) );
    }

    public function testMalformedDefinitions()
    {
        $cases = array(
            'no_definition' => array( array( 'other.xml', '<package/>' ) ),
            'definition_malformed' => array( array( 'package.xml', '<package><name>k1</name>' ) ),
            'definition_malformed ' => array( array( 'package.xml', '<?xml version="1.0"?><notapackage><name>k1</name></notapackage>' ) ),
            'definition_malformed  ' => array( array( 'package.xml', '<?xml version="1.0"?><!DOCTYPE package [<!ENTITY x SYSTEM "file:///etc/passwd">]><package><name>&x;</name></package>' ) ),
            'definition_no_name' => array( array( 'package.xml', '<package><summary>s</summary></package>' ) ),
            'definition_incomplete' => array( array( 'package.xml', '<package><name>k1_upload</name></package>' ) ),
            'invalid_name' => array( array( 'package.xml', eZPackageTestFixtures::definition( array( 'name' => '../../7x/sevenx_classes' ) ) ) ),
            'invalid_name ' => array( array( 'package.xml', eZPackageTestFixtures::definition( array( 'name' => 'Upper Case' ) ) ) ),
            'invalid_vendor' => array( array( 'package.xml', eZPackageTestFixtures::definition( array( 'name' => 'k1_upload', 'vendor' => '..' ) ) ) ),
        );
        foreach ( $cases as $code => $entries )
            $this->assertSame( trim( $code ), $this->code( $this->archive( $entries ) ), $code );
        // a vendor with slashes in it gives a plain directory name, never a path
        $result = eZPackageUploadInspector::inspect( $this->archive( $this->good( array(), array( 'vendor' => '../../../tmp/x' ) ) ), 'a.ezpkg', $this->limits );
        $this->assertTrue( $result['ok'] );
        $this->assertSame( 'tmp-x', $result['vendor_dir'] );
    }

    public function testDefinitionWithoutElementsDoesNotEndTheRequest()
    {
        // eZPackage's own parser: these lacked a guard and ended the request with a fatal error
        $cases = array( 'noname' => '<package><summary>s</summary></package>',
                        'noezpublish' => '<package><name>k1_x</name><summary>s</summary><packaging><timestamp>1</timestamp><host>h</host></packaging></package>',
                        'nopackaging' => '<package><name>k1_x</name><summary>s</summary><ezpublish><version>6</version><named-version>6</named-version></ezpublish></package>',
                        'bare' => '<package><name>k1_x</name></package>' );
        foreach ( $cases as $label => $xml )
        {
            file_put_contents( "$this->dir/$label.xml", $xml );
            $package = @eZPackage::fetchFromFile( "$this->dir/$label.xml" );
            if ( $label === 'noname' )
                $this->assertFalse( $package, $label );
            else
            {
                $this->assertInstanceOf( 'eZPackage', $package, $label );
                $this->assertSame( 'k1_x', $package->attribute( 'name' ) );
            }
        }
        $package = eZPackage::fetchFromFile( "$this->dir/nopackaging.xml" );
        $this->assertFalse( $package->attribute( 'packaging-timestamp' ) );
        file_put_contents( "$this->dir/broken.xml", '<package><name>' );
        $this->assertFalse( @eZPackage::fetchFromFile( "$this->dir/broken.xml" ) );
    }

    public function testImportRefusesZipSlipArchives()
    {
        $before = is_dir( eZPackage::repositoryPath() . '/k1-zipslip-vendor' );
        $this->assertFalse( $before );
        foreach ( array( array( '../k1-escaped.txt', 'x' ), array( '/k1-escaped.txt', 'x' ) ) as $i => $entry )
        {
            $path = $this->archive( $this->good( array( $entry ), array( 'name' => 'k1_zipslip_' . $i, 'vendor' => 'k1-zipslip-vendor' ) ), "slip$i.ezpkg" );
            $name = '';
            $result = @eZPackage::importUnaudited( $path, $name, false );
            $this->assertFalse( $result, 'archive ' . $i );
            $this->assertFalse( is_dir( eZPackage::repositoryPath() . '/k1-zipslip-vendor' ), 'nothing written for archive ' . $i );
        }
        // a link out of the package is passed over, the package itself imported without it
        $path = $this->archive( $this->good( array( array( 'link', '', '2', '/etc' ) ), array( 'name' => 'k1_zipslip_link', 'vendor' => 'k1-zipslip-vendor' ) ), 'sliplink.ezpkg' );
        $name = '';
        $result = @eZPackage::importUnaudited( $path, $name, false );
        $this->assertInstanceOf( 'eZPackage', $result );
        $this->assertSame( 'k1_zipslip_link', $name );
        $imported = eZPackage::repositoryPath() . '/k1-zipslip-vendor/k1_zipslip_link';
        $this->assertFileExists( "$imported/package.xml" );
        $this->assertFalse( file_exists( "$imported/link" ) || is_link( "$imported/link" ) );
        // a vendor that gives no directory name
        $path = $this->archive( $this->good( array(), array( 'name' => 'k1_novendor', 'vendor' => '..' ) ), 'novendor.ezpkg' );
        $name = '';
        $this->assertFalse( @eZPackage::importUnaudited( $path, $name, false ) );
        $this->assertFalse( is_dir( eZPackage::repositoryPath() . '/k1_novendor' ) );
    }
}
