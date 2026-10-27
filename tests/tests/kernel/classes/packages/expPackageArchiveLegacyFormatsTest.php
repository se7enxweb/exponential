<?php
/**
 * The package archives of every generation import: the fixtures of fixtures/legacy/ (built by
 * fixtures/build-legacy-package-fixtures.php the way eZ Publish 3.x, 4.x and 5.x and GNU tar, bsdtar and PEAR
 * Archive_Tar wrote them) each go through eZPackageUploadInspector::inspect(), expPackageJson::extractArchive() and
 * eZPackage::importUnaudited(), and
 * - the package is accepted, under the name its package.xml gives;
 * - every file lands with its content, files 0644 and directories 0755 (within the installation's limits);
 * - a hard link or a symbolic link to a file of the package lands as a copy of that file;
 * - a symbolic link out of the package, to a directory or to nothing, a device and a fifo are passed over, each with
 *   the notice expected, and nothing of them lands: no link, no device, nothing outside the target directory.
 *
 * No database: the packages are imported into a repository of their own under the package storage, removed in
 * tearDown().
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/eZPackageTestFixtures.php';

class expPackageArchiveLegacyFormatsTest extends PHPUnit\Framework\TestCase
{
    const REPOSITORY = 'k1-legacy-formats';
    const LICENCE = "GNU General Public License v2.0 (or any later version)\n";
    const SETTINGS = 'ezextension/legacyfixture/settings/site.ini.append.php';
    const LOGO = 'ezextension/legacyfixture/design/standard/images/logo.gif';
    const LONG = 'ezextension/legacyfixture/design/standard/templates/node/view/a_directory_name_long_enough_for_the_prefix/a_template_whose_name_makes_the_path_longer_than_one_hundred_bytes.tpl';
    const UMLAUT = "documents/Lizenz-\xC3\xBCbersicht.txt";

    /** the links, the fifo of a repacked package and the notices they give */
    const REPACKED_NOTICES = array( 'ezextension/legacyfixture/dangling' => 'link_missing',
                                    'ezextension/legacyfixture/design/current' => 'link_directory',
                                    'ezextension/legacyfixture/hosts' => 'link_outside',
                                    'ezextension/legacyfixture/passwd' => 'link_outside',
                                    'ezextension/legacyfixture/pipe' => 'special_file' );

    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->dir = eZPackageTestFixtures::tempDir( 'packagelegacy' );
        $this->removeRepository();
    }

    protected function tearDown(): void
    {
        if ( $this->dir && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
        $this->removeRepository();
    }

    private function removeRepository()
    {
        $repository = eZPackage::repositoryPath() . '/' . self::REPOSITORY;
        if ( is_dir( $repository ) )
            eZDir::recursiveDelete( $repository );
    }

    /**
     * fixture => array( package name, files that land (path => content, or true for "the same as the file of that
     * path in $copies"), copies (path => the path whose content it has), notices (path => code), paths that must not land )
     */
    public static function fixtures()
    {
        $common = array( 'documents/LICENCE' => self::LICENCE );
        $repackedCopies = array( 'ezextension/legacyfixture/design/standard/images/logo-copy.gif' => self::LOGO,
                                 'ezextension/legacyfixture/settings/site.ini.append' => self::SETTINGS );
        $repackedAbsent = array_keys( self::REPACKED_NOTICES );
        $repacked = array( 'files' => $common + array( self::UMLAUT => "Lizenz\n" ), 'copies' => $repackedCopies,
                           'notices' => self::REPACKED_NOTICES, 'absent' => $repackedAbsent, 'long' => true );
        $plain = array( 'files' => $common, 'copies' => array(), 'notices' => array(), 'absent' => array(), 'long' => true );
        return array(
            'eZ Publish 3.x: Archive_Tar port, V7' => array( 'ez3x-archive-tar-v7', 'legacy_ez3x_archive_tar_v7', $plain ),
            'eZ Publish 4.0-4.2: archive handler, ustar with empty version' => array( 'ez4x-archive-tar-ustar', 'legacy_ez4x_archive_tar_ustar', $plain ),
            'eZ Publish 4.x/5.x: ezcArchive ustar (the exporter)' => array( 'ez4x-ezcarchive-ustar', 'legacy_ez4x_ezcarchive_ustar', $plain ),
            'eZ Publish 4.x: a package name with upper case letters and a hyphen' => array( 'ez4x-uppercase-name', 'Legacy_UpperCase-Name', $plain ),
            'PEAR Archive_Tar 1.4 with a symbolic link' => array( 'pear-archive-tar', 'legacy_pear_archive_tar',
                array( 'copies' => array( 'ezextension/legacyfixture/settings/site.ini.append' => self::SETTINGS ) ) + $plain ),
            'GNU tar, gnu format, repacked with ./ only' => array( 'gnutar-gnu-plain', 'legacy_gnutar_gnu_plain', $plain ),
            'GNU tar, gnu format, links and fifo' => array( 'gnutar-gnu', 'legacy_gnutar_gnu', $repacked ),
            'GNU tar, posix (PAX) format, links and fifo' => array( 'gnutar-posix', 'legacy_gnutar_posix', $repacked ),
            'GNU tar, ustar format, links and fifo' => array( 'gnutar-ustar', 'legacy_gnutar_ustar', $repacked ),
            'GNU tar, v7 format, links' => array( 'gnutar-v7', 'legacy_gnutar_v7',
                array( 'notices' => array_diff_key( self::REPACKED_NOTICES, array( 'ezextension/legacyfixture/pipe' => 1 ) ), 'long' => false ) + $repacked ),
            'bsdtar on macOS: PAX, ./, AppleDouble, links' => array( 'bsdtar-macos-pax', 'legacy_bsdtar_macos_pax',
                array( 'files' => $common + array( self::UMLAUT => "Lizenz\n" ),
                       'copies' => array( 'ezextension/legacyfixture/settings/site.ini.append' => self::SETTINGS,
                                          'ezextension/legacyfixture/same.tpl' => self::LONG ),
                       'present' => array( '._package.xml' ) ) + $plain ),
            'GNU oddities: V, K, L of 512, D, S' => array( 'gnu-oddities', 'legacy_gnu_oddities',
                array( 'files' => $common + array( 'ezextension/legacyfixture/' . str_repeat( 'd', 200 ) . '/' . str_repeat( 'e', 200 ) . '/'
                                                   . str_repeat( 'n', 511 - 428 - 4 ) . '.txt' => 'block length',
                                                   'ezextension/legacyfixture/' . str_repeat( 'k', 110 ) . '.txt' => 'kkkk' ),
                       'copies' => array( 'ezextension/legacyfixture/to-long-target.txt' => 'ezextension/legacyfixture/' . str_repeat( 'k', 110 ) . '.txt' ),
                       'notices' => array( 'ezextension/legacyfixture/sparse.bin' => 'unsupported_entry' ),
                       'absent' => array( 'ezextension/legacyfixture/sparse.bin' ),
                       'directories' => array( 'ezextension/legacyfixture/dumped' ), 'long' => false ) + $plain ),
            'ustar oddities: g, 7, NUL type, devices, fifo, unknown' => array( 'ustar-oddities', 'legacy_ustar_oddities',
                array( 'notices' => array( 'ezextension/legacyfixture/tty' => 'special_file', 'ezextension/legacyfixture/disk' => 'special_file',
                                           'ezextension/legacyfixture/pipe' => 'special_file', 'ezextension/legacyfixture/unknown.bin' => 'unsupported_entry' ),
                       'absent' => array( 'ezextension/legacyfixture/tty', 'ezextension/legacyfixture/disk', 'ezextension/legacyfixture/pipe',
                                          'ezextension/legacyfixture/unknown.bin', 'pax_global_header' ),
                       'long' => false ) + $plain ),
        );
    }

    private static function fixturePath( $fixture )
    {
        return __DIR__ . '/fixtures/legacy/' . $fixture . '.ezpkg';
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'fixtures' )]
    public function testFixtureIsInspectedAndExtracted( $fixture, $name, array $expected )
    {
        $path = self::fixturePath( $fixture );
        $this->assertFileExists( $path );
        $start = microtime( true );
        $result = eZPackageUploadInspector::inspect( $path, $fixture . '.ezpkg' );
        $this->assertLessThan( 5.0, microtime( true ) - $start );
        $this->assertTrue( $result['ok'], $fixture . ': ' . $result['code'] . ' ' . $result['path'] );
        $this->assertSame( $name, $result['name'] );
        $this->assertNotices( $expected['notices'], $result['notices'] );
        foreach ( $result['notices'] as $notice )
        {
            $this->assertStringContainsString( $notice['path'], expPackageArchive::noticeText( $notice ) );
            $this->assertStringContainsString( $notice['path'], expPackageArchive::noticeMessage( $notice ) );
        }

        $target = $this->dir . '/extracted';
        $this->assertTrue( expPackageJson::extractArchive( $path, $target, $error, $notices ), (string)$error );
        $this->assertNotices( $expected['notices'], $notices );
        $this->assertPackageTree( $target, $name, $expected );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'fixtures' )]
    public function testFixtureIsImported( $fixture, $name, array $expected )
    {
        $packageName = '';
        $package = @eZPackage::importUnaudited( self::fixturePath( $fixture ), $packageName, false, self::REPOSITORY );
        $this->assertSame( $name, $packageName );
        $this->assertInstanceOf( 'eZPackage', $package, $fixture );
        $this->assertSame( $name, $package->attribute( 'name' ) );
        $this->assertPackageTree( eZPackage::repositoryPath() . '/' . self::REPOSITORY . '/' . $name, $name, $expected );
    }

    /**
     * A package named with upper case letters (as 4.x exported "RSSFeed_content_class" or "infoProf") is imported into
     * a directory of exactly that name, found by that name only (the file system is case-sensitive), listed by the
     * package list, offered for removal and removed.
     */
    public function testPackageWithUpperCaseNameIsImportedListedAndRemoved()
    {
        $name = 'Legacy_UpperCase-Name';
        $packageName = '';
        $package = @eZPackage::importUnaudited( self::fixturePath( 'ez4x-uppercase-name' ), $packageName, false, self::REPOSITORY );
        $this->assertInstanceOf( 'eZPackage', $package );
        $this->assertSame( $name, $packageName );
        $repositoryPath = eZPackage::repositoryPath() . '/' . self::REPOSITORY;
        $this->assertFileExists( "$repositoryPath/$name/package.xml" );
        $this->assertSame( $repositoryPath . '/' . $name, $package->path() );

        $this->assertInstanceOf( 'eZPackage', eZPackage::fetch( $name, false, self::REPOSITORY, false ) );
        $this->assertFalse( eZPackage::fetch( strtolower( $name ), false, self::REPOSITORY, false ) );
        // a second import of the same name is refused as existing
        $again = '';
        $this->assertSame( eZPackage::STATUS_ALREADY_EXISTS, @eZPackage::importUnaudited( self::fixturePath( 'ez4x-uppercase-name' ), $again, false, self::REPOSITORY ) );

        $repositories = array( array( 'id' => self::REPOSITORY, 'name' => self::REPOSITORY, 'type' => 'global', 'path' => $repositoryPath ) );
        $cards = @eZPackageCatalog::scan( $repositories, false, self::REPOSITORY )['cards'];
        $key = $name . '@' . self::REPOSITORY;
        $this->assertArrayHasKey( $key, $cards );
        $this->assertSame( array( $name, self::REPOSITORY ), eZPackageRemovalPlan::parseValue( $key ) );
        $plan = eZPackageRemovalPlan::build( array( $key ), '', $cards, null, $repositoryPath );
        $this->assertSame( array( $key ), array_keys( $plan['items'] ) );
        $this->assertSame( array(), $plan['refused'] );

        // what package/list does with the plan's items
        $toRemove = eZPackage::fetch( $name, false, self::REPOSITORY, false );
        $toRemove->Parameters['install_type'] = 'import';
        $toRemove->remove();
        clearstatcache();
        $this->assertDirectoryDoesNotExist( "$repositoryPath/$name" );
        $this->assertFalse( eZPackage::fetch( $name, false, self::REPOSITORY, false ) );
    }

    /**
     * The upload page shows what was passed over among the validation warnings, and the package passes.
     */
    public function testUploadValidationListsTheNotices()
    {
        $report = \Exponential\View\Kernel\Package\Upload::validate( self::fixturePath( 'gnutar-gnu' ) );
        $this->assertTrue( $report['valid'], implode( '; ', $report['errors'] ) );
        $text = implode( "\n", $report['warnings'] );
        foreach ( array_keys( self::REPACKED_NOTICES ) as $path )
            $this->assertStringContainsString( '"' . $path . '"', $text );
    }

    /**
     * path => code of the notices, sorted by path.
     */
    private static function noticeMap( array $notices, $codes = false )
    {
        $map = array();
        foreach ( $notices as $path => $notice )
            $map[$codes ? $path : $notice['path']] = $codes ? $notice : $notice['code'];
        ksort( $map );
        return $map;
    }

    private function assertNotices( array $expected, array $notices )
    {
        $this->assertSame( self::noticeMap( $expected, true ), self::noticeMap( $notices ) );
    }

    private function assertPackageTree( $target, $name, array $expected )
    {
        $expected += array( 'present' => array(), 'directories' => array() );
        $this->assertDirectoryExists( $target );
        $root = realpath( $target );
        $this->assertStringContainsString( '<name>' . $name . '</name>', file_get_contents( "$target/package.xml" ) );
        $files = $expected['files'] + array( self::SETTINGS => null, self::LOGO => null );
        if ( $expected['long'] )
            $files[self::LONG] = "{* a template with a long path *}\n<p>{\$node.name|wash}</p>\n";
        foreach ( $files as $path => $content )
        {
            $this->assertFileExists( "$target/$path", $path );
            if ( $content !== null )
                $this->assertSame( $content, file_get_contents( "$target/$path" ), $path );
        }
        $this->assertStringStartsWith( "GIF89a", file_get_contents( "$target/" . self::LOGO ) );
        foreach ( $expected['copies'] as $path => $source )
        {
            $this->assertFalse( is_link( "$target/$path" ), $path );
            $this->assertTrue( is_file( "$target/$path" ), $path . ' is a copy of ' . $source );
            $this->assertSame( file_get_contents( "$target/$source" ), file_get_contents( "$target/$path" ), $path );
        }
        foreach ( $expected['present'] as $path )
            $this->assertFileExists( "$target/$path" );
        foreach ( $expected['directories'] as $path )
            $this->assertDirectoryExists( "$target/$path" );
        foreach ( $expected['absent'] as $path )
            $this->assertFalse( file_exists( "$target/$path" ) || is_link( "$target/$path" ), $path . ' was passed over' );

        // nothing but plain files and directories, all inside, with the modes of the installation
        clearstatcache();
        $fileMode = sprintf( '%04o', eZFile::fileMode( 0644 ) );
        $dirMode = sprintf( '%04o', eZDir::dirMode( 0755 ) );
        $items = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $target, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::SELF_FIRST );
        foreach ( $items as $item )
        {
            $path = $item->getPathname();
            // eZPackage's own cache of an imported package, not from the archive
            if ( strpos( substr( $path, strlen( $target ) ), '/.cache' ) === 0 )
                continue;
            $this->assertFalse( is_link( $path ), "$path is a link" );
            $this->assertStringStartsWith( $root . '/', realpath( $path ) );
            if ( is_dir( $path ) )
                $this->assertSame( $dirMode, sprintf( '%04o', fileperms( $path ) & 07777 ), $path );
            else
            {
                $this->assertTrue( is_file( $path ), "$path is no plain file" );
                $this->assertSame( $fileMode, sprintf( '%04o', fileperms( $path ) & 07777 ), $path );
            }
        }
    }
}
