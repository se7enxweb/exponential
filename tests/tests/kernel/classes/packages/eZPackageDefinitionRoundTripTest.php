<?php
/**
 * Tests of eZPackage's package definition written to package.xml and read back without the database: every kind
 * of entry a package can have (maintainers with and without a role, documents, groups, changelog entries with
 * several changes, files of several collections and types, dependencies, install and uninstall items, release and
 * packaging information, install data) must come back as it was written, and a package without the optional
 * sections must be readable.
 *
 * Files are written under var/tmp and removed in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZPackageDefinitionRoundTripTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->dir = 'var/tmp/phpunit-package-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0775, true );
    }

    protected function tearDown(): void
    {
        if ( $this->dir && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
    }

    private function package()
    {
        $package = eZPackage::create( 'k1_test_package', array( 'summary' => 'A test package', 'description' => 'Its description',
                                                                'vendor' => 'Example Vendor', 'type' => 'contentclass',
                                                                'priority' => '10', 'source' => 'https://example.invalid/src',
                                                                'extension' => 'k1ext' ),
                                      $this->dir );
        $package->setRelease( '1.2', '3', 1700000000, 'GPL-2.0-or-later', 'stable' );
        $package->setPackager( 1700000100, 'build.example.invalid', 'Builder' );
        $package->appendMaintainer( 'Ada', 'ada@k1.example.invalid', 'lead' );
        $package->appendMaintainer( 'Bob', 'bob@k1.example.invalid' );
        $package->appendDocument( 'README', false, 'unix', 'end-user' );
        $package->appendDocument( 'LICENCE', 'text/x-licence' );
        $package->appendGroup( 'Content' );
        $package->appendChange( 'Ada', 'ada@k1.example.invalid', array( 'First change', 'Second change' ), '2', 1700000200 );
        $package->appendFile( 'templates/full/article.tpl', 'design', 'template', 'standard', 'design/standard/templates/full/article.tpl', 'default', null, 'aaaa', false, null, 'file' );
        $package->appendFile( 'site.ini.append.php', 'ini', 'siteaccess', false, 'settings/siteaccess/x/site.ini.append.php', 'default', false, 'bbbb', false, null, 'file', 'mysite', 'siteaccess_name' );
        $package->appendFile( 'thumbnail.png', 'thumbnail', false, false, 'thumb.png', 'default', false, 'cccc', false, null, 'file' );
        $package->appendFile( 'other.txt', 'file', false, false, 'other.txt', 'extra', 'sub', 'dddd', false, null, 'file' );
        $package->appendProvides( 'ezcontentclass', 'article', 'article' );
        $package->appendDependency( 'requires', array( 'type' => 'ezextension', 'name' => 'ezoe', 'value' => '5.0' ) );
        $package->appendDependency( 'conflicts', array( 'type' => 'ezpackage', 'name' => 'old', 'value' => '1.0' ) );
        $package->appendInstall( 'ezfile', 'install-one', 'unix', true, false, false, array( 'collection' => 'default' ) );
        $package->appendInstall( 'ezfile', 'uninstall-one', false, false, false, false, array( 'collection' => 'default' ) );
        return $package;
    }

    private function roundTrip( $package )
    {
        $file = $this->dir . '/package.xml';
        $this->assertTrue( $package->storeToFile( $file ) );
        $copy = eZPackage::fetchFromFile( $file );
        $this->assertInstanceOf( 'eZPackage', $copy );
        return $copy;
    }

    public function testDefinitionIsWrittenAndReadBack()
    {
        $package = $this->package();
        $file = $this->dir . '/package.xml';
        $this->assertTrue( $package->storeToFile( $file ) );
        $copy = eZPackage::fetchFromFile( $file );
        $this->assertInstanceOf( 'eZPackage', $copy );

        foreach ( array( 'name', 'summary', 'description', 'vendor', 'type', 'priority', 'source', 'extension',
                         'version-number', 'release-number', 'licence', 'state', 'install_type',
                         'packaging-timestamp', 'packaging-host', 'packaging-packager',
                         'ezpublish-version', 'ezpublish-named-version' ) as $attribute )
        {
            $this->assertEquals( $package->attribute( $attribute ), $copy->attribute( $attribute ), $attribute );
        }
        $this->assertSame( 'example-vendor', $copy->attribute( 'vendor-dir' ) );
        $this->assertEquals( $package->attribute( 'maintainers' ), $copy->attribute( 'maintainers' ) );
        $this->assertSame( array( 'README', 'LICENCE' ), array_column( $copy->attribute( 'documents' ), 'name' ) );
        $this->assertSame( array( 'text/plain', 'text/x-licence' ), array_column( $copy->attribute( 'documents' ), 'mime-type' ) );
        $this->assertSame( 'end-user', $copy->attribute( 'documents' )[0]['audience'] );
        $this->assertEquals( $package->attribute( 'dependencies' ), $copy->attribute( 'dependencies' ) );
        $this->assertSame( array( 'install-one' ), array_column( $copy->attribute( 'install' ), 'name' ) );
        $this->assertSame( 'unix', $copy->attribute( 'install' )[0]['os'] );
        $this->assertSame( array( 'uninstall-one' ), array_column( $copy->attribute( 'uninstall' ), 'name' ) );

        $files = $copy->attribute( 'file-list' );
        $this->assertSame( array( 'default', 'extra' ), array_keys( $files ) );
        $this->assertSame( array( 'article.tpl', 'site.ini.append.php', 'thumbnail.png' ), array_column( $files['default'], 'name' ) );
        $this->assertSame( 'templates/full', $files['default'][0]['subdirectory'] );
        $this->assertSame( 'standard', $files['default'][0]['design'] );
        $this->assertSame( 'aaaa', $files['default'][0]['md5'] );
        $this->assertSame( 'mysite', $files['default'][1]['role-value'] );
        $this->assertSame( 'siteaccess_name', $files['default'][1]['variable-name'] );
        $this->assertSame( 'sub', $files['extra'][0]['subdirectory'] );
        $this->assertSame( 4, $copy->attribute( 'file-count' ) );
    }

    public function testGroupsAndChangelogAreReadBack()
    {
        $package = $this->package();
        $file = $this->dir . '/package.xml';
        $package->storeToFile( $file );
        $copy = eZPackage::fetchFromFile( $file );
        $this->assertSame( array( array( 'name' => 'Content' ) ), $copy->attribute( 'groups' ) );
        $changelog = $copy->attribute( 'changelog' );
        $this->assertCount( 1, $changelog );
        $this->assertSame( array( 'First change', 'Second change' ), $changelog[0]['changes'] );
        $this->assertEquals( 1700000200, $changelog[0]['timestamp'] );
        $this->assertSame( '2', $changelog[0]['release'] );
    }

    public function testMinimalDefinitionWithoutOptionalSectionsIsRead()
    {
        $package = eZPackage::create( 'k1_minimal', array( 'summary' => 'Minimal' ), $this->dir );
        $file = $this->dir . '/package.xml';
        $this->assertTrue( $package->storeToFile( $file ) );
        $copy = eZPackage::fetchFromFile( $file );
        $this->assertInstanceOf( 'eZPackage', $copy );
        $this->assertSame( 'k1_minimal', $copy->attribute( 'name' ) );
        $this->assertSame( 'local', $copy->attribute( 'vendor-dir' ) );
        $this->assertSame( array(), $copy->attribute( 'documents' ) );
    }

    public function testMaintainerWithoutRoleIsReadBack()
    {
        $copy = $this->roundTrip( $this->package() );
        $this->assertSame( array( 'name' => 'Bob', 'email' => 'bob@k1.example.invalid', 'role' => false ), $copy->attribute( 'maintainers' )[1] );
    }

    public function testReleaseTimestampIsReadBack()
    {
        $this->assertEquals( 1700000000, $this->roundTrip( $this->package() )->attribute( 'release-timestamp' ) );
    }

    public function testDirectoryEntriesStayDirectories()
    {
        $package = eZPackage::create( 'k1_dirs', array( 'summary' => 'Dirs' ), $this->dir );
        $package->appendFile( 'images', 'design', 'image', 'standard', 'design/standard/images', 'default', false, false, false, null, 'dir' );
        $item = $this->roundTrip( $package )->fileList( 'default' )[0];
        $this->assertSame( 'dir', $item['file-type'] );
        $this->assertSame( 'root/design/standard/images', $package->fileStorePath( $item, 'default', 'root' ) );
    }

    public function testInstallDataIsReadBack()
    {
        $package = $this->package();
        $package->InstallData = array( 'ezcontentclass' => array( 'class_update' => array( 'article' => 'updated' ), 'mode' => 'merge' ) );
        $this->assertSame( $package->InstallData, $this->roundTrip( $package )->InstallData );
    }

    public function testEveryPackageOfTheInstallationIsRead()
    {
        $files = glob( 'var/storage/packages/*/*/package.xml' );
        if ( !$files )
            $this->markTestSkipped( 'no packages in var/storage/packages (installation data)' );
        foreach ( $files as $file )
        {
            $package = eZPackage::fetchFromFile( $file );
            $this->assertInstanceOf( 'eZPackage', $package, $file );
            $this->assertSame( basename( dirname( $file ) ), $package->attribute( 'name' ), $file );
            $copy = $this->roundTrip( $package );
            $this->assertEquals( $package->attribute( 'file-list' ), $copy->attribute( 'file-list' ), $file );
            $this->assertEquals( $package->attribute( 'install' ), $copy->attribute( 'install' ), $file );
        }
    }
}
