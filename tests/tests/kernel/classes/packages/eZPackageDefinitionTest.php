<?php
/**
 * Tests of eZPackage's package definition (package.xml) without the database: a package built in memory with
 * every kind of entry (maintainers, documents, groups, changelog, files of each type, dependencies, install and
 * uninstall items, release and packaging information); the reader of a definition in the format of a shipped
 * package; the lookups (dependency and install items, file
 * lists, thumbnails, file counts); the paths of package files; names; the attributes.
 *
 * Files are written under var/tmp and removed in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZPackageDefinitionTest extends PHPUnit\Framework\TestCase
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

    public function testCreateUsesTheGivenRepository()
    {
        $package = $this->package();
        $this->assertSame( $this->dir, $package->currentRepositoryPath() );
        $this->assertSame( $this->dir . '/k1_test_package', $package->path() );
        $this->assertTrue( $package->attribute( 'is_local' ) );
        $this->assertSame( 'k1_test_package-1.2-3.ezpkg', $package->exportName() );
        $this->assertSame( '1.2-3', $package->getVersion() );
    }

    public function testShippedFormatIsRead()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<package version="5.3.0">
  <name>k1_shipped</name>
  <summary>shipped extension</summary>
  <description>shipped extension</description>
  <vendor>eZ systems</vendor>
  <type value="extension"/>
  <ezpublish>
    <version>5.3.0</version>
    <named-version>5.3</named-version>
  </ezpublish>
  <packaging>
    <timestamp>1241769735</timestamp>
    <host>packages.example.invalid</host>
  </packaging>
  <documents>
    <document mime-type="text/plain" name="LICENCE"/>
  </documents>
  <simple-files/>
  <version>
    <number>5.3</number>
    <release>0</release>
  </version>
  <licence>GPL</licence>
  <state>stable</state>
  <dependencies>
    <provides/>
    <requires/>
    <obsoletes/>
    <conflicts/>
  </dependencies>
  <install>
    <item type="ezextension" filename="extension-k1" sub-directory="ezextension"/>
  </install>
  <uninstall>
    <item type="ezextension" filename="extension-k1" sub-directory="ezextension"/>
  </uninstall>
</package>
XML;
        file_put_contents( $this->dir . '/package.xml', $xml );
        $package = eZPackage::fetchFromFile( $this->dir . '/package.xml' );
        $this->assertSame( 'k1_shipped', $package->attribute( 'name' ) );
        $this->assertSame( 'ez-systems', $package->attribute( 'vendor-dir' ) );
        $this->assertSame( 'extension', $package->attribute( 'type' ) );
        $this->assertSame( '5.3', $package->attribute( 'version-number' ) );
        $this->assertSame( '0', $package->attribute( 'release-number' ) );
        $this->assertSame( 'GPL', $package->attribute( 'licence' ) );
        $this->assertSame( '1241769735', $package->attribute( 'packaging-timestamp' ) );
        $this->assertFalse( $package->attribute( 'packaging-packager' ) );
        $this->assertSame( '5.3.0', $package->attribute( 'ezpublish-version' ) );
        $install = $package->attribute( 'install' );
        $this->assertSame( 'ezextension', $install[0]['type'] );
        $this->assertSame( 'extension-k1', $install[0]['filename'] );
        $this->assertSame( 'ezextension', $install[0]['sub-directory'] );
        $this->assertSame( array(), $package->attribute( 'file-list' ) );
    }

    public function testMissingOrBrokenFiles()
    {
        $this->assertFalse( eZPackage::fetchFromFile( $this->dir . '/missing.xml' ) );
        file_put_contents( $this->dir . '/broken.xml', '<package><name>x' );
        $this->assertFalse( @eZPackage::fetchFromFile( $this->dir . '/broken.xml' ) );
    }

    public function testDependencyItems()
    {
        $package = $this->package();
        $package->appendProvides( 'ezcontentclass', 'folder', 'folder', array( 'extra' => 'x' ) );
        $this->assertCount( 2, $package->dependencyItems( 'provides' ) );
        $this->assertSame( array( array( 'type' => 'ezcontentclass', 'name' => 'folder', 'value' => 'folder', 'extra' => 'x' ) ),
                           $package->dependencyItems( 'provides', array( 'name' => 'folder' ) ) );
        $this->assertSame( array(), $package->dependencyItems( 'provides', array( 'name' => 'folder', 'value' => 'other' ) ) );
        $this->assertSame( array(), $package->dependencyItems( 'provides', array( 'nosuchkey' => 'x' ) ) );
        $this->assertFalse( $package->dependencyItems( 'wants' ) );
        $this->assertFalse( $package->appendDependency( 'wants', array() ) );
        $this->assertSame( array( 'ezcontentclass' ), array_keys( $package->groupDependencyItemsByType( $package->dependencyItems( 'provides' ) ) ) );
        $this->assertSame( '=', $package->dependencyOperatorText( array() ) );
    }

    public function testInstallItemsList()
    {
        $package = $this->package();
        $this->assertSame( array( 'install-one' ), array_column( $package->installItemsList(), 'name' ) );
        $this->assertSame( array( 'uninstall-one' ), array_column( $package->installItemsList( false, false, false, false ), 'name' ) );
        $this->assertSame( array( 'install-one' ), array_column( $package->installItemsList( false, 'unix' ), 'name' ) );
        $this->assertSame( array(), $package->installItemsList( false, 'windows' ) );
        $this->assertSame( array( 'install-one' ), array_column( $package->installItemsList( false, 'windows', 'install-one' ), 'name' ) );
    }

    public function testFileListsAndThumbnails()
    {
        $package = $this->package();
        $this->assertCount( 3, $package->fileList( 'default' ) );
        $this->assertFalse( $package->fileList( 'nosuchcollection' ) );
        $this->assertSame( array( 'thumbnail.png' ), array_column( $package->thumbnailList( 'default' ), 'name' ) );
        $this->assertSame( array(), $package->thumbnailList( 'nosuchcollection' ) );
        $this->assertSame( array( 'thumbnail.png' ), array_column( $package->attribute( 'thumbnail-list' ), 'name' ) );
    }

    public function testFileStorePath()
    {
        $package = $this->package();
        $files = $package->fileList( 'default' );
        $this->assertSame( 'root/design/standard/templates/templates/full/article.tpl', $package->fileStorePath( $files[0], 'default', 'root' ) );
        $this->assertSame( 'root/design/mydesign/templates/templates/full/article.tpl',
                           $package->fileStorePath( $files[0] + array(), 'default', 'root', array() ) === 'x' ? '' :
                           $package->fileStorePath( array_merge( $files[0], array( 'variable-name' => 'design' ) ), 'default', 'root', array( 'design' => 'mydesign' ) ) );
        $this->assertSame( 'root/settings/siteaccess/mysite/site.ini.append.php', $package->fileStorePath( $files[1], 'default', 'root' ) );
        $this->assertSame( 'root/settings/siteaccess/othersite/site.ini.append.php', $package->fileStorePath( $files[1], 'default', 'root', array( 'siteaccess_name' => 'othersite' ) ) );
        $extra = $package->fileList( 'extra' );
        $this->assertSame( 'root/sub/other.txt', $package->fileStorePath( $extra[0], 'extra', 'root' ) );
    }

    public function testFileItemPath()
    {
        $package = $this->package();
        $files = $package->fileList( 'default' );
        $this->assertSame( $this->dir . '/k1_test_package/files/default/design.standard.template/templates/full/article.tpl', $package->fileItemPath( $files[0], 'default' ) );
        $this->assertSame( 'p/k1_test_package/files/default/ini.siteaccess-mysite/site.ini.append.php', $package->fileItemPath( $files[1], 'default', 'p' ) );
    }

    public function testAppendFileTakesTheSubdirectoryFromThePathAndComputesTheMd5()
    {
        $source = $this->dir . '/source.txt';
        file_put_contents( $source, 'content' );
        $package = eZPackage::create( 'k1_files', array(), $this->dir );
        $package->appendFile( 'a/b/c.txt', 'file', false, false, $source, false );
        $item = $package->fileList( 'default' )[0];
        $this->assertSame( 'c.txt', $item['name'] );
        $this->assertSame( 'a/b', $item['subdirectory'] );
        $this->assertSame( md5( 'content' ), $item['md5'] );
        $this->assertSame( md5( 'content' ), eZPackage::md5sum( $source ) );
        $this->assertFalse( @eZPackage::md5sum( $this->dir . '/missing' ) );
    }

    public function testAppendFileCopiesTheFileIntoThePackage()
    {
        $source = $this->dir . '/source.txt';
        file_put_contents( $source, 'copied' );
        $package = eZPackage::create( 'k1_copy', array(), $this->dir );
        $package->appendFile( 'source.txt', 'design', 'template', 'standard', $source, 'default', 'sub', null, true );
        $this->assertSame( 'copied', file_get_contents( $this->dir . '/k1_copy/files/default/design.standard.template/sub/source.txt' ) );
    }

    public function testSetAttributeChangesOnlyScalarEditableAttributes()
    {
        $package = $this->package();
        $this->assertTrue( $package->setAttribute( 'summary', 'New summary' ) );
        $this->assertSame( 'New summary', $package->attribute( 'summary' ) );
        $this->assertFalse( $package->setAttribute( 'maintainers', array() ), 'not editable' );
        $this->assertFalse( $package->setAttribute( 'version-number', '9' ), 'not editable' );
        $this->assertSame( '1.2', $package->attribute( 'version-number' ) );
        $this->assertTrue( $package->hasAttribute( 'file-count' ) );
        $this->assertFalse( $package->hasAttribute( 'nosuchattribute' ) );
    }

    public function testAppendChangeDefaults()
    {
        $package = eZPackage::create( 'k1_changes', array(), $this->dir );
        $package->appendChange( 'Ada', 'ada@k1.example.invalid', 'One change' );
        $change = $package->attribute( 'changelog' )[0];
        $this->assertSame( array( 'One change' ), $change['changes'] );
        $this->assertSame( 1, $change['release'] );
        $package->setRelease( false, '4' );
        $package->appendChange( 'Ada', 'ada@k1.example.invalid', 'Two', false, 5 );
        $this->assertSame( '4', $package->attribute( 'changelog' )[1]['release'] );
        $this->assertSame( 5, $package->attribute( 'changelog' )[1]['timestamp'] );
    }

    public static function nameProvider()
    {
        return array(
            'valid' => array( 'my_package', true, 'my_package' ),
            'uppercase' => array( 'MyPackage', false, 'mypackage' ),
            'spaces' => array( 'my package', false, 'my_package' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('nameProvider')]
    public function testIsValidName( $name, $valid, $transformed )
    {
        $this->assertSame( $valid, eZPackage::isValidName( $name, $result ) );
        $this->assertSame( $transformed, $result );
    }

    public function testDirectoryNames()
    {
        $this->assertSame( 'ezpkg', eZPackage::suffix() );
        $this->assertSame( 'package.xml', eZPackage::definitionFilename() );
        $this->assertSame( 'files', eZPackage::filesDirectory() );
        $this->assertSame( 'documents', eZPackage::documentDirectory() );
        $this->assertSame( 'simplefiles', eZPackage::simpleFilesDirectory() );
        $this->assertSame( 'settings', eZPackage::settingsDirectory() );
        $this->assertSame( '.cache', eZPackage::cacheDirectory() );
        $this->assertStringStartsWith( 'var/', eZPackage::repositoryPath() );
    }

    public function testTypeAndStateListsComeFromTheSettings()
    {
        $this->assertContains( 'contentclass', array_column( eZPackage::typeList(), 'id' ) );
        $this->assertContains( 'stable', array_column( eZPackage::stateList(), 'id' ) );
    }

    public function testArchiveTarType()
    {
        $this->assertSame( ezcArchive::TAR_GNU, eZPackage::archiveTarType( $this->dir . '/missing.ezpkg' ) );
        $v7 = $this->dir . '/v7.tar.gz';
        file_put_contents( $v7, gzencode( str_repeat( "\0", 1024 ) ) );
        $this->assertSame( ezcArchive::TAR_V7, eZPackage::archiveTarType( $v7 ) );
        $gnu = $this->dir . '/gnu.tar.gz';
        file_put_contents( $gnu, gzencode( str_repeat( "\0", 257 ) . "ustar  \0" . str_repeat( "\0", 1024 ) ) );
        $this->assertSame( ezcArchive::TAR_GNU, eZPackage::archiveTarType( $gnu ) );
        $short = $this->dir . '/short.tar.gz';
        file_put_contents( $short, gzencode( 'tiny' ) );
        $this->assertSame( ezcArchive::TAR_GNU, eZPackage::archiveTarType( $short ) );
    }

    public function testSimpleFilePath()
    {
        $package = $this->package();
        $this->assertFalse( $package->simpleFilePath( 'nokey' ) );
        $package->Parameters['simple-file-list']['k'] = array( 'original-path' => 'a.png', 'package-path' => 'simplefiles/abc.png' );
        $this->assertSame( $this->dir . '/k1_test_package/simplefiles/abc.png', $package->simpleFilePath( 'k' ) );
    }
}
