<?php
/**
 * The path and file helpers of lib/ezfile:
 *   - eZDir path text: cleanPath() (".", "..", doubled separators, ".." past the start of a relative path and past
 *     the root of an absolute one, the root alone), path(), dirpath(), convertSeparators(), separator(),
 *     filenamePath(), createMultiLevelPath(), getPathFromFilename(), temporaryFileRegexp()
 *   - eZDir on disk: mkdir() (recursive, existing), recursiveList(), recursiveFind(), recursiveFindRelative(),
 *     findSubitems()/findSubdirs() with types, hidden items and exclusions, unlinkWildcard(), copy() as a child and
 *     into a directory, cleanupEmptyDirectories(), isWriteable()
 *   - eZFileHandler: open/write/read/seek/tell/eof/rewind/close, the stat helpers, rename/unlink, copy() and
 *     move() into a directory and onto a file, link(), symlink() (the link resolves to the source from another
 *     directory), linkCopy(), instance() of the plain and the zlib handler, gzip round trip, duplicate()
 *
 * No database. Every file is created in a private directory under var/tmp that tearDown() removes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezfile
 */

class eZDirAndFileHandlerTest extends PHPUnit\Framework\TestCase
{
    private static $root;
    private $dir;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 4 );
    }

    protected function setUp(): void
    {
        chdir( self::$root );
        $this->dir = 'var/tmp/phpunit-ezdir-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir, 0777, true );
    }

    protected function tearDown(): void
    {
        chdir( self::$root );
        self::removeTree( $this->dir );
    }

    private static function removeTree( $path )
    {
        if ( is_link( $path ) || is_file( $path ) )
        {
            unlink( $path );
            return;
        }
        if ( !is_dir( $path ) )
            return;
        foreach ( scandir( $path ) as $entry )
        {
            if ( $entry !== '.' && $entry !== '..' )
                self::removeTree( $path . '/' . $entry );
        }
        rmdir( $path );
    }

    private function put( $relative, $content = 'x' )
    {
        $file = $this->dir . '/' . $relative;
        if ( !is_dir( dirname( $file ) ) )
            mkdir( dirname( $file ), 0777, true );
        file_put_contents( $file, $content );
        return $file;
    }

    public static function cleanPathProvider()
    {
        return array(
            'plain'                     => array( 'lib/ezdb', 'lib/ezdb' ),
            'dot'                       => array( './lib/./ezdb', 'lib/ezdb' ),
            'dot dot'                   => array( 'var/../lib/ezdb', 'lib/ezdb' ),
            'leading dot dot kept'      => array( '../site/var', '../site/var' ),
            'two leading dot dots kept' => array( '../../site', '../../site' ),
            'dot dot past the start'    => array( 'a/../../b', '../b' ),
            'doubled separators'        => array( 'a//b///c', 'a/b/c' ),
            'backslashes'               => array( 'a\\b\\c', 'a/b/c' ),
            'absolute'                  => array( '/var/www/../tmp', '/var/tmp' ),
            'absolute past the root'    => array( '/../etc', '/etc' ),
            'root alone'                => array( '/', '/' ),
            'back to the root'          => array( '/a/..', '/' ),
            'nothing left'              => array( 'a/..', '.' ),
            'empty'                     => array( '', '' ),
            'trailing separator'        => array( 'a/b/', 'a/b/' ),
        );
    }

    /**
     * ".." after a ".." used to remove it ("../../site" became "site"), and ".." at the root of an absolute path
     * removed the root ("/../etc" became the relative "etc").
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('cleanPathProvider')]
    public function testCleanPath( $path, $expected )
    {
        $this->assertSame( $expected, eZDir::cleanPath( $path ) );
    }

    public function testCleanPathToDosSeparators()
    {
        $this->assertSame( 'a\\b\\c', eZDir::cleanPath( 'a/b/./c', eZDir::SEPARATOR_DOS ) );
        $this->assertSame( 'a\\b', eZDir::convertSeparators( 'a/b', eZDir::SEPARATOR_DOS ) );
        $this->assertSame( 'a/b/c', eZDir::convertSeparators( 'a\\b/c' ) );
    }

    public function testSeparators()
    {
        $this->assertSame( '/', eZDir::separator( eZDir::SEPARATOR_UNIX ) );
        $this->assertSame( '\\', eZDir::separator( eZDir::SEPARATOR_DOS ) );
        $this->assertSame( DIRECTORY_SEPARATOR, eZDir::separator( eZDir::SEPARATOR_LOCAL ) );
        $this->assertNull( eZDir::separator( 'nonsense' ) );
    }

    public function testPath()
    {
        $this->assertSame( 'a/b/c', eZDir::path( array( 'a', 'b', 'c' ) ) );
        $this->assertSame( 'a/b/c/', eZDir::path( array( 'a', 'b', 'c' ), true ) );
        $this->assertSame( 'a/b', eZDir::path( array( 'a/', '/b/' ) ) );
        $this->assertSame( 'var/storage', eZDir::path( array( 'var', 'cache', '..', 'storage' ) ) );
        $this->assertSame( '/', eZDir::path( array( '/' ) ), 'the root keeps its separator' );
        $this->assertSame( 'a\\b\\', eZDir::path( array( 'a', 'b' ), true, eZDir::SEPARATOR_DOS ) );
    }

    public function testDirpath()
    {
        $this->assertSame( 'path/to/some', eZDir::dirpath( 'path/to/some/file.txt' ) );
        $this->assertSame( 'file.txt', eZDir::dirpath( 'file.txt' ) );
        $this->assertSame( 'a/b', eZDir::dirpath( 'a/./b/c' ) );
    }

    public function testFilenameAndKeyPaths()
    {
        $this->assertSame( 'a/b/c/', eZDir::filenamePath( 'abcde' ) );
        $this->assertSame( 'a/', eZDir::filenamePath( 'abc', 2 ) );
        $this->assertSame( '', eZDir::filenamePath( 'ab' ) );
        $this->assertSame( 'a/b/c/d/', eZDir::filenamePath( 'abcde', 1 ) );
        $this->assertSame( '/1/2/3', eZDir::createMultiLevelPath( '123' ) );
        $this->assertSame( '/1/23', eZDir::createMultiLevelPath( '123', 2 ) );
        $this->assertSame( '/7', eZDir::createMultiLevelPath( 7 ) );
    }

    public function testPathFromFilenameUsesTheDirDepthSetting()
    {
        $depth = (int)eZINI::instance()->variable( 'FileSettings', 'DirDepth' );
        $expected = implode( '/', str_split( substr( 'abcdefghijkl', 0, $depth ) ) );
        $this->assertSame( $expected, eZDir::getPathFromFilename( 'abcdefghijkl' ) );
        $this->assertSame( 'a', eZDir::getPathFromFilename( 'a' ) );
    }

    public function testTemporaryFileRegexp()
    {
        $re = eZDir::temporaryFileRegexp();
        foreach ( array( 'file~', '#file#', 'x.bak', '.svn', 'CVS' ) as $name )
            $this->assertMatchesRegularExpression( $re, $name );
        foreach ( array( 'file.php', 'notes.txt' ) as $name )
            $this->assertDoesNotMatchRegularExpression( $re, $name );
        $this->assertStringStartsWith( '(', eZDir::temporaryFileRegexp( false ) );
    }

    public function testMkdir()
    {
        $deep = $this->dir . '/a/b/c';
        $this->assertFalse( eZDir::mkdir( $deep ), 'not without recursive' );
        $this->assertTrue( eZDir::mkdir( $deep, false, true ) );
        $this->assertDirectoryExists( $deep );
        $this->assertFalse( eZDir::mkdir( $deep, false, true ), 'an existing directory' );
        $this->assertTrue( eZDir::mkdir( $this->dir . '/a/./d//', 0755 ) );
        $this->assertDirectoryExists( $this->dir . '/a/d' );
        $this->assertSame( octdec( eZINI::instance()->variable( 'FileSettings', 'StorageDirPermissions' ) ), eZDir::directoryPermission() );
    }

    public function testRecursiveListAndFind()
    {
        $this->put( 'one.php' );
        $this->put( 'sub/two.php' );
        $this->put( 'sub/three.txt' );
        $this->put( '.hidden/four.php' );

        $list = array();
        eZDir::recursiveList( $this->dir, '', $list );
        $names = array_map( function ( $e ) { return $e['path'] . '/' . $e['name'] . ':' . $e['type']; }, $list );
        sort( $names );
        $this->assertSame( array( '/.hidden/four.php:file', '/.hidden:dir', '/one.php:file', '/sub/three.txt:file', '/sub/two.php:file', '/sub:dir' ), $names );

        $found = eZDir::recursiveFind( $this->dir, '\.php' );
        sort( $found );
        $this->assertSame( array( $this->dir . '/one.php', $this->dir . '/sub/two.php' ), $found, 'hidden folders are skipped' );

        $relative = eZDir::recursiveFindRelative( $this->dir, '', '\.php' );
        sort( $relative );
        $this->assertSame( array( '/one.php', '/sub/two.php' ), $relative );
        $this->assertSame( array( 'sub/two.php' ), eZDir::recursiveFindRelative( $this->dir, 'sub', '\.php' ) );
        $this->assertSame( array(), eZDir::recursiveFindRelative( $this->dir, 'missing', '\.php' ) );
        $this->assertSame( array(), eZDir::recursiveFind( $this->dir . '/missing', 'x' ) );
    }

    public function testFindSubitems()
    {
        $this->put( 'f.txt' );
        $this->put( 'd/x' );
        $this->put( '.h' );
        $sorted = function ( $a ) { sort( $a ); return $a; };
        $this->assertSame( array( 'd', 'f.txt' ), $sorted( eZDir::findSubitems( $this->dir ) ) );
        $this->assertSame( array( '.h', 'd', 'f.txt' ), $sorted( eZDir::findSubitems( $this->dir, false, false, true ) ) );
        $this->assertSame( array( 'd' ), eZDir::findSubdirs( $this->dir ) );
        $this->assertSame( array( 'f.txt' ), eZDir::findSubitems( $this->dir, 'f' ) );
        $this->assertSame( array( 'f.txt' ), eZDir::findSubitems( $this->dir, false, false, false, '/^d$/' ) );
        $this->assertSame( array( $this->dir . '/d' ), eZDir::findSubitems( $this->dir, 'd', true ) );
        $this->assertSame( array( 'base/d' ), eZDir::findSubitems( $this->dir, 'd', 'base' ) );
        $this->assertSame( array(), eZDir::findSubitems( $this->dir . '/missing' ) );
    }

    public function testUnlinkWildcard()
    {
        foreach ( array( 'a.tmp', 'b.tmp', 'c.txt', 'cache-1.php', 'cache-22.php', 'keep' ) as $name )
            $this->put( $name );
        eZDir::unlinkWildcard( $this->dir, '*.tmp' );
        eZDir::unlinkWildcard( $this->dir . '/', 'cache-?.php' );
        $left = eZDir::findSubitems( $this->dir );
        sort( $left );
        $this->assertSame( array( 'c.txt', 'cache-22.php', 'keep' ), $left );
    }

    public function testCopy()
    {
        $this->put( 'src/a.txt', 'A' );
        $this->put( 'src/sub/b.txt', 'B' );
        $this->put( 'src/.hidden', 'H' );
        mkdir( $this->dir . '/dst' );

        $destination = $this->dir . '/dst';
        $items = eZDir::copy( $this->dir . '/src', $destination );
        $this->assertSame( $this->dir . '/dst/src', $destination, 'copied as a child' );
        $this->assertSame( 'A', file_get_contents( $destination . '/a.txt' ) );
        $this->assertSame( 'B', file_get_contents( $destination . '/sub/b.txt' ) );
        $this->assertFileDoesNotExist( $destination . '/.hidden' );
        $this->assertContains( 'sub/b.txt', $items );

        mkdir( $this->dir . '/flat' );
        $flat = $this->dir . '/flat';
        eZDir::copy( $this->dir . '/src', $flat, false, true, true );
        $this->assertSame( $this->dir . '/flat', $flat );
        $this->assertSame( 'H', file_get_contents( $flat . '/.hidden' ) );

        $missing = $this->dir . '/nowhere';
        $this->assertFalse( @eZDir::copy( $this->dir . '/src', $missing ) );
        $this->assertFalse( @eZDir::copy( $this->dir . '/nosource', $flat ) );
    }

    public function testCleanupEmptyDirectories()
    {
        mkdir( $this->dir . '/e1/e2/e3', 0777, true );
        $this->put( 'e1/keep.txt' );
        $this->assertTrue( eZDir::cleanupEmptyDirectories( $this->dir . '/e1/e2/e3' ) );
        $this->assertDirectoryDoesNotExist( $this->dir . '/e1/e2' );
        $this->assertFileExists( $this->dir . '/e1/keep.txt', 'stops at the first directory that is not empty' );
        $this->assertTrue( eZDir::isWriteable( $this->dir ) );
    }

    public function testFileHandlerReadsAndWrites()
    {
        $file = $this->dir . '/data.bin';
        $fh = eZFileHandler::instance( false );
        $this->assertSame( 'plain', $fh->identifier() );
        $this->assertSame( 'Plain', $fh->name() );
        $this->assertFalse( $fh->isOpen() );

        $this->assertNotFalse( $fh->open( $file, 'w' ), 'the file resource' );
        $this->assertTrue( $fh->isOpen() );
        $this->assertTrue( $fh->isBinaryMode() );
        $this->assertSame( $file, $fh->filename() );
        $this->assertSame( 'wb', $fh->mode() );
        $this->assertSame( 11, $fh->write( 'hello world' ) );
        $this->assertSame( 3, $fh->write( 'abcdef', 3 ) );
        $this->assertTrue( $fh->close() );
        $this->assertSame( 'hello worldabc', file_get_contents( $file ) );

        $this->assertNotFalse( $fh->open( $file, 'r' ) );
        $this->assertSame( 'hello', $fh->read( 5 ) );
        $this->assertSame( 5, $fh->tell() );
        $this->assertSame( 0, $fh->seek( 6 ) );
        $this->assertSame( 'world', $fh->read( 5 ) );
        $this->assertTrue( $fh->rewind() );
        $this->assertSame( 'hello worldabc', $fh->read() );
        $fh->read( 1 );
        $this->assertTrue( $fh->eof() );
        $fh->close();

        $this->assertTrue( $fh->exists( $file ) );
        $this->assertTrue( $fh->isFile( $file ) );
        $this->assertFalse( $fh->isDirectory( $file ) );
        $this->assertTrue( $fh->isDirectory( $this->dir ) );
        $this->assertTrue( $fh->isReadable( $file ) );
        $this->assertTrue( $fh->isWriteable( $file ) );
        $this->assertFalse( $fh->isLink( $file ) );
        $this->assertSame( 14, $fh->statistics( $file )['size'] );

        $this->assertTrue( $fh->rename( $this->dir . '/renamed.bin', $file ) );
        $this->assertFalse( $fh->exists( $file ) );
        $this->assertTrue( $fh->unlink( $this->dir . '/renamed.bin' ) );
        $this->assertFalse( $fh->exists( $this->dir . '/renamed.bin' ) );
    }

    public function testCopyAndMove()
    {
        $source = $this->put( 'a.txt', 'content' );
        mkdir( $this->dir . '/into' );
        $this->assertTrue( eZFileHandler::copy( $source, $this->dir . '/into' ) );
        $this->assertSame( 'content', file_get_contents( $this->dir . '/into/a.txt' ) );
        $this->assertTrue( eZFileHandler::copy( $source, $this->dir . '/into/' ) );
        $this->assertTrue( eZFileHandler::copy( $source, $source ), 'onto itself' );
        $this->assertFalse( @eZFileHandler::copy( $this->dir, $this->dir . '/x' ), 'not a directory' );
        $this->assertFalse( @eZFileHandler::copy( $this->dir . '/missing', $this->dir . '/x' ) );

        $this->put( 'b.txt', 'old' );
        $this->assertTrue( eZFileHandler::move( $source, $this->dir . '/b.txt' ) );
        $this->assertFileDoesNotExist( $source );
        $this->assertSame( 'content', file_get_contents( $this->dir . '/b.txt' ) );
        mkdir( $this->dir . '/moved' );
        $this->assertTrue( eZFileHandler::move( $this->dir . '/b.txt', $this->dir . '/moved' ) );
        $this->assertSame( 'content', file_get_contents( $this->dir . '/moved/b.txt' ) );
        $this->assertFalse( @eZFileHandler::move( $this->dir . '/missing', $this->dir . '/x' ) );
    }

    public function testHardLink()
    {
        $source = $this->put( 'h.txt', 'hard' );
        $this->assertTrue( eZFileHandler::link( $source, $this->dir . '/h2.txt' ) );
        $this->assertSame( fileinode( $source ), fileinode( $this->dir . '/h2.txt' ) );
        mkdir( $this->dir . '/hd' );
        $this->assertTrue( eZFileHandler::link( $source, $this->dir . '/hd' ) );
        $this->assertSame( 'hard', file_get_contents( $this->dir . '/hd/h.txt' ) );
        $this->assertFalse( @eZFileHandler::link( $this->dir . '/missing', $this->dir . '/x' ) );
    }

    public static function symlinkTargetProvider()
    {
        return array(
            'same directory'        => array( 'a/b/file', 'a/b/link', 'file' ),
            'sibling directory'     => array( 'a/b/file', 'a/c/link', '../b/file' ),
            'deeper link'           => array( 'a/file', 'a/b/c/link', '../../file' ),
            'deeper source'         => array( 'a/b/c/file', 'a/link', 'b/c/file' ),
            'no common directory'   => array( 'x/file', 'y/z/link', '../../x/file' ),
            'link in the cwd'       => array( 'a/file', 'link', 'a/file' ),
            'source in the cwd'     => array( 'file', 'a/link', '../file' ),
            'absolute source'       => array( '/srv/file', 'a/link', '/srv/file' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('symlinkTargetProvider')]
    public function testSymlinkTarget( $source, $link, $expected )
    {
        $this->assertSame( $expected, eZFileHandler::symlinkTarget( $source, $link ) );
    }

    public function testSymlinkTargetForAnAbsoluteLink()
    {
        $this->assertSame( getcwd() . '/a/file', eZFileHandler::symlinkTarget( 'a/file', '/srv/link' ) );
    }

    /**
     * A symbolic link in another directory resolves to the source. The target was written relative to the current
     * directory, so it dangled whenever the link was not in the current directory.
     */
    public function testSymlinkFromAnotherDirectoryResolves()
    {
        $source = $this->put( 'from/s.txt', 'linked' );
        mkdir( $this->dir . '/to/deeper', 0777, true );
        $this->assertTrue( eZFileHandler::symlink( $source, $this->dir . '/to/deeper/l.txt' ) );
        $this->assertTrue( is_link( $this->dir . '/to/deeper/l.txt' ) );
        $this->assertSame( '../../from/s.txt', readlink( $this->dir . '/to/deeper/l.txt' ) );
        $this->assertSame( 'linked', file_get_contents( $this->dir . '/to/deeper/l.txt' ) );

        // into a directory, and replacing an existing link
        $this->assertTrue( eZFileHandler::symlink( $source, $this->dir . '/to' ) );
        $this->assertSame( 'linked', file_get_contents( $this->dir . '/to/s.txt' ) );
        $this->assertTrue( eZFileHandler::symlink( $source, $this->dir . '/to/s.txt' ) );
        $this->assertFalse( @eZFileHandler::symlink( $this->dir . '/missing', $this->dir . '/to/x' ) );
    }

    public function testLinkCopy()
    {
        $source = $this->put( 'lc/s.txt', 'lc' );
        mkdir( $this->dir . '/lcd' );
        $this->assertTrue( eZFileHandler::linkCopy( $source, $this->dir . '/lcd/sym.txt' ) );
        $this->assertSame( 'lc', file_get_contents( $this->dir . '/lcd/sym.txt' ) );
        $this->assertTrue( eZFileHandler::linkCopy( $source, $this->dir . '/lcd/hard.txt', false ) );
        $this->assertSame( fileinode( $source ), fileinode( $this->dir . '/lcd/hard.txt' ) );
    }

    public function testZlibHandlerRoundTrip()
    {
        if ( !function_exists( 'gzopen' ) )
            $this->markTestSkipped( 'zlib is not available' );
        $file = $this->dir . '/data.gz';
        $gz = eZFileHandler::instance( 'gzipzlib' );
        $this->assertInstanceOf( eZGZIPZLIBCompressionHandler::class, $gz );
        $this->assertNotFalse( $gz->open( $file, 'w' ) );
        $gz->write( str_repeat( 'compressible ', 100 ) );
        $gz->close();
        $this->assertLessThan( 1300, filesize( $file ) );
        $this->assertSame( str_repeat( 'compressible ', 100 ), gzdecode( file_get_contents( $file ) ) );

        $this->assertNotFalse( $gz->open( $file, 'r' ) );
        $this->assertSame( 'compressible', $gz->read( 12 ) );
        $gz->close();

        $this->assertSame( 'round trip', $gz->decompress( $gz->compress( 'round trip' ) ) );
        $gz->setCompressionLevel( 9 );
        $this->assertSame( 9, $gz->compressionLevel() );
        $copy = $gz->duplicate();
        $this->assertInstanceOf( eZGZIPZLIBCompressionHandler::class, $copy );
        $this->assertNotSame( $gz, $copy );
    }

    public function testInstanceWithAFileOpensIt()
    {
        $file = $this->put( 'open.txt', 'opened' );
        $fh = eZFileHandler::instance( false, $file, 'r' );
        $this->assertTrue( $fh->isOpen() );
        $this->assertSame( 'opened', $fh->read() );
        $fh->close();
        $this->assertFalse( $fh->isOpen() );
    }
}
